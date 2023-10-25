<?php

/**
 *  $name,
 *  $label,
 *  $placeholder,
 *  $required,
 *  $id,
 *  $disabled => true|false,
 *  $data_atts => []
 *  $options : [
 *     [
 *         'label' => '',
 *         'value' => '',
 *         ----------- Optional ----------
 *         'data_atts' => []
 *         -------------------------------
 *      ]
 *  ]
 */

 $required = $required ?? false;

if ($required && 1 === count($options)) {
    $form->setValue($name, current($options)['value']);
}

$input_args_default = [
    'required' => $required,
    'name' => $name,
    'refresh' => $refresh ?? null,
    'id' => sprintf('%s-%s', $form->id(), $name),
    'options' => $options
];
$input_args = array_merge($input_args_default, $input ?? []);

$component = [
    'template' => 'fields/components/select',
    'args' => $input_args
];


include locate_template('templates/forms/fields/field.php');
