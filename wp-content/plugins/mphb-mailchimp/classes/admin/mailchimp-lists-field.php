<?php

namespace MPHB\Addons\MailChimp\Admin;

use MPHB\Admin\Fields\InputField;

class MailchimpListsField extends InputField
{
    const TYPE = 'mailchimp-lists';

    protected $default = []; // Replace parent's default value

    /**
     * @var array [List remote ID => [name, categories => [Category remote ID =>
     *     [name, groups => [Group remote ID => Group name]]]]]
     */
    protected $lists = []; // Lists to show

    protected $topDescription = '';

    public function __construct($name, $args, $value = '')
    {
        // Fix "default" and "value"
        if (!is_array($args['default'])) {
            $args['default'] = $this->default;
        }

        if (!is_array($value)) {
            $value = $this->default;
        }

        parent::__construct($name, $args, $value);

        $this->lists = isset($args['lists']) ? $args['lists'] : $this->lists;

        // Move description to the top where it is easier to see
        $this->topDescription = $this->description;
        $this->description = '';
    }

    public function sanitize($value)
    {
        if (!is_array($value)) {
            return [];
        } else {
            array_walk($value, function (&$groups) {
                if ($groups === 'true') {
                    $groups = [];
                }
            });

            return $value;
        }
    }

    protected function renderInput()
    {
        $output = '';

        // If nothing checked then post the empty value
        $output .= '<input type="hidden" name="' . esc_attr($this->name) . '" value="" type="hidden" />';

        if (!empty($this->topDescription)) {
            $output .= '<p class="top-description">' . $this->topDescription . '</p>';
        }

        $output .= '<div class="mphb-mailchimp-lists-sections">';
            $output .= $this->renderLists();
        $output .= '</div>';

        return $output;
    }

    protected function renderLists()
    {
        if (empty($this->lists)) {
            return '';
        }

        $output = '';

        foreach ($this->lists as $listRemoteId => $list)
        {
            $inputName = $this->name . '[' . $listRemoteId . ']';
            $isChecked = array_key_exists($listRemoteId, $this->value);

            $output .= '<div class="mphb-mailchimp-lists-section">';
                $output .= '<h3 class="mphb-mailchimp-lists-section-title">';
                    $output .= '<label>';
                        $output .= '<input name="' . esc_attr($inputName) . '" value="true" type="checkbox" ' . checked(true, $isChecked, false) . ' />';
                        $output .= ' ' . esc_html($list['name']);
                    $output .= '</label>';
                $output .= '</h3>';

                $output .= '<hr />';

                if (!empty($list['categories'])) {
                    $output .= $this->renderCategories($list['categories'], $listRemoteId);
                } else {
                    // translators: %s - list name like "My Product"
                    $output .= '<p class="mphb-mailchimp-lists-no-categories">' . sprintf(esc_html__('There are no interests in the list %s.', 'mphb-mailchimp'), $list['name']) . '</p>';
                }

            $output .= '</div>';
        }

        return $output;
    }

    protected function renderCategories($categories, $listRemoteId)
    {
        $output = '';

        foreach ($categories as $category) {
            $output .= '<p>' . esc_html($category['name']) . '</p>';

            if (!empty($category['groups'])) {
                $output .= $this->renderGroups($category['groups'], $listRemoteId);
            }
        }

        return $output;
    }

    protected function renderGroups($groups, $listRemoteId)
    {
        $inputName = $this->name . '[' . $listRemoteId . '][]';

        $output = '<ul class="mphb-mailchimp-lists-groups-of-interest">';

            foreach ($groups as $groupRemoteId => $groupName) {
                $isChecked = array_key_exists($listRemoteId, $this->value)
                    && in_array($groupRemoteId, $this->value[$listRemoteId]);

                $output .= '<li>';
                    $output .= '<label>';
                        $output .= '<input name="' . esc_attr($inputName) . '" value="' . esc_attr($groupRemoteId) . '" type="checkbox" ' . checked(true, $isChecked, false) . ' />';
                        $output .= ' ' . $groupName;
                    $output .= '</label>';
                $output .= '</li>';
            }

        $output .= '</ul>';

        return $output;
    }

    public function getInnerLabelTag()
    {
        // Don't show the inner label after the field body. We already displayed
        // it between tabs and lists
        return '';
    }
}
