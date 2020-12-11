<?php
/**
 * @var tiFy\Contracts\Partial\PartialView $this
 */
?>
<?php if ($this->get('responsive')) : ?>
    <?php $this->layout('responsive-layout', $this->all()); ?>
<?php endif; ?>
<video <?php echo $this->htmlAttrs(); ?>>
    <?php if ($sources = $this->get('sources')) : ?>
        <?php foreach ($this->get('sources') as $source) : ?>
            <?php echo partial('tag', $source); ?>
        <?php endforeach; ?>
    <?php endif; ?>
</video>
