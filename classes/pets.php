<?php

/**
 * FuelPHP package for the error push to slack
 *
 * @package   Push error to slack
 * @version   1.0
 * @author    Yu Watanabe
 * @link    https://watanabeyu.blogspot.com
 * 
 */

namespace Pets;

class Error extends \Fuel\Core\Error
{
  public static function show_php_error(\Exception $e)
  {
    \Config::load('pets',true);
    $webhook_url = \Config::get("pets.webhook_url");

    if(isset($webhook_url) && $webhook_url){
      $fatal = (bool)( ! in_array($e->getCode(), \Config::get('errors.continue_on', array())));
      $data = static::prepare_exception($e, $fatal);

      $request = new Pets_request($data);
      $request->send_webhook();
    }
    
    parent::show_php_error($e);
  }

  public static function show_production_error(\Exception $e){
    \Config::load('pets',true);
    $webhook_url = \Config::get("pets.webhook_url");

    if(isset($webhook_url) && $webhook_url){
      $fatal = (bool)( ! in_array($e->getCode(), \Config::get('errors.continue_on', array())));
      $data = static::prepare_exception($e, $fatal);

      $request = new Pets_request($data);
      $request->send_webhook();
    }
    
    parent::show_production_error($e);
  }
}