import { X } from 'lucide-react';
import NavItems from '../../ui/extend/NavItems';
import HSplit from '../../ui/h-split';
import { cn } from '@/lib/utils';
import { useRef } from 'react';
import useOutsideClick from '@/hooks/useOutsideClick';
import { useUserState } from '@/context/UserProvider';
import JoinButton from '@/components/ui/extend/JoinButton';
import LogoutButton from '@/components/ui/extend/LogoutButton';
import LoginButtons from '@/components/ui/extend/LoginButtons';

type Props = { isOpen: boolean; onClose: () => void };

export default function SideNavBar({ isOpen, onClose }: Props) {
  const { user } = useUserState();
  const asideRef = useRef<HTMLElement>(null);
  useOutsideClick({ containerRef: asideRef, onOutsideClick: onClose });

  return (
    <>
      {/* Overlay*/}
      <div
        className={`fixed inset-0 z-20 bg-black transition-all md:hidden ${isOpen ? 'opacity-50' : 'pointer-events-none opacity-0'}`}
      />

      <aside
        ref={asideRef}
        className={cn(
          'bg-background fixed top-0 bottom-0 z-20 block w-72 space-y-4 border-e p-6 drop-shadow-2xl transition-all md:hidden',
          isOpen ? 'start-0' : '-start-72'
        )}
      >
        <X onClick={onClose} className="mb-8 cursor-pointer" />

        <ul className="space-y-4">
          <NavItems onSelect={onClose} />
        </ul>

        <HSplit />

        <div className="flex flex-col gap-2">
          {user ? (
            <>
              <JoinButton />
              <LogoutButton />
            </>
          ) : (
            <LoginButtons />
          )}
        </div>
      </aside>
    </>
  );
}
