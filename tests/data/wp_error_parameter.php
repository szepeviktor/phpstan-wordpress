<?php

/**
 * Test data for WPErrorParameterDynamicFunctionReturnTypeExtension.
 */

declare(strict_types=1);

namespace SzepeViktor\PHPStan\WordPress\Tests;

use function PHPStan\Testing\assertType;
use function wp_clear_scheduled_hook;
use function wp_insert_attachment;
use function wp_insert_category;
use function wp_insert_link;
use function wp_insert_post;
use function wp_reschedule_event;
use function wp_schedule_event;
use function wp_schedule_single_event;
use function wp_set_comment_status;
use function wp_unschedule_event;
use function wp_unschedule_hook;
use function wp_update_comment;
use function wp_update_post;

/**
 * wp_insert_link()
 */
$value = wp_insert_link([]);
assertType('int<0, max>', $value);

$value = wp_insert_link([], false);
assertType('int<0, max>', $value);

$value = wp_insert_link([], true);
assertType('int<1, max>|WP_Error', $value);

$value = wp_insert_link([], $_GET['wp_error']);
assertType('int<0, max>|WP_Error', $value);

/**
 * wp_insert_category()
 */
$value = wp_insert_category([]);
assertType('int<0, max>', $value);

$value = wp_insert_category([], false);
assertType('int<0, max>', $value);

$value = wp_insert_category([], true);
assertType('int<1, max>|WP_Error', $value);

$value = wp_insert_category([], $_GET['wp_error']);
assertType('int<0, max>|WP_Error', $value);

/**
 * wp_set_comment_status()
 */
$value = wp_set_comment_status(1, 'spam');
assertType('bool', $value);

$value = wp_set_comment_status(1, 'spam', false);
assertType('bool', $value);

$value = wp_set_comment_status(1, 'spam', true);
assertType('WP_Error|true', $value);

$value = wp_set_comment_status(1, 'spam', $_GET['wp_error']);
assertType('bool|WP_Error', $value);

/**
 * wp_update_comment()
 */
$value = wp_update_comment([]);
assertType('0|1|false', $value);

$value = wp_update_comment([], false);
assertType('0|1|false', $value);

$value = wp_update_comment([], true);
assertType('0|1|WP_Error', $value);

$value = wp_update_comment([], $_GET['wp_error']);
assertType('0|1|WP_Error|false', $value);

/**
 * wp_schedule_single_event()
 */
$value = wp_schedule_single_event(1, 'hook');
assertType('bool', $value);

$value = wp_schedule_single_event(1, 'hook', []);
assertType('bool', $value);

$value = wp_schedule_single_event(1, 'hook', [], false);
assertType('bool', $value);

$value = wp_schedule_single_event(1, 'hook', [], true);
assertType('WP_Error|true', $value);

$value = wp_schedule_single_event(1, 'hook', [], $_GET['wp_error']);
assertType('bool|WP_Error', $value);

/**
 * wp_schedule_event()
 */
$value = wp_schedule_event(1, 'daily', 'hook');
assertType('bool', $value);

$value = wp_schedule_event(1, 'daily', 'hook', []);
assertType('bool', $value);

$value = wp_schedule_event(1, 'daily', 'hook', [], false);
assertType('bool', $value);

$value = wp_schedule_event(1, 'daily', 'hook', [], true);
assertType('WP_Error|true', $value);

$value = wp_schedule_event(1, 'daily', 'hook', [], $_GET['wp_error']);
assertType('bool|WP_Error', $value);

/**
 * wp_reschedule_event()
 */
$value = wp_reschedule_event(1, 'daily', 'hook');
assertType('bool', $value);

$value = wp_reschedule_event(1, 'daily', 'hook', []);
assertType('bool', $value);

$value = wp_reschedule_event(1, 'daily', 'hook', [], false);
assertType('bool', $value);

$value = wp_reschedule_event(1, 'daily', 'hook', [], true);
assertType('WP_Error|true', $value);

$value = wp_reschedule_event(1, 'daily', 'hook', [], $_GET['wp_error']);
assertType('bool|WP_Error', $value);

/**
 * wp_unschedule_event()
 */
$value = wp_unschedule_event(1, 'hook');
assertType('bool', $value);

$value = wp_unschedule_event(1, 'hook', []);
assertType('bool', $value);

$value = wp_unschedule_event(1, 'hook', [], false);
assertType('bool', $value);

$value = wp_unschedule_event(1, 'hook', [], true);
assertType('WP_Error|true', $value);

$value = wp_unschedule_event(1, 'hook', [], $_GET['wp_error']);
assertType('bool|WP_Error', $value);

/**
 * wp_clear_scheduled_hook()
 */
$value = wp_clear_scheduled_hook('hook');
assertType('int<0, max>|false', $value);

$value = wp_clear_scheduled_hook('hook', []);
assertType('int<0, max>|false', $value);

$value = wp_clear_scheduled_hook('hook', [], false);
assertType('int<0, max>|false', $value);

$value = wp_clear_scheduled_hook('hook', [], true);
assertType('int<0, max>|WP_Error', $value);

$value = wp_clear_scheduled_hook('hook', [], $_GET['wp_error']);
assertType('int<0, max>|WP_Error|false', $value);

/**
 * wp_unschedule_hook()
 */
$value = wp_unschedule_hook('hook');
assertType('int<0, max>|false', $value);

$value = wp_unschedule_hook('hook', false);
assertType('int<0, max>|false', $value);

$value = wp_unschedule_hook('hook', true);
assertType('int<0, max>|WP_Error', $value);

$value = wp_unschedule_hook('hook', $_GET['wp_error']);
assertType('int<0, max>|WP_Error|false', $value);

/**
 * _set_cron_array()
 */
$value = _set_cron_array([]);
assertType('bool', $value);

$value = _set_cron_array([], false);
assertType('bool', $value);

$value = _set_cron_array([], true);
assertType('WP_Error|true', $value);

$value = _set_cron_array([], $_GET['wp_error']);
assertType('bool|WP_Error', $value);

/**
 * wp_insert_post()
 */
$value = wp_insert_post([]);
assertType('int<0, max>', $value);

$value = wp_insert_post([], false);
assertType('int<0, max>', $value);

$value = wp_insert_post([], false, true);
assertType('int<0, max>', $value);

$value = wp_insert_post([], false, false);
assertType('int<0, max>', $value);

$value = wp_insert_post([], true);
assertType('int<1, max>|WP_Error', $value);

$value = wp_insert_post([], true, true);
assertType('int<1, max>|WP_Error', $value);

$value = wp_insert_post([], true, false);
assertType('int<1, max>|WP_Error', $value);

$value = wp_insert_post([], $_GET['wp_error']);
assertType('int<0, max>|WP_Error', $value);

$value = wp_insert_post([], $_GET['wp_error'], true);
assertType('int<0, max>|WP_Error', $value);

$value = wp_insert_post([], $_GET['wp_error'], false);
assertType('int<0, max>|WP_Error', $value);

/**
 * wp_update_post()
 */
$value = wp_update_post([]);
assertType('int<0, max>', $value);

$value = wp_update_post([], false);
assertType('int<0, max>', $value);

$value = wp_update_post([], false, true);
assertType('int<0, max>', $value);

$value = wp_update_post([], false, false);
assertType('int<0, max>', $value);

$value = wp_update_post([], true);
assertType('int<1, max>|WP_Error', $value);

$value = wp_update_post([], true, true);
assertType('int<1, max>|WP_Error', $value);

$value = wp_update_post([], true, false);
assertType('int<1, max>|WP_Error', $value);

$value = wp_update_post([], $_GET['wp_error']);
assertType('int<0, max>|WP_Error', $value);

$value = wp_update_post([], $_GET['wp_error'], true);
assertType('int<0, max>|WP_Error', $value);

$value = wp_update_post([], $_GET['wp_error'], false);
assertType('int<0, max>|WP_Error', $value);

/**
 * wp_insert_attachment()
 */
$value = wp_insert_attachment([]);
assertType('int<0, max>', $value);

$value = wp_insert_attachment([], true);
assertType('int<0, max>', $value);

$value = wp_insert_attachment([], false);
assertType('int<0, max>', $value);

$value = wp_insert_attachment([], true, 1);
assertType('int<0, max>', $value);

$value = wp_insert_attachment([], true, 1, false);
assertType('int<0, max>', $value);

$value = wp_insert_attachment([], true, 1, false, false);
assertType('int<0, max>', $value);

$value = wp_insert_attachment([], true, 1, false, true);
assertType('int<0, max>', $value);

$value = wp_insert_attachment([], false, 0, true);
assertType('int<1, max>|WP_Error', $value);

$value = wp_insert_attachment([], false, 0, true, true);
assertType('int<1, max>|WP_Error', $value);

$value = wp_insert_attachment([], false, 0, true, false);
assertType('int<1, max>|WP_Error', $value);

$value = wp_insert_attachment([], true, 1, $_GET['wp_error']);
assertType('int<0, max>|WP_Error', $value);

$value = wp_insert_attachment([], true, 1, $_GET['wp_error'], true);
assertType('int<0, max>|WP_Error', $value);

$value = wp_insert_attachment([], true, 1, $_GET['wp_error'], false);
assertType('int<0, max>|WP_Error', $value);
