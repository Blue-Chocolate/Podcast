import BlogCard from '@/components/cards/BlogCard';
import type { BlogListItem } from '@/schemas/types';

export default function BlogPostsSection({ blogs }: { blogs: BlogListItem[] }) {
  return (
    <div className="grid grid-cols-1 gap-y-10 lg:grid-cols-2">
      {blogs.map((post) => (
        <BlogCard
          id={post.id}
          key={post.id}
          imageSrc={post.image}
          title={post.title}
          description={post.description.slice(0, 150) + '...'}
          category={post.category.name}
          categoryId={post.category.id}
          publishDate={post.published_at || post.publish_date}
        />
      ))}
    </div>
  );
}
