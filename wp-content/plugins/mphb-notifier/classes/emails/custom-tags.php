<?php

namespace MPHB\Notifier\Emails;

use MPHB\Entities\Booking;
use MPHB\Entities\ReservedRoom;

/**
 * @since 1.0
 */
class CustomTags
{
    protected $tagGroups = [];

    public function __construct()
    {
        $this->setupTags();
        $this->registerTags();
    }

    protected function setupTags()
    {
        $this->tagGroups = [
            // Its "booking" tags, but we need to push them to the top of the list
            'global' => [
                [
                    'name'        => 'accommodation_notice_1',
                    // translators: "Notification Notice 1", "Notification Notice 2" etc.
                    'description' => sprintf(esc_html__('Accommodation Notice %d', 'mphb-notifier'), 1)
                ],
                [
                    'name'        => 'accommodation_notice_2',
                    // translators: "Notification Notice 1", "Notification Notice 2" etc.
                    'description' => sprintf(esc_html__('Accommodation Notice %d', 'mphb-notifier'), 2)
                ]
            ]
        ];
    }

    protected function registerTags()
    {
        // Must be called at least on "plugins_loaded" with priority 9 to work properly.
        // Otherwise it will be too late to add filters "mphb_email_{$groupName}_tags"
        foreach (array_keys($this->tagGroups) as $groupName) {
            add_filter("mphb_email_{$groupName}_tags", [$this, 'addTags']);
        }

        if (!empty($this->tagGroups)) {
            add_filter('mphb_email_reserved_room_tags', [$this, 'addReservedRoomTemplateTags']);

            add_filter('mphb_email_replace_tag', [$this, 'replaceTag'], 10, 3);
            add_filter('mphb_email_reserved_room_replace_tag', [$this, 'replaceReservedRoomTemplateTag'], 10, 5);
        }
    }

    /**
     * Callback for filter "mphb_email_{$groupName}_tags".
     *
     * @param array $tags
     * @return array
     */
    public function addTags($tags)
    {
        $filter = current_filter();
        $group = preg_replace('/mphb_email_(\w+)_tags/i', '$1', $filter);

        return $this->addTagsToGroup($tags, $group);
    }

    protected function addTagsToGroup($tags, $group)
    {
        if (array_key_exists($group, $this->tagGroups)) {
            $tags = array_merge($this->tagGroups[$group], $tags);
        }

        return $tags;
    }

    /**
     * @since 1.3.3
     *
     * @param array $tags [
     *     [
     *         name        => string,
     *         description => string
     *     ],
     *     ...
     * ]
     */
    public function addReservedRoomTemplateTags($tags)
    {
        $tags = array_merge($tags, $this->tagGroups['global']);

        return $tags;
    }

    /**
     * Callback for filter "mphb_email_replace_tag".
     *
     * @param string $replaceText
     * @param string $tag
     * @param Booking|null $booking
     * @return string
     */
    public function replaceTag($replaceText, $tag, $booking)
    {
        switch ($tag) {
            case 'accommodation_notice_1':
            case 'accommodation_notice_2':
                if (!is_null($booking)) {
                    $noticeNo = (int)substr($tag, -1);

                    // Get notice for each booked room
                    $notices = array_map(
                        function ($reservedRoom) use ($noticeNo, $booking) {
                            return $this->getNoticeForReservedRoom($reservedRoom, $noticeNo, $booking->getLanguage());
                        },

                        $booking->getReservedRooms()
                    );

                    $notices = array_filter($notices);
                    $notices = array_unique($notices);

                    $delimeter = apply_filters('mphb_notification_notices_delimeter', '<br />');

                    $replaceText = implode($delimeter, $notices);
                }
                break;
        }

        return $replaceText;
    }

    /**
     * @since 1.3.3
     *
     * @param string $replaceText
     * @param string $tag
     * @param ReservedRoom $reservedRoom
     * @param int $reservedRoomNo
     * @param Booking $booking
     * @return string
     */
    public function replaceReservedRoomTemplateTag($replaceText, $tag, $reservedRoom, $reservedRoomNo, $booking)
    {
        switch ($tag) {
            case 'accommodation_notice_1':
            case 'accommodation_notice_2':
                $noticeNo = (int)substr($tag, -1);

                $replaceText = $this->getNoticeForReservedRoom($reservedRoom, $noticeNo, $booking->getLanguage());
                break;
        }

        return $replaceText;
    }

    /**
     * @since 1.3.3
     *
     * @param ReservedRoom $reservedRoom
     * @param int $noticeNo
     * @param string $language
     */
    private function getNoticeForReservedRoom($reservedRoom, $noticeNo, $language)
    {
        $roomTypeId = apply_filters(
            'wpml_object_id',
            $reservedRoom->getRoomTypeId(),
            MPHB()->postTypes()->roomType()->getPostType(),
            true,
            $language
        );

        $noticeMetaField = "mphb_notification_notice_{$noticeNo}";

        $notice = get_post_meta($roomTypeId, $noticeMetaField, true);
        $notice = nl2br($notice);

        return $notice;
    }
}
