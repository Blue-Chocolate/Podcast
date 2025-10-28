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
      <itunes:image href="{{ Str::startsWith($podcast->cover_image, 'http') ? $podcast->cover_image : 'https://haqqeq.com/' . ltrim($podcast->cover_image, '/') }}" />
      <image>
        <url>{{ Str::startsWith($podcast->cover_image, 'http') ? $podcast->cover_image : 'https://haqqeq.com/' . ltrim($podcast->cover_image, '/') }}</url>
        <title>{{ $podcast->title }}</title>
        <link>https://haqqeq.com</link>
      </image>
    @endif

    @foreach ($podcast->episodes as $episode)
      <item>
        <title>{{ $episode->title }}</title>
        <description><![CDATA[{{ $episode->description ?? $episode->short_description ?? '' }}]]></description>

        @if($episode->audio_url)
          <enclosure 
            url="{{ Str::startsWith($episode->audio_url, 'http') ? $episode->audio_url : 'https://haqqeq.com/audios/' . basename($episode->audio_url) }}"
            length="{{ file_exists(public_path('audios/'.basename($episode->audio_url))) ? filesize(public_path('audios/'.basename($episode->audio_url))) : 0 }}"
            type="audio/mpeg" />
        @elseif($episode->video_url)
          <enclosure 
            url="{{ Str::startsWith($episode->video_url, 'http') ? $episode->video_url : 'https://haqqeq.com/videos/' . basename($episode->video_url) }}"
            length="{{ file_exists(public_path('videos/'.basename($episode->video_url))) ? filesize(public_path('videos/'.basename($episode->video_url))) : 0 }}"
            type="video/mp4" />
        @endif

        <guid isPermaLink="true">https://haqqeq.com/episodes/{{ $episode->slug }}</guid>
        <link>https://haqqeq.com/episodes/{{ $episode->slug }}</link>
        <pubDate>{{ $episode->created_at->toRfc2822String() }}</pubDate>

        <itunes:title>{{ $episode->title }}</itunes:title>
        <itunes:episodeType>{{ $episode->episode_type ?? 'full' }}</itunes:episodeType>
        @if($episode->episode_number)
          <itunes:episode>{{ $episode->episode_number }}</itunes:episode>
        @endif
        @if($episode->season_id)
          <itunes:season>{{ $episode->season->season_number ?? 1 }}</itunes:season>
        @endif
        <itunes:duration>{{ $episode->duration_seconds ?? 0 }}</itunes:duration>
        <itunes:explicit>{{ $episode->explicit ? 'yes' : 'no' }}</itunes:explicit>

        @if($episode->cover_image)
          <itunes:image href="{{ Str::startsWith($episode->cover_image, 'http') ? $episode->cover_image : 'https://haqqeq.com/' . ltrim($episode->cover_image, '/') }}" />
        @endif
      </item>
    @endforeach
  </channel>
</rss>
