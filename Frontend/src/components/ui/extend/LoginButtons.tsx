import { Dialog, DialogContent, DialogHeader } from '../dialog';
import { Button } from '../button';
import Logo from '../logo';
import { X } from 'lucide-react';
import { useState } from 'react';
import SubmitButton from '../submit-button';
import { loginSchema, registerSchema, type LoginForm, type RegisterForm } from '@/schemas/validation';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { useMutation } from '@tanstack/react-query';
import { isAxiosError } from 'axios';
import ErrorMessage from './error-message';
import FormInput from './FormInput';
import { useUserState } from '@/context/UserProvider';
import { loginUser, registerUser } from '@/services/auth';
import { setToken } from '@/lib/token';
import { DialogTitle } from '@radix-ui/react-dialog';
import { toast } from 'react-toastify';

type FormProps = {
  onSuccess: () => void;
};

export default function LoginButton() {
  const [isOpen, setIsOpen] = useState(false);
  const [isRegister, setIsRegister] = useState(false);

  return (
    <div className="space-x-2">
      <Button
        size="sm"
        className="h-7 rounded-md px-2"
        onClick={() => {
          setIsOpen(true);
          setIsRegister(true);
        }}
      >
        تسجيل حساب
      </Button>
      <Button
        variant="secondary"
        size="sm"
        className="h-7"
        onClick={() => {
          setIsOpen(true);
          setIsRegister(false);
        }}
      >
        الدخول
      </Button>

      <Dialog open={isOpen}>
        <DialogContent
          showCloseButton={false}
          className="flex h-screen min-w-full justify-center overflow-auto border-none bg-transparent px-0 py-6"
          dir="ltr"
        >
          <div className="fixed inset-0 -z-1 bg-transparent" onClick={() => setIsOpen(false)}></div>
          <DialogTitle
            dir="rtl"
            aria-describedby={'login'}
            className="bg-background h-fit w-full max-w-[600px] rounded-2xl p-6"
            onClick={(e) => e.stopPropagation()}
          >
            <div className="flex w-full justify-end">
              <button onClick={() => setIsOpen(false)}>
                <X size={25} className="cursor-pointer" />
              </button>
            </div>

            <DialogHeader className="mb-4 items-center">
              <Logo className="w-32" />
              <p className="text-2xl">{isRegister ? 'رجاءا ادخل بيانات التسجيل' : 'رجاءا ادخل بريدك الالكتروني'}</p>
              <p className="text-muted text-base">انشئ حسابا او سجل دخولك بحساب موجود في حقق</p>
            </DialogHeader>

            {isRegister ? (
              <RegisterForm onSuccess={() => setIsOpen(false)} />
            ) : (
              <LoginForm onSuccess={() => setIsOpen(false)} />
            )}
          </DialogTitle>
        </DialogContent>
      </Dialog>
    </div>
  );
}

function RegisterForm({ onSuccess }: FormProps) {
  const { setUser } = useUserState();
  const {
    register,
    handleSubmit,
    formState: { errors }
  } = useForm<RegisterForm>({
    resolver: zodResolver(registerSchema)
  });

  const [formError, setFormError] = useState<string>();

  const { mutate, isPending } = useMutation({
    mutationKey: ['register'],
    mutationFn: (form: RegisterForm) => registerUser(form),
    onSuccess: (data) => {
      setUser({ email: data.user.email, name: data.user.name, id: data.user.id, phone: data.user.phone_number });
      setToken(data.token);
      onSuccess();
      toast.success('تم تسجيل حسابكم و تسجيل الدخول');
    },
    onError: (error) => {
      if (isAxiosError(error)) {
        if (error.response?.status === 405) {
          return setFormError('البريد مستخدم من قبل');
        }
        setFormError(error.response?.data?.message ?? 'خطاء غير معروف حاول لاحقا');
      }
    }
  });

  const submitForm = handleSubmit((form: RegisterForm) => mutate(form));

  return (
    <form className="space-y-4" onSubmit={submitForm}>
      <div className="grid gap-4">
        <div className="grid gap-1">
          <FormInput {...register('name')} error={errors.name?.message} label="الاسم الاول" />
        </div>
        <div className="grid gap-1">
          <FormInput {...register('phone')} error={errors.phone?.message} label="رقم الهاتف" />
        </div>
        <div className="grid gap-1">
          <FormInput {...register('email')} error={errors.email?.message} label="البريد" />
        </div>
        <div className="grid gap-1">
          <FormInput type="password" {...register('password')} error={errors.password?.message} label="كلمة المرور" />
        </div>
        <div className="grid gap-1">
          <FormInput
            type="password"
            {...register('password_confirmation')}
            error={errors.password?.message}
            label="تأكيد كلمة المرور"
          />
        </div>
        {/* <div>
          <Label className="text-muted">صلاحية المسجِل</Label>
          <SelectUserAuthority onChange={(value) => rest.setValue('auth', value)} />
          <ErrorMessage error={errors.auth?.message} />
        </div> */}
      </div>
      <ErrorMessage error={formError} />
      <SubmitButton className="w-full" type="submit" isLoading={isPending}>
        متابعة
      </SubmitButton>
    </form>
  );
}

function LoginForm({ onSuccess }: FormProps) {
  const { setUser } = useUserState();
  const {
    register,
    handleSubmit,
    formState: { errors }
  } = useForm<LoginForm>({
    resolver: zodResolver(loginSchema)
  });

  const [formError, setFormError] = useState<string | null>();

  const { isPending, mutate } = useMutation({
    mutationKey: ['login'],
    mutationFn: (form: LoginForm) => loginUser(form),
    onSuccess: async (data) => {
      setToken(data.token);
      setUser({ email: data.email, name: data.name, id: '', phone: '' });
      onSuccess();
      toast.success('تم تسجيل الدخول بنجاح');
    },
    onError: (err) => {
      if (isAxiosError(err)) {
        if (err.response?.status === 401) setFormError('كلمة المرور او البريد غير صحيح');
        else setFormError(JSON.stringify(err.response?.data ?? 'حطاء غي متوقع حاول لاحقا'));
      }
    },
    onMutate: () => {
      setFormError(null);
    }
  });

  const submitForm = handleSubmit((form: LoginForm) => mutate(form));

  return (
    <form onSubmit={submitForm} className="space-y-2">
      <div className="grid gap-1">
        <FormInput {...register('email')} placeholder="البريد" error={errors.email?.message} />
      </div>
      <div className="grid gap-1">
        <FormInput type="password" {...register('password')} placeholder="كلمة السر" error={errors.password?.message} />
      </div>
      <ErrorMessage error={formError || errors.root?.message} />
      <SubmitButton className="w-full" type="submit" isLoading={isPending}>
        متابعة
      </SubmitButton>
    </form>
  );
}
