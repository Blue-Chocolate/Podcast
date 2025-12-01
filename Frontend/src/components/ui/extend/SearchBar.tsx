import { cn, hideBodyScroll, showBodyScroll } from '@/lib/utils';
import SearchIcon from '../icons';
import { memo, useState, type ChangeEvent } from 'react';
import Img from './Img';
import { Link } from 'react-router';
import { AnimatePresence, motion } from 'motion/react';
import { useQuery } from '@tanstack/react-query';
import { search } from '@/services/search';
import useDebounce from '@/hooks/useDebounce';
import { Loader2 } from 'lucide-react';

const variants = {
  hidden: { opacity: 0, y: -8, scale: 0.98 },
  visible: { opacity: 1, y: 0, scale: 1 }
};

export default function SearchBar() {
  const [showData, setShowData] = useState(false);
  const [query, setQuery] = useState('');

  const { data, isFetching } = useQuery({
    queryKey: ['search', query],
    queryFn: () => search(query),
    enabled: query.length > 0
  });
  const results = data?.results || null;
  const handleOnChange = useDebounce((e: ChangeEvent<HTMLInputElement>) => setQuery(e.target.value), 500);

  const isEmpty =
    query.length > 0 &&
    !results?.blogs &&
    !results?.blog_categories &&
    !results?.releases &&
    !results?.videos &&
    !results?.episodes &&
    !results?.podcasts;

  return (
    <div className="relative">
      <SearchIcon className="absolute end-0 top-1" size={21} />
      <input
        type="text"
        className={cn(
          'border-b-2 bg-transparent p-1 ps-7 text-sm opacity-55 transition-all',
          'focus:border-b-primary box-border focus:opacity-100 focus:outline-0'
        )}
        placeholder="بحث"
        dir="ltr"
        onFocus={() => {
          setShowData(true);
          hideBodyScroll();
        }}
        onBlur={() => {
          setShowData(false);
          showBodyScroll();
        }}
        onChange={handleOnChange}
        autoComplete="off"
      />
      {isFetching && (
        <div className="text-muted absolute start-0 top-1">
          <Loader2 className="spinner" size={16} />
        </div>
      )}

      <AnimatePresence>
        {showData && (
          <motion.div
            key="dropdown"
            className="absolute end-0 top-10 max-h-80 w-2xs overflow-auto rounded-sm border bg-white shadow-lg"
            variants={variants}
            initial="hidden"
            animate="visible"
            exit="hidden"
            transition={{ duration: 0.28, ease: [0.16, 0.84, 0.33, 1] }}
            style={{ transformOrigin: 'top right' }}
          >
            <ul>
              {isEmpty ? (
                isFetching ? (
                  <li className="text-muted px-4 py-4 text-center text-sm">جارٍ البحث...</li>
                ) : (
                  <li className="text-muted px-4 py-4 text-center text-sm">لا توجد نتائج مطابقة</li>
                )
              ) : null}

              {query.length === 0 && <li className="text-muted px-4 py-4 text-center text-sm">أبداء الكتابة للبحث</li>}

              {results?.releases?.data.map((release) => (
                <SearchItem
                  key={release.id}
                  imageUrl={release.images[0]}
                  name={release.title}
                  type="إصدار"
                  to={`/إصدارات/${release.id}`}
                />
              ))}

              {results?.episodes?.data.map((ep) => (
                <SearchItem
                  key={ep.id}
                  imageUrl={ep.cover_image}
                  name={ep.title}
                  type="حلقة"
                  to={`/البودكاست/الحلقات/${ep.id}`}
                />
              ))}

              {results?.podcasts?.data.map((podcast) => (
                <SearchItem
                  key={podcast.id}
                  imageUrl={podcast.cover_image}
                  name={podcast.title}
                  type="بودكاست"
                  to={`/البودكاست/${podcast.id}`}
                />
              ))}

              {results?.videos?.data.map((video) => (
                <SearchItem
                  key={video.id}
                  imageUrl={video.image_path}
                  name={video.title}
                  type="فيديو وثائقي"
                  to={`/الفيديوهات/${video.id}`}
                />
              ))}

              {results?.blogs?.data.map((blog) => (
                <SearchItem
                  key={blog.id}
                  imageUrl={blog.image}
                  name={blog.title}
                  type="مدونة"
                  to={`/المدونة/${blog.id}`}
                />
              ))}

              {results?.blog_categories?.data.map((blogCategory) => (
                <SearchItem
                  key={blogCategory.id}
                  imageUrl={blogCategory.image_path}
                  name={blogCategory.name}
                  type="نشرة"
                  to={`/المدونة/تصنيف/${blogCategory.id}`}
                />
              ))}

              {results?.video_categories?.data.map((videoCategory) => (
                <SearchItem
                  key={videoCategory.id}
                  imageUrl={videoCategory.image_path}
                  name={videoCategory.name}
                  type="برامج فيديوهات"
                  to={`/الفيديوهات/تصنيف/${videoCategory.id}`}
                />
              ))}
            </ul>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

const SearchItem = memo(function ({
  imageUrl,
  name,
  type,
  to
}: {
  imageUrl: string;
  name: string;
  type: string;
  to: string;
}) {
  return (
    <li className="hover:bg-accent border-b-2">
      <Link to={to} className="flex cursor-pointer gap-2 px-4 py-2">
        <Img src={imageUrl} alt={name} className="h-9 w-9 shrink-0 rounded-sm object-cover" />
        <div>
          <p className="text-sm font-bold">{name}</p>
          <p className="text-muted text-xs">{type}</p>
        </div>
      </Link>
    </li>
  );
});

SearchItem.displayName = 'SearchItem';
