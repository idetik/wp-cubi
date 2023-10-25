<?php

$required       = $required ?? false;
$value          = $form->getValue($name);
$class          = $class ?? 'field__date';
$id             = $id ?? $name;
$list_id        = $list_id ?? (($datalist ?? false) ? $id . '-list' : null);

?>
<input
    type="date"
    name="<?= $form->fieldName($name) ?>"
    class="<?= $class ?>"
    <?= $required ? 'aria-required="true"' : 'aria-required="false"' ?>
    id="<?= $id ?? $name ?>"
    placeholder="<?= !empty($placeholder) ? $placeholder : ' ' ?>"
    value="<?= $value ?>"
    <?= !empty($min) ? 'min="' . $min . '"' : '' ?>
    <?= !empty($max) ? 'max="' . $max . '"' : '' ?>
    <?= !empty($step) ? 'step="' . $step . '"' : '' ?>
    <?= !empty($list_id) ? 'list="' . $list_id . '"' : '' ?>
    <?= isset($refresh) ? 'data-trigger-refresh' : null ?>
    <?= ($disabled ?? false) ? 'disabled="disabled"' : '' ?>
    <?= ($readonly ?? false) ? 'readonly="readonly"' : '' ?>
    <?= ($has_error ?? false) ? 'aria-invalid="true"' : 'aria-invalid="false"' ?>
    <?= ($has_error ?? false) ? 'aria-describedby="' . $id . '-error"' : '' ?>
    data-datepicker
    />

<?php
if (!empty($datalist)) :
    ?>
    <datalist id="<?= $list_id ?>">
        <?= implode('', array_map(
            fn ($item) => sprintf('<option value="%s"></option>', $item),
            $datalist
        ))
        ?>
    </datalist>
    <?php
endif;
