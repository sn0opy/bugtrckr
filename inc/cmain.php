<?php

/**
 * cmain.php
 * 
 * Everything comes together in here
 * 
 * @package Main
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */
class cmain extends Controller
{

    function start()
    {
        $this->set('pageTitle', '{{@lng.home}}');
        $this->set('template', 'home.tpl.php');
        $this->set('onpage', 'start');
        $this->tpserve();
    }
 
    /**
     *
     */
    function selectProject($hash = false, $routeBack = true)
    {
        $url = $this->get('SERVER.HTTP_REFERER');
        $projHash = ($hash) ? $hash : $this->get('REQUEST.project');

        if($projHash == 'new') 
            $this->reroute($this->get('BASE').'/project/add');

        $project = new Project();
        $project->load(array("hash = :hash", array(':hash' =>$projHash)));

        if (!$project->id)
        {
            $this->tpfail("Failure while changing Project");
            return;
        }

        if ($this->get('SESSION.user.id'))
        {
            $user = new User();
            $user->load("id = " . $this->get('SESSION.user.id'));
            $user->lastProject = $project->id;
            $user->save();
        }

        $this->set('SESSION.project', $project->id);
		$this->set('SESSION.projectHash', $project->hash);
        
        if($routeBack)
            $this->reroute($url);
    }

}
