<?php

namespace MyNamespace\Models;

class Contact extends PostModel
{
    use Traits\Recipients;

    protected function initializeModel(): void
    {
        $this->declareMetas([
            'contact_form_success',
            'contact_form_instructions',
            'use_subjects' => 'contact_use_subjects',
            'subjects' => 'contact_subjects',
        ]);
    }

    public function formInstructions()
    {
        return $this->getField('contact_form_instructions');
    }

    public function formSuccess()
    {
        return $this->getField('contact_form_success');
    }

    public function contactPlaces()
    {
        return $this->getField('contact_places');
    }

    public static function translateDay(string $month): string
    {
        switch ($month) {
            case 'monday':
                return __('lundi', 'themetik');
            case 'tuesday':
                return __('mardi', 'themetik');
            case 'wednesday':
                return __('mercredi', 'themetik');
            case 'thursday':
                return __('jeudi', 'themetik');
            case 'friday':
                return __('vendredi', 'themetik');
            case 'saturday':
                return __('samedi', 'themetik');
            case 'sunday':
                return __('dimanche', 'themetik');
            default:
                return $month;
        }
    }

    public static function formatSlots($slots)
    {
        return \array_map(function ($slot) {
            return [
                'from' => static::translateDay($slot['from']),
                'to' => static::translateDay($slot['to']),
                'hours' => $slot['hours'],
            ];
        }, $slots);
    }

    public function getSubjectsAttribute()
    {
        if (empty($this->get('subjects'))) {
            return [];
        }

        return \array_map(function ($subject) {
            $subject['value'] = \sanitize_title($subject['title']);
            if ($subject['use_recipients']) {
                $recipients = $subject['recipients'];
                $emails = [];
                if (!empty($recipients['recipients_users'])) {
                    foreach ($recipients['recipients_users'] as $user_id) {
                        $emails[] = app()->schema('users')->model($user_id)->user_email;
                    }
                }
                if (!empty($recipients['recipients_others'])) {
                    foreach ($recipients['recipients_others'] as $email) {
                        $emails[] = $email['email'];
                    }
                }

                $subject['recipients'] = $emails;
            } else {
                $subject['recipients'] = [];
            }
            return $subject;
        }, $this->get('subjects'));
    }

    public function get(string $prop)
    {
        if ($this->hasMeta($prop)) {
            return $this->getField($this->getMetaKeyFromLocalKey($prop));
        }
        return parent::get($prop);
    }
}
