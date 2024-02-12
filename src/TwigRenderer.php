<?php
namespace Nickyeoman\Framework;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer {
    private $twig;

    public function __construct(string $viewPath, string $twigCachePath) {

        // Initialize Twig loader with specified paths
        $loader = new FilesystemLoader(BASEPATH . '/' . $viewPath);
        $loader->addPath(BASEPATH . '/vendor/nickyeoman/nytwig/src', 'nytwig');

        // Create Twig environment with cache and debug settings
        $this->twig = new Environment($loader, [
            'cache' => BASEPATH . '/' . $twigCachePath,
            'debug' => true,
        ]);

        // Add Twig debug extension
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
    }

    public function render(string $viewName = 'index', array $vars = [], ?string $component = null): void {
        
        // Load component view if specified
        if ($component !== null) {
            $this->twig->getLoader()->prependPath(BASEPATH . "/vendor/nickyeoman/phpframework/src/Components/$component/twig");
        }

        // Render Twig template with provided variables
        echo $this->twig->render("$viewName.html.twig", $vars);
    }
}
