<?php
   class contactController extends Nickyeoman\Framework\BaseController {

     public function index() {

       $this->data['menuActive'] = 'contact';
       $this->data['showform'] = true;
       bdump($this->post, 'Post Data From Framework');

       if ( $this->post['submitted'] ) {

         //load helper
         $ch = new Nickyeoman\helpers\contactHelper($this->post);
         if ( $ch->checkerrors() ) {

           foreach ( $ch->error as $k => $v)
             $numerr = $this->adderror($v, $k );

         } else {

           // Save to Database
           $insertData = array(
             'email'      => $this->post['email'],
             'message'    => $this->post['message']
           );

          // Insert/create database
          $id = $this->db->create("contactForm", $insertData );

          // Error check the database
          if ( empty( $id ) )
            $this->adderror("There was a problem sending the message.", 'db' );
          else
            $this->data['showform'] = false;

          // Send am email
          $this->sendEmail(
            'nick.yeoman@gmail.com'
            ,'New Message At NickYeoman.com'
            ,"Here is your message: " . $this->post['message']
          );

         } // end if errors

       } //end if submitted

       $this->twig('contact', $this->data);
       $this->session->writeSession();

     }

     public function admin() {

       if ( ! $this->session->loggedin() ) {

         $this->session->addflash("You need to login to edit messages.",'error');
         $this->writeSession();
         $this->redirect('user', 'login');

       } elseif ( !$this->session->inGroup('admin') ) {

         $this->session->addflash('You need Admin permissions to edit pages.','error');
         $this->writeSession();
         $this->redirect('user', 'login');

       }
       //Grab pages
         $result = $this->db->findall('contactForm', 'id,email,message,created,unread');

         $this->data['contactMessages'] = array();
         foreach ($result as $key => $value){
           $this->data['contactMessages'][$key] = $value;
         }

         $this->data['pageid'] = "contact-admin";
       $this->twig('admin', $this->data);
     }

     public function view($parms = null) {

       if ( ! $this->session->loggedin() ) {

         $this->session->addflash("You need to login to edit messages.",'error');
         $this->session->writeSession();
         $this->redirect('user', 'login');

       } elseif ( !$this->session->inGroup('admin') ) {

         $this->session->addflash("You need to login to edit messages.",'error');
         $this->writeSession();
         $this->redirect('user', 'login');

       }

       $pid = $parms[0];

       //Grab pages
       $this->data['msg'] = $this->db->findone('contactForm', 'id', $pid);


         $this->data['pageid'] = "contact-admin-edit";
       $this->twig('view', $this->data);
     }
   }
