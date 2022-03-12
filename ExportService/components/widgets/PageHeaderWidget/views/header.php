<?php

use frontend\components\services\page_export_service\widget\PageExportWidget;

/** @var string $exportUrl */
?>
<div class="page-header">
    <div class="page-head">
        ...
    </div>
    <div class="d-si__head_icons">
        <div class="d-si__icons_item load-anim-fade">
            <div class="page-header__panel share__panel">
                ...
            </div>
        </div>
        <div class="d-si__icons_item load-anim-fade">
            ...
        </div>
         <?php echo PageExportWidget::widget([
            'exportUrl' => $exportUrl
        ]); ?>
    </div>
</div>
