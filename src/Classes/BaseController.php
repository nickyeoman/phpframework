<?php
namespace Nickyeoman\Framework\Classes;

use Twig\Environment;

class BaseController {
    protected $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    protected function view(string $template, array $data = []): void
    {
        echo $this->twig->render($template, $data);
    }
}
