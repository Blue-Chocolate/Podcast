import Img from '@/components/ui/extend/Img';
import ViewsBadge from '@/components/ui/extend/ViewsBadge';
import { parsedDate } from '@/lib/utils';
import DefaultMotionDiv from '@/layouts/DefaultMotionElement';
import { Link } from 'react-router';
import usePrefetchBlog from '@/hooks/queries/prefetch/usePrefetchBlog';
import usePrefetchBlogCategory from '@/hooks/queries/prefetch/usePrefetchBlogCategory';
import type { BlogListItem } from '@/schemas/types';

export default function BlogsHeroSection({ heroBlog }: { heroBlog: BlogListItem }) {
  const { handlePrefetchBlog } = usePrefetchBlog();
  const { handlePrefetchBlogCategory } = usePrefetchBlogCategory();

  return (
    <DefaultMotionDiv
      onMouseEnter={() => handlePrefetchBlog(heroBlog.id)}
      className="mt-4 flex flex-col items-end md:mt-10 md:h-56 md:flex-row"
    >
      <Link to={`/المدونة/${heroBlog?.id}`} className="max-h-56 basis-1/3 overflow-clip md:h-96">
        <Img
          src={heroBlog.image}
          alt={heroBlog.title}
          loading="eager"
          fetchPriority="high"
          className="h-full w-full rounded-2xl object-cover object-top"
        />
      </Link>

      <div className="basis-2/3 px-4">
        <h1 className="mb-4 text-[28px] font-medium">
          <Link to={`/المدونة/${heroBlog.id}`}>{heroBlog.title}</Link>
        </h1>
        <p className="text-muted mb-4 xl:me-18">{heroBlog.description.slice(0, 200)}</p>

        <p className="inline-block">
          في{' '}
          <Link
            onMouseEnter={() => handlePrefetchBlogCategory(heroBlog.category.id)}
            to={`/المدونة/تصنيف/${heroBlog.category.id}`}
            className="font-bold"
          >
            {heroBlog.category.name}
          </Link>
        </p>
        <p className="text-muted ms-3 inline-block text-sm">
          {parsedDate(heroBlog.published_at || heroBlog.publish_date)}
        </p>

        <ViewsBadge views={heroBlog.views} />
      </div>
    </DefaultMotionDiv>
  );
}
