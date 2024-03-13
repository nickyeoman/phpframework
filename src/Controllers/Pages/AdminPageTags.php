<?php
namespace Nickyeoman\Framework\Controllers\Pages;

use Nickyeoman\Framework\Classes\BaseController;
USE Nickyeoman\Dbhelper\Dbhelp as DB;
use Nickyeoman\Framework\Attributes\Route;
use Nickyeoman\Framework\Classes\paginationHelper; // TODO: Needs pagination

class AdminPageTags extends BaseController {

  // List pages to edit
  

  public function tagadmin($params = null) {

    $s = $this->session;
		$v = $this->viewClass;
		$r = $this->request;

    if ( ! $s->loggedin() ) {

      $s->addflash("You need to login to edit tags.",'notice');
      redirect('user', 'login');

    } elseif ( !$s->inGroup('admin') ) {

      $s->addflash("You need Admin permissions to edit tags.",'notice');
      redirect('user', 'login');

    }

    $tag = $params[0];
    if ( empty( $tag ) )
      die("no tag supplied, TODO: 404");

    $DB = new DB();      
    $result = $DB->findall('pages', '*', "`tags` LIKE '%$tag%'");
    foreach ($result as $key => $value){
      $value['tags'] = explode(',',$value['tags']);
      $this->data['pages'][$key] = $value;
    }
    $DB->close();

    $v->set('adminbar', true);
    $v->set('pageid', "page-admin");
    $view('@page/admin');
  }

} //end class
