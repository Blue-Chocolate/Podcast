import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious
} from '../pagination';

type Props = {
  currentPage: number;
  totalPages: number;
  onPageChange: (page: number) => void;
};

export default function DataPagination({ currentPage, totalPages, onPageChange }: Props) {
  const handleClick = (page: number) => {
    if (page <= 0 || page > totalPages || page === currentPage) return;
    onPageChange(page);
  };

  if (totalPages <= 1) return null;

  const getPages = () => {
    const pages = [];
    if (totalPages <= 5) {
      for (let i = 1; i <= totalPages; i++) pages.push(i);
    } else {
      if (currentPage > 2) pages.push(1, '...');
      for (let i = Math.max(1, currentPage - 1); i <= Math.min(totalPages, currentPage + 1); i++) {
        pages.push(i);
      }
      if (currentPage < totalPages - 1) pages.push('...', totalPages);
    }
    return pages;
  };

  return (
    <Pagination dir="ltr">
      <PaginationContent>
        <PaginationItem>
          <PaginationPrevious
            className={currentPage <= 1 ? 'cursor-not-allowed opacity-40 hover:bg-transparent' : 'cursor-pointer'}
            aria-disabled={currentPage <= 1}
            onClick={() => handleClick(currentPage - 1)}
          />
        </PaginationItem>
        {getPages().map((page, idx) =>
          page === '...' ? (
            <PaginationItem key={idx} className="cursor-pointer">
              <PaginationEllipsis />
            </PaginationItem>
          ) : (
            <PaginationItem key={page} className="cursor-pointer">
              <PaginationLink isActive={page === currentPage} onClick={() => handleClick(Number(page))}>
                {page}
              </PaginationLink>
            </PaginationItem>
          )
        )}
        <PaginationItem>
          <PaginationNext
            className={
              currentPage >= totalPages ? 'cursor-not-allowed opacity-40 hover:bg-transparent' : 'cursor-pointer'
            }
            aria-disabled={currentPage >= totalPages}
            onClick={() => handleClick(currentPage + 1)}
          />
        </PaginationItem>
      </PaginationContent>
    </Pagination>
  );
}
