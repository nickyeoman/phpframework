<?php
namespace Nickyeoman\helpers;
USE \Nickyeoman\Validation;

class userHelper {

  public $userTraits = array(
    "uid"               => '',
    "username"          => '',
    "password"          => '',
    "email"             => '',
    "confirmationToken" => '',
    "reset"             => '',
    "created"           => '',
    "updated"           => '',
    "blocked"           => '',
    "admin"             => '',
    "loggedin"          => false,
  );
  public $errors = array();
  public $valid; // Validation Class
  public $db; // Database class

  /*
  * Make sure there is not a session
  */
  public function __construct() {

    $this->checkSession();
    $this->valid = new \Nickyeoman\Validation\Validate();

    // debugging
    bdump($this->userTraits, "User Traits.");

    //database
    if ( ! empty( $_ENV['DBUSER'] ) )
      $this->db = new \Nickyeoman\Dbhelper\Dbhelp($_ENV['DBHOST'], $_ENV['DBUSER'], $_ENV['DBPASSWORD'], $_ENV['DB'], $_ENV['DBPORT'] );

  }
  // End Construct

  /*
  * Set session array if exists
  */
  private function checkSession() {

    if ( ! empty( $_SESSION['NY_FRAMEWORK_USER']['email'] ) ) {

      $this->userTraits = $_SESSION['NY_FRAMEWORK_USER'];

    } else {

      $this->userTraits['loggedin'] = false;

    }

  }
  // End Check Session

  /**
  * Check if login is set
  **/
  public function loggedin() {

    if ( $this->userTraits['loggedin'] ) {

      return true;

    }

    return false;

  }
  //End loggedin

  /**
  * Checks if username is valid based on length
  * Checks username doesn't exist in database
  * Return true username is valid and unused
  * Return false username is not valid or in use $user->errors['username']
  **/
  public function checkUsername( $minlength = 4 ) {

    //Username (grabs POST of same name or validates input)
    $username = $this->valid->clean( 'username' );

    //validate minimum length
    if ( ! $this->valid->minLength( $username, $minlength ) ) {

      $this->errors['username'] = "<div id=\"error-username\">Username must be at least $minlength characters</div>";
      $this->userTraits['username'] = '';
      return false;

    }

    //Check database
    $existingUser = $this->db->findone('users', 'username', $username);

    if ( ! empty( $existingUser ) ) {

      $this->errors['username'] = '<div id="error-username">Username is taken.</div>';
      $this->userTraits['username'] = '';
      return false;

    }

    // Checks all seem good

    $this->userTraits['username'] = $username;
    $this->userTraits['confirmationToken'] = md5( $username . $_ENV['SALT'] );

    return true;

  }
  //End Check username

  /**
  * Checks if email is valid and not already in system
  * unless exists param is passed then checks if in database
  **/
  public function checkEmail($exists = false){

    //Email
    $email = $this->valid->clean( 'email' );

    //check email address is valid (if not do things)
    if ( ! $this->valid->isEmail( $email ) ) {
      $this->errors['email'] = "<div id=\"error-email\">Not a valid E-mail address: $email</div>";
      $this->userTraits['email'] = '';
      return false;
    }

    //Check database
    $existingEmail = $this->db->findone('users', 'email', $email);

    if ( ! empty($existingEmail) ) {

        $this->userTraits['email'] = $email;
        if ( $exists )
          return true;
        else
          return false;

    } else {

      if ( ! $exists ) {
        $this->userTraits['email'] = $email;
        return true;
      } else {
        $this->errors['email'] = '<div id="error-email">Email is not registered.</div>';
        $this->userTraits['email'] = '';
        return false;
      }

    }

    // Checks all seem good
    $this->userTraits['email'] = $email;
    return true;

  }
  //End Check email

  /**
  * Check if valid password
  * check if password matches a confirm
  * if so save (encrypted) to object
  **/
  public function checkPassword( $key = 'password', $confirm = '', $minlength = 8, $maxlength = 70) {

    $password = $_POST[$key];

    //validate minimum length
    if ( ! $this->valid->minLength( $password, $minlength ) ) {
      $this->errors['password'] = "<div id=\"error-password\">Password must be at least $minlength characters long.</div>";
      return false;
    }

    //validate max length
    if ( ! $this->valid->maxLength( $password, $maxlength ) ) {
      $this->errors['password'] = "<div id=\"error-password\">Password must be shorter than $maxlength characters.</div>";
      return false;
    }

    //Check it matches confirm
    if ( ! empty($confirm) ) {

      if ( !empty($_POST[$confirm]) ) {

        if ( $password != $_POST[$confirm] ) {

          $this->errors['password'] = '<div id="error-password">Passwords do not match</div>';
          return false;

        }

      }

    } // end confirm

    // encrypt password
    $this->userTraits['password'] = password_hash($password, PASSWORD_BCRYPT);
    return true;

  }
  //end checkPassword()

  /*
  * Write the info to the database
  * Accepts an array
  */
  public function newuser() {

    $user = array(
      'username'          => $this->userTraits['username'],
      'email'             => $this->userTraits['email'],
      'password'          => $this->userTraits['password'],
      'confirmationToken' => $this->userTraits['confirmationToken'],
      'confirmed'         => '0',
      'blocked'           => '0',
      'created'        => date("Y-m-d H:i:s"),
      'updated'        => date("Y-m-d H:i:s")
    );

    $id = $this->db->create("users", $user );

    if ( ! empty( $id ) ) {
      return true;
    }

    $this->errors['database'] = "<div id=\"error-user\">User not created in database</div>";
    return false;

  }
  // End Create New User

  //TODO: send to a text file or output if debugging
  public function sendRegistrationEmail() {

    // https://packagist.org/packages/nette/mail
    $mail = new \Nette\Mail\Message;

    $mail->setFrom($_ENV['MAIL_FROM_NAME'] . ' <' . $_ENV['MAIL_FROM_ADDRESS'] .'>')
      ->addTo( $this->userTraits['email'] )
      ->setSubject('Validate your email address at GOPOLI')
      ->setBody('Hello, please go to this link to veirfy your email address ' . $_ENV['BASEURL'] . 'user/validate/?valid=' . $this->userTraits['confirmationToken'] . '&email=' . $this->userTraits['email'])
    ;

    $mailer = new \Nette\Mail\SmtpMailer([
       'host'     => $_ENV['MAIL_HOST'],
       'username' => $_ENV['MAIL_USERNAME'],
       'password' => $_ENV['MAIL_PASSWORD'],
       'secure'   => $_ENV['MAIL_ENCRYPTION'],
       'port'     => $_ENV['MAIL_PORT'],
     ]);

    $mailer->send($mail);

  }
  //end Send Registration Email


  /**
  * Creates a password reset key, stores it in db and emails to  user
  */
  public function passwordReset() {

    $resetkey = md5( $this->userTraits['email'] . $_ENV['SALT'] );

    //Passed Checks, Remove Key from database
    $user = $this->db->findone('users', 'email', $this->userTraits['email']);

    // Create array for db
    $userdb = array(
      'id'                 => $user['id'],
      'resetPasswordToken' => $resetkey,
      'updated_at'         => date("Y-m-d H:i:s")
    );

    // Update db
    $id = $this->db->update("users", $userdb, 'id' );

    // https://packagist.org/packages/nette/mail
    $mail = new \Nette\Mail\Message;

    $mail->setFrom($_ENV['MAIL_FROM_NAME'] . ' <' . $_ENV['MAIL_FROM_ADDRESS'] .'>')
      ->addTo( $this->userTraits['email'] )
      ->setSubject('Reset your password at GOPOLI')
      ->setBody('Hello, please go to this link to change your password ' . $_ENV['BASEURL'] . 'user/reset/?valid=' . $resetkey . '&email=' . $this->userTraits['email'])
    ;

    $mailer = new \Nette\Mail\SmtpMailer([
       'host'     => $_ENV['MAIL_HOST'],
       'username' => $_ENV['MAIL_USERNAME'],
       'password' => $_ENV['MAIL_PASSWORD'],
       'secure'   => $_ENV['MAIL_ENCRYPTION'],
       'port'     => $_ENV['MAIL_PORT'],
     ]);

    $mailer->send($mail);

  }
  //end Send Reset Email

  /*
  * Checks complete, update db
  */
  public function resetUserPassword() {

        // Create array for db
        $userdb = array(
          'id'                 => $this->userTraits['uid'],
          'resetPasswordToken' => '',
          'password'           => $this->userTraits['password'],
          'updated_at'         => date("Y-m-d H:i:s")
        );

        // Update db
        $id = $this->db->update("users", $userdb, 'id' );
  }
  // End resetUserPassword

  /**
  * Is the Validation key correct?
  * take a key and and email, check the database
  * */
  public function checkValidationKey($key2check) {

    $cleanKey = $this->valid->clean( $key2check );
    $email    = $this->valid->clean( $_GET['email'] );

    // Check for empty key
    if ( empty( $cleanKey ) ) {
      $this->errors['valid'] = '<div id="error-key">Key is Empty</div>';
      return false;
    }
    // cleankey is not empty

    // Check if email exists
    if ( $this->valid->isEmail( $email ) ) {

      $user = $this->db->findone('users', 'email', $email);

    } else {

      $this->errors['valid'] = '<div id="error-email">Key or Email not valid</div>';
      return false;

    }
    // the email address exists

    if ( $cleanKey != $user['confirmationToken']) {

      $this->errors['valid'] = '<div id="error-token">cleankey: Key not valid</div>';
      return false;

    }

    //Passed Checks, Remove Key from database
    $user = array(
      'id'                => $user['id'],
      'confirmationToken' => 'NULL',
      'confirmed'         => '1',
      'updated_at'        => date("Y-m-d H:i:s")
    );

    $id = $this->db->update("users", $user, 'id' );

    if ( ! empty( $id ) ) {
      return true;
    }

    return false;

  } //end Check validationkey

    /**
     * Is the password reset key correct?
     * take a key and email, check the database
     * */
  public function checkResetKey($key2check) {

      $cleanKey = $this->valid->clean( $key2check );

      // Check for empty key
      if ( empty( $cleanKey ) ) {
          $this->errors['valid'] = '<div id="error-key">Key is Empty</div>';
          return false;
      }
      // cleankey is not empty

      // probably alrady checked email by now, but just in case
      // TODO: add session
      if ( ! empty( $_POST['email'] ) )
          $email = $this->valid->clean( $_POST['email'] );
      else if ( ! empty( $_GET['email'] ) )
          $email = $this->valid->clean( $_GET['email'] );
      else {
          $this->errors['valid'] = '<div id="error-email">Email is Empty</div>';
          return false;
      }

      // Check if email exists
      if ( $this->valid->isEmail( $email ) ) {

          $user = $this->db->findone('users', 'email', $email);

      } else {

          $this->errors['valid'] = '<div id="error-email">Email address is not valid</div>';
          return false;

      }
      // the email address exists

      // Check database returned something
      if ( isset( $user ) )
          $this->userTraits['uid'] = $user['id'];
      else {
          $this->errors['valid'] = '<div id="error-email">Email not valid</div>';
          return false;
      }

      if ( $cleanKey != $user['resetPasswordToken']) {

          $this->errors['valid'] = '<div id="error-token">Key or Email not valid</div>';
          return false;

      }

      return true;

  }
  //end Check validationkey

  /**
  * Checks if a user login request was valid
  * form name is the name of the post
  **/
  public function login($formName = 'login') {

    //Check if username or email exists
    if ( $this->valid->isEmail( $_POST[$formName] ) ) {

      //validate with email
      $userdb = $this->db->findone('users', 'email', $_POST[$formName]);

    } else {

      //validate with username
      $userdb = $this->db->findone('users', 'username', $_POST[$formName]);

    }

    if ( empty($userdb) ) {

      $this->errors['login'] = 'Username or password invalid';
      return false;

    }

    if ( ! empty( $userdb['confirmationToken'] ) && $userdb['confirmationToken'] != "NULL" ) {

      $this->errors['login'] = '<div id="error-email">Please validate your email address</div>';
      return false;

    }

    if ( $userdb['blocked'] == 1 ) {

      $this->errors['login'] = '<div id="error-login">Your account has been blocked</div>';
      return false;

    }

    if (  empty( $_POST['password'] ) ) {

      $this->errors['login'] = '<div id="error-blank-password">blank password</div>';
      return false;

    }

    if ( ! password_verify( $_POST['password'] , $userdb['password'])  ) {

      $this->errors['login'] = 'Invalid login';
      return false;

    }

    //Passed all the checks

    //set session
    $_SESSION['NY_FRAMEWORK_USER'] = [
      'id' => $userdb['id'],
      'username' => $userdb['username'],
      'email' => $userdb['email'],
      'loggedin' => true,
      ];

    $this->userTraits['loggedin'] = true;
    return true;
  }

}
