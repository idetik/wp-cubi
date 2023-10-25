<?php

$form->viewPart('parts/form-header', ['action' => \Globalis\WP\Cubi\get_current_url()]);

$subject = $form->getMeta('subject');

if ($form->submittedOk() && empty($errors)) {
    ?>
        <script>
            $('html, body').animate({
                scrollTop: ($('[data-form-id="<?= $form->id ?>"]').offset().top - 40)
            }, 400);
        </script>
        <div class="u-format margin-top--5 margin-bottom--10">
        <?= $form->getMeta('model')->formSuccess() ?>
        </div>
        <?php
} else {
    ?>
        <p class="margin-bottom--6"><?= $form->getMeta('model')->formInstructions() ?></p>
        <div class="grid--12 grid--has-gutter-3x">
            <div class="grid__col--6 grid__col--small-12">
            <?php
            $form->viewPart(
                'fields/text',
                [
                    'label' => 'Votre nom',
                    'required' => true,
                    'name' => 'lastname',
                    'input' => [
                        'placeholder' => 'Nom'
                    ]
                ]
            );
            ?>
            </div>
            <div class="grid__col--6 grid__col--small-12">
                <?php
                $form->viewPart(
                    'fields/text',
                    [
                        'label' => 'Votre prénom',
                        'required' => true,
                        'name' => 'firstname',
                        'input' => [
                            'placeholder' => 'Prénom'
                        ]
                    ]
                );
                ?>
            </div>
            <div class="grid__col--12">
                <p class="field__instructions margin-bottom--2">Veuillez saisir votre email et/ou votre numéro de téléphone.</p>
                <?php
                $form->viewPart(
                    'fields/email',
                    [
                        'label' => 'Votre email',
                        'required' => false,
                        'name' => 'email',
                        'input' => [
                            'placeholder' => 'Email',
                        ]
                    ]
                );
                ?>
            </div>
            <div class="grid__col--12">
                <?php
                $form->viewPart(
                    'fields/text',
                    [
                        'label' => 'Votre téléphone',
                        'required' => false,
                        'name' => 'phone',
                        'input' => [
                            'placeholder' => 'Téléphone',
                            'type' => 'tel'
                        ]
                    ]
                );
                ?>
            </div>
            <?php
            if ($form->getMeta('model')->get('use_subjects')) : ?>
                <div class="grid__col--12">
                    <?php
                    $form->viewPart(
                        'fields/select',
                        [
                            'label' => 'Objet de la demande',
                            'required' => true,
                            'name' => 'subject',
                            'options' => array_map(fn($subject) => ['label' => $subject['title'], 'value' => $subject['value']], $form->getMeta('model')->subjects),
                        ]
                    );
                    ?>
                </div>
            <?php endif; ?>
            <div class="grid__col--12">
                <?php
                $form->viewPart(
                    'fields/textarea',
                    [
                    'label' => 'Votre message',
                    'required' => true,
                    'name' => 'message',
                    'input' => [
                        'placeholder' => 'Message',
                        'rows' => '10'
                    ]
                    ]
                );
                ?>
            </div>
        </div>

        <div class="margin-top--12">
            <p class="field__instructions margin-bottom--3"><em>Aucune donnée personnelle n’est conservée par notre site via ce formulaire.</em></p>
            <button type="submit" class="btn btn--secondary btn--block">
            Envoyer
            </button>
        </div>
    <?php
}
$form->viewPart('parts/form-footer');
