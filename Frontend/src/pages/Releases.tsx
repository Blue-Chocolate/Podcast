import ReleasesHeroSection from '@/components/sections/Releases/Hero';
import MoreReleses from '@/components/sections/Releases/MoreReleses';
import { release as releaseIcon } from '@/assets/images';
import SectionHeader from '@/components/ui/extend/SectionHeader';
import { getReleases } from '@/services/getReleases';
import { RELEASES_PAGE_QUERY_KEY } from '@/constants/query-keys';
import { useQuery } from '@tanstack/react-query';
import DataWrapper from '@/layouts/DataWrapper';
import { useDocumentHead } from '@/hooks/useDocumentHead';

export default function ReleasesPage() {
  const { data, isPending, isError, refetch, isFetching } = useQuery({
    queryKey: [RELEASES_PAGE_QUERY_KEY],
    queryFn: () => getReleases({ page: 1, limit: 11 })
  });
  const releases = data ? [...data] : [];

  useDocumentHead({
    title: `حقق - إصدارات حقق`,
    description: 'اكتشف أحدث إصدارات حقق واستمتع بالمحتوى المميز في مختلف المجالات.',
    ogTitle: `حقق - إصدارات حقق`,
    ogDescription: 'اكتشف أحدث إصدارات حقق واستمتع بالمحتوى المميز في مختلف المجالات.'
  });

  return (
    <div className="mb-28 space-y-12">
      <title>أصدارات حقق - حقق</title>

      <header className="bg-accent">
        <div className="container space-y-8 py-10">
          <SectionHeader icon={releaseIcon} title="إصــــــدرات حقق" />
          <DataWrapper
            isError={isError}
            isPending={isPending}
            retry={refetch}
            isRefetching={isFetching}
            isEmpty={!releases[0]}
          >
            <ReleasesHeroSection heroRelease={releases.splice(0, 1)[0]} />
          </DataWrapper>
        </div>
      </header>

      <section className="container space-y-12">
        <DataWrapper
          isError={isError}
          isPending={isPending}
          retry={refetch}
          isRefetching={isFetching}
          isEmpty={!releases.length}
        >
          <MoreReleses releases={releases} />
        </DataWrapper>
      </section>
    </div>
  );
}
