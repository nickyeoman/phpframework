<?php
class contactController extends Nickyeoman\Framework\BaseController {

  /**
   * Public Contact Us form
   **/
  public function index() {

    // set variables
    $this->data['menuActive'] = 'contact'; //active menu
    $this->data['showform'] = true; // show form by default (hide if entered)
    $this->data['pageid'] = "contactform"; // for the css

    //debug
    bdump($this->post, 'Post Data From Framework');

    if ( $this->post['submitted'] ) {

      // sendEmail clarifies if we are going to save to the db and send and email
      // false will stop the process and reshow the page
      $sendEmail = true;

      // Check Spam words


      // Check Email against db
      $result = $this->db->findone('badEmails','email',$this->post['email']);
      if ( !empty($result) ) {
        $this->adderror("Your email address has been flagged", 'error' );
        $sendEmail = false;
      }

      //load helper
      $ch = new Nickyeoman\helpers\contactHelper($this->post);
      if ( $ch->checkerrors() || !$sendEmail ) {

        foreach ( $ch->error as $k => $v)
          $numerr = $this->adderror($v, $k );

      } else {

        // Save to Database
        $insertData = array(
          'email'      => $this->post['email'],
          'message'    => $this->post['message']
        );

      // Insert/create database
      $id = $this->db->create("contactForm", $insertData );

      // Error check the database
      if ( empty( $id ) )
        $this->adderror("There was a problem sending the message.", 'db' );
      else
        $this->data['showform'] = false;

      // Send am email
      $this->sendEmail(
        'nick.yeoman@gmail.com'
        ,'New Message At NickYeoman.com'
        ,"Here is your message: " . $this->post['message']
      );

      } // end if errors

    } //end if submitted

    $this->twig('contact', $this->data);

  }

  public function admin() {

    if ( ! $this->session->loggedin('You need to login to edit messages.') )
   		$this->redirect('user', 'login');

    if ( !$this->session->inGroup('admin', 'You need to be an admin to view messages..') )
      $this->redirect('user', 'login');

    //Variables
    $this->data['pageid'] = "contact-admin";
    $this->data['contactMessages'] = array();

    //Grab pages from db
    $result = $this->db->findall('contactForm', 'id,email,message,created,unread');
    
    //Set if not null
    if ( is_null($result) ) {
      foreach ($result as $key => $value){
        $this->data['contactMessages'][$key] = $value;
      }
    }
    
    $this->twig('admin', $this->data);

  }

  public function view($parms = null) {

    if ( ! $this->session->loggedin('You need to login to edit messages.') )
   		$this->redirect('user', 'login');

    if ( !$this->session->inGroup('admin', 'You need to be an admin to view messages..') )
      $this->redirect('user', 'login');

    // variables
    $this->data['pageid'] = "viewMessage"; // for the css
    $pid = $parms[0];

    if ( empty($msgid) || !is_numeric($msgid) ) {
      //Grab pages
      $this->data['msg'] = $this->db->findone('contactForm', 'id', $pid);
    } else {

      $this->session->addFlash("Message id not correct or no longer exists", 'error');
      $this->redirect('contact', 'admin');

    }

    $this->twig('view', $this->data);

  } // end view page

  /**
   * Deletes a message /contact/delete/[msgid]
   */
  public function delete($params) {

		if ( ! $this->session->loggedin('You need to login to delete messages.') )
      		$this->redirect('user', 'login');

    if ( !$this->session->inGroup('admin', 'You need Admin permissions to delete messages.') )
        $this->redirect('user', 'login');

		if ( !empty($params))
			$msgid = $params[0];

		if ( empty($msgid) || !is_numeric($msgid) ) {

			$this->session->addFlash("Message id not correct", 'error');
			$this->redirect('contact', 'admin');

		}

		// Check message exists
		$result = $this->db->findone('contactForm','id',$msgid);

		if ( empty($result ) ) {

			$this->session->addFlash("Message does not exist", 'error');
			$this->redirect('contact', 'admin');

		} else {

			$this->db->delete('contactForm',"id = $msgid");
			$this->session->addFlash('Notice: user removed (deleted)', "notice");

			$this->redirect('contact', 'admin');
		}

	} // end delete msg

  public function bademail($params) {

    if ( ! $this->session->loggedin('You need to login to report Emails.') )
      		$this->redirect('user', 'login');

    if ( !$this->session->inGroup('admin', 'You need Admin permissions to report emails.') )
        $this->redirect('user', 'login');

    if ( $this->post['submitted'] ) {

      $this->db->create('badEmails', ['email' => $this->post['email'] ] );
      $this->db->delete('contactForm',"`email` = '{$this->post['email']}'");

      $this->session->addFlash("Email Added, message removed", 'notice');
      $this->redirect('contact','admin');

    } else {

      $this->session->addFlash("Post to save bad emails.", 'error');
      $this->redirect('contact','admin');

    }

  }

  /**
   * Make sure your db is the way you want it
   */
  public function migrate() {

		if ( ! $this->session->loggedin('You need to login to edit messages.') )
      $this->redirect('user', 'login');

    if ( !$this->session->inGroup('admin', 'You need Admin permissions to edit messages.') )
      $this->redirect('user', 'login');

    // Contact form table
		$schem = array(
			array(
				'name' => 'email'
				,'type' => 'varchar'
				,'size' => '255'
				,'null' => 'No'
			)
			,array(
				'name' => 'message'
				,'type' => 'TEXT'
				,'null' => 'Yes'
        ,'default' => 'NULL'
			)
			,array(
				'name' => 'created'
				,'type' => 'DATETIME'
				,'null' => 'No'
				,'default' => 'CURRENT_TIMESTAMP'
			)
			,array(
				'name' => 'unread'
				,'type' => 'tinyint'
				,'size' => '1'
				,'null' => 'No'
				,'default' => '1'
        ,'comment' => 'Unread is true by default'
			)
		);
		$this->db->migrate('contactForm',$schem);

    // Bad Email Address Table
    $badEmailTable = array(
			array(
				'name' => 'email'
				,'type' => 'varchar'
				,'size' => '255'
				,'null' => 'No'
        ,'unique' => 'Yes'
			)
		);
		$this->db->migrate('badEmails',$badEmailTable);

		echo '<p><a href="/user/admin">back to admin</a></p>';
	}
  // end migrate

} // end class
