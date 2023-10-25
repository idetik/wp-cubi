<?php
$success = $success ?? ($form->submittedOk() && empty($form_data['errors']));
?>
<div class="form-success" data-form-success aria-live="<?= $success ? 'assertive' : 'off' ?>">
<?php
if ($success) :
    $message = $success_message ?? $form_data['success_message'] ?? false;

    if (!empty($message)) :
        $scrollTo = $scrollTo ?? $form_data['scroll_to_success'] ?? false;
        ?>
            <span class="icon icon-info form-success__icon" aria-hidden="true"></span>
            <span class="visuallyhidden">Information !</span>
            <p><?= $message ?></p>

            <?php if ($scrollTo) : ?>
                <script>
                    $formSuccess = $('[data-form-id="<?= $form->id ?>"]').find('[data-form-success]');

                    if ($formSuccess) {
                        $('html, body').animate({
                            scrollTop: ($formSuccess.offset().top - $('.topbar').outerHeight())
                        }, 400, function () {
                            $formSuccess.attr('tabindex', 0);
                            $formSuccess.focus();
                        });
                    }
                </script>
                <?php
            endif;

            $form->triggerJsEvent('success');
    endif;
endif;
?>
</div>
