<?php namespace Request\Mobilpay\Payment\Request;

use Request\Mobilpay\Payment\Address;

/**
 * Class Notify
 * This class can be used for accessing payment info sent by mobilpay.ro in confirmation process
 * @copyright NETOPIA System
 * @author Claudiu Tudose
 * @version 1.0
 *
 */
class Notify
{
    const ERROR_LOAD_FROM_XML_CRC_ATTR_MISSING = 0x60000001;
    const ERROR_LOAD_FROM_XML_ACTION_ELEM_MISSING = 0x60000002;

    public $purchaseId = null;
    public $action = null;
    public $errorCode = null;
    public $errorMessage = null;
    public $timestamp = null;
    public $originalAmount = null;
    public $processedAmount = null;
    public $promotionAmount = null;
    public $current_payment_count = 1;
    public $pan_masked = null;
    public $token_id = null;
    public $token_expiration_date = null;
    public $customer_id = null;
    public $customer_type = null;
    public $customer = null;
    public $issuer = null;

    private $_crc = null;

    function __construct ()
    {

    }

    public function loadFromXml (\DOMElement $elem)
    {

        $attr = $elem->attributes->getNamedItem('timestamp');
        if ($attr != null)
        {
            $this->timestamp = $attr->nodeValue;
        }

        $attr = $elem->attributes->getNamedItem('crc');
        if ($attr == null)
        {
            throw new \Exception('Notify::loadFromXml failed; mandatory crc attribute missing', self::ERROR_LOAD_FROM_XML_CRC_ATTR_MISSING);
        }
        $this->_crc = $attr->nodeValue;

        $elems = $elem->getElementsByTagName('action');
        if ($elems->length != 1)
        {
            throw new \Exception('Notify::loadFromXml failed; mandatory action attribute missing', self::ERROR_LOAD_FROM_XML_ACTION_ELEM_MISSING);
        }
        $this->action = $elems->item(0)->nodeValue;

        $elems = $elem->getElementsByTagName('customer');
        if ($elems->length == 1)
        {
            $this->customer = new Address($elems->item(0));
        }

        $elems = $elem->getElementsByTagName('issuer');
        if ($elems->length == 1)
        {
            $this->issuer = $elems->item(0)->nodeValue;
        }

        $elems = $elem->getElementsByTagName('purchase');
        if ($elems->length == 1)
        {
            $this->purchaseId = $elems->item(0)->nodeValue;
        }

        $elems = $elem->getElementsByTagName('original_amount');
        if ($elems->length == 1)
        {
            $this->originalAmount = $elems->item(0)->nodeValue;
        }

        $elems = $elem->getElementsByTagName('processed_amount');
        if ($elems->length == 1)
        {
            $this->processedAmount = $elems->item(0)->nodeValue;
        }

        $elems = $elem->getElementsByTagName('promotion_amount');
        if ($elems->length == 1)
        {
            $this->promotionAmount = $elems->item(0)->nodeValue;
        }

        $elems = $elem->getElementsByTagName('current_payment_count');
        if ($elems->length == 1)
        {
            $this->current_payment_count = $elems->item(0)->nodeValue;
        }

        $elems = $elem->getElementsByTagName('pan_masked');
        if ($elems->length == 1)
        {
            $this->pan_masked = $elems->item(0)->nodeValue;
        }

        $elems = $elem->getElementsByTagName('token_id');
        if ($elems->length == 1)
        {
            $this->token_id = $elems->item(0)->nodeValue;
        }

        $elems = $elem->getElementsByTagName('token_expiration_date');
        if ($elems->length == 1)
        {
            $this->token_expiration_date = $elems->item(0)->nodeValue;
        }

        $elems = $elem->getElementsByTagName('customer_type');
        if ($elems->length == 1)
        {
            $this->customer_type = $elems->item(0)->nodeValue;
        }


        $elems = $elem->getElementsByTagName('customer_id');
        if ($elems->length == 1)
        {
            $this->customer_id = $elems->item(0)->nodeValue;
        }

        $elems = $elem->getElementsByTagName('error');
        if ($elems->length == 1)
        {
            $xmlErrorElem = $elems->item(0);

            $attr = $xmlErrorElem->attributes->getNamedItem('code');
            if ($attr != null)
            {
                $this->errorCode = $attr->nodeValue;
            }

            $this->errorMessage = $xmlErrorElem->nodeValue;
        }
    }

    public function _loadFromQueryString ($queryString)
    {

        $parameters = explode('&', $queryString);
        $reqParams = array();
        foreach ($parameters as $item)
        {
            list ($key, $value) = explode('=', $item);
            $reqParams[$key] = urldecode($value);
        }

        $this->purchaseId = isset($reqParams['mobilpay_refference_id']) ? $reqParams['mobilpay_refference_id'] : null;
        $this->action = isset($reqParams['mobilpay_refference_action']) ? $reqParams['mobilpay_refference_action'] : null;
        $this->originalAmount = isset($reqParams['mobilpay_refference_original_amount']) ? $reqParams['mobilpay_refference_original_amount'] : null;
        $this->processedAmount = isset($reqParams['mobilpay_refference_processed_amount']) ? $reqParams['mobilpay_refference_processed_amount'] : null;
        $this->promotionAmount = isset($reqParams['mobilpay_refference_promotion_amount']) ? $reqParams['mobilpay_refference_promotion_amount'] : null;
        $this->current_payment_count = isset($reqParams['mobilpay_refference_current_payment_count']) ? $reqParams['mobilpay_refference_current_payment_count'] : null;
        $this->pan_masked = isset($reqParams['mobilpay_refference_pan_masked']) ? $reqParams['mobilpay_refference_pan_masked'] : null;
        $this->token_id = isset($reqParams['mobilpay_refference_token_id']) ? $reqParams['mobilpay_refference_token_id'] : null;
        $this->token_expiration_date = isset($reqParams['mobilpay_refference_token_expiration_date']) ? $reqParams['mobilpay_refference_token_expiration_date'] : null;
        $this->customer_id = isset($reqParams['mobilpay_refference_customer_id']) ? $reqParams['mobilpay_refference_customer_id'] : null;
        $this->customer_type = isset($reqParams['mobilpay_refference_customer_type']) ? $reqParams['mobilpay_refference_customer_type'] : null;
        $this->errorCode = isset($reqParams['mobilpay_refference_error_code']) ? $reqParams['mobilpay_refference_error_code'] : null;
        $this->errorMessage = isset($reqParams['mobilpay_refference_error_message']) ? $reqParams['mobilpay_refference_error_message'] : null;
        $this->timestamp = isset($reqParams['mobilpay_refference_timestamp']) ? $reqParams['mobilpay_refference_timestamp'] : null;
    }

    public function createXmlElement (\DOMDocument $xmlDoc)
    {

        $xmlNotifyElem = $xmlDoc->createElement('mobilpay');

        $attr = $xmlDoc->createAttribute('timestamp');
        $attr->nodeValue = date('YmdHis');
        $xmlNotifyElem->appendChild($attr);

        $this->_crc = md5(rand() . time());
        $attr = $xmlDoc->createAttribute('crc');
        $attr->nodeValue = $this->_crc;
        $xmlNotifyElem->appendChild($attr);

        $elem = $xmlDoc->createElement('action');
        $elem->nodeValue = $this->action;
        $xmlNotifyElem->appendChild($elem);

        if ($this->customer instanceof Address)
        {

            $xmlNotifyElem->appendChild($this->customer->createXmlElement($xmlDoc, 'customer'));
        }

        $elem = $xmlDoc->createElement('purchase');
        $elem->nodeValue = $this->purchaseId;
        $xmlNotifyElem->appendChild($elem);

        if ($this->originalAmount != null)
        {
            $elem = $xmlDoc->createElement('original_amount');
            $elem->nodeValue = $this->originalAmount;
            $xmlNotifyElem->appendChild($elem);
        }

        if ($this->processedAmount != null)
        {
            $elem = $xmlDoc->createElement('processed_amount');
            $elem->nodeValue = $this->processedAmount;
            $xmlNotifyElem->appendChild($elem);
        }

        if ($this->promotionAmount != null)
        {
            $elem = $xmlDoc->createElement('promotion_amount');
            $elem->nodeValue = $this->promotionAmount;
            $xmlNotifyElem->appendChild($elem);
        }

        if (is_null($this->current_payment_count) == FALSE)
        {
            $elem = $xmlDoc->createElement('current_payment_count');
            $elem->nodeValue = $this->current_payment_count;
            $xmlNotifyElem->appendChild($elem);
        }

        if (is_null($this->pan_masked) == FALSE)
        {
            $elem = $xmlDoc->createElement('pan_masked');
            $elem->nodeValue = $this->pan_masked;
            $xmlNotifyElem->appendChild($elem);
        }

        if (is_null($this->token_id) == FALSE)
        {
            $elem = $xmlDoc->createElement('token_id');
            $elem->nodeValue = $this->token_id;
            $xmlNotifyElem->appendChild($elem);
        }

        if (is_null($this->token_expiration_date) == FALSE)
        {
            $elem = $xmlDoc->createElement('token_expiration_date');
            $elem->nodeValue = $this->token_expiration_date;
            $xmlNotifyElem->appendChild($elem);
        }

        if (is_null($this->customer_type) == FALSE)
        {
            $elem = $xmlDoc->createElement('customer_type');
            $elem->nodeValue = $this->customer_type;
            $xmlNotifyElem->appendChild($elem);
        }
        else
        {
            //TODO alex
        }

        if (is_null($this->customer_id) == FALSE)
        {
            $elem = $xmlDoc->createElement('customer_id');
            $elem->nodeValue = $this->customer_id;
            $xmlNotifyElem->appendChild($elem);
        }

        if ($this->issuer != null)
        {
            $elem = $xmlDoc->createElement('issuer');
            $elem->nodeValue = $this->issuer;
            $xmlNotifyElem->appendChild($elem);
        }

        $elem = $xmlDoc->createElement('error');
        $attr = $xmlDoc->createAttribute('code');
        $attr->nodeValue = $this->errorCode;
        $elem->appendChild($attr);
        $elem->appendChild($xmlDoc->createCDATASection($this->errorMessage));
        $xmlNotifyElem->appendChild($elem);

        return $xmlNotifyElem;
    }

    public function getCrc ()
    {

        return $this->_crc;
    }
}
