import EpisodeCard from '@/components/cards/EpisodeCard';
import type { EpisodeListItem } from '@/schemas/types';

export default function MoreEpisodesSection({ episodes }: { episodes: EpisodeListItem[] }) {
  return (
    <div className="grid grid-cols-1 gap-y-10 lg:grid-cols-2">
      {episodes.map((ep) => (
        <EpisodeCard
          key={ep.id}
          id={ep.id}
          imageSrc={ep.image}
          category={ep.podcast?.name}
          categoryId={ep.podcast?.id}
          description={ep.description}
          title={ep.title}
          publishDate={ep.published_at}
          audioSrc={ep.audio_url}
        />
      ))}
    </div>
  );
}
