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

class PermissionModel extends PandaDatabase
{
    public static function fetch_all($limit = null, $isCount = false)
    {
        $return = self::$db->get(self::permission . ' p', $limit);
        if ($isCount) $return = self::$db->count;
        return $return;
    }

    public static function search_by_title($title = null, $limit = null, $isCount = false)
    {
        if (!empty($title))
            self::$db->where('permission_title', '%' . $title . '%', 'LIKE');

        $return = self::$db->get(self::permission, $limit);
        if ($isCount) $return = self::$db->count;
        return $return;
    }

    public static function fetch_permissions_group($group_key = null)
    {
        if (!is_null($group_key)) {
            $group_key = self::$db->escape($group_key);
            self::$db->join(self::group_permission . ' gp', "gp.permission_id=p.permission_id AND gp.group_key='" . $group_key . "'", 'LEFT');
        } else {
            self::$db->join(self::group_permission . ' gp', "gp.permission_id=p.permission_id", 'LEFT');
        }

        return self::$db->get(self::permission . ' p', null, 'p.*,gp.group_key,IFNULL(gp.node,0) `node`, IFNULL(gp.access,0) `access`');
    }

    public static function fetch_all_group_permission($group_key = null)
    {
        if (!is_null($group_key))
            self::$db->where('gp.group_key', $group_key);
        return self::$db->get(self::group_permission . ' gp', null);
    }

    public static function set_group_permission($group_key, $tree)
    {
        $branches = $tree['branches'];
        $nodes = $tree['nodes'];

        //reset
        $gPermissions = self::fetch_permissions_group($group_key);
        foreach ($gPermissions as $p) {
            self::$db->where('gp.permission_id', $p['permission_id']);
            self::$db->where('gp.group_key', $group_key);
            self::$db->update(self::group_permission . ' gp', [
                'node' => 0,
                'access' => 0,
            ]);
        }

        //update nodes
        foreach ($nodes as $n) {
            self::$db->where('gp.permission_id', $n);
            self::$db->where('gp.group_key', $group_key);
            self::$db->update(self::group_permission . ' gp', [
                'node' => 1
            ]);
        }

        //update access
        foreach ($branches as $b) {
            self::$db->where('gp.permission_id', $b['id']);
            self::$db->where('gp.group_key', $group_key);
            self::$db->update(self::group_permission . ' gp', [
                'access' => 1
            ]);
        }
    }

    public static function insert_group_permission($group_key)
    {
        $permissions = self::fetch_all();
        return self::multiInsertGroupPermissions($group_key, $permissions);
    }

    public static function sync_groups_permissions()
    {
        GroupModel::noGuest();
        $groups = GroupModel::fetch_all();

        foreach ($groups as $g) {
            $gPermissions = PermissionModel::fetch_all_group_permission($g['group_key']);
            if (!empty($gPermissions)) {
                $ids = array_column($gPermissions, 'permission_id');
                self::$db->where('p.permission_id', $ids, 'NOT IN');
                $permissions = self::$db->get(self::permission . ' p');
                if (empty($permissions)) continue;

            } else {
                $permissions = PermissionModel::fetch_all();
            }

            self::multiInsertGroupPermissions($g['group_key'], $permissions);
        }

    }

    private static function multiInsertGroupPermissions($group_key, $permissions)
    {
        $data = [];
        foreach ($permissions as $p) {
            $data[] = [
                'permission_id' => $p['permission_id'],
                'group_key' => $group_key,
            ];
        }
        return self::$db->insertMulti(self::group_permission, $data);
    }

}