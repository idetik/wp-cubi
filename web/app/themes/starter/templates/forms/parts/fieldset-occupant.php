<fieldset class="fieldset fieldset--capacity" id="fieldset-capacity">
    <legend class="legend">Nombre de personnes</legend>
    <div class="df-listing-sejour__capacity">
        <span class="tag"><?= $form->getMeta('data-occupant') ?> personnes</span> dont
        <div class="df-listing-sejour__children">
            <?php
            $form->viewPart(
                'fields/select',
                [
                    'label' => 'Nombre d\'enfants',
                    'label_hidden' => true,
                    'required' => true,
                    'name' => 'children',
                    'options' => array_map(
                        function ($item) {
                            return [
                                'label' => $item,
                                'value' => $item,
                            ];
                        },
                        range(0, ($form->getMeta('data-occupant') - 1))
                    ),
                ]
            );
            ?>
            enfant(s)
        </div>
    </div>
</fieldset>
