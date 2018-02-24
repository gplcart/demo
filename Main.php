<?php

/**
 * @package Demo
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\demo;

use gplcart\core\Container;

/**
 * Main class for Demo module
 */
class Main
{

    /**
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        $routes['admin/tool/demo'] = array(
            'menu' => array(
                'admin' => 'Demo content' // @text
            ),
            'access' => GC_PERM_SUPERADMIN,
            'handlers' => array(
                'controller' => array('gplcart\\modules\\demo\\controllers\\Demo', 'editDemo')
            )
        );
    }

    /**
     * Implements hook "cli.route.list"
     * @param array $routes
     */
    public function hookCliRouteList(array &$routes)
    {
        $routes['demo-create'] = array(
            'description' => 'Populate a store with a demo content', // @text
            'usage' => array(
                'gplcart demo-create -h',
                'gplcart demo-create [--package=<string> --store=<int>]'
            ),
            'options' => array(
                '--package' => 'A package ID used as an source [default: default]', // @text
                '--store' => 'A numeric ID of the store you want to create demo content for [default: 1]' // @text
            ),
            'handlers' => array(
                'controller' => array('gplcart\\modules\\demo\\controllers\\Cli', 'createCli')
            )
        );

        $routes['demo-delete'] = array(
            'description' => 'Delete all created demo content from a store', // @text
            'options' => array(
                '--package' => 'A package ID used as an source [default: default]', // @text
                '--store' => 'A numeric ID of the store you want to create demo content for [default: 1]' // @text
            ),
            'handlers' => array(
                'controller' => array('gplcart\\modules\\demo\\controllers\\Cli', 'deleteCli')
            )
        );
    }

    /**
     * Create a demo content
     * @param integer $store_id
     * @param string $handler_id
     * @return string|boolean
     */
    public function create($store_id, $handler_id)
    {
        return $this->getModel()->create($store_id, $handler_id);
    }

    /**
     * Returns an array of demo handlers
     * @return array
     */
    public function getHandlers()
    {
        return $this->getModel()->getHandlers();
    }

    /**
     * Returns model instance
     * @return \gplcart\modules\demo\models\Demo
     */
    public function getModel()
    {
        /** @var \gplcart\modules\demo\models\Demo $instance */
        $instance = Container::get('gplcart\\modules\\demo\\models\\Demo');
        return $instance;
    }
}
