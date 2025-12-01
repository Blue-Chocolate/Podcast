import { remote } from '@/lib/utils';
import { useEffect, useId, useRef, useState } from 'react';
import WaveSurfer from 'wavesurfer.js';

// Global state for managing audio players
const audioPlayers = new Map<string, () => void>();
let currentPlayingId: string | null = null;

const WAVESURFER_CONFIG = {
  waveColor: '#b0b0b0',
  progressColor: '#1f2937',
  height: 40,
  cursorWidth: 0,
  barWidth: 3,
  barRadius: 3,
  barGap: 4
} as const;

export default function useWaveSurfer({ src }: { src: string | null | undefined }) {
  const waveformRef = useRef<HTMLDivElement | null>(null);
  const wavesurferRef = useRef<WaveSurfer | null>(null);
  const playerId = useId();

  const [isPlaying, setIsPlaying] = useState(false);
  const [isError, setIsError] = useState(!src);
  const [soundSrc, setSoundSrc] = useState(() => (src ? remote(src) : 'test.mp3'));
  const [duration, setDuration] = useState<number | null>(null);

  useEffect(() => {
    const container = waveformRef.current;
    if (!container) return;

    wavesurferRef.current?.destroy(); // Cleanup previous instance
    const ws = WaveSurfer.create({
      container,
      ...WAVESURFER_CONFIG
    });
    wavesurferRef.current = ws;

    // Event handlers
    const handlePause = () => setIsPlaying(false);

    const handleReady = () => {
      const d = ws.getDuration();
      setDuration(isFinite(d) ? d : null);
    };

    const handlePlay = () => {
      setIsPlaying(true);
      // Pause other players when this one starts playing
      if (currentPlayingId && currentPlayingId !== playerId) {
        audioPlayers.get(currentPlayingId)?.();
      }
      currentPlayingId = playerId;
    };

    const handleError = () => {
      setIsError(true);
      setSoundSrc('test.mp3');
    };

    const handleFinish = () => {
      setIsPlaying(false);
      ws.seekTo(0);
    };

    // Register events
    ws.on('ready', handleReady);
    ws.on('error', handleError);
    ws.on('play', handlePlay);
    ws.on('pause', handlePause);
    ws.on('finish', handleFinish);

    ws.load(soundSrc);
    audioPlayers.set(playerId, () => ws.pause()); // Register this player's pause function

    return () => {
      // Cleanup
      ws.un('error', handleError);
      ws.un('play', handlePlay);
      ws.un('pause', handlePause);
      ws.un('finish', handleFinish);
      ws.un('ready', handleReady);
      ws.destroy();
      audioPlayers.delete(playerId);
      if (currentPlayingId === playerId) currentPlayingId = null;
    };
  }, [soundSrc, playerId]);

  const togglePlay = () => {
    wavesurferRef.current?.playPause();
  };

  return { waveformRef, isPlaying, isError, togglePlay, duration };
}
