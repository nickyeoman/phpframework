<?php
namespace Nickyeoman\Framework\Controllers;

use Nickyeoman\Framework\Classes\BaseController;
USE \Nickyeoman\Dbhelper\Dbhelp as DB;
use Nickyeoman\Framework\Attributes\Route;

class sitemapController extends BaseController {

  public function index() {

    echo 'HTML sitemap goes here (TODO) <a href="/sitemap.xml">sitemap.xml</a>';

  }
  
  #[Route('/sitemap.xml')]
  public function sitemap() {

    // xml for the sitemap
    // https://www.sitemaps.org/protocol.html
    header('Content-Type: text/xml');

    //Get the pages (priority of zero will remove it from the sitemap, drafts are not shown)
    $DB = new DB();
    $thepages = $DB->findall('pages', 'draft, slug, updated, changefreq, priority, path', 'priority > 0 AND draft < 1', 'updated');

    // Start the view
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    // Loop through the pages
    if ( ! empty($thepages) ) {
      foreach( $thepages as $v) {

        $date = new \DateTime($v['updated']);
        $date = $date->format('Y-m-d');

        echo "<url>";

        if ( empty($v['path']) )
          echo "<loc>" . $_ENV['BASEURL'] . '/page/' . $v['slug'] . "</loc>";
        else
          echo "<loc>" . $_ENV['BASEURL'] . $v['path'] . "</loc>";

        echo "<lastmod>" . $date . "</lastmod>";
        echo "<changefreq>" . $v['changefreq'] . "</changefreq>";
        echo "<priority>" . $v['priority'] . "</priority>";
        echo "</url>";

      }
    } else {
      echo "no pages added yet";
    }

    echo "</urlset>";

    // TODO: add caching
  }

}
//end class
