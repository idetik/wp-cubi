<?php

$required       = $required ?? false;
$max_length     = $form->fieldConstraint($name, 'max-size');
$value          = $form->getValue($name);
$output_value   = !empty($datalist) ? collect($datalist)->where('value', $value)->pluck('label')->first() : $value;
$class          = $class ?? 'field__range';
$id             = $id ?? $name;
$list_id        = $list_id ?? (($datalist ?? false) ? $id . '-list' : null);

?>
<div class="field-input-range">
    <input
        type="range"
        name="<?= $form->fieldName($name) ?>"
        class="<?= $class ?>"
        <?= $required ? 'aria-required="true"' : 'aria-required="false"' ?>
        id="<?= $id ?? $name ?>"
        value="<?= $value ?>"
        <?= !empty($min) ? 'min="' . $min . '"' : '' ?>
        <?= !empty($max) ? 'max="' . $max . '"' : '' ?>
        <?= !empty($step) ? 'step="' . $step . '"' : '' ?>
        <?= !empty($list_id) ? 'list="' . $list_id . '"' : '' ?>
        <?= ($disabled ?? false) ? 'disabled="disabled"' : '' ?>
        <?= ($readonly ?? false) ? 'readonly="readonly"' : '' ?>
        <?= ($has_error ?? false) ? 'aria-invalid="true"' : 'aria-invalid="false"' ?>
        <?= ($has_error ?? false) ? 'aria-describedby="' . $id . '-error"' : '' ?>
        />
    <?php printf('<output for="%s" class="field__output"><span id="%s">%s</span>%s</output>', $id, $id . '-output', $output_value, $output['suffix'] ?? ''); ?>
</div>

<?php
if (!empty($datalist)) :
    ?>
    <datalist id="<?= $list_id ?>">
        <?= implode('', array_map(
            fn ($item) => sprintf('<option value="%s" label="%s"></option>', $item['value'], $item['label']),
            $datalist
        ))
        ?>
    </datalist>
<?php endif; ?>
<?php if (!empty($list_id)) : ?>
    <script>
        document.getElementById('<?= $id ?>').addEventListener('input', function(e) {
            const list = document.getElementById("<?= $list_id ?>");
            const value = e.target.value;
            const listOption = [...list.options].find((option) => option.value === e.target.value);
            document.getElementById('<?= $id ?>-output').textContent = listOption.label || listOption.value;
        });
    </script>
<?php else : ?>
    <script>
        document.getElementById('<?= $id ?>').addEventListener('input', function(e) {
            document.getElementById('<?= $id ?>-output').textContent = e.target.value;
        });
    </script>
<?php endif;
