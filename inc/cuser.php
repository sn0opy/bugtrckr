<?php

/**
 * cuser.php
 * 
 * User controller
 * 
 * @package user
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */
class cuser extends Controller
{
      /**
     *
     */
    function showUser()
    {
        $name = $this->get('PARAMS.name');

        $user = new User();
        $user->load(array('name = :name', array(':name' => $name)));

        if (!$user->id)
        {
            $this->tpfail("User not found");
            return;
        }

        $ticket = new DisplayableTicket();
        $tickets = $ticket->find('owner = ' . $user->id);

        $this->set('user', $user);
        $this->set('tickets', $tickets);
        $this->set('template', 'user.tpl.php');
        $this->set('pageTitle', '{{@lng.user}} › ' . $name);
        $this->tpserve();
    }
    
       /**
     *
     */
    function showUserRegister()
    {
        $this->set('template', 'userRegister.tpl.php');
        $this->set('pageTitle', '{{@lng.user}} › {{@lng.registration}}');
        $this->tpserve();
    }

    /**
     *
     */
    function registerUser()
    {
        $salt = helper::randStr();

        $user = new user();
        $user->name = $this->get('POST.name');
        $user->email = $this->get('POST.email');
        $user->password = helper::salting($salt, $this->get('POST.password'));
        $user->salt = $salt;
        $user->hash = helper::getFreeHash('User');
        $user->admin = 0;
        $user->save();

        if (!$user->_id)
        {
            $this->tpfail("Failure while saving User");
            return;
        }

        $this->set('SESSION.SUCCESS', 'User registred successfully');
        $this->reroute($this->get('BASE') . '/');
    }

    /**
     *
     */
    function showUserLogin()
    {
        $this->set('template', 'userLogin.tpl.php');
        $this->set('pageTitle', '{{@lng.user}} › {@lng.login}');
        $this->tpserve();
    }

    /**
     *
     */
    function loginUser()
    {
        $user = new User();
        $user->load(array('email = :email', array(':email' => $this->get('POST.email'))));
        $user->load(array('email = :email AND password = :password',
            array(':email' => $this->get('POST.email'),
                ':password' => helper::salting($user->salt, $this->get('POST.password')))));

        if ($user->dry())
        {
            $this->set('FAILURE', 'Login failed.');
            $this->reroute($this->get('BASE') . '/user/login');
        }

        $this->set('SESSION.user', array('name' => $user->name, 'id' => $user->id, 'admin' => $user->admin, 'hash' => $user->hash));
        $this->set('SESSION.SUCCESS', 'Login successful');
        $this->reroute($this->get('BASE') . '/');
    }

    /**
     *
     */
    function logoutUser()
    {
        $this->set('SESSION.userName', NULL);
        $this->set('SESSION.userPassword', NULL);
        $this->set('SESSION.userHash', NULL);
        $this->set('SESSION.userId', NULL);
        session_destroy();

        $this->set('SESSION.SUCCESS', 'User logged out');
        $this->reroute($this->get('BASE') . '/');
    }
}