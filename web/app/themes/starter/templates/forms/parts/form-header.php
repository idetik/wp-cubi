<?php

use function Globalis\WP\Cubi\get_current_url;

$action = $action ?? get_current_url();

if (isset($_GET['redirect_to']) && !empty($_GET['redirect_to']) && false !== filter_var(rawurldecode($_GET['redirect_to']), FILTER_VALIDATE_URL)) {
    $redirect_to = esc_url(rawurldecode($_GET['redirect_to']));
    $redirect_to_encoded = rawurlencode($redirect_to);
    $action = add_query_arg('redirect_to', $redirect_to_encoded, $action);
} else {
    $redirect_to = false;
    $redirect_to_encoded = false;
}

$ajax_refresh = $ajax_refresh ?? $form_data['ajax_refresh'] ?? false;
if (!empty($form_data['data_atts'])) {
    $data_atts = array_merge($form_data['data_atts'], ($data_atts ?? []));
}
if (!empty($form_data['container_id'])) {
    $data_atts = array_merge(['data-container-id="' . $form_data['container_id'] . '"'], ($data_atts ?? []));
}

if ($form->isSubmitting()) {
    if (!$form->submittedOk() || !empty($form_data['errors'])) {
        $data_atts[] = 'aria-invalid="true"';
    } else {
        $data_atts[] = 'aria-invalid="false"';
    }
}

?>
<form
    id="<?= $id ?? $form->id() ?>"
    data-form-id="<?= $form->id ?>"
    method="post"
    action="<?= $action ?>"
    <?= isset($class) ? 'class="' . $class . '"' : null ?>
    <?= isset($data_atts) ? implode(' ', $data_atts) : null ?>
    <?= isset($enctype) ? 'enctype="' . $enctype . '"' : null ?>
    <?= isset($autocomplete) ? 'autocomplete="' . $autocomplete . '"' : null ?>
    <?php $ajax_refresh && print('data-form-refresh') ?>
    <?php $ajax_refresh && printf('data-ajax-endpoint="%s"', $ajax_endpoint ?? $form_data['ajax_endpoint'] ?? admin_url('admin-ajax.php')) ?>
    <?php $ajax_refresh && printf('data-ajax-action="%s"', $ajax_action ?? $form_data['ajax_action'] ?? '') ?>
    novalidate
>
    <input type="hidden" name="<?= $form->fieldName('form_id'); ?>" value="<?= $form->id ?>">
    <?php
    $form->nonceField();
    $ajax_refresh && wp_original_referer_field();
    $form->spamField();
    $form->viewPart('parts/form-errors');
    $form->viewPart('parts/form-success');

    $form->triggerJsEvent('display');
    foreach ($form_data['persistent_data'] ?? [] as $key => $value) :
        printf(
            '<input type="hidden" name="persistent-data[%s]" value="%s">',
            $key,
            esc_attr($value),
        );
    endforeach;
