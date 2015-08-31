<?php

/**
 * Purge using AmazonSNS object
 */

w3_require_once(W3TC_LIB_W3_DIR . '/Enterprise/SnsBase.php');
w3_require_once(W3TC_LIB_DIR . '/SNS/services/MessageValidator/Message.php');
w3_require_once(W3TC_LIB_DIR . '/SNS/services/MessageValidator/MessageValidator.php');

/**
 * Class W3_Sns
 */
class W3_Enterprise_SnsServer extends W3_Enterprise_SnsBase {

    /**
     * Processes message from SNS
     *
     * @throws Exception
     */
    function process_message() {
        $this->_log('Received message');

        try {
            $message = Message::fromRawPostData();
            $validator = new MessageValidator();
            $error = '';
            if ($validator->isValid($message)) {
                if ($message->get('Type') == 'SubscriptionConfirmation')
                    $this->_subscription_confirmation($message);
                else if ($message->get('Type') == 'Notification')
                    $this->_notification($message->get('Message'));
            } else {
                $this->_log('Error processing message it was not valid.' );
            }
        } catch (Exception $e) {
            $this->_log('Error processing message: ' . $e->getMessage());
        }
        $this->_log('Message processed');
    }

    /**
     * Confirms subscription
     *
     * @param Message $message
     * @throws Exception
     */
    private function _subscription_confirmation($message) {
        $topic_arn = $this->_config->get_string('cluster.messagebus.sns.topic_arn');

        if ($topic_arn != $message->get('TopicArn'))
            throw new Exception ('Not my Topic. My is ' .
                $this->_topic_arn . ' while request came from ' .
            $message->get('TopicArn'));

        $this->_log('Issuing confirm_subscription');
        $response = $this->_get_api()->confirm_subscription(
            $topic_arn, $message->get('Token'));
        $this->_log('Subscription confirmed: ' .
            ($response->isOK() ? 'OK' : 'Error'));
    }
    
    /**
     * Processes notification
     *
     * @param array $v
     */
    private function _notification($v) {
        $m = json_decode($v, true);
        if (isset($m['hostname']))
            $this->_log('Message originated from hostname: ' . $m['hostname']);

        define('DOING_SNS', true);
        $this->_log('Actions executing');
        if (isset($m['actions'])) {
            $actions = $m['actions'];
            foreach($actions as $action)
                $this->_execute($action);
            do_action('sns_actions_executed');
        } else {
            $this->_execute($m['action']);
        }
        $this->_log('Actions executed');
    }

    /**
     * Execute action
     * @param $m
     * @throws Exception
     */
    private function _execute($m) {
        $action = $m['action'];
        $this->_log('Executing action ' . $action);
        //Needed for cache flushing
        $executor = w3_instance('W3_CacheFlushLocal');
        //Needed for cache cleanup
        $pgcache_admin = w3_instance('W3_Plugin_PgCacheAdmin');

        //See which message we got
        if ($action == 'dbcache_flush')
            $executor->dbcache_flush();
        else if ($action == 'objectcache_flush')
            $executor->objectcache_flush();
        else if ($action == 'fragmentcache_flush')
            $executor->fragmentcache_flush();
        else if ($action == 'fragmentcache_flush_group')
            $executor->fragmentcache_flush_group($m['group'], $m['global']);
        else if ($action == 'minifycache_flush')
            $executor->minifycache_flush();
        else if ($action == 'browsercache_flush')
            $executor->browsercache_flush();
        else if ($action == 'cdn_purge_files')
            $executor->cdn_purge_files($m['purgefiles']);
        else if ($action == 'pgcache_flush')
            $executor->pgcache_flush();
        else if ($action == 'pgcache_flush_post')
            $executor->pgcache_flush_post($m['post_id']);
        else if ($action == 'pgcache_flush_url')
            $executor->pgcache_flush_url($m['url']);
        else if ($action == 'pgcache_cleanup')
            $pgcache_admin->cleanup_local();
        else if ($action == 'varnish_flush_post')
            $executor->varnish_flush_post($m['post_id']);
        else if ($action == 'varnish_flush_url')
            $executor->varnish_flush_url($m['url']);
        else if ($action == 'varnish_flush')
            $executor->varnish_flush();
        else if ($action == 'cdncache_purge_post')
            $executor->cdncache_purge_post($m['post_id']);
        else if ($action == 'cdncache_purge')
            $executor->cdncache_purge();
        else if ($action == 'cdncache_purge_url')
            $executor->cdncache_purge_url($m['url']);
        else if ($action == 'apc_system_flush')
            $executor->apc_system_flush();
        else if ($action == 'apc_reload_file')
            $executor->apc_reload_file($m['filename']);
        else if ($action == 'apc_reload_files')
            $executor->apc_reload_files($m['filenames']);
        else if ($action == 'apc_delete_files_based_on_regex')
            $executor->apc_delete_files_based_on_regex($m['regex']);
        else if ($action == 'flush')
            $executor->flush();
        else if ($action == 'flush_all')
            $executor->flush_all();
        else if ($action == 'flush_post')
            $executor->flush_post($m['post_id']);
        else if ($action == 'flush_url')
            $executor->flush_url($m['url']);
        else if ($action == 'prime_post')
            $executor->prime_post($m['post_id']);
        else
            throw new Exception('Unknown action ' . $action);
        $this->_log('succeeded');
    }
}
