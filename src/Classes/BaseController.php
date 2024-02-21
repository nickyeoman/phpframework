<?php
namespace Nickyeoman\Framework\Classes;

use Twig\Environment;

class BaseController {
    protected $twig;
    protected $viewClass;

    public function __construct(Environment $twig, $viewClass) {
        $this->twig = $twig;
        $this->viewClass = $viewClass;
    }

    protected function view(string $template): void
    {
        echo $this->twig->render("$template.html.twig", $this->viewClass->data);
    }
}
