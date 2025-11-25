<?php

namespace MPHB\Notifier\Admin\MetaBoxes;

use MPHB\Admin\Fields\FieldFactory;

/**
 * @since 1.0
 */
class NoticesMetaBox extends CustomMetaBox
{
    /**
     * @return array
     */
    protected function generateFields()
    {
        // mphb_notifier()->postTypes()->notification()->getManagePage() is null
        // at this moment
        $notificationsUrl = add_query_arg(
            [
                'post_type' => mphb_notifier()->postTypes()->notification()->getPostType(),
            ],
            admin_url('edit.php')
        );

        return [
            'notices_help' => FieldFactory::create('_mphb_notification_notices_help', [
                'type'              => 'placeholder',
                'description'       => sprintf(
                    wp_kses_post(
                        __('Following information can be included in <a href="%s">Notifications</a> specifically for this accommodation type when it is booked.', 'mphb-notifier')
                    ),
                    $notificationsUrl
                ),
            ]),
            'notice_1'     => FieldFactory::create('mphb_notification_notice_1', [
                'type'              => 'textarea',
                // translators: "Notice 1", "Notice 2" etc.
                'label'             => sprintf(esc_html__('Notice %d', 'mphb-notifier'), 1),
                'rows'              => 2,
                'translatable'      => true
            ]),
            'notice_2'     => FieldFactory::create('mphb_notification_notice_2', [
                'type'              => 'textarea',
                // translators: "Notice 1", "Notice 2" etc.
                'label'             => sprintf(esc_html__('Notice %d', 'mphb-notifier'), 2),
                'rows'              => 2,
                'translatable'      => true
            ])
        ];
    }
}
