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
            'menu' => array('admin' => 'Demo content'),
            'access' => '__superadmin',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\demo\\controllers\\Demo', 'editDemo')
            )
        );
    }

    /**
     * Implements hook "dashboard.intro"
     * @param array $items
     * @param \gplcart\core\controllers\backend\Dashboard $controller
     */
    public function hookDashboardIntro(array &$items, $controller)
    {
        if ($controller->isSuperadmin()) {

            $last = end($items);
            $count = count($items);
            $max = isset($last['weight']) && $last['weight'] > $count ? $last['weight'] : $count;

            $items['demo'] = array(
                'weight' => $max++,
                'rendered' => $controller->render('demo|intro')
            );
        }
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
                'description' => 'Populate a store with a demo content',
                'options' => array(
                    '--package' => 'Optional. A package ID used as an source. Defaults to "default"',
                    '--store' => 'Optional. A numeric ID of the store you want to create demo content for. Defaults to 1'
                )
            )
        );

        $routes['demo-delete'] = array(
            'handlers' => array(
                'process' => array('gplcart\\modules\\demo\\controllers\\Cli', 'deleteCli')
            ),
            'help' => array(
                'description' => 'Delete all created demo content from a store',
                'options' => array(
                    '--package' => 'Optional. A package ID used as an source. Defaults to "default"',
                    '--store' => 'Optional. A numeric ID of the store you want to delete demo content from. Defaults to 1'
                )
            )
        );
    }

    /**
     * Implements hook "store.get.after"
     * @param array $store
     */
    public function hookStoreGetAfter(array &$store)
    {
        if (empty($store['store_id'])) {
            return null;
        }

        /* @var $demo_model \gplcart\modules\demo\models\Demo */
        $demo_model = $this->getInstance('gplcart\\modules\\demo\\models\\Demo');

        /* @var $collection_model \gplcart\core\models\Collection */
        $collection_model = $this->getInstance('gplcart\\core\\models\\Collection');

        // Adjust the store collection settings to show the demo content
        foreach ($demo_model->getCreated($store['store_id'], 'collection') as $id) {
            $collection = $collection_model->get($id);
            if (!empty($collection['status']) && isset($store['data']["collection_{$collection['type']}"])) {
                $store['data']["collection_{$collection['type']}"] = $id;
            }
        }
    }

    /**
     * Implements hook "cli.install.after"
     * @param mixed $result
     * @param string $message
     * @param \gplcart\core\controllers\cli\Install $controller
     */
    public function hookCliInstallAfter($result, &$message, $controller)
    {
        if ($result !== true) {
            return null;
        }

        /* @var $model \gplcart\modules\demo\models\Demo */
        $model = $this->getInstance('gplcart\\modules\\demo\\models\\Demo');
        $options = $this->getHandlerOptions($model, $controller);

        if (count($options) < 2) {
            return null;
        }

        $title = $controller->text('Would you like to create demo content? Enter a number of demo package');
        $input = $controller->menu($options, 0, $title);

        if (empty($input)) {
            return null; // Finished
        }

        $handler_id = array_search($input, $options);

        if (empty($handler_id)) {
            return null; // Refused
        }

        $created_result = $model->create(1, $handler_id);
        if ($created_result !== true) {
            $controller->line($created_result);
        }
    }

    /**
     * Returns an array of supported demo handlers
     * @param \gplcart\modules\demo\models\Demo $model
     * @param \gplcart\core\controllers\cli\Install $controller
     * @return array
     */
    protected function getHandlerOptions($model, $controller)
    {
        $options = array($controller->text('No demo'));
        foreach ($model->getHandlers() as $id => $handler) {
            $options[$id] = "$id - {$handler['title']}";
        }
        return $options;
    }

}
