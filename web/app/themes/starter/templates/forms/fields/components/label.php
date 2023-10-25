<?php

$class = $class ?? 'field__label';
$required = $required ?? false;

if ($hidden ?? false) {
    $class .= ' visuallyhidden';
}

?>
<label for="<?= $for ?>" class="<?= $class ?>">
    <?= $label ?>
    <?= ($required ?? false) ? '<span aria-hidden="true">*</span><span class="visuallyhidden">(champs obligatoire)</span>' : null ?>
</label>