import { Button } from '@/components/ui/button';
import { useDocumentHead } from '@/hooks/useDocumentHead';
import { ArrowLeftIcon, LucideHome } from 'lucide-react';
import { useNavigate, useRouteError } from 'react-router';

type RouterError = {
  status: number;
  statusText: string;
  internal: boolean;
  data: string;
  error: object;
};

export default function ErrorPage() {
  const navigate = useNavigate();

  const error = useRouteError() as RouterError;

  useDocumentHead({
    title: `حقق - ${error.statusText}`,
    description: error.data,
    ogTitle: `حقق - ${error.statusText}`,
    ogDescription: error.data
  });

  return (
    <div id="error-page" className="flex h-screen flex-col items-center justify-center gap-4">
      <h2 className="text-8xl">{error.status}</h2>
      <div className="flex gap-2 text-4xl">
        <p>{error.status === 404 ? 'يبدو انه لا يجد شيء هنا !' : 'حدث خطاء غير متوقع.'}</p>
      </div>
      {error.status === 404 ? (
        <div>
          <Button className="me-2" onClick={() => navigate(-1)}>
            <ArrowLeftIcon /> رجوع
          </Button>
          <Button onClick={() => navigate('/')}>
            <LucideHome /> الرئيسية
          </Button>
        </div>
      ) : (
        <Button onClick={() => location.reload()}>محاولة</Button>
      )}
    </div>
  );
}
