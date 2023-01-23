<?php
namespace Nickyeoman\Framework\Components\install;

USE Nickyeoman\Framework\SessionManager;
USE Nickyeoman\Framework\ViewData;
USE Nickyeoman\Framework\RequestManager;
USE \Nickyeoman\Validation\Validate;
USE \Nickyeoman\Dbhelper\Dbhelp as DB;

class installController extends \Nickyeoman\Framework\BaseController {

	// This is the installer
	public function post(){

		$s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);
		$validate = new Validate();

		$v->set('showForm',true);

		if ( $r->submitted ) {

			$secretkey = $r->get('secret');
			$safeUser = false;

			// Check secret key for access
			if ($secretkey == $_ENV['SALT']) 
				$safeUser = true;
			else
				$v->adderror("Secret Keys don't match");

			// Protect against defaults
			if ($secretkey == 'MakeASalt32charactersWouldBeNice') {
				$safeUser = false;
				$v->adderror("Please change the SALT in your dotenv, before proceeding");
			}

			// Check Email address
			$email = $validate->isEmail($r->get('email'));
			if ( empty($email) ) {
				$v->adderror("Email not valid");
				$safeUser = false;
			}

			// Check Username
			if ( $validate->minLength($r->get('username'), 4) ){
				$username = $r->get('username');
			} else {
				$v->adderror("Username too short");
				$safeUser = false;
			}

			// Check Passwords match
			if ( $r->get('password') != $r->get('password-confirm') ) {
				$v->adderror("Passwords don't match");
				$safeUser = false;
			}

			// Check Password min length
			if ( $validate->minLength($r->get('password'), 8) ){

				$password = password_hash($r->get('password'), PASSWORD_BCRYPT);

			} else {

				$v->adderror("Password too short");
				$safeUser = false;

			}

			// Do the install
			if ($safeUser && $v->counterrors() < 1 ) {

				$v->set('showForm',false);
				$DB = new DB();
				if ( $DB->tableExists('pages') ) {
					$v->adderror('db table exists, you need to migrate, not install');
				} else {
					$v->addnotice('run install');

					// Loop through sql files in sql folder
					$sqlfilepath = $_ENV['BASEPATH'] . '/vendor/nickyeoman/phpframework/sql';
					
					$files = scandir($sqlfilepath);
					
					foreach ( $files as $file ) {
						if ( Strstr($file, 'sql')) {
							$sql = file_get_contents("$sqlfilepath/$file");
							$DB->query($sql);
						}
					}

					// Create the Admin User
					$user = array(
						'username'          => $username,
						'email'             => $r->get('email'),
						'password'          => $password,
						'confirmationToken' => '',
						'confirmed'         => '1',
						'blocked'           => '0',
						'created'        => date("Y-m-d H:i:s"),
						'updated'        => date("Y-m-d H:i:s")
					  );
				  
					  $DB->create("users", $user );
					  $DB->create("userGroups", ['user_id' => 1, 'groupName' => 'admin']);
					
				}
				$DB->close();

			}
		}
		
		$this->twig('installForm', $v->data, 'install');
		$s->writeSession();

	} // end function override

	public function index() {
		$s  = new SessionManager();
		$v = new ViewData($s);
		$v->set('showForm',true);
		$this->twig('installForm', $v->data, 'install');
		$s->writeSession();
	}

} //End Class
