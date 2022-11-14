<?php
class spamwordsController extends Nickyeoman\Framework\BaseController {

  function index() {

    if ( ! $this->session->loggedin('You need to login to edit spamwords.') )
      $this->redirect('user', 'login');

    if ( !$this->session->inGroup('admin', 'You need to be an admin to change spamwords.') )
      $this->redirect('user', 'login');
    
    if ( $this->post['submitted'] ) {

      $addword = true;

      if ( ! empty($this->post['spamword']) ) {

        // Save to Database
        $insertData = array(
          'phrase'      => trim(strtolower( $this->post['spamword'] ))
        );
        $id = $this->db->create("spamwords", $insertData );
        // Error check the database
        if ( empty( $id ) )
          $this->adderror("There was a problem saving the phrase.", 'db' );
        else
          $this->data['spamwordadded'] = 'Added ' . $this->post['spamword'] . ' to the db.';

      }

    }

    $this->data['spamWords'] = array();
    $result = $this->db->findall('spamwords', 'id,phrase');
    
    //Set if not null
    if ( !is_null($result) ) {
      foreach ($result as $key => $value){
        $this->data['spamWords'][$key] = $value;
      }
    }


    $this->twig('spamwords', $this->data);

  }

  /**
   * Deletes a phrase
   */
  public function delete($params) {

		if ( ! $this->session->loggedin('You need to login to delete spamwords.') )
      $this->redirect('spamwords', 'index');

    if ( !$this->session->inGroup('admin', 'You need Admin permissions to delete spamwords.') )
      $this->redirect('spamwords', 'index');

		if ( !empty($params))
			$spamid = $params[0];

		if ( empty($spamid) || !is_numeric($spamid) ) {

			$this->session->addFlash("Message id not correct", 'error');
			$this->redirect('spamwords', 'index');

    }
    // Check message exists
		$result = $this->db->findone('spamwords','id',$spamid);

		if ( empty($result ) ) {

			$this->session->addFlash("Spamword does not exist", 'error');
			$this->redirect('spamwords', 'index');

		} else {

			$this->db->delete('spamwords',"id = $spamid");
			$this->session->addFlash('Notice: spamword "' . $result['phrase'] . '" removed (deleted)', "notice");

			$this->redirect('spamwords', 'index');
		}

	} // end delete function

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
				'name' => 'phrase'
				,'type' => 'varchar'
				,'size' => '255'
				,'null' => 'No'
			)
		);
		$this->db->migrate('spamwords',$schem);

		echo '<p><a href="/user/admin">back to admin</a></p>';

	}
  // end migrate
}
