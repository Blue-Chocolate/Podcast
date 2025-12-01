import { cn } from '@/lib/utils';
import type { LabelHTMLAttributes } from 'react';

type Props = LabelHTMLAttributes<HTMLLabelElement>;

export function Label({ children, className, ...props }: Props) {
  return (
    <label className={cn('text-muted mb-0.5 inline-block text-base', className)} {...props}>
      {children}
    </label>
  );
}
