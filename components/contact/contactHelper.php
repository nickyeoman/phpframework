<?php
namespace Nickyeoman\helpers;
USE \Nickyeoman\Validation;

class contactHelper {

  public $error = array();
  public $notice = array();
  public $post = array();
  public $valid;

  /*
  * get the data
  */
  public function __construct($post = null) {

    if ( $post['submitted'] ) {

      // We are going to use validate
      $valid = new \Nickyeoman\Validation\Validate();

      // Check email
      if ( ! $valid->isEmail( $post['email'] ) )
        $this->error[] = "Not a valid E-mail address: $email";

      // Check Message
      $message = trim($post['message']);
      if ( empty( $message ) )
        $this->error[] = "Message can't be blank.";

      // debug
      bdump($post, "Modified Post in Helper");
      bdump($this->error, "Error Array in helper");
      $this->post = $post;

    }
    // end if submitted

  }
  // end construct

  public function checkerrors() {

    if ( empty( $this->error ) )
      // no errors
      return false;
    else
      //errors
      return true;

  }
  // end checkerror()

}
