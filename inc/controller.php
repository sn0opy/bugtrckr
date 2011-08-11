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

        if (!$projects)
        {
//            $this->set('FAILURE', $msg);
            $this->set('template', 'error404.tpl.php');
        }

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
        $this->set('FAILURE', $msg);
        $this->set('template', 'error404.tpl.php');
        $this->tpserve();
    }

}
