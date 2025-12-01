import { motion, type HTMLMotionProps } from 'motion/react';
import { useEffect, useRef, useState, type Ref } from 'react';

interface Props extends HTMLMotionProps<'div'> {
  children: React.ReactNode;
  delay?: number;
  className?: string;
  ref?: Ref<HTMLDivElement>;
  as?: keyof Pick<typeof motion, 'div' | 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'p' | 'span' | 'section' | 'header'>;
}

export default function DefaultMotionElement({
  children,
  delay = 0.2,
  className = '',
  ref,
  as = 'div',
  ...props
}: Props) {
  const lastY = useRef<number>(window?.scrollY || 0);
  const [scrollingUp, setScrollingUp] = useState(false);

  useEffect(() => {
    if (typeof window === 'undefined') return;

    const onScroll = () => {
      const y = window.scrollY || window.pageYOffset;
      setScrollingUp(y < lastY.current);
      lastY.current = y;
    };

    const initY = window.scrollY || window.pageYOffset;
    lastY.current = initY;

    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  const Tag = motion[as];

  return (
    <Tag
      ref={ref}
      className={className}
      variants={{
        hiddenUp: { opacity: 0, y: -40 },
        hiddenDown: { opacity: 0, y: 40 },
        visible: { opacity: 1, y: 0 }
      }}
      initial={scrollingUp ? 'hiddenDown' : 'hiddenUp'}
      whileInView="visible"
      transition={{ duration: 0.4, delay }}
      {...props}
    >
      {children}
    </Tag>
  );
}
