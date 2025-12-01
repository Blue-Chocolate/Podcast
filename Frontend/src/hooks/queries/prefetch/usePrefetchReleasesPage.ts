import { RELEASES_PAGE_QUERY_KEY } from '@/constants/query-keys';
import { getReleases } from '@/services/getReleases';
import usePrefetch from './usePrefetch';

export default function usePrefetchReleasesPage() {
  const handlePrefetchReleasesPage = usePrefetch({
    getKey: () => [RELEASES_PAGE_QUERY_KEY],
    queryFn: () => getReleases({ page: 1, limit: 11 })
  });

  return { handlePrefetchReleasesPage };
}
