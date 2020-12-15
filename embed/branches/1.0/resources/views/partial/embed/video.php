<?php
/**
 * @var tiFy\Contracts\Partial\PartialView $this
 */
?>
<video <?php echo $this->htmlAttrs(); ?>>
    <?php if ($sources = $this->get('sources')) : ?>
        <?php foreach ($this->get('sources') as $source) : ?>
            <?php echo partial('tag', $source); ?>
        <?php endforeach; ?>
    <?php endif; ?>
</video>
