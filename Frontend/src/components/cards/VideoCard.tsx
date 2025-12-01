import { cn, parsedDate, remote } from '@/lib/utils';
import Img from '../ui/extend/Img';
import { VideoIcon } from '../ui/icons';
import DefaultMotionDiv from '@/layouts/DefaultMotionElement';
import { Link } from 'react-router';
import { memo } from 'react';
import usePrefetchVideo from '@/hooks/queries/prefetch/usePrefetchVideo';

type Props = {
  thumbnail: string;
  title: string;
  publishDate: string;
  category?: string | null;
  categoryId?: string | number;
  description?: string | null;
  id: string | number;
};

const VideoCard = memo(({ thumbnail, category, publishDate, title, description, id, categoryId }: Props) => {
  const { handlePrefetchVideo } = usePrefetchVideo();
  const isWide = description || description === null || description === '';

  return (
    <DefaultMotionDiv
      className={cn('w-full', isWide ? 'flex flex-col items-start sm:flex-row' : '')}
      delay={0.2}
      onMouseEnter={() => handlePrefetchVideo(id)}
    >
      <Link
        to={`/الفيديوهات/${id}`}
        className={cn('relative block aspect-video w-full shrink-0', isWide ? 'sm:w-64' : '')}
      >
        <div className="absolute inset-0 flex items-center justify-center">
          <VideoIcon size={60} />
        </div>
        <Img src={remote(thumbnail)} alt="" className="h-full w-full rounded-xl object-cover" />
      </Link>
      <div className="px-2">
        <p className="mt-4 text-xl font-medium">{title}</p>
        <p className="mt-2">
          {category && 'في'}
          <Link to={`/الفيديوهات/تصنيف/${categoryId}`} className="font-bold">
            {' '}
            {category}
          </Link>
          <span className="text-muted ms-3 inline-block text-sm"> {parsedDate(publishDate)}</span>
        </p>
      </div>
    </DefaultMotionDiv>
  );
});

VideoCard.displayName = 'VideoCard';
export default VideoCard;
