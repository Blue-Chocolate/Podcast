import type { InputHTMLAttributes } from 'react';
import { Input } from '../input';
import ErrorMessage from './error-message';
import { cn } from '@/lib/utils';
import { Label } from '../label';

type Props = InputHTMLAttributes<HTMLInputElement> & {
  error?: string | null;
  label?: string;
};

export default function FormInput({ label, className, error, id, ...props }: Props) {
  return (
    <>
      <Label htmlFor={id}>{label}</Label>
      <Input id={id} className={cn(error ? 'border-destructive' : 'border-slate-300', className)} {...props} />
      <ErrorMessage error={error} />
    </>
  );
}
