import { downloadRelease } from '@/services/DownloadRelease';
import { useMutation } from '@tanstack/react-query';
import { isAxiosError } from 'axios';
import { toast } from 'react-toastify';

type Props = {
  onSuccess?: () => void;
  onError?: (error: unknown) => void;
};

export const useDownloadRelease = ({ onSuccess, onError }: Props) => {
  return useMutation({
    mutationFn: ({ id, title }: { id: string | number; title: string }) => downloadRelease(id, `${title}.pdf`, false),
    onSuccess: () => onSuccess?.(),
    onError: (error) => {
      onError?.(error);
      if (isAxiosError(error)) {
        if (error.response?.status === 401) toast.error('يجب تسجيل الدخول لتحميل هذا الملف');
        if (error.response?.status === 404) toast.error('لم يتم العثور على الملف');
      } else {
        toast.error('حدث خطأ غير متوقع حاول مرة اخرى لاحقا');
      }
    }
  });
};
