import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export const BASE_URL = import.meta.env.VITE_API_BASE_URL;

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function parsedDate(date?: string | null) {
  if (!date) return null;
  return new Date(date).toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

export function formatDuration(sec: number | null) {
  if (sec == null || !isFinite(sec)) return '--:--';
  const s = Math.floor(sec);
  const m = Math.floor(s / 60)
    .toString()
    .padStart(2, '0');
  const ss = (s % 60).toString().padStart(2, '0');
  return `${m}:${ss}`;
}

export function remote(path: string | null | undefined) {
  if (path && path.includes('://')) return path;
  if (path?.includes('storage/')) return `${BASE_URL}/${path}`;
  return `${BASE_URL}/storage/${path}`;
}

export function isHere(to: string, location: string) {
  if (to === '/' && location === '/') return true;
  return to !== '/' && decodeURIComponent(location).includes(to);
}

export function hideBodyScroll() {
  if (document === undefined) return;
  const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
  document.body.style.paddingRight = `${scrollbarWidth + 0.1}px`;
  document.body.style.overflowY = 'hidden';
}

export function showBodyScroll() {
  if (document === undefined) return;
  document.body.style.paddingRight = `0px`;
  document.body.style.overflowY = 'auto';
}

export function getYouTubeEmbedUrl(url: string): string {
  if (!url) return '';
  let videoId = '';

  // Extract from youtube.com/watch?v=VIDEO_ID
  const watchMatch = url.match(/[?&]v=([^&]+)/);
  if (watchMatch) videoId = watchMatch[1];

  if (!videoId) {
    const sectors = url.split('/');
    videoId = sectors[sectors.length - 1];
  }

  return `https://www.youtube.com/embed/${videoId}`;
}
