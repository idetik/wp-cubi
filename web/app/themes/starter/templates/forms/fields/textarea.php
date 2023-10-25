<?php

/**
 * Textarea :
 * --------------------------
 * [
 *    'name' => '', (required)
 *    'label' => '',
 *    'required' => true|false,
 *    'error' => '',
 *    'input' => [
 *        'rows' => '',
 *        'cols' => '',
 *        'id' => '',
 *        'placeholder' => '',
 *        'data_atts' => [],
 *        'disabled' => true|false,
 *     ]
 * ]
 */

$input_args_default = [
    'required' => $required ?? false,
    'name' => $name,
    'id' => sprintf('%s-%s', $form->id(), $name),
];
$input_args = array_merge($input_args_default, $input ?? []);

$component = [
    'template' => 'fields/components/input-textarea',
    'args' => $input_args
];

include locate_template('templates/forms/fields/field.php');
