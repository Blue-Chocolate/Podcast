import useCurrentUserQuery from '@/hooks/queries/useCurrentUser';
import { getToken } from '@/lib/token';
import type { User } from '@/schemas/types';
import { createContext, useContext, useState, type Dispatch } from 'react';

type Props = {
  children: React.ReactNode;
};

type ContextProvidedValues = {
  user: User | undefined;
  setUser: Dispatch<React.SetStateAction<User | undefined>>;
  isInit: boolean;
};
const userContext = createContext<ContextProvidedValues | undefined>(undefined);

export default function UserProvider({ children }: Props) {
  const [user, setUser] = useState<User>();

  const verifyQuery = useCurrentUserQuery({ onSuccess: setUser });

  const isInit = verifyQuery.isFetchedAfterMount || (!getToken() && !user) || !!user;
  return <userContext.Provider value={{ user, setUser, isInit }}>{children}</userContext.Provider>;
}

export function useUserState() {
  const context = useContext(userContext);
  if (!context) throw new Error('useUser must be used within a UserProvider');
  return context;
}
