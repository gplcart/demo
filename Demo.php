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
        // Create
        $routes['demo-create'] = array(
            'handlers' => array(
                'process' => array('gplcart\\modules\\demo\\controllers\\Cli', 'createCli')
            ),
            'help' => array(
                'description' => 'Populate a store with a demo content',
                'options' => array(
                    '--package' => 'Optional. A package ID used as an source. Defaults to "default"',
                    '--store' => 'Optional. A store ID to be populated with the demo content. Defaults to 1'
                )
            )
        );

        // Delete
        $routes['demo-delete'] = array(
            'handlers' => array(
                'process' => array('gplcart\\modules\\demo\\controllers\\Cli', 'deleteCli')
            ),
            'help' => array(
                'description' => 'Delete all created demo content from a store',
                'options' => array(
                    '--package' => 'Optional. A package ID that was used as an source. Defaults to "default"',
                    '--store' => 'Optional. A store ID that contains the demo content. Defaults to 1'
                )
            )
        );
    }

    /**
     * Implements hook "cli.install.finish"
     * @param mixed $result
     * @param string $message
     * @param \gplcart\core\controllers\cli\Install $controller
     */
    public function hookCliInstallFinish($result, &$message, $controller)
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
        $input = (int) $controller->menu($options, 0, $title);

        if ($input < 2) {
            return null;
        }

        $created_result = $model->create(array_search($input, $options), 1);

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
