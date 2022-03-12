<?php

namespace frontend\components\services\page_export_service\widget;

use yii\base\Widget;

class PageExportWidget extends Widget
{
    /** @var array */
    public $formats = [
        ['type' => 'doc', 'caption' => '*.doc(x)'],
        ['type' => 'pdf', 'caption' => '*.pdf'],
    ];

    /** @var array */
    public $exportUrl;

    public function run()
    {
        return $this->render('export', [
            'formats' => $this->formats,
            'exportUrl' => $this->exportUrl
        ]);
    }
}
