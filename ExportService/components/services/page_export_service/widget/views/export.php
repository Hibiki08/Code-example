<?php

use yii\helpers\Url;

/** @var array $exportUrl */
/** @var array $formats */
?>
<div class="d-si__icons_item download load-anim-fade">
    ...
    <div class="page-header__panel download__panel">
        <?php foreach ($formats as $format) { ?>
                <?php $exportUrl['type'] = $format['type']; ?>
            <a href="<?php echo Url::to($exportUrl); ?>" class="download__a" target="_blank">
                <?php echo $format['caption']; ?>
            </a>
        <?php } ?>
    </div>
</div>