<?php

Autoloader::add_core_namespace('Pets');

Autoloader::add_classes(array(
  'Pets\Pets_request' => __DIR__.'/classes/request.php',  
  'Pets\Error' => __DIR__.'/classes/pets.php',
));