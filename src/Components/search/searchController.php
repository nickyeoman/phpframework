<?php namespace Nickyeoman\Framework\Components\search;

USE Nickyeoman\Dbhelper\Dbhelp as DB;
USE Nickyeoman\Framework\ViewData;
USE Nickyeoman\Framework\RequestManager;
USE Nickyeoman\Framework\SessionManager;

 class searchController extends \Nickyeoman\Framework\BaseController {

   function index($params = null) {

    $s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);

     $searchRequest = null;
     $v->data['searchCount'] = null;
     $v->data['searchTerm'] = null;

     if ( !empty($params[0]) )
       $searchRequest = $params[0];

     if ( !empty($r->get('search') ) )
       $searchRequest = $r->get('search');

     if ( !empty($searchRequest) ) {
       //clean search request
       $searchRequest = trim(strip_tags($searchRequest));
       $DB = new DB();
       $searchRequest = mysqli_real_escape_string($DB->con, $searchRequest);

       // do the search
       $sql = <<<EOSQL
       SELECT
         title, slug, description
      FROM `pages`
      WHERE
        `draft` != 1
        AND (`keywords` LIKE '%$searchRequest%' OR 'body' LIKE '$searchRequest' )
        AND `tags` LIKE '%blog%' ;
EOSQL;
          $results = $DB->query($sql);
          while( $fetched = $results->fetch_array(MYSQLI_ASSOC) ) {
            $rows[] = $fetched;
          }
          
          $v->data['searchResults'] = $rows;
          $v->data['searchTerm'] = $searchRequest;

          if (empty($v->data['searchResults']) )
            $v->data['searchCount'] = 0;
          else
            $v->data['searchCount'] = count($v->data['searchResults']);

     }

     $v->data['menuActive'] = 'search';
     $this->twig('search', $v->data, 'search');

   }
 }