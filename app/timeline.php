<?php

/**
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright 2013 Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

class Timeline extends Controller {
    function showTimeline($f3) {
		if (!ctype_alnum($f3->get('SESSION.project')))
			return $this->tpfail($f3->get('lng.noProject'));

		if (!helper::canRead($f3->get('SESSION.project')))
			return $this->tpfail($f3->get('lng.insuffPermissions'));

        $timeline = array();

        $project = $f3->get('SESSION.project');

        $activities = new \DB\SQL\Mapper($f3->get('DB'), 'displayableactivity');
        $activities = $activities->find(array("project = :proj", array(':proj' => $project)));

        $f3->set('activities', $activities);
        $f3->set('pageTitle', $f3->get('lng.timeline'));
        $f3->set('template', 'timeline.tpl.php');
        $f3->set('onpage', 'timeline');
    }
	
}