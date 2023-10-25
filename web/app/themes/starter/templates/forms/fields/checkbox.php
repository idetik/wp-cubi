<?php

/**
 *  $name,
 *  $label,
 *  $required,
 *  $options : [
 *     [
 *         'label' => '',
 *         'value' => '',
 *         ----------- Optional ----------
 *         'name' => '',
 *         'id' => '',
 *         'disabled' => true|false,
 *         'required' => true|false,
 *         'data_atts' => []
 *         -------------------------------
 *      ]
 *  ]
 */

 $required = $required ?? false;

$input_args_default = [
    'required' => $required,
    'name' => $name,
    'refresh' => $refresh ?? null,
];

foreach ($options as $i => &$option) {
    $option = array_merge($input_args_default, [
        'id' => sprintf('%s-%s-%s', $form->id(), $name, $i),
    ], $option);
}

$component = [
    'template' => 'fields/components/checkbox-group',
    'args' => [
        'options' => $options,
        'inline' => $inline ?? false
    ]
];

// Use custom field label for checkboxes group
if (!empty($label)) {
    ob_start();
    ?>
    <span class="field__label field__label-radio<?= ($label_hidden ?? false) ? ' visuallyhidden' : null ?>">
        <?= $label ?>
        <?php if ($required) : ?>
            <span aria-hidden="true">*</span>
            <span class="visuallyhidden">(champs obligatoire)</span>
        <?php endif; ?>
    </span>
    <?php
    $label_html = ob_get_clean();
    $label = false;
}

include locate_template('templates/forms/fields/field.php');
