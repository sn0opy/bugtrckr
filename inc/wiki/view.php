<?php

/**
 * view.php
 * 
 * @package Wiki
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */

namespace wiki;

class view extends \misc\controller {
    
    public function showEntry()
    {
        $title = $this->get('PARAMS.title');
        $project = $this->get('SESSION.projectHash');

        if (!($project > 0))
            ; //Fail

        if ($title == null)
            $title = '{{main}}';

        // Load Entry
        $entry = new \wiki\wikiEntry();
        $entry->load(array("title = :title AND project = :project", array(":title" => $title, ":project" => $project)));

        // Entry does not exist
        if ($entry->dry())
        {
            $entry->title = $title;
            $entry->content = "Insert your content here.";
        }

        if ($entry->title == '{{main}}')
            $entry->title = $this->get('lng.mainpage');

        $this->set('entry', $entry);
        $controller = new \wiki\controller;
        $this->set('displayablecontent', $controller->translateHTML($entry->content));

        $this->set('pageTitle', '{{@lng.wiki}} › ' . $entry->title);
        $this->set('template', 'wiki.tpl.php');
        $this->set('onpage', 'wiki');
        $this->tpserve();
    }
    
}