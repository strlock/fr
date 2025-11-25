<?php

namespace MPHB\Addons\MailChimp\Admin;

use MPHB\Admin\Fields\SelectField;

class TiedMailchimpListField extends SelectField
{
    const TYPE = 'tied-mailchimp-list';

    /**
     * @return bool
     */
    public function isTied()
    {
        return $this->value !== $this->default;
    }

    protected function renderInput()
    {
        if ($this->isTied()) {
            $listRemoteId = esc_sql($this->value);

            $listName = mphbmc()->repository()->lists()->getList([
                'fields' => 'names',
                'where'  => sprintf("remote_id = '%s'", $listRemoteId)
            ], $listRemoteId);

            // translators: %s - list name. Example: "Tied to list/audience My Product".
            return '<p>' . sprintf(esc_html__('Tied to list/audience %s', 'mphb-mailchimp'), '<code>' . $listName . '</code>') . '</p>';
        } else {
            return parent::renderInput();
        }
    }
}
