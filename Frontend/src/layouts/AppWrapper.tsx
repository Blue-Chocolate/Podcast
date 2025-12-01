import { ScrollRestoration } from 'react-router';
import UserProvider from '../context/UserProvider';
import type React from 'react';
import { ToastContainer } from 'react-toastify';

export default function AppWrapper({ children }: { children: React.ReactNode }) {
  return (
    <UserProvider>
      <ScrollRestoration />
      <ToastContainer position="bottom-left" theme="dark" />
      {children}
    </UserProvider>
  );
}
