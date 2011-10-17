<?php

namespace user;

class view extends \misc\controller
{
    /**
     *	Displays users infopage
     */
    function showUser()
    {
        $name = $this->get('PARAMS.name');

        $user = new \user\model();
        $user->load(array('name = :name', array(':name' => $name)));

        if (!$user->hash)
            return $this->tpfail("User not found");

        $ticket = new \ticket\displayable();
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
