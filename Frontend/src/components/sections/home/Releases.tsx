import SquareImage from '../../cards/SquareImage';
import { useQuery } from '@tanstack/react-query';
import { getReleases } from '@/services/getReleases';
import DataWrapper from '@/layouts/DataWrapper';

export default function ReleasesSection() {
  const { data, isPending, isError, refetch, isFetching } = useQuery({
    queryKey: ['haqqeq-releases'],
    queryFn: () => getReleases({ page: 1, limit: 5 })
  });

  const releases = data ? [...data] : [];

  return (
    <DataWrapper
      isError={isError}
      isPending={isPending}
      retry={refetch}
      isRefetching={isFetching}
      isEmpty={!releases.length}
    >
      <div className="mx-auto grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-6 lg:grid-cols-5">
        {releases.map((release) => (
          <SquareImage src={release.image} alt={release.title} key={release.id} to={`/إصدارات/${release.id}`} />
        ))}
      </div>
    </DataWrapper>
  );
}
