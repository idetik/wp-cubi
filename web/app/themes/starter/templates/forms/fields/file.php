<?php

/**
 * File :
 * --------------------------
 * [
 *    'name' => '', (required)
 *    'label' => '',
 *    'required' => true|false,
 *    'error' => '',
 *    'input' => [
 *        'id' => '',
 *        'placeholder' => '',
 *        'class' => '',
 *        'data_atts' => [],
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
    'template' => 'fields/components/input-file',
    'args' => $input_args
];

include locate_template('templates/forms/fields/field.php');
