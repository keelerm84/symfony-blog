<?php

namespace Koios\BlogBundle\Services;

use \Guzzle\Service\Client;
use \Guzzle\Service\Description\ServiceDescription;
use \Guzzle\Common\Cache\DoctrineCacheAdapter;
use \Doctrine\Common\Cache\ArrayCache;
use \Guzzle\Http\Plugin\CachePlugin;

class BlogBackendClient extends Client {
  public function __construct($description_path, $baseUrl = '', $config = null) {
	parent::__construct($baseUrl, $config);
	$this->setDescription(ServiceDescription::factory($description_path));

    $adapter = new DoctrineCacheAdapter(new ArrayCache());
    $cache = new CachePlugin($adapter, true);

    $this->addSubscriber($cache);
  }

  public function getCommand($name, array $args = array()) {
      $default_headers = array('content-type' => 'application/json', 'accept' => 'application/json');
    $args['headers'] = array_merge($default_headers, isset($args['headers']) ? $args['headers'] : array());

    return parent::getCommand($name, $args);
  }
}
