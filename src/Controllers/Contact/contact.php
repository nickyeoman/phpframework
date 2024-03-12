<?php
namespace Nickyeoman\Framework\Controllers\Contact;

use Nickyeoman\Framework\Classes\BaseController;
use Nickyeoman\Dbhelper\Dbhelp as DB;
use Nickyeoman\Framework\Helpers\contactHelper as contacthelp;
use Nickyeoman\Framework\Attributes\Route;

class contact extends BaseController {

  /**
   * Public Contact form
   **/
  #[Route('/contact', methods: ['GET'])]
    public function index() {

    // Grab 
    $v = $this->viewClass;

    $v->set('menuActive', 'contact');
    $v->set('showform',true);
    $v->set('pageid', "contactform");

    $this->view('@cms/contact/contact');

  }

  #[Route('/contact', methods: ['POST'])]
  public function contacted() {

    $s = $this->session;
    $v = $this->viewClass;
    $r = $this->request;

    // Did we get anything?
    if ( $r->submitted ) {

      // sendEmail clarifies if we are going to save to the db and send and email
      // false will stop the process and reshow the page
      $sendEmail = true;

      $DB = new DB();

      // Check Spam words
      $results    = $DB->findall('spamwords', 'phrase');
      $checktext  = ' ' . strtolower( $r->get('message') );
      $theword    = '';
      
      foreach ($results as $val) {
        if ( stripos($checktext, $val['phrase'] ) ) {
          $sendEmail = false;
          $theword = $val['phrase'];
        }
        
      }

      if ( !$sendEmail )
        $s->addFlash("Your message contains spammy words ($theword)",'error' );
      // End check spamwords

      // Check Email adress against db
      $result = $DB->findone('badEmails','email',$r->get('email'));
      if ( !empty($result) ) {
        $s->addFlash("Your email address has been flagged", 'error' );
        $sendEmail = false;
      }

      //load helper
      $ch = new contacthelp($r->post);
      if ( $ch->checkerrors() || !$sendEmail ) {

        foreach ( $ch->error as $val)
          $s->addFlash($val,'error');

      } else {

        // Save to Database
        $insertData = array(
          'email'      => $r->get('email'),
          'message'    => $r->get('message')
        );

      // Insert/create database
      $id = $DB->create("contactForm", $insertData );
      $DB->close();

      // Error check the database
      if ( empty( $id ) )
        $v->addFlash("There was a problem sending the message.", 'error');

      // Send am email
      // TODO: this shouldn't be public
      // $this->sendEmail(
      //   $_ENV['MAIL_FROM_ADDRESS']
      //   ,'New Message on ' . $_ENV['MAIL_FROM_NAME']
      //   ,"Here is your message: " . $r->get('message')
      // );

      } // end if errors

    } //end if submitted

    $s->addPost($r->post);
    $s->writeSession();
    redirect('/contact');
    
  }

} // end class
