import type { DetailedCardProps } from './DetailedCard';
import DetailedCard from './DetailedCard';
import usePrefetchEpisode from '@/hooks/queries/prefetch/usePrefetchEpisode';

type Props = { id: string | number; categoryId: string | number } & Omit<DetailedCardProps, 'to' | 'categoryTo'>;

export default function EpisodeCard({ id, categoryId, ...props }: Props) {
  const { handlePrefetchEpisode } = usePrefetchEpisode();

  return (
    <div onMouseEnter={() => handlePrefetchEpisode(id)}>
      <DetailedCard {...props} to={`/البودكاست/الحلقات/${id}`} categoryTo={`/البودكاست/${categoryId}`} />
    </div>
  );
}
