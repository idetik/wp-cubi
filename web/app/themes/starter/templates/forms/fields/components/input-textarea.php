<?php

$max_length     = $form->fieldConstraint($name, 'max-size');
$class          = $class ?? 'field__textarea';

?>
<textarea
    name="<?= $form->fieldName($name) ?>"
    class="<?= $class ?>"
    id="<?= $id ?? $name ?>"
    placeholder="<?= !empty($placeholder) ? $placeholder : ' ' ?>"
    rows="<?= $rows ?>"
    <?= $required ? 'aria-required="true"' : 'aria-required="false"' ?>
    <?= !empty($max_length) ? 'maxlength="' . $max_length . '"' : '' ?>
    <?= !empty($cols) ? 'cols="' . $cols . '"' : '' ?>
    <?= isset($data_atts) ? implode(' ', $data_atts) : null ?>
    <?= ($disabled ?? false) ? 'disabled="disabled"' : '' ?>
    <?= ($has_error ?? false) ? 'aria-invalid="true"' : 'aria-invalid="false"' ?>
    <?= ($has_error ?? false) ? 'aria-describedby="' . $id . '-error"' : '' ?>
><?= $form->getValue($name) ?></textarea>
