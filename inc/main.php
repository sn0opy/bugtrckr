<?php

	require_once 'dao.inc.php';

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
			$road = array();

			/* Get Project */
			$project = F3::get('SESSION.project');

			/* Get Milestones */				
			$milestones = Dao::getMilestones("project = $project");

			foreach($milestones as $i=>$milestone)
			{
				$road[$i]['milestone'] = $milestone->toArray();

				/* Get all Tickets of this milestone */				
				$road[$i]['tickets'] = Dao::getTickets("milestone = ". $milestone->getId());
				foreach($road[$i]['tickets'] as $j=>$ticket)
				{
					$road[$i]['tickets'][$j] = $ticket->toArray();
				}

				$road[$i]['ticketcount'] = count($road[$i]['tickets']);
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
			$timeline = array();

			/* Get Project */
			$project = F3::get('SESSION.project');

			$activities = Dao::getActivities("project = $project");

			foreach($activities as $activity)
			{
				$timeline[] = $activity->toArray();
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
			$project = F3::get('SESSION.project');

			/* Get Milestones of the Project */
			$milestones = Dao::getMilestones("project = $project");

			foreach($milestones as $i=>$milestone)
			{
				$milestones[$i] = $milestone->toArray();
			}

			/* Get Data from DB */
			$tickets = Dao::getTickets("milestone IN " .
				"(SELECT id FROM Milestone WHERE project = $project)" .
				"ORDER BY $order");

			foreach($tickets as $i=>$ticket)
			{
				$tickets[$i] = $ticket->toArray();
			}

			F3::set('milestones', $milestones);
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

			$milestone = new Milestone();
			$milestone->load("id = ". $ticket->getMilestone());

			F3::set('ticket', $ticket->toArray());
			F3::set('milestone', $milestone->toArray());
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
			$owner = F3::get('SESSION.user');

			$ticket = new Ticket();
			$ticket->setTitle($post['title']);
			$ticket->setDescription($post['description']);
			$ticket->setOwner($owner);
			$ticket->setType($post['type']);
			$ticket->setState(1);
			$ticket->setPriority($post['priority']);
			$ticket->setCategory(1);
			$ticket->setMilestone($post['milestone']);

			$hash = $ticket->save();
			
			/* Redirect to the added Ticket */
			if (!is_string($hash) && $hash == 0)
			{
				F3::set('FAILURE', 'Failure while adding Ticket');
				$this->tpserve();
			}
			else
			{
				Dao::addActivity("created Ticket ". $ticket->getTitle());
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
				Dao::addActivity("created Milestone ". $milestone->getName());
				$this->showRoadmap();
			}
		}

		/**
		 *
		 */
		function selectProject()
		{
			$post = F3::get('POST');
			$url = F3::get('SERVER.HTTP_REFERER');

			$project = new Project();
			$project->load("hash = '$post[project]'");

			F3::set('SESSION.project', $project->getId());

			F3::reroute($url);
		}

		/**
		 *
		 */
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
			$projects = Dao::getProjects('1 = 1');
			foreach($projects as $i=>$project)
			{
				$projects[$i] = $project->toArray();
			}

			F3::set('projects', $projects);
	
			echo Template::serve('main.tpl.php');
		}

	}
