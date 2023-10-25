<?php

use function Globalis\WP\Cubi\include_template_part;

$form->viewPart('parts/form-header', ['action' => \Globalis\WP\Cubi\get_current_url()]);

$form->viewPart('fields/hidden', ['name' => 'date_start_after', 'error' => false]);
$form->viewPart('fields/hidden', ['name' => 'date_end_before', 'error' => false]);

$date_start_id = sprintf('%s-%s', $form->id(), 'date_sart');
$date_end_id = sprintf('%s-%s', $form->id(), 'date_end');

$checkouts = $form->getMeta('data-checkoutDates');
?>
<div class="rental-search-fields">
    <?php
    $form->viewPart(
        'fields/date',
        [
            'label' => 'Dates d\'arrivée',
            'required' => false,
            'name' => 'date_start',
            'id' => $date_start_id,
            'input' => [
                'placeholder' => 'Dates de séjour',
                'datalist' => $form->getMeta('data-checkinDates'),
                'refresh' => true
            ],
        ]
    );
    $form->viewPart(
        'fields/date',
        [
            'label' => 'Dates de départ',
            'required' => false,
            'name' => 'date_end',
            'id' => $date_end_id,
            'input' => [
                'placeholder' => 'Dates de séjour',
                'datalist' => $checkouts,
                'disabled' => empty($form->getValue('date_start')),
                'min' => !empty($checkouts) ? current($checkouts) : null
            ],
        ]
    );
    $form->viewPart(
        'fields/select',
        [
            'label' => 'Ville',
            'required' => false,
            'name' => 'city',
            'input' => [
                'placeholder' => 'Ville',
            ],
            'options' => $form->getMeta('datalist-cities')->all(),
        ]
    );
    $form->viewPart(
        'fields/select',
        [
            'label' => 'Nombre',
            'required' => false,
            'name' => 'capacity',
            'input' => [
                'placeholder' => 'Personnes',
            ],
            'options' => $form->getMeta('datalist-capacity')->all(),
        ]
    );
    $form->viewPart(
        'fields/select',
        [
            'label' => 'Type de bien',
            'required' => false,
            'name' => 'type',
            'input' => [
                'placeholder' => 'Type de bien',
            ],
            'options' => $form->getMeta('datalist-types')->all(),
        ]
    );
    ?>
    <div class="rental-search-fields-submit hidden--down-small">
        <button type="submit" class="btn btn--primary btn--submit">
            je cherche une location
        </button>
    </div>
</div>
<div class="rental-search-fields-more">
    <button
        type="button"
        class="btn btn--primary"
        aria-expanded="<?= $form->getMeta('has-secondary-fields') ? 'true' : 'false' ?>"
        data-disclosure
        aria-controls="expand-rental-search-form"
        <?= $form->getMeta('has-secondary-fields') ? '' : 'hidden' ?>
        >
        <span class="cssicon-more" aria-hidden="true"></span> Recherche avancée
    </button>
    <div class="rental-search-fields-more__fields" id="expand-rental-search-form">
        <?php
        $form->viewPart(
            'fields/text',
            [
                'label' => 'Référence',
                'required' => false,
                'name' => 'ref',
                'input' => [
                    'placeholder' => 'Recherche par réf.',
                ],
            ]
        );
        $form->viewPart(
            'fields/range',
            [
                'label' => 'Prix minimum',
                'required' => false,
                'name' => 'min_price',
                'input' => [
                    'step' => '100',
                    'min' => $form->getMeta('datalist-prices-ranges')->first()['value'] ?? null,
                    'max' => $form->getMeta('datalist-prices-ranges')[$form->getMeta('datalist-prices-ranges')->count() - 2]['value'] ?? null,
                    'datalist' => $form->getMeta('datalist-prices-ranges')->all()
                ],
                'output' => [
                    'suffix' => '<small>/sem</small>'
                ],
            ]
        );
        $form->viewPart(
            'fields/range',
            [
                'label' => 'Prix maximum',
                'required' => false,
                'name' => 'max_price',
                'input' => [
                    'step' => '100',
                    'min' => $form->getMeta('datalist-prices-ranges')[1]['value'] ?? null,
                    'max' => $form->getMeta('datalist-prices-ranges')->last()['value'] ?? null,
                    'datalist' => $form->getMeta('datalist-prices-ranges')->all()
                ],
                'output' => [
                    'suffix' => '<small>/sem</small>'
                ]
            ]
        );
        $form->viewPart(
            'fields/checkbox',
            [
                'label' => 'Équipements',
                'required' => false,
                'inline' => true,
                'name' => 'gears',
                'options' => $form->getMeta('datalist-gears')->all()
            ]
        );
        $form->viewPart(
            'fields/checkbox',
            [
                'label' => 'Aménagements',
                'required' => false,
                'inline' => true,
                'name' => 'attributes',
                'options' => $form->getMeta('datalist-attributes')->all()
            ]
        );
        ?>
        <div class="text-center hidden--down-small">
            <button type="submit" class="btn btn--secondary">
                je cherche une location
            </button>
        </div>
    </div>
</div>
<div class="rental-search-fields-submit hidden--up-medium">
    <button type="submit" class="btn btn--secondary btn--submit">
        je cherche une location
    </button>
</div>
<?php
$form->viewPart('parts/form-footer');

if ($form->submittedOk() && empty($errors) && wp_doing_ajax()) {
    $base_url = esc_attr(wp_get_original_referer() ?: wp_unslash($_SERVER['REQUEST_URI']));
    include_template_part('templates/listing/search/result', [
        'engine' => $form->getMeta('engine'),
        'base_url' => $base_url
    ]);
    include_template_part('templates/common/breadcrumb', [
        'breadcrumb' => app()->navigation()->partsFactory('listing-search', [$form->getMeta('engine')])->setCurrent()->breadcrumb()->map(function ($row) {
            return $row->toArray();
        })->all()
    ]);
    ?>
    <data
        data-search-url
        href="<?= add_query_arg($form->getMeta('engine')->filtersToArray(true), remove_query_arg(array_keys($form->getMeta('engine')::FILTERS), htmlspecialchars_decode($base_url, ENT_NOQUOTES))) ?>"
        title="Résultat de recherche"
        ></data>
    <?php
}