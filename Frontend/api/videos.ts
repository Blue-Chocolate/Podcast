//TODO: /api/videos ?limit=x&page=x
//Returns list of Videos
//Paginated with limit and page queries
type VideosResponse = {
  success: boolean;
  data: [
    {
      id: number;
      title: string;
      description: string;
      views: number;
      image: string;
      published_at: number;
      category: {
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

//TODO: /api/videos/{video_id}
//Returns single Video details,
type VideoDetailsResponse = {
  success: boolean;
  data: {
    id: number;
    title: string;
    short_description: string;
    description: string;
    views: number;
    image: string;
    video_url: string;
    published_at: number;
    category: {
      id: number;
      name: string;
    };
  };
};

//TODO: /api/categories/videos/{Category_id} ?limit=x&page=x
//Returns single category with list of videos under this category,
//pagenated with limit and page queries
type VideosCategoryDetailsResponse = {
  success: boolean;
  category: {
    id: number;
    name: string;
    image: string;
    description: string;
  };
  videos: [
    {
      id: number;
      title: string;
      description: string;
      views: number;
      image: string;
      published_at: number;
      category: {
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

//TODO: /api/categories/videos ?limit=x&page=x
//Returns list of categories only
// paginated with limit and page queries
type VideosCategoriesResponse = {
  success: boolean;
  data: [
    {
      id: number;
      name: string;
      image: string;
      description: string;
    }
  ];
  pagination: {
    current_page: number;
    per_page: number;
    total_items: number;
    last_page: number;
  };
};
