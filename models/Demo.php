<?php

/**
 * @package Demo
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\demo\models;

use Exception;
use gplcart\core\Cache,
    gplcart\core\Model,
    gplcart\core\Handler;
use gplcart\core\models\Language as LanguageModel;

/**
 * Manages basic behaviors and data related to Demo module
 */
class Demo extends Model
{

    /**
     * Language model instance
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
     * Returns an array of handlers
     */
    public function getHandlers()
    {
        $handlers = &Cache::memory(__METHOD__);

        if (isset($handlers)) {
            return $handlers;
        }

        $handlers = array();

        $handlers['default'] = array(
            'title' => $this->language->text('Default (watches)'),
            'description' => $this->language->text('Create basic demo set containing products, categories and banners'),
            'handlers' => array(
                'create' => array('gplcart\\modules\\demo\\handlers\\Demo', 'create'),
                'delete' => array('gplcart\\modules\\demo\\handlers\\Demo', 'delete')
            )
        );

        $this->hook->attach('module.demo.handlers', $handlers, $this);
        return $handlers;
    }

    /**
     * Returns a handler
     * @param string $handler_id
     * @return array
     */
    public function getHandler($handler_id)
    {
        $handlers = $this->getHandlers();
        return empty($handlers[$handler_id]) ? array() : $handlers[$handler_id];
    }

    /**
     * Create a demo content
     * @param integer $store_id
     * @param string $handler_id
     * @return string|integer
     */
    public function create($store_id, $handler_id)
    {
        if ($this->getCreated($store_id)) {
            return $this->language->text('Demo content already exists for the store');
        }

        $handlers = $this->getHandlers();

        try {
            $created = Handler::call($handlers, $handler_id, 'create', array($store_id, $this));
        } catch (Exception $ex) {
            return $ex->getMessage();
        }

        if (empty($created)) {
            return $this->language->text('Demo content has not been created');
        }

        $this->setCreated($store_id, $handler_id, $created);
        return true;
    }

    /**
     * Deletes the demo content
     * @param integer $store_id
     * @return string|integer
     */
    public function delete($store_id)
    {
        $created = $this->getCreated($store_id);

        if (empty($created)) {
            return true;
        }

        $handlers = $this->getHandlers();

        try {
            $result = Handler::call($handlers, $created['handler_id'], 'delete', array($store_id, $this));
        } catch (Exception $ex) {
            $result = $ex->getMessage();
        }

        $this->resetCreated($store_id);
        return $result;
    }

    /**
     * Set an array of created entity IDs in the database
     * @param integer $store_id
     * @param string $handler_id
     * @param array $data
     * @return boolean
     */
    public function setCreated($store_id, $handler_id, array $data)
    {
        $saved = $this->config->get('module_demo_content', array());

        // Remove unneeded keys before saving
        $saved[$store_id] = array_map('array_values', $data);
        $saved[$store_id]['handler_id'] = $handler_id;

        return $this->config->set('module_demo_content', $saved);
    }

    /**
     * Returns an array of previously created entity IDs
     * @param integer $store_id
     * @param null|string $key
     * @param mixed $default
     * @return mixed
     */
    public function getCreated($store_id, $key = null, $default = array())
    {
        $data = $this->config->get('module_demo_content', array());
        $per_store = isset($data[$store_id]) ? (array) $data[$store_id] : $default;

        if (isset($key)) {
            return isset($per_store[$key]) ? (array) $per_store[$key] : $default;
        }
        return $per_store;
    }

    /**
     * Removes all saved data about previously created entity IDs from the database
     * @param integer $store_id
     * @return boolean
     */
    public function resetCreated($store_id)
    {
        $data = $this->config->get('module_demo_content', array());
        unset($data[$store_id]);

        if (empty($data)) {
            return $this->config->reset('module_demo_content');
        }
        return $this->config->set('module_demo_content', $data);
    }

}
