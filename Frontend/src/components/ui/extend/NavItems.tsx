import { cn, isHere } from '@/lib/utils';
import { useMemo } from 'react';
import { NavLink, useLocation } from 'react-router';
import { home, release, podcast, videos, blog } from '@/assets/images';
import usePrefetchBlogsPageBlogs from '@/hooks/queries/prefetch/usePrefetchBlogsPageBlogs';
import usePrefetchPodcastsPageEpisodes from '@/hooks/queries/prefetch/usePrefetchPodcastsPageEpisodes';
import usePrefetchHomePageBlogs from '@/hooks/queries/prefetch/usePrefetchHomePageBlogs';
import usePrefetchReleasesPage from '@/hooks/queries/prefetch/usePrefetchReleasesPage';
import usePrefetchVideosPage from '@/hooks/queries/prefetch/usePrefetchVideosPage';

type Props = {
  onSelect?: () => void;
};

export default function NavItems({ onSelect }: Props) {
  const location = useLocation();
  const { handlePrefetchBlogsPageBlogs } = usePrefetchBlogsPageBlogs();
  const { handlePrefetchHomePageBlogs } = usePrefetchHomePageBlogs();
  const { handlePrefetchPodcastsPageEpisodes } = usePrefetchPodcastsPageEpisodes();
  const { handlePrefetchReleasesPage } = usePrefetchReleasesPage();
  const { handlePrefetchVideosPage } = usePrefetchVideosPage();

  const mainMenu = useMemo(
    () => [
      { icon: home, name: 'الرئيسية', to: '/', prefetch: handlePrefetchHomePageBlogs, iconAlt: 'home' },
      {
        icon: podcast,
        name: 'البودكاست',
        to: '/البودكاست',
        prefetch: handlePrefetchPodcastsPageEpisodes,
        iconAlt: 'podcast'
      },
      { icon: release, name: 'إصدارات', to: '/إصدارات', prefetch: handlePrefetchReleasesPage, iconAlt: 'release' },
      { icon: videos, name: 'الفيديوهات', to: '/الفيديوهات', prefetch: handlePrefetchVideosPage, iconAlt: 'videos' },
      { icon: blog, name: 'المدونة', to: '/المدونة', prefetch: handlePrefetchBlogsPageBlogs, iconAlt: 'blog' }
    ],
    [
      handlePrefetchBlogsPageBlogs,
      handlePrefetchHomePageBlogs,
      handlePrefetchPodcastsPageEpisodes,
      handlePrefetchReleasesPage,
      handlePrefetchVideosPage
    ]
  );

  return mainMenu.map((item) => (
    <li
      key={item.name}
      className={cn(
        'rounded-lg ps-1 pe-2 pt-1 transition-all',
        isHere(item.to, location.pathname) && 'text-primary-foreground bg-black'
      )}
      onMouseEnter={item.prefetch}
    >
      <NavLink onClick={onSelect} to={item.to} className="flex items-start gap-1 text-lg">
        <img
          src={item.icon}
          alt={item.iconAlt}
          className={`h-6 contrast-200 ${isHere(item.to, location.pathname) ? 'invert' : ''}`}
        />
        {item.name}
      </NavLink>
    </li>
  ));
}
