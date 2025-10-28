@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<rss version="2.0"
  xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:media="http://search.yahoo.com/mrss/">

  <channel>
    <title>{{ $podcast->title }}</title>
    <link>{{ $podcast->website_url ?? 'https://haqqeq.com' }}</link>
    <atom:link href="https://haqqeq.com/podcast/{{ $podcast->slug }}/rss" rel="self" type="application/rss+xml" />
    <language>{{ $podcast->language ?? 'en' }}</language>
    <copyright>{{ $podcast->copyright ?? 'All rights reserved' }}</copyright>
    <description><![CDATA[{{ $podcast->description ?? '' }}]]></description>

    <itunes:author>{{ $podcast->author ?? 'Unknown' }}</itunes:author>
    <itunes:summary><![CDATA[{{ $podcast->description ?? '' }}]]></itunes:summary>
    <itunes:type>{{ $podcast->type ?? 'episodic' }}</itunes:type>

    <itunes:owner>
      <itunes:name>{{ $podcast->owner_name ?? $podcast->author ?? 'Unknown' }}</itunes:name>
      <itunes:email>{{ $podcast->owner_email ?? config('mail.from.address') }}</itunes:email>
    </itunes:owner>

    <itunes:explicit>{{ $podcast->explicit ? 'yes' : 'no' }}</itunes:explicit>
    <itunes:category text="{{ $podcast->category ?? 'Technology' }}" />

    @if($podcast->cover_image)
      @php
        $podcastCover = Str::startsWith($podcast->cover_image, 'http')
            ? $podcast->cover_image
            : 'https://haqqeq.com/' . ltrim($podcast->cover_image, '/');
      @endphp
      <itunes:image href="{{ $podcastCover }}" />
      <image>
        <url>{{ $podcastCover }}</url>
        <title>{{ $podcast->title }}</title>
        <link>https://haqqeq.com</link>
      </image>
    @endif

    @foreach ($podcast->episodes as $episode)
      @php
        // Determine audio or video file info
        $enclosureUrl = null;
        $enclosureType = null;
        $enclosureLength = 0;

        if ($episode->audio_url) {
            $filePath = public_path('audios/' . basename($episode->audio_url));
            $enclosureUrl = Str::startsWith($episode->audio_url, 'http')
                ? $episode->audio_url
                : 'https://haqqeq.com/audios/' . basename($episode->audio_url);
            $enclosureType = 'audio/mpeg';
            $enclosureLength = file_exists($filePath) ? filesize($filePath) : 0;
        } elseif ($episode->video_url) {
            $filePath = public_path('videos/' . basename($episode->video_url));
            $enclosureUrl = Str::startsWith($episode->video_url, 'http')
                ? $episode->video_url
                : 'https://haqqeq.com/videos/' . basename($episode->video_url);
            $enclosureType = 'video/mp4';
            $enclosureLength = file_exists($filePath) ? filesize($filePath) : 0;
        }

        $episodeCover = $episode->cover_image
            ? (Str::startsWith($episode->cover_image, 'http')
                ? $episode->cover_image
                : 'https://haqqeq.com/' . ltrim($episode->cover_image, '/'))
            : null;
      @endphp

      <item>
        <title>{{ $episode->title }}</title>
        <description><![CDATA[{{ $episode->description ?? $episode->short_description ?? '' }}]]></description>

        @if($enclosureUrl)
          <enclosure 
            url="{{ $enclosureUrl }}"
            length="{
