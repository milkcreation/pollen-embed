<?php
/**
 * @var tiFy\Contracts\Partial\PartialView $this
 */
?>
<div style="position:relative;width:100%;height:0;padding-bottom:<?php echo $this->get('ratio'); ?>%;">
    <?php echo $this->section('content'); ?>
</div>
