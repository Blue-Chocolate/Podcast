import { user } from '@/assets/images';
import SectionHeader from '@/components/ui/extend/SectionHeader';
import FormInput from '@/components/ui/extend/FormInput';
import { useDocumentHead } from '@/hooks/useDocumentHead';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import SubmitButton from '@/components/ui/submit-button';
import SectionCard from '@/components/cards/SectionCard';
import { profileSchema, type ProfileFormData } from '@/schemas/validation';

export default function ProfilePage() {
  const {
    register,
    handleSubmit,
    formState: { errors }
  } = useForm<ProfileFormData>({
    resolver: zodResolver(profileSchema)
  });

  useDocumentHead({
    title: 'حقق - تعديل الملف الشخصي',
    description: 'قم بتحديث معلومات ملفك الشخصي',
    ogTitle: 'حقق - تعديل الملف الشخصي',
    ogDescription: 'قم بتحديث معلومات ملفك الشخصي'
  });

  const onSubmit = (data: ProfileFormData) => {
    return data;
  };

  return (
    <div className="container my-8">
      <SectionCard className="pb-12">
        <header className="mb-12">
          <SectionHeader icon={user} title="تعديل الملف الشخصي" />
        </header>

        <form onSubmit={handleSubmit(onSubmit)} className="mx-auto max-w-2xl space-y-2">
          <FormInput label="الاسم" error={errors.name?.message} {...register('name')} />

          <FormInput label="البريد الإلكتروني" type="email" error={errors.email?.message} {...register('email')} />

          <FormInput label="رقم الهاتف" type="tel" error={errors.phone?.message} {...register('phone')} />

          <FormInput
            label="كلمة المرور القديمة"
            type="password"
            error={errors.oldPassword?.message}
            {...register('oldPassword')}
          />

          <FormInput
            label="كلمة المرور الجديدة"
            type="password"
            error={errors.newPassword?.message}
            {...register('newPassword')}
          />

          <FormInput
            label="تأكيد كلمة المرور"
            type="password"
            error={errors.confirmPassword?.message}
            {...register('confirmPassword')}
          />

          <SubmitButton type="submit" className="w-full">
            حفظ التغييرات
          </SubmitButton>
        </form>
      </SectionCard>
    </div>
  );
}
