<?php

class ProjectPermission extends F3instance
{
    private $userId;
    private $projectId;
    private $roleId;

    private $ax;

    public function  __construct()
    {
        parent::__construct();

        $this->ax = new Axon('ProjectPermission');
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function getProjectId() {
        return $this->projectId;
    }

    public function setProjectId($projectId) {
        $this->projectId = $projectId;
    }

    public function getRoleId() {
        return $this->roleId;
    }

    public function setRoleId($roleId) {
        $this->roleId = $roleId;
    }

    public function save()
    {
        $this->ax->userId = $this->userId;
        $this->ax->projectId = $this->projectId;
        $this->ax->roleId = $this->roleId;
        $this->ax->save();
    }

    public function load($stmt)
    {
        $this->ax->load($stmt);

        if(!$this->ax->dry())
        {
            $this->userId = $this->ax->userId;
            $this->roleId = $this->ax->roleId;
            $this->projectId = $this->ax->projectId;
        }
    }
}