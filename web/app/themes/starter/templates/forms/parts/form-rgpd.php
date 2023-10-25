<?php

$rgpd = $form->getMeta('rgpd');

if (empty($rgpd)) {
    return;
}

?>
<div class="form-rgpd comment margin-top--3 margin-bottom--3 text-color--grey-dark">
    <?= html_entity_decode($rgpd) ?>
</div>
