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
            F3::set('template', 'roadmap.tpl.php');
			$this->tpserve();
		}


		function showTimeline()
		{
            F3::set('template', 'timeline.tpl.php');
			$this->tpserve();
		}


		function showTickets()
		{
			/* Get ordering */
			$order = F3::get('PARAMS["ordering"]') == " " ? 
					F3::get('PARAMS["ordering"]') : "id";
			

			/* Get Project */
			$project = F3::get('project');
			$project = 1;

			/* Get Data from DB */
			$db = F3::get('DB');
			$db = new DB($db['dsn']);
			$result = $db->sql("SELECT * FROM Ticket WHERE project = $project ORDER BY $order");

			F3::set('tickets', $result);
            F3::set('template', 'tickets.tpl.php');
			$this->tpserve();
		}


		function showTicket()
		{
			$hash = F3::get('PARAMS["hash"]');

			$db = F3::get('DB');
			$db = new DB($db['dsn']);
			$result = $db->sql("SELECT * FROM Ticket WHERE id = $hash");

			F3::set('ticket', $result[0]);
            F3::set('template', 'ticket.tpl.php');
			$this->tpserve();
		}


		function addTicket()
		{
			$post = F3::get('POST');
			var_dump($post);
			$hash = 1;	// Created while saving into DB

			/* Redirect to the added Ticket */
			F3::set('PARAMS["hash"]', $hash);
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

        public function testUser()
        {
            $user = new user;
            $user->login();
            $this->tpserve();
        }
	}
