<?php
namespace Nickyeoman\Framework\Controllers\Contact;

use Nickyeoman\Framework\Classes\BaseController;
use Nickyeoman\Dbhelper\Dbhelp as DB;
use Nickyeoman\Framework\Attributes\Route;

class contactAdmin extends BaseController {

  #[Route('/admin/contact', methods: ['GET'])]
  public function admin() {

    $s = $this->session;
    
    // redirects
    if ( ! $s->loggedin('You need to login to edit messages.') )
   		redirect('/login');

    if ( ! $s->isAdmin() ) {
      $s->addflash('You need to be an admin to view messages..','error');
      redirect('admin', 'index');
    }

    // view data
    $v = $this->viewClass;
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
    $this->view('@cms/contact/admin');

  }

  #[Route('/admin/contact/view', methods: ['GET'])]
  public function contactView($parms = null) {

    $s = $this->session;
    
    // redirects
    if ( ! $s->loggedin('You need to login to edit messages.') )
   		redirect('login', 'index');

    if ( ! $s->isAdmin() ) {
      $s->addflash('You need to be an admin to view messages..','error');
      redirect('admin', 'index');
    }

    $pid = $parms['msgid'];

    // view data
    $v = $this->viewClass;
    $v->set('pageid', "viewMessage");
    $v->data['contactMessages'] = array();

    if ( ! empty($pid) && is_numeric($pid) ) {
      $DB = new DB();
      $msg = $DB->findone('contactForm', 'id', $pid);
      $DB->close();
      $v->set('msg', $msg);
    } else {

      $s->addflash("Message id not correct or no longer exists", 'error');
      redirect('contact', 'admin');

    }

    $v->set('adminbar', true);
    $this->view('@cms/contact/view');

  } // end view page

  /**
   * Deletes a message /contact/delete/[msgid]
   */
  #[Route('/admin/contact/delete/{msgid}', methods: ['GET'])]
  public function delete($params) {

		$s = $this->session;
    
    // redirects
    if ( ! $s->loggedin('You need to login to edit messages.') )
   		redirect('login', 'index');

    if ( ! $s->isAdmin() ) {
      $s->addflash('You need to be an admin to view messages..','error');
      redirect('admin', 'index');
    }

		if ( !empty($params))
			$msgid = $params['msgid'];

		if ( empty($msgid) || !is_numeric($msgid) ) {

			$s->addFlash("Message id not correct", 'error');
			redirect('contact', 'admin');

		}

		// Check message exists
    $DB = new DB();
		$result = $DB->findone('contactForm','id',$msgid);

		if ( empty($result ) ) {

			$s->addFlash("Message does not exist", 'error');
      $DB->close();
			redirect('contact', 'admin');

		} else {

			$DB->delete('contactForm',"id = $msgid");
			$s->addFlash('Notice: user removed (deleted)', "notice");
      $DB->close();
			redirect('contact', 'admin');
		}

	} // end delete msg

  #[Route('/admin/contact/bademail/{msgid}', methods: ['GET'])]
  public function bademail($params) {

    $s = $this->session;
    
    // redirects
    if ( ! $s->loggedin('You need to login to edit messages.') )
   		$this->redirect('login', 'index');

    if ( ! $s->isAdmin() ) {
      $s->addflash('You need to be an admin to view messages..','error');
      redirect('admin', 'index');
    }

    $v = $this->viewClass;
    $r = $this->requestManager;

    if ( $r->submitted ) {

      $DB = new DB();

      $DB->create('badEmails', ['email' => $r->get('email') ] );
      $where = "`email` = '" . $r->get('email') . "'";
      $DB->delete('contactForm',$where);

      $s->addFlash("Email Added, message removed", 'notice');
      redirect('contact','admin');

    } else {

      $s->addFlash("Post to save bad emails.", 'error');
      redirect('contact','admin');

    }

  }

} // end class
