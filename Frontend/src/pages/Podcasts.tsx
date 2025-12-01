import { podcast, star } from '@/assets/images';
import PodcastsHeroSection from '@/components/sections/podcast/Hero';
import MainPodcastsSection from '@/components/sections/podcast/RecentEpisodes';
import MorePodcastsSection from '@/components/sections/podcast/MoreEpisodes';
import SpecialProgramsSection from '@/components/sections/podcast/SpecialPrograms';
import SectionHeader from '@/components/ui/extend/SectionHeader';
import HSplit from '@/components/ui/h-split';
import { PODCASTS_PAGE_EPISODES_QUERY_KEY } from '@/constants/query-keys';
import DataWrapper from '@/layouts/DataWrapper';
import { useQuery } from '@tanstack/react-query';
import { getEpisodes } from '@/services/getEpisodes';
import { useDocumentHead } from '@/hooks/useDocumentHead';

export default function PodcastsPage() {
  const { data, isPending, isError, refetch, isFetching } = useQuery({
    queryKey: [PODCASTS_PAGE_EPISODES_QUERY_KEY],
    queryFn: () => getEpisodes({ page: 1, limit: 15 })
  });
  const episodes = data ? [...data] : [];
  const hero = episodes.shift();
  const moreEpisodes = episodes.splice(0, 4);

  useDocumentHead({
    title: `حقق - البودكاست`,
    description: 'اكتشف أحدث حلقات البودكاست من حقق واستمتع بالمحتوى الصوتي المميز في مختلف المجالات.',
    ogTitle: `حقق - البودكاست`,
    ogDescription: 'اكتشف أحدث حلقات البودكاست من حقق واستمتع بالمحتوى الصوتي المميز في مختلف المجالات.'
  });

  return (
    <div className="mt-8 mb-28 space-y-12">
      <header className="container space-y-8">
        <SectionHeader icon={podcast} title="البودكـــــاست" />
        <DataWrapper isError={isError} isPending={isPending} retry={refetch} isRefetching={isFetching} isEmpty={!hero}>
          {hero && <PodcastsHeroSection heroEpisode={hero} />}
        </DataWrapper>
      </header>

      <section className="bg-accent py-8">
        <div className="container">
          <DataWrapper
            isError={isError}
            isPending={isPending}
            retry={refetch}
            isRefetching={isFetching}
            isEmpty={!moreEpisodes.length}
          >
            <MainPodcastsSection episodes={moreEpisodes} />
          </DataWrapper>
        </div>
      </section>

      <section className="container space-y-8">
        <SectionHeader icon={star} title="برامج متميزة" as="h2" />
        <SpecialProgramsSection />
        <HSplit className="mt-14 border-t-[#989696CC]" />
      </section>

      <section className="container mt-20">
        <DataWrapper
          isError={isError}
          isPending={isPending}
          retry={refetch}
          isRefetching={isFetching}
          isEmpty={!episodes.length}
        >
          <MorePodcastsSection episodes={episodes} />
        </DataWrapper>
      </section>
    </div>
  );
}
