import type { BlogDetails, BlogListItem, Category, Pagination } from '@/schemas/types';
import api from './api';

type BlogsResponse = {
  success: boolean;
  data: BlogListItem[];
  pagination: Pagination;
};

type BlogDetailsResponse = {
  success: boolean;
  data: BlogDetails;
};

type BlogsCategoryDetailsResponse = {
  success: boolean;
  category: Category;
  blogs: BlogListItem[];
  pagination: Pagination;
};

type BlogsCategoriesResponse = {
  success: boolean;
  data: Category[];
  pagination: Pagination;
};

export async function getBlogs({ page, limit = 5 }: { page: number; limit?: number }) {
  return await api.get<BlogsResponse>(`/api/blogs`, { params: { page, limit } }).then((res) => res.data);
}

export async function getBlogDetails(id: string | number) {
  return api.get<BlogDetailsResponse>(`/api/blogs/${id}`).then((res) => res.data);
}

export async function getBlogsCategories({ limit, page }: { limit?: number; page?: number }) {
  return api
    .get<BlogsCategoriesResponse>(`/api/categories/blogs`, { params: { limit, page } })
    .then((res) => res.data.data);
}

export async function getBlogsCategoryDetails(id: string | number, { limit, page }: { limit?: number; page?: number }) {
  return api
    .get<BlogsCategoryDetailsResponse>(`/api/categories/blogs/${id}`, { params: { limit, page } })
    .then((res) => res.data);
}
