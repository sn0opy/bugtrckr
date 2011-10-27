<?php

/**
 * controller.php
 * 
 * Main controller class
 * 
 * @package controller
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */
namespace misc;

class controller extends \F3instance
{
    /**
     * 
     */
    protected function tpserve()
    {
        $project = new \project\model;
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
        $this->set('pageTitle', 'Error');
        $this->set('SESSION.FAILURE', $msg);
        $this->tpserve();
    }

}
