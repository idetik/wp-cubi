<?php

$required = !empty($required);
$disabled = !empty($disabled);
$class = $class ?? 'field__checkbox-input';
$nameAttr = ($auto_bracket ?? true) ? $form->fieldName($name) . '[]' : $form->fieldName($name);

$values = $form->getValue($name);
if (is_array($values)) {
    $checked = checked(in_array($value, $values), true, false);
} else {
    $checked = checked($value, $values ?? '', false);
}
?>
<input
    class="<?= $class ?>"
    type="checkbox"
    name="<?= $nameAttr ?>"
    id="<?= $id ?? $name ?>"
    value="<?= $value ?>"
    <?= $checked ?>
    <?= $required ? 'aria-required="true"' : '' ?>
    <?= $disabled ? 'disabled="disabled"' : '' ?>
    <?= isset($data_atts) ? implode(' ', $data_atts) : null ?>
    <?= isset($refresh) ? sprintf('data-trigger-refresh="%s"', $refresh) : null ?>
    />