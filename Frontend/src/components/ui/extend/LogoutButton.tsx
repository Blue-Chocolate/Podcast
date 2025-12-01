import { logoutUser } from '@/services/auth';
import { useMutation } from '@tanstack/react-query';
import SubmitButton from '../submit-button';
import { toast } from 'react-toastify';
import { useUserState } from '@/context/UserProvider';

export default function LogoutButton() {
  const { setUser } = useUserState();
  const { isPending, mutate } = useMutation({
    mutationKey: ['register'],
    mutationFn: logoutUser,
    onSuccess: () => {
      setUser(undefined);
      toast.warn('تم تسجيل الخروج');
    },
    onError: () => toast.error('فشل تسجيل الخروج')
  });

  return (
    <SubmitButton
      variant="secondary"
      size="sm"
      className="inline-block h-7"
      onClick={() => mutate()}
      isLoading={isPending}
    >
      خروج
    </SubmitButton>
  );
}
