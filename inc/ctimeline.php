<?php

require_once('controller.php');

class ctimeline extends Controller
{

    /**
     *
     */
    function showTimeline()
    {
        $timeline = array();

        /* Get Project */
        $project = $this->get('SESSION.project');

        $activities = new DisplayableActivity();
        $activities = $activities->find("project = $project");

        $this->set('activities', $activities);
        $this->set('pageTitle', '{{@lng.timeline}}');
        $this->set('template', 'timeline.tpl.php');

        $this->tpserve();
    }

}