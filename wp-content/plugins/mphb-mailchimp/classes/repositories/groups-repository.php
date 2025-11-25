<?php

namespace MPHB\Addons\MailChimp\Repositories;

class GroupsRepository extends Repository
{
    /**
     * @param array $groups Array of [id, name] ("id" is remote ID).
     * @param int $categoryId Category ID (not remote ID).
     *
     * @global \wpdb $wpdb
     */
    public function addGroups($groups, $categoryId)
    {
        global $wpdb;

        $values = array_map(function ($group) use ($categoryId) {
            $remoteId = esc_sql($group['id']);
            $name = esc_sql($group['name']);

            return "({$categoryId}, '{$remoteId}', '{$name}')";
        }, $groups);

        $query = "INSERT INTO {$this->groupsTable} (category_id, remote_id, group_name) VALUES " . implode(', ', $values);
        $wpdb->query($query);
    }

    /**
     * @param int[] $ids Group IDs.
     *
     * @global \wpdb $wpdb
     */
    public function removeGroups($ids)
    {
        global $wpdb;

        $query = "DELETE FROM {$this->groupsTable} WHERE group_id IN (" . implode(', ', $ids) . ")";
        $wpdb->query($query);
    }
}
