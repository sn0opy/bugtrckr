<?php

	class main extends F3instance
	{
        private $helper;

        function  __construct() {
            parent::__construct();

            $this->helper = new helper;
			$this->helper->intlSupport();

			require_once 'inc/mapping.inc.php';
        }

		function start()
		{
            F3::set('pageTitle', '{@lng.home}');
            F3::set('template', 'home.tpl.php');
			$this->tpserve();
		}

        /**
         * 
         */
        function showMilestone()
        {
            $hash = F3::get('PARAMS.hash');
            $ms = new Milestone();
            $ms->load('hash = "' .$hash.'"');

            $tickets = Dao::getTickets('milestone = ' .$ms->getId());

            foreach($tickets as $i=>$ticket)
				$tickets[$i] = $ticket->toArray();

            $stats['ticketCount'] = Dao::getTicketCount($ms->getId());

            $fullCount = 0;
            foreach($stats['ticketCount'] as $cnt)
                $fullCount += $cnt['count'];

            $stats['fullTicketCount'] = $fullCount;

            foreach($stats['ticketCount'] as $j=>$cnt)
            {
                $stats['ticketCount'][$j]['percent'] = round($cnt['count'] * 100 / $fullCount);
                $stats['ticketCount'][$j]['title'] = F3::get("ticket_state.".$stats['ticketCount'][$j]['state']);

                if($stats['ticketCount'][$j]['state'] == 5)
                    $stats['openTickets'] = $fullCount - $stats['ticketCount'][$j]['count'];
            }

            $stats['openTickets'] = ($fullCount) ? $stats['openTickets'] : 0 ;

            F3::set('tickets', $tickets);
            F3::set('stats', $stats);
            F3::set('milestone', $ms->toArray());
            F3::set('pageTitle', '{@lng.milestone} › '. $ms->getName());
            F3::set('template', 'milestone.tpl.php');
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
                $road[$i]['ticketCount'] = Dao::getTicketCount($milestone->getId());

                $fullCount = 0;
                foreach($road[$i]['ticketCount'] as $cnt)
                    $fullCount += $cnt['count'];

                $road[$i]['fullTicketCount'] = $fullCount;

                foreach($road[$i]['ticketCount'] as $j=>$cnt)
                {
                    $road[$i]['ticketCount'][$j]['percent'] = round($cnt['count'] * 100 / $fullCount);
                    $road[$i]['ticketCount'][$j]['title'] = F3::get("ticket_state.".$road[$i]['ticketCount'][$j]['state']);

                    if($road[$i]['ticketCount'][$j]['state'] == 5)
                        $road[$i]['openTickets'] = $fullCount - $road[$i]['ticketCount'][$j]['count'];
                }
                
                $road[$i]['openTickets'] = ($fullCount) ? $road[$i]['openTickets'] : 0 ;

			}

			F3::set('road', $road);
            F3::set('today', date('Y-m-d', time()));
			F3::set('pageTitle', '{@lng.roadmap}');
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
			F3::set('pageTitle', '{@lng.timeline}');
            F3::set('template', 'timeline.tpl.php');
			$this->tpserve();
		}

		/**
		 *
		 */
		function showTickets()
		{
			/* Get ordering */
			$order = F3::get('PARAMS.order') != NULL ?
					F3::get('PARAMS.order') : "id";
			
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
				$tickets[$i] = $ticket->toArray();

			F3::set('milestones', $milestones);
			F3::set('tickets', $tickets);
			F3::set('pageTitle', '{@lng.tickets}');
            F3::set('template', 'tickets.tpl.php');
			$this->tpserve();
		}

		/**
		 *
		 */
		function showTicket()
		{
			$hash = F3::get('PARAMS.hash');

			$ticket = new Ticket();
			$ticket->load("hash = '$hash'");

			$milestone = new Milestone();
			$milestone->load("id = ". $ticket->getMilestone());

			if (true /* hasAccess('editTicket') */)
			{
				$users = Dao::getUsers("1 = 1");
				
				foreach($users as $i=>$user)
				{
					$users[$i] = $user->toArray();
				}

				F3::set('users', $users);
			}

			F3::set('ticket', $ticket->toArray());
			F3::set('milestone', $milestone->toArray());
			F3::set('pageTitle', '{@lng.tickets} › '. $ticket->getTitle());
			F3::set('template', 'ticket.tpl.php');
			$this->tpserve();
		}

		/**
		 *
		 */
		function addTicket()
		{
			$post = F3::get('POST');
			$owner = F3::get('SESSION.userId');

			$ticket = new Ticket();
			$ticket->setHash($this->helper->getFreeHash('Ticket'));
			$ticket->setTitle($post['title']);
			$ticket->setDescription($post['description']);
			$ticket->setOwner($owner);
            $ticket->setAssigned(1); // TODO: get nicklist of users 
			$ticket->setType($post['type']);
			$ticket->setState(1);
			$ticket->setPriority($post['priority']);
			$ticket->setCategory(1);
			$ticket->setMilestone($post['milestone']);

			try 
			{
				$ticket->save();

				Dao::addActivity("created Ticket ". $ticket->getTitle());
				F3::set('PARAMS.hash', $ticket->getHash());
				$this->showTicket();
			}
			catch (Exception $e)
			{
				F3::set('FAILURE', 'Failure while adding Ticket');
				F3::set('pageTitle', 'Failure');
				$this->tpserve();
			}
		}

		/**
		 *
		 */
		function editTicket()
		{
			require_once 'ticket.php';

			$post = F3::get('POST');
			$hash = F3::get('PARAMS.hash');

			$ticket = new Ticket();
			$ticket->load("hash = '$hash'");

			$ticket->setAssigned($post['userId']);
			$ticket->setState($post['state']);

			$hash = $ticket->save();

			/* Redirect to the changed Ticket */			
			if (!is_string($hash) && $hash == 0)
			{
				F3::set('FAILURE', 'Failure while adding Ticket');
				F3::set('pageTitle', 'Failure');
				$this->tpserve();
			}
			else
			{
				Dao::addActivity("changed Ticket ". $ticket->getTitle());
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
				F3::set('pageTitle', 'Failure');
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
            $name = F3::get('PARAMS.name');
            $result = F3::get('DB')->sql('SELECT * FROM User WHERE name = :name', array(':name' => $name));
            
            if(!$result) 
                F3::set('FAILURE', 'User not found.');
            else
                F3::set('user', $result[0]);


            $userTickets = Dao::getTickets('owner = ' .F3::get('user.id'));
            
            foreach($userTickets as $i => $userTicket)
				$userTickets[$i] = $userTicket->toArray();
            
            F3::set('tickets', $userTickets);
            F3::set('template', 'user.tpl.php');
            F3::set('pageTitle', '{@lng.user} › '.$name);
            $this->tpserve();
        }

        /**
         *
         */
        function showUserRegister()
        {
            F3::set('template', 'userRegister.tpl.php');
            F3::set('pageTitle', '{@lng.user} › {@lng.registration}');
            $this->tpserve();
        }

        /**
         *
         */
        function registerUser()
        {
            $salt = $this->helper->randStr();

            $user = new user();
            $user->setName(F3::get('POST.name'));
            $user->setEmail(F3::get('POST.email'));
            $user->setPassword($this->helper->salting($salt, F3::get('POST.password')));
            $user->setSalt($salt);
            $user->setHash($this->helper->getFreeHash('User'));
            $user->setAdmin(0);
            $user->save();
        }

        /**
         *
         */
        function showUserLogin()
        {
            $this->set('template', 'userLogin.tpl.php');
            F3::set('pageTitle', '{@lng.user} › {@lng.login}');
            $this->tpserve();
        }

        /**
         *
         */
        function loginUser()
        {
            $user = new user();
            $user->load('email = "' .$this->get('POST.email'). '"'); // get salt
            $salt = $user->getSalt();

            $user->load("email = '" .$this->get('POST.email'). "' AND password = '" . $this->helper->salting($salt, $this->get('POST.password')). "'");

            if(!$user->getId()) {
                $this->set('FAILURE', 'Login failed.');
                $this->reroute('/'. F3::get('BASE') .'user/login');
            } else {
                $this->set('SESSION.userName', $user->getName());
                $this->set('SESSION.userPassword', $user->getPassword());
                $this->set('SESSION.userHash', $user->getHash());
                $this->set('SESSION.userId', $user->getId());
                $this->reroute('/'. F3::get('BASE'));
            }

            $this->tpserve();
        }

        /**
         *
         */
        function logoutUser()
        {
            $this->set('SESSION.userName', NULL);
            $this->set('SESSION.userPassword', NULL);
            $this->set('SESSION.userHash', NULL);
            $this->set('SESSION.userId', NULL);
            session_destroy();
            $this->reroute('/'. F3::get('BASE'));  
        }

        /**
         * 
         */
		private function tpserve()
		{
			$projects = Dao::getProjects('1 = 1');
			foreach($projects as $i=>$project)
				$projects[$i] = $project->toArray();
            
			F3::set('projects', $projects);
			echo Template::serve('main.tpl.php');
		}

	}
