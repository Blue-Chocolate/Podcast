import { useEffect, useRef } from 'react';

export default function useDebounce<T extends (...args: any[]) => void>(callback: T, waitTime: number = 500) {
  const timeoutRef = useRef<ReturnType<typeof setTimeout> | null>(null);

  useEffect(() => {
    return () => {
      if (timeoutRef.current) {
        clearTimeout(timeoutRef.current);
      }
    };
  }, []);

  const debounced = (...args: Parameters<T>) => {
    if (timeoutRef.current) clearTimeout(timeoutRef.current);
    timeoutRef.current = setTimeout(() => callback(...args), waitTime);
  };

  return debounced;
}
