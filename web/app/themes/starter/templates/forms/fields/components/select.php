<?php

$current_value = $form->getValue($name);
$class = $class ?? 'field__select';

$use_group = !empty($options) && isset(current($options)['group']);

ob_start();

if ($use_group) {
    $options = collect($options)
        ->groupBy('group')
        ->all();

    foreach ($options as $groupLabel => $groupOptions) {
        printf('<optgroup label="%s">', $groupLabel);
        foreach ($groupOptions as $option) : ?>
                <option
                    value="<?= $option['value'] ?>"
                    <?php selected($current_value, $option['value']) ?>
                    <?= isset($option['data_atts']) ? implode(' ', $option['data_atts']) : null ?>
                    >
                    <?= $option['label'] ?>
                </option>
        <?php endforeach;
        echo "</optgroup>";
    }
} else {
    foreach ($options as $option) : ?>
        <option
            value="<?= $option['value'] ?>"
            <?php selected($current_value, $option['value']) ?>
            <?= isset($option['data_atts']) ? implode(' ', $option['data_atts']) : null ?>
            >
            <?= $option['label'] ?>
        </option>
    <?php endforeach;
}

$options = ob_get_clean();

?>
<select
    name="<?= $form->fieldName($name) ?>"
    id="<?= $id ?? $name ?>"
    class="<?= $class ?>"
    <?= ($required ?? false) ? 'required="required" aria-required="true"' : 'aria-required="false"' ?>
    <?= ($disabled ?? false) ? 'disabled="disabled"' : '' ?>
    <?= isset($data_atts) ? implode(' ', $data_atts) : null ?>
    <?= isset($refresh) ? sprintf('data-trigger-refresh="%s"', $refresh) : null ?>
    <?= ($has_error ?? false) ? 'aria-invalid="true"' : 'aria-invalid="false"' ?>
    <?= ($has_error ?? false) ? 'aria-describedby="' . $id . '-error"' : '' ?>
    >
    <option value="" <?= $required ? ' disabled' : '' ?><?= empty($current_value) ? ' selected' : '' ?>><?= !empty($placeholder) ? $placeholder : 'Sélectionnez' ?></option>
    <?= $options ?>
</select>
