<?php
namespace Nickyeoman\Framework\Components\contact;

USE Nickyeoman\Framework\SessionManager;
USE Nickyeoman\Framework\ViewData;
USE Nickyeoman\Framework\RequestManager;
USE \Nickyeoman\Dbhelper\Dbhelp as DB;
USE Nickyeoman\Framework\Components\contact\contactHelper as contacthelp;

class contactController extends \Nickyeoman\Framework\BaseController {

  /**
   * Public Contact form
   **/
  public function index() {

    $s = new SessionManager();
		
    $v = new ViewData($s);
    $v->set('menuActive', 'contact');
    $v->set('showform',true);
    $v->set('pageid', "contactform");

    $r = new RequestManager($s, $v);

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
        $v->adderror("Your message contains spammy words ($theword)" );
      // End check spamwords


      // Check Email adress against db
      $result = $DB->findone('badEmails','email',$r->get('email'));
      if ( !empty($result) ) {
        $v->adderror("Your email address has been flagged" );
        $sendEmail = false;
      }

      //load helper
      $ch = new contacthelp($r->post);
      if ( $ch->checkerrors() || !$sendEmail ) {

        foreach ( $ch->error as $val)
          $v->adderror($val);

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
        $v->adderror("There was a problem sending the message." );
      else
        $v->set('showform', false);

      // Send am email
      // TODO: this shouldn't be public
      $this->sendEmail(
        $_ENV['MAIL_FROM_ADDRESS']
        ,'New Message on ' . $_ENV['MAIL_FROM_NAME']
        ,"Here is your message: " . $r->get('message')
      );

      } // end if errors

    } //end if submitted

    $this->twig('contact', $v->data, 'contact');   

  }

  public function admin() {

    $s = new SessionManager();
    
    // redirects
    if ( ! $s->loggedin('You need to login to edit messages.') )
   		$this->redirect('login', 'index');

    if ( ! $s->isAdmin() ) {
      $s->addflash('You need to be an admin to view messages..','error');
      $this->redirect('admin', 'index');
    }

    // view data
    $v = new ViewData($s);
    $v->set('pageid', "contact-admin");
    $v->data['contactMessages'] = array();
      
    //Grab pages from db
    $DB = new DB();
    $result = $DB->findall('contactForm', 'id,email,message,created,unread');
    $DB->close();

    //Set if not null
    if ( !is_null($result) ) {
      foreach ($result as $key => $value){
        $v->data['contactMessages'][$key] = $value;
      }
    }
    
    $v->set('adminbar', true);
    $this->twig('admin', $v->data, 'contact');

  }

  public function view($parms = null) {

    $s = new SessionManager();
    
    // redirects
    if ( ! $s->loggedin('You need to login to edit messages.') )
   		$this->redirect('login', 'index');

    if ( ! $s->isAdmin() ) {
      $s->addflash('You need to be an admin to view messages..','error');
      $this->redirect('admin', 'index');
    }

    $pid = $parms[0];

    // view data
    $v = new ViewData($s);
    $v->set('pageid', "viewMessage");
    $v->data['contactMessages'] = array();

    if ( ! empty($pid) && is_numeric($pid) ) {
      $DB = new DB();
      $msg = $DB->findone('contactForm', 'id', $pid);
      $DB->close();
      $v->set('msg', $msg);
    } else {

      $s->addflash("Message id not correct or no longer exists", 'error');
      $this->redirect('contact', 'admin');

    }

    $v->set('adminbar', true);
    $this->twig('view', $v->data, 'contact');

  } // end view page

  /**
   * Deletes a message /contact/delete/[msgid]
   */
  public function delete($params) {

		$s = new SessionManager();
    
    // redirects
    if ( ! $s->loggedin('You need to login to edit messages.') )
   		$this->redirect('login', 'index');

    if ( ! $s->isAdmin() ) {
      $s->addflash('You need to be an admin to view messages..','error');
      $this->redirect('admin', 'index');
    }

		if ( !empty($params))
			$msgid = $params[0];

		if ( empty($msgid) || !is_numeric($msgid) ) {

			$s->addFlash("Message id not correct", 'error');
			$this->redirect('contact', 'admin');

		}

		// Check message exists
    $DB = new DB();
		$result = $DB->findone('contactForm','id',$msgid);

		if ( empty($result ) ) {

			$s->addFlash("Message does not exist", 'error');
      $DB->close();
			$this->redirect('contact', 'admin');

		} else {

			$DB->delete('contactForm',"id = $msgid");
			$s->addFlash('Notice: user removed (deleted)', "notice");
      $DB->close();
			$this->redirect('contact', 'admin');
		}

	} // end delete msg

  public function bademail($params) {

    $s = new SessionManager();
    
    // redirects
    if ( ! $s->loggedin('You need to login to edit messages.') )
   		$this->redirect('login', 'index');

    if ( ! $s->isAdmin() ) {
      $s->addflash('You need to be an admin to view messages..','error');
      $this->redirect('admin', 'index');
    }

    $v = new ViewData($s); // need for Request, TODO: shouldn't need this
    $r = new RequestManager($s, $v);

    if ( $r->submitted ) {

      $DB = new DB();

      $DB->create('badEmails', ['email' => $r->get('email') ] );
      $where = '`email` = ' . $r->get('email');
      $DB->delete('contactForm',$where);

      $s->addFlash("Email Added, message removed", 'notice');
      $this->redirect('contact','admin');

    } else {

      $s->addFlash("Post to save bad emails.", 'error');
      $this->redirect('contact','admin');

    }

  }

} // end class
