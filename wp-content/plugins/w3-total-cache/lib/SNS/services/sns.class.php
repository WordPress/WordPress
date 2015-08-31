<?php
/*
 * Copyright 2010-2011 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

/**

 *
 * @version Thu Sep 01 21:23:56 PDT 2011
 * @license See the included NOTICE.md file for complete information.
 * @copyright See the included NOTICE.md file for complete information.
 * @link http://aws.amazon.com/sns/Amazon Simple Notification Service
 * @link http://aws.amazon.com/documentation/sns/Amazon Simple Notification Service documentation
 */
class AmazonSNS extends CFRuntime
{

	/*%******************************************************************************************%*/
	// CLASS CONSTANTS

	/**
	 * Specify the default queue URL.
	 */
	const DEFAULT_URL = 'sns.us-east-1.amazonaws.com';

	/**
	 * Specify the queue URL for the US-East (Northern Virginia) Region.
	 */
	const REGION_US_E1 = self::DEFAULT_URL;

	/**
	 * Specify the queue URL for the US-West (Northern California) Region.
	 */
	const REGION_US_W1 = 'sns.us-west-1.amazonaws.com';

	/**
	 * Specify the queue URL for the EU (Ireland) Region.
	 */
	const REGION_EU_W1 = 'sns.eu-west-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Asia Pacific (Singapore) Region.
	 */
	const REGION_APAC_SE1 = 'sns.ap-southeast-1.amazonaws.com';

	/**
	 * Specify the queue URL for the Asia Pacific (Japan) Region.
	 */
	const REGION_APAC_NE1 = 'sns.ap-northeast-1.amazonaws.com';


	/*%******************************************************************************************%*/
	// SETTERS

	/**
	 * This allows you to explicitly sets the region for the service to use.
	 *
	 * @param string $region (Required) The region to use for subsequent Amazon S3 operations. [Allowed values: `AmazonSNS::REGION_US_E1 `, `AmazonSNS::REGION_US_W1`, `AmazonSNS::REGION_EU_W1`, `AmazonSNS::REGION_APAC_SE1`]
	 * @return $this A reference to the current instance.
	 */
	public function set_region($region)
	{
		$this->set_hostname($region);
		return $this;
	}


	/*%******************************************************************************************%*/
	// CONVENIENCE METHODS

	/**
	 * Gets a simple list of Topic ARNs.
	 *
	 * @param string $pcre (Optional) A Perl-Compatible Regular Expression (PCRE) to filter the names against.
	 * @return array A list of Topic ARNs.
	 * @link http://php.net/pcre Perl-Compatible Regular Expression (PCRE) Docs
	 */
	public function get_topic_list($pcre = null)
	{
		if ($this->use_batch_flow)
		{
			throw new SNS_Exception(__FUNCTION__ . '() cannot be batch requested');
		}

		// Get a list of topics.
		$list = $this->list_topics();
		if ($list = $list->body->TopicArn())
		{
			$list = $list->map_string($pcre);
			return $list;
		}

		return array();
	}


	/*%******************************************************************************************%*/
	// CONSTRUCTOR

	/**
	 * Constructs a new instance of <AmazonSNS>. If the <code>AWS_DEFAULT_CACHE_CONFIG</code> configuration
	 * option is set, requests will be authenticated using a session token. Otherwise, requests will use
	 * the older authentication method.
	 *
	 * @param string $key (Optional) Your AWS key, or a session key. If blank, it will look for the <code>AWS_KEY</code> constant.
	 * @param string $secret_key (Optional) Your AWS secret key, or a session secret key. If blank, it will look for the <code>AWS_SECRET_KEY</code> constant.
	 * @param string $token (optional) An AWS session token. If blank, a request will be made to the AWS Secure Token Service to fetch a set of session credentials.
	 * @return boolean A value of <code>false</code> if no valid values are set, otherwise <code>true</code>.
	 */
	public function __construct($key = null, $secret_key = null, $token = null)
	{
		$this->api_version = '2010-03-31';
		$this->hostname = self::DEFAULT_URL;

		if (!$key && !defined('AWS_KEY'))
		{
			// @codeCoverageIgnoreStart
			throw new SNS_Exception('No account key was passed into the constructor, nor was it set in the AWS_KEY constant.');
			// @codeCoverageIgnoreEnd
		}

		if (!$secret_key && !defined('AWS_SECRET_KEY'))
		{
			// @codeCoverageIgnoreStart
			throw new SNS_Exception('No account secret was passed into the constructor, nor was it set in the AWS_SECRET_KEY constant.');
			// @codeCoverageIgnoreEnd
		}

		if (defined('AWS_DEFAULT_CACHE_CONFIG') && AWS_DEFAULT_CACHE_CONFIG)
		{
			return parent::session_based_auth($key, $secret_key, $token);
		}

		return parent::__construct($key, $secret_key);
	}


	/*%******************************************************************************************%*/
	// SERVICE METHODS

	/**
	 *
	 * The ConfirmSubscription action verifies an endpoint owner's intent to receive messages by validating the token sent to the endpoint by an
	 * earlier Subscribe action. If the token is valid, the action creates a new subscription and returns its Amazon Resource Name (ARN). This call
	 * requires an AWS signature only when the AuthenticateOnUnsubscribe flag is set to "true".
	 *
	 * @param string $topic_arn (Required) The ARN of the topic for which you wish to confirm a subscription.
	 * @param string $token (Required) Short-lived token sent to an endpoint during the Subscribe action.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>AuthenticateOnUnsubscribe</code> - <code>string</code> - Optional - Indicates that you want to disable unauthenticated unsubsciption of the subscription. If parameter is present in the request, the request has an AWS signature, and the value of this parameter is true, only the topic owner and the subscription owner will be permitted to unsubscribe the endopint, and the Unsubscribe action will require AWS authentication. </li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function confirm_subscription($topic_arn, $token, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		$opt['Token'] = $token;

		return $this->authenticate('ConfirmSubscription', $opt, $this->hostname);
	}

	/**
	 *
	 * The GetTopicAttribtues action returns all of the properties of a topic customers have created. Topic properties returned might differ based
	 * on the authorization of the user.
	 *
	 * @param string $topic_arn (Required) The ARN of the topic whose properties you want to get.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function get_topic_attributes($topic_arn, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;

		return $this->authenticate('GetTopicAttributes', $opt, $this->hostname);
	}

	/**
	 *
	 * The Subscribe action prepares to subscribe an endpoint by sending the endpoint a confirmation message. To actually create a subscription,
	 * the endpoint owner must call the ConfirmSubscription action with the token from the confirmation message. Confirmation tokens are valid for
	 * twenty-four hours.
	 *
	 * @param string $topic_arn (Required) The ARN of topic you want to subscribe to.
	 * @param string $protocol (Required) The protocol you want to use. Supported protocols include: <ul> <li>http -- delivery of JSON-encoded message via HTTP POST</li><li>https -- delivery of JSON-encoded message via HTTPS POST</li><li>email -- delivery of message via SMTP</li><li>email-json -- delivery of JSON-encoded message via SMTP</li><li>sqs -- delivery of JSON-encoded message to an Amazon SQS queue</li> </ul>
	 * @param string $endpoint (Required) The endpoint that you want to receive notifications. Endpoints vary by protocol: <ul> <li>For the http protocol, the endpoint is an URL beginning with "http://"</li><li>For the https protocol, the endpoint is a URL beginning with "https://"</li><li>For the email protocol, the endpoint is an e-mail address</li><li>For the email-json protocol, the endpoint is an e-mail address</li><li>For the sqs protocol, the endpoint is the ARN of an Amazon SQS queue</li> </ul>
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function subscribe($topic_arn, $protocol, $endpoint, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		$opt['Protocol'] = $protocol;
		$opt['Endpoint'] = $endpoint;

		return $this->authenticate('Subscribe', $opt, $this->hostname);
	}

	/**
	 *
	 * The SetTopicAttributes action allows a topic owner to set an attribute of the topic to a new value.
	 *
	 * @param string $topic_arn (Required) The ARN of the topic to modify.
	 * @param string $attribute_name (Required) The name of the attribute you want to set. Only a subset of the topic's attributes are mutable.
	 * @param string $attribute_value (Required) The new value for the attribute.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function set_topic_attributes($topic_arn, $attribute_name, $attribute_value, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		$opt['AttributeName'] = $attribute_name;
		$opt['AttributeValue'] = $attribute_value;

		return $this->authenticate('SetTopicAttributes', $opt, $this->hostname);
	}

	/**
	 *
	 * The DeleteTopic action deletes a topic and all its subscriptions. Deleting a topic might prevent some messages previously sent to the topic
	 * from being delivered to subscribers. This action is idempotent, so deleting a topic that does not exist will not result in an error.
	 *
	 * @param string $topic_arn (Required) The ARN of the topic you want to delete.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function delete_topic($topic_arn, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;

		return $this->authenticate('DeleteTopic', $opt, $this->hostname);
	}

	/**
	 *
	 * The RemovePermission action removes a statement from a topic's access control policy.
	 *
	 * @param string $topic_arn (Required) The ARN of the topic whose access control policy you wish to modify.
	 * @param string $label (Required) The unique label of the statement you want to remove.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function remove_permission($topic_arn, $label, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		$opt['Label'] = $label;

		return $this->authenticate('RemovePermission', $opt, $this->hostname);
	}

	/**
	 *
	 * The ListSubscriptions action returns a list of the requester's subscriptions. Each call returns a limited list of subscriptions. If there
	 * are more subscriptions, a NextToken is also returned. Use the NextToken parameter in a new ListSubscriptions call to get further results.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - Token returned by the previous ListSubscriptions request. </li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_subscriptions($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('ListSubscriptions', $opt, $this->hostname);
	}

	/**
	 *
	 * The AddPermission action adds a statement to a topic's access control policy, granting access for the specified AWS accounts to the
	 * specified actions.
	 *
	 * @param string $topic_arn (Required) The ARN of the topic whose access control policy you wish to modify.
	 * @param string $label (Required) A unique identifier for the new policy statement.
	 * @param string|array $account_id (Required) The AWS account IDs of the users (principals) who will be given access to the specified actions. The users must have AWS accounts, but do not need to be signed up for this service.  Pass a string for a single value, or an indexed array for multiple values.
	 * @param string|array $action_name (Required) The action you want to allow for the specified principal(s).  Pass a string for a single value, or an indexed array for multiple values.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function add_permission($topic_arn, $label, $account_id, $action_name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		$opt['Label'] = $label;

		// Required parameter
		$opt = array_merge($opt, CFComplexType::map(array(
			'AWSAccountId' => (is_array($account_id) ? $account_id : array($account_id))
		), 'member'));

		// Required parameter
		$opt = array_merge($opt, CFComplexType::map(array(
			'ActionName' => (is_array($action_name) ? $action_name : array($action_name))
		), 'member'));

		return $this->authenticate('AddPermission', $opt, $this->hostname);
	}

	/**
	 *
	 * The CreateTopic action creates a topic to which notifications can be published. Users can create at most 25 topics. This action is
	 * idempotent, so if the requester already owns a topic with the specified name, that topic's ARN will be returned without creating a new
	 * topic.
	 *
	 * @param string $name (Required) The name of the topic you want to create. Constraints: Topic names must be made up of only uppercase and lowercase ASCII letters, numbers, and hyphens, and must be between 1 and 256 characters long.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function create_topic($name, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['Name'] = $name;

		return $this->authenticate('CreateTopic', $opt, $this->hostname);
	}

	/**
	 *
	 * The ListTopics action returns a list of the requester's topics. Each call returns a limited list of topics. If there are more topics, a
	 * NextToken is also returned. Use the NextToken parameter in a new ListTopics call to get further results.
	 *
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - Token returned by the previous ListTopics request. </li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_topics($opt = null)
	{
		if (!$opt) $opt = array();

		return $this->authenticate('ListTopics', $opt, $this->hostname);
	}

	/**
	 *
	 * The Unsubscribe action deletes a subscription. If the subscription requires authentication for deletion, only the owner of the subscription
	 * or the its topic's owner can unsubscribe, and an AWS signature is required. If the Unsubscribe call does not require authentication and the
	 * requester is not the subscription owner, a final cancellation message is delivered to the endpoint, so that the endpoint owner can easily
	 * resubscribe to the topic if the Unsubscribe request was unintended.
	 *
	 * @param string $subscription_arn (Required) The ARN of the subscription to be deleted.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function unsubscribe($subscription_arn, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['SubscriptionArn'] = $subscription_arn;

		return $this->authenticate('Unsubscribe', $opt, $this->hostname);
	}

	/**
	 *
	 * The ListSubscriptionsByTopic action returns a list of the subscriptions to a specific topic. Each call returns a limited list of
	 * subscriptions. If there are more subscriptions, a NextToken is also returned. Use the NextToken parameter in a new ListSubscriptionsByTopic
	 * call to get further results.
	 *
	 * @param string $topic_arn (Required) The ARN of the topic for which you wish to find subscriptions.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>NextToken</code> - <code>string</code> - Optional - Token returned by the previous ListSubscriptionsByTopic request. </li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function list_subscriptions_by_topic($topic_arn, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;

		return $this->authenticate('ListSubscriptionsByTopic', $opt, $this->hostname);
	}

	/**
	 *
	 * The Publish action sends a message to all of a topic's subscribed endpoints. When a messageId is returned, the message has been saved and
	 * Amazon SNS will attempt to deliver it to the topic's subscribers shortly. The format of the outgoing message to each subscribed endpoint
	 * depends on the notification protocol selected.
	 *
	 * @param string $topic_arn (Required) The topic you want to publish to.
	 * @param string $message (Required) The message you want to send to the topic. Constraints: Messages must be UTF-8 encoded strings at most 8 KB in size (8192 bytes, not 8192 characters).
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>Subject</code> - <code>string</code> - Optional - Optional parameter to be used as the "Subject" line of when the message is delivered to e-mail endpoints. This field will also be included, if present, in the standard JSON messages delivered to other endpoints. Constraints: Subjects must be ASCII text that begins with a letter, number or punctuation mark; must not include line breaks or control characters; and must be less than 100 characters long. </li>
	 * 	<li><code>curlopts</code> - <code>array</code> - Optional - A set of values to pass directly into <code>curl_setopt()</code>, where the key is a pre-defined <code>CURLOPT_*</code> constant.</li>
	 * 	<li><code>returnCurlHandle</code> - <code>boolean</code> - Optional - A private toggle specifying that the cURL handle be returned rather than actually completing the request. This toggle is useful for manually managed batch requests.</li></ul>
	 * @return CFResponse A <CFResponse> object containing a parsed HTTP response.
	 */
	public function publish($topic_arn, $message, $opt = null)
	{
		if (!$opt) $opt = array();
		$opt['TopicArn'] = $topic_arn;
		$opt['Message'] = $message;

		return $this->authenticate('Publish', $opt, $this->hostname);
	}
}


/*%******************************************************************************************%*/
// EXCEPTIONS

/**
 * Default SNS Exception.
 */
class SNS_Exception extends Exception {}