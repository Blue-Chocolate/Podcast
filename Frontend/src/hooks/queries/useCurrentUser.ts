import { CURRENT_USER_QUERY_KEY } from '@/constants/query-keys';
import { getToken } from '@/lib/token';
import type { User } from '@/schemas/types';
import { getCurrentUser } from '@/services/auth';
import { useQuery } from '@tanstack/react-query';

type Props = {
  onSuccess?: (data: User) => void;
};

export default function useCurrentUserQuery({ onSuccess }: Props) {
  return useQuery({
    queryKey: [CURRENT_USER_QUERY_KEY],
    queryFn: () =>
      getCurrentUser().then((data) => {
        onSuccess?.({ id: data.id, name: data.name, email: data.email, phone: data.phone_number || '' });
        return null;
      }),

    retry: false,
    refetchOnReconnect: false,
    refetchOnWindowFocus: false,
    enabled: () => !!getToken()
  });
}
