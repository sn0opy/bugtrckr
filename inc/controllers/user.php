<?php

/**
 * User controller
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */
namespace controllers;

class User extends \controllers\Controller
{

    /**
     *	Adds a new user to the database
     */
    function registerUser($name = false, $password = false, $email = false, $admin = false)
    {
        if (($this->get('POST.name') == "" && $name == "") || ($this->get('POST.email') == "" && $email == ""))
			return $this->tpfail($this->get('lng.noCorretdata'));

        $salt = \misc\helper::randStr();

        $user = new \models\User();
        $user->name = $name ? $name : $this->get('POST.name');
        $user->email = $email ? $email : $this->get('POST.email');
        $user->password = $password ? \misc\helper::salting($salt, $password) : \misc\helper::salting($salt, $this->get('POST.password'));
        $user->salt = $salt;
        $user->hash = \misc\helper::getFreeHash('User');
        $user->admin = $admin ? 1 : 0;
        $user->save();

        if(!$name) {
            $this->set('SESSION.SUCCESS', $this->get('lng.userRegSuccessfull'));
            $this->reroute('/');
        } 

        return true;
    }


    /**
     *	Checks user to log in
     */
    function loginUser()
    {
        $email = $this->get('POST.email');
        
        $user = new \models\User();        
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
        $this->clear('SESSION');

        $this->set('SESSION.SUCCESS', $this->get('lng.logoutSuccess'));
        $this->reroute('/');
    }
	
    /**
     *	Displays users infopage
     */
    function showUser()
    {
        $name = $this->get('PARAMS.name');

        $user = new \models\User();
        $user->load(array('name = :name', array(':name' => $name)));

        if (!$user->hash)
            return $this->tpfail($this->get('lng.userNotFound'));

        $ticket = new \models\Displayableticket();
        $tickets = $ticket->find(array('owner = :owner', array(':owner' => $user->hash)));

        $this->set('user', $user);
        $this->set('tickets', $tickets);
        $this->set('template', 'user.tpl.php');
        $this->set('pageTitle', '{{@lng.user}} › ' . $name);
        $this->set('onpage', 'user');
        $this->tpserve();
    }
    
    /**
     *	Displays a form for registration
     */
    function showUserRegister()
    {
        $this->set('template', 'userRegister.tpl.php');
        $this->set('pageTitle', '{{@lng.user}} › {{@lng.registration}}');
        $this->set('onpage', 'registration');
        $this->tpserve();
    }
    
    /**
     *	Show loginform
     */
    function showUserLogin()
    {
        $this->set('template', 'userLogin.tpl.php');
        $this->set('pageTitle', '{{@lng.user}} › {{@lng.login}}');
        $this->set('onpage', 'login');
        $this->tpserve();
    }
}
