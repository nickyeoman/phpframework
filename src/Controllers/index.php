<?php
namespace Nickyeoman\Framework\Controllers;

use Nickyeoman\Framework\Classes\BaseController;
use Nickyeoman\Framework\Attributes\Route;

class index extends BaseController {

    #[Route('/')]
    public function index() {
        $this->view('@cms/index');
    }
}