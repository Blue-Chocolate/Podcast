import { cn } from '@/lib/utils';
import Img, { type CustomeImgProps } from '../ui/extend/Img';
import DefaultMotionDiv from '@/layouts/DefaultMotionElement';
import { Link } from 'react-router';
import { memo } from 'react';

type Props = { to: string } & CustomeImgProps;

const SquareImage = memo(({ className, to, ...props }: Props) => {
  return (
    <DefaultMotionDiv className={cn('aspect-square', className)}>
      <Link to={to}>
        <Img {...props} className="h-full w-full rounded-[10px] object-cover" />
      </Link>
    </DefaultMotionDiv>
  );
});

SquareImage.displayName = 'SquareImage';
export default SquareImage;
