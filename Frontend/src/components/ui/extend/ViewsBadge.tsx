import { Badge } from '../badge';
import { eye } from '@/assets/images';

type Props = {
  views: number | string;
};

export default function ViewsBadge({ views }: Props) {
  return (
    <Badge variant="secondary" className="my-2 flex h-5 rounded-sm px-1 text-[#D3CFCF]">
      <img src={eye} alt="عين المشاهدين" className="ms-0.5 w-5" />
      <span className="h-[18px]">{views}</span>
    </Badge>
  );
}
