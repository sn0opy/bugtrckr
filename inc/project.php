<?php

/**
 * project.php
 * 
 * Getter / setter class for projects
 * 
 * @package Project
 * @author Sascha Ohms
 * @author Phillipp Hirsch
 * @copyright Copyright 2011, Bugtrckr-Team
 * @license http://www.gnu.org/licenses/lgpl.txt
 *   
**/
class Project extends F3instance
{
    private $id;
    private $hash;
    private $name;
    private $public;
    private $description;
    
    private $ax;

    function __construct()
    {
        parent::__construct();
        
        $this->ax = new Axon('Project');
    }


    public function getId()
    {
        return $this->id;
    }

    public function getHash()
    {
        return $this->hash;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function getPublic()
    {
        return $this->public;
    }
    
    public function setPublic($public)
    {
        $this->public = $public;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     *
     */
    public function save()
    {        
        $this->ax->id = $this->id;
        $this->ax->name = $this->name;
        $this->ax->hash = $this->hash;
        $this->ax->description = $this->description;
        $this->ax->public = $this->public;
        $this->ax->save();    
    }

    /**
     *
     */
    public function load($stmt)
    {
        $this->ax->load($stmt);

        if (!$this->ax->dry())
        {
            $this->id = $this->ax->id;
            $this->hash = $this->ax->hash;
            $this->name = $this->ax->name;
            $this->description = $this->ax->description;
            $this->public = $this->ax->public;
        }
    }


    /**
     *
     */
    public function toArray()
    {
        $project = array();

        $project['id'] = $this->id;
        $project['hash'] = $this->hash;
        $project['name'] = $this->name;
        $project['description'] = $this->description;
        $project['public'] = $this->public;

        return $project;
    }
}
