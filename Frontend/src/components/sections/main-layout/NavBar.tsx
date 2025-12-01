import { Menu } from 'lucide-react';
import SearchBar from '@/components/ui/extend/SearchBar';
import Logo from '@/components/ui/logo';
import NavItems from '@/components/ui/extend/NavItems';
import HSplit from '@/components/ui/h-split';
import LoginButtons from '@/components/ui/extend/LoginButtons';
import { useUserState } from '@/context/UserProvider';
import LogoutButton from '@/components/ui/extend/LogoutButton';
import JoinButton from '@/components/ui/extend/JoinButton';

type Props = {
  onOpenSideBar: () => void;
};

export default function NavBar({ onOpenSideBar }: Props) {
  const { user } = useUserState();

  return (
    <nav className="bg-background sticky top-0 z-10 w-full space-y-0 border-b-2 py-4 md:space-y-4 xl:space-y-0">
      <div className="container flex items-end justify-between gap-4">
        <div className="flex items-end">
          <Logo className="sm:me-8" />
          <ul className="hidden gap-0 xl:flex xl:gap-[18px]">
            <NavItems />
          </ul>
        </div>

        <div className="flex gap-2">
          <SearchBar />
          <Menu onClick={onOpenSideBar} className="cursor-pointer md:hidden" size={26} />
          <div className="hidden gap-2 md:flex">
            {user ? (
              <>
                <JoinButton />
                <LogoutButton />
              </>
            ) : (
              <LoginButtons />
            )}
          </div>
        </div>
      </div>

      <HSplit className="hidden md:flex xl:hidden" />

      <ul className="container hidden gap-[18px] md:flex xl:hidden">
        <NavItems />
      </ul>
    </nav>
  );
}
