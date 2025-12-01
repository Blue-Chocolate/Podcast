import api from './api';

export const downloadRelease = async (id: string | number, fileName?: string, openInNewTab = false) => {
  const res = await api.get(`/api/releases/${id}/download/pdf`, {
    responseType: 'blob',
    headers: {
      Accept: 'application/pdf',
      credentials: 'include'
    }
  });

  const blob = new Blob([res.data], { type: 'application/pdf' });
  const blobUrl = URL.createObjectURL(blob);

  if (openInNewTab) {
    window.open(blobUrl, '_blank', 'noopener,noreferrer');
  } else {
    const a = document.createElement('a');
    a.href = blobUrl;
    a.download = fileName || 'download.pdf';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  setTimeout(() => URL.revokeObjectURL(blobUrl), 2000);
  return res;
};
