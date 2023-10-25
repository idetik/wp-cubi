<fieldset class="fieldset" id="fieldset-contact">
    <legend class="legend">Vos coordonnées</legend>

    <div class="grid--2 grid--small-1 grid--has-gutter-3x field__row">
        <?php
        $form->viewPart(
            'fields/text',
            [
                'label' => 'Nom',
                'required' => true,
                'name' => 'lastname',
                'input' => [
                    'placeholder' => 'Votre nom',
                    'autocomplete' => 'family-name'
                ]
            ]
        );
        $form->viewPart(
            'fields/text',
            [
                'label' => 'Prénom',
                'required' => true,
                'name' => 'firstname',
                'input' => [
                    'placeholder' => 'Votre prénom',
                    'autocomplete' => 'given-name'
                ]
            ]
        );
        ?>
    </div>
    <div class="grid--2 grid--small-1 grid--has-gutter-3x field__row">
        <?php
        $form->viewPart(
            'fields/email',
            [
                'label' => 'Email',
                'required' => true,
                'name' => 'email',
                'input' => [
                    'placeholder' => 'Votre adresse email',
                    'autocomplete' => 'email'
                ]
            ]
        );
        $form->viewPart(
            'fields/tel',
            [
                'label' => 'Téléphone',
                'required' => true,
                'name' => 'phone',
                'input' => [
                    'placeholder' => 'Votre numéro de téléphone',
                    'autocomplete' => 'tel'
                ]
            ]
        );
        ?>
    </div>
    <div class="grid--2 grid--small-1 grid--has-gutter-3x field__row">
        <?php
        $form->viewPart(
            'fields/text',
            [
                'label' => 'Adresse',
                'required' => true,
                'name' => 'address',
                'input' => [
                    'placeholder' => 'Exemple : 13 Pl. de Castille',
                    'autocomplete' => 'address-line1'
                ]
            ]
        );
        $form->viewPart(
            'fields/text',
            [
                'label' => 'Code postal',
                'required' => true,
                'name' => 'postal-code',
                'input' => [
                    'placeholder' => 'Exemple : 40510',
                    'autocomplete' => 'postal-code'
                ]
            ]
        );
        $form->viewPart(
            'fields/text',
            [
                'label' => 'Ville',
                'required' => true,
                'name' => 'city',
                'input' => [
                    'placeholder' => 'Exemple : Seignosse',
                    'autocomplete' => 'home city'
                ]
            ]
        );
        $form->viewPart(
            'fields/text',
            [
                'label' => 'Pays',
                'required' => true,
                'name' => 'country',
                'input' => [
                    'placeholder' => 'Exemple : France',
                    'autocomplete' => 'country-name'
                ]
            ]
        );
        ?>
    </div>
</fieldset>
