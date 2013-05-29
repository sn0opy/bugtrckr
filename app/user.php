<?php

/**
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright 2013 Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

define('BCRYPT_COUNT', 14);

class User extends Controller
{

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
  function registerUser($base = false, $params = false, $name = false, $password = false, $email = false, $admin = false)
  {
    $f3 = Base::instance();		

    $f3->get("log")->write("Calling /user/new");
    $f3->get("log")->write("POST: " . print_r($f3->get("POST"), true));
		
    if(($f3->get('POST.name') == "" && $name == "") || ($f3->get('POST.email') == "" && $email == ""))
    	return $this->tpfail('Nope', "name = $name" . $f3->get('POST.name') . ", email = $email" . $f3->get("POST.email"));

    $salt = helper::randStr(22);

    $user = new DB\SQL\Mapper($this->db, 'User');
    $user->name = $name ? $name : $f3->get('POST.name');
    $user->email = $email ? $email : $f3->get('POST.email');
    $user->password = $password ? Bcrypt::instance()->hash($password, $salt, BCRYPT_COUNT) : Bcrypt::instance()->hash($f3->get('POST.password'), $salt, BCRYPT_COUNT);
    $user->salt = $salt;
    $user->hash = helper::getFreeHash('User');
    $user->admin = $admin ? 1 : 0;
    $user->save();
 
    if(!$name) {
      $f3->set('SESSION.SUCCESS', 'Registration successfull');
      $f3->reroute('/');
    } 

    return true;
  }

	/**
	 * 
	 * @param type $f3
	 * @return type
	 */
  function loginUser($f3)
  {
    $f3->get("log")->write("Calling /user/login");
    $f3->get("log")->write("POST: not posted because of clear password.");

    $email = $f3->get('POST.email');

    $user = new DB\SQL\Mapper($this->db, 'User');
    $user->load(array('email = :email', array(':email' => $email)));

    if ($user->dry())
			return $this->tpfail($f3->get('lng.pwMailWrong'), "User with email = '" . $f3->get('POST.email') . "' not found");

		$salt = $user->salt;

		if(!Bcrypt::instance()->verify($f3->get('POST.password'), $user->password))
			return $this->tpfail($f3->get('lng.pwMailWrong'), "Password wrong");

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
  function logoutUser($f3)
  {
    $f3->get("log")->write("Calling /user/logout");

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
  function showUser($f3)
  {
    $f3->get("log")->write("Calling /user/@name with @name = " . $f3->get("PARAMS.name"));

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
  function showUserRegister($f3)
  {
    $f3->get("log")->write("Calling /user/new");

    $f3->set('template', 'userRegister.tpl.php');
		$f3->set('pageTitle', $f3->get('lng.user') . ' › ' . $f3->get('lng.registration'));
    $f3->set('onpage', 'registration');
  }
    
	/**
	 * 
	 * @param type $f3
	 */
  function showUserLogin($f3)
  {
    $f3->get("log")->write("Calling /user/login");

    $f3->set('template', 'userLogin.tpl.php');
    $f3->set('pageTitle', $f3->get('lng.user') . ' › ' . $f3->get('lng.login'));
    $f3->set('onpage', 'login');
  }
}
