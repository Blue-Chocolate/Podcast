import { cn } from '@/lib/utils';
import React from 'react';

type Props = {
  children: React.ReactNode;
  className?: string;
};

export default function SectionCard({ children, className }: Props) {
  return <div className={cn('rounded-2xl bg-white p-6 drop-shadow-sm', className)}>{children}</div>;
}
