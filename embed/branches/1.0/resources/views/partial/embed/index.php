<?php
/**
 * @var tiFy\Contracts\Partial\PartialView $this
 */
?>
<?php if ($this->get('responsive')) : ?>
<?php $this->layout('responsive-layout', $this->all()); ?>
<?php endif; ?>
<iframe <?php echo $this->htmlAttrs(); ?>></iframe>
