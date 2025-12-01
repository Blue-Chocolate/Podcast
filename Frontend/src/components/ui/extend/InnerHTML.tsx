import { cn } from '@/lib/utils';
import DOMPurify from 'dompurify';

type Props = {
  content: string;
  className?: string;
};

export default function InnerHTML({ content, className }: Props) {
  return (
    <div
      dangerouslySetInnerHTML={{ __html: DOMPurify.sanitize(content) }}
      className={cn('prose mt-4 max-w-none', className)}
    ></div>
  );
}
