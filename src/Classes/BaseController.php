<?php
namespace Nickyeoman\Framework\Classes;

use Twig\Environment;

class BaseController {
    protected $twig;
    protected $viewClass;
    protected $session;
    protected $request;

    public function __construct(Environment $twig, $viewClass, $session, $request) {
        $this->twig = $twig;
        $this->viewClass = $viewClass;
        $this->session = $session;
        $this->request = $request;
    }

    protected function view(string $template): void
    {
        echo $this->twig->render("$template.html.twig", $this->viewClass->data);
    }
}
