import BlogsHeroSection from '@/components/sections/Blogs/Hero';
import MainBlogsSection from '@/components/sections/Blogs/MainBlogs';
import MoreBlogsSection from '@/components/sections/Blogs/MoreBlogs';
import StarBlogsCategorySection from '@/components/sections/Blogs/StarBlogsCategory';
import SectionHeader from '@/components/ui/extend/SectionHeader';
import { blog as blogIcon, star } from '@/assets/images';
import HSplit from '@/components/ui/h-split';
import { useQuery } from '@tanstack/react-query';
import { getBlogs } from '@/services/getBlogs';
import DataWrapper from '@/layouts/DataWrapper';
import { BLOGS_PAGE_QUERY_KEY } from '@/constants/query-keys';
import { useDocumentHead } from '@/hooks/useDocumentHead';

export default function BlogPage() {
  useDocumentHead({
    title: 'حقق - المدونة',
    description: 'اكتشف مقالاتنا الجديدة والمميزة',
    ogTitle: 'حقق - المدونة',
    ogDescription: 'اكتشف مقالاتنا الجديدة والمميزة'
  });

  const { data, isPending, isError, refetch, isRefetching } = useQuery({
    queryKey: [BLOGS_PAGE_QUERY_KEY],
    queryFn: () => getBlogs({ page: 1, limit: 15 })
  });
  const blogs = data ? [...data.data] : [];

  return (
    <div className="mt-8 mb-28 space-y-12">
      <header className="container">
        <SectionHeader icon={blogIcon} title="المقالات الجديدة" />
        <DataWrapper
          isError={isError}
          isPending={isPending}
          retry={refetch}
          isRefetching={isRefetching}
          isEmpty={!blogs[0]}
        >
          <BlogsHeroSection heroBlog={blogs.splice(0, 1)[0]} />
        </DataWrapper>
      </header>

      <section className="bg-accent">
        <div className="container py-8">
          <DataWrapper
            isError={isError}
            isPending={isPending}
            retry={refetch}
            isRefetching={isRefetching}
            isEmpty={!blogs.length}
          >
            <MainBlogsSection blogs={blogs.splice(0, 4)} />
          </DataWrapper>
        </div>
      </section>

      <section className="container space-y-12">
        <SectionHeader icon={star} title="نشرات متميزة" as="h2" />
        <StarBlogsCategorySection />
        <HSplit className="border-t-[#989696CC]" />
      </section>

      <section className="container">
        <DataWrapper
          isError={isError}
          isPending={isPending}
          retry={refetch}
          isRefetching={isRefetching}
          isEmpty={!blogs.length}
        >
          <MoreBlogsSection blogs={blogs} />
        </DataWrapper>
      </section>
    </div>
  );
}
