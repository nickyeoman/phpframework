<?php
   class pageController extends Nickyeoman\Framework\BaseController {

     // There is no index page, this will catch
     function override( $params = array() ) {
       $slug = $params[0];
       $this->data['page'] = $this->db->findone('pages', 'slug', $slug);
       bdump($this->data['page']);

       $this->twig('page/page', $this->data);
     }

     // List pages to edit
     function admin() {

       if ( ! $this->session['loggedin'] ) {

         $this->session['notice'] = "You need to login to edit pages.";
         $this->writeSession();
         $this->redirect('user', 'login');

       }

       //Grab pages
       $this->data['pages'] = $this->db->findall('pages','id,title,slug');
       $this->twig('page/admin', $this->data);

     }

     // Edit a page
     function edit($params = null) {

       if ( ! $this->session['loggedin'] ) {

         $this->session['notice'] = "You need to login to edit pages.";
         $this->writeSession();
         $this->redirect('user', 'login');

       }

       if ( $this->post['submitted'] ) {
         $page = new Nickyeoman\helpers\pageHelper($this->post);

         $array = $page->insertArray();

         $id = $this->db->update('pages', $array, 'id' );

         $this->session['notice'] = "Saved Page.";
         $this->writeSession();
         $this->redirect('page', 'admin');

       }

       $pid = $params[0];

       if ( empty($pid) ) {
         //TODO: error, page not given
       }

       $this->data['info'] = $this->db->findone('pages', 'id', $pid);

       $this->twig('page/edit', $this->data);

     }

     public function new(){

       if ( ! $this->session['loggedin'] ) {

         $this->session['notice'] = "You need to login to edit pages.";
         $this->writeSession();
         $this->redirect('user', 'login');

       }

       if ( $this->post['submitted'] ) {
         $page = new Nickyeoman\helpers\pageHelper($this->post);

         $array = $page->insertArray();

         $id = $this->db->create('pages', $array);

         $this->session['notice'] = "Saved Page.";
         $this->writeSession();
         $this->redirect('page', 'admin');

       }

       $this->data['info'] = array(
         'title' => 'New Title',
         'slug' => 'slug',
         'intro' => 'Placeholder Text',
         'body' => 'Placeholder Text'
       );
       $this->data['mode'] = 'new';

       $this->twig('page/edit', $this->data);



     }

   }
