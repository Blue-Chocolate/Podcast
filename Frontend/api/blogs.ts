//TODO: /api/blogs ?limit=x&page=x
//Returns list of blogs
//Paginated with limit and page queries
type blogsResponse = {
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

//TODO: /api/blogs/{blog_id}
//Returns single Blog details,
type BlogDetailsResponse = {
  success: boolean;
  data: {
    id: number;
    title: string;
    description: string;
    content: string;
    views: number;
    image: string;
    published_at: number;
    header_image: string;
    announcement: string;
    user_name: string;
    category: {
      id: number;
      name: string;
    };
  };
};

//TODO: /api/categories/blogs/{Category_id} ?limit=x&page=x
//Returns single category with list of blogs under this category,
//pagenated with limit and page queries
type BlogsCategoryDetailsResponse = {
  success: boolean;
  category: {
    id: number;
    name: string;
    image: string;
    description: string;
  };
  blogs: [
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

//TODO: /api/categories/blogs ?limit=x&page=x
//Returns list of categories only
// paginated with limit and page queries
type blogsCategoriesResponse = {
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
