<?php

/**
 * @package Demo
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\demo\handlers;

use gplcart\core\Config;
use gplcart\core\models\Category;
use gplcart\core\models\CategoryGroup;
use gplcart\core\models\Collection;
use gplcart\core\models\CollectionItem;
use gplcart\core\models\File;
use gplcart\core\models\Product;
use gplcart\core\models\User;

/**
 * Handler for Demo module
 */
class Demo
{

    /**
     * Config class instance
     * @var \gplcart\core\Config $config
     */
    protected $config;

    /**
     * File model instance
     * @var \gplcart\core\models\File $file
     */
    protected $file;

    /**
     * User model instance
     * @var \gplcart\core\models\User $user
     */
    protected $user;

    /**
     * Category model instance
     * @var \gplcart\core\models\Category $category
     */
    protected $category;

    /**
     * Category group model instance
     * @var \gplcart\core\models\CategoryGroup $category_group
     */
    protected $category_group;

    /**
     * Product model instance
     * @var \gplcart\core\models\Product $product
     */
    protected $product;

    /**
     * Collection model instance
     * @var \gplcart\core\models\Collection $collection
     */
    protected $collection;

    /**
     * Collection item model instance
     * @var \gplcart\core\models\CollectionItem $collection_item
     */
    protected $collection_item;

    /**
     * An array of created entity IDs
     * Entity keys are preserved to guarantee their order upon deletion
     * E.g, collection items must be deleted before the related collection
     * @var array
     */
    protected $created = array(
        'file' => array(),
        'product' => array(),
        'category' => array(),
        'category_group' => array(),
        'collection_item' => array(),
        'collection' => array()
    );

    /**
     * The store ID
     * @var integer
     */
    protected $store_id;

    /**
     * The current user ID
     * @var integer
     */
    protected $user_id;

    /**
     * Demo constructor.
     * @param Config $config
     * @param User $user
     * @param File $file
     * @param Product $product
     * @param Category $category
     * @param Collection $collection
     * @param CategoryGroup $category_group
     * @param CollectionItem $collection_item
     */
    public function __construct(Config $config, User $user, File $file,
                                Product $product, Category $category, Collection $collection,
                                CategoryGroup $category_group, CollectionItem $collection_item)
    {
        $this->user = $user;
        $this->file = $file;
        $this->config = $config;
        $this->product = $product;
        $this->category = $category;
        $this->collection = $collection;
        $this->category_group = $category_group;
        $this->collection_item = $collection_item;

        $this->user_id = $this->user->getId();

        if (empty($this->user_id) || GC_CLI) {
            $this->user_id = $this->user->getSuperadminId();
        }
    }

    /**
     * Creates all demo content
     * @param integer $store_id
     * @return array
     */
    public function create($store_id)
    {
        set_time_limit(0);

        $this->store_id = $store_id;

        $this->createCategoryGroups();
        $this->createCategories();
        $this->createProducts();
        $this->createProductImages();
        $this->createCollections();
        $this->createFiles();
        $this->createCollectionItems();

        return $this->created;
    }

    /**
     * Deletes all created demo content
     * @param integer $store_id
     * @param \gplcart\modules\demo\models\Demo $model
     * @return bool
     */
    public function delete($store_id, $model)
    {
        set_time_limit(0);

        foreach ($model->get($store_id) as $entity => $ids) {

            if (!is_array($ids)) {
                continue;
            }

            foreach ($ids as $id) {
                switch ($entity) {
                    case 'product':
                        $this->product->delete($id, false);
                        break;
                    case 'category':
                        $this->category->delete($id, false);
                        break;
                    case 'category_group':
                        $this->category_group->delete($id, false);
                        break;
                    case 'collection':
                        $this->collection->delete($id, false);
                        break;
                    case 'collection_item':
                        $this->collection_item->delete($id);
                        break;
                    case 'file':
                        $db = $disk = 0;
                        $this->file->deleteAll($id, $db, $disk, false);
                        break;
                }
            }
        }

        return true;
    }

    /**
     * Creates collections
     */
    protected function createCollections()
    {
        $data = include __DIR__ . '/../config/default/collection.php';

        foreach ($data as $item) {
            $this->created['collection'][$item['title']] = $this->collection->add($item);
        }
    }

    /**
     * Creates files
     */
    protected function createFiles()
    {
        $data = include __DIR__ . '/../config/default/file.php';

        foreach ($data as $item) {

            $destination = $this->copyFile(realpath($item['path']), GC_DIR_IMAGE);

            if (!empty($destination)) {

                $file = array(
                    'title' => $item['title'],
                    'path' => gplcart_file_relative($destination)
                );

                $this->created['file'][$item['title']] = $this->file->add($file);
            }
        }
    }

    /**
     * Creates collection items
     */
    protected function createCollectionItems()
    {
        $data = include __DIR__ . '/../config/default/collection_item.php';

        foreach ($data as $item) {
            $this->created['collection_item'][] = $this->collection_item->add($item);
        }
    }

    /**
     * Create category groups
     */
    protected function createCategoryGroups()
    {
        $data = include __DIR__ . '/../config/default/category_group.php';

        foreach ($data as $item) {
            $this->created['category_group'][$item['title']] = $this->category_group->add($item);
        }
    }

    /**
     * Creates demo categories
     */
    protected function createCategories()
    {
        $data = include __DIR__ . '/../config/default/category.php';

        foreach ($data as $item) {
            $this->created['category'][$item['title']] = $this->category->add($item);
        }
    }

    /**
     * Creates demo products
     */
    protected function createProducts()
    {
        $data = include __DIR__ . '/../config/default/product.php';

        foreach ($data as $item) {
            $this->created['product'][$item['sku']] = $this->product->add($item);
        }
    }

    /**
     * Creates product images
     */
    protected function createProductImages()
    {
        foreach ($this->created['product'] as $sku => $product_id) {

            $directory = __DIR__ . "/../image/default/product/$sku";

            if (!is_dir($directory)) {
                continue;
            }

            $dirname = $this->config->get('product_image_dirname', 'product');
            $dir = gplcart_path_absolute($dirname, GC_DIR_IMAGE);

            foreach (glob("$directory/*.{jpg,png,gif}", GLOB_BRACE) as $file) {

                $destination = $this->copyFile($file, $dir);

                if (empty($destination)) {
                    continue;
                }

                $data = array(
                    'entity' => 'product',
                    'entity_id' => $product_id,
                    'path' => gplcart_file_relative($destination)
                );

                $this->created['file'][$destination] = $this->file->add($data);
            }
        }
    }

    /**
     * Copy files from module to file directory
     * @param string $source
     * @param string $directory
     * @return string
     */
    protected function copyFile($source, $directory)
    {
        if (file_exists($directory) || mkdir($directory, 0775, true)) {
            $destination = gplcart_file_unique("$directory/" . basename($source));
            return copy($source, $destination) ? $destination : '';
        }

        return '';
    }

}
