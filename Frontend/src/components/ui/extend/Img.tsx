import { fallbackImage } from '@/assets/images';
import { remote } from '@/lib/utils';
import { useState } from 'react';

export type CustomeImgProps = {
  src: string | null | undefined;
  alt: string | null | undefined;
  className?: string;
} & Omit<React.ImgHTMLAttributes<HTMLImageElement>, 'src' | 'alt'>;

export default function Img({ src, alt, className, loading = 'lazy', ...props }: CustomeImgProps) {
  const [error, setError] = useState(false);

  if (!src || error)
    return <img src={fallbackImage} alt={alt ?? ''} className={className} loading={loading} {...props} />;

  return (
    <img
      src={remote(src)}
      alt={alt ?? ''}
      className={className}
      onError={() => setError(true)}
      loading="lazy"
      {...props}
    />
  );
}
