<?php

$class = $class ?? 'field__row';
$inline = $inline ?? false;

if ($inline) {
    $class .= ' field__row--inline';
}

?>
<div class="<?= $class ?>">
    <?php
    foreach ($options as $radio_item) :
        $form->viewPart('fields/components/radio-item', [
            'label' => $radio_item['label'],
            'class' => $radio_item['class'] ?? null,
            'input' => array_diff_key($radio_item, ['label', 'class'])
        ]);
    endforeach;
    ?>
</div>