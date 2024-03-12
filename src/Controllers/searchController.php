<?php namespace Nickyeoman\Framework\Controllers;

USE Nickyeoman\Framework\Classes\BaseController;
USE Nickyeoman\Dbhelper\Dbhelp as DB;
USE Nickyeoman\Framework\Attributes\Route;

 class searchController extends BaseController {

  #[Route('/search', methods: ['GET','POST'])]
   function index($params = null) {

    $s = $this->session;
    $v = $this->viewClass;
    $r = $this->request;

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
         p.title as title, p.slug as slug, p.description as description, t.title as tagtitle
      FROM pages as p
      JOIN tag_pages tp ON p.id = tp.pages_id
      JOIN tags t ON t.id = tp.tag_id
      WHERE
        p.draft != 1
        AND (p.keywords LIKE '%$searchRequest%' OR 'p.body' LIKE '$searchRequest' )
        AND t.title LIKE '%blog%'
      GROUP BY p.id
EOSQL;

          $v->data['searchResults'] = $DB->query($sql);
          $v->data['searchTerm'] = $searchRequest;

          if (empty($v->data['searchResults']) )
            $v->data['searchCount'] = 0;
          else
            $v->data['searchCount'] = count($v->data['searchResults']);

     }

     $v->data['menuActive'] = 'search';
     $this->view('@cms/search/search');

   }
 }