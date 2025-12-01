import { Button } from '@/components/ui/button';
import ErrorMessage from '@/components/ui/extend/error-message';
import FormInput from '@/components/ui/extend/FormInput';
import Logo from '@/components/ui/logo';
import SubmitButton from '@/components/ui/submit-button';
import { useDocumentHead } from '@/hooks/useDocumentHead';
import { subscribeWhatsApp } from '@/services/subscribe';
import { useMutation } from '@tanstack/react-query';
import { isAxiosError } from 'axios';
import { CircleCheckBig, LucideHome } from 'lucide-react';
import { useState } from 'react';
import { Link } from 'react-router';

export default function JoinPage() {
  const [subscribeState, setSubscribeState] = useState(false);
  const [error, setError] = useState<string | null>();
  const [phone, setPhone] = useState('');

  const { isPending, mutate } = useMutation({
    mutationKey: ['subscribe'],
    mutationFn: () => subscribeWhatsApp({ phone }),
    onSuccess: async () => setSubscribeState(true),
    onError: (err) => {
      if (isAxiosError(err)) {
        setError(err.response?.data.message);
      } else setError('لم نتمكن من تسجيلك, حاول لاحقا');
    }
  });

  const submitForm = () => mutate();

  useDocumentHead({
    title: `حقق - اشترك ليصلك كل جديد`,
    description: 'اشترك في خدمة حقق على الواتس اب ليصلك كل جديد من حقق من بودكاست، مقالات، وفيديوهات مباشرة على هاتفك.',
    ogTitle: `حقق - اشترك ليصلك كل جديد`,
    ogDescription:
      'اشترك في خدمة حقق على الواتس اب ليصلك كل جديد من حقق من بودكاست، مقالات، وفيديوهات مباشرة على هاتفك.'
  });

  return (
    <section className="container mt-22 flex min-h-[70vh] flex-col items-center gap-8">
      {subscribeState ? (
        <>
          <CircleCheckBig className="stroke-succces" size={150} />
          <p className="text-muted text-center text-xl font-bold md:text-4xl">
            لقد اشتركت بنجاح سوف يصلك كل جديد من حقق
          </p>
          <Button>
            <Link to="/" className="flex items-center gap-2">
              <LucideHome /> إلي الصفحة الرئيسية
            </Link>
          </Button>
        </>
      ) : (
        <>
          <Logo className="w-48 transition-transform hover:scale-105" />

          <h1 className="text-muted max-w-2xl text-center text-2xl leading-relaxed font-medium">
            جملة تعريفية عن حقق و خدمات حقق و لماذا اشترك فيه
          </h1>

          <form
            onSubmit={submitForm}
            className="flex w-full max-w-md items-stretch justify-end gap-1 rounded-lg bg-white p-2 shadow-lg"
          >
            <FormInput onChange={(e) => setPhone(e.target.value)} className="flex-1" placeholder="رقم الهاتف" />
            <SubmitButton isLoading={isPending} className="h-auto px-8">
              اشترك
            </SubmitButton>
          </form>
          <ErrorMessage error={error} />
        </>
      )}
    </section>
  );
}
