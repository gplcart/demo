<?php

/**
 * @package Demo 
 * @author Iurii Makukh <gplcart.software@gmail.com> 
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com> 
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+ 
 */

namespace gplcart\modules\demo\models;

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

        $this->hook->fire('module.demo.handlers', $handlers, $this);
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
     * @param string $handler_id
     * @param integer $store_id
     * @return string|integer
     */
    public function create($handler_id, $store_id)
    {
        $existing = $this->getCreatedEntityId($handler_id, $store_id);

        if (!empty($existing)) {
            return $this->language->text('Demo content already exists');
        }

        $handlers = $this->getHandlers();
        return Handler::call($handlers, $handler_id, 'create', array($store_id, $this));
    }

    /**
     * Deletes the demo content
     * @param string $handler_id
     * @param integer $store_id
     * @return string|integer
     */
    public function delete($handler_id, $store_id)
    {
        $existing = $this->getCreatedEntityId($handler_id, $store_id);

        if (empty($existing)) {
            return true;
        }

        $handlers = $this->getHandlers();
        return Handler::call($handlers, $handler_id, 'delete', array($store_id, $this));
    }

    /**
     * Set an array of created entity IDs in the database
     * @param string $handler_id
     * @param integer $store_id
     * @param array $data
     * @return boolean
     */
    public function setCreatedEntityId($handler_id, $store_id, array $data)
    {
        // Remove unneeded keys before saving
        $cleaned = array_map('array_values', $data);
        return $this->config->set("module_demo_{$handler_id}_{$store_id}", $cleaned);
    }

    /**
     * Returns an array of previously created entity IDs
     * @param string $handler_id
     * @param integer $store_id
     * @return array
     */
    public function getCreatedEntityId($handler_id, $store_id)
    {
        return $this->config->get("module_demo_{$handler_id}_{$store_id}", array());
    }

    /**
     * Removes all saved data about previously created entity IDs from the database
     * @param string $handler_id
     * @param integer $store_id
     * @return boolean
     */
    public function resetCreatedEntityId($handler_id, $store_id)
    {
        return $this->config->reset("module_demo_{$handler_id}_{$store_id}");
    }

}
