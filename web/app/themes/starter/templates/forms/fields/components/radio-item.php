<?php

$class = $class ?? 'field__radio';

?>
<div class="<?= $class ?>">
    <?php
    $form->viewPart('fields/components/input-radio', $input);
    ?>
    <label for="<?= $input['id'] ?>" class="field__radio-label">
        <?= $label ?>
    </label>
</div>
