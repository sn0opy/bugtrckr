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
            $db = new DB(F3::get('DB.dsn'));
            $ax = new Axon($table, $db);
            do {
                $hash = self::randStr($length);
                $ax->find('hash = "' .$hash. '"');
            } while(!$ax->dry());
            return $hash;
        }

        public function intlSupport()
        {
            // check if intl module is loaded, otherwise use own fallback
            if(!extension_loaded('intl'))
            {
                if(!$this->exists('lng'))
                {
                    $locale = include $this->get('LOCALES').$this->get('LANGUAGE').'.php';
                    $this->set('lng', $locale['lng']);
                }
            }
        }

        public static function objectToArray($object)
        {
            if(!is_object($object) && !is_array($object))
                return $object;

            if(is_object($object))
                $object = get_object_vars($object);
            
            return array_map('self::objectToArray', $object);
        }

    }