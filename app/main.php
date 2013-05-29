<?php

/**
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2013, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

class Main extends Controller
{
	/**
	 * 
	 * @param type $f3
	 */
  function start($f3)
  {
    $f3->get("log")->write("Calling /");
    $f3->set('pageTitle', $f3->get('lng.home'));
    $f3->set('template', 'home.tpl.php');
    $f3->set('onpage', 'start');
  }

	/**
	 * 
	 * @param type $f3
	 * @param type $params
	 * @param type $hash
	 * @param type $routeBack
	 * @return type
	 */
  function selectProject($f3 = false, $params = false, $hash = false, $routeBack = true)
  {
    if(!$f3)
      $f3 = Base::instance();

    $f3->get("log")->write("Calling /project/select/@project with @project = " . $f3->get("PARAMS.project"));
		
    $url = $f3->get('SERVER.HTTP_REFERER');
    $projHash = ($hash) ? $hash : $f3->get('PARAMS.project');
		
    $project = new DB\SQL\Mapper($this->db, 'Project');
    $project->load(array("hash = :hash", array(':hash' => $projHash)));

    if($project->dry())
      return $this->tpfail($f3->get('lng.changeProjectFail'));

    if ($f3->get('SESSION.user.hash'))
    {
      $user = new DB\SQL\Mapper($this->db, 'User');
      $user->load(array('hash = :hash', array(':hash' => $f3->get('SESSION.user.hash'))));

      if (!$user->dry())
      {
        $user->lastProject = $project->hash;
       	$user->save();
      }
    }

    $f3->set('SESSION.project', $project->hash);
    $f3->set('SESSION.projectHash', $project->hash);

    if($routeBack)
      $f3->reroute($url);
  }
}
