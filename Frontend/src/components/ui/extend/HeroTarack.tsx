import { thump } from '@/assets/images';
import { Pause, Play } from 'lucide-react';
import { formatDuration } from '@/lib/utils';
import useWaveSurfer from '@/hooks/useWaveSurfer';

type Props = {
  src?: string | null;
};

export default function HeroAudio({ src }: Props) {
  const { waveformRef, isPlaying, togglePlay, duration } = useWaveSurfer({ src });

  return (
    <div className="flex items-stretch gap-3 rounded-[19px] bg-[#ECE6E6] p-2.5">
      <button className="bg-primary w-20 rounded-md text-white">
        <img src={thump} alt="thump" />
      </button>

      <div className="relative flex w-full items-center gap-1 rounded-md bg-[#FEDFCA] px-4">
        <button
          onClick={togglePlay}
          className="outline-primary flex h-8 w-8 items-center justify-center rounded-full text-white outline-4"
        >
          {isPlaying ? (
            <Pause className="fill-primary stroke-0" size={18} />
          ) : (
            <Play className="fill-primary stroke-0" size={18} />
          )}
        </button>

        <span className="text-primary absolute end-4 bottom-1 block rounded-sm bg-[#F7EEEE] px-2 pb-1 text-xs font-medium">
          {formatDuration(duration)}
        </span>

        {/* Waveform */}
        <div ref={waveformRef} className="flex-1" />
      </div>
    </div>
  );
}
