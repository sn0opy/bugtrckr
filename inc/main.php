<?php

	class main 
	{
		function start()
		{
            
            F3::set('template', 'home.tpl.php');
			$this->tpserve();
		}


		function showRoadmap()
		{
			
			$this->tpserve();
		}


		function showTimeline()
		{

			$this->tpserve();
		}


		function showTickets()
		{

			$this->tpserve();
		}


		function showTicket($hash)
		{

			$this->tpserve();
		}


		function addTicket()
		{

			$hash = rand();	// Created while saving into DB

			$this->showTicket($hash);
		}

        function showUsers()
        {
            $this->tpserve();
        }

        function showUser()
        {

            $user = new user;
            $user->show();
            $this->tpserver();
        }

		private function tpserve()
		{
            
			echo Template::serve('main.tpl.php');
		}
	}
