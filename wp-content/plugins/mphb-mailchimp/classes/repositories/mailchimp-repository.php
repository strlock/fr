<?php

namespace MPHB\Addons\MailChimp\Repositories;

class MailchimpRepository extends Repository
{
    /**
     * @return array [List remote ID => [name, categories => [Category remote ID =>
     *     [name, groups => [Group remote ID => Group name]]]]]
     *
     * @global \wpdb $wpdb
     */
    public function getAvailableInterests()
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT mc_lists.list_name, mc_lists.remote_id AS list_id,"
                . " mc_categories.category_name, mc_categories.remote_id AS category_id,"
                . " mc_groups.group_name, mc_groups.remote_id AS group_id"
                . " FROM {$this->listsTable} AS mc_lists"
                . " LEFT JOIN {$this->categoriesTable} AS mc_categories ON mc_categories.list_id = mc_lists.list_id"
                // Just "groups" will trigger SQL error
                . " LEFT JOIN {$this->groupsTable} AS mc_groups ON mc_groups.category_id = mc_categories.category_id"
                . " WHERE mc_lists.sync_status = %s"
                . " ORDER BY mc_lists.list_id ASC, mc_categories.category_id ASC, mc_groups.group_id ASC",
            self::STATUS_SYNCED
        );

        $results = $wpdb->get_results($query, ARRAY_A);
        $lists = [];

        foreach ($results as $row) {
            // Only remote IDs
            $listId     = $row['list_id'];
            $categoryId = $row['category_id'];
            $groupId    = $row['group_id'];

            if (!isset($lists[$listId])) {
                $lists[$listId] = [
                    'name'       => $row['list_name'],
                    'categories' => []
                ];
            }

            if (!is_null($categoryId) && !isset($lists[$listId]['categories'][$categoryId])) {
                $lists[$listId]['categories'][$categoryId] = [
                    'name'   => $row['category_name'],
                    'groups' => []
                ];
            }

            if (!is_null($groupId)) {
                $lists[$listId]['categories'][$categoryId]['groups'][$groupId] = $row['group_name'];
            }
        }

        return $lists;
    }

    /**
     * @param string $listRemoteId
     * @return array [Group remote IDs]
     *
     * @global \wpdb $wpdb
     */
    public function findInterestsByListRemoteId($listRemoteId)
    {
        global $wpdb;

        $remoteIds = $wpdb->get_col($wpdb->prepare(
            "SELECT mc_groups.remote_id"
                . " FROM {$this->listsTable} AS mc_lists"
                . " INNER JOIN {$this->categoriesTable} AS mc_categories ON mc_categories.list_id = mc_lists.list_id"
                . " INNER JOIN {$this->groupsTable} AS mc_groups ON mc_groups.category_id = mc_categories.category_id"
                . " WHERE mc_lists.remote_id = %s"
                . " ORDER BY mc_groups.group_id ASC",
            $listRemoteId
        ));

        return $remoteIds;
    }

    /**
     * @param string $syncStatus Optional. Synced or pending status. Synced by default.
     * @return array [lists => [List IDs], categories => [...], groups => [...]]
     *
     * @global \wpdb $wpdb
     */
    public function collectIds($syncStatus = 'synced')
    {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT mc_lists.list_id, mc_categories.category_id, mc_groups.group_id"
                    . " FROM {$this->listsTable} AS mc_lists"
                    . " LEFT JOIN {$this->categoriesTable} AS mc_categories ON mc_categories.list_id = mc_lists.list_id"
                    . " LEFT JOIN {$this->groupsTable} AS mc_groups ON mc_groups.category_id = mc_categories.category_id"
                    . " WHERE mc_lists.sync_status = %s"
                    . " ORDER BY mc_lists.list_id ASC, mc_categories.category_id ASC, mc_groups.group_id ASC",
                $syncStatus
            ),
            ARRAY_A
        );

        $ids = ['lists' => [], 'categories' => [], 'groups' => []];

        foreach ($results as $row) {
            $ids['lists'][] = absint($row['list_id']);

            if (!is_null($row['category_id'])) {
                $ids['categories'][] = absint($row['category_id']);
            }

            if (!is_null($row['group_id'])) {
                $ids['groups'][] = absint($row['group_id']);
            }
        }

        $ids['lists'] = array_values(array_unique($ids['lists']));
        $ids['categories'] = array_values(array_unique($ids['categories']));
        $ids['groups'] = array_values(array_unique($ids['groups']));

        return $ids;
    }
}
