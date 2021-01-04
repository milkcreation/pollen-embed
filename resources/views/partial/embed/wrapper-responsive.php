<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 */
?>
<div class="Embed-wrapper Embed-wrapper--responsive" style="padding-bottom:<?php echo $this->get('ratio'); ?>%;">
    <?php echo $this->section('content'); ?>
</div>
