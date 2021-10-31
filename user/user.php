<?php
class userController extends Nickyeoman\Framework\BaseController {

	public bool $error = false;

	public function index(){

		$user = new Nickyeoman\Framework\User();

		if ( ! $user->loggedin() ) {

			$this->session['notice'] = "You need to login.";
			$this->writeSession();
			$this->redirect('user', 'login');

		}

		$this->twig('user/index', $this->data);
		// If the user is logged in we don't need to proceed

	} // end function index

	/**
	* /user/registration
	**/
	public function registration(){

		// Create new user class
    $user = new Nickyeoman\Framework\User();

    // If the user is logged in we don't need to proceed
    if ( $user->loggedin() )
      $this->redirect('user', 'index');

		// Session data
    $this->data['formkey']  = $this->session['formkey']; // Form cross site protection

    //check Form Was submitted is enabled
    if ( ! empty( $_POST['formkey'] ) ){

			//check session matches
      if ( $_POST['formkey'] == $this->session['formkey'] ) {

				//clean the data (true = good, false = bad)
				if ( ! $user->checkUsername() ) {

					$this->error = true;

					//For the view
					$this->data['error'] .= $user->errors['username'];

				}

				// check email address
				if ( ! $user->checkEmail(false) ){

					$this->error = true;
					//For the view
					$this->data['error'] .= $user->errors['email'];

				}

				// check password address
				if ( ! $user->checkPassword() ){

					$this->error = true;
					//For the view
					$this->data['error'] .= $user->errors['password'];

				}

				//No Errors, then save
				if (! $this->error) {

					//write to database
					$user->newuser();

					// Send registration Email
					$user->sendRegistrationEmail();

					//redirect to verify page
					$this->redirect($controller = 'user', $action = 'validate');

				} else {

					$this->data['post']['email'] = $_POST['email'];
					$this->data['post']['username'] = $_POST['username'];

				}

      } else {
        //TODO: cross site scripting, log this
      }

    }

    //Display view
    $this->twig('user/registration', $this->data);
		$this->writeSession();

	}
	//end register action (page)

	public function validate(){

		if ( isset( $_GET['valid'] ) ) {

			//A validation key exists, we better check it
			$user = new Nickyeoman\Framework\User();

			// TODO: Check user isn't already valid.

			// Check if the key is valid
			if ( ! $user->checkValidationKey($_GET['valid']) ) {

				$this->error = true;
				//For the view
				$this->data['error'] = $user->errors['valid'];

			} else {

				$this->data['notice'] = 'Your Email Address is now validated,  you can login';

			}

		}
		//end check key

		// display view
		$this->twig('user/validate', $this->data);

	}
	// End validate action (page)

	public function logout() {

		//TODO: Logout message
		$this->destroySession();
		$this->redirect('index', 'index');

	}

	/**
	* Login Form
	**/
	public function login() {

		// initiate class
		$user = new Nickyeoman\Framework\User();

		// If the user is logged in we don't need to proceed
		if ( $user->loggedin() ) {
			$this->redirect('user', 'index');
		}

		// Check if form Submitted
		if ( $this->post['submitted'] ) {

			//Process the form
			if ( $user->login() ) {
				$this->redirect('user', 'index');
			} else {

				//prep errors, login failed

				if ( !empty($user->errors) ) {

					foreach( $user->errors as $k => $v ) {

						$this->adderror($string = $v, $k );

					}
					//end foreach

				}
				//endif

			}
			//end else

		}
		// end POST SUBMITTED

		$this->twig('user/login', $this->data);
		$this->writeSession();

	}
	//END Login

    /**
     *  Forgot Password Controller
     */
	public function forgot() {

		$user = new Nickyeoman\Framework\User();

		// If the user is logged in we don't need to proceed
		if ( $user->loggedin() )
		    $this->redirect('user', 'index');

		$this->data['formkey'] = $this->session['formkey'];       // Form cross site protection

		//check Form Was submitted is enabled
		if ( ! empty( $_POST['formkey'] ) ) {

			//check session matches
			if ( $_POST['formkey'] == $this->session['formkey'] ) {

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
		$this->twig('user/forgot', $this->data);
		$this->writeSession();
	}

	public function reset() {

        if ( isset( $_GET['valid'] ) ) {

            bdump($_GET, 'Get Data');

            //A validation key exists, we better check it
            $user = new Nickyeoman\Framework\User();

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
            if ( $_POST['formkey'] == $this->session['formkey'] ) {

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
                        $this->writeSession();

                        $this->redirect($controller = 'user', $action = 'login');  //redirect to login page

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

        $this->twig('user/reset', $this->data); // display view

    } //End Function reset

} //End Class
