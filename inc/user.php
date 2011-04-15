<?php

	class user extends F3instance
	{
		private $id;
        private $hash;
        private $password;
        private $salt;
        private $email;
        private $admin;
        private $name;

        private $ax;

        public function __construct()
        {
            parent::__construct();

            require_once "lib/db.php";
            $this->ax = new Axon('User', new DB(F3::get('DB.dsn')));
        }

        public function setId($id)
        {
            $this->id = $id;
        }

        public function setName($name)
        {
            $this->name = $name;
        }

        public function setHash($hash)
        {
            $this->hash = $hash;
        }

        public function setPassword($password)
        {
            $this->password = $password;
        }

        public function setSalt($salt)
        {
            $this->salt = $salt;
        }

        public function setEmail($email)
        {
            $this->email = $email;
        }

        public function setAdmin($admin)
        {
            $this->admin = $admin;
        }

        public function getId()
        {
            return $this->id;
        }

        public function getName()
        {
            return $this->name;
        }

        public function getHash()
        {
            return $this->hash;
        }

        public function getPassword()
        {
            return $this->password;
        }

        public function getSalt()
        {
            return $this->salt;
        }

        public function getEmail()
        {
            return $this->email;
        }

        public function getAdmin()
        {
            return $this->admin;
        }

        public function save()
        {
            $this->ax->load('hash = "' .$this->hash. '"');
            $this->ax->name = $this->name;
            $this->ax->hash = $this->hash;
            $this->ax->password = $this->password;
            $this->ax->salt = $this->salt;
            $this->ax->email = $this->email;
            $this->ax->admin = $this->admin;
            $this->ax->save();
        }

        public function load($smtm)
        {
            $this->ax->load($smtm);

            if(!$this->ax->dry())
            {
                $this->name = $this->ax->name;
                $this->hash = $this->ax->hash;
                $this->password = $this->ax->password;
                $this->salt = $this->ax->salt;
                $this->email = $this->ax->email;
                $this->admin = $this->ax->admin;
                $this->id = $this->ax->id;
            }
        }
	}
