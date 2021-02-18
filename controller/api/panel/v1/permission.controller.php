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

namespace pinoox\app\com_pinoox_panda\controller\api\panel\v1;

use pinoox\app\com_pinoox_panda\model\PermissionModel;
use pinoox\component\Cache;
use pinoox\component\Request;
use pinoox\component\Response;
use pinoox\component\Tree;
use pinoox\component\Validation;

class PermissionController extends LoginConfiguration
{

    public function getAll()
    {
        $group_key = Request::inputOne('group_key', null, '!empty');

        $permissions = PermissionModel::fetch_permissions_group($group_key);
        $permissions = array_map(function ($p) {
            return [
                'id' => $p['permission_id'],
                'text' => $p['permission_title'],
                'parent' => $p['permission_parent'],
                'key' => $p['permission_key'],
                'node' => $p['node'],
                'access' => $p['access'],
            ];
        }, $permissions);

        $checked = [];
        foreach ($permissions as $p) {
            if ($p['node'])
                $checked[] = $p['id'];
        }
        $tree = new Tree();
        $treePermissions = $tree->createTree($permissions, 'parent', 'id');

        Response::json(['permissions' => $treePermissions, 'checked' => $checked]);
    }

    public function save()
    {
        $data = Request::input('group_key,tree', null, '!empty');

        $valid = Validation::check($data, [
            'group_key' => ['required', ''],
            'tree' => ['required', ''],
        ], [
            'group_key:required' => rlang('panel.invalid_request'),
            'tree:required' => rlang('panel.invalid_request')
        ]);

        if ($valid->isFail()) Response::jsonMessage($valid->first(), false);

        PermissionModel::set_group_permission($data['group_key'], $data['tree']);
        Cache::clean('permission');
        Response::jsonMessage(rlang('panel.edited_successfully'), true);
    }

}
