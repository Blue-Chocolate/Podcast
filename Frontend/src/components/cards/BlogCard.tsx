import usePrefetchBlog from '@/hooks/queries/prefetch/usePrefetchBlog';
import DetailedCard, { type DetailedCardProps } from './DetailedCard';

type Props = { id: string | number; categoryId: string | number } & Omit<DetailedCardProps, 'to' | 'categoryTo'>;

export default function BlogCard({ id, categoryId, ...props }: Props) {
  const { handlePrefetchBlog } = usePrefetchBlog();

  return (
    <div onMouseEnter={() => handlePrefetchBlog(id)}>
      <DetailedCard {...props} to={`/المدونة/${id}`} categoryTo={`/المدونة/تصنيف/${categoryId}`} />
    </div>
  );
}
