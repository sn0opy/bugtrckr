<?php

/**
 * wiki\view.php
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
		if (!\misc\helper::canRead($this->get('SESSION.project')))
			return $this->tpfail($this->get('lng.insuffPermissions'));

        $title = $this->get('PARAMS.title');
        $project = $this->get('SESSION.projectHash');

        if (!($project > 0))
            ; //Fail

        if ($title == null)
            $title = '{{main}}';

        // Load Entry
        $entry = new \wiki\wikiEntry();
        $entry->load(array("title = :title AND project = :project ORDER BY created", array(":title" => $title, ":project" => $project)));

        // Entry does not exist
        if ($entry->dry())
        {
            $entry->title = $title;
            $entry->content = $this->get('lng.insertContent');
        }

        if ($entry->title == '{{main}}')
            $pagetitle = $this->get('lng.mainpage');
		else
			$pagetitle = $entry->title;

        $this->set('entry', $entry);
        $controller = new \wiki\controller;
        $this->set('displayablecontent', $controller->translateHTML($entry->content));

        $this->set('pageTitle', $this->get('lng.wiki') . ' › ' . $pagetitle);
		$this->set('title', $pagetitle);
        $this->set('template', 'wiki.tpl.php');
        $this->set('onpage', 'wiki');
        $this->tpserve();
    }

	public function showDiscussion()
	{
		if (!\misc\helper::canRead($this->get('SESSION.project')))
			return $this->tpfail($this->get('lng.insuffPermissions'));

		$hash = $this->get('PARAMS.hash');

		$d = new \wiki\WikiDiscussion;
		$discussions = $d->find(array('entry = :hash', array(':hash' => $hash)));

		$entry = new \wiki\WikiEntry;
		$entry->load(array('hash = :hash', array(':hash' => $hash)));

		$controller = new \wiki\controller;
		foreach ($discussions as $discussion)
			$discussion->content = $controller->translateHTML($discussion->content);

        if ($entry->title == '{{main}}')
            $pagetitle = $this->get('lng.mainpage');
		else
			$pagetitle = $entry->title;

		$this->set('entry', $entry);
		$this->set('discussions', $discussions);
        $this->set('pageTitle', $this->get('lng.wiki') . ' › ' . $pagetitle);
		$this->set('title', $pagetitle);
        $this->set('template', 'wikidiscussion.tpl.php');
        $this->set('onpage', 'wiki');
        $this->tpserve();
	}
}
