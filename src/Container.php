<?php

namespace Nickyeoman\Framework;

use Nickyeoman\Framework\SessionManager;
use Nickyeoman\Framework\ViewData;
use Nickyeoman\Framework\RequestManager;
use Nickyeoman\Framework\TwigRenderer;

class Container {
    private static $instance;
    private $sessionManager;
    private $viewData;
    private $requestManager;
    private $twigRenderer;
    private $logger;

    // Private constructor to prevent instantiation from outside
    private function __construct() {
        // Initialize dependencies
        $this->sessionManager = new SessionManager();
        $this->viewData = new ViewData($this->sessionManager);
        $this->requestManager = new RequestManager($this->sessionManager, $this->viewData);
        $this->twigRenderer = new TwigRenderer($_ENV['VIEWPATH'], $_ENV['TWIGCACHE']);
        $this->logger = new Logger($this);
    }

    // Get the singleton instance of Container
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Get SessionManager instance
    public function getSessionManager() {
        return $this->sessionManager;
    }

    // Get ViewData instance
    public function getViewData() {
        return $this->viewData;
    }

    // Get RequestManager instance
    public function getRequestManager() {
        return $this->requestManager;
    }

    // Get TwigRenderer instance
    public function getTwigRenderer() {
        return $this->twigRenderer;
    }

    // Get Logger instance
    public function getLogger() {
        return $this->logger;
    }
}
