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

    function __construct()
    {
        parent::__construct();
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
        if ($this->id > 0)
        {
            $stat = F3::get('DB')->sql("UPDATE Project SET name = $this->name " .
                    "WHERE id = $this->id");

            return is_array($stat) ? $this->hash : 0;
        }
        else
        {            
            $hash = helper::getFreeHash('Project');
            $id = F3::get('DB')->sql("SELECT max(id)+1 as next FROM Project");
            $stat = F3::get('DB')->sql("INSERT INTO Project " .
                    "(hash, name, description, public) VALUES " .
                    "('". $hash ."', '" .$this->name. "', '" .$this->description. "', ". $this->public. ")");

            return is_array($stat) ? $hash : 0;
        }
    }

    /**
     *
     */
    public function load($stmt)
    {
        $ax = new Axon('Project');
        $ax->load($stmt);

        if (!$ax->dry())
        {
            $this->id = $ax->id;
            $this->hash = $ax->hash;
            $this->name = $ax->name;
            $this->description = $ax->description;
            $this->public = $ax->public;
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
