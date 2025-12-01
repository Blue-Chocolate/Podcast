<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $episode->title }} - Podcast</title>
  <meta name="description" content="{{ $episode->short_description ?? $episode->description }}">
</head>
<body>
  <h1>{{ $episode->title }}</h1>
  <p>{{ $episode->description }}</p>

  @if($episode->cover_image)
    <img src="{{ Str::startsWith($episode->cover_image, 'http') ? $episode->cover_image : url($episode->cover_image) }}" alt="{{ $episode->title }}" width="300">
  @endif

  @if($episode->audio_url)
    <h3>Listen:</h3>
    <audio controls preload="metadata" style="width: 100%; max-width: 400px;">
      <source src="{{ Str::startsWith($episode->audio_url, 'http') ? $episode->audio_url : url('audios/' . basename($episode->audio_url)) }}" type="audio/mpeg">
      Your browser does not support the audio element.
    </audio>
  @endif
</body>
</html>
