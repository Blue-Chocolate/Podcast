import api from './api';
interface SearchResultData<T> {
  count: number;
  data: T[];
}

interface FullSearchResponse {
  success: true;
  query: string;
  total_tables_scanned: number;
  matched_tables: number;
  total_results: number;
  results: {
    blog_categories: SearchResultData<{
      id: number;
      name: string;
      image_path: string;
    }>;
    video_categories: SearchResultData<{
      id: number;
      name: string;
      image_path: string;
    }>;
    blogs?: SearchResultData<{
      id: number;
      title: string;
      image: string;
    }>;
    categories?: SearchResultData<{
      id: number;
      name: string;
      image: string;
    }>;
    releases?: SearchResultData<{
      id: number;
      title: string;
      images: string[];
    }>;
    videos?: SearchResultData<{
      id: number;
      title: string;
      image_path: string;
    }>;
    episodes?: SearchResultData<{
      id: number;
      title: string;
      cover_image: string;
    }>;
    podcasts?: SearchResultData<{
      id: number;
      title: string;
      cover_image: string;
    }>;
  };
}

export async function search(q: string) {
  return api.get<FullSearchResponse>('/api/search', { params: { q } }).then((res) => res.data);
}
