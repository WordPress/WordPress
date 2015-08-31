<?php
/**
 * Copyright 2010-2013 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

class Message
{
    protected static $requiredKeys = array(
        '__default' => array(
            'Message',
            'MessageId',
            'Timestamp',
            'TopicArn',
            'Type',
            'Signature',
            'SigningCertURL',
        ),
        'SubscriptionConfirmation' => array(
            'SubscribeURL',
            'Token'
        ),
        'UnsubscribeConfirmation' => array(
            'SubscribeURL',
            'Token'
        ),
    );

    protected static $signableKeys = array(
        'Message',
        'MessageId',
        'Subject',
        'SubscribeURL',
        'Timestamp',
        'Token',
        'TopicArn',
        'Type',
    );

    /**
     * @var array The message data
     */
    protected $data;

    /**
     * Creates a Message object from an array of raw message data
     *
     * @param array $data The message data
     *
     * @return Message
     * @throws InvalidArgumentException If a valid type is not provided or there are other required keys missing
     */
    public static function fromArray($data) {
        // Make sure the type key is set
        if (!isset($data['Type'])) {
            throw new InvalidArgumentException('The "Type" key must be provided to instantiate a Message object.');
        }

        // Determine required keys and create a collection from the message data
        $requiredKeys = array_merge(
            self::$requiredKeys['__default'],
            isset(self::$requiredKeys[$data['Type']]) ? self::$requiredKeys[$data['Type']] : array()
        );
        $data = array_merge($requiredKeys, $data);

        return new self($data);
    }

    /**
     * Creates a message object from the raw POST data
     *
     * @return Message
     * @throws UnexpectedValueException If the POST data is absent, or not a valid JSON document
     */
    public static function fromRawPostData() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            throw new UnexpectedValueException('POST data absent, or not a valid JSON document', json_last_error());
        }
        return self::fromArray($data);
    }

    /**
     * @param array $data A Collection of message data with all required keys
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Get the entire message data as a Collection
     *
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Gets a single key from the message data
     *
     * @param string $key
     * @return string
     */
    public function get($key) {
        return $this->data[$key];
    }

    /**
     * Builds a newline delimited string to sign according to the specs
     *
     * @return string
     * @link http://docs.aws.amazon.com/sns/latest/gsg/SendMessageToHttp.verify.signature.html
     */
    public function getStringToSign() {
        $stringToSign = '';

        $data = $this->data;
        ksort($data);
        $has = array();
        foreach ($data as $key => $value) {
            if (in_array($key, self::$signableKeys) && !in_array($key, $has)) {
                $stringToSign .= "{$key}\n{$value}\n";
                $has[] = $key;
            }
        }

        return $stringToSign;
    }
}
