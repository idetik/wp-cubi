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
 *         'id' => '',
 *         'disabled' => true|false,
 *         'data_atts' => []
 *         -------------------------------
 *      ]
 *  ]
 */

$required = $required ?? false;
$refresh = $refresh ?? null;

if ($required && 1 === count($options)) {
    $form->setValue($name, current($options)['value']);
}

$input_args_default = [
    'required' => $required,
    'name' => $name,
    'refresh' => $refresh,
];

foreach ($options as $i => &$option) {
    $option = array_merge($input_args_default, [
        'id' => sprintf('%s-%s-%s', $form->id(), $name, $i),
    ], $option);
}

$component = [
    'template' => 'fields/components/radio-group',
    'args' => [
        'options' => $options,
        'inline' => $inline ?? false
    ]
];

// Use custom field label for radio group
if (!empty($label)) {
    ob_start();
    ?>
    <span class="field__label field__label-radio" id="<?= $form->id() . '-' . $name ?>">
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
