import BlogCard from '@/components/cards/BlogCard';
import type { BlogListItem } from '@/schemas/types';

export default function MainBlogsSection({ blogs }: { blogs: BlogListItem[] }) {
  return (
    <div className="grid grid-cols-1 gap-3 lg:grid-cols-2">
      {blogs.map((blog) => (
        <BlogCard
          id={blog.id}
          key={blog.id}
          imageSrc={blog.image}
          category={blog.category.name}
          categoryId={blog.category.id}
          description={blog.description}
          title={blog.title}
          publishDate={blog.published_at || blog.publish_date}
          views={blog.views}
        />
      ))}
    </div>
  );
}
