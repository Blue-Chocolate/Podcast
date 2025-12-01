import { useQueryClient } from '@tanstack/react-query';
import { useCallback } from 'react';
import type { QueryKey } from '@tanstack/react-query';
import { isAxiosError } from 'axios';
import { INFINITE_QUERY_KEY } from '@/constants/query-keys';

type GetKey<TArgs extends unknown[]> = (...args: TArgs) => QueryKey;
type QueryFn<TData, TArgs extends unknown[]> = (...args: TArgs) => Promise<TData>;

type UsePrefetchOptions<TData = unknown, TArgs extends unknown[] = [void]> = {
  getKey: GetKey<TArgs>;
  queryFn: QueryFn<TData, TArgs>;
  retry?: boolean | number;
  staleTime?: number;
};

export default function usePrefetchInfinite<TData = unknown, TArgs extends unknown[] = [void]>({
  getKey,
  queryFn
}: UsePrefetchOptions<TData, TArgs>) {
  const queryClient = useQueryClient();

  const prefetch = useCallback(
    (...args: TArgs) => {
      const key = getKey(...args);
      const state = queryClient.getQueryState(key);

      if (
        (state?.data && !state?.isInvalidated) ||
        (state?.error instanceof Error && isAxiosError(state.error) && state.error.response?.status === 404)
      ) {
        return Promise.resolve(state.data as TData);
      }
      return queryClient.prefetchInfiniteQuery({
        queryKey: [INFINITE_QUERY_KEY, ...key],
        queryFn: () => queryFn(...args),
        initialPageParam: 1,
        getNextPageParam: () => undefined,
        pages: 1
      });
    },
    [queryClient, getKey, queryFn]
  );

  return prefetch;
}
