<?php

$options = $form->getMeta('datalist-options');
if (empty($options)) {
    return;
}

?>
<fieldset class="fieldset" id="fieldset-prestation">
    <legend class="legend">Prestations</legend>
    <?php
    foreach ($options as $option) :
        if ($option['service']->option_optionType === 1) {
            $settings = $option['settings'];
        } else {
            $settings = [
                'option_unitPrice' => $option['service']->unitPrice,
                'option_quantity' => $option['service']->quantity,
                'option_totalPrice' => $option['service']->totalPrice,
                'option_downPayment' => $option['service']->downPayment,
                'option_mandatory' => $option['service']->mandatory,
            ];
        }
        ?>
        <div class="grid--1 grid--has-gutter-3x field__row">
            <?php
            if (!empty($settings['option_quantity'])) {
                ?>
                <div class="df-listing-prestation df-listing-prestation--single">
                    <?php
                    $label = $option['service']->title();
                    $desc = $option['service']->description;
                    $form->viewPart(
                        'fields/checkbox',
                        [
                            'label' => $option['service']->title(),
                            'label_hidden' => true,
                            'refresh' => true,
                            'after_component_html' => !empty($desc) ? '<div class="field__instructions">' . $desc . '</div>' : null,
                            'name' => 'service[' . $option['service']->id() . ']',
                            'inline' => true,
                            'options' => [
                                [
                                    'label' => $label,
                                    'value' => '1',
                                    'data_atts' => ['aria-controls="sejour-price fieldset-prices"'],
                                    'auto_bracket' => false
                                ]
                            ],
                        ]
                    );
                    ?>
                    <div class="df-listing-prestation__price">
                        <?= app()->normalizer()->currency($settings['option_totalPrice'] ?: $settings['option_unitPrice']) ?>
                    </div>
                </div>
                <?php
            } else {
                $form->viewPart(
                    'fields/select',
                    [
                        'label' => $option['service']->title(),
                        'after_label_html' => $option['service']->description ? '<div class="field__instructions">' . $option['service']->description . '</div>' : null,
                        'name' => 'service[' . $option['service']->id() . ']',
                        'refresh' => true,
                        'input' => [
                            'placeholder' => 'Quantité',
                        ],
                        'options' => array_map(
                            function ($item) use ($settings) {
                                return [
                                    'label' => $item . ' : ' . app()->normalizer()->currency($item * (float)$settings['option_unitPrice']),
                                    'value' => $item,
                                    'data_atts' => ['aria-controls="sejour-price fieldset-prices"']
                                ];
                            },
                            range(1, $form->getMeta('data-occupant'))
                        ),
                    ]
                );
            }
            ?>
        </div>
        <?php
    endforeach;
    ?>
</fieldset>
