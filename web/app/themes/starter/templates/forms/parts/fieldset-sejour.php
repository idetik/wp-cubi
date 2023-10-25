<fieldset class="fieldset fieldset--stay" id="fieldset-stay">
    <legend class="legend">Votre séjour</legend>
    <div class="df-listing-sejour__time">
        <time class="time" datetime="<?= $form->getMeta('checkin')->date->format('Y-m-d') ?>">
            <span class="time__day"><?= $form->getMeta('checkin')->date->format('d') ?></span>
            <span class="time__month"><?= $form->getMeta('checkin')->date->translatedFormat('M') ?></span>
            <span class="time__year"><?= $form->getMeta('checkin')->date->format('Y') ?></span>
        </time>
        <div class="time-separator" role="separator" aria-label="jusqu'au">
            <span class="time-separator__bullet" aria-hidden="true">&#8226;</span>
            <span class="time-separator__line" aria-hidden="true"></span>
            <span class="time-separator__arrow" aria-hidden="true"><i class="icon-flaticon-right-chevron"></i></span>
        </div>
        <time class="time" datetime="<?= $form->getMeta('checkout')->date->format('Y-m-d') ?>">
            <span class="time__day"><?= $form->getMeta('checkout')->date->format('d') ?></span>
            <span class="time__month"><?= $form->getMeta('checkout')->date->translatedFormat('M') ?></span>
            <span class="time__year"><?= $form->getMeta('checkout')->date->format('Y') ?></span>
        </time>
    </div>
    <div class="df-listing-sejour__price" role="region" id="sejour-price" aria-live="assertive">
        <?= $form->getMeta('data-price') ?>
    </div>
    <div class="df-listing-sejour__cta">
        <a class="link" href="#fieldset-prices" data-scrollTo="fieldset-prices">
            Voir le détail
            <i class="icon-flaticon-down"></i>
        </a>
    </div>
</fieldset>
