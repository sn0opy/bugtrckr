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
namespace user;

class controller extends \misc\Controller
{

    /**
     *	Adds a new user to the database
     */
    function registerUser($name = false, $password = false, $email = false, $admin = false)
    {
        if (($this->get('POST.name') == "" && $name == "") ||
                ($this->get('POST.email') == "" && $email == ""))
                return $this->tpfail('Please correct your data.');

        $salt = \misc\helper::randStr();

        $user = new \user\model();
        $user->name = $name ? $name : $this->get('POST.name');
        $user->email = $email ? $email : $this->get('POST.email');
        $user->password = $password ? \misc\helper::salting($salt, $password) : \misc\helper::salting($salt, $this->get('POST.password'));
        $user->salt = $salt;
        $user->hash = \misc\helper::getFreeHash('User');
        $user->admin = $admin ? 1 : 0;
        $user->save();
        
        if (!$user->_id)
            return $this->tpfail("Failure while creating User");
        elseif(!$user->_id && $name != false)
            return true;

        if(!$name) {
            $this->set('SESSION.SUCCESS', 'User registred successfully');
            $this->reroute($this->get('BASE') . '/');
        } 

        return true;
    }


    /**
     *	Checks user to log in
     */
    function loginUser()
    {
        $email = $this->get('POST.email');
        
        $user = new \user\model();        
        $user->load(array('email = :email', array(':email' => $email))); // to get a user's salt first
        
        $user->load(array('email = :email AND password = :password',
            array(':email' => $this->get('POST.email'),
                ':password' => \misc\helper::salting($user->salt, $this->get('POST.password')))));

        
        if ($user->dry())
        {
            $this->set('SESSION.FAILURE', 'Password or Email is incorrect');
            $this->reroute($this->get('BASE') . '/user/login');
        }

        // enable user's last used project if he hasn't already chosen one
        if($user->lastProject && !$this->get('SESSION.project'))
            $this->set('SESSION.project', $user->lastProject);

        $this->set('SESSION.user', array('name' => $user->name, 'admin' => $user->admin, 'hash' => $user->hash));
        $this->set('SESSION.SUCCESS', 'Login successful');
        $this->reroute($this->get('BASE') . '/');
    }

    /**
     *	Destroy users session 
     */
    function logoutUser()
    {
        $this->set('SESSION.user', NULL);
        session_destroy();

        $this->set('SESSION.SUCCESS', 'User logged out');
        $this->reroute($this->get('BASE') . '/');
    }
}
