<?php

/**
 * 
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright 2013 Bugtrckr-Team
 * @license http://www.gnu.org/licenses/gpl.txt
 *   
 */

class Wiki extends Controller
{

    public function editEntry()
    {
		if (!helper::getPermission('wiki_editWiki'))
			return $this->tpfail($this->get('lng.insuffPermissions'));

        $hash = $this->get('POST.hash');
        $content = $this->get('POST.content');
        $title = $this->get('POST.title');
        $project = $this->get('SESSION.projectHash');

        $entry = new \models\WikiEntry();
        $entry->load(array('hash = :hash AND project = :project', array(':hash' => $hash, ':project' => $project)));

        if ($entry->dry())
        {
            $entry->hash = helper::getFreeHash('WikiEntry');
            $entry->title = $title;
            $entry->created = date("Y-m-d H:i:s");
            $entry->created_by = $this->get('SESSION.user.hash');
            $entry->project = $project;
        }

        $entry->content = $content;
        $entry->edited = date("Y-m-d H:i:s");
        $entry->edited_by = $this->get('SESSION.user.hash');

        $entry->save();

        if ($entry->title == '{{main}}')
            $this->reroute('/wiki');
        else
            $this->reroute('/wiki/' . $entry->title);
    }

	public function addDiscussion()
	{
		if (!helper::getPermission('wiki_editWiki'))
			return $this->tpfail($this->get('lng.insuffPermissions'));

		$disc = new \models\WikiDiscussion();

		$entry = $this->get('POST.entry');
		$content = $this->get('POST.content');

		$disc->hash = helper::getFreeHash('WikiDiscussion');
		$disc->entry = $entry;
		$disc->content = $content;
		$disc->created = date("Y-m-d H:i:s");
		$disc->created_by = $this->get('SESSION.user.hash');

		$disc->save();

		$this->reroute('/wiki/discussion/' . $disc->entry);
	}

    public function translateHTML($string)
    {
        $string = preg_replace('/===(.+)===/', '<h3>${1}</h3>', $string);
        $string = preg_replace('/==(.+)==/', '<h2>${1}</h2>', $string);
        $string = preg_replace('/\'\'\'(.+)\'\'\'/', '<b>${1}</b>', $string);			
		$string = preg_replace('/\'\'(.+)\'\'/', '<i>${1}</i>', $string);
        $string = preg_replace('/----/', '<hr />', $string);
		$string = preg_replace('/\[\[(.+) (.+)\]\]/', '<a href="${1}">${2}</a>', $string);
        $string = preg_replace('/\[\[(.+)\]\]/', '<a href="' . $this->get('BASE') . '/wiki/${1}">${1}</a>', $string);
        $string = preg_replace('/\~\~(.+)\~\~/', '<pre>${1}</pre>', $string);
        $string = preg_replace('/\n/', '<br />', $string);

        return $string;
    }
	
    
    public function showEntry()
    {
		if (!helper::canRead($this->get('SESSION.project')))
			return $this->tpfail($this->get('lng.insuffPermissions'));

        $title = $this->get('PARAMS.title');
        $project = $this->get('SESSION.projectHash');

        if (!($project > 0))
            ; //Fail

        if ($title == null)
            $title = '{{main}}';

        // Load Entry
        $entry = new \models\WikiEntry();
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
        $controller = new \controllers\Wiki();
        $this->set('displayablecontent', $controller->translateHTML($entry->content));

        $this->set('pageTitle', $this->get('lng.wiki') . ' › ' . $pagetitle);
		$this->set('title', $pagetitle);
        $this->set('template', 'wiki.tpl.php');
        $this->set('onpage', 'wiki');
        $this->tpserve();
    }

	public function showDiscussion()
	{
		if (!helper::canRead($this->get('SESSION.project')))
			return $this->tpfail($this->get('lng.insuffPermissions'));

		$hash = $this->get('PARAMS.hash');

		$d = new \models\WikiDiscussion();
		$discussions = $d->find(array('entry = :hash', array(':hash' => $hash)));

		$entry = new \models\WikiEntry();
		$entry->load(array('hash = :hash', array(':hash' => $hash)));

		$controller = new \controllers\Wiki();
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
