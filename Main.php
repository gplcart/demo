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
            'menu' => array('admin' => /* @text */'Demo content'),
            'access' => '_superadmin',
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
            'handlers' => array(
                'controller' => array('gplcart\\modules\\demo\\controllers\\Cli', 'createCli')
            ),
            'help' => array(
                'description' => /* @text */'Populate a store with a demo content',
                'options' => array(
                    '--package' => /* @text */'Optional. A package ID used as an source. Defaults to "default"',
                    '--store' => /* @text */'Optional. A numeric ID of the store you want to create demo content for. Defaults to 1'
                )
            )
        );

        $routes['demo-delete'] = array(
            'handlers' => array(
                'controller' => array('gplcart\\modules\\demo\\controllers\\Cli', 'deleteCli')
            ),
            'help' => array(
                'description' => /* @text */'Delete all created demo content from a store',
                'options' => array(
                    '--package' => /* @text */'Optional. A package ID used as an source. Defaults to "default"',
                    '--store' => /* @text */'Optional. A numeric ID of the store you want to delete demo content from. Defaults to 1'
                )
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
    protected function getModel()
    {
        return Container::get('gplcart\\modules\\demo\\models\\Demo');
    }

}
