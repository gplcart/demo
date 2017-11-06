<?php

/**
 * @package Demo
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\demo\models;

use gplcart\core\Handler,
    gplcart\core\Config,
    gplcart\core\Hook;
use gplcart\core\models\Language as LanguageModel;

/**
 * Manages basic behaviors and data related to Demo module
 */
class Demo extends Model
{

    /**
     * Hook class instance
     * @var \gplcart\core\Hook $hook
     */
    protected $hook;

    /**
     * Config class instance
     * @var \gplcart\core\Config $config
     */
    protected $config;

    /**
     * Language model instance
     * @var \gplcart\core\models\Language $language
     */
    protected $language;

    /**
     * @param Hook $hook
     * @param Config $config
     * @param LanguageModel $language
     */
    public function __construct(Hook $hook, Config $config, LanguageModel $language)
    {
        $this->hook = $hook;
        $this->config = $config;
        $this->language = $language;
    }

    /**
     * Returns an array of handlers
     */
    public function getHandlers()
    {
        $handlers = &gplcart_static(__METHOD__);

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
     * Call a handler
     * @param string $handler_id
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function callHandler($handler_id, $method, $arguments = array())
    {
        $handlers = $this->getHandlers();

        try {
            $result = Handler::call($handlers, $handler_id, $method, $arguments);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }

        return $result;
    }

    /**
     * Create a demo content
     * @param integer $store_id
     * @param string $handler_id
     * @return string|boolean
     */
    public function create($store_id, $handler_id)
    {
        if ($this->get($store_id)) {
            return $this->language->text('Demo content already exists for the store');
        }

        $created = $this->callHandler($handler_id, 'create', array($store_id, $this));

        if (empty($created)) {
            return $this->language->text('Demo content has not been created');
        }

        $this->set($store_id, $handler_id, $created);
        return true;
    }

    /**
     * Deletes the demo content
     * @param integer $store_id
     * @return string|integer
     */
    public function delete($store_id)
    {
        $created = $this->get($store_id);

        if (empty($created)) {
            return true;
        }

        $result = $this->callHandler($created['handler_id'], 'delete', array($store_id, $this));
        $this->reset($store_id);
        return $result;
    }

    /**
     * Set an array of created entity IDs in the database
     * @param integer $store_id
     * @param string $handler_id
     * @param array $data
     * @return boolean
     */
    public function set($store_id, $handler_id, array $data)
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
    public function get($store_id, $key = null, $default = array())
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
    public function reset($store_id)
    {
        $data = $this->config->get('module_demo_content', array());
        unset($data[$store_id]);

        if (empty($data)) {
            return $this->config->reset('module_demo_content');
        }

        return $this->config->set('module_demo_content', $data);
    }

}
