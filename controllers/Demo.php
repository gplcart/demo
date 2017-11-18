<?php

/**
 * @package Demo
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\demo\controllers;

use gplcart\modules\demo\models\Demo as DemoModuleModel;
use gplcart\core\controllers\backend\Controller as BackendController;

/**
 * Handles incoming requests and outputs data related to Demo module
 */
class Demo extends BackendController
{

    /**
     * Demo module model instance
     * @var \gplcart\modules\demo\models\Demo $demo
     */
    protected $demo_model;

    /**
     * @param DemoModuleModel $demo
     */
    public function __construct(DemoModuleModel $demo)
    {
        parent::__construct();

        $this->demo_model = $demo;
    }

    /**
     * Route page callback
     */
    public function editDemo()
    {
        $this->setTitleEditDemo();
        $this->setBreadcrumbEditDemo();

        $this->setData('stores', $this->store->getList());
        $this->setData('handlers', $this->demo_model->getHandlers());

        $this->submitDemo();
        $this->outputEditDemo();
    }

    /**
     * Set title on the demo content creation page
     */
    protected function setTitleEditDemo()
    {
        $this->setTitle($this->text('Demo content'));
    }

    /**
     * Set breadcrumbs on the demo content creation page
     */
    protected function setBreadcrumbEditDemo()
    {
        $breadcrumb = array(
            'text' => $this->text('Dashboard'),
            'url' => $this->url('admin')
        );

        $this->setBreadcrumb($breadcrumb);
    }

    /**
     * Handles submitted data
     */
    protected function submitDemo()
    {
        if ($this->isPosted('create') && $this->validateDemo()) {
            $this->createDemo();
        } else if ($this->isPosted('delete') && $this->validateDemo()) {
            $this->deleteDemo();
        }
    }

    /**
     * Validates submitted data
     * @return boolean
     */
    protected function validateDemo()
    {
        $this->setSubmitted('demo');
        $handler_id = $this->getSubmitted('handler_id');

        if (empty($handler_id) || !$this->demo_model->getHandler($handler_id)) {
            $this->setError('handler_id', $this->language->text('Unknown handler'));
        }

        return !$this->hasErrors();
    }

    /**
     * Creates the demo content
     */
    protected function createDemo()
    {
        $this->controlAccessSuperAdmin();

        $store_id = $this->getSubmitted('store_id', 1);
        $handler_id = $this->getSubmitted('handler_id');

        $result = $this->demo_model->create($store_id, $handler_id);

        if ($result === true) {
            $this->redirect('', $this->text('Demo content has been created'), 'success');
        }

        $this->redirect('', $result, 'warning');
    }

    /**
     * Delete the previously created demo content
     */
    protected function deleteDemo()
    {
        $this->controlAccessSuperAdmin();

        $result = $this->demo_model->delete($this->getSubmitted('store_id', 1));

        if ($result === true) {
            $this->redirect('', $this->text('Demo content has been deleted'), 'success');
        }

        $this->redirect('', $result, 'warning');
    }

    /**
     * Render and output the demo content creation page
     */
    protected function outputEditDemo()
    {
        $this->output('demo|edit');
    }

}
