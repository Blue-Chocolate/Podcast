import { PODCAST_QUERY_KEY } from '@/constants/query-keys';
import { getPodcastDetails } from '@/services/getPodcasts';
import usePrefetchInfinite from './usePrefetchInfinite';

export default function usePrefetchPodcast() {
  const handlePrefetchPodcast = usePrefetchInfinite({
    getKey: (id: string | number) => [PODCAST_QUERY_KEY, `${id}`],
    queryFn: (id: string | number) => getPodcastDetails(id)
  });

  return { handlePrefetchPodcast };
}
