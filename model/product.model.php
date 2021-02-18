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

use pinoox\component\Date;
use pinoox\component\User;

class ProductModel extends PandaDatabase
{

    const inactive = 'inactive';
    const active = 'active';


    public static function insert($data)
    {
        return self::$db->insert(self::product, [
            'product_name' => $data['product_name'],
            'category_id' => isset($data['category_id']) ? $data['category_id'] : null,
            'brand_id' => isset($data['brand_id']) ? $data['brand_id'] : null,
            'status' => self::inactive,
            'insert_date' => Date::g('Y-m-d H:i:s'),
        ]);
    }

    public static function update($data)
    {
        self::$db->where('product_id', $data['product_id']);
        return self::$db->update(self::product, [
            'product_name' => $data['product_name'],
            'category_id' => isset($data['category_id']) ? $data['category_id'] : null,
            'brand_id' => isset($data['brand_id']) ? $data['brand_id'] : null,
            'status' => isset($data['status']) ? $data['status'] : null,
        ]);
    }

    
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

    /*
     * Images
     */

    public static function fetch_images($product_id)
    {
        self::$db->join(self::file . ' f', 'f.file_id=pm.media_id');
        self::$db->where('pm.product_id', $product_id);
        return self::$db->get(self::product_media . ' pm');
    }

    public static function insert_media($product_id, $media_id)
    {
        return self::$db->insert(self::product_media, [
            'product_id' => $product_id,
            'media_id' => $media_id,
            'media_type' => 'image',
        ]);
    }

    public static function update_media_primary($product_id, $media_id)
    {
        self::$db->where('product_id', $product_id);
        self::$db->update(self::product_media, [
            'is_primary' => 0,
        ]);

        self::$db->where('product_id', $product_id);
        self::$db->where('media_id', $media_id);
        return self::$db->update(self::product_media, [
            'is_primary' => 1,
        ]);
    }

}