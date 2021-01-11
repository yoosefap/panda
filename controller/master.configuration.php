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

namespace pinoox\app\com_pinoox_panda\controller;

use pinoox\app\com_pinoox_panda\component\TemplateHelper;
use pinoox\component\interfaces\ControllerInterface;
use pinoox\component\Lang;
use pinoox\component\Template;

class MasterConfiguration implements ControllerInterface
{
    /**
     * @var Template
     */
    protected static $template;
    /**
     * @var array
     */
    protected static $config;

    public function __construct()
    {
        $this->initTemplate();
    }

    private function initTemplate()
    {
        self::$template = new Template();
        self::$template->set('_site', url('~'));
        self::$template->set('_app', url());
        self::$template->set('_direction', rlang('front.direction'));
        self::$template->set('_translate', Lang::current());
    }

    public function _main()
    {
        self::$template->show('index');
    }

    public function _exception()
    {
        self::_main();
    }

}