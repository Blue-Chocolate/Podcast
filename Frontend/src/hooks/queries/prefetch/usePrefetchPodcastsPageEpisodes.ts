import { PODCASTS_PAGE_EPISODES_QUERY_KEY } from '@/constants/query-keys';
import usePrefetch from './usePrefetch';
import { getEpisodes } from '@/services/getEpisodes';

export default function usePrefetchPodcastsPageEpisodes() {
  const handlePrefetchPodcastsPageEpisodes = usePrefetch({
    getKey: () => [PODCASTS_PAGE_EPISODES_QUERY_KEY],
    queryFn: () => getEpisodes({ page: 1, limit: 15 })
  });

  return { handlePrefetchPodcastsPageEpisodes };
}
