<?php

$required = !empty($required);
$disabled = !empty($disabled);
$class = $class ?? 'field__radio-input';

?>
<input
    class="<?= $class ?>"
    type="radio"
    name="<?= $form->fieldName($name) ?>"
    id="<?= $id ?? $name ?>"
    value="<?= $value ?>"
    <?= checked($value, $form->getValue($name) ?? '') ?>
    <?= $required ? 'aria-required="true"' : '' ?>
    <?= $disabled ? 'disabled="disabled"' : '' ?>
    <?= isset($data_atts) ? implode(' ', $data_atts) : null ?>
    <?= isset($refresh) ? sprintf('data-trigger-refresh="%s"', $refresh) : null ?>
    />
