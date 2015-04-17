<?php

/**
 * Main controller class
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */
namespace controllers;

class Controller extends \F3instance
{
    /**
     * 
     */
    protected function tpserve()
    {
        $project = new \models\Project();
        $projects = $project->find();
        $this->set('projects', $projects);
        
        if(file_exists('setup.php') || file_exists('install/sqlite.php') || file_exists('install/mysql.php'))
            $this->set('installWarning', true);
        
        echo \Template::serve('main.tpl.php');
    }

    /**
     *
     */
    protected function tpdeny()
    {
        echo \Template::serve('main.tpl.php');
    }

    /**
     *
     */
    protected function tpfail($msg)
    {
        $this->set('pageTitle', $this->get('lng.error'));
        $this->set('SESSION.FAILURE', $msg);
        $this->tpserve();
    }

    /**
     * Reroute to specified URI
     * @param string $uri
     * @param bool $permanent
     */
    function reroute($uri,$permanent=FALSE) {
        if (!$permanent)
            $_SERVER['REQUEST_METHOD']='POST';//dirty hack to force a 303 redirection
        parent::reroute($uri);
    }

}
