import * as motion from 'motion/react-client';
import DefaultMotionDiv from './DefaultMotionElement';
import SubmitButton from '@/components/ui/submit-button';

type Props = {
  isError: boolean;
  isEmpty?: boolean | null;
  isPending: boolean;
  isRefetching?: boolean;
  children: React.ReactNode;
  LoadingFallback?: React.ComponentType;
  retry: () => void;
};

function ErrorFetchingResource({ retry, isRetrying }: { retry: () => void; isRetrying: boolean }) {
  return (
    <DefaultMotionDiv className="text-muted flex h-96 flex-col items-center justify-center gap-4 text-center text-4xl font-bold">
      خطاء في الحصول علي المحتوي !
      <SubmitButton isLoading={isRetrying} className="block font-medium" onClick={retry}>
        إعادة المحاولة
      </SubmitButton>
    </DefaultMotionDiv>
  );
}

function NoResourceAvilable({ retry, isRetrying }: { retry: () => void; isRetrying: boolean }) {
  return (
    <DefaultMotionDiv className="text-muted flex h-96 flex-col items-center justify-center gap-4 text-center text-4xl font-bold">
      لا يوجد محتوي للعرض
      <SubmitButton isLoading={isRetrying} className="block font-medium" onClick={retry}>
        إعادة المحاولة
      </SubmitButton>
    </DefaultMotionDiv>
  );
}

function DefaultLoading() {
  return (
    <div className="container my-12 items-center justify-center">
      <motion.div
        className="h-96 rounded bg-gray-300"
        animate={{ opacity: [0.5, 1, 0.5] }}
        transition={{ duration: 0.8, repeat: Infinity }}
      />
    </div>
  );
}

export default function DataWrapper({
  children,
  isError,
  isPending,
  LoadingFallback = DefaultLoading,
  retry,
  isEmpty,
  isRefetching
}: Props) {
  if (isPending) return <LoadingFallback />;
  if (isError) return <ErrorFetchingResource retry={retry} isRetrying={!!isRefetching} />;
  if (isEmpty) return <NoResourceAvilable retry={retry} isRetrying={!!isRefetching} />;
  return children;
}
