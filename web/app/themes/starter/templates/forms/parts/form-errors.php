<?php

$errors = $form->getErrors();

?>
<div class="form-error" data-form-errors aria-live="<?= !empty($errors) ? 'assertive' : 'off' ?>" aria-atomic="true">
    <?php if (!empty($errors)) :
        $scrollTo = $scrollTo ?? $form_data['scroll_to_errors'] ?? false;
        ?>
        <p class="form-error__content">
            <span class="icon icon-important form-error__icon" aria-hidden="true"></span>
            <span class="visuallyhidden">Attention !</span>
            Certains champs contiennent des erreurs :
        </p>
        <ul class="form-error__list">
            <?php
                $errors = array_map(
                    function ($field) use ($form) {
                        return [
                            'id' => $form->id() . '-' . $field,
                            'name' => $form->fields[$field]['name'],
                        ];
                    },
                    array_keys($form->getErrors())
                );
            ?>
            <?php foreach ($errors as $error) :  ?>
                <li>
                    <a href="#<?= $error['id'] ?>" class="form-error-anchor">
                        <span class="visuallyhidden">Aller au champ</span>
                        <?= $error['name'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
        if (defined('WP_ENV') && 'development' === WP_ENV) {
            $errors = array_map(
                fn ($field) => $field . ' : ' . $form->getErrorMessage($field),
                array_keys($form->getErrors())
            );
            printf(
                '<h3>DEBUG</h3><p>%s</p><pre>%s</pre>',
                $form->getErrorDebug(),
                print_r($errors, true)
            );
        }
        ?>
        <?php if ($scrollTo) : ?>
            <script>
                $formErrors = $('[data-form-id="<?= $form->id ?>"]').find('[data-form-errors]');

                if ($formErrors) {
                    $('html, body').animate({
                        scrollTop: ($formErrors.offset().top - $('.topbar').outerHeight())
                    }, 400, function () {
                        $formErrors.attr('tabindex', 0);
                        $formErrors.focus();
                    });
                }
            </script>
        <?php endif; ?>
    <?php endif; ?>
</div>
