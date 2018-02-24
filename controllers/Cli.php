<?php

/**
 * @package Demo
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\demo\controllers;

use gplcart\core\CliController;
use gplcart\core\models\Store as StoreModel;
use gplcart\modules\demo\models\Demo as DemoModel;

/**
 * Handles Demo module CLI commands
 */
class Cli extends CliController
{

    /**
     * Demo module model instance
     * @var \gplcart\modules\demo\models\Demo $demo
     */
    protected $demo;

    /**
     * Store model class instance
     * @var \gplcart\core\models\Store $store
     */
    protected $store;

    /**
     * The current demo handler id
     * @var string
     */
    protected $data_handler_id;

    /**
     * The current demo store ID
     * @var integer
     */
    protected $data_store_id;

    /**
     * @param StoreModel $store
     * @param DemoModel $demo
     */
    public function __construct(StoreModel $store, DemoModel $demo)
    {
        parent::__construct();

        $this->demo = $demo;
        $this->store = $store;

        $this->data_store_id = $this->getParam('store', 1);
        $this->data_handler_id = $this->getParam('package', 'default');
    }

    /**
     * Handles "demo-create" command
     */
    public function createCli()
    {
        if ($this->validateCli()) {
            $result = $this->demo->create($this->data_store_id, $this->data_handler_id);
            if ($result !== true) {
                $this->setError('result', $result);
            }
        }

        $this->output();
    }

    /**
     * Handles "demo-delete" command
     */
    public function deleteCli()
    {
        if ($this->validateCli()) {
            $result = $this->demo->delete($this->data_store_id);
            if ($result !== true) {
                $this->setError('result', $result);
            }
        }

        $this->output();
    }

    /**
     * Validates submitted arguments
     * @return boolean
     */
    protected function validateCli()
    {
        if (!$this->store->get($this->data_store_id)) {
            $this->setError('store_id', $this->text('Store does not exist'));
        }

        if (!$this->demo->getHandler($this->data_handler_id)) {
            $this->setError('handler_id', $this->text('Unknown handler'));
        }

        return !$this->isError();
    }

}
