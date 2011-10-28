<?php

/**
 * user\controller.php
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
                return $this->tpfail($this->get('lng.noCorretdata'));

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
            return $this->tpfail($this->get('lng.createUserFail'));
        elseif(!$user->_id && $name != false)
            return true;

        if(!$name) {
            $this->set('SESSION.SUCCESS', $this->get('lng.userRegSuccessfull'));
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
            return $this->tpfail($this->get('lng.pwMailWrong'));


        // enable user's last used project if he hasn't already chosen one
        if($user->lastProject && !$this->get('SESSION.project'))
            $this->set('SESSION.project', $user->lastProject);

        $this->set('SESSION.user', array('name' => $user->name, 'admin' => $user->admin, 'hash' => $user->hash));
        $this->set('SESSION.SUCCESS', $this->get('lng.loginSuccess'));
        $this->reroute('/');
    }

    /**
     *	Destroy users session 
     */
    function logoutUser()
    {
        $this->set('SESSION.user', NULL);
        session_destroy();

        $this->set('SESSION.SUCCESS', $this->get('lng.logoutSuccess'));
        $this->reroute('/');
    }
}
