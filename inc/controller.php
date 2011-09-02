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
class Controller extends F3instance
{

    /**
     * 
     */
    protected function tpserve()
    {
        $project = new Project();
        $projects = $project->find();
        $this->set('projects', $projects);
        echo Template::serve('main.tpl.php');
    }

    /**
     *
     */
    protected function tpdeny()
    {
        echo Template::serve('main.tpl.php');
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
