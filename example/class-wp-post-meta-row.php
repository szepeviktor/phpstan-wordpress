<?php

/**
 * Value object for postmeta database table rows.
 *
 * Created to aid static analysis by PHPStan.
 *
 * @package WordPress
 * @see wp_get_db_schema()
 */

/**
 * List fields of postmeta database table as object properties.
 */
class WP_Post_Meta_Row {

    /**
     * @var int
     */
    public $meta_id;

    /**
     * @var int
     */
    public $post_id;

    /**
     * @var string
     */
    public $meta_key;

    /**
     * @var string
     */
    public $meta_value;
}
