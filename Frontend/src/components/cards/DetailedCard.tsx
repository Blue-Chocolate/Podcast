import { parsedDate } from '@/lib/utils';
import SquareImage from './SquareImage';
import Audio from '../ui/extend/Audio';
import ViewsBadge from '../ui/extend/ViewsBadge';
import DefaultMotionDiv from '@/layouts/DefaultMotionElement';
import { Link } from 'react-router';
import { memo } from 'react';

export type DetailedCardProps = {
  title: string;
  description: string;
  imageSrc: string | null;
  publishDate: string;
  category: string;
  categoryTo: string;
  audioSrc?: string | null;
  views?: number;
  to: string;
};

const DetailedCard = memo(
  ({ audioSrc, title, description, imageSrc, publishDate, category, views, to, categoryTo }: DetailedCardProps) => {
    return (
      <DefaultMotionDiv delay={0.2} className="flex flex-col items-start sm:flex-row">
        <SquareImage className="w-auto shrink-0 sm:w-48" src={imageSrc || ''} alt={title} to={to} />
        <div className="w-full px-4 py-2 sm:my-0">
          <Link to={to} className="mb-2 block text-xl font-medium">
            {title}
          </Link>

          <p className="text-muted mb-4 text-[15px]">{(description || 'لا يوجد وصف ').slice(0, 120)}...</p>

          <p className="inline-block">
            في
            <Link to={categoryTo} className="font-bold">
              {' '}
              {category}
            </Link>
            <span className="text-muted ms-3 inline-block text-sm"> {parsedDate(publishDate)}</span>
          </p>

          {views && <ViewsBadge views={views} />}

          {audioSrc !== undefined && <Audio src={audioSrc} />}
        </div>
      </DefaultMotionDiv>
    );
  }
);

DetailedCard.displayName = 'DetailedCard';
export default DetailedCard;
