<?php
/**
 * Allow usage of $wpdb.
 */

global $wpdb;
$wpdb = new \wpdb( '', '', '', '' );
