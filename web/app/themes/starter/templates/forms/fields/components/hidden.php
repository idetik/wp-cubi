<?php

$value = isset($hide_value) && true === $hide_value ? '' : $form->getValue($name); ?>
<input 
    type="hidden"
    name="<?= $form->fieldName($name) ?>"
    id="<?= $id ?? $name ?>"
    value="<?= $value ?>"
    <?= isset($data_atts) ? implode(' ', $data_atts) : null ?>
    />
