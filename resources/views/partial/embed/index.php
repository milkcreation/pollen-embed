<?php
/**
 * @var tiFy\Contracts\Partial\PartialView $this
 */
?>
<?php if ($this->get('responsive')) : ?>
    <?php $this->layout('wrapper-responsive', $this->all()); ?>
<?php else : ?>
    <?php $this->layout('wrapper-default', $this->all()); ?>
<?php endif; ?>

<?php $this->insert($this->get('tmpl'), $this->all());