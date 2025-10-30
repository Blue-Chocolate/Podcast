<?php

namespace App\Filament\Resources\NewsResource\Pages;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;
    protected function afterCreate(): void
{
    $news = $this->record;

    $html = view('pdf.news', compact('news'))->render();

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $fileName = 'news_' . $news->id . '.pdf';
    Storage::disk('public')->put("news-pdfs/{$fileName}", $dompdf->output());
}
}