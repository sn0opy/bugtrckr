<?php

/**
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright 2013 Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
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
		
		$app->config('../app/sql.ini');

		$app->set('DB', new DB\SQL('mysql:host=localhost;dbname=' . 
					$app->get('DB_DBNAME'), 
					$app->get('DB_USER'), 
					$app->get('DB_PASSWORD'))
				);

		$this->db = $f3->get('DB');

		$app->set('getPermission', function($permission) {
			return Helper::getPermission($permission);
		});

		// TODO: just a workaround, remove this asap
		$f3->set('onpage', '');			

	}
	
	/**
	 * 
	 */
    public function afterRoute() {
		$f3 = Base::instance();
		
        $project = new \DB\SQL\Mapper($this->db, 'Project');
        $projects = $project->find();
        $f3->set('projects', $projects);
        
        if(file_exists('setup.php') || file_exists('install/sqlite.php') || file_exists('install/mysql.php'))
			$f3->set('installWarning', true);
		       		
        echo Template::instance()->render('main.tpl.php');
		
		$app->clear('SESSION.SUCCESS');
		$app->clear('SESSION.FAILURE');
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
		$f3 = Base::instance();
		
		$f3->set('template', 'error.tpl.php');
        $f3->set('pageTitle', $f3->get('lng.error'));
        $f3->set('SESSION.FAILURE', $msg);
    }

}
