<?php

$type           = !empty($type)  ? $type  : 'text';
$required       = $required ?? false;
$max_length     = $form->fieldConstraint($name, 'max-size');
$value          = isset($hide_value) && true === $hide_value ? '' : $form->getValue($name);
$class          = $class ?? 'field__input';

?>
<input
    type="<?= $type ?>"
    name="<?= $form->fieldName($name) ?>"
    class="<?= $class ?>"
    <?= $required ? 'aria-required="true"' : 'aria-required="false"' ?>
    <?= !empty($max_length) ? 'maxlength="' . $max_length . '"' : '' ?>
    id="<?= $id ?? $name ?>"
    placeholder="<?= !empty($placeholder) ? $placeholder : ' ' ?>"
    value="<?= $value ?>"
    <?= !empty($autocapitalize) ? 'autocapitalize="' . $autocapitalize . '"' : '' ?>
    <?= !empty($autocomplete) ? 'autocomplete="' . $autocomplete . '"' : '' ?>
    <?= isset($data_atts) ? implode(' ', $data_atts) : null ?>
    <?= ($disabled ?? false) ? 'disabled="disabled"' : '' ?>
    <?= ($readonly ?? false) ? 'readonly="readonly"' : '' ?>
    <?= ($has_error ?? false) ? 'aria-invalid="true"' : 'aria-invalid="false"' ?>
    <?= ($has_error ?? false) ? 'aria-describedby="' . $id . '-error"' : '' ?>
    />
