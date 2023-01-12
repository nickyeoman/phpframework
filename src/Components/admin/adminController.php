<?php
namespace Nickyeoman\Framework\Components\admin;

class adminController extends \Nickyeoman\Framework\BaseController {

	public bool $error = false;

	// This is the dashboard
	public function index(){

		if ( ! $this->session->loggedin("You need to login to access dashboard.") )
			$this->redirect('user', 'login');
	  
		$this->data['username'] = $this->session->getKey('username');

		$this->twig('index', $this->data, 'admin');

	} // end function index

} //End Class
