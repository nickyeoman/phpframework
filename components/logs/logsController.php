<?php

//TODO: the syntax on this page is a mess
class logsController extends Nickyeoman\Framework\BaseController {

  public function index() {

    if ( ! $this->session->loggedin('You need to login to view logs.') )
      $this->redirect('user', 'login');

    if ( !$this->session->inGroup('admin', 'You need Admin permissions to edit pages.') )
      $this->redirect('user', 'login');

    //Grab pages
    //$result = $this->db->findall('logs', 'id,ip,url,time');
    // do the search
$sql = <<<EOSQL
SELECT id,ip,url,time, count(url) as num FROM `logs` WHERE num > 1 GROUP BY url ORDER BY num DESC
EOSQL;

   $result = $this->db->query($sql);

    $this->data['logs'] = array();
    foreach ($result as $key => $value){

      $this->data['logs'][$key] = $value;

    }

    $this->data['pageid'] = "page-admin";
    $this->twig('notfound', $this->data);

  }

   public function delete($parms){

     if ( !$this->session->loggedin('You need to login to delete logs.', true) )
        $this->redirect('user', 'login');

      if ( !$this->session->inGroup('admin', 'You need Admin permissions to delete logs.',true) )
        $this->redirect('user', 'login');

      $deleteid = mysqli_real_escape_string($this->db->con, $parms[0]);
      $this->db->delete('logs', "id = $deleteid");
      $this->session->addflash("Removed Log entry $deleteid.",'notice');
      $this->session->writeSession();
      $this->redirect('logs', 'index');
   }

   public function deletegroup($parms){

    if ( !$this->session->loggedin('You need to login to delete logs.', true) )
      $this->redirect('user', 'login');

    if ( !$this->session->inGroup('admin', 'You need Admin permissions to delete logs.',true) )
        $this->redirect('user', 'login');

      $deleteid = implode('/', $parms);
      $deleteid = mysqli_real_escape_string($this->db->con, $deleteid);
      $this->db->delete('logs', "`url` = '$deleteid'");
      $this->session->addflash("Removed Log entries for $deleteid.",'notice');
      $this->redirect('logs', 'index');
   }

   public function deleteall(){

     if ( !$this->session->loggedin('You need to login to delete logs.', true) )
        $this->redirect('user', 'login');

    if ( !$this->session->inGroup('admin', 'You need Admin permissions to delete logs.',true) )
        $this->redirect('user', 'login');

      $this->db->delete('logs', "`id` > 1 AND `title` LIKE '%404%'");
      $this->session->addflash("Removed All 404 Log entries.",'notice');
      $this->redirect('logs', 'index');
   }

   public function deletequerystrings(){

     if ( !$this->session->loggedin('You need to login to delete logs.', true) )
        $this->redirect('user', 'login');

    if ( !$this->session->inGroup('admin', 'You need Admin permissions to delete logs.',true) )
        $this->redirect('user', 'login');

      $this->db->delete('logs', "url LIKE '%?%' AND `title` LIKE '%404%'");
      $this->session->addflash("Removed Log entries for entries containing query strings.",'notice');
      $this->redirect('logs', 'index');
   }


   }
