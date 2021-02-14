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

    public function getAll()
    {
        $form = Request::input('keyword,sort,status=all,perPage=10,page=1', null, '!empty');

        $this->filterSearch($form);
        $count = ProductModel::fetch_all(null, true);

        // pagination
        $pagination = new Pagination($count, $form['perPage']);
        $pagination->setCurrentPage($form['page']);

        $this->filterSearch($form);
        $products= ProductModel::fetch_all($pagination->getArrayLimit());

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

    public function add()
    {
        $input = Request::post('avatar_id,status,fname,lname,username,email,password,re_password', null, '!empty');
        $valid = Validation::check($input, [
            'fname' => ['required|length:>2', rlang('user.fname')],
            'lname' => ['required|length:>2', rlang('user.lname')],
            'username' => ['required|username', rlang('user.username')],
            'email' => ['required|email', rlang('user.email')],
            'password' => ['required|length:>4', rlang('user.password')],
            're_password' => ['match:==[password]', rlang('user.password')],
        ], [
            're_password:match' => rlang('user.passwords_not_matched'),
        ]);
        if ($valid->isFail())
            Response::jsonMessage($valid->first(), false);

        $input['status'] = $input['status'] === UserModel::active ? UserModel::active : UserModel::suspend;

        $username = UserModel::fetch_user_by_email_or_username($input['username']);
        if ($username)
            Response::jsonMessage(rlang('user.username_duplicated'), false);

        $email = UserModel::fetch_user_by_email_or_username($input['email']);
        if ($email)
            Response::jsonMessage(rlang('user.email_duplicated'), false);

        $user_id = UserModel::insert($input);

        if ($user_id) {
            $this->uploadAvatar($user_id);
            Response::jsonMessage(rlang('user.added_successfully'), true);
        }

        Response::jsonMessage(rlang('panel.error_happened'), false);
    }

}
