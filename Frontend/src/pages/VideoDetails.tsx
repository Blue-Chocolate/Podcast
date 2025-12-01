import { videos } from '@/assets/images';
import SectionCard from '@/components/cards/SectionCard';
import MoreVideosSection from '@/components/sections/videos/MoreVideos';
import Img from '@/components/ui/extend/Img';
import SectionHeader from '@/components/ui/extend/SectionHeader';
import { VIDEO_QUERY_KEY, VIDEOS_CATEGORY_QUERY_KEY } from '@/constants/query-keys';
import { useDocumentHead } from '@/hooks/useDocumentHead';
import DefaultMotionElement from '@/layouts/DefaultMotionElement';
import { getYouTubeEmbedUrl, parsedDate, remote } from '@/lib/utils';
import { getVideoDetails, getVideosCategoryDetails } from '@/services/getVideos';
import { useQuery } from '@tanstack/react-query';
import { useParams } from 'react-router';

export default function VideoDetailsPage() {
  const { id = '' } = useParams<{ id: string }>();

  const { data, isFetched } = useQuery({
    queryKey: [VIDEO_QUERY_KEY, id],
    queryFn: () => getVideoDetails(id),
    throwOnError: true,
    retry: 0
  });

  const video = data?.data;

  const relatedVideosQuery = useQuery({
    queryKey: [VIDEOS_CATEGORY_QUERY_KEY, video?.category.id],
    queryFn: () => getVideosCategoryDetails(video?.category.id || '', { page: 1, limit: 6 }),
    enabled: !!video?.category.id && isFetched
  });
  const relatedVideos = relatedVideosQuery.data?.videos || [];

  useDocumentHead({
    title: `حقق - ${video?.title}`,
    description: video?.description,
    ogTitle: `حقق - ${video?.title}`,
    ogDescription: video?.description
  });

  return (
    <div className="container my-8 space-y-12">
      <header className="space-y-2">
        <DefaultMotionElement className="mb-4 text-[28px] font-medium" as="h1">
          {video?.title}
        </DefaultMotionElement>

        <DefaultMotionElement as="p" className="text-muted mb-4 pe-14">
          {video?.description}
        </DefaultMotionElement>

        <DefaultMotionElement as="p">
          في <span className="font-bold">{video?.category.name}</span>
          <span className="text-muted ms-3 inline-block text-sm">{parsedDate(video?.created_at)}</span>
        </DefaultMotionElement>

        <DefaultMotionElement className="mt-8 flex justify-center">
          {video?.video_url ? (
            <iframe
              src={getYouTubeEmbedUrl(video.video_url)}
              className="aspect-video w-full max-w-4xl rounded-2xl bg-black object-contain"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowFullScreen
            />
          ) : (
            <Img
              src={remote(video?.image)}
              alt={video?.title}
              className="aspect-video w-full max-w-4xl rounded-2xl bg-black object-contain"
            />
          )}
        </DefaultMotionElement>
      </header>

      <section>
        <DefaultMotionElement className="container space-y-8">
          <SectionCard>
            <p className="text-muted mt-4 text-xl">{video?.description}</p>
          </SectionCard>
        </DefaultMotionElement>
      </section>

      {relatedVideos.length > 0 && (
        <section className="space-y-4">
          <SectionHeader title="المزيد من حلقات حقق" icon={videos} />
          <MoreVideosSection videos={relatedVideos} />
        </section>
      )}
    </div>
  );
}
