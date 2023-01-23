<?php
namespace Nickyeoman\Framework\Components\admin;

USE Nickyeoman\Framework\SessionManager;
USE Nickyeoman\Framework\ViewData;

class adminController extends \Nickyeoman\Framework\BaseController {

	public bool $error = false;

	// This is the dashboard
	public function index(){

		$s = new SessionManager();
		$v = new ViewData($s);

		if ( ! $s->loggedin("You need to login to access dashboard.") )
			$this->redirect('user', 'login');
	  
		$v->set('username', $s->data['user']['username'] );
		$v->set('adminbar', true);
		
		$this->twig('index', $v->data, 'admin');

	} // end function index

} //End Class
