<?php

namespace MPHB\Addons\MailChimp\Repositories;

class RoomsRepository
{
    /**
     * @param array $args Optional.
     * @return array [Room ID => Room title]
     *
     * @deprecated Hotel Booking v3.7.1
     */
    public function getIdTitleList($args = [])
    {
        $args = array_merge([
            'fields'      => 'all',
            'orderby'     => 'ID',
            'order'       => 'ASC',
            'post_status' => ['publish', 'pending', 'draft', 'future', 'private']
        ], $args);

        $posts = mphb()->getRoomPersistence()->getPosts($args);

        $list = array();

        foreach ($posts as $post) {
            $list[$post->ID] = $post->post_title;
        }

        return $list;
    }
}
