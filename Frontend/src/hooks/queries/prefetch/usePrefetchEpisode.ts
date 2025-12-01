import { EPISODE_QUERY_KEY } from '@/constants/query-keys';
import usePrefetch from './usePrefetch';
import { getEpisodeDetails } from '@/services/getEpisodes';

export default function usePrefetchEpisode() {
  const handlePrefetchEpisode = usePrefetch({
    getKey: (id: string | number) => [EPISODE_QUERY_KEY, `${id}`],
    queryFn: (id: string | number) => getEpisodeDetails(id)
  });

  return { handlePrefetchEpisode };
}
