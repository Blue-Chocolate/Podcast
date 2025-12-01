import SectionCard from '@/components/cards/SectionCard';
import { Button } from '@/components/ui/button';
import ErrorMessage from '@/components/ui/extend/error-message';
import FormInput from '@/components/ui/extend/FormInput';
import { Label } from '@/components/ui/label';
import Logo from '@/components/ui/logo';
import SubmitButton from '@/components/ui/submit-button';
import { Textarea } from '@/components/ui/textarea';
import { useDocumentHead } from '@/hooks/useDocumentHead';
import { contactSchema, type ContactForm } from '@/schemas/validation';
import { contact } from '@/services/subscribe';
import { zodResolver } from '@hookform/resolvers/zod';
import { useMutation } from '@tanstack/react-query';
import { isAxiosError } from 'axios';
import { CircleCheckBig, LucideHome } from 'lucide-react';
import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { Link } from 'react-router';

export default function ContactUsPage() {
  const [messageState, setMessageState] = useState(false);
  const [error, setError] = useState<string | null>();

  const {
    register,
    handleSubmit,
    formState: { errors }
  } = useForm<ContactForm>({
    resolver: zodResolver(contactSchema)
  });

  const { isPending, mutate } = useMutation({
    mutationKey: ['subscribe'],
    mutationFn: (form: ContactForm) => contact(form),
    onSuccess: async () => setMessageState(true),
    onError: (err) => {
      if (isAxiosError(err)) {
        setError(err.response?.data.message);
      } else setError('لم نتمكن من استقبال رسالتك, حاول لاحقا');
    }
  });

  const submitForm = handleSubmit((form: ContactForm) => mutate(form));

  useDocumentHead({
    title: `حقق - تواصل معنا`,
    description: `تواصل معنا عبر ملء النموذج أدناه`,
    ogTitle: `حقق - تواصل معنا`,
    ogDescription: `تواصل معنا عبر ملء النموذج أدناه`
  });

  return (
    <section className="container my-12 min-h-[70vh]">
      <form onSubmit={submitForm}>
        <SectionCard className="mx-auto max-w-3xl">
          {messageState ? (
            <div className="flex flex-col items-center gap-6 text-center">
              <CircleCheckBig className="stroke-succces" size={150} />
              <div>
                <p className="text-muted text-4xl font-bold">تم استلام رسالتك,</p>
                <p className="text-muted mt-2 text-xl">سوف نتواصل معك في اقرب وقت ممكن</p>
              </div>
              <Button>
                <Link to="/" className="flex items-center gap-2">
                  <LucideHome /> إلي الصفحة الرئيسية
                </Link>
              </Button>
            </div>
          ) : (
            <>
              <div className="flex w-full flex-col-reverse items-center gap-8 sm:flex-row sm:gap-12">
                <div className="w-full flex-1 space-y-1 sm:w-auto">
                  <h1 className="mb-8 text-xl font-bold">تواصل معنا</h1>
                  <FormInput {...register('name')} error={errors.name?.message} label="الاسم" />
                  <FormInput {...register('email')} error={errors.email?.message} label="البريد الالكتروني" />
                  <FormInput {...register('phone')} error={errors.phone?.message} label="رقم الجوال" />
                  <FormInput {...register('subject')} error={errors.subject?.message} label="عنوان للرسالة" />
                  <div>
                    <Label>محتوي التواصل</Label>
                    <Textarea
                      {...register('message')}
                      className={`min-h-56 ${errors.message?.message ? 'border-destructive' : ''}`}
                    />
                    <ErrorMessage error={errors.message?.message} />
                  </div>
                </div>

                <div className="w-52 space-y-20">
                  <Logo className="w-full" />
                </div>
              </div>

              <ErrorMessage error={error} />
              <SubmitButton isLoading={isPending} className="block w-full">
                أرسال
              </SubmitButton>
            </>
          )}
        </SectionCard>
      </form>
    </section>
  );
}
