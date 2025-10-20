<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Releases</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded-xl shadow-md">
        <h1 class="text-3xl font-bold mb-4 text-center">Available Releases</h1>
        <div id="release-list" class="space-y-4 text-center">Loading...</div>
    </div>

    <script>
        async function loadReleases() {
            try {
                const res = await fetch('/releases/list', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                
                const data = await res.json();

                const container = document.getElementById('release-list');
                container.innerHTML = '';

                if (data.length === 0) {
                    container.innerHTML = '<p class="text-gray-500">No releases available</p>';
                    return;
                }

                data.forEach(r => {
                    const div = document.createElement('div');
                    div.className = "p-4 border rounded-lg hover:bg-gray-50 transition";
                    div.innerHTML = `
                        <p class="font-semibold text-lg">${r.title}</p>
                        ${r.description ? `<p class="text-gray-600 text-sm mt-1">${r.description}</p>` : ''}
                        <button 
                            onclick="downloadRelease(${r.id})" 
                            class="mt-2 inline-block text-blue-600 hover:underline cursor-pointer"
                        >
                            Download PDF
                        </button>
                    `;
                    container.appendChild(div);
                });
            } catch (error) {
                console.error('Error loading releases:', error);
                document.getElementById('release-list').innerHTML = 
                    '<p class="text-red-500">Failed to load releases</p>';
            }
        }

        async function downloadRelease(id) {
            try {
                const res = await fetch(`/api/releases/${id}/download`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin'
                });

                if (res.status === 401) {
                    // User not authenticated, redirect to login
                    window.location.href = '/login';
                    return;
                }

                if (!res.ok) {
                    const error = await res.json();
                    alert(error.error || 'Download failed');
                    return;
                }

                // Get the blob and download it
                const blob = await res.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = res.headers.get('Content-Disposition')?.split('filename=')[1]?.replace(/"/g, '') || `release-${id}.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            } catch (error) {
                console.error('Download error:', error);
                alert('Failed to download file');
            }
        }

        loadReleases();
    </script>
</body>
</html>