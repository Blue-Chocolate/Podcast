import { Pause, Play } from 'lucide-react';
import useWaveSurfer from '@/hooks/useWaveSurfer';

type Props = {
  src: string | null;
};

export default function Audio({ src }: Props) {
  const { waveformRef, isPlaying, togglePlay, isError } = useWaveSurfer({ src });

  return (
    <div className="relative flex w-full items-center gap-1 px-4">
      {isError && (
        <div className="bg-background/0 absolute inset-0 z-5 flex items-center">
          <p>الصوت غير متاح</p>
        </div>
      )}
      <button
        onClick={togglePlay}
        className={`outline-primary flex h-5 w-5 items-center justify-center rounded-full text-white outline-3 ${isError ? 'opacity-30' : ''}`}
      >
        {isPlaying ? (
          <Pause className="fill-primary stroke-0" size={12} />
        ) : (
          <Play className="fill-primary stroke-0" size={12} />
        )}
      </button>

      {/* Waveform */}
      <div ref={waveformRef} className={`flex-1 ${isError ? 'opacity-30' : ''}`} />
    </div>
  );
}
