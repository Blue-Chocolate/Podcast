import BlogCard from '@/components/cards/BlogCard';
import type { BlogListItem } from '@/schemas/types';

export default function MoreBlogsSection({ blogs }: { blogs: BlogListItem[] }) {
  return (
    <div className="grid grid-cols-1 gap-y-10 lg:grid-cols-2">
      {blogs.map((blog) => (
        <BlogCard
          key={blog.id}
          id={blog.id}
          imageSrc={blog.image}
          category={blog.category.name}
          categoryId={blog.category.id}
          description={blog.description}
          title={blog.title}
          publishDate={blog.published_at || blog.publish_date}
        />
      ))}
    </div>
  );
}
