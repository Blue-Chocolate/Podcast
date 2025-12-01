import MoreVideosSection from '@/components/sections/videos/MoreVideos';
import Img from '@/components/ui/extend/Img';
import HSplit from '@/components/ui/h-split';
import Logo from '@/components/ui/logo';
import SubmitButton from '@/components/ui/submit-button';
import { INFINITE_QUERY_KEY, VIDEOS_CATEGORY_QUERY_KEY } from '@/constants/query-keys';
import { useDocumentHead } from '@/hooks/useDocumentHead';
import DataWrapper from '@/layouts/DataWrapper';
import DefaultMotionElement from '@/layouts/DefaultMotionElement';
import { getVideosCategoryDetails } from '@/services/getVideos';
import { useInfiniteQuery } from '@tanstack/react-query';
import { useCallback } from 'react';
import { useParams } from 'react-router';

const PAGE_LIMIT = 6;

export default function VideoCategoryPage() {
  const { id = '' } = useParams<{ id: string }>();

  const { data, isError, isLoading, refetch, isFetching, fetchNextPage, hasNextPage, isFetchingNextPage } =
    useInfiniteQuery({
      queryKey: [INFINITE_QUERY_KEY, VIDEOS_CATEGORY_QUERY_KEY, `${id}`],
      queryFn: ({ pageParam }) => getVideosCategoryDetails(id, { limit: PAGE_LIMIT, page: pageParam }),
      getNextPageParam: (lastPage, allPages) => {
        if (lastPage.pagination.total_items <= PAGE_LIMIT * allPages.length) return undefined;
        return allPages.length + 1;
      },
      initialPageParam: 1,
      throwOnError: true
    });

  const videos = data?.pages?.flatMap((p) => p.videos) ?? [];
  const category = data?.pages[0].category ?? null;
  const totalEpisodes = null;

  const loadMore = useCallback(() => {
    if (hasNextPage && !isFetchingNextPage) fetchNextPage();
  }, [fetchNextPage, hasNextPage, isFetchingNextPage]);

  useDocumentHead({
    title: `حقق - ${category?.name}`,
    description: category?.description,
    ogTitle: `حقق - ${category?.name}`,
    ogDescription: category?.description
  });

  return (
    <DataWrapper isError={isError} isPending={isLoading} retry={refetch} isRefetching={isFetching} isEmpty={!data}>
      <title>{category?.name} - حقق</title>

      <div className="container my-8 space-y-10">
        <DefaultMotionElement as="header" className="flex flex-col gap-2 sm:flex-row">
          <Img
            src={category?.image}
            alt={category?.name}
            loading="eager"
            fetchPriority="high"
            className="aspect-video w-full shrink-0 rounded-xl object-cover sm:w-44"
          />
          <div className="space-y-2">
            <h1 className="mb-4 text-[28px] font-medium">{category?.name}</h1>
            <p className="text-muted flex items-center gap-2 text-sm">
              <span>من</span>
              <span>
                <Logo className="w-8" />
              </span>
            </p>
            <p className="text-muted mb-4 pe-14">{category?.description?.slice(0, 100)}...</p>
          </div>
        </DefaultMotionElement>

        <HSplit />

        <div className="text-muted -mt-6 flex justify-between">
          <p>{totalEpisodes} حلقة</p>
          <p>مرتب حسب الأحدث</p>
        </div>

        <MoreVideosSection videos={videos} />

        {hasNextPage && (
          <SubmitButton isLoading={isFetching} className="mx-auto flex px-10" onClick={() => loadMore()}>
            المزيد
          </SubmitButton>
        )}
      </div>
    </DataWrapper>
  );
}
