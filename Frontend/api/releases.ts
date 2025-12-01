//TODO: /api/releases ?limit=x&page=x
//Returns list of releases
//Paginated with limit and page queries
type ReleasesResponse = {
  success: boolean;
  data: [
    {
      id: number;
      title: string;
      short_description: string;
      image: string;
      views: number;
      published_at: number;
      pdf_url: string; // can be removed if there is an api to download with id
    }
  ];
  pagination: {
    current_page: number;
    per_page: number;
    total_items: number;
    last_page: number;
  };
};

//TODO: /api/releases/{release_id}
//Returns single release details,
type ReleaseDetailsResponse = {
  success: boolean;
  data: {
    id: number;
    title: string;
    short_description: string;
    description: string;
    image: string;
    views: number;
    published_at: number;
    pdf_url: string; // can be removed if there is an api to download with id
  };
};
