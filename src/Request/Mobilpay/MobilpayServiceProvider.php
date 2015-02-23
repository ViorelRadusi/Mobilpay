<?php namespace Request\Mobilpay;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class MobilpayServiceProvider extends ServiceProvider {

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;

  /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('request/mobilpay');
        AliasLoader::getInstance()->alias('Mobilpay', 'Request\Mobilpay\Facades\Mobilpay');

        include __DIR__ . '/../../routes.php';
        include __DIR__ . '/../../controllers/MobilpayController.php';
    }

    /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $this->app['mobilpay'] = $this->app->share(function($app)
    {
      return new Mobilpay($app['config']->get('mobilpay::settings'));
    });
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return array('mobilpay');
  }

}
