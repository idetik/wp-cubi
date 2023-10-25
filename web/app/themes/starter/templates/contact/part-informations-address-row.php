<div class="contact-address">
    <div class="contact-address__row">
        <?php if (!empty($place['address'])) : ?>
            <i class="icon-map_outlined"></i>
            <a target="_blank" href="<?php printf('https://maps.google.com/maps?q=%s', urlencode(strip_tags($place['address']))) ?>"><?= $place['address'] ?></a>
        <?php endif; ?>
    </div>
    <div class="contact-address__row">
        <?php if (!empty($place['phone'])) : ?>
            <i class="icon-call"></i>
            <a href="tel:<?= $place['phone'] ?>"><?= $place['phone'] ?></a>
        <?php endif; ?>
    </div>
    <?php if ($place['use_time_slots'] ?? false) : ?>
        <div class="contact-address__row flex--align-items-center">
            <i class="icon-calendar_month"></i>
            <div>
                <?php
                if ('repeater' === $place['slot_type']) {
                    foreach ($model::formatSlots($place['time_slots']) as $slot) :
                        $hours = array_map(function ($hour) {
                            return sprintf('de %s à %s', $hour['start'], $hour['end']);
                        }, $slot['hours']);

                        if ('none' === $slot['to']) {
                            printf('<p class="margin--1">Le %s %s</p>', $slot['from'], implode(' ' . __('et', 'themetik') . ' ', $hours));
                        } else {
                            printf('<p class="margin--1">Du %s au %s %s</p>', $slot['from'], $slot['to'], implode(' ' . __('et', 'themetik') . ' ', $hours));
                        }
                    endforeach;
                } else {
                    echo $place['slot_text'];
                }
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>
