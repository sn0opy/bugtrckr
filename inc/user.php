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

        public function __construct()
        {
            parent::__construct();
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

        public function save()
        {
            
        }




        public function login()
        {
            $email = 'sasch9r@gmail.com'; #F3::get('POST.email');
            $pass = '1234'; #F3::get('POST.password');

            $this->db->find('email = "' .$email. '"');

            if(!$ax->dry())
            {
                $salt = $ax->salt;
                $saltedPass = $this->encryptPw($salt, $pass);

                $this->db->sqlbind('SELECT id FROM User WHERE email = :email AND password = :password', array(':email' => $email, ':password' => $saltedPass));
            }
        }

	}
