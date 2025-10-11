<div class="w-full">
    @if($getRecord()->video_url)
        <div class="relative w-full rounded-lg overflow-hidden bg-gray-900" style="padding-bottom: 56.25%;">
            <video 
                controls 
                class="absolute top-0 left-0 w-full h-full"
                preload="metadata"
            >
                <source src="{{ $getRecord()->video_url }}" type="{{ $getRecord()->mime_type ?? 'video/mp4' }}">
                Your browser does not support the video tag.
            </video>
        </div>
    @endif
</div>