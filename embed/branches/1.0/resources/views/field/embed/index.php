<?php
/**
 * @var tiFy\Field\FieldViewInterface $this
 */
?>
<?php $this->layout('wrapper', $this->all()); ?>
<textarea <?php echo $this->htmlAttrs(); ?>><?php echo $this->getValue(); ?></textarea>
