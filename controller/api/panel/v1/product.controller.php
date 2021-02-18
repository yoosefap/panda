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

use pinoox\app\com_pinoox_panda\component\Helper;
use pinoox\app\com_pinoox_panda\model\CategoryModel;
use pinoox\app\com_pinoox_panda\model\PandaDatabase;
use pinoox\app\com_pinoox_panda\model\ProductModel;
use pinoox\component\Cookie;
use pinoox\component\Pagination;
use pinoox\component\Request;
use pinoox\component\Response;
use pinoox\component\Upload;
use pinoox\component\Uploader;
use pinoox\component\Url;
use pinoox\component\User;
use pinoox\component\Validation;
use pinoox\model\FileModel;
use pinoox\model\PinooxDatabase;
use pinoox\app\com_pinoox_panda\model\UserModel;

class ProductController extends LoginConfiguration
{

    private static $uploadPath = 'uploads/products/';

    public function getById($product_id)
    {
        $product = ProductModel::fetch_by_id($product_id);
        if (empty($product))
            Response::json(null);

        $product['category'] = CategoryModel::fetch_by_id($product['category_id']);

        Response::json($product);
    }

    public function getAll()
    {
        $form = Request::input('keyword,sort,status=all,perPage=10,page=1', null, '!empty');

        $this->filterSearch($form);
        $count = ProductModel::fetch_all(null, true);

        // pagination
        $pagination = new Pagination($count, $form['perPage']);
        $pagination->setCurrentPage($form['page']);

        $this->filterSearch($form);
        $products = ProductModel::fetch_all($pagination->getArrayLimit());

        $products = array_map(function ($item) {
            return $item = $this->getItemInfo($item);
        }, $products);

        Response::json(['products' => $products, 'pages' => $pagination->getInfoPage()['page']]);

    }

    private function filterSearch($form)
    {
        UserModel::where_search($form['keyword']);
        UserModel::where_status($form['status']);
        UserModel::sort($form['sort']);
    }

    private function getItemInfo($item)
    {
        if (empty($item)) return $item;
        $item['insert_date'] = Helper::getLocaleDate('Y/m/d', $item['insert_date']);

        return $item;
    }

    public function save()
    {
        $data = Request::input('product_id,product_name,category,brand,tags,description', null, '!empty');
        $valid = Validation::check($data, [
            'product_name' => ['required|length:>2', rlang('product.product_name')],
        ]);
        if ($valid->isFail())
            Response::jsonMessage($valid->first(), false);

        $oldProduct = null;
        if (!empty($data['product_id']))
            $oldProduct = ProductModel::fetch_by_id($data['product_id']);

        if (!empty($data['category']))
            $data['category_id'] = $data['category']['cat_id'];

        if (!empty($oldProduct)) {
            $data['status'] = isset($data['status']) && !empty($data['status']) ? $data['status'] : $oldProduct['status'];
            $status = ProductModel::update($data);
            if ($status)
                Response::jsonMessage(rlang('product.saved_successfully'), true);
        } else {
            $product_id = ProductModel::insert($data);
            if ($product_id)
                Response::jsonMessage(rlang('product.added_successfully'), true, $product_id);
        }


        Response::jsonMessage(rlang('panel.error_happened'), false);
    }

    public function getImages()
    {
        $media = ProductModel::fetch_images(1);
        $media = array_map(function ($item) {
            $media = self::$uploadPath . $item['file_name'];
            return [
                'id' => $item['file_id'],
                'img' => $media,
                'link' => Url::file($media),
                'active' => $item['is_primary'],
            ];
        }, $media);

        Response::json($media);
    }

    public function uploadImage()
    {
        if (Request::isFile('image')) {

            $path = path(self::$uploadPath);
            $upload = Uploader::init('image', $path)
                ->insert(null, 'product', User::get('user_id'))
                ->allowedTypes('png,jpg,jpeg,svg', 10)
                ->changeName('time')
                ->finish(true);

            if ($result = $upload->result()) {
                $result['id'] = $upload->getInsertId();
                $result['media'] = self::$uploadPath . $result['uploadname'];

                ProductModel::insert_media(1, $result['id']);

                Response::json([
                    'id' => $result['id'],
                    'img' => $result['media'],
                    'link' => Url::file($result['media']),
                ], true);
            }


            Response::json($upload->error('first'), false);
        }
    }

    public function setPrimaryImage()
    {
        $image_id = Request::inputOne('image_id', null, '!empty');
        ProductModel::update_media_primary(1, $image_id);
        Response::json(null, true);
    }

    public function deleteImage($file_id)
    {
        Uploader::init()->actRemoveRow($file_id);
        Response::json(null, true);
    }

}
