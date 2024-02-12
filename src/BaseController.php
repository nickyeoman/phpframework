<?php
namespace Nickyeoman\Framework;

class BaseController {

  public $sessionManager;
  public $viewData;
  public $requestManager;
  public $twig;
  public $logger;

  public function __construct($container) {

    $this->sessionManager = $container->getSessionManager();
    $this->viewData = $container->getViewData();
    $this->requestManager = $container->getRequestManager();
    $this->twig = $container->getTwigRenderer();
    $this->logger = $container->getLogger();

  } 
} 
