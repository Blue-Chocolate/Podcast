@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<rss version="2.0"
     xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:media="http://search.yahoo.com/mrss/">
  <channel>
    <title>{{ $podcast->title }}</title>
    <link>{{ $podcast->website_url ?? url('/') }}</link>
    <atom:link href="{{ url('/rss/podcast/' . $podcast->slug) }}" rel="self" type="application/rss+xml" />
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
      <itunes:image href="{{ $podcast->cover_image }}" />
      <image>
        <url>{{ $podcast->cover_image }}</url>
        <title>{{ $podcast->title }}</title>
        <link>{{ $podcast->website_url ?? url('/') }}</link>
      </image>
    @endif

    @foreach ($podcast->episodes as $episode)
      <item>
        <title>{{ $episode->title }}</title>
        <description><![CDATA[{{ $episode->description ?? $episode->short_description ?? '' }}]]></description>

        {{-- ✅ Updated Enclosure Section --}}
        @if($episode->video_url)
          <enclosure 
            url="{{ Storage::disk('videos')->url($episode->video_url) }}" 
            length="{{ Storage::disk('videos')->size($episode->video_url) }}" 
            type="video/mp4" />
        @elseif($episode->audio_url)
          <enclosure 
            url="{{ Storage::disk('videos')->url($episode->audio_url) }}" 
            length="{{ Storage::disk('videos')->size($episode->audio_url) }}" 
            type="audio/mpeg" />
        @endif

        <guid isPermaLink="true">{{ url('/episodes/' . ($episode->slug ?? uniqid('ep-'))) }}</guid>
        <link>{{ url('/episodes/' . ($episode->slug ?? uniqid('ep-'))) }}</link>
        <pubDate>{{ optional($episode->published_at)->format('D, d M Y H:i:s O') }}</pubDate>
        
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
          <itunes:image href="{{ $episode->cover_image }}" />
        @endif
      </item>
    @endforeach
  </channel>
</rss>
