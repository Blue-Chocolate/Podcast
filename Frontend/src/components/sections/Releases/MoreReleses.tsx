import ReleaseCard from '@/components/cards/ReleaseCard';
import type { ReleaseListItem } from '@/schemas/types';

export default function MoreRelesesSection({ releases }: { releases: ReleaseListItem[] }) {
  return (
    <div className="grid grid-cols-1 gap-y-10 xl:grid-cols-2">
      {releases.map((release) => (
        <ReleaseCard
          key={release.id}
          id={release.id}
          imageSrc={release.image}
          title={release.title}
          description={release.short_description}
          publishDate={release.published_at}
        />
      ))}
    </div>
  );
}
