<?php
namespace Nickyeoman\Framework\Helpers;
use \Nickyeoman\Validation\Validate as Validate;

class contactHelper {

  public $error  = array();
  public $notice = array();
  public $post   = array();
  public $valid;

  /*
  * get the data
  */
  public function __construct($post = null) {

    // We are going to use validate
    $valid = new Validate();

    // Check email
    if ( ! $valid->isEmail( $post['email'] ) )
      $this->error[] = "Not a valid E-mail address: $email";

    // Check Message
    $message = trim($post['message']);
    if ( empty( $message ) )
      $this->error[] = "Message can't be blank.";

    // TODO: this should grab from the database
    $spamwordstemp = 'boobs,tits,cunt';
    if ( $valid->checkSpam($message, $spamwordstemp ) )
      $this->error[] = "Contains spammy words.";

    if ( !empty($badwords) )
      $this->session->addflash("You can't use these words: $badwords",'error');

    $this->post = $post;

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
