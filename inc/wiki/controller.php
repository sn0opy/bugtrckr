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
        $this->set('displayablecontent', $this->translateHTML($entry->content));

        $this->set('pageTitle', '{{@lng.wiki}} › ' . $entry->title);
        $this->set('template', 'wiki.tpl.php');
        $this->set('onpage', 'wiki');
        $this->tpserve();
    }


    public function editEntry()
    {
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
            $entry->created = date();
            $entry->created_by = 1;
            $entry->project = $project;
        }

        $entry->content = $content;
        $entry->edited = date();
        $entry->edited_by = 1;

        $entry->save();

        if ($entry->title == '{{main}}')
            $this->reroute($this->get('BASE') . '/wiki');
        else
            $this->reroute($this->get('BASE') . '/wiki/' . $entry->title);
    }


    private function translateHTML($string)
    {
        $string = preg_replace('/===(.+)===/', '<h3>${1}</h3>', $string);
        $string = preg_replace('/==(.+)==/', '<h2>${1}</h2>', $string);
        $string = preg_replace('/\'\'\'(.+)\'\'\'/', '<b>${1}</b>', $string);			$string = preg_replace('/\'\'(.+)\'\'/', '<i>${1}</i>', $string);
        $string = preg_replace('/----/', '<hr />', $string);
        $string = preg_replace('/\[\[(.+)\]\]/', '<a href="' . $this->get('BASE') . '/wiki/${1}">${1}</a>', $string);
//			$string = preg_replace('/\[\[(.+) (.+)\]\]/', '<a href="${1}">${2}</a>', $string);
        $string = preg_replace('/\~\~(.+)\~\~/', '<pre>${1}</pre>', $string);
        $string = preg_replace('/\n/', '<br />', $string);

        return $string;
    }
}
