import { blog, podcast, release, videos } from '@/assets/images';
import HeroSection from '@/components/sections/home/Hero';
import NewBlogPostsSection from '@/components/sections/home/BlogPosts';
import NewEpisodesSection from '@/components/sections/home/Episodes';
import ReleasesSection from '@/components/sections/home/Releases';
import VideosSection from '@/components/sections/home/Videos';
import SectionHeader from '@/components/ui/extend/SectionHeader';
import HSplit from '@/components/ui/h-split';
import { HOME_PAGE_BLOGS_QUERY_KEY } from '@/constants/query-keys';
import DataWrapper from '@/layouts/DataWrapper';
import { getBlogs } from '@/services/getBlogs';
import { useQuery } from '@tanstack/react-query';
import { useDocumentHead } from '@/hooks/useDocumentHead';

export default function HomePage() {
  const { data, isError, isPending, refetch, isFetching } = useQuery({
    queryKey: [HOME_PAGE_BLOGS_QUERY_KEY],
    queryFn: () => getBlogs({ page: 1, limit: 13 })
  });
  const blogs = data ? [...data.data] : [];
  const heroBlog = blogs?.[0] ?? null;
  blogs?.shift();
  const moreBlogs = blogs?.splice(0, 2);

  useDocumentHead({
    title: `حقق - الرئيسية`,
    description:
      'مرحبًا بك في حقق، منصتك حقق للاستماع إلى البودكاست، قراءة المقالات، ومشاهدة الفيديوهات التي تلهمك وتثريك بالمعلومات.',
    ogTitle: `حقق - الرئيسية`,
    ogDescription:
      'مرحبًا بك في حقق، منصتك حقق للاستماع إلى البودكاست، قراءة المقالات، ومشاهدة الفيديوهات التي تلهمك وتثريك بالمعلومات.'
  });

  return (
    <div className="mt-8 mb-28 space-y-12">
      <header className="container space-y-8">
        <DataWrapper
          isError={isError}
          isPending={isPending}
          retry={refetch}
          isRefetching={isFetching}
          isEmpty={!heroBlog}
        >
          <HeroSection heroBlog={heroBlog} moreBlogs={moreBlogs} />
        </DataWrapper>
      </header>

      <section className="bg-accent">
        <div className="container space-y-8 py-8">
          <SectionHeader icon={podcast} moreUrl="/البودكاست" title="الحلقات الجديده" />
          <NewEpisodesSection />
        </div>
      </section>

      <section className="container space-y-8">
        <SectionHeader icon={release} moreUrl="/إصدارات" title="إصدارات حقق" as="h3" />
        <ReleasesSection />
        <HSplit className="mt-12 border-t-[#989696CC]" />
      </section>

      <section className="container space-y-8">
        <SectionHeader title="الفيديوهات" icon={videos} moreUrl="/الفيديوهات" as="h4" />
        <VideosSection />
        <HSplit className="container mt-14 border-t-[#989696CC]" />
      </section>

      <section className="container space-y-12">
        <SectionHeader as="h5" icon={blog} moreUrl="/المدونة" title="المقالات الجديدة" />
        <DataWrapper
          isError={isError}
          isPending={isPending}
          retry={refetch}
          isRefetching={isFetching}
          isEmpty={!blogs.length}
        >
          <NewBlogPostsSection blogs={blogs} />
        </DataWrapper>
      </section>
    </div>
  );
}
