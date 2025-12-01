import { useQuery } from '@tanstack/react-query';
import DataWrapper from '@/layouts/DataWrapper';
import SquareImage from '@/components/cards/SquareImage';
import EpisodeCard from '@/components/cards/EpisodeCard';
import { getEpisodes } from '@/services/getEpisodes';
import usePrefetchEpisode from '@/hooks/queries/prefetch/usePrefetchEpisode';

export default function EpisodesSection() {
  const { handlePrefetchEpisode } = usePrefetchEpisode();

  const { data, isPending, isError, refetch, isFetching } = useQuery({
    queryKey: ['recent-home-episodes'],
    queryFn: () => getEpisodes({ page: 1, limit: 8 })
  });
  const allEpisodes = data ? [...data] : [];
  const mainEpisodes = allEpisodes.splice(0, 2);
  const remainingEpisodes = [...allEpisodes];

  return (
    <DataWrapper
      isError={isError}
      isPending={isPending}
      retry={refetch}
      isEmpty={!data?.length}
      isRefetching={isFetching}
    >
      <div className="flex w-full flex-col items-center justify-between gap-8 py-10 xl:flex-row xl:gap-1">
        <div className="block space-y-6 lg:flex lg:space-y-0 xl:block xl:space-y-10">
          {mainEpisodes.map((ep) => (
            <EpisodeCard
              key={ep.id}
              id={ep.id}
              imageSrc={ep.image}
              category={ep.podcast.name || ''}
              categoryId={ep.podcast.id || ''}
              description={ep.description}
              title={ep.title}
              publishDate={ep.published_at}
            />
          ))}
        </div>

        <div className="grid shrink-0 grid-cols-3 gap-2 sm:gap-6">
          {remainingEpisodes.map((ep) => (
            <SquareImage
              key={ep.id}
              className="xl:w-56"
              src={ep.image || ''}
              alt={ep.title}
              to={`/البودكاست/${ep.id}`}
              onMouseEnter={() => handlePrefetchEpisode(ep.id)}
            />
          ))}
        </div>
      </div>
    </DataWrapper>
  );
}
