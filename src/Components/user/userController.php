<?php
namespace Nickyeoman\Framework\Components\user;

class userController extends \Nickyeoman\Framework\BaseController {

	public bool $error = false;

	// This is the dashboard
	public function index(){

		$this->redirect('admin', 'index');

	} // end function index

	/**
	* Login Form
	**/
	public function login() {

		// If the user is logged in we don't need to proceed
		if ( $this->session->loggedin() )
			$this->redirect('user', 'index');

		// Check if form Submitted
		if ( $this->post['submitted'] ) {

			$user = new Nickyeoman\helpers\userHelper();

			//Process the form
			if ( $user->login() ) {

				foreach ($user->userTraits as $k => $v) {
					$this->session->setKey($k,$v);
				}

				$this->log('notice','LOGIN Success', 'Controller/user.php/login()' );
				$this->redirect('user', 'index');

			} else {

				//prep errors, login failed

				if ( !empty($user->errors) ) {

					foreach( $user->errors as $k => $v ) {

						//$this->adderror($string = $v, "$k error" );
						array_push($this->data['error'], "$k $v");

					}
					//end foreach

					$this->log('notice','LOGIN Fail', 'Controller/user.php/login()' );

				}
				//endif

			}
			//end else

		}
		// end POST SUBMITTED

		$this->data['page']['slug'] = 'user-login';
		$this->twig('login', $this->data, 'user');

	}
	//END Login

	/**
	* /user/registration
	**/
	public function registration() {

    // If the user is logged in we don't need to proceed
	if ( $this->session->loggedin() )
      $this->redirect('user', 'index');

    //check Form Was submitted is enabled
    if ( ! empty( $_POST['formkey'] ) ){

			$user = new Nickyeoman\helpers\userHelper();

		//check session matches
    	if ( $_POST['formkey'] == $this->session->getKey('formkey') ) {

				//clean the data (true = good, false = bad)
				if ( ! $user->checkUsername() ) {

					$this->error = true;  // TODO: not sure this is needed just a count(data-error). this appears elsewhere

					//For the view
					array_push($this->data['error'], $user->errors['username']);

				}

				// check email address
				if ( ! $user->checkEmail(false) ){

					$this->error = true;
					//For the view
					array_push($this->data['error'], $user->errors['email']);

				}

				// check password address
				if ( ! $user->checkPassword() ){

					$this->error = true;
					//For the view
					array_push($this->data['error'], $user->errors['password']);

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

					$this->data['post']['email'] = $_POST['email'];
					$this->data['post']['username'] = $_POST['username'];

				}

      } else {
        //TODO: cross site scripting, log this
      }

    }

    //Display view
    $this->twig('registration', $this->data);
	$this->session->writeSession();

	}
	//end register action (page)

	public function validate(){

		if ( isset( $_GET['valid'] ) ) { //TODO: change this to param

			//A validation key exists, we better check it
			$user = new Nickyeoman\helpers\userHelper();

			// TODO: Check user isn't already valid.

			// Check if the key is valid
			if ( ! $user->checkValidationKey($_GET['valid']) ) {

				$this->error = true;

			} else {

				$this->data['notice'] = 'Your Email Address is now validated,  you can login';

			}

		}
		//end check key

		// display view
		$this->twig('validate', $this->data);

	}
	// End validate action (page)

	/**
	 * Log out
	 * Destroys the framework and php session objects
	 */
	public function logout() {

		$this->session->addFlash("You have been logged out.", 'notice');
		$this->session->destroySession();
		$this->redirect('index', 'index');

	}

    /**
     *  Forgot Password Controller
     */
	public function forgot() {

		// If the user is logged in we don't need to proceed
		if ( $this->session->loggedin() )
		    $this->redirect('user', 'index');

		$user = new Nickyeoman\helpers\userHelper();

		$this->data['formkey'] = $this->session->getKey('formkey');       // Form cross site protection

		//check Form Was submitted is enabled
		if ( ! empty( $_POST['formkey'] ) ) {

			//check session matches
			if ( $_POST['formkey'] == $this->session->getKey('formkey') ) {

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

		}

		//Display view
		$this->twig('login', $this->data);
		$this->session->writeSession();
	}

	public function reset() {

        if ( isset( $_GET['valid'] ) ) {

            bdump($_GET, 'Get Data');

            //A validation key exists, we better check it
            $user = new Nickyeoman\helpers\userHelper();

            // Check if the key is valid
            if ( ! $user->checkResetKey($_GET['valid']) ) {

                $this->error = true;
                //For the view
                $this->data['error'] = $user->errors['valid'];

            } else {

                $this->valid = new \Nickyeoman\Validation\Validate();
                $this->data['goodValid'] = 'Please reset your password below';
                $this->data['resetkey'] = $this->valid->clean( $_GET['valid'] );
                $this->data['formkey'] = $this->session['formkey'];
                $this->data['email'] = $this->valid->clean( $_GET['email'] );

            }

        }
        //end check the get key

        // Start checking POST for new password
        if ( isset( $_POST['formkey'] ) ) {

            bdump($_POST, 'Post Data');

            // Check form key matches session
            if ( $_POST['formkey'] == $this->session->getKey('formkey') ) {

                if ( ! empty( $_POST['resetkey'] ) ) {

                    bdump($_POST['resetkey'], 'reset key not empty');
                    //A validation key exists, we better check it
                    $user = new Nickyeoman\Framework\User();

                    // check reset key
                    if ( ! $user->checkResetKey($_POST['resetkey'] ) ) {
                        bdump('','CheckResetKey Failed');
                        $this->error = true;
                        $this->data['badValid'] = $user->errors['valid'];  //For the view
                    }
                    //reset key is good

                    //Check email
                    if ( ! $user->checkEmail( true ) ) {
                        $this->error = true;
                        $this->data['badValid'] = $user->errors['valid'];  //For the view
                    }
                    //email is good

                    //check password
                    if ( ! $user->checkPassword() ){

                        $this->error = true;
                        $this->data['badPassword'] = $user->errors['password'];  //For the view

                    }
                    //password is good

                    if ( ! $this->error ) {

                        $user->resetUserPassword(); //write to database

                        $this->setFlash('notice', "Password Change Successful, please login.");
                        $this->session->writeSession();

                        $this->redirect('user', 'login');  //redirect to login page

                    }
                    // If all was good, data was written to database

                } else { // There is a problem with the login key

                    $this->error = true;
                    $this->data['badValid'] = "Bad Reset Key (001)";  //For the view

                }

            } else { //There is a problem with the session formkey

                $this->error = true;
                $this->data['badValid'] = "Bad Reset Key (002)"; //For the view

            }

        }

        $this->twig('reset', $this->data); // display view

    }
		//End Function reset

	// Manage your users
	public function admin() {

		if ( ! $this->session->loggedin('You need to login to edit users.') )
      		$this->redirect('user', 'login');

    	if ( !$this->session->inGroup('admin', 'You need Admin permissions to edit users.') )
      		$this->redirect('user', 'login');

		//Grab pages
		$result = $this->db->findall('users','id,username,email,blocked,admin');

		if ( !empty( $result ) ) {

	    	foreach ($result as $key => $value){

				//$value['tags'] = explode(',',$value['tags']);
	      		$this->data['users'][$key] = $value;

	    	} //endforeach

	  	} //endif

		$this->data['page']['slug'] = "user-admin"; // instead of "admin" (for css pageid)
		$this->twig('admin', $this->data);

	} //end admin

	public function block($params) {

		if ( ! $this->session->loggedin('You need to login to edit users.') )
      		$this->redirect('user', 'login');

    	if ( !$this->session->inGroup('admin', 'You need Admin permissions to edit users.') )
      		$this->redirect('user', 'login');

		if ( !empty($params))
			$userid = $params[0];

		if ( empty($userid) || !is_numeric($userid) ) {

			$this->session->addFlash('error', "User id not correct");
			$this->redirect('user', 'admin');

		}

		// Check user exists
		$result = $this->db->findone('users','id',$userid);

		if ( empty($result ) ) {

			$this->session->addFlash('error', "User does not exist");
			$this->redirect('user', 'admin');

		} else {

			if ( $result['blocked'] ) {
				//unblock
				$this->session->addFlash('notice: user Unblocked', "blockuser");
				$blocked = 0;
			} else {
				//block
				$this->session->addFlash('notice: user blocked', "blockuser");
				$blocked = 1;
			}

			$update = array(
				'blocked' => $blocked
				,'id' => $result['id']
			);

			$this->db->update('users',$update,'id');

			$this->redirect('user', 'admin');
		}

	} // end block user

	/**
	 * Delete a user
	 */
	public function delete($params) {

		if ( ! $this->session->loggedin('You need to login to edit users.') )
      		$this->redirect('user', 'login');

    	if ( !$this->session->inGroup('admin', 'You need Admin permissions to edit users.') )
      		$this->redirect('user', 'login');

		if ( !empty($params))
			$userid = $params[0];

		if ( empty($userid) || !is_numeric($userid) ) {

			$this->session->addFlash("User id not correct", 'error');
			$this->redirect('user', 'admin');

		}

		// Check user exists
		$result = $this->db->findone('users','id',$userid);

		if ( empty($result ) ) {

			$this->session->addFlash("User does not exist", 'error');
			$this->redirect('user', 'admin');

		} else {

			$this->db->delete('users',"id = $userid");
			$this->session->addFlash('Notice: user removed (deleted)', "notice");

			$this->redirect('user', 'admin');
		}

	} // end delete user

	public function myprofile() {
		if ( $this->session->loggedin() ) {

			$this->session->addflash("You need to login to edit your profile.",'error');
	    	$this->session->writeSession();
	    	$this->redirect('user', 'login');

		}

		// based on session id
		$result = $this->db->findone('users', 'id', $this->session->getKey('userid'));

		$this->data['userdata'] = array(
			'username' => $result['username']
			,'email' => $result['email']
			,'created' => $result['created']
		);

		$this->data['page']['slug'] = "myprofile";

	  $this->twig('myprofile', $this->data);
		$this->session->writeSession();
	} //end my profile

	public function saveprofile() {

		$userdb = array();

		// form was submitted prepare helper class
		if ( ! empty( $_POST['formkey'] ) )
			$user = new Nickyeoman\helpers\userHelper();

		// Change username
		if ( !empty($this->post['username']) && $this->post['username'] != $this->session->getKey('userid') ) {

			// make sure username is valid (this includes a db check)
			if ( ! $user->checkUsername() ) {

				//For the view
				$this->data['error'] .= $user->errors['username'];

			} else {

				$username = strtolower(trim( $this->post['username']));
				$userdb['id'] = $this->session->getKey('userid');
				$userdb['username'] = $username;
				// update session data
				$this->session->setKey($username);

			}

		} // end username

		// Change Password
		if ( !empty($this->post['currentpassword']) && !empty($this->post['newpassword']) && !empty($this->post['confirm']) ){

			// check password matches
			if ( ! $user->checkPassword('newpassword', $this->post['confirmpassword']) ){

					$this->adderror($user->errors['password']);  //TODO: Adderror function

			} else {

				$userdb['password'] = $user->userTraits['password'];
				$userdb['id'] = $this->session->getKey('userid');

			}

		}

		// update database
		if ( !empty($userdb) ) {

			$id = $this->db->update("users", $userdb, 'id' );

			// TODO: you need to create the array handler like adderror in the base controller
			$this->data['notice'] = "Profile Updated";

			$this->writeSession();
		}

		//dump($this->post);dump($this->session);dump($this->data);dump($userdb);
		//die();
		$this->redirect('user', 'myprofile');


	} // end save profile

	public function migrate() {

		if ( ! $this->session->loggedin('You need to login to edit users.') )
      		$this->redirect('user', 'login');

    	if ( !$this->session->inGroup('admin', 'You need Admin permissions to edit users.') )
      		$this->redirect('user', 'login');

		$schem = array(
			array(
				'name' => 'username'
				,'type' => 'varchar'
				,'size' => '30'
				,'null' => 'No'
			)
			,array(
				'name' => 'password'
				,'type' => 'varchar'
				,'size' => '70'
				,'null' => 'No'
			)
			,array(
				'name' => 'email'
				,'type' => 'varchar'
				,'size' => '255'
				,'null' => 'No'
			)
			,array(
				'name' => 'validate'
				,'type' => 'varchar'
				,'size' => '32'
				,'null' => 'Yes'
				,'default' => 'NULL'
			)
			,array(
				'name' => 'confirmationToken'
				,'type' => 'varchar'
				,'size' => '255'
				,'null' => 'Yes'
				,'default' => 'NULL'
			)
			,array(
				'name' => 'reset'
				,'type' => 'varchar'
				,'size' => '32'
				,'null' => 'Yes'
				,'default' => 'NULL'
			)
			,array(
				'name' => 'birthdate'
				,'type' => 'DATETIME'
				,'null' => 'No'
				,'default' => 'CURRENT_TIMESTAMP'
			)
			,array(
				'name' => 'created'
				,'type' => 'DATETIME'
				,'null' => 'No'
				,'default' => 'CURRENT_TIMESTAMP'
			)
			,array(
				'name' => 'updated'
				,'type' => 'DATETIME'
				,'null' => 'Yes'
				,'default' => 'CURRENT_TIMESTAMP'
			)
			,array(
				'name' => 'confirmed'
				,'type' => 'tinyint'
				,'size' => '1'
				,'null' => 'No'
				,'default' => '0'
			)
			,array(
				'name' => 'blocked'
				,'type' => 'tinyint'
				,'size' => '1'
				,'null' => 'No'
				,'default' => '0'
			)
			,array(
				'name' => 'admin'
				,'type' => 'varchar'
				,'size' => '255'
				,'null' => 'Yes'
				,'default' => 'NULL'
				,'comment' => 'CSV'
			)
		);
		$this->db->migrate('users',$schem);
		echo '<p><a href="/admin/index">back to admin</a></p>';
	}

} //End Class
