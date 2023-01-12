<?php
namespace Nickyeoman\Framework\Components\search;

 class searchController extends \Nickyeoman\Framework\BaseController {

   function index($params = null) {

     $searchRequest = null;
     $this->data['searchCount'] = null;
     $this->data['searchTerm'] = null;

     if ( !empty($params[0]) )
       $searchRequest = $params[0];

     if ( !empty($this->post['search']) )
       $searchRequest = $this->post['search'];

     if ( !empty($searchRequest) ) {
       //clean search request
       $searchRequest = trim(strip_tags($searchRequest));
       $searchRequest = mysqli_real_escape_string($this->db->con, $searchRequest);

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
          $results = $this->db->query($sql);
          $this->data['searchResults'] = $results;
          $this->data['searchTerm'] = $searchRequest;

          if (empty($this->data['searchResults']) )
            $this->data['searchCount'] = 0;
          else
            $this->data['searchCount'] = count($this->data['searchResults']);

     }

     $this->data['menuActive'] = 'search';
     $this->twig('search', $this->data, 'search');

   }
 }
