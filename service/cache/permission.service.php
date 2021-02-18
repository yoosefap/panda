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

namespace pinoox\app\com_pinoox_panda\service\cache;


use pinoox\app\com_pinoox_panda\model\GroupModel;
use pinoox\app\com_pinoox_panda\model\PermissionModel;
use pinoox\component\Cache;
use pinoox\component\interfaces\ServiceInterface;

class PermissionService implements ServiceInterface
{
    public function _run()
    {
        Cache::init('permission',function (){
            return GroupModel::getGroupsForCache();
        });
    }

    public function _stop()
    {

    }

}
