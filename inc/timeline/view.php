<?php

/**
 * ctimeline.php
 * 
 * timeline controller
 * 
 * @package timeline
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */
namespace timeline;

class view
{

    /**
     *	Show all activities of a project
     */
    function showTimeline()
    {
		if (!ctype_alnum($this->get('SESSION.project')))
			return $this->tpfail('Please select a project.');

        $timeline = array();

        $project = $this->get('SESSION.project');

        $activities = new DisplayableActivity();
        $activities = $activities->find("project = '$project'");

        $this->set('activities', $activities);
        $this->set('pageTitle', '{{@lng.timeline}}');
        $this->set('template', 'timeline.tpl.php');
        $this->set('onpage', 'timeline');
        $this->tpserve();
    }

}
