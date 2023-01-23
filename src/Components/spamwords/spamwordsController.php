<?php
namespace Nickyeoman\Framework\Components\spamwords;

USE Nickyeoman\Framework\SessionManager;
USE Nickyeoman\Framework\ViewData;
USE Nickyeoman\Framework\RequestManager;
USE \Nickyeoman\Dbhelper\Dbhelp as DB;

class spamwordsController extends \Nickyeoman\Framework\BaseController {

  function index() {

    $s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);
    $DB = new DB();

    if ( ! $s->loggedin('You need to login to edit spamwords.') )
      $this->redirect('user', 'login');

    if ( !$s->inGroup('admin', 'You need to be an admin to change spamwords.') )
      $this->redirect('user', 'login');
    
    if ( $r->submitted ) {

      if ( ! empty($r->get('spamword') ) ) {

        // Save to Database
        $insertData = array(
          'phrase'      => trim(strtolower( $r->get('spamword') ))
        );

        $id = $DB->create("spamwords", $insertData );
        // Error check the database
        if ( empty( $id ) )
          $v->adderror("There was a problem saving the phrase.", 'db' );
        else
          $v->data['spamwordadded'] = 'Added ' . $r->get('spamword') . ' to the db.';

      }

    }

    $v->data['spamWords'] = array();
    $result = $DB->findall('spamwords', 'id,phrase');
    
    //Set if not null
    if ( !is_null($result) ) {
      foreach ($result as $key => $value){
        $v->data['spamWords'][$key] = $value;
      }
    }

    $DB->close();
    $v->set('adminbar', true);
    $this->twig('spamwords', $v->data,'spamwords');

  }

  /**
   * Deletes a phrase
   */
  public function delete($params) {

    $s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);
    $DB = new DB();

		if ( ! $s->loggedin('You need to login to delete spamwords.') )
      $this->redirect('spamwords', 'index');

    if ( !$s->inGroup('admin', 'You need Admin permissions to delete spamwords.') )
      $this->redirect('spamwords', 'index');

		if ( !empty($params))
			$spamid = $params[0];

		if ( empty($spamid) || !is_numeric($spamid) ) {

			$s->addFlash("Message id not correct", 'error');
			$this->redirect('spamwords', 'index');

    }
    // Check message exists
		$result = $DB->findone('spamwords','id',$spamid);

		if ( empty($result ) ) {

			$s->addFlash("Spamword does not exist", 'error');
			$this->redirect('spamwords', 'index');

		} else {

			$DB->delete('spamwords',"id = $spamid");
			$s->addFlash('Notice: spamword "' . $result['phrase'] . '" removed (deleted)', "notice");

			$this->redirect('spamwords', 'index');
		}

	} // end delete function

}
