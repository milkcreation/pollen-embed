<?php
/**
 * @var tiFy\Contracts\Partial\PartialView $this
 */
?>
<?php if ($this->get('responsive')) : ?>
    <?php $this->layout('responsive-layout', $this->all()); ?>
<?php endif; ?>

<?php $this->insert($this->get('provider'), $this->all());