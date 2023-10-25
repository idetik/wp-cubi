<?php

$class = $class ?? 'field__checkbox';

?>
<div class="<?= $class ?>">
    <?php $form->viewPart('fields/components/input-checkbox', $input); ?>
    <label for="<?= $input['id'] ?>" class="field__checkbox-label"><?= $label ?></label>
</div>