import { HOME_PAGE_BLOGS_QUERY_KEY } from '@/constants/query-keys';
import { getBlogs } from '@/services/getBlogs';
import usePrefetch from './usePrefetch';

export default function usePrefetchHomePageBlogs() {
  const handlePrefetchHomePageBlogs = usePrefetch({
    getKey: () => [HOME_PAGE_BLOGS_QUERY_KEY],
    queryFn: () => getBlogs({ page: 1, limit: 13 })
  });

  return { handlePrefetchHomePageBlogs };
}
