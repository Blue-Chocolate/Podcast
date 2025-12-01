import type { Pagination, ReleaseDetails, ReleaseListItem } from '@/schemas/types';
import api from './api';

type ReleasesResponse = {
  success: boolean;
  data: ReleaseListItem[];
  pagination: Pagination;
};

type ReleaseDetailsResponse = {
  success: boolean;
} & ReleaseDetails;

export async function getReleases({ page, limit = 5 }: { page: number; limit: number }) {
  return api
    .get<ReleasesResponse>('/api/releases', {
      params: { page, limit }
    })
    .then((res) => res.data.data);
}

export async function getReleaseDetails(id: string | number) {
  return api.get<ReleaseDetailsResponse>(`/api/releases/${id}`).then((res) => res.data);
}
