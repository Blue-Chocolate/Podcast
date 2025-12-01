export interface Pagination {
  current_page: number;
  per_page: number;
  total_items: number;
  last_page: number;
}

export interface User {
  id: number | string;
  name: string;
  email: string;
  phone: string;
}

export interface Category {
  id: number;
  name: string;
  image: string;
  image_path: string;
  description: string;
}

export interface Podcast {
  id: number;
  title: string;
  description: string;
  image: string;
}

export interface ReleaseListItem {
  id: number;
  title: string;
  short_description: string;
  image: string;
  views: number;
  published_at: string;
}

export interface ReleaseDetails extends Omit<ReleaseListItem, 'image'> {
  success: boolean;
  id: number;
  title: string;
  description: string;
  images: string[];
  views: number;
  created_at: string;
  has_pdf: boolean;
  has_excel: boolean;
  has_powerbi: boolean;
}

export interface BlogListItem {
  id: number;
  title: string;
  description: string;
  views: number;
  image: string;
  published_at?: string;
  publish_date: string;
  category: {
    id: number;
    name: string;
  };
}

export interface BlogDetails extends BlogListItem {
  content: string;
  header_image: string;
  announcement: string;
  user_name: string;
}

export interface EpisodeListItem {
  id: number;
  title: string;
  description: string;
  views: number;
  views_count?: number;
  image: string;
  audio_url: string;
  published_at: string;
  podcast: {
    id: number;
    name: string;
  };
}

export interface EpisodeDetails extends EpisodeListItem {
  description: string;
  video_url: string;
}

export interface VideoListItem {
  id: number;
  title: string;
  description: string;
  views: number;
  image: string;
  created_at: string;
  category: {
    id: number;
    name: string;
  };
}

export interface VideoDetails extends VideoListItem {
  id: number;
  title: string;
  short_description: string;
  description: string;
  views: number;
  image: string;
  video_url: string;
  created_at: string;
  category: {
    id: number;
    name: string;
  };
}
