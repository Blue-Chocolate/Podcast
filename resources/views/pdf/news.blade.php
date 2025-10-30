<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $news->title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; line-height: 1.6; margin: 30px; }
        h1 { color: #222; font-size: 22px; }
        .meta { color: #666; margin-bottom: 20px; }
        .content { white-space: pre-line; }
        img { max-width: 100%; margin-top: 15px; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>{{ $news->title }}</h1>
    <p class="meta">الكاتب: {{ $news->author }} | التاريخ: {{ $news->created_at->format('d M Y - H:i') }}</p>

    @if($news->image)
        <img src="{{ public_path('storage/' . $news->image) }}" alt="صورة الخبر">
    @endif

    <div class="content">{{ $news->content }}</div>
</body>
</html>
