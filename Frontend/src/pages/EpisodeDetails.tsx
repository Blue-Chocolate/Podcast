import { videos } from '@/assets/images';
import SectionCard from '@/components/cards/SectionCard';
import MoreEpisodesSection from '@/components/sections/podcast/MoreEpisodes';
import HeroAudio from '@/components/ui/extend/HeroTarack';
import Img from '@/components/ui/extend/Img';
import SectionHeader from '@/components/ui/extend/SectionHeader';
import { EPISODE_QUERY_KEY, PODCAST_QUERY_KEY } from '@/constants/query-keys';
import { useDocumentHead } from '@/hooks/useDocumentHead';
import DataWrapper from '@/layouts/DataWrapper';
import DefaultMotionElement from '@/layouts/DefaultMotionElement';
import { getYouTubeEmbedUrl, parsedDate, remote } from '@/lib/utils';
import { getEpisodeDetails } from '@/services/getEpisodes';
import { getPodcastDetails } from '@/services/getPodcasts';
import { useQuery } from '@tanstack/react-query';
import { useParams } from 'react-router';

export default function EpisodeDetailsPage() {
  const { id = '' } = useParams<{ id: string }>();

  const { data, isError, isPending, refetch, isFetching, isFetched } = useQuery({
    queryKey: [EPISODE_QUERY_KEY, id],
    queryFn: () => getEpisodeDetails(id),
    throwOnError: true,
    retry: 0
  });

  const relatedEpisodesQuery = useQuery({
    queryKey: [PODCAST_QUERY_KEY, data?.podcast.id],
    queryFn: () => getPodcastDetails(data?.podcast.id || '', { page: 1, limit: 8 }),
    enabled: !!data?.podcast.id && isFetched
  });
  const relatedEpisodes = relatedEpisodesQuery.data?.episodes || [];

  useDocumentHead({
    title: `حقق - ${data?.title}`,
    description: data?.description,
    ogTitle: `حقق - ${data?.title}`,
    ogDescription: data?.description
  });

  return (
    <>
      <title>{data?.title} - حقق</title>

      <DataWrapper isError={isError} isPending={isPending} retry={refetch} isRefetching={isFetching} isEmpty={!data}>
        <div className="container my-8 space-y-12">
          <header className="space-y-2">
            <DefaultMotionElement className="mb-4 text-[28px] font-medium" as="h1">
              {data?.title}
            </DefaultMotionElement>

            <DefaultMotionElement as="p" className="text-muted mb-4 pe-14">
              {data?.description}
            </DefaultMotionElement>

            <DefaultMotionElement as="p">
              في <span className="font-bold">{data?.podcast.name}</span>
              <span className="text-muted ms-3 inline-block text-sm">{parsedDate(data?.published_at)}</span>
            </DefaultMotionElement>

            <DefaultMotionElement>
              <HeroAudio src={data?.audio_url} />
            </DefaultMotionElement>

            <DefaultMotionElement className="mt-8 flex justify-center">
              {data?.video_url ? (
                <iframe
                  src={getYouTubeEmbedUrl(data.video_url)}
                  className="aspect-video w-full max-w-4xl rounded-2xl bg-black object-contain"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowFullScreen
                />
              ) : (
                <Img
                  src={remote(data?.image)}
                  alt={data?.title}
                  className="aspect-video w-full max-w-4xl rounded-2xl bg-black object-contain"
                />
              )}
            </DefaultMotionElement>
          </header>

          <section>
            <DefaultMotionElement className="container space-y-8">
              <SectionCard>
                <p className="text-muted mt-4 text-xl">{data?.description}</p>
              </SectionCard>
            </DefaultMotionElement>
          </section>

          {relatedEpisodes.length > 0 && (
            <section className="space-y-4">
              <SectionHeader title="المزيد من مقاطع فيديو حقق" icon={videos} />
              <MoreEpisodesSection episodes={relatedEpisodes} />
            </section>
          )}
        </div>
      </DataWrapper>
    </>
  );
}
