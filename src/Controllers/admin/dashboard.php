<?php
namespace Nickyeoman\Framework\Controllers\admin;

use Nickyeoman\Framework\Classes\BaseController;
use Nickyeoman\Framework\Attributes\Route;

class dashboard extends BaseController {

    #[Route('/admin')]
    public function index() {
        $s = $this->session;
        $v = $this->viewClass;
        $r = $this->request;

        if ( ! $s->loggedin('You need to login to edit pages.') )
            redirect('login');

        if ( !$s->isAdmin() ) {
            $s->addflash('You need to be an admin to view messages..','error');
            redirect('login');
        }

        $v->set('adminbar', true);
        $this->view('@cms/admin/dashboard');
    }
}