<?php

/**
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright 2013 Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

class Setup {
    /**
     * 
     * @param type $f3
     */
    public function start() {
	$f3 = Base::instance();
        
	$f3->set('reqs', $this->_doChecks());        
        $this->tpserve();
    }    
    
    /**
     * 
     * @return type
     */
    public function install() {
	$f3 = Base::instance();

	foreach($f3->get('POST') as $elem => $val) {
	    $f3->set('SESSION.'.$elem, $val);
	}
	
	if($f3->get('username') && $f3->get('email') &&
	    $f3->get('password') && $f3->get('passwordrepeat')) {
	    if($f3->get('password') == $f3->get('passwordrepeat')) {
		
	    } else {
		
	    }
	} else {
	    
	}
    }           
    

    /**
     * 
     * @param type $f3
     * @return boolean
     */
    private static function _doChecks() {
	$f3 = Base::instance();	
	$error = false;
	$check = null;

	$check['sqlite'] = !extension_loaded('pdo_sqlite') ? false : true;
	$check['mysql'] = !extension_loaded('pdo_mysql') ? false: true;

	if(!$check['sqlite'] && !$check['mysql'])
		$error = true;

	if(is_writeable('../app/')) {
		$check['config1'] = true;
	} else {
		$check['config1'] = false;
		$error = true;
	}

	if(!file_exists('../app/config.ini.php')) {
		$check['config2'] = true;
	} else {
		$error = true;
		$check['config2'] = false;
	}

        return array('error' => $error, 'checks' => $check);
    }
	
	
    /**
     * 
     */
    private static function tpserve() {
        echo Template::instance()->render('setup.tpl.php');
    }
}
