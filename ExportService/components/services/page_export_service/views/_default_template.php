<?php
/** @var string $content */
?>
<div style="font-family: Tahoma, sans-serif; font-size: 11pt">
    <?php echo $this->render('_header_template'); ?>

    <?php $this->beginBlock('content'); ?>
    <?php echo $content; ?>
    <?php $this->endBlock(); ?>

    <?php echo $this->blocks['content'] ?>
</div>