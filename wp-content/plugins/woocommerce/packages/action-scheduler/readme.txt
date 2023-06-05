=== Action Scheduler ===
Contributors: Automattic, wpmuguru, claudiosanches, peterfabian1000, vedjain, jamosova, obliviousharmony, konamiman, sadowski, royho, barryhughes-1
Tags: scheduler, cron
Requires at least: 5.2
Tested up to: 6.0
Stable tag: 3.5.4
License: GPLv3
Requires PHP: 5.6

Action Scheduler - Job Queue for WordPress

== Description ==

Action Scheduler is a scalable, traceable job queue for background processing large sets of actions in WordPress. It's specially designed to be distributed in WordPress plugins.

Action Scheduler works by triggering an action hook to run at some time in the future. Each hook can be scheduled with unique data, to allow callbacks to perform operations on that data. The hook can also be scheduled to run on one or more occassions.

Think of it like an extension to `do_action()` which adds the ability to delay and repeat a hook.

## Battle-Tested Background Processing

Every month, Action Scheduler processes millions of payments for [Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/), webhooks for [WooCommerce](https://wordpress.org/plugins/woocommerce/), as well as emails and other events for a range of other plugins.

It's been seen on live sites processing queues in excess of 50,000 jobs and doing resource intensive operations, like processing payments and creating orders, at a sustained rate of over 10,000 / hour without negatively impacting normal site operations.

This is all on infrastructure and WordPress sites outside the control of the plugin author.

If your plugin needs background processing, especially of large sets of tasks, Action Scheduler can help.

## Learn More

To learn more about how to Action Scheduler works, and how to use it in your plugin, check out the docs on [ActionScheduler.org](https://actionscheduler.org).

There you will find:

* [Usage guide](https://actionscheduler.org/usage/): instructions on installing and using Action Scheduler
* [WP CLI guide](https://actionscheduler.org/wp-cli/): instructions on running Action Scheduler at scale via WP CLI
* [API Reference](https://actionscheduler.org/api/): complete reference guide for all API functions
* [Administration Guide](https://actionscheduler.org/admin/): guide to managing scheduled actions via the administration screen
* [Guide to Background Processing at Scale](https://actionscheduler.org/perf/): instructions for running Action Scheduler at scale via the default WP Cron queue runner

## Credits

Action Scheduler is developed and maintained by [Automattic](http://automattic.com/) with significant early development completed by [Flightless](https://flightless.us/).

Collaboration is cool. We'd love to work with you to improve Action Scheduler. [Pull Requests](https://github.com/woocommerce/action-scheduler/pulls) welcome.

== Changelog ==

= 3.5.4 - 2023-01-17 =
* Add pre filters during action registration.
* Async scheduling.
* Calculate timeouts based on total actions.
* Correctly order the parameters for `ActionScheduler_ActionFactory`'s calls to `single_unique`.
* Fetch action in memory first before releasing claim to avoid deadlock.
* PHP 8.2: declare property to fix creation of dynamic property warning.
* PHP 8.2: fix "Using ${var} in strings is deprecated, use {$var} instead".
* Prevent `undefined variable` warning for `$num_pastdue_actions`.

= 3.5.3 - 2022-11-09 =
* Query actions with partial match.

= 3.5.2 - 2022-09-16 =
* Fix - erroneous 3.5.1 release.

= 3.5.1 - 2022-09-13 =
* Maintenance on A/S docs.
* fix: PHP 8.2 deprecated notice.

= 3.5.0 - 2022-08-25 =
* Add - The active view link within the "Tools > Scheduled Actions" screen is now clickable.
* Add - A warning when there are past-due actions.
* Enhancement - Added the ability to schedule unique actions via an atomic operation.
* Enhancement - Improvements to cache invalidation when processing batches (when running on WordPress 6.0+).
* Enhancement - If a recurring action is found to be consistently failing, it will stop being rescheduled.
* Enhancement - Adds a new "Past Due" view to the scheduled actions list table.

= 3.4.2 - 2022-06-08 =
* Fix - Change the include for better linting.
* Fix - update: Added Action scheduler completed action hook.

= 3.4.1 - 2022-05-24 =
* Fix - Change the include for better linting.
* Fix - Fix the documented return type.

= 3.4.0 - 2021-10-29 =
* Enhancement - Number of items per page can now be set for the Scheduled Actions view (props @ovidiul). #771
* Fix - Do not lower the max_execution_time if it is already set to 0 (unlimited) (props @barryhughes). #755
* Fix - Avoid triggering autoloaders during the version resolution process (props @olegabr). #731 & #776
* Dev - ActionScheduler_wcSystemStatus PHPCS fixes (props @ovidiul). #761
* Dev - ActionScheduler_DBLogger.php PHPCS fixes (props @ovidiul). #768
* Dev - Fixed phpcs for ActionScheduler_Schedule_Deprecated (props @ovidiul). #762
* Dev - Improve actions table indicies (props @glagonikas). #774 & #777
* Dev - PHPCS fixes for ActionScheduler_DBStore.php (props @ovidiul). #769 & #778
* Dev - PHPCS Fixes for ActionScheduler_Abstract_ListTable (props @ovidiul). #763 & #779
* Dev - Adds new filter action_scheduler_claim_actions_order_by to allow tuning of the claim query (props @glagonikas). #773
* Dev - PHPCS fixes for ActionScheduler_WpPostStore class (props @ovidiul). #780

= 3.3.0 - 2021-09-15 =
* Enhancement - Adds as_has_scheduled_action() to provide a performant way to test for existing actions. #645
* Fix - Improves compatibility with environments where NO_ZERO_DATE is enabled. #519
* Fix - Adds safety checks to guard against errors when our database tables cannot be created. #645
* Dev - Now supports queries that use multiple statuses. #649
* Dev - Minimum requirements for WordPress and PHP bumped (to 5.2 and 5.6 respectively). #723

= 3.2.1 - 2021-06-21 =
* Fix - Add extra safety/account for different versions of AS and different loading patterns. #714
* Fix - Handle hidden columns (Tools → Scheduled Actions) | #600.

= 3.2.0 - 2021-06-03 =
* Fix - Add "no ordering" option to as_next_scheduled_action().
* Fix - Add secondary scheduled date checks when claiming actions (DBStore) | #634.
* Fix - Add secondary scheduled date checks when claiming actions (wpPostStore) | #634.
* Fix - Adds a new index to the action table, reducing the potential for deadlocks (props: @glagonikas).
* Fix - Fix unit tests infrastructure and adapt tests to PHP 8.
* Fix - Identify in-use data store.
* Fix - Improve test_migration_is_scheduled.
* Fix - PHP notice on list table.
* Fix - Speed up clean up and batch selects.
* Fix - Update pending dependencies.
* Fix - [PHP 8.0] Only pass action arg values through to do_action_ref_array().
* Fix - [PHP 8] Set the PHP version to 7.1 in composer.json for PHP 8 compatibility.
* Fix - add is_initialized() to docs.
* Fix - fix file permissions.
* Fix - fixes #664 by replacing __ with esc_html__.
