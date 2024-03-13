<?php
namespace Nickyeoman\Framework\Controllers\Pages;

use Nickyeoman\Framework\Classes\BaseController;
USE Nickyeoman\Dbhelper\Dbhelp as DB;
use Nickyeoman\Framework\Attributes\Route;

class AdminPageComments extends BaseController {

  #[Route('/admin/comments')]
  public function admincomment($params = null) {

    $s = $this->session;
		$v = $this->viewClass;
		$r = $this->request;
    $DB = new DB();

    if ( ! $s->loggedin('You need to login to admin comments.') )
      redirect('/admin');

    if ( !$s->inGroup('admin', 'You need admin permissions;') )
      redirect('/admin');

    //Grab pages
    $SQL = <<<EOSQL
SELECT
  c.id, c.body, u.username, c.date
FROM
  `comments` as c
LEFT JOIN
  `users` as u
ON
  c.userid = u.id
EOSQL;

    $v->data['comments'] = $DB->query($SQL);

    $v->data['pageid'] = "comments-admin";
    $v->set('adminbar', true);
    $this->view('@cms/pages/adminComments');
  }

  #[Route('/admin/comments/delete/{comid}')]
  public function deleteComment($comid) {

    $s = $this->session;
    $DB = new DB();

    if ( ! $s->loggedin('You need to login to admin comments.') )
      redirect('/admin');

    if ( !$s->inGroup('admin', 'You need admin permissions;') )
      redirect('/admin');

    // Remove a comment
    
    if ( !empty($comid) ) {
      $comid = (int)$comid;
      if ( $comid >= 0 && is_int($comid) ) {
        $DB->delete('comments', "`id` = $comid");
        $s->addFlash("removed Comment $comid", 'notice');
        $s->writeSession();
      }
    }
    redirect('/admin/comments');
  }

} //end class
