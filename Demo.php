<?php

/**
 * @package Demo
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\demo;

use gplcart\core\Module;

/**
 * Main class for Demo module
 */
class Demo extends Module
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        $routes['admin/tool/demo'] = array(
            'menu' => array('admin' => /* @text */'Demo content'),
            'access' => '__superadmin',
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
                'process' => array('gplcart\\modules\\demo\\controllers\\Cli', 'createCli')
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
                'process' => array('gplcart\\modules\\demo\\controllers\\Cli', 'deleteCli')
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
     * Implements hook "store.get.after"
     * @param int $store_id
     * @param array $store
     */
    public function hookStoreGetAfter($store_id, array &$store)
    {
        if (!empty($store['store_id'])) {
            $this->replaceStoreCollection($store);
        }
    }

    /**
     * Replace collection settings in the store settings
     * @param array $store
     */
    protected function replaceStoreCollection(array &$store)
    {
        /* @var $collection_model \gplcart\core\models\Collection */
        $collection_model = $this->getModel('Collection');

        /* @var $demo_model \gplcart\modules\demo\models\Demo */
        $demo_model = $this->getModel('Demo', 'demo');

        foreach ($demo_model->get($store['store_id'], 'collection') as $id) {
            $collection = $collection_model->get($id);
            if (!empty($collection['status']) && isset($store['data']["collection_{$collection['type']}"])) {
                $store['data']["collection_{$collection['type']}"] = $id;
            }
        }
    }

    /**
     * Create a demo content
     * @param integer $store_id
     * @param string $handler_id
     * @return string|boolean
     */
    public function create($store_id, $handler_id)
    {
        /* @var $model \gplcart\modules\demo\models\Demo */
        $model = $this->getModel('Demo', 'demo');
        return $model->create($store_id, $handler_id);
    }

    /**
     * Returns an array of demo handlers
     * @return array
     */
    public function getHandlers()
    {
        /* @var $model \gplcart\modules\demo\models\Demo */
        $model = $this->getModel('Demo', 'demo');
        return $model->getHandlers();
    }

}
