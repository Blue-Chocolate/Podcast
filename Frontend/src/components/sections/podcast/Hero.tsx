import HeroAudio from '@/components/ui/extend/HeroTarack';
import Img from '@/components/ui/extend/Img';
import ViewsBadge from '@/components/ui/extend/ViewsBadge';
import { parsedDate } from '@/lib/utils';
import DefaultMotionDiv from '@/layouts/DefaultMotionElement';
import { Link } from 'react-router';
import usePrefetchEpisode from '@/hooks/queries/prefetch/usePrefetchEpisode';
import type { EpisodeListItem } from '@/schemas/types';

export default function PodcastsHeroSection({ heroEpisode }: { heroEpisode: EpisodeListItem }) {
  const { handlePrefetchEpisode } = usePrefetchEpisode();

  return (
    <DefaultMotionDiv
      onMouseEnter={() => handlePrefetchEpisode(heroEpisode?.id || '')}
      className="flex flex-col items-end md:h-80 md:flex-row"
    >
      <Link to={`/البودكاست/الحلقات/${heroEpisode?.id}`} className="max-h-80 basis-1/3 overflow-clip md:h-80">
        <Img
          src={heroEpisode?.image}
          alt={heroEpisode?.title}
          loading="eager"
          fetchPriority="high"
          className="h-full w-full rounded-2xl object-cover object-top"
        />
      </Link>

      <div className="w-full sm:p-4 md:basis-2/3">
        <h1 className="mb-4 text-[28px] font-medium">
          <Link to={`/البودكاست/${heroEpisode?.id}`}>{heroEpisode?.title}</Link>
        </h1>
        <p className="text-muted mb-6">{heroEpisode?.description}</p>

        <p className="inline-block">
          في{' '}
          <Link to={`/البودكاست/${heroEpisode?.podcast?.id}`} className="font-bold">
            {heroEpisode?.podcast?.name}
          </Link>
        </p>
        <p className="text-muted ms-3 inline-block text-sm">{parsedDate(heroEpisode?.published_at)}</p>

        <ViewsBadge views={heroEpisode?.views_count || heroEpisode?.views} />

        <HeroAudio src={heroEpisode?.audio_url} />
      </div>
    </DefaultMotionDiv>
  );
}
