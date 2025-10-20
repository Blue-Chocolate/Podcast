@if($state)
    <audio controls preload="metadata" style="width: 200px;">
        <source src="{{ Storage::disk('public')->url($state) }}" type="audio/mpeg">
        متصفحك لا يدعم تشغيل الصوت.
    </audio>
@else
    <span>-</span>
@endif
