import { cn } from '@/lib/utils';

type Props = {} & React.HTMLAttributes<HTMLDivElement>;

export default function HSplit({ className, ...props }: Props) {
  return (
    <div className={cn('flex w-full items-center border-t', className)} {...props}>
      <hr className="flex-grow" />
    </div>
  );
}
