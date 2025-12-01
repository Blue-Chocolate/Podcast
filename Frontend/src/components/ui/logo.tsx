import { NavLink } from 'react-router';
import { cn } from '@/lib/utils';
import { whiteLogo, logo } from '@/assets/images';

type Props = {
  className?: string;
  variant?: keyof typeof variants;
};

const variants = {
  dark: logo,
  light: whiteLogo
};

export default function Logo({ className = '', variant = 'dark' }: Props) {
  return (
    <NavLink aria-label="إلي الرئيسية" className={cn('inline-block w-20 items-center gap-2', className)} to="/">
      <img src={variants[variant]} alt="شعار حقق" className="w-full" />
    </NavLink>
  );
}
