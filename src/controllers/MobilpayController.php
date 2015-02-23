<?php namespace Request\Mobilpay\Controllers;

class MobilpayController extends \BaseController {

  public function confirmation() {
    $response = \Mobilpay::makeResponse();

    return \Response::view('mobilpay::response', $response, 200, ['Content-type' => 'application/xml']);
  }
}
