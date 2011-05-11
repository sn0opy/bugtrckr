<?php

/**
 * Activity
 * 
 * Getter / setter class for activities
 * 
 * @package Activity
 * @author Sascha Ohms
 * @author Phillipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
**/

class Activity extends F3instance
{
    private $id;
    private $hash;
    private $description;
    private $user;
    private $project;
    private $changed;

    private $ax;

    function __construct()
    {
        parent::__construct();

        $this->ax = new Axon('Activity');
    }


    public function getId()
    {
        return $this->id;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getChanged()
    {
        return $this->changed;
    }

    public function setProject($project)
    {
        $this->project = $project;
    }

    public function getProject()
    {
        return $this->project;
    }


    /**
     *
     */
    public function save()
    {
        $this->ax->load('hash = "'. $this->hash .'"');
        $this->ax->hash = $this->hash;
        $this->ax->description = $this->description;
        $this->ax->user = $this->user;
        $this->ax->changed = $this->changed;
        $this->ax->project = $this->project;
        $this->ax->save();

        if ($this->ax->_id != NULL && $this->ax->_id <= 0)
            throw new Exception();
    }

    /**
     *
     */
    public function load($stmt)
    {
        $this->ax->load($stmt);

        if(!$this->ax->dry())
        {
            $this->id = $this->ax->id;
            $this->hash = $this->ax->hash;
            $this->description = $this->ax->description;
            $this->user = $this->ax->user;
            $this->changed = $this->ax->changed;
            $this->project = $this->ax->project;
        }
        else
            throw new Exception();
    }

    /**
     *
     */
    public function toArray()
    {
        $activity = array();

        $activity['id'] = $this->id;
        $activity['hash'] = $this->hash;
        $activity['description'] = $this->description;
        $activity['user'] = Dao::getUserName($this->user);
        $activity['changed'] = date('d.m.Y H:i', $this->changed);
        $activity['project'] = $this->project;

        return $activity;
    }
}
    
