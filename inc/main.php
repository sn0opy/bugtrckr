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
			require_once 'dao.inc.php';

			$road = array();

			/* Get Project */
			$project = F3::get('project');
			$project = 1;

			$milestones = Dao::getMilestones("project = $project");
			
			foreach($milestones as $milestone)
			{
				$road[] = $milestone->toArray();
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
			require_once 'dao.inc.php';

			/* Get ordering */
			$order = F3::get('PARAMS["order"]') != NULL ? 
					F3::get('PARAMS["order"]') : "id";
			
			/* Get Project */
			$project = F3::get('project');
			$project = 1;

			/* Get Data from DB */
			$tickets = Dao::getTickets("milestone IN " .
				"(SELECT id FROM Milestone WHERE project = $project)" .
				"ORDER BY $order");

			foreach($tickets as $i=>$ticket)
			{
				$tickets[$i] = $tickets[$i]->toArray();
			}

			F3::set('tickets', $tickets);
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
				$this->tpserve();
			}
			else
			{
				F3::set('PARAMS["hash"]', $hash);
				$this->showTicket($hash);
			}
		}

		/**
		 *
		 */
		function addMilestone()
		{
			require_once 'milestone.php';

			$post = F3::get('POST');
			$project = F3::get('SESSION.project');
			$project = 1;

			$milestone = new Milestone();
			$milestone->setName($post['name']);
			$milestone->setDescription($post['description']);
			$milestone->setFinished($post['finished']);
			$milestone->setProject($project);

			$hash = $milestone->save();

			if (!is_string($hash) && $hash == 0)
			{
				F3::set('FAILURE', 'Failure while adding Milestone');
				$this->tpserve();
			}
			else
			{
				$this->showRoadmap();
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
