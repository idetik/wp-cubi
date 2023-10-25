<?php

namespace MyNamespace\Models\Traits;

trait Recipients
{
    public function initializePageBuilder()
    {
        $this->declareMetas(['recipients_users', 'recipients_others']);
    }

    public function getRecipientsUsersAttribute()
    {
        return $this->getField('recipients_users');
    }

    public function getRecipientsOthersAttribute()
    {
        return $this->getField('recipients_others');
    }

    public function recipientsEmails()
    {
        $emails = [];
        if (!empty($this->getRecipientsUsersAttribute())) {
            foreach ($this->recipients_users as $user_id) {
                $emails[] = app()->schema('users')->model($user_id)->user_email;
            }
        }
        if (!empty($this->getRecipientsOthersAttribute())) {
            foreach ($this->recipients_others as $email) {
                $emails[] = $email['email'];
            }
        }
        return $emails;
    }
}
