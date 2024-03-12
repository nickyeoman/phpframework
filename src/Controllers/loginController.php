<?php
namespace Nickyeoman\Framework\Controllers;

use Nickyeoman\Framework\Classes\BaseController;
use Nickyeoman\Dbhelper\Dbhelp as DB;
use Nickyeoman\Framework\Attributes\Route;
USE Nickyeoman\Validation\Validate;

class LoginController extends BaseController {

    #[Route('/login', methods: ['GET'])]
    public function index() {
        
        // Your existing code goes here
        $s = $this->session;
        $v = $this->viewClass;

        // If the user is logged in we don't need to proceed
        if ($s->loggedin()) {
            redirect('/admin'); // TODO: change this for non admin users
        }

        if ( !empty($s->data['post']['login'])) {
            $v->data['login'] = $s->data['post']['login'];
        }

        $s->clearPost();
        $this->view('@cms/login/login');
        $s->writeSession();
    }

    #[Route('/login', methods: ['POST'])]
    public function loginAttempt() {

        $s = $this->session;
        $v = $this->viewClass;
        $r = $this->request; // Assuming you also need RequestManager
		$validate = new Validate;


        // Check if form Submitted
        if ($r->submitted) {

            $dirty = false;

            // Check Username or email is not null
            if (!$validate->minLength($r->get('login'), 4)) {
                $s->addFlash("Login too short",'username_length');
                $dirty = true;
            }

            // Check password is not null
            if (!$validate->minLength($r->get('password'), 8)) {
                $s->addFlash("Password too short",'pass_length');
                $dirty = true;
            }

            // query db for user
            if (!$dirty) {
                $DB = new DB();

                // Email or username?
                if ($validate->isEmail($r->get('login'))) {
                    $userdb = $DB->findone('users', 'email', $r->get('login'));
                } else {
                    $userdb = $DB->findone('users', 'username', $r->get('login'));
                }
            }

            // Check if results are empty
            if (empty($userdb)) {
                $dirty = true;
                $s->addFlash("User Information is not correct.", 'login_error');
            } else {
                if (!password_verify($r->get('password'), $userdb['password'])) {
                    $dirty = true;
                    $v->addFlash("Incorrect Password.", 'password_error');
                }
            }

            // Login
            if (!$dirty) {
                $userSession = [];
                foreach (['id', 'username', 'email'] as $value) {
                    $userSession[$value] = $userdb[$value];
                }

                // groups
                $results = $DB->findall('userGroups', 'groupName', 'user_id = ' . $userdb['id']);
                $DB->close();
                $ugroups = [];
                foreach ($results as $v) {
                    array_push($ugroups, $v['groupName']);
                }

                $s->setUserGroups($ugroups);
                $s->authorize($userSession);
                $s->writeSession();

                redirect('/admin');
            }
        } // end if submitted

        $s->addPost($r->post);
        $s->writeSession();
        redirect('/login');
    }

    #[Route('/logout', methods: ['GET','POST'])]
    public function logout(){

		$s = $this->session;
		$s->destroySession();
		$s->addflash("You have been logged out.",'notice');
		redirect('/'); // TODO: this should be user definable

	}
}
