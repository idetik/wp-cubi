<?php

$class = $class ?? 'field__row';
$class .= ($inline ?? false) ? ' field__row--inline' : '';
?>
<div class="<?= $class ?>">
    <?php
    foreach ($options as $checkbox_item) :
        $form->viewPart('fields/components/checkbox-item', [
            'label' => $checkbox_item['label'],
            'class' => $checkbox_item['class'] ?? null,
            'input' => array_diff_key($checkbox_item, ['label', 'class'])
        ]);
    endforeach;
    ?>
</div>
