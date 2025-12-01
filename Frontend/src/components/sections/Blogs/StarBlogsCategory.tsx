import SquareImage from '@/components/cards/SquareImage';
import { Button } from '@/components/ui/button';
import usePrefetchBlogCategory from '@/hooks/queries/prefetch/usePrefetchBlogCategory';
import DataWrapper from '@/layouts/DataWrapper';
import { getBlogsCategories } from '@/services/getBlogs';
import { useQuery } from '@tanstack/react-query';
import { useState } from 'react';

export default function StarBlogsCategorySection() {
  const { handlePrefetchBlogCategory } = usePrefetchBlogCategory();
  const [more, setMore] = useState(false);

  const { data, isPending, isError, refetch, isFetching } = useQuery({
    queryKey: ['star-blog-categories'],
    queryFn: () => getBlogsCategories({ page: 1, limit: 15 })
  });

  const categories = data ? [...data] : [];
  const mainCategories = categories.splice(0, 5);

  return (
    <div>
      {categories.length > 0 && (
        <div className="absolute end-0 -top-20">
          <Button
            variant="link"
            className="text-2xl font-medium underline"
            onClick={() => setMore((prev) => categories.length > 0 && !prev)}
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
        isEmpty={!data?.length}
      >
        <div className="mx-auto grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-6 lg:grid-cols-5">
          {mainCategories.map((category) => (
            <SquareImage
              key={category.id}
              src={category.image}
              alt={category.name}
              to={`/المدونة/تصنيف/${category.id}`}
              onMouseEnter={() => handlePrefetchBlogCategory(category.id)}
            />
          ))}

          {more && (
            <div className="mx-auto mt-12 grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-6 lg:grid-cols-5">
              {categories.map((category) => (
                <SquareImage
                  key={category.id}
                  src={category.image}
                  alt={category.name}
                  to={`/المدونة/تصنيف/${category.id}`}
                  onMouseEnter={() => handlePrefetchBlogCategory(category.id)}
                />
              ))}
            </div>
          )}
        </div>
      </DataWrapper>
    </div>
  );
}
