<?php

namespace MPHB\Addons\MailChimp\Repositories;

class CategoriesRepository extends Repository
{
    /**
     * @param array $categories Array of [id, name] ("id" is remote ID).
     * @param int $listId List ID (not remote ID).
     *
     * @global \wpdb $wpdb
     */
    public function addCategories($categories, $listId)
    {
        global $wpdb;

        $values = array_map(function ($category) use ($listId) {
            $remoteId = esc_sql($category['id']);
            $name = esc_sql($category['name']);

            return "({$listId}, '{$remoteId}', '{$name}')";
        }, $categories);

        $query = "INSERT INTO {$this->categoriesTable} (list_id, remote_id, category_name) VALUES " . implode(', ', $values);
        $wpdb->query($query);
    }

    /**
     * @param int $listId
     * @return array [Category remote ID => Category ID]
     *
     * @global \wpdb $wpdb
     */
    public function findIdsByListId($listId)
    {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT category_id, remote_id FROM {$this->categoriesTable} WHERE list_id = %d ORDER BY category_id ASC",
                $listId
            ),
            ARRAY_A
        );

        $categories = wp_list_pluck($results, 'category_id', 'remote_id');
        $categories = array_map('absint', $categories);

        return $categories;
    }

    /**
     * @param int[] $ids Category IDs.
     *
     * @global \wpdb $wpdb
     */
    public function removeCategories($ids)
    {
        global $wpdb;

        $query = "DELETE FROM {$this->categoriesTable} WHERE category_id IN (" . implode(', ', $ids) . ")";
        $wpdb->query($query);
    }
}
