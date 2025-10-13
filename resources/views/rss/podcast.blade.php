@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<rss version="2.0"
     xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
     xmlns:media="http://search.yahoo.com/mrss/">

  <channel>
    <title>{{ $podcast->title }}</title>
    <link>{{ $podcast->website_url ?? url('/') }}</link>
    <language>{{ $podcast->language ?? 'en' }}</language>
    <itunes:author>{{ $podcast->author ?? 'Unknown' }}</itunes:author>
    <itunes:summary><![CDATA[{{ $podcast->description ?? '' }}]]></itunes:summary>
    @if($podcast->cover_image)
      <itunes:image href="{{ $podcast->cover_image }}" />
    @endif

    @foreach ($podcast->episodes as $episode)
      <item>
        <title>{{ $episode->title }}</title>
        <description><![CDATA[{{ $episode->description ?? $episode->short_description ?? '' }}]]></description>

        @if($episode->video_url)
          <enclosure url="{{ $episode->video_url }}" length="{{ $episode->file_size ?? 0 }}" type="{{ $episode->mime_type ?? 'video/mp4' }}" />
        @elseif($episode->audio_url)
          <enclosure url="{{ $episode->audio_url }}" length="{{ $episode->file_size ?? 0 }}" type="{{ $episode->mime_type ?? 'audio/mpeg' }}" />
        @endif

        <guid>{{ url('/episodes/' . ($episode->slug ?? uniqid('ep-'))) }}</guid>
        <pubDate>{{ optional($episode->published_at)->toRfc2822String() }}</pubDate>
        <itunes:duration>{{ gmdate("H:i:s", $episode->duration_seconds ?? 0) }}</itunes:duration>
        <itunes:explicit>{{ $episode->explicit ? 'yes' : 'no' }}</itunes:explicit>
      </item>
    @endforeach
  </channel>
</rss>
