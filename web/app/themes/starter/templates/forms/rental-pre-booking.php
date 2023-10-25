<?php

use Carbon\Carbon;

$form->viewPart('parts/form-header', ['action' => \Globalis\WP\Cubi\get_current_url()]);

$pickerId = sprintf('%s-%s', $form->id(), 'date_range');
$losPricing = $form->getMeta('data-losPricing');
$endDate = $form->getMeta('data-endDate');

if (empty($endDate) || empty($losPricing)) {
    return;
}

$endDate = $endDate->format('Y-m-d');
$startDate = $form->getMeta('data-startDate')->format('Y-m-d');
$price = $form->getMeta('data-price');

if ($form->submittedOk() && empty($errors)) {
    ?>
    <div class="rental-prebooking-fields">
    </div>
    <?php
} else {
    $form->viewPart('fields/hidden', ['name' => 'rental_id']);
    $form->viewPart('fields/hidden', ['name' => 'date_range_start', 'id' => $pickerId . '_start', 'error' => false]);
    $form->viewPart('fields/hidden', ['name' => 'date_range_end', 'id' => $pickerId . '_end', 'error' => false]);
    ?>
    <div class="rental-prebooking-fields grid--12 grid--has-gutter">
        <div class="grid__col--small-6 grid__col--6">
            <?php
            $form->viewPart(
                'fields/hotel-datepicker',
                [
                    'label' => 'Dates de séjour',
                    'label_hidden' => true,
                    'required' => true,
                    'refresh' => true,
                    'name' => 'date_range',
                    'id' => $pickerId,
                    'input' => [
                        'placeholder' => 'Dates de séjour',
                        'class' => 'field__input field-rental-datepicker',
                        'readonly' => true
                    ],
                    'datepicker' => [
                        'noCheckInDates' => $form->getMeta('data-disabledCheckinDates'),
                        'noCheckInDaysOfWeek' => [],
                        'startDate' => $startDate ?? null,
                        'endDate' => $endDate ?? false,
                        'disabledDates' => array_values($form->getMeta('data-bookedDates'))
                    ]
                ]
            );
            ?>
        </div>
        <div class="grid__col--small-4 grid__col--2">
            <?php
            $form->viewPart(
                'fields/select',
                [
                    'label' => 'Nombre de personnes',
                    'label_hidden' => true,
                    'required' => true,
                    'name' => 'capacity',
                    'input' => [
                        'placeholder' => 'Personnes',
                    ],
                    'options' => $form->getMeta('datalist-capacity'),
                ]
            );
            ?>
        </div>
        <div class="hidden--down-xsmall grid__col--2">
            <div class="field">
                <p class="field__input margin--0 text-center"><?= $price ?></p>
            </div>
        </div>
        <div class="grid__col--small-2 grid__col--2">
            <button type="submit" class="btn btn--primary btn--submit btn--block">
                <span class="hidden--xsmall">Réserver</span>
                <div class="hidden--up-small margin--0"><span class="visuallyhidden">Réserver pour le prix de <?= $price ?></span>&#10003;</div>
            </button>
        </div>
    </div>
    <?php
}
$form->viewPart('parts/form-footer');

?>
<script>
    const picker = window.hotelDatePickers.find((item) => item.id === "<?= $pickerId ?>");
    picker.options['losPricing'] = <?= json_encode($losPricing) ?>;
    window.addEventListener('load', () => {
        window.rentalPreBookingPreparePicker(picker);
    });
</script>
<?php
