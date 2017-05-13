<?php

/**
 * @package Demo
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */
return array(
    array(
        'status' => 1,
        'type' => 'file',
        'title' => 'Banners',
        'store_id' => $this->store_id,
        'description' => 'Block with banners on the front page',
    ),
    array(
        'status' => 1,
        'type' => 'product',
        'title' => 'Featured products',
        'store_id' => $this->store_id,
        'description' => 'Block with featured products on the front page',
    ),
    array(
        'status' => 1,
        'type' => 'page',
        'title' => 'News/articles',
        'store_id' => $this->store_id,
        'description' => 'Block with news/articles on the front page',
    )
);

