<?php

/**
 * Range :
 * --------------------------
 * [
 *    'name' => '', (required)
 *    'label' => '',
 *    'required' => true|false,
 *    'error' => '',
 *    'input' => [
 *        'id' => '',
 *        'min' => '',
 *        'max' => '',
 *        'step' => '',
 *        'list' => '',
 *        'disabled' => true|false,
 *     ]
 * ]
 */

$input_args_default = [
    'required' => $required ?? false,
    'name' => $name,
    'id' => sprintf('%s-%s', $form->id(), $name),
    'output' => $output ?? []
];
$input_args = array_merge($input_args_default, $input ?? []);

$component = [
    'template' => 'fields/components/input-range',
    'args' => $input_args
];

include locate_template('templates/forms/fields/field.php');
