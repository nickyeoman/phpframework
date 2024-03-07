<?php
namespace Nickyeoman\Framework\Controllers\admin;

use Nickyeoman\Framework\Classes\BaseController;
use Nickyeoman\Dbhelper\Dbhelp as DB;
use Nickyeoman\Framework\Components\contact\contactHelper as contacthelp;
use Nickyeoman\Framework\Attributes\Route;

class dashboard extends BaseController {

    #[Route('/admin')]
    public function index() {
        $s = $this->session;
        $v = $this->viewClass;
        $r = $this->request;

        $this->view('@cms/admin/dashboard');
    }
}