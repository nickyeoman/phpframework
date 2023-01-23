<?php
namespace Nickyeoman\Framework\Components\logout;

USE Nickyeoman\Framework\SessionManager;

class logoutController extends \Nickyeoman\Framework\BaseController {

	// This is the dashboard
	public function index(){

		$s = new SessionManager();
		$s->destroySession();
		$s->addflash("You have been logged out.",'notice');
		$this->redirect('index', 'index');

	}

} //End Class
