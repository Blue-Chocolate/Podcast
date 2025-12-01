import { BLOG_QUERY_KEY } from '@/constants/query-keys';
import { getBlogDetails } from '@/services/getBlogs';
import usePrefetch from './usePrefetch';

export default function usePrefetchBlog() {
  const handlePrefetchBlog = usePrefetch({
    getKey: (id: string | number) => [BLOG_QUERY_KEY, `${id}`],
    queryFn: (id: string | number) => getBlogDetails(id)
  });

  return { handlePrefetchBlog };
}
