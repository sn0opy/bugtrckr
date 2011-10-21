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

namespace misc;

class main extends \misc\controller
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

        $project = new \project\model();
        $project->load(array("hash = :hash", array(':hash' =>$projHash)));

        if (!$project->hash)
        {
            $this->tpfail("Failure while changing Project");
            return;
        }

        if ($this->get('SESSION.user.hash'))
        {
            $user = new \user\model();
            $user->load(array('hash = :hash', array(':hash' => $this->get('SESSION.user.hash'))));
			
            if (!$user->dry())
            {
                $user->lastProject = $project->hash;
            	$user->save();
            }
        }

        $this->set('SESSION.project', $project->hash);
        $this->set('SESSION.projectHash', $project->hash);

        if($routeBack)
            $this->reroute($url);
    }

}
