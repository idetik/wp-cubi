<?php

$input_args_default = [
    'required' => $required ?? false,
    'name' => $name,
    'id' => sprintf('%s-%s', $form->id(), $name),
];
$input_args = array_merge($input_args_default, $input ?? []);
$input_args['type'] = 'hidden';

$form->viewPart(
    'fields/components/input-text',
    $input_args
);
