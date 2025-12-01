import LoginButton from '@/components/ui/extend/LoginButtons';
import { useUserState } from '@/context/UserProvider';
import { ShieldAlert } from 'lucide-react';
import { useEffect } from 'react';
import { Outlet, useNavigate } from 'react-router';

export default function UserGuard() {
  const { user } = useUserState();
  const navigate = useNavigate();

  useEffect(() => {
    if (!user) navigate('/login');
  }, []);

  if (!user)
    return (
      <div className="h-[90vh]">
        <div className="mt-36 space-y-4 text-center text-xl font-medium">
          <ShieldAlert size={112} className="stroke-primary mx-auto" />
          <p>يجب تسجيل الدخول اولا, لا يمكنك الوصول إلى هذه الصفحة</p>
          <LoginButton />
        </div>
      </div>
    );

  return <Outlet />;
}
