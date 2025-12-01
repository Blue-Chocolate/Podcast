import SquareImage from '@/components/cards/SquareImage';
import { Button } from '@/components/ui/button';
import usePrefetchPodcast from '@/hooks/queries/prefetch/usePrefetchPodcast';
import DataWrapper from '@/layouts/DataWrapper';
import getPodcasts from '@/services/getPodcasts';
import { useQuery } from '@tanstack/react-query';
import { useState } from 'react';

export default function SpecialProgramsSection() {
  const [more, setMore] = useState(false);
  const { handlePrefetchPodcast } = usePrefetchPodcast();

  const { data, isPending, isError, refetch, isFetching } = useQuery({
    queryKey: ['special-programs-podcasts'],
    queryFn: () => getPodcasts({ page: 1, limit: 15 })
  });

  const podcasts = data ? [...data.data] : [];
  const mainPodcasts = podcasts.splice(0, 5);

  return (
    <div className="relative">
      {podcasts.length > 0 && (
        <div className="absolute end-0 -top-20">
          <Button
            variant="link"
            className="text-2xl font-medium underline"
            onClick={() => setMore((prev) => podcasts.length > 0 && !prev)}
          >
            المــــــزيد
          </Button>
        </div>
      )}

      <DataWrapper
        isError={isError}
        isPending={isPending}
        retry={refetch}
        isRefetching={isFetching}
        isEmpty={!data?.data.length}
      >
        <div className="mx-auto mt-12 grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-6 lg:grid-cols-5">
          {mainPodcasts.map((podcast) => (
            <SquareImage
              key={podcast.id}
              src={podcast.image}
              alt={podcast.title}
              to={`/البودكاست/${podcast?.id}`}
              onMouseEnter={() => handlePrefetchPodcast(podcast.id)}
            />
          ))}
        </div>

        {more && (
          <div className="mx-auto mt-12 grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-6 lg:grid-cols-5">
            {podcasts.map((podcast) => (
              <SquareImage
                key={podcast.id}
                src={podcast.image}
                alt={podcast.title}
                to={`/البودكاست/${podcast?.id}`}
                onMouseEnter={() => handlePrefetchPodcast(podcast.id)}
              />
            ))}
          </div>
        )}
      </DataWrapper>
    </div>
  );
}
