<?php
if (!defined('W3TC')) {
    die();
}
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');

class W3_AdminActions_AwsActionsAdmin {

    /**
     * @var W3_Config $_config
     */
    private $_config = null;

    /**
     * @var W3_ConfigAdmin
     */
    private $_config_admin = null;

    function __construct() {
        $this->_config = w3_instance('W3_Config');
        $this->_config_admin = w3_instance('W3_ConfigAdmin');
    }

    /**
     * @throws Exception
     */
    function action_aws_sns_subscribe() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $topic_arn = W3_Request::get_string('cluster_messagebus_sns_topic_arn_subscribe');

        /**
         * @var W3_Enterprise_SnsClient $sns_client
         */
        $sns_client = w3_instance('W3_Enterprise_SnsClient');
        $sns_client->subscribe(plugins_url('pub/sns.php', W3TC_FILE), $topic_arn);
        try {
            $this->_config->set('cluster.messagebus.sns.topic_arn', $topic_arn);
            $this->_config->save();
        } catch (Exception $ex) {
            throw new Exception(
                '<strong>Can\'t change configuration</strong>: ' .
                $ex->getMessage());
        }
        w3_admin_redirect(array(
            'w3tc_note' => 'sns_subscribed'
        ));
    }
}