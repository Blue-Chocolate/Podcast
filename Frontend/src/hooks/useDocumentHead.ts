import { useEffect } from 'react';

export function useDocumentHead(config: {
  title: string;
  description?: string;
  ogTitle?: string;
  ogDescription?: string;
  ogImage?: string;
}) {
  useEffect(() => {
    // Set title
    document.title = config.title;

    // Set meta tags
    const metaTags: HTMLMetaElement[] = [];

    if (config.description) {
      const desc = document.querySelector('meta[name="description"]') || document.createElement('meta');
      desc.setAttribute('name', 'description');
      desc.setAttribute('content', config.description);
      if (!desc.parentElement) document.head.appendChild(desc);
      metaTags.push(desc as HTMLMetaElement);
    }

    if (config.ogTitle) {
      const ogTitle = document.querySelector('meta[property="og:title"]') || document.createElement('meta');
      ogTitle.setAttribute('property', 'og:title');
      ogTitle.setAttribute('content', config.ogTitle);
      if (!ogTitle.parentElement) document.head.appendChild(ogTitle);
      metaTags.push(ogTitle as HTMLMetaElement);
    }

    if (config.ogDescription) {
      const ogDesc = document.querySelector('meta[property="og:description"]') || document.createElement('meta');
      ogDesc.setAttribute('property', 'og:description');
      ogDesc.setAttribute('content', config.ogDescription);
      if (!ogDesc.parentElement) document.head.appendChild(ogDesc);
      metaTags.push(ogDesc as HTMLMetaElement);
    }

    if (config.ogImage) {
      const ogImg = document.querySelector('meta[property="og:image"]') || document.createElement('meta');
      ogImg.setAttribute('property', 'og:image');
      ogImg.setAttribute('content', config.ogImage);
      if (!ogImg.parentElement) document.head.appendChild(ogImg);
      metaTags.push(ogImg as HTMLMetaElement);
    }
  }, [config]);
}
