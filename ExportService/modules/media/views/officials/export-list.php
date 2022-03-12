<?php
/** @var string $title */
/** @var \common\models\officials\Officials[] $officials */
?>
<h1 style="font-size: 16pt; text-transform: capitalize"><?php echo $title; ?></h1><br/>
<?php foreach ($officials as $official) { ?>
<div>
    <h2 style="font-size: 12pt;"><b><?php echo $official->FIO; ?></b></h2>
    <span><?php echo $official->BIOGRAPHY; ?></span>
</div><br/>
<?php } ?>