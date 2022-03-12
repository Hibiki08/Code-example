<?php

namespace frontend\components\services\page_export_service;

use Yii;

/**
 * Class DocExportEntity
 * @package frontend\components\services\page_export_service
 */
class DocExportEntity implements IExportEntity
{
    public function export(string $fileName, string $content)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        HtmlParser::addHtml($section, $content);
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $filePath = Yii::getAlias("@runtime/{$fileName}.docx");
        $objWriter->save($filePath);
        header("Content-Disposition: attachment; filename={$fileName}.docx");
        readfile($filePath);
        unlink($filePath);
        exit;
    }
}
