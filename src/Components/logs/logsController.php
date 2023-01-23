<?php
namespace Nickyeoman\Framework\Components\logs;

USE Nickyeoman\Framework\SessionManager;
USE Nickyeoman\Framework\ViewData;
USE \Nickyeoman\Dbhelper\Dbhelp as DB;

//TODO: the syntax on this page is a mess
class logsController extends \Nickyeoman\Framework\BaseController {

  public function index() {

    $s = new SessionManager();
    
    // redirects
    if ( ! $s->loggedin('You need to login to view logs.') )
   		$this->redirect('login', 'index');

    if ( ! $s->isAdmin() ) {
      $s->addflash('You need Admin permissions to edit pages.','error');
      $this->redirect('admin', 'index');
    }

    // view data
    $v = new ViewData($s);
    $v->set('pageid', "logs-admin");

    //Grab pages
    //$result = $this->db->findall('logs', 'id,ip,url,time');
    // do the search
    $sql = <<<EOSQL
SELECT id,ip,url,time, count(url) as num FROM `logs` GROUP BY url HAVING count(url) > 1 ORDER BY num DESC
EOSQL;

    $DB = new DB();
    $result = $DB->query($sql);
    $DB->close();

    $v->data['logs'] = array();

    foreach ($result as $key => $value){

      $v->data['logs'][$key] = $value;

    }

    $v->set('adminbar', true);
    $v->set('pageid',"page-admin");
    $this->twig('logs', $v->data, 'logs');

  }

   public function delete($parms){

    $s = new SessionManager();
    
    // redirects
    if ( ! $s->loggedin('You need to login to delete logs.') )
   		$this->redirect('login', 'index');

    if ( ! $s->isAdmin() ) {
      $s->addflash('You need Admin permissions to delete logs.','error');
      $this->redirect('admin', 'index');
    }

    $DB = new DB();
    $deleteid = mysqli_real_escape_string($DB->con, $parms[0]);
    $DB->delete('logs', "id = $deleteid");
    $DB->close();
    $s->addflash("Removed Log entry $deleteid.",'notice');
    $s->writeSession();
    $this->redirect('logs', 'index');

   }

   public function deletegroup($parms){

      $s = new SessionManager();

      // redirects
      if ( ! $s->loggedin('You need to login to delete logs.') )
        $this->redirect('login', 'index');

      if ( ! $s->isAdmin() ) {
        $s->addflash('You need Admin permissions to delete logs.','error');
        $this->redirect('admin', 'index');
      }

      $deleteid = implode('/', $parms);
      $DB = new DB();
      $deleteid = mysqli_real_escape_string($DB->con, $deleteid);
      $DB->delete('logs', "`url` = '$deleteid'");
      $DB->close();
      $s->addflash("Removed Log entries for $deleteid.",'notice');
      $this->redirect('logs', 'index');
   }

   public function deleteall(){

    $s = new SessionManager();

      // redirects
      if ( ! $s->loggedin('You need to login to delete logs.') )
        $this->redirect('login', 'index');

      if ( ! $s->isAdmin() ) {
        $s->addflash('You need Admin permissions to delete logs.','error');
        $this->redirect('admin', 'index');
      }

      $DB = new DB();
      $DB->delete('logs', "`id` > 1 AND `title` LIKE '%404%'");
      $DB->close();
      $s->addflash("Removed All 404 Log entries.",'notice');
      $this->redirect('logs', 'index');
   }

   public function deletequerystrings(){

    $s = new SessionManager();

    // redirects
    if ( ! $s->loggedin('You need to login to delete logs.') )
      $this->redirect('login', 'index');

    if ( ! $s->isAdmin() ) {
      $s->addflash('You need Admin permissions to delete logs.','error');
      $this->redirect('admin', 'index');
    }

      $DB = new DB();
      $DB->delete('logs', "url LIKE '%?%' AND `title` LIKE '%404%'");
      $DB->close();
      $s->addflash("Removed Log entries for entries containing query strings.",'notice');
      $this->redirect('logs', 'index');
   }


}
