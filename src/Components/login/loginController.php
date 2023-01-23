<?php
namespace Nickyeoman\Framework\Components\login;

USE Nickyeoman\Framework\SessionManager;
USE Nickyeoman\Framework\ViewData;
USE Nickyeoman\Framework\RequestManager;
USE \Nickyeoman\Validation\Validate;
USE \Nickyeoman\Dbhelper\Dbhelp as DB;

class loginController extends \Nickyeoman\Framework\BaseController {

	public bool $error = false;

	// This is the dashboard
	public function index(){

		$s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);

		// If the user is logged in we don't need to proceed
		if ( $s->loggedin() )
			$this->redirect('user', 'index');

		// Check if form Submitted
		if ( $r->submitted ) {
			
			$validate = new Validate();
			$dirty = false;

			// Check Username or email is not null
			if ( ! $validate->minLength($r->get('login'), 4) ){
				$v->adderror("Login too short");
				$dirty = true;
			}

			// Check password is not null
			if ( ! $validate->minLength($r->get('password'), 8) ){
				$v->adderror("Password too short");
				$dirty = true;
			}

			// query db for user
			if ( !$dirty ) {
				$DB = new DB();

				// Email or username?
				if ( $validate->isEmail($r->get('login') ) ) {
					$userdb = $DB->findone('users', 'email', $r->get('login') );
				} else {
					$userdb = $DB->findone('users', 'username', $r->get('login'));
				}

			}

			// Check if results are empty
			if ( empty($userdb) ) {
				$dirty = true;
				$v->adderror("User not found.");
			} else {
				
				if ( ! password_verify($r->get('password'), $userdb['password'] ) ) {
					$dirty = true;
					$v->adderror("Incorrect Password.");
				}
				
			}

			// Login
			if ( !$dirty ){

				foreach (['id','username','email'] as $value) {
					$userSession[$value] = $userdb[$value];
				}

				// groups
				$results = $DB->findall('userGroups', 'groupName', 'user_id = ' . $userdb['id']);
				$DB->close();
				$ugroups = array();
				foreach ($results as $v){
					array_push($ugroups, $v['groupName']);
				}
				
				$s->setUserGroups($ugroups);
							
				$s->authorize($userSession);
				
				$this->redirect('admin', 'index');

			}

		} // end if submitted

		$this->twig('login', $v->data, 'login');
		$s->writeSession();

	}
	//END Login

} //End Class
