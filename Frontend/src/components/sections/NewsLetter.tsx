import { useMutation } from '@tanstack/react-query';
import SectionCard from '../cards/SectionCard';
import FormInput from '../ui/extend/FormInput';
import Logo from '../ui/logo';
import SubmitButton from '../ui/submit-button';
import { subscribeSchema, type SubscribeForm } from '@/schemas/validation';
import { useForm } from 'react-hook-form';
import { subscribeNewsLetter } from '@/services/subscribe';
import { zodResolver } from '@hookform/resolvers/zod';
import { useState } from 'react';
import ErrorMessage from '../ui/extend/error-message';
import { isAxiosError } from 'axios';
import DefaultMotionElement from '@/layouts/DefaultMotionElement';
import { CircleCheckBig } from 'lucide-react';

export default function NewsLetter() {
  const [subscribeState, setSubscribeState] = useState(false);
  const [error, setError] = useState<string | null>();
  const {
    register,
    handleSubmit,
    formState: { errors }
  } = useForm<SubscribeForm>({
    resolver: zodResolver(subscribeSchema)
  });

  const { isPending, mutate } = useMutation({
    mutationKey: ['subscribe'],
    mutationFn: (form: SubscribeForm) => subscribeNewsLetter(form),
    onSuccess: async () => setSubscribeState(true),
    onError: (err) => {
      if (isAxiosError(err)) {
        setError(err.response?.data.message);
      } else setError('لم نتمكن من تسجيلك, حاول لاحقا');
    }
  });

  const submitForm = handleSubmit((form: SubscribeForm) => mutate(form));

  return (
    <DefaultMotionElement>
      <SectionCard className="sm:px-18 md:mx-16">
        {subscribeState ? (
          <p className="text-succces flex items-center gap-2">
            <span>
              <CircleCheckBig className="stroke-succces" size={25} />
            </span>
            انت لان مشترك في نشرة حقق الالكترونية
          </p>
        ) : (
          <div className="flex flex-col items-center gap-4 py-12 lg:flex-row">
            <Logo className="w-40" />
            <div className="flex-1 space-y-2">
              <p className="text-2xl font-bold">ابق مطلع علي اخر اخبار و تحديثات حقق عبر نشرتنا الالكترونية</p>
              <p className="text-xl">الاتصال المؤسسي و أثرة علي القطاع الغير ربحي </p>

              <Logo className="w-10" />

              <form className="mt-12 flex w-full flex-col items-stretch gap-1 sm:flex-row" onSubmit={submitForm}>
                <FormInput
                  className="sm:basis-5/12"
                  placeholder="اسمك الأول"
                  {...register('first_name')}
                  error={errors.first_name?.message}
                />
                <FormInput
                  className="sm:basis-5/12"
                  placeholder="أدخل بريدك الالكتروني هنا"
                  {...register('email')}
                  error={errors.email?.message}
                />
                <SubmitButton className="h-auto sm:basis-2/12" isLoading={isPending}>
                  أشترك
                </SubmitButton>
              </form>
              <ErrorMessage error={error} />
            </div>
          </div>
        )}
      </SectionCard>
    </DefaultMotionElement>
  );
}
