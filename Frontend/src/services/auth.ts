import api from './api';
import type { LoginForm, RegisterForm } from '@/schemas/validation';

type LoginResponse = {
  token: string;
  role: string;
  name: string;
  email: string;
  avatar: string | null;
};

type RegisterResponse = {
  user: {
    name: string;
    email: string;
    phone_number: string;
    age: number;
    updated_at: string;
    created_at: string;
    id: string | number;
  };
  token: string;
};

type CurrentUserResponse = {
  id: number | string;
  name: string;
  email: string;
  age: number;
  phone_number: string | null;
  avatar_url: string | null;
  created_at: string;
  updated_at: string;
};

export async function getCurrentUser() {
  return api.get<CurrentUserResponse>('/api/user').then((res) => res.data);
}

export async function loginUser(payload: LoginForm) {
  return api.post<LoginResponse>('/api/login', { ...payload }).then((res) => res.data);
}

export async function logoutUser() {
  return api.post<{ message: string }>('/api/logout').then((res) => res.data);
}

export function registerUser(payload: RegisterForm) {
  return api.post<RegisterResponse>('/api/register', { ...payload }).then((res) => res.data);
}
