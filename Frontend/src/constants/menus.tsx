import { FacebookIcon, GoogleIcon, XIcon } from '@/components/ui/icons';

export const DISCOVER_MENU = [
  { name: 'إصدارات حقق', to: '/إصدارات' },
  { name: 'تواصل معنا', to: '/تواصل-معنا' }
];

export const HAQEQ_MENU = [
  { name: 'الرئيسية', to: '/' },
  { name: 'البودكاست', to: '/البودكاست' },
  { name: 'المدونة', to: '/المدونة' }
];

export const SOCIALS_MENU = [
  { name: 'Facebook', to: '/', icon: <FacebookIcon size={29} /> },
  { name: 'Google', to: '/', icon: <GoogleIcon size={29} /> },
  { name: 'X', to: '/', icon: <XIcon size={29} /> }
];
