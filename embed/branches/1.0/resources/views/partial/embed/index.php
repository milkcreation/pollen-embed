<?php
/**
 * @var tiFy\Contracts\Partial\PartialView $this
 */
?>
<?php if ($this->get('responsive')) : ?>
<?php $this->layout('responsive-layout', $this->all()); ?>
<?php endif; ?>
<div <?php echo $this->htmlAttrs(); ?>></div>
