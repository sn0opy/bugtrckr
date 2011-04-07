<?php

	class User extends F3instance
	{
		private $id;
		private $hash;
		private $name;
        private $db;

        public function __construct()
        {
            $this->db = new db('sqlite:'.F3::get('btdb'));
        }


        public function show($userID = false)
        {
            $userID = ($userID) ? $userID : $this->getUserID();
            $this->db->find('ROWID = '.$userID); # does not work with f3 ...
        }


        private function getUserID()
        {
            return 1; # will be replace with cookie stuff later
        }



        public function logout()
        {
            F3::set('SESSION', null);
            session_destroy();
        }

	}
