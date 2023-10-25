<?php

use function Globalis\WP\Cubi\include_template_part;

use Coretik\Services\Forms\Core\Utils;

$label = $label ?? false;
$label_html = $label_html ?? false;
$label_hidden = $label_hidden ?? false;
$required = $required ?? false;
$error = $error ?? $form->getErrorMessage(Utils::removeBracket($name)) ?? false;
$has_error = !empty($error);
$after_label_html = $after_label_html ?? '';
$after_component_html = $after_component_html ?? '';

?>
<div
    class="field field--<?= $name ?><?= $has_error ? ' form-error' : '' ?>"
    data-field="<?= $name ?>"
    <?= isset($error_id) ? 'id="' . $error_id . '"' : '' ?>
>
    <?php
    if ($label_html) :
        echo $label_html;
    elseif ($label) :
        include_template_part('templates/forms/fields/components/label', [
            'label' => $label,
            'hidden' => $label_hidden,
            'required' => $required,
            'for' => $component['args']['id']
        ]);
    endif;

    print($after_label_html);

    if (!empty($component) && is_array($component)) :
        $form->viewPart(
            $component['template'],
            $component['args'] + ['has_error' => $has_error]
        );
    elseif (!empty($component) && is_string($component)) :
        print($component);
    endif;

    print($after_component_html);

    if ($has_error) :
        if (isset($component['args']['id'])) {
            $errorId = $component['args']['id'] . '-error';
        } else if (isset($component['args']['options']) && !empty($component['args']['options'])) {
            $errorId = $form->id() . '-' . $component['args']['options'][0]['name'] . '-error';
        }

        ?>
        <p class="form-error__content" id="<?= $errorId ?>">
            <span class="icon icon-important form-error__icon" aria-hidden="true"></span>
            <span class="visuallyhidden">Attention !</span>
            <?= $error ?>
        </p>
        <?php
    endif;
    ?>
</div>
