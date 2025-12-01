import { cn, parsedDate } from '@/lib/utils';
import ViewsBadge from '@/components/ui/extend/ViewsBadge';
import Img from '@/components/ui/extend/Img';
import DefaultMotionDiv from '@/layouts/DefaultMotionElement';
import { Link } from 'react-router';
import usePrefetchBlog from '@/hooks/queries/prefetch/usePrefetchBlog';
import BlogCard from '@/components/cards/BlogCard';
import type { BlogListItem } from '@/schemas/types';

type Props = {
  heroBlog: BlogListItem;
  moreBlogs: BlogListItem[];
};
export default function HeroSection({ heroBlog, moreBlogs }: Props) {
  const { handlePrefetchBlog } = usePrefetchBlog();

  return (
    <>
      <DefaultMotionDiv
        className={cn(
          'flex flex-col items-center overflow-clip bg-[#EFEDE8] md:h-80 md:flex-row',
          'rounded-t-[21px] rounded-b-lg md:rounded-s-[21px] md:rounded-e-lg'
        )}
        onMouseEnter={() => handlePrefetchBlog(heroBlog.id)}
      >
        <Link to={`/المدونة/${heroBlog?.id}`} className="max-h-80 basis-1/3 overflow-clip md:h-80">
          <Img
            src={heroBlog?.image}
            alt={heroBlog?.title}
            loading="eager"
            fetchPriority="high"
            className="h-full w-full object-cover object-top"
          />
        </Link>

        <div className="basis-2/3 p-4">
          <h1 className="mb-4 text-[28px] font-medium">
            <Link to={`/المدونة/${heroBlog?.id}`}>{heroBlog?.title}</Link>
          </h1>
          <p className="text-muted mb-10">{heroBlog?.description.slice(0, 200)}...</p>
          <ViewsBadge views={heroBlog?.views || 0} />
          <p>
            في{' '}
            <Link to={`/المدونة/تصنيف/${heroBlog?.category.id}`} className="font-bold">
              {heroBlog?.category.name}
            </Link>
            <span className="text-muted ms-3 inline-block text-sm">
              {parsedDate(heroBlog?.published_at || heroBlog?.publish_date)}
            </span>
          </p>
        </div>
      </DefaultMotionDiv>

      {moreBlogs.length > 0 && (
        <div className="flex flex-col gap-8 lg:flex-row lg:gap-14">
          {moreBlogs?.map((blog) => (
            <div className="basis-1/2" key={blog.id}>
              <BlogCard
                id={blog.id}
                description={blog.description.slice(0, 100)}
                title={blog.title}
                imageSrc={blog.image}
                publishDate={blog.published_at || blog.publish_date}
                category={blog.category.name}
                categoryId={blog.category.id}
              />
            </div>
          ))}
        </div>
      )}
    </>
  );
}
