import { BLOG_CATEGORY_QUERY_KEY } from '@/constants/query-keys';
import { getBlogsCategoryDetails } from '@/services/getBlogs';
import usePrefetchInfinite from './usePrefetchInfinite';

export default function usePrefetchBlogCategory() {
  const handlePrefetchBlogCategory = usePrefetchInfinite({
    getKey: (id: string | number) => [BLOG_CATEGORY_QUERY_KEY, `${id}`],
    queryFn: (id: string | number) => getBlogsCategoryDetails(id, { page: 1, limit: 6 })
  });

  return { handlePrefetchBlogCategory };
}
