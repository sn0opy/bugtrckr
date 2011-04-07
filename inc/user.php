<?php

	class User extends F3instance
	{
		private $id;
		private $hash;
		private $name;
        private $db;

        public function __construct()
        {
            $this->db = new db('sqlite:'.F3::get('DB'));
        }


        public function show($userID = false)
        {
            $userID = ($userID) ? $userID : $this->getUserID();
            $this->db->find('id = '.$userID);
        }


        private function getUserID()
        {
            return 1; # will be replace with cookie stuff later
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

        public function encryptPw($salt, $pass)
        {
            $salt = md5($salt);
            $pw = md5($pass);
            return sha1(md5($salt.$pw).$salt);
        }

        public function logout()
        {
            F3::set('SESSION', null);
            session_destroy();
        }

	}
