<?php

namespace Nickyeoman\Framework\Controllers;

use Nickyeoman\Framework\Classes\BaseController;
use \Nickyeoman\Dbhelper\Dbhelp as DB;
use Nickyeoman\Framework\Classes\paginationHelper;
use Nickyeoman\Framework\Attributes\Route;

class Blog extends BaseController {

  private $DB;

  public function __construct($session, $viewClass, $dbConnection, $otherDependency) {
      parent::__construct($session, $viewClass, $dbConnection, $otherDependency);
      $this->DB = new DB();
  }

  #[Route('/blog/{pageid}')]
  public function blog($pageid = null) {
    $this->processPage($pageid, "t.title LIKE '%blog%' AND p.draft != 1");
  }

  #[Route('/blog/tag/{tagname}/{pageid}')]
  public function tag($tagname = '', $pageid = 1) {
      $this->processPage($pageid, "t.title LIKE '%$tagname%' AND p.draft != 1");
  }

  private function processPage($pageid, $whereCondition) {
    // TODO: it displays the last page even when you do more than the last page number
      $current_page = isset($pageid) && is_numeric($pageid) && $pageid >= 1 ? $pageid : 1;

      $limit = '9';

      $pagination = $this->getPagination($whereCondition, $current_page, $limit);
      $result = $this->executeQuery($whereCondition, $pagination->sql_limit);

      $this->viewClass->data['pages'] = !empty($result) ? $result : [];
      $this->viewClass->data['menuActive'] = 'blog';
      $this->viewClass->data['pagination'] = $pagination->data;
      $this->viewClass->data['pageid'] = 'blog';

      $this->view('@cms/blog');
      $this->session->writeSession();
  }

  private function getPagination($whereCondition, $current_page, $limit) {
      $sql = "SELECT COUNT(*) as i FROM pages p JOIN tag_pages tp ON p.id = tp.pages_id JOIN tags t ON t.id = tp.tag_id WHERE $whereCondition";
      $result = $this->DB->query($sql);
      $num_articles = $result[0]['i'];
      return new paginationHelper($limit, $current_page, $num_articles);
  }

  private function executeQuery($whereCondition, $limit) {
      $sql = "SELECT p.*, GROUP_CONCAT(t.title SEPARATOR ', ') as 'tags' FROM pages p JOIN tag_pages tp ON p.id = tp.pages_id JOIN tags t ON t.id = tp.tag_id WHERE $whereCondition GROUP BY p.id LIMIT $limit";
      $result = $this->DB->query($sql);
      foreach ($result as $key => $value) {
          $value['tags'] = explode(',', $value['tags']);
          $result[$key] = $value;
      }
      return $result;
  }
}
