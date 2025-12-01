import VedioCard from '../../cards/VideoCard';
import { useQuery } from '@tanstack/react-query';
import DataWrapper from '@/layouts/DataWrapper';
import { getVideos } from '@/services/getVideos';

//TODO: Connect videos data source
export default function VideosSection() {
  const { data, isPending, isError, refetch, isFetching } = useQuery({
    queryKey: ['new-videos'],
    queryFn: () => getVideos({ page: 1, limit: 3 })
  });

  const videos = data ? data.data : [];

  return (
    <DataWrapper
      isError={isError}
      isPending={isPending}
      retry={refetch}
      isRefetching={isFetching}
      isEmpty={!videos.length}
    >
      <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {videos.map((video) => (
          <VedioCard
            key={video.id}
            id={video.id}
            thumbnail={video.image}
            category={video.category.name}
            categoryId={video.category.id}
            title={video.title}
            publishDate={video.created_at}
          />
        ))}
      </div>
    </DataWrapper>
  );
}
