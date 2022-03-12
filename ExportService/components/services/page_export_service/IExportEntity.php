<?php

namespace frontend\components\services\page_export_service;

interface IExportEntity
{
    public function export(string $fileName, string $content);
}
