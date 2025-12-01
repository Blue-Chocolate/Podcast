import SquareImage from '@/components/cards/SquareImage';
import { Button } from '@/components/ui/button';
import usePrefetchVideoCategoryDetails from '@/hooks/queries/prefetch/usePrefetchVideoCategoryDetails';
import DataWrapper from '@/layouts/DataWrapper';
import { getVideosCategories } from '@/services/getVideos';
import { useQuery } from '@tanstack/react-query';
import { useState } from 'react';

export default function SpecialVideoProgramsSection() {
  const [more, setMore] = useState(false);
  const { handlePrefetchVideoCategory } = usePrefetchVideoCategoryDetails();

  const { data, isPending, isError, refetch, isFetching } = useQuery({
    queryKey: ['special-programs-videos'],
    queryFn: () => getVideosCategories({ page: 1, limit: 15 })
  });

  const categories = data ? [...data.data] : [];
  const mainCategories = categories.splice(0, 5);

  return (
    <div className="relative">
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
        isEmpty={!data?.data.length}
      >
        <div className="mx-auto mt-12 grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-6 lg:grid-cols-5">
          {mainCategories.map((category) => (
            <SquareImage
              key={category.id}
              src={category.image_path}
              alt={category.name}
              to={`/الفيديوهات/تصنيف/${category?.id}`}
              onMouseEnter={() => handlePrefetchVideoCategory(category.id)}
            />
          ))}
        </div>

        {more && (
          <div className="mx-auto mt-12 grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-6 lg:grid-cols-5">
            {categories.map((category) => (
              <SquareImage
                key={category.id}
                src={category.image_path}
                alt={category.name}
                to={`/الفيديوهات/تصنيف/${category?.id}`}
                onMouseEnter={() => handlePrefetchVideoCategory(category.id)}
              />
            ))}
          </div>
        )}
      </DataWrapper>
    </div>
  );
}
