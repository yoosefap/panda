<?php

/**
 *      ****  *  *     *  ****  ****  *    *
 *      *  *  *  * *   *  *  *  *  *   *  *
 *      ****  *  *  *  *  *  *  *  *    *
 *      *     *  *   * *  *  *  *  *   *  *
 *      *     *  *    **  ****  ****  *    *
 * @author   Pinoox
 * @link https://www.pinoox.com/
 * @license  https://opensource.org/licenses/MIT MIT License
 */

namespace pinoox\app\com_pinoox_panda\model;

class GroupModel extends PandaDatabase
{
    public static function fetch_by_key($group_key, $old_group_key = null)
    {
        if ($group_key == $old_group_key)
            return false;

        self::$db->where('group_key', $group_key);
        return self::$db->getOne(self::group);
    }

    public static function insert($form)
    {
        return self::$db->insert(self::group, [
            'group_key' => $form['group_key'],
            'group_name' => $form['group_name'],
        ]);
    }

    public static function update($group_key, $form)
    {
        self::$db->where('group_key', $group_key);
        return self::$db->update(self::group, [
            'group_key' => $form['group_key'],
            'group_name' => $form['group_name'],
        ]);
    }

    public static function delete($group_key)
    {
        self::$db->where('group_key', $group_key);
        return self::$db->delete(self::group);
    }

    public static function fetch_all($limit = null, $isCount = false)
    {
        $result = self::$db->get(self::group . ' g', $limit);
        return ($isCount) ? self::$db->count : $result;
    }

    public static function noGuest()
    {
        self::$db->where('g.group_key', 'guest', '!=');
    }

    public static function filter($form)
    {
        if (!empty($form['keyword'])) {
            $form['keyword'] = '%' . $form['keyword'] . '%';
            self::$db->where('g.group_key LIKE ? OR g.group_name LIKE ?', [$form['keyword'], $form['keyword']]);
        }
    }

    public static function fetch_permissions($group_key)
    {
        self::$db->where('gp.group_key', $group_key);
        self::$db->join(self::permission . " p", "p.permission_id = gp.permission_id");
        return self::$db->get(self::group_permission . ' gp');
    }

    public static function permission_group_delete_all($group_key)
    {
        self::$db->where('group_key', $group_key);
        return self::$db->delete(self::group_permission);
    }

    public static function change_permission($status, $permission_id, $group_key)
    {
        if ($status) {
            return self::permission_group_insert($permission_id, $group_key);
        } else {
            return self::permission_group_delete($permission_id, $group_key);
        }
    }

    public static function permission_group_insert($permission_id, $group_key)
    {
        self::permission_group_delete($permission_id, $group_key);
        return self::$db->insert(self::group_permission, array(
            "permission_id" => $permission_id,
            "group_key" => $group_key,
        ));
    }

    public static function permission_group_delete($permission_id, $group_key)
    {
        self::$db->where('permission_id', $permission_id);
        self::$db->where('group_key', $group_key);
        return self::$db->delete(self::group_permission);
    }

    public static function sort($sort)
    {
        if (!empty($sort) && isset($sort['field']) && !empty($sort['field'])) {
            self::$db->orderBy($sort['field'], $sort['type']);
        }
    }

    public static function getGroupsForCache()
    {
        $groups = GroupModel::fetch_all();

        $result = [];
        $permissions = PermissionModel::fetch_all();
        foreach ($groups as $group) {
            $result[$group['group_key']] = [
                'module' => [],
                'option' => [],
            ];

            $gPermissions = GroupModel::fetch_permissions($group['group_key']);
            $ids = array_column($gPermissions, 'permission_id');

            foreach ($permissions as $p) {
                if (in_array($p['permission_id'], $ids) && $gPermissions[array_search($p['permission_id'], $ids)]['access']) {
                    $status = true;
                } else {
                    $status = ($p['permission_value'] == 1);
                }

                $result[$group['group_key']][$p['permission_type']][$p['permission_key']] = $status;
            }
        }
        return $result;
    }
}