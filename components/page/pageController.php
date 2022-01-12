<?php
   class pageController extends Nickyeoman\Framework\BaseController {

     // There is no index page, this will catch
     function override() {

       $this->data['pages'] = $this->db->findall('pages','id,title,slug');

       $this->twig('page/pages', $this->data);
     }

     // List pages to edit
     function admin() {

       $user = new Nickyeoman\Framework\User();

       if ( ! $user->loggedin() ) {

         $this->session['notice'] = "You need to login.";
         $this->writeSession();
         $this->redirect('user', 'login');

       }

       $this->twig('admin', $this->data);

     }

     // Edit a page
     function edit($params = null) {

       $user = new Nickyeoman\Framework\User();

       if ( ! $user->loggedin() ) {

         $this->session['notice'] = "You need to login.";
         $this->writeSession();
         $this->redirect('user', 'login');

       }

       $pid = $params[0];

       if ( empty($pid) ) {
         //TODO: error, page not given
       }

       $this->data['info'] = $this->db->findone('pages', 'id', $pid);

       $this->twig('page/edit', $this->data);

     }

   }
