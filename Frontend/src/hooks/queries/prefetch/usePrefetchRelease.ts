import { RELEASE_QUERY_KEY } from '@/constants/query-keys';
import { getReleaseDetails } from '@/services/getReleases';
import usePrefetch from './usePrefetch';

export default function usePrefetchRelease() {
  const handlePrefetchRelease = usePrefetch({
    getKey: (id: string | number) => [RELEASE_QUERY_KEY, `${id}`],
    queryFn: (id: string | number) => getReleaseDetails(id)
  });

  return { handlePrefetchRelease };
}
