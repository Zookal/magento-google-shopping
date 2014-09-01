<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Varien
 * @package     Varien_Gdata
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Exception class parse google responses to human readble format
 *
 * @category    Varien
 * @package     Varien_Gdata
 */
class Varien_Gdata_Gshopping_HttpException extends Zend_Gdata_App_HttpException
{
    /**
     * Array of descriptions for Google's error codes.
     * array('code' => 'description')
     *
     * @var array
     */
    protected $_errors = array(
        'auth/frontend/adwords' => "Your AdWords advertisements are not running. You need to activate your AdWords account.",
        'auth/frontend/checkout' => "Google Checkout not enabled",
        'auth/frontend/feed_config' => "You have not set your data feed to go live",
        'auth/frontend/missing_homepage' => "You haven't specified your website's URL on the settings page before uploading items.",
        'auth/frontend/not_claimed' => "You didn't verify and claim your website's URL.",
        'auth/frontend/terms_of_service' => "You didn't sign the Merchant Center's terms of service.",
        'quota/geo_mutation' => "Geo quota exceeded.",
        'quota/geo_request' => "Geo quota exceeded.",
        'quota/too_many_feeds' => "You have too many data feeds registered.",
        'quota/too_many_requests' => "You executed too many requests.",
        'quota/too_many_subaccounts' => "You have too many subaccounts registered.",
        'validation/checkout_not_supported' => "Google Checkout not supported for this type of item.",
        'validation/feed' => "Multiple reasons why a data feed upload failed.",
        'validation/internal' => "Internal error during validation. Please retry inserting the item. If the problem persist, please report this issue to us together with the request you're trying to execute.",
        'validation/invalid_attribute' => "Error code covering various reasons why the attribute is invalid.",
        'validation/invalid_attribute/duplicate' => "The item contains two identical attributes (with identical values).",
        'validation/invalid_attribute/reserved' => "The attribute you're trying to insert uses a name reserved by Content API for Shopping.",
        'validation/invalid_attribute/too_many' => "The attribute has too many values.",
        'validation/invalid_attribute/unknown' => "The attribute is not supported.",
        'validation/invalid_character' => "There is a problem with the character encoding of this attribute.",
        'validation/invalid_destination' => "Specified destination is not supported. See the Destinations section for more information.",
        'validation/invalid_format' => "The format of the attribute value is not valid (e.g. dates, numbers).",
        'validation/invalid_item' => "Generic error code for an invalid item. Check the content of the <internalReason> element for more information of the reason.",
        'validation/invalid_value' => "Generic error code for an invalid value of an attribute.",
        'validation/invalid_value/missing' => "You didn't specify a value for an attribute.",
        'validation/invalid_value/too_high' => "The numeric value is too high.",
        'validation/invalid_value/too_long' => "The text value is too long.",
        'validation/invalid_value/too_low' => "The numeric value is too low.",
        'validation/invalid_value/unknown' => "The value you specified for this attribute is not in the list of the supported values.",
        'validation/missing_recommended' => "A recommended attribute is missing.",
        'validation/missing_required' => "A required attribute is missing.",
        'validation/other' => "Generic validation error.",
        'validation/policy' => "One of the policies has been violated.",
        'validation/warning' => "We found this attribute problematic for some reason and recommend checking it.",
    );

    /**
     * Error codes.
     * One exception may have several codes.
     *
     * @var array codes
     */
    protected $_codes = array();

    /**
     * Error messages.
     * One exception may have several codes with messages.
     *
     * @var array messages
     */
    protected $_messages = array();

    public function __construct($message = null, $httpClientException = null, $response = null)
    {
        if ($message instanceof Zend_Gdata_App_HttpException) {
            parent::__construct($message->getMessage(), $message->getHttpClientException(), $message->getResponse());
        } else {
            parent::__construct($message, $httpClientException, $response);
        }
        $this->_parseResponse($this->_response);
    }

    /**
     * Set the Zend_Http_Response.
     *
     * @param Zend_Http_Response $response
     */
    public function setResponse($response)
    {
        $this->_parseResponse($response);
        return parent::setResponse($response);
    }

    /**
     * Get array of error messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Get array of error codes
     *
     * @return array
     */
    public function getCodes()
    {
        return $this->_codes;
    }

    /**
     * Parse error response XML and fill arrays of codes and messages.
     *
     * @param Zend_Http_Response $response
     * @return Varien_Gdata_Gshopping_HttpException
     */
    protected function _parseResponse($response)
    {
        if (!$response instanceof Zend_Http_Response) {
            return;
        }
        $body = $response->getBody();

        if ($body &&
            ($errors = @simplexml_load_string($body)) &&
            'errors' == $errors->getName()) {

            $this->_messages = array();
            $this->_codes = array();
            foreach ($errors as $error) {
                $reason = isset($this->_errors["$error->code"])
                    ? $this->_errors["$error->code"]
                    : "Error code: $error->code.";
                $this->_messages[] = "$reason Internal reason: $error->internalReason @ $error->location\n";
                $this->_codes[] = "$error->code";
            }
            $this->message = implode("\n", $this->_messages);
            $this->code = implode(';', $this->_codes);
        }
        return $this;
    }
}
