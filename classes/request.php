<?php 

namespace Pets;

class Pets_request
{
  
  private $data;

  private $payload = array(
    "username" => "pets",
    "icon_url" => "http://www.bad-company.jp/assets/img/pets.png"
  );

  private $config = array();

  /**
   * construce 
   * @param array $_data [this array is error data in \Fuel\Core\Error]
   */
  function __construct($_data = array())
  {
    $this->data = $_data;

    $this->config = \Config::load('pets',true);
    
    if(isset($this->config['channel']) && $this->config['channel']){
      $this->payload['channel'] = $this->config['channel'];
    }

    if(isset($this->config['icon_url']) && $this->config['icon_url']){
      $this->payload['icon_url'] = $this->config['icon_url'];
    }
  }

  /**
   * \Config::get("pets.mode") === "payload"
   * @return array
   */
  private function get_payload_params(){
    $text = "--------------------------------------------------\n";
    $text .= $_SERVER["HTTP_HOST"]." - ".date("Y/m/d H:i:s")."\n";
    $text .= "\n";
    $text .= "*{$this->data['severity']}!*\n";
    $text .= "{$this->data['type']}[{$this->data['severity']}]\n";
    $text .= "{$this->data['message']}\n";
    $text .= "\n";
    $text .= "{$this->data['filepath']} @ line{$this->data['error_line']}\n";
    $text .= "\n";
    $text .= "*backtrace*\n";
    foreach($this->data['backtrace'] as $key => $backtrace){
      $text .= " - ".($key + 1).". {$backtrace['file']} @ line{$backtrace['line']}\n";
    }
    $text .= "\n--------------------------------------------------";

    $this->payload['text'] = $text;

    return array("payload" => json_encode($this->payload));
  }

  /**
   * \Config::get("pets.mode") === "attachments"
   * @return array
   */
  private function get_attachments_params(){
    $bt = "";
    foreach($this->data['backtrace'] as $key => $backtrace){
      $bt .= " - ".($key + 1).". {$backtrace['file']} @ line{$backtrace['line']}\n";
    }

    $fields = array(
      array(
        "title" => "DATE",
        "value" => date("Y/m/d H:i:s"),
        "short" => false
      ),
      array(
        "title" => "URL",
        "value" => $_SERVER["HTTP_HOST"],
        "short" => false
      ),
      array(
        "title" => "ENVIROMENT",
        "value" => \Fuel::$env,
        "short" => false
      ),
      array(
        "title" => "{$this->data['type']}[{$this->data['severity']}]",
        "value" =>  "{$this->data['message']}",
        "short" => false
      ),
      array(
        "title" => "BACKTRACE",
        "value" => $bt
      )
    );

    $this->payload['attachments'] = array([
      "color" => "#".substr(preg_replace("/[^0-9a-f]/","",md5($this->data['severity'])),-6),
      "fallback" => "{$this->data['type']}[{$this->data['severity']}] - {$this->data['message']}",
      "pretext" => "{$this->data['type']}[{$this->data['severity']}] - {$this->data['message']}",
      "fields" => $fields
    ]);

    return array("payload" => json_encode($this->payload));
  }

  /**
   * send webhook to slack
   * @return result
   */
  public function send_webhook(){
    $params = (isset($this->config['mode']) && $this->config['mode'] == "attachments")?
      $this->get_attachments_params():
      $this->get_payload_params();

    $headers = array();

    $ch = curl_init($this->config['webhook_url']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if($error){
      throw new \Exception($error);
    }

    return $result;
  }
  
}
