<?php
/**
 * @var tiFy\Contracts\Field\FieldView $this
 */
?>
<?php $this->layout('wrapper', $this->all()); ?>
<textarea <?php echo $this->htmlAttrs(); ?>><?php echo $this->getValue(); ?></textarea>
