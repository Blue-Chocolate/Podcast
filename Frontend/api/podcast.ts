//TODO: /api/podcasts ?limit=x&page=x
//Returns list of podcasts
//Paginated with limit and page queries
type podcastsResponse = {
  success: boolean;
  data: [
    {
      id: number;
      title: string;
      description: string;
      image: string;
    }
  ];
  pagination: {
    current_page: number;
    per_page: number;
    total_items: number;
    last_page: number;
  };
};

//TODO: /api/podcasts/{podcast_id}
//Returns single podcast details,
type podcastDetailsResponse = {
  success: boolean;
  podcast: {
    id: number;
    title: string;
    description: string;
    image: string;
  };
  episodes: [
    {
      id: number;
      title: string;
      description: string;
      views: number;
      image: string;
      audio_url: string;
      published_at: number;
      podcast: {
        id: number;
        name: string;
      };
    }
  ];
  pagination: {
    current_page: number;
    per_page: number;
    total_items: number;
    last_page: number;
  };
};
