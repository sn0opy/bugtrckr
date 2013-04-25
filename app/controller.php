<?php

/**
 * Main controller class
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2013, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */

class Controller {
	/**
	 *
	 * @var type 
	 */
	protected $db;
	
	/**
	 * 
	 */
	public function __construct() {
		$f3 = Base::instance();
		
		$this->db = new DB\SQL('mysql:host=localhost;dbname=' . $f3->get('DB_DBNAME'), $f3->get('DB_USER'), $f3->get('DB_PASSWORD'));
	}
	
	/**
	 * 
	 */
    public function afterRoute() {
		$f3 = Base::instance();
		
        $project = new \DB\SQL\Mapper($this->db, 'project');
        $projects = $project->find();
        $f3->set('projects', $projects);
        
        if(file_exists('setup.php') || file_exists('install/sqlite.php') || file_exists('install/mysql.php'))
			$f3->set('installWarning', true);
        
        echo Template::instance()->render('main.tpl.php');
    }

    /**
     *
     */
    protected function tpdeny() {
        echo Template::instance()->render('main.tpl.php');
    }

    /**
     *
     */
    protected function tpfail($msg) {
        $this->set('pageTitle', $this->get('lng.error'));
        $this->set('SESSION.FAILURE', $msg);
        $this->tpserve();
    }

}
