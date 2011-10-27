<?php

/**
 * cwiki.php
 * 
 * Everything comes together in here
 * 
 * @package Wiki
 * @author Sascha Ohms
 * @author Philipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
 */

namespace wiki;

class controller extends \misc\controller
{

    public function editEntry()
    {
		if (!\misc\helper::getPermission('wiki_editWiki'))
			return $this->tpfail('You don\'t have the permissions to do this');

        $hash = $this->get('POST.hash');
        $content = $this->get('POST.content');
        $title = $this->get('POST.title');
        $project = $this->get('SESSION.projectHash');

        $entry = new \wiki\wikiEntry();
        $entry->load(array('hash = :hash AND project = :project', array(':hash' => $hash, ':project' => $project)));

        if ($entry->dry())
        {
            $entry->hash = \misc\helper::getFreeHash('WikiEntry');
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
            $this->reroute($this->get('BASE') . '/wiki');
        else
            $this->reroute($this->get('BASE') . '/wiki/' . $entry->title);
    }

	public function addDiscussion()
	{
		if (!\misc\helper::getPermission('wiki_editWiki'))
			return $this->tpfail('You don\'t have the permissions to do this');

		$disc = new \wiki\WikiDiscussion;

		$entry = $this->get('POST.entry');
		$content = $this->get('POST.content');

		$disc->hash = \misc\helper::getFreeHash('WikiDiscussion');
		$disc->entry = $entry;
		$disc->content = $content;
		$disc->created = date("Y-m-d H:i:s");
		$disc->created_by = $this->get('SESSION.user.hash');

		$disc->save();

		$this->reroute($this->get('BASE') . '/wikidiscussion/' . $disc->entry);
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
}
