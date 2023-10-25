<?php

if (empty($acceptances)) {
    $acceptances = [
        'cgu' => [
            'label' => 'Je reconnais avoir pris connaissance des&nbsp;<a rel="nofollow" href="' . app()->agency()->get('generalTermsURI') . '" target="_blank">conditions générales</a>',
            'value' => 'cgu',
        ],
    ];

    if (!empty($options)) {
        $acceptances = array_intersect_key($acceptances, array_flip($options));
    }
}

if (!empty($append_acceptances)) {
    $acceptances = array_merge($acceptances, $append_acceptances);
}

?>
<div class="form-fieldset-footer">
    <p><strong>Une option qui bloque votre logement pendant 5 jours va être créée.</strong> Nous restons en attente de réception de votre règlement d'acompte, et allons vous contacter très rapidement à ce sujet.</p>
    <?php

    $form->viewPart(
        'fields/checkbox',
        [
            'required' => true,
            'name' => 'acceptance',
            'error_id' => $form->id() . '-acceptance',
            'options' => array_values($acceptances)
        ]
    );
    ?>
</div>
