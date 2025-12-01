import { Link, NavLink } from 'react-router';
import HSplit from '@/components/ui/h-split';
import Logo from '@/components/ui/logo';
import { DISCOVER_MENU, HAQEQ_MENU, SOCIALS_MENU } from '@/constants/menus';
import { MailIcon, PhoneIcon } from '@/components/ui/icons';

export default function Footer() {
  return (
    <footer className="bg-primary text-primary-foreground py-12">
      <div className="container">
        <div className="flex w-full flex-wrap items-start justify-between gap-8 px-8 md:items-center">
          {/* Identity & Socials*/}
          <div className="flex flex-col">
            <Logo variant="light" className="mb-12 h-24 w-36" />
            <div className="flex flex-col items-center">
              <h4 className="mb-4 text-xl font-bold">تابعنـــــــــــــــــــــا على</h4>
              <div className="flex gap-8">
                {SOCIALS_MENU.map((item) => (
                  <Link to={item.to} key={item.name}>
                    {item.icon}
                  </Link>
                ))}
              </div>
            </div>
          </div>

          {/* Important links */}
          <div className="flex basis-1/3 justify-between gap-8">
            <ul className="space-y-6 text-nowrap">
              <li className="text-xl font-bold">حقق</li>
              {HAQEQ_MENU.map((item) => (
                <li key={item.name}>
                  <NavLink className="hover:underline" to={item.to}>
                    {item.name}
                  </NavLink>
                </li>
              ))}
            </ul>
            <ul className="space-y-6 text-nowrap">
              <li className="text-xl font-bold">اكتشف</li>
              {DISCOVER_MENU.map((item) => (
                <li key={item.name}>
                  <NavLink className="hover:underline" to={item.to}>
                    {item.name}
                  </NavLink>
                </li>
              ))}
            </ul>
          </div>

          {/* Contacts */}
          <div className="flex flex-col">
            <ul className="space-y-6">
              <li className="text-xl font-bold underline">تواصــــــــــــــــــل معنا</li>
              <li className="flex gap-2">
                <PhoneIcon />
                <Link to="tel:0567771966" className="hover:underline">
                  0567771966
                </Link>
                <Link to="tel:0560086999" className="hover:underline">
                  0560086999
                </Link>
              </li>
              <li className="flex gap-2">
                <MailIcon />
                <Link to="mailto:admin@birthofdream.com" className="hover:underline">
                  admin@birthofdream.com
                </Link>
              </li>
            </ul>
          </div>
        </div>
        <HSplit className="mt-12" />
        <p className="font-poppins pt-8 text-center text-sm">جميع الحقوق محفوظة لدى شركة ولادة حلم @2024 </p>
      </div>
    </footer>
  );
}
