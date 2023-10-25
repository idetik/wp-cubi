<fieldset class="fieldset">
    <legend class="legend">Votre demande</legend>
    <?php
    $form->viewPart(
        'fields/textarea',
        [
            'label' => 'Message',
            'required' => $required ?? true,
            'name' => 'message',
            'input' => [
                'placeholder' => 'Bonjour, je vous contacte pour...',
                'cols' => 30,
                'rows' => 10,
            ]
        ]
    );
    ?>
</fieldset>