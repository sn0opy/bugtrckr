<?php

/**
 * Everything comes together in here
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2013, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */

class Main extends Controller {
	/**
	 * 
	 */
    function start($f3) {
        $f3->set('pageTitle', '{{@lng.home}}');
        $f3->set('template', 'home.tpl.php');
        $f3->set('onpage', 'start');
    }
 
    /**
     *
     */
    function selectProject($hash = false, $routeBack = true)
    {
        $url = $this->get('SERVER.HTTP_REFERER');
        $projHash = ($hash) ? $hash : $this->get('PARAMS.project');
		
        $project = new \models\Project();
        $project->load(array("hash = :hash", array(':hash' => $projHash)));

        if($project->dry()) {
            $this->tpfail($this->get('lng.changeProjectFail'));
            return;
        }

        if ($this->get('SESSION.user.hash'))
        {
            $user = new \models\User();
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
