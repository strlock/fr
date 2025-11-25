<?php

namespace MPHB\Addons\MailChimp\Entities;

class MailchimpProduct
{
    /** @var string */
    public $id = '0';

    /** @var int */
    public $roomTypeId = 0;

    /** @var \MPHB\Entities\RoomType */
    public $roomType = null;

    /**
     * @param int $roomTypeId
     * @param \MPHB\Entities\RoomType $roomType Optional.
     */
    public function __construct($roomTypeId, $roomType = null)
    {
        $this->id = (string)$roomTypeId;
        $this->roomTypeId = $roomTypeId;
        $this->roomType = $roomType;
    }

    /**
     * @return array
     *
     * @throws \Exception If can't find the room type.
     */
    public function toArray()
    {
        $roomType = $this->getRoomType();

        if (is_null($roomType)) {
            throw new \Exception(sprintf(esc_html__('Can\'t find the room type #%d to create MailChimp product.', 'mphb-mailchimp'), $this->roomTypeId));
        }

        $roomLink  = $roomType->getLink();
        $roomPrice = $roomType->getDefaultPrice();
        $rooms     = mphbmc()->repository()->rooms()->getIdTitleList();
        $post      = get_post($this->roomTypeId);

        $productInfo = [
            'id'          => $this->id,
            'title'       => $post->post_title,
            'handle'      => $post->post_name,
            'url'         => $roomLink,
            'description' => $post->post_excerpt,
            'type'        => mphb()->postTypes()->roomType()->getPostType(),
            'vendor'      => get_bloginfo('name'),
            'variants'    => [],
            'published_at_foreign' => get_post_time('c', true, $this->roomTypeId, true)
        ];

        // Generate variants
        foreach ($rooms as $roomId => $roomTitle) {
            $productInfo['variants'][] = [
                'id'    => (string)$roomId,
                'title' => $roomTitle,
                'url'   => $roomLink,
                'price' => $roomPrice
            ];
        }

        // Add image if set
        if ($roomType->hasFeaturedImage()) {
            $productInfo['image_url'] = wp_get_attachment_url($roomType->getFeaturedImageId());
        }

        return $productInfo;
    }

    public function getRoomType()
    {
        if (is_null($this->roomType)) {
            $this->roomType = mphb()->getRoomTypeRepository()->findById($this->roomTypeId);
        }

        return $this->roomType;
    }
}
