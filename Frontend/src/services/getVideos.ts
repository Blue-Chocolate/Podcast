import type { Category, Pagination, VideoDetails, VideoListItem } from '@/schemas/types';
import api from './api';

type VideosResponse = {
  success: boolean;
  data: VideoListItem[];
  pagination: Pagination;
};

type VideoDetailsResponse = {
  success: boolean;
  data: VideoDetails;
};

type VideosCategoriesResponse = {
  success: boolean;
  data: Category[];
  pagination: Pagination;
};

type Link = {
  url: string | null;
  label: string;
  page: number | null;
  active: boolean;
};

type VideoItem = {
  id: number;
  title: string;
  description: string;
  views_count: number;
  video_category_id: number;
  image_path: string;
  video_path: string;
  created_at: string;
  updated_at: string;
};

type PaginationVideos = {
  current_page: number;
  data: VideoItem[];
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: Link[];
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
};

type CategoryData = {
  id: number;
  name: string;
  description: string;
  image_path: string;
  slug: string;
  is_active: number;
  views_count: number;
  created_at: string;
  updated_at: string;
  videos: PaginationVideos;
};

type VideosCategoryDetailsResponse = {
  success: boolean;
  status: string;
  message: string;
  data: CategoryData;
};

export async function getVideos({ page, limit = 5 }: { page?: number; limit?: number }) {
  return await api.get<VideosResponse>(`/api/videos`, { params: { page, limit } }).then((res) => res.data);
}

export async function getVideoDetails(id: string | number) {
  return api.get<VideoDetailsResponse>(`/api/videos/${id}`).then((res) => res.data);
}

export async function getVideosCategories(params?: { limit: number; page: number }) {
  return api.get<VideosCategoriesResponse>(`/api/categories/videos`, { params }).then((res) => res.data);
}

export async function getVideosCategoryDetails(id: string | number, params?: { limit: number; page: number }) {
  const response = await api.get<VideosCategoryDetailsResponse>(`/api/categories/videos/${id}`, { params });
  const { success, data } = response.data;

  const category = {
    id: data.id,
    name: data.name,
    image: data.image_path,
    description: data.description
  };

  const videos: VideoListItem[] = data.videos.data.map((video) => ({
    id: video.id,
    title: video.title,
    description: video.description,
    views: video.views_count,
    image: video.image_path,
    created_at: video.created_at,
    category: {
      id: video.video_category_id,
      name: data.name
    }
  }));

  const pagination = {
    current_page: data.videos.current_page,
    per_page: data.videos.per_page,
    total_items: data.videos.total,
    last_page: data.videos.last_page
  };

  return {
    success,
    category,
    videos,
    pagination
  };
}
