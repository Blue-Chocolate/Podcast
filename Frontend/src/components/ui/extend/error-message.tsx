import { cn } from '@/lib/utils';
import { CircleAlert } from 'lucide-react';

type Props = { error?: string | null; className?: string };

export default function ErrorMessage({ error, className }: Props) {
  return (
    <p className={cn('text-destructive mt-1 min-h-5 text-sm', className)}>
      {error && (
        <span className="flex items-center gap-1">
          <CircleAlert className="inline-block" size={13} /> <span>{error}</span>
        </span>
      )}
    </p>
  );
}
