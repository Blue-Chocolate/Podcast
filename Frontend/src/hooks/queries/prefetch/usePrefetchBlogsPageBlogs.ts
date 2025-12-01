import { BLOGS_PAGE_QUERY_KEY } from '@/constants/query-keys';
import { getBlogs } from '@/services/getBlogs';
import usePrefetch from './usePrefetch';

export default function usePrefetchBlogsPageBlogs() {
  const handlePrefetchBlogsPageBlogs = usePrefetch({
    getKey: () => [BLOGS_PAGE_QUERY_KEY],
    queryFn: () => getBlogs({ page: 1, limit: 15 })
  });

  return { handlePrefetchBlogsPageBlogs };
}
