<?php

	class main
	{
		function start()
		{
            
            F3::set('template', 'home.tpl.php');
			$this->tpserve();
		}

		/**
		 *
		 */
		function showRoadmap()
		{
			$road = array(array());
			$i = 0;
			$ms = new Milestone();
			$t = new Ticket();

			/* Get Project */
			$project = F3::get('project');
			$project = 1;

			/* Get Data from DB */
			$db = new DB(F3::get('DB.dsn'));
			$milestones = $db->sql("SELECT id FROM Milestone WHERE project = $project ORDER BY finished");

			foreach($milestones as $milestone)
			{
				$ms->load("id = $milestone[id]");
				$road[$i]['milestone'] = $ms->toArray();
				$road[$i]['tickets'] = 
					$db->sql("SELECT hash, title, type FROM Ticket WHERE milestone = ". $milestone['id']);
				$road[$i]['ticketcount'] = 
					$db->sql("SELECT count(id) as count FROM Ticket WHERE milestone = ". $milestone['id']);
				$i++;
			}

			F3::set('road', $road);
            F3::set('template', 'roadmap.tpl.php');
			$this->tpserve();
		}

		/**
		 *
		 */
		function showTimeline()
		{
			$a = new Activity();
			$timeline = array();

			$db = new DB(F3::get('DB.dsn'));

			/* Get Project */
			$project = F3::get('project');
			$project = 1;

			$activities = $db->sql("SELECT id FROM Activity WHERE project = $project ORDER BY changed");

			foreach($activities as $activity)
			{
				$a->load("id = $activity[id]");
				$timeline[] = $a->toArray();
			}

			F3::set('activities', $timeline);
            F3::set('template', 'timeline.tpl.php');
			$this->tpserve();
		}

		/**
		 *
		 */
		function showTickets()
		{
			/* Get ordering */
			$order = F3::get('PARAMS["order"]') != NULL ? 
					F3::get('PARAMS["order"]') : "id";
			
			/* Get Project */
			$project = F3::get('project');
			$project = 1;

			/* Get Data from DB */
			$db = new DB(F3::get('DB.dsn'));
			$result = $db->sql("SELECT t.id, t.hash, t.title, t.description, t.created, t.owner, t.type, t.state, t.priority, t.category FROM Ticket t, Milestone m WHERE t.milestone = m.id AND m.project = $project ORDER BY t.$order");

			/* Translate Statenr into String */
			$ticket_state = F3::get('ticket_state');
			foreach($result as $i=>$ticket)
			{
				$result[$i]['state'] = $ticket_state[$ticket['state']];
			}

			F3::set('tickets', $result);
            F3::set('template', 'tickets.tpl.php');
			$this->tpserve();
		}

		/**
		 *
		 */
		function showTicket()
		{
			$hash = F3::get('PARAMS["hash"]');

			$ticket = new Ticket();
			$ticket->load("hash = '$hash'");

			F3::set('ticket', $ticket->toArray());
			F3::set('template', 'ticket.tpl.php');
			$this->tpserve();
		}

		/**
		 *
		 */
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
			$ticket->setMilestone(1);

			$hash = $ticket->save();
			
			/* Redirect to the added Ticket */
			if (!is_string($hash) && $hash == 0)
			{
				F3::set('FAILURE', 'Failure while adding Ticket');
				$this->showTickets();
			}
			else
			{
				F3::set('PARAMS["hash"]', $hash);
				$this->showTicket($hash);
			}
		}

        function showUser()
        {
            $hash = F3::get('PARAMS.hash');
            $db = new DB(F3::get('DB.dsn'));
            $result = $db->sql('SELECT * FROM User WHERE hash = :hash', array(':hash' => $hash));
            
            if(!$result) 
                F3::set('FAILURE', 'Failure, user not found.');
            else
                F3::set('user', $result[0]);

            F3::set('template', 'user.tpl.php');
            $this->tpserve();
        }

        function showUserRegister()
        {
            F3::set('template', 'userRegister.tpl.php');
            $this->tpserve();
        }

        function registerUser()
        {
            $helper = new helper();
            $salt = $helper->randStr();

            $user = new user();
            $user->setName(F3::get('POST.name'));
            $user->setEmail(F3::get('POST.email'));
            $user->setPassword($helper->salting($salt, F3::get('POST.password')));
            $user->setSalt($salt);
            $user->setHash($helper->getFreeHash('User'));
            $user->setAdmin(0);
            $user->save();
        }

		private function tpserve()
		{            
			echo Template::serve('main.tpl.php');
		}

	}
