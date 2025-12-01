import type { ContactForm, SubscribeForm } from '@/schemas/validation';
import api from './api';

export async function subscribeNewsLetter(payload: SubscribeForm) {
  return api.post('/api/subscribe', { ...payload });
}

export async function subscribeWhatsApp(payload: { phone: string }) {
  return api.post('/api/subscribe', { ...payload });
}

export async function contact(payload: ContactForm) {
  return api.post('/api/contact', { ...payload });
}
