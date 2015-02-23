<?php namespace Request\Mobilpay\Facades;

use Illuminate\Support\Facades\Facade;

class Mobilpay extends Facade {

  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor() { return 'mobilpay'; }

}
