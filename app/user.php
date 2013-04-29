<?php

/**
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright 2013 Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

class User extends Controller {

	/**
	 * 
	 * @param type $f3
	 * @param type $params
	 * @param type $name
	 * @param type $password
	 * @param type $email
	 * @param type $admin
	 * @return boolean
	 */
    function registerUser($f3 = false, $params = false, $name = false, $password = false, $email = false, $admin = false) {
		if(!$f3)
			$f3 = Base::instance();
		
        if (($f3->get('POST.name') == "" && $name == "") || ($f3->get('POST.email') == "" && $email == ""))
			return $this->tpfail($f3->get('lng.noCorretdata'));

        $salt = helper::randStr();

        $user = new DB\SQL\Mapper($this->db, 'User');
        $user->name = $name ? $name : $f3->get('POST.name');
        $user->email = $email ? $email : $f3->get('POST.email');
        $user->password = $password ? helper::salting($salt, $password) : helper::salting($salt, $f3->get('POST.password'));
        $user->salt = $salt;
        $user->hash = helper::getFreeHash('User');
        $user->admin = $admin ? 1 : 0;
        $user->save();

        if(!$name) {
            $f3->set('SESSION.SUCCESS', $f3->get('lng.userRegSuccessfull'));
            $f3->reroute('/');
        } 

        return true;
    }


	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
    function loginUser($f3) {
        $email = $f3->get('POST.email');
        
        $user = new DB\SQL\Mapper($this->db, 'User');
        $user->load(array('email = :email', array(':email' => $email)));

		// TODO: use password_* function
        $user->load(array('email = :email AND password = :password',
            array(':email' => $f3->get('POST.email'),
                ':password' => helper::salting($user->salt, $f3->get('POST.password')))));

        if($user->dry())
            return $this->tpfail($f3->get('lng.pwMailWrong'));


        // enable user's last used project if he hasn't already chosen one
        if($user->lastProject && !$f3->get('SESSION.project'))
            $f3->set('SESSION.project', $user->lastProject);

        $f3->set('SESSION.user', array('name' => $user->name, 'admin' => $user->admin, 'hash' => $user->hash));
        $f3->set('SESSION.SUCCESS', $f3->get('lng.loginSuccess'));
        $f3->reroute('/');
    }
	

	/**
	 * 
	 * @param type $f3
	 */
    function logoutUser($f3) {
        $f3->set('SESSION.user', NULL);
        $f3->clear('SESSION');

        $f3->set('SESSION.SUCCESS', $f3->get('lng.logoutSuccess'));
        $f3->reroute('/');
    }
	
	
	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
    function showUser($f3) {
        $name = $f3->get('PARAMS.name');

        $user = new DB\SQL\Mapper($this->db, 'User');
        $user->load(array('name = :name', array(':name' => $name)));

        if (!$user->hash)
            return $this->tpfail($f3->get('lng.userNotFound'));

        $ticket = new DB\SQL\Mapper($this->db, 'displayableticket');
        $tickets = $ticket->find(array('owner = :owner', array(':owner' => $user->hash)));

        $f3->set('user', $user);
        $f3->set('tickets', $tickets);
        $f3->set('template', 'user.tpl.php');
        $f3->set('pageTitle', $f3->get('lng.user') . ' ' . $name);
        $f3->set('onpage', 'user');
    }
	
    
	/**
	 * 
	 * @param type $f3
	 */
    function showUserRegister($f3) {
        $f3->set('template', 'userRegister.tpl.php');
		$f3->set('pageTitle', $f3->get('lng.user') . ' › ' . $f3->get('lng.registration'));
        $f3->set('onpage', 'registration');
    }
	
    
	/**
	 * 
	 * @param type $f3
	 */
    function showUserLogin($f3) {
        $f3->set('template', 'userLogin.tpl.php');
        $f3->set('pageTitle', $f3->get('lng.user') . ' › ' . $f3->get('lng.login'));
        $f3->set('onpage', 'login');
    }
}
