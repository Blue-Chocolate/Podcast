import { VIDEO_QUERY_KEY } from '@/constants/query-keys';
import { getVideoDetails } from '@/services/getVideos';
import usePrefetch from './usePrefetch';

export default function usePrefetchVideo() {
  const handlePrefetchVideo = usePrefetch({
    getKey: (id: string | number) => [VIDEO_QUERY_KEY, `${id}`],
    queryFn: (id: string | number) => getVideoDetails(id)
  });

  return { handlePrefetchVideo };
}
