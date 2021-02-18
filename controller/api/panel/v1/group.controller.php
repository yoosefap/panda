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

use pinoox\app\com_pinoox_panda\model\GroupModel;
use pinoox\app\com_pinoox_panda\model\PermissionModel;
use pinoox\app\com_pinoox_panda\model\UserModel;
use pinoox\component\Cache;
use pinoox\component\Lang;
use pinoox\component\Pagination;
use pinoox\component\Request;
use pinoox\component\Response;
use pinoox\component\Service;
use pinoox\component\Validation;
use pinoox\model\PinooxDatabase;

class GroupController extends LoginConfiguration
{

    public function getGroup($group_key)
    {
        $group = GroupModel::fetch_by_key($group_key);
        Response::json($group);
    }

    public function getAll()
    {
        $p=Cache::get('permission');
        $form = Request::input('keyword,sort,perPage=10,page=1', null, '!empty');

        $this->filterSearch($form);
        GroupModel::filter($form);
        $count = GroupModel::fetch_all(null, true);
        $pagination = new Pagination($count, 20, 1);
        $pagination->setCurrentPage($form['page']);

        $this->filterSearch($form);
        GroupModel::filter($form);
        $groups = GroupModel::fetch_all($pagination->getArrayLimit());

        Response::json([
            'groups' => $groups,
            'pages' => $pagination->getInfoPage()['page']
        ]);
    }

    private function filterSearch($form)
    {
        GroupModel::noGuest();
        GroupModel::sort($form['sort']);
    }

    public function save()
    {
        $form = Request::post('old_group_key,group_key,group_name', null, '!empty');

        $valid = Validation::check($form, [
            'group_name' => ['required|length:>=2', rlang('user.group_name')],
            'group_key' => ['required|length:>=2', rlang('user.group_key')],
        ]);

        if ($valid->isFail())
            Response::jsonMessage($valid->first(), false);

        if (GroupModel::fetch_by_key($form['group_key'], $form['old_group_key']))
            Response::jsonMessage(rlang('user.repeat_group_key'), false);

        PinooxDatabase::startTransaction();

        if (!empty($form['old_group_key'])) {
            $status = GroupModel::update($form['old_group_key'], $form);
        } else {
            $status = GroupModel::insert($form);
            PermissionModel::insert_group_permission($form['group_key']);

        }


        PinooxDatabase::commit();

        if ($status)
            Response::jsonMessage(rlang('user.group_added_successfully'), true);
        else
            Response::jsonMessage(rlang('panel.unknown_try_again'), false);
    }

    public function delete($group_key)
    {
        $group = GroupModel::fetch_by_key($group_key);
        if ($group) {
            if (GroupModel::delete($group_key)) {
                Response::jsonMessage(Lang::replace('user.group_successful_delete', $group['group_name']), true);
            }
        }
        Response::jsonMessage(Lang::get('panel.error_happened'), false);
    }

    public function search($keyword = null)
    {
        GroupModel::noGuest();
        GroupModel::filter($keyword);
        $groups = GroupModel::fetch_all(8);
        $groups = empty($groups) ? [] : $groups;
        Response::json($groups);
    }

    public function getPermissions()
    {
        GroupModel::noGuest();
        $groups = GroupModel::fetch_all();

        $groups = array_map(function ($group) {

            $gPermissions = GroupModel::fetch_permissions($group['group_key']);
            $ids = array_column($gPermissions, 'permission_id');

            $permissions = PermissionModel::fetch_all();
            $result = [];
            foreach ($permissions as $permission) {
                if (in_array($permission['permission_id'], $ids))
                    $permission['permission_value'] = true;
                else
                    $permission['permission_value'] = false;

                $result[] = $permission;
            }

            $group['permissions'] = $result;
            return $group;
        }, $groups);

        Response::json($groups);
    }

    public function changePermission()
    {
        $form = Request::input(['group_key', 'permission_id', 'value'], null, '!empty');
        $form['value'] = $form['value'] == 'true';

        if (GroupModel::change_permission($form['value'], $form['permission_id'], $form['group_key'])) {
            Cache::clean('permission');
            Response::json(rlang('global.save_successful'), true);
        } else {
            Response::json(rlang('global.err.unknown_try_again'), false);
        }
    }

    public function syncPermission()
    {
        PermissionModel::sync_groups_permissions();
        //todo: generate and update cache
    }
}
