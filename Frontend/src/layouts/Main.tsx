import Footer from '@/components/sections/main-layout/Footer';
import NavBar from '@/components/sections/main-layout/NavBar';
import SideNavBar from '@/components/sections/main-layout/SideNavBar';
import { useState } from 'react';
import { Outlet } from 'react-router';
import AppWrapper from './AppWrapper';

export default function MainLayout() {
  const [isSideBarOpen, setIsSideBarOpen] = useState(false);

  return (
    <AppWrapper>
      <NavBar onOpenSideBar={() => setIsSideBarOpen(true)} />
      <SideNavBar isOpen={isSideBarOpen} onClose={() => setIsSideBarOpen(false)} />
      <main>
        <Outlet />
      </main>
      <Footer />
    </AppWrapper>
  );
}
