<?php
/**
 * @var tiFy\Field\FieldViewInterface $this
 */
?>
<div class="FieldEmbed-wrapper">
    <div class="FieldEmbed-input">
        <?php echo $this->section('content'); ?>

        <?php if ($name = $this->get('provider_datas.name')) : ?>
            <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $this->get('provider_datas.value'); ?>"/>
        <?php endif; ?>

        <div class="FieldEmbed-spinner"></div>
    </div>
    <div class="FieldEmbed-preview"></div>
</div>
