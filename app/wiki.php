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

  public function editEntry($f3)
  {
    $f3->get("log")->write("Calling /wiki");
    $f3->get("log")->write("POST: " . print_r($f3->get("POST"), true));

    if (!helper::getPermission('wiki_editWiki'))
      return $this->tpfail($this->get('lng.insuffPermissions'));

    $hash = $f3->get('POST.hash');
    $content = $f3->get('POST.content');
    $title = $f3->get('POST.title');
    $project = $f3->get('SESSION.projectHash');

    $entry = new DB\SQL\Mapper($this->db, 'WikiEntry');
    $entry->load(array('hash = :hash AND project = :project', array(':hash' => $hash, ':project' => $project)));

    if ($entry->dry())
    {
      $entry->hash = helper::getFreeHash('WikiEntry');
      $entry->title = $title;
      $entry->created = date("Y-m-d H:i:s");
      $entry->created_by = $f3->get('SESSION.user.hash');
      $entry->project = $project;
    }
      
    $entry->content = $content;
    $entry->edited = date("Y-m-d H:i:s");
    $entry->edited_by = $f3->get('SESSION.user.hash');

    $entry->save();

    if ($entry->title == '{{main}}')
      $f3->reroute('/wiki');
    else
      $f3->reroute('/wiki/' . $entry->title);
  }

  public function addDiscussion($f3)
  {
    $f3->get("log")->write("Calling /wikidiscussion");
    $f3->get("log")->write("POST: " . print_r($f3->get("POST"), true));

    if (!helper::getPermission('wiki_editWiki'))
      return $this->tpfail($f3->get('lng.insuffPermissions'));

    $disc = new DB\SQL\Mapper($this->db, 'WikiDiscussion');

		$entry = $f3->get('POST.entry');
		$content = $f3->get('POST.content');

		$disc->hash = helper::getFreeHash('WikiDiscussion');
		$disc->entry = $entry;
		$disc->content = $content;
		$disc->created = date("Y-m-d H:i:s");
		$disc->created_by = $f3->get('SESSION.user.hash');

		$disc->save();

		$f3->reroute('/wiki/discussion/' . $disc->entry);
	}

  public function translateHTML($string)
  {
    $f3 = Base::instance();

    $string = preg_replace('/===(.+)===/', '<h3>${1}</h3>', $string);
    $string = preg_replace('/==(.+)==/', '<h2>${1}</h2>', $string);
    $string = preg_replace('/\'\'\'(.+)\'\'\'/', '<b>${1}</b>', $string);			
    $string = preg_replace('/\'\'(.+)\'\'/', '<i>${1}</i>', $string);
    $string = preg_replace('/----/', '<hr />', $string);
    $string = preg_replace('/\[\[(.+) (.+)\]\]/', '<a href="${1}">${2}</a>', $string);
    $string = preg_replace('/\[\[(.+)\]\]/', '<a href="' . $f3->get('BASE') . '/wiki/${1}">${1}</a>', $string);
    $string = preg_replace('/\~\~(.+)\~\~/', '<pre>${1}</pre>', $string);
    $string = preg_replace('/\n/', '<br />', $string);

    return $string;
  }
	
    
  public function showEntry($f3)
  {
    $f3->get("log")->write("Calling /wiki/@title with @title = " . $f3->get("PARAMS.title"));

    if (!helper::canRead($f3->get('SESSION.project')))
	  	return $this->tpfail($f3->get('lng.insuffPermissions'));

    $title = $f3->get('PARAMS.title');
    $project = $f3->get('SESSION.projectHash');

    if (!($project > 0))
      ; //Fail

    if ($title == null)
      $title = '{{main}}';

    // Load Entry
    $entry = new DB\SQL\Mapper($this->db, 'WikiEntry');
    $entry->load(array("title = :title AND project = :project ORDER BY created", array(":title" => $title, ":project" => $project)));

    // Entry does not exist
    if ($entry->dry())
    {
      $entry->title = $title;
      $entry->content = $f3->get('lng.insertContent');
    }

    if($entry->title == '{{main}}')
      $pagetitle = $f3->get('lng.mainpage');
    else
  	  $pagetitle = $entry->title;

    $f3->set('entry', $entry);
    $f3->set('displayablecontent', $this->translateHTML($entry->content));

    $f3->set('pageTitle', $f3->get('lng.wiki') . ' › ' . $pagetitle);
		$f3->set('title', $pagetitle);
    $f3->set('template', 'wiki.tpl.php');
    $f3->set('onpage', 'wiki');
  }

	public function showDiscussion($f3)
  {
    $f3->get("log")->write("Calling /wiki/discussion/@hash with @hash = " . $f3->get("PARAMS.hash"));

    if (!helper::canRead($f3->get('SESSION.project')))
      return $this->tpfail($f3->get('lng.insuffPermissions'));

    $hash = $f3->get('PARAMS.hash');

    $d = new DB\SQL\Mapper($this->db, 'WikiDiscussion');
    $discussions = $d->find(array('entry = :hash', array(':hash' => $hash)));

    $entry = new DB\SQL\Mapper($this->db, 'WikiEntry');
    $entry->load(array('hash = :hash', array(':hash' => $hash)));

    foreach ($discussions as $discussion)
      $discussion->content = $this->translateHTML($discussion->content);

    if ($entry->title == '{{main}}')
      $pagetitle = $f3->get('lng.mainpage');
    else
      $pagetitle = $entry->title;

    $f3->set('entry', $entry);
    $f3->set('discussions', $discussions);
    $f3->set('pageTitle', $f3->get('lng.wiki') . ' › ' . $pagetitle);
    $f3->set('title', $pagetitle);
    $f3->set('template', 'wikidiscussion.tpl.php');
    $f3->set('onpage', 'wiki');
	}
}
