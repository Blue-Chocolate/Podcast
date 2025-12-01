//TODO: /api/episodes ?limit=x&page=x
//Returns list of episodes
//Paginated with limit and page queries
type episodesResponse = {
  success: boolean;
  data: [
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

//TODO: /api/episodes/{episode_id}
//Returns single episode details,
type episodeDetailsResponse = {
  success: boolean;
  data: {
    id: number;
    title: string;
    short_description: string;
    description: string;
    views: number;
    image: string;
    audio_url: string;
    video_url: string;
    published_at: number;
    podcast: {
      id: number;
      name: string;
    };
  };
};
