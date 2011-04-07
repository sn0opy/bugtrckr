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
			require_once 'ticket.php';

			$post = F3::get('POST');
			$owner = F3::get('SESSION');

			$ticket = new Ticket();
			$ticket->setTitle($post['title']);
			$ticket->setDescription($post['description']);
			$ticket->setOwner(1/*$owner*/);
			$ticket->setType($post['type']);
			$ticket->setState(1);
			$ticket->setPriority($post['priority']);
			$ticket->setCategory(1);
			$ticket->setProject(1);

			var_dump($ticket->save());

			$hash = 1;
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
	}
