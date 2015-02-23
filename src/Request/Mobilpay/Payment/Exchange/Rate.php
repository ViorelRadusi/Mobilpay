<?php namespace Request\Mobilpay\Payment\Exchange;
/**
 * Class Rate
 * @copyright NETOPIA System
 * @author Claudiu Tudose
 * @version 1.0
 *
 */
class Rate
{
  const ERROR_INVALID_PARAMETER     = 0x11111001;
  const ERROR_INVALID_PROPERTY      = 0x11110002;

  const ERROR_LOAD_FROM_XML_CURRENCY_ATTR_MISSING = 0x50000001;

  public $currency  = null;
  public $value   = null;

  public function __construct(\DOMNode $elem = null)
  {
    if($elem == null)
    {
      $this->loadFromXml($elem);
    }
  }

  protected function loadFromXml(\DOMNode $elem)
  {
    $attr = $elem->attributes->getNamedItem('currency');
    if($attr == null)
    {
      throw new \Exception('Rate::loadFromXml', self::ERROR_LOAD_FROM_XML_CURRENCY_ATTR_MISSING);
    }
    $this->currency = $attr->nodeValue;
    $this->value  = $elem->nodeValue;
  }

  public function createXmlElement(\DOMDocument $xmlDoc)
  {
    if(!($xmlDoc instanceof \DOMDocument))
    {
      throw new \Exception('', self::ERROR_INVALID_PARAMETER);
    }


    if($this->currency == null || $this->value == null)
    {
      throw new \Exception('Invalid property', self::ERROR_INVALID_PROPERTY);
    }

    $xmlRateElem = $xmlDoc->createElement('rate');

    $xmlAttr      = $xmlDoc->createAttribute('currency');
    $xmlAttr->nodeValue = $this->currency;
    $xmlRateElem->appendChild($xmlAttr);
    $xmlRateElem->nodeValue = sprintf('%.02f', doubleval($this->currency));

    return $xmlRateElem;
  }
}
