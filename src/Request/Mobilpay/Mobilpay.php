<?php namespace Request\Mobilpay;

use Illuminate\Support\Collection ,
Event, Input;

use Request\Mobilpay\Payment\Invoice;
use Request\Mobilpay\Payment\Address;
use Request\Mobilpay\Payment\Request\RequestAbstract;
use Request\Mobilpay\Payment\Request\Card;
use Request\Mobilpay\Payment\Request\Transfer;

class Mobilpay {

  protected $settings;

  protected $actions = [ 'confirmed', 'confirmed_pending', 'paid_pending', 'paid', 'canceled', 'credit' ];

  public function __construct($config) {
    $this->settings = new Collection($config);
    $this->handleStatus();
  }

  public function getOrderId(){
    $response = $this->getResponse();
    return $response->orderId;

  }

  public function makePayment($type, $orderId, $amount, $currency = 'RON', $details = '', array $billing = array(), array $shipping = array(), $installments = '2,3') {
    $request = $this->makeRequest($type, $orderId);

    $request->invoice               = new Invoice();
    $request->invoice->currency     = $currency;
    $request->invoice->amount       = doubleval($amount);
    $request->invoice->details      = $details;
    $request->invoice->installments = $installments;
    $request->invoice->setBillingAddress($this->makeAddress($billing));
    $request->invoice->setShippingAddress($this->makeAddress($shipping));

    $request->encrypt($this->getPublicCert());

    return $request;
  }

  public function getPaymentUrl($type) {
    return $this->settings->get($this->settings->get('environment') == 'sandbox' ? 'sandbox_url' : 'payment_url') . ($type != 'card' ? '/' . $type : '');
  }

  public function makeResponse() {
    $errorType = RequestAbstract::CONFIRM_ERROR_TYPE_NONE;
    $errorCode = 0;
    $errorMessage = '';

    if(Input::has('env_key') && Input::has('data'))
    {
      try
      {
        $response = $this->getResponse();
        if(in_array($response->notify->action, $this->actions))
        {
          $errorMessage = $response->notify->getCrc();
          $orderId      = $response->orderId;

          Event::fire('mobilpay.status', compact('orderId', 'response'));
          Event::fire('mobilpay.confirm', compact('orderId', 'response', 'errorType', 'errorCode', 'errorMessage'));
        }
        else
        {
          $errorType    = RequestAbstract::CONFIRM_ERROR_TYPE_PERMANENT;
          $errorCode    = RequestAbstract::ERROR_CONFIRM_INVALID_ACTION;
          $errorMessage = 'mobilpay_refference_action paramaters is invalid';
        }
      }
      catch(\Exception $e)
      {
        $errorType    = RequestAbstract::CONFIRM_ERROR_TYPE_TEMPORARY;
        $errorCode    = $e->getCode();
        $errorMessage = $e->getMessage();
      }
    }
    else
    {
      $errorType    = RequestAbstract::CONFIRM_ERROR_TYPE_PERMANENT;
      $errorCode    = RequestAbstract::ERROR_CONFIRM_INVALID_POST_PARAMETERS;
      $errorMessage = 'Invalid request method for payment confirmation';
    }

    return compact('errorType', 'errorCode', 'errorMessage');
  }

  protected function getResponse() {
    $response = RequestAbstract::factoryFromEncrypted(Input::get('env_key'), Input::get('data'), $this->getPrivateKey());

    return $response;
  }

  protected function makeRequest($type, $orderId) {
    switch ($type) {
      case 'card':
        $request = new Card();
        break;

      case 'transfer':
        $request = new Transfer();
        break;

      default:
        $request = new Card();
        break;
    }


    $request->orderId    = $orderId;
    $request->signature  = $this->settings->get('signature');
    $request->confirmUrl = url($this->settings->get('confirm_url'));
    $request->returnUrl  = url($this->settings->get('return_url'));

    return $request;
  }

  protected function makeAddress(array $info = []) {
    $props   = ['type', 'firstName', 'lastName', 'fiscalNumber', 'identityNumber', 'country', 'county', 'city', 'zipCode', 'adddress', 'email', 'mobilePhone', 'bank', 'iban'];

    $info    = new Collection($info);
    $address = new Address();

    foreach ($props as $prop)
      $address->$prop = $info->get($prop);

    return $address;
  }

  protected function handleStatus(){
    if($this->settings->get('updateStatusDB')) {
      Event::listen('mobilpay.status', function($orderId, $response){
        $model = $this->settings->get('model');
        if($orderId)
          $model::find($orderId)->update([ $this->settings->get('status') => $response->notify->action ]);
      });
    }
  }

  protected function getPublicCert() {
    return $this->settings->get('certificates_path') . $this->settings->get('public_cer');
  }

  protected function getPrivateKey() {
    return $this->settings->get('certificates_path') . $this->settings->get('private_key');
   }
}
