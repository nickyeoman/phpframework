<?php
   class contactController extends Nickyeoman\Framework\BaseController {

     function index() {

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
       $this->writeSession();

     }

     function admin() {
       $this->twig('admin', $this->data);
     }
   }
