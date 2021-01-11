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
namespace pinoox\app\com_pinoox_panda\controller\panel;

use pinoox\component\HelperHeader;
use pinoox\component\Router;

class MainController extends MasterConfiguration
{
    public function dist()
    {
        $url = implode('/', Router::params());
        if ($url === 'panda/pinoox.js') {
            HelperHeader::contentType('application/javascript', 'UTF-8');
            self::$template->view('dist/panda/pinoox.js');
        } else {
            self::error404();
        }
    }
    public function _main()
    {
        self::$template->view('index');
    }
}