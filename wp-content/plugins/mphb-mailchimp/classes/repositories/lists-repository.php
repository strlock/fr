<?php

namespace MPHB\Addons\MailChimp\Repositories;

class ListsRepository extends Repository
{
    /**
     * @param array $lists Array of [id, name] ("id" is remote ID).
     * @param string $syncStatus Optional. Synced or pending status. Synced by default.
     *
     * @global \wpdb $wpdb
     */
    public function addLists($lists, $syncStatus = self::STATUS_SYNCED)
    {
        global $wpdb;

        $values = array_map(function ($list) use ($syncStatus) {
            $remoteId = esc_sql($list['id']);
            $name = esc_sql($list['name']);

            return "('{$remoteId}', '{$name}', '{$syncStatus}')";
        }, $lists);

        $query = "INSERT INTO {$this->listsTable} (remote_id, list_name, sync_status) VALUES " . implode(', ', $values);
        $wpdb->query($query);
    }

    /**
     * @param array $args Optional.
     * @param string|array $args['fields'] "all"|"ids"|"remote_ids"|"names"|column
     *     name (string)|column names (array). "all" by default.
     * @param string $args['sync_status'] synced|pending|any. "synced" by defaul.
     * @param string[] $args['where']
     * @param mixed $default Optional. FALSE by default.
     * @return mixed ID (integer), column value (string) or row values (array).
     *     FALSE if failed to find anything.
     *
     */
    public function getList($args = [], $default = false)
    {
        $args['limit'] = 1;

        $lists = $this->getLists($args);

        if (!empty($lists)) {
            $list = reset($lists);
        } else {
            $list = $default;
        }

        return $list;
    }

    /**
     * @param array $args Optional.
     * @param string|array $args['fields'] "all"|"ids"|"remote_ids"|"names"|column
     *     name (string)|column names (array). "all" by default.
     * @param string $args['sync_status'] synced|pending|any. "synced" by defaul.
     * @param string|array $args['where'] Single or multiple conditions.
     * @param int $args['limit']
     * @return array For "ids" and "names" the keys will be remote IDs, for
     *     "remote_ids" - IDs.
     *
     * @global \wpdb $wpdb
     */
    public function getLists($args = [])
    {
        global $wpdb;

        $args = array_merge([
            'fields'      => 'all', // all|ids|remote_ids|names|column name|column names
            'sync_status' => 'synced', // synced|pending|any
            'where'       => '',
            'limit'       => -1
        ], $args);

        if (is_array($args['fields'])) {
            $fields = $args['fields'];
        } else {
            switch ($args['fields']) {
                case 'all':
                    $fields = ['list_id AS id', 'remote_id', 'list_name AS name', 'sync_status']; break;
                case 'ids':
                    $fields = ['list_id AS id', 'remote_id']; break; // Use remote_id as key in the result array
                case 'remote_ids':
                    $fields = ['list_id AS id', 'remote_id']; break; // Use id as key in the result array
                case 'names':
                    $fields = ['remote_id', 'list_name AS name']; break; // Use remote_id as key in the result array
                default:
                    $fields = [$args['fields']]; break; // Select exact field ("list_id" will not transform into "id")
            }
        }

        $query = "SELECT " . implode(', ', $fields) . " FROM {$this->listsTable} WHERE 1=1";

        // Filter by synchronization status
        if ($args['sync_status'] != self::STATUS_ANY) {
            $query .= $wpdb->prepare(' AND sync_status = %s', $args['sync_status']);
        }

        // Additional WHERE conditions
        if (!empty($args['where'])) {
            if (is_array($args['where'])) {
                $query .= ' AND ' . implode(' AND ', $args['where']);
            } else {
                $query .= ' AND ' . $args['where'];
            }
        }

        // Limit the results amount
        if ($args['limit'] > 0) {
            $query .= " LIMIT 0, {$args['limit']}";
        }

        $results = $wpdb->get_results($query, ARRAY_A);

        // Convert numeric fields into integers
        if (in_array('list_id AS id', $fields)) {
            mphb_mc_array_absint($results, 'id');
        } else if (in_array('list_id', $fields)) {
            mphb_mc_array_absint($results, 'list_id');
        }

        if (is_array($args['fields'])) {
            $lists = $results;
        } else {
            switch ($args['fields']) {
                case 'ids':
                    $lists = wp_list_pluck($results, 'id', 'remote_id'); break;
                case 'remote_ids':
                    $lists = wp_list_pluck($results, 'remote_id', 'id'); break;
                case 'names':
                    $lists = wp_list_pluck($results, 'name', 'remote_id'); break;
                default:
                    $lists = $results;
            }
        }

        return $lists;
    }

    /**
     * @param int[] $ids List IDs.
     *
     * @global \wpdb $wpdb
     */
    public function removeLists($ids)
    {
        global $wpdb;

        $query = "DELETE FROM {$this->listsTable} WHERE list_id IN (" . implode(', ', $ids) . ")";
        $wpdb->query($query);
    }

    public function approvePendings()
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "UPDATE {$this->listsTable} SET sync_status = %s WHERE sync_status = %s",
            self::STATUS_SYNCED,
            self::STATUS_PENDING
        );

        $wpdb->query($query);
    }
}
