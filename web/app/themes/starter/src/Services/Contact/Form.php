<?php

namespace MyNamespace\Services\Contact;

use Coretik\Services\Forms\AsyncForm as FormParent;
use Coretik\Services\Forms\Exception;
use Coretik\Services\Forms\Core\Utils;

class Form extends FormParent
{
    public function __construct()
    {
        parent::__construct('contact');
    }

    public function getRules(): array
    {
        return [
            'lastname' => [
                'name' => __('Nom', 'themetik'),
                'constraints' => [
                    'required' => true,
                    'max-size' => 150,
                ]
            ],
            'firstname' => [
                'name' => __('Prénom', 'themetik'),
                'constraints' => [
                    'required' => true,
                    'max-size' => 150,
                ]
            ],
            'email' => [
                'name' => __('Email', 'themetik'),
                'constraints' => [
                    'required-if-one-not-set' => ['phone'],
                    'email' => true,
                    'max-size' => 150,
                ]
            ],
            'phone' => [
                'name' => __('Téléphone', 'themetik'),
                'constraints' => [
                    'required-if-one-not-set' => ['email'],
                    'max-size' => 12,
                    'phone' => true,
                ]
            ],
            'subject' => [
                'name' => __('Objet', 'themetik'),
                'constraints' => [
                    'callback' => [
                        'name' => 'required-late',
                        'message' => 'Le sujet est obligatoire',
                        'callback' => fn ($form, $args, $value) => $this->getMeta('model')?->use_subjects ? Utils::issetValue($value) : true,
                    ],
                    'choice-late' => [
                        'provider' => fn () => $this->getMeta('model')?->use_subjects ? \array_flip(\array_column($this->getMeta('model')->subjects, 'value')) : []
                    ]
                ]
            ],
            'message' => [
                'name' => __('Message', 'themetik'),
                'constraints' => [
                    'required' => true,
                ]
            ],
        ];
    }

    protected function isValidContext(): bool
    {
        if (\wp_doing_ajax()) {
            return !empty($_REQUEST['_wp_original_http_referer'])
                && \url_to_postid($_REQUEST['_wp_original_http_referer']) === app()->map()->ids()->get('contact');
        } else {
            return app()->map()->is('contact');
        }
    }

    protected function initialize()
    {
        $contact = app()->map()->ids()->get('contact');
        if ($contact) {
            $this->addMeta('model', app()->schema('page')->model($contact));
        }
    }

    protected function run(): void
    {
        $form_values = $this->getValues();

        $email = app()->email();
        if (!empty($form_values['email'])) {
            $email->addHeader(sprintf('Reply-To: %s', $form_values['email']));
        }

        $values = [];
        foreach ($form_values as $field_name => $value) {
            $values[$field_name] = ['label' => $this->humanize($field_name), 'value' => $value];
        }

        $form_data_array = [];
        $recipients = [];

        if (!empty($values['subject'])) {
            $modelSubjects = $this->getMeta('model')->subjects;
            foreach ($modelSubjects as $modelSubject) {
                if ($modelSubject['value'] === $values['subject']['value']) {
                    $subject = $modelSubject;
                    $form_data_array[] = '<b>Objet :</b> ' . $modelSubject['title'];
                    if ($modelSubject['use_recipients']) {
                        $recipients = \array_merge($recipients, $modelSubject['recipients']);
                    }
                    break;
                }
            }
        }
        if (!empty($values['firstname']['value'])) {
            $form_data_array[] = '<b>Prénom :</b> ' . $values['firstname']['value'];
        }
        if (!empty($values['lastname']['value'])) {
            $form_data_array[] = '<b>Nom :</b> ' . $values['lastname']['value'];
        }
        if (!empty($values['email']['value'])) {
            $form_data_array[] = '<b>Email :</b> ' . $values['email']['value'];
        }
        if (!empty($values['phone']['value'])) {
            $form_data_array[] = '<b>Téléphone :</b> ' . $values['phone']['value'];
        }
        if (!empty($values['message']['value'])) {
            $form_data_array[] = '<b>Message :</b> ' . $values['message']['value'];
        }

        $form_data = implode('<br />', $form_data_array);

        $recipients = array_merge($recipients, $this->getMeta('model')->recipientsEmails());
        if (empty($recipients)) {
            throw new Exception('Une erreur est survenue, veuillez réessayer ultérieurement.');
        }

        $recipients = \array_unique($recipients);

        $subject_prefix = sprintf('[%s] ', \get_bloginfo('name'));
        $subject = \str_replace([
            '%%firstname%%',
            '%%lastname%%',
            '%%email%%',
            '%%phone%%',
            '%%subject%%',
        ], [
            $values['firstname']['value'],
            $values['lastname']['value'],
            $values['email']['value'],
            $values['phone']['value'],
            !empty($subject) ? ' : "' . $subject['title'] . '"' : '',
        ], 'Nouveau message de %%firstname%% %%lastname%% %%subject%%');

        $email
            ->setTo($recipients)
            ->setSubject($subject_prefix . $subject)
            ->useTemplate('contact/contact', ['form_data' => $form_data])
            ->send();
    }
};
