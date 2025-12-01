import { Link } from 'react-router';

type Props = { icon: string; title: string; moreUrl?: string; as?: keyof React.JSX.IntrinsicElements };

export default function SectionHeader({ icon, title, moreUrl, as: HeadingTag = 'h2' }: Props) {
  return (
    <div className="flex items-end justify-between">
      <HeadingTag className="flex items-end gap-1 text-2xl font-extrabold sm:text-4xl md:gap-3">
        {icon && <img src={icon} alt={title} className="w-8 shrink-0 object-contain contrast-200 sm:h-14 sm:w-12" />}
        <span className="mb-1 block md:mb-3">{title}</span>
      </HeadingTag>
      {moreUrl && (
        <Link to={moreUrl} className="text-primary text-2xl font-medium underline">
          المــــزيد
        </Link>
      )}
    </div>
  );
}
