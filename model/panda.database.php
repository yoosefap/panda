<?php
/**
 *      ****  *  *     *  ****  ****  *    *
 *      *  *  *  * *   *  *  *  *  *   *  *
 *      ****  *  *  *  *  *  *  *  *    *
 *      *     *  *   * *  *  *  *  *   *  *
 *      *     *  *    **  ****  ****  *    *
 * @author   Pinoox
 * @license  https://opensource.org/licenses/MIT MIT License
 */
namespace pinoox\app\com_pinoox_panda\model;

use pinoox\model\PinooxDatabase;

class PandaDatabase extends PinooxDatabase
{

    //tables
    const group = 'com_pinoox_panda_group';
    const permission = 'com_pinoox_panda_permission';
    const group_permission = 'com_pinoox_panda_group_permission';
    const product = 'com_pinoox_panda_product';
    const category = 'com_pinoox_panda_category';

}
