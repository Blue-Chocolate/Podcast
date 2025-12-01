import { VIDEOS_PAGE_QUERY_KEY } from '@/constants/query-keys';
import { getVideos } from '@/services/getVideos';
import usePrefetch from './usePrefetch';

export default function usePrefetchVideosPage() {
  const handlePrefetchVideosPage = usePrefetch({
    getKey: () => [VIDEOS_PAGE_QUERY_KEY],
    queryFn: () => getVideos({ page: 1, limit: 14 })
  });

  return { handlePrefetchVideosPage };
}
