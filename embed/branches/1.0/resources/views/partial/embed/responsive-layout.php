<?php
/**
 * @var tiFy\Contracts\Partial\PartialView $this
 */
?>
<div class="Embed-wrapper--responsive" style="padding-bottom:<?php echo $this->get('ratio'); ?>%;">
    <?php echo $this->section('content'); ?>
</div>
