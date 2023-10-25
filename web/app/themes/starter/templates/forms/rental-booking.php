<?php

use Carbon\Carbon;

if ($form->getMeta('booking-error')) {
    ?>
    <p class="h5 margin-top--4 margin-bottom--4">Impossible d'effectuer une réservation sur cette période. Veuillez choisir des dates disponibles en cliquant ici : <a href="<?= $form->getMeta('listing')->permalink() ?>">effectuer une nouvelle réservation.</a> </p>
    <?php
    return;
}

if ($form->submittedOk() && empty($errors)) {
    ?>
    <div id="rental-booking">
        <div class="booking-success">
            <div class="booking-success__icon" aria-hidden="true">
                <i class="icon-flaticon-accept"></i>
            </div>
            <div class="booking-success__content">
                <h2>Confirmée !</h2>
                <p>Votre réservation a bien été enregistrée. Un mail de confirmation vous a été envoyé et une option a été posée pour une durée de 5 jours. Nous allons vous contacter pour procéder au réglement de l'acompte afin de finaliser la réservation.</p>
            </div>
        </div>
    </div>
    <?php
} else {
    $form->viewPart('parts/form-header', ['action' => \Globalis\WP\Cubi\get_current_url()]);
    $form->viewPart('fields/hidden', ['name' => 'period']);
    $form->viewPart('fields/hidden', ['name' => 'checkout']);
    $form->viewPart('fields/hidden', ['name' => 'capacity']);
    ?>
    <p class="field__instructions">Vérifier les informations de votre séjour puis remplissez le formulaire pour réserver le bien.<br/>Les champs marqués * sont obligatoires.</p>
    <div class="grid--6 grid--small-1 grid--has-gutter-3x field__row">
        <div class="grid__col--2 grid__col--medium-6">
            <?php
            $form->viewPart('parts/fieldset-sejour');
            ?>
        </div>
        <div class="grid__col--4  grid__col--medium-6">
            <?php
            $form->viewPart('parts/fieldset-occupant');
            $form->viewPart('parts/fieldset-prestation');
            ?>
        </div>
    </div>
    <?php
    $form->viewPart('parts/fieldset-coordonnees');
    $form->viewPart('parts/fieldset-total');
    $form->viewPart('parts/fieldset-taxes');
    $form->viewPart('parts/fieldset-footer');
    ?>
    <div class="margin-top--4">
        <button type="submit" class="btn btn--secondary btn--block">Réserver</button>
        <p class="field__instructions margin-top--2">Les informations recueillies à partir de ce formulaire permettent le traitement de votre demande.
        Elles sont enregistrées dans le but de traiter votre demande. <a href="<?= get_privacy_policy_url() ?>">En savoir plus sur la gestion de vos données et vos droits.</a></p>
    </div>
    <?php
    $form->viewPart('parts/form-footer');
}
