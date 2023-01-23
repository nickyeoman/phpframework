<?php
namespace Nickyeoman\Framework\Components\user;

USE Nickyeoman\Framework\SessionManager;
USE Nickyeoman\Framework\ViewData;
USE Nickyeoman\Framework\RequestManager;
USE \Nickyeoman\Validation\Validate;
USE \Nickyeoman\Dbhelper\Dbhelp as DB;

class userController extends \Nickyeoman\Framework\BaseController {

	public bool $error = false;

	// This is the dashboard
	public function index(){

		$this->redirect('admin', 'index');

	} // end function index

	/**
	* /user/registration
	**/
	public function registration() {

		$s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);

		// If the user is logged in we don't need to proceed
		if ( $s->loggedin() )
		$this->redirect('admin', 'index');

		//check Form Was submitted is enabled
		if ( ! empty( $r->get('formkey') ) ){

		

		//check session matches
    	if ( $r->submitted ) {

			$user = new \Nickyeoman\Framework\Components\user\userHelper();

			//clean the data (true = good, false = bad)
			if ( ! $user->checkUsername() ) {

				$this->error = true;  // TODO: not sure this is needed just a count(data-error). this appears elsewhere

				//For the view
				array_push($v->data['error'], $user->errors['username']);

			}

			// check email address
			if ( ! $user->checkEmail(false) ){

				$this->error = true;
				//For the view
				array_push($v->data['error'], $user->errors['email']);
			}

			// check password address
			if ( ! $user->checkPassword() ){

				$this->error = true;
				//For the view
				array_push($v->data['error'], $user->errors['password']);

			}

			//No Errors, then save
			if (! $this->error) {

				//write to database
				$user->newuser();
				$userTraits = json_encode($user->userTraits);
				$this->log('INFO', 'Created User', 'userController register', $userTraits);

				// Send registration Email
				$user->sendRegistrationEmail();

				//redirect to verify page
				$this->redirect('user', 'validate');

			} else {

				$v->data['post']['email'] = $_POST['email'];
				$v->data['post']['username'] = $_POST['username'];

			}

		} else {
			//TODO: cross site scripting, log this
		}

    }

    //Display view
    $this->twig('registration', $v->data,'user');
	$s->writeSession();

	}
	//end register action (page)

	public function validate(){

		$s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);

		if ( $r->get('valid') ) { //TODO: change this to param

			//A validation key exists, we better check it
			$user = new \Nickyeoman\Framework\Components\user\userHelper();

			// TODO: Check user isn't already valid.

			// Check if the key is valid
			if ( ! $user->checkValidationKey($r->get('valid')) ) {

				$this->error = true;

			} else {

				$v->data['notice'] = 'Your Email Address is now validated,  you can login';

			}

		}
		//end check key

		// display view
		$this->twig('validate', $v->data, 'user');

	}
	// End validate action (page)

    /**
     *  Forgot Password Controller
     */
	public function forgot() {

		$s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);

		// If the user is logged in we don't need to proceed
		if ( $s->loggedin() )
		    $this->redirect('user', 'index');

		$user = new Nickyeoman\helpers\userHelper();

		//check Form Was submitted is enabled
		if ( $r->submitted) {			

			// check email address (and exists in the database)
			// TODO: if validation key is present
			if ( ! $user->checkEmail(true) ){

				$this->error = true;
				$this->data['badLogin'] = $user->errors['email'];  //For the view

			} //END IF

			//No Errors, then save
			if ( ! $this->error ) {

				$user->passwordReset(); // Send registration Email
				$this->data['notice'] = "Email Sent for verification";

			} else {

				$this->data['post']['email'] = $_POST['email'];
				$this->data['badLogin'] = "There was an error.";

			}

		} else {
			//TODO: cross site scripting, log this
		}

		//Display view
		$this->twig('login', $v->data);
		$s->writeSession();
	}

	public function reset() {

		$s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);

        if ( isset( $_GET['valid'] ) ) {

            bdump($_GET, 'Get Data');

            //A validation key exists, we better check it
            $user = new Nickyeoman\helpers\userHelper();

            // Check if the key is valid
            if ( ! $user->checkResetKey($_GET['valid']) ) {

                $this->error = true;
                //For the view
                array_push($this->data['error'] , $user->errors['valid']);

            } else {

                $this->valid = new \Nickyeoman\Validation\Validate();
                $v->data['goodValid'] = 'Please reset your password below';
                $v->data['resetkey'] = $this->valid->clean( $_GET['valid'] );
                $v->data['formkey'] = $s->get('formkey');
                $v->data['email'] = $this->valid->clean( $_GET['email'] );

            }

        }
        //end check the get key

        // Start checking POST for new password
        if ( isset( $_POST['formkey'] ) ) {

            bdump($_POST, 'Post Data');

            // Check form key matches session
            if ( $_POST['formkey'] == $s->getKey('formkey') ) {

                if ( ! empty( $_POST['resetkey'] ) ) {

                    bdump($_POST['resetkey'], 'reset key not empty');
                    //A validation key exists, we better check it
                    $user = new Nickyeoman\Framework\User();

                    // check reset key
                    if ( ! $user->checkResetKey($_POST['resetkey'] ) ) {
                        bdump('','CheckResetKey Failed');
                        $this->error = true;
                        $v->data['badValid'] = $user->errors['valid'];  //For the view
                    }
                    //reset key is good

                    //Check email
                    if ( ! $user->checkEmail( true ) ) {
                        $this->error = true;
                        $v->data['badValid'] = $user->errors['valid'];  //For the view
                    }
                    //email is good

                    //check password
                    if ( ! $user->checkPassword() ){

                        $this->error = true;
                        $v->data['badPassword'] = $user->errors['password'];  //For the view

                    }
                    //password is good

                    if ( ! $this->error ) {

                        $user->resetUserPassword(); //write to database

                        $s->setFlash('notice', "Password Change Successful, please login.");
                        $s->writeSession();

                        $this->redirect('admin', 'index');  //redirect to login page

                    }
                    // If all was good, data was written to database

                } else { // There is a problem with the login key

                    $this->error = true;
                    $v->data['badValid'] = "Bad Reset Key (001)";  //For the view

                }

            } else { //There is a problem with the session formkey

                $this->error = true;
                $v->data['badValid'] = "Bad Reset Key (002)"; //For the view

            }

        }

        $this->twig('reset', $v->data); // display view

    }
		//End Function reset

	public function block($params) {

		$s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);

		if ( ! $s->loggedin('You need to login to edit users.') )
      		$this->redirect('admin', 'index');

    	if ( !$s->inGroup('admin', 'You need Admin permissions to edit users.') )
      		$this->redirect('admin', 'index');

		if ( !empty($params))
			$userid = $params[0];

		if ( empty($userid) || !is_numeric($userid) ) {

			$s->addFlash('error', "User id not correct");
			$this->redirect('user', 'admin');

		}

		$DB = new DB();
		// Check user exists
		$result = $DB->findone('users','id',$userid);

		if ( empty($result ) ) {

			$s->addFlash('error', "User does not exist");
			$this->redirect('user', 'admin');

		} else {

			if ( $result['blocked'] ) {
				//unblock
				$s->addFlash('notice: user Unblocked', "blockuser");
				$blocked = 0;
			} else {
				//block
				$s->addFlash('notice: user blocked', "blockuser");
				$blocked = 1;
			}

			$update = array(
				'blocked' => $blocked
				,'id' => $result['id']
			);

			$DB->update('users',$update,'id');
			$DB->close();

			$this->redirect('user', 'admin');
		}

	} // end block user

	/**
	 * Delete a user
	 */
	public function delete($params) {

		$s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);

		if ( ! $s->loggedin('You need to login to edit users.') )
      		$this->redirect('admin', 'index');

    	if ( !$s->inGroup('admin', 'You need Admin permissions to edit users.') )
      		$this->redirect('admin', 'index');

		if ( !empty($params))
			$userid = $params[0];

		if ( empty($userid) || !is_numeric($userid) ) {

			$s->addFlash("User id not correct", 'error');
			$this->redirect('user', 'admin');

		}

		$DB = new DB();

		// Check user exists
		$result = $DB->findone('users','id',$userid);

		if ( empty($result ) ) {

			$s->addFlash("User does not exist", 'error');
			$this->redirect('user', 'admin');

		} else {

			$DB->delete('users',"id = $userid");
			$s->addFlash('Notice: user removed (deleted)', "notice");

			$this->redirect('user', 'admin');
		}

	} // end delete user

	public function myprofile() {
		if ( $s->loggedin() ) {

			$s->addflash("You need to login to edit your profile.",'error');
	    	$s->writeSession();
	    	$this->redirect('admin', 'index');

		}

		// based on session id
		$result = $DB->findone('users', 'id', $s->getKey('userid'));

		$this->data['userdata'] = array(
			'username' => $result['username']
			,'email' => $result['email']
			,'created' => $result['created']
		);

		$v->data['page']['slug'] = "myprofile";

	  $this->twig('myprofile', $v->data);
		$s->writeSession();
	} //end my profile

	public function saveprofile() {

		$s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);

		$userdb = array();

		// form was submitted prepare helper class
		if ( ! empty( $_POST['formkey'] ) )
			$user = new Nickyeoman\helpers\userHelper();

		// Change username
		if ( !empty($this->post['username']) && $this->post['username'] != $s->getKey('userid') ) {

			// make sure username is valid (this includes a db check)
			if ( ! $user->checkUsername() ) {

				//For the view
				array_push($this->data['error'] , $user->errors['username']);

			} else {

				$username = strtolower(trim( $this->post['username']));
				$userdb['id'] = $s->getKey('userid');
				$userdb['username'] = $username;
				// update session data
				$s->setKey($username);

			}

		} // end username

		// Change Password
		if ( !empty($this->post['currentpassword']) && !empty($this->post['newpassword']) && !empty($this->post['confirm']) ){

			// check password matches
			if ( ! $user->checkPassword('newpassword', $this->post['confirmpassword']) ){

					$this->adderror($user->errors['password']);  //TODO: Adderror function

			} else {

				$userdb['password'] = $user->userTraits['password'];
				$userdb['id'] = $s->getKey('userid');

			}

		}

		// update database
		if ( !empty($userdb) ) {
			$DB = new DB();
			$id = $DB->update("users", $userdb, 'id' );
			$DB->close();
			// TODO: you need to create the array handler like adderror in the base controller
			$v->data['notice'] = "Profile Updated";

			$s->writeSession();
		}

		//dump($this->post);dump($s->);dump($this->data);dump($userdb);
		//die();
		$this->redirect('user', 'myprofile');


	} // end save profile

} //End Class
