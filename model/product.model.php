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

class ProductModel extends PandaDatabase
{
    public static function fetch_all($limit = null, $isCount = false)
    {
        $result = self::$db->get(self::product . ' p', $limit);
        if ($isCount)
            return self::$db->count;
        return $result;
    }

    public static function fetch_by_id($product_id)
    {
        self::$db->where('p.product_id', $product_id);
        return self::$db->getOne(self::product . ' p');
    }

    public static function sort($sort)
    {
        if (!empty($sort) && isset($sort['field']) && !empty($sort['field'])) {
            self::$db->orderBy($sort['field'], $sort['type']);
        }
    }

    public static function where_status($status)
    {
        if (!is_null($status) && $status != 'all')
            self::$db->where('status', $status);
    }


}