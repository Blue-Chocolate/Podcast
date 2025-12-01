import { VIDEOS_CATEGORY_QUERY_KEY } from '@/constants/query-keys';
import { getVideosCategoryDetails } from '@/services/getVideos';
import usePrefetchInfinite from './usePrefetchInfinite';

export default function usePrefetchVideoCategoryDetails() {
  const handlePrefetchVideoCategory = usePrefetchInfinite({
    getKey: (id: string | number) => [VIDEOS_CATEGORY_QUERY_KEY, `${id}`],
    queryFn: (id: string | number) => getVideosCategoryDetails(id)
  });

  return { handlePrefetchVideoCategory };
}
