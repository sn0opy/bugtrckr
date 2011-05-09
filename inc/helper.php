<?php

    class helper extends F3instance {
        
        public function randStr($length = 5)
        {
            return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
        }
        

        public function salting($salt, $pass)
        {
            $salt = md5($salt);
            $pw = md5($pass);
            return sha1(md5($salt.$pw).$salt);
        }


        public function getFreeHash($table, $length = 12)
        {
            $ax = new Axon($table);
            do {
                $hash = self::randStr($length);
                $ax->find('hash = "' .$hash. '"');
            } while(!$ax->dry());
            return $hash;
        }
        

        public static function setTitle($subTitles)
        {
            $title = '';
            $subTitles = (array) $subTitles;

            foreach($subTitles as $sub)
            {
                $seperator = !empty($title) ? ' › ' : '';
                $title .= $seperator .$sub;
            }
            
            F3::set('title', $title.' - '.F3::get('title'));
        }

    }

