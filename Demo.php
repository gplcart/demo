<?php

/**
 * @package Demo
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\demo;

use gplcart\core\Module;
use gplcart\core\models\Language as LanguageModel;

/**
 * Main class for Demo module
 */
class Demo extends Module
{

    /**
     * Language class instance
     * @var \gplcart\core\models\Language $language
     */
    protected $language;

    /**
     * @param LanguageModel $language
     */
    public function __construct(LanguageModel $language)
    {
        parent::__construct();

        $this->language = $language;
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

        foreach ($demo_model->getCreated($store['store_id'], 'collection') as $id) {
            $collection = $collection_model->get($id);
            if (!empty($collection['status']) && isset($store['data']["collection_{$collection['type']}"])) {
                $store['data']["collection_{$collection['type']}"] = $id;
            }
        }
    }

    /**
     * Implements hook "install.after"
     * @param array $data
     * @param array $result
     * @param array $cli_route
     */
    public function hookInstallAfter($data, $result, $cli_route)
    {
        if (GC_CLI//
                && isset($result['severity']) && $result['severity'] === 'success'//
                && isset($cli_route['command']) && $cli_route['command'] === 'install') {
            $this->createDemo();
        }
    }

    /**
     * Create demo in wizard mode
     */
    protected function createDemo()
    {
        /* @var $cli_helper \gplcart\core\helpers\Cli */
        $cli_helper = $this->getInstance('gplcart\\core\\helpers\\Cli');

        /* @var $demo_model \gplcart\modules\demo\models\Demo */
        $demo_model = $this->getInstance('gplcart\\modules\\demo\\models\\Demo');

        $options = $this->getHandlerOptions($demo_model);

        if (count($options) < 2) {
            return null;
        }

        $title = $this->language->text('Would you like to create demo content? Enter a number of demo package');

        $input = $cli_helper->menu($options, 0, $title);

        if (empty($input)) {
            return null;
        }

        $handler_id = array_search($input, $options);

        if (empty($handler_id)) {
            return null;
        }

        $created_result = $demo_model->create(1, $handler_id);

        if ($created_result !== true) {
            $cli_helper->line($created_result);
        }
    }

    /**
     * Returns an array of supported demo handlers
     * @param \gplcart\modules\demo\models\Demo $model
     * @return array
     */
    protected function getHandlerOptions(\gplcart\modules\demo\models\Demo $model)
    {
        $options = array($this->language->text('No demo'));

        foreach ($model->getHandlers() as $id => $handler) {
            $options[$id] = "$id - {$handler['title']}";
        }

        return $options;
    }

}
