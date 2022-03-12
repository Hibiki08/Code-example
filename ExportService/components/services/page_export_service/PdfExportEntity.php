<?php

namespace frontend\components\services\page_export_service;

use Mpdf\HTMLParserMode;
use Mpdf\Output\Destination;
use Yii;

class PdfExportEntity implements IExportEntity
{
    public function export(string $fileName, string $content)
    {
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'tempDir' => Yii::getAlias('@temp')]);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($content, HTMLParserMode::HTML_BODY);
        $mpdf->Output($fileName . '.pdf', Destination::DOWNLOAD);
        Yii::$app->end();
    }
}
