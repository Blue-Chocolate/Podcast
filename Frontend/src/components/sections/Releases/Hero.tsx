import Img from '@/components/ui/extend/Img';
import ViewsBadge from '@/components/ui/extend/ViewsBadge';
import { parsedDate } from '@/lib/utils';
import DefaultMotionDiv from '@/layouts/DefaultMotionElement';
import { Link } from 'react-router';
import usePrefetchRelease from '@/hooks/queries/prefetch/usePrefetchRelease';
import SubmitButton from '@/components/ui/submit-button';
import type { ReleaseListItem } from '@/schemas/types';
import { useDownloadRelease } from '@/hooks/queries/useDownloadRelease';

export default function ReleasesHeroSection({ heroRelease }: { heroRelease: ReleaseListItem }) {
  const { handlePrefetchRelease } = usePrefetchRelease();

  const downloadReleaseMutation = useDownloadRelease({});
  const handleDownload = () => downloadReleaseMutation.mutate({ id: heroRelease.id, title: heroRelease.title });
  const busy = downloadReleaseMutation.isPending;

  return (
    <DefaultMotionDiv
      onMouseEnter={() => handlePrefetchRelease(heroRelease?.id || '')}
      className="mt-10 flex flex-col overflow-clip md:h-96 md:flex-row md:items-end"
    >
      <Link to={`/إصدارات/${heroRelease?.id}`} className="overflow-clip md:h-96">
        <Img
          src={heroRelease?.image}
          alt={heroRelease?.title}
          loading="eager"
          fetchPriority="high"
          className="h-full w-full rounded-2xl object-cover object-top"
        />
      </Link>

      <div className="basis-2/3 p-4">
        <h1 className="mb-4 text-[28px] font-medium">
          <Link to={`/إصدارات/${heroRelease?.id}`}>{heroRelease?.title}</Link>
        </h1>
        <p className="text-muted mb-4 hidden lg:block xl:me-18">
          {(heroRelease?.short_description || '').slice(0, 350)}
        </p>
        <p className="text-muted mb-4 block lg:hidden xl:me-18">
          {(heroRelease?.short_description || '').slice(0, 220)}
        </p>

        <p>
          في <span className="font-bold">{heroRelease?.title}</span>
          <span className="text-muted ms-3 inline-block text-sm">{parsedDate(heroRelease?.published_at)}</span>
        </p>

        <ViewsBadge views={0} />

        <SubmitButton
          isLoading={busy}
          onClick={handleDownload}
          className="me-4 h-11 w-32 text-3xl"
          disabled={busy}
          aria-busy={busy}
        >
          تحميــــل
        </SubmitButton>
        <a
          href={'/'}
          target="_blank"
          rel="noopener noreferrer"
          className="border-foreground text-foreground inline-flex h-11 w-32 justify-center rounded-md border-2 bg-transparent py-1 text-3xl hover:no-underline"
          aria-busy={busy}
        >
          قــــراءة
        </a>
      </div>
    </DefaultMotionDiv>
  );
}
