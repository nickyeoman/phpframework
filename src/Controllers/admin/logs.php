<?php
namespace Nickyeoman\Framework\Controllers\admin;

USE Nickyeoman\Framework\Attributes\Route;
use Nickyeoman\Framework\Classes\BaseController;
USE \Nickyeoman\Dbhelper\Dbhelp as DB;

//TODO: the syntax on this page is a mess
//TODO: need pagination
class logs extends BaseController {

  #[Route('/admin/logs')]
  public function index() {

    $s = $this->session;
    
    // redirects
    if ( ! $s->loggedin('You need to login to view logs.') )
   		redirect('/login');

    if ( ! $s->isAdmin() ) {
      $s->addFlash('You need Admin permissions to edit pages.','error');
      redirect('/admin');
    }

    // view data
    $v = $this->viewClass;
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
    $this->view('@cms/admin/logs');

  }

   #[Route('/admin/logs/delete/{logid}')]
   public function delete($logid){

    $s = $this->session;
    
    // redirects
    if ( ! $s->loggedin('You need to login to delete logs.') )
   		redirect('/login');

    if ( ! $s->isAdmin() ) {
      $s->addFlash('You need Admin permissions to delete logs.','error');
      redirect('/admin');
    }

    $DB = new DB();
    $deleteid = mysqli_real_escape_string($DB->con, $logid);
    $DB->delete('logs', "id = $deleteid");
    $DB->close();
    $s->addFlash("Removed Log entry $deleteid.",'notice');
    $s->writeSession();
    redirect('/admin/logs');

   }
   
   // TODO: slug system isn't good
   public function deletegroup($parms){

    $s = $this->session;

      // redirects
      if ( ! $s->loggedin('You need to login to delete logs.') )
        redirect('/login');

      if ( ! $s->isAdmin() ) {
        $s->addFlash('You need Admin permissions to delete logs.','error');
        $this->redirect('admin', 'index');
      }

      $deleteid = implode('/', $parms);
      $DB = new DB();
      $deleteid = mysqli_real_escape_string($DB->con, $deleteid);
      $DB->delete('logs', "`url` = '$deleteid'");
      $DB->close();
      $s->addFlash("Removed Log entries for $deleteid.",'notice');
      redirect('/logs');
   }

   public function deleteall(){

    $s = $this->session;

      // redirects
      if ( ! $s->loggedin('You need to login to delete logs.') )
        redirect('/login');

      if ( ! $s->isAdmin() ) {
        $s->addFlash('You need Admin permissions to delete logs.','error');
        redirect('/admin');
      }

      $DB = new DB();
      $DB->delete('logs', "`id` > 1 AND `title` LIKE '%404%'");
      $DB->close();
      $s->addFlash("Removed All 404 Log entries.",'notice');
      redirect('/logs');
   }

   #[Route('/admin/logs/deletequerystrings')]
   public function deletequerystrings(){

      $s = $this->session;

      // redirects
      if ( ! $s->loggedin('You need to login to delete logs.') )
        redirect('/login');

      if ( ! $s->isAdmin() ) {
        $s->addFlash('You need Admin permissions to delete logs.','error');
        redirect('/admin');
      }

      $DB = new DB();
      $DB->delete('logs', "url LIKE '%?%' AND `title` LIKE '%404%'");
      $DB->close();
      $s->addFlash("Removed Log entries for entries containing query strings.",'notice');
      redirect('/admin/logs');
   }


}
