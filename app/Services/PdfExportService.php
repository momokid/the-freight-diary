<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfExportService
{
    /**
     * Render a Blade view to PDF and stream it inline (opens in browser tab).
     */
    public function stream(string $view, array $data, string $filename): Response
    {
        return Pdf::loadView($view, $data)->stream($filename);
    }

    /**
     * Render a Blade view to PDF and force a download.
     */
    public function download(string $view, array $data, string $filename): Response
    {
        return Pdf::loadView($view, $data)->download($filename);
    }
}
