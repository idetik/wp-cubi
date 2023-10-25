<?php

$required = !empty($required);
$disabled = !empty($disabled);
$class = $class ?? 'field__file';
$id = $id ?? $name;

?>
<span
    role="button"
    aria-controls="<?= $id ?>-filename"
    tabindex="0"
    class="field__file-button"
>
    <span class="field__file-button--empty">
        <?= $placeholder ?? '' ?>
        <span aria-hidden="true" class="icon icon-plus"></span>
    </span>
    <span class="field__file-button--filled">
        <span class="field__file-button__filename"></span>
        <span class="field__file-button__update">modifier le fichier</span>
    </span>
</span>
<input
    id="<?= $id ?>"
    name="<?= $form->fieldName($name) ?>"
    type="file"
    class="<?= $class ?>"
    <?= $required ? 'aria-required="true"' : '' ?>
    <?= isset($data_atts) ? implode(' ', $data_atts) : null ?>
    hidden
/>
