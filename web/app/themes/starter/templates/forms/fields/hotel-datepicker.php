<?php

$input_args_default = [
    'required' => $required ?? false,
    'name' => $name,
    'id' => $id ?? sprintf('%s-%s', $form->id(), $name),
    'autocomplete' => 'off'
];
$input_args = array_merge($input_args_default, $input ?? []);

$component = [
    'template' => 'fields/components/input-text',
    'args' => $input_args
];

include locate_template('templates/forms/fields/field.php');


$id = $input_args['id'];
$refresh = $refresh ?? false;
$options = json_encode($datepicker ?? []);

?>
<script>
    window.hotelDatePickers = window.hotelDatePickers || [];
    window.hotelDatePickers.push({
        id: "<?= $id ?>",
        instance: null,
        options: <?= $options ?>,
        mount: (options = {}) => mountDatepicker("<?= $id ?>", <?= $refresh ? 'true' : 'false' ?>, options)
    });
</script>
<?php
