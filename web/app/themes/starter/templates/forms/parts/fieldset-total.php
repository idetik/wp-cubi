<fieldset class="fieldset" id="fieldset-prices" aria-live="polite" aria-relevant="all" aria-atomic="true">
    <legend class="legend">Récapitulatif financier</legend>
    <div class="df-listing-overview">
        <?php
        $details = $form->getMeta('data-price-details');

        foreach ($details as $data) :
            ?>
            <div class="df-listing-overview__row">
                <span class="df-listing-overview__label"><?= $data['label'] ?></span>
                <span class="df-listing-overview__price"><?= app()->normalizer()->currency($data['value']) ?></span>
            </div>
            <?php
        endforeach;
        ?>
        <div class="df-listing-overview__row df-listing-overview__row--footer">
            <span class="df-listing-overview__label">Prix total</span>
            <span class="df-listing-overview__price"><?= $form->getMeta('data-price') ?></span>
        </div>
    </div>
</fieldset>
