<?php

	class Dao extends F3instance
	{
		/**
		 *
		 */
		static function getMilestones($stmt)
		{
			$result = array();
			$milestones = F3::get('DB')->sql("SELECT id FROM Milestone WHERE $stmt");

			/* Selecting Milestones failed */
			if ($milestones == NULL)
				throw new Exception();

			/* Get Milestones data */
			foreach($milestones as $i=>$milestone)
			{
				try {
					$result[$i] = new Milestone();
					$result[$i]->load("id = $milestone[id]");
				} catch (Exception $e) {
					throw $e;
				}
			}

			return $result;
		}

		/**
		 *
		 */
		static function getTickets($stmt)
		{
			$result = array();

			$tickets = F3::get('DB')->sql("SELECT id FROM Ticket WHERE $stmt");

			/* Selecting Tickets failed */
			if ($tickets == NULL)
				throw new Exception();

			/* Get Tickets data */
			foreach($tickets as $i=>$ticket)
			{
				try {
					$result[$i] = new Ticket();
					$result[$i]->load("id = $ticket[id]");
				} catch (Exception $e) {
					throw $e;
				}
			}

			return $result;
		}

		/**
		 *
		 */
		static function getActivities($stmt)
		{
			$result = array();

			$activities = F3::get('DB')->sql("SELECT id FROM Activity WHERE $stmt");

			/* Selecting Activities failed */
			if ($activities == NULL)
				throw new Exception();

			/* Get Activities data */
			foreach($activities as $i=>$activity)
			{
				try {
					$result[$i] = new Activity();
					$result[$i]->load("id = $activity[id]");
				} catch (Exception $e) {
					throw $e;
				}
			}

			return $result;
		}

		/**
		 *
		 */
		static function getProjects($stmt)
		{
			$result = array();

			$projects = F3::get('DB')->sql("SELECT id FROM Project WHERE $stmt");

			/* Selecting Projects failed */
			if ($projects == NULL)
				throw new Exception();

			/* Get Projects data */
			foreach($projects as $i=>$project)
			{
				try {
					$result[$i] = new Project();
					$result[$i]->load("id = $project[id]");
				} catch (Exception $e) {
					throw $e;
				}
			}

			return $result;
		}

		/**
		 *
		 */
		static function getUsers($stmt)
		{
			$result = array();

			$users = F3::get('DB')->sql("SELECT id FROM User WHERE $stmt");

			/* Selecting Users failed */
			if ($users == NULL)
				throw new Exception();

			/* Get Users data */ 
			foreach($users as $i=>$user)
			{
				try {
					$result[$i] = new User();
					$result[$i]->load("id = $user[id]");
				} catch (Exception $e) {
					throw $e;
				}
			}

			return $result;
		}

		/**
		 *
		 */
		static function addActivity($message)
		{
			$userId = F3::get('SESSION.userId');
			$projectId = F3::get('SESSION.project');

			try {
				$user = new User();
				$user->load("id = $userId");
				
				$activity = new Activity();

				$activity->setHash($this->helper->getFreeHash('Activity'));
				$activity->setDescription($user->getName() ." $message");
				$activity->setProject($projectId);
				$activity->setUser($userId);

				$activity->save();
			} catch (Exception $e) {
				return $e;
			}
		}

        /**
         *
         */
        static function getPermission($permission)
        {
            $userId = F3::get('SESSION.userId');
            $projectId = F3::get('SESSION.project');

			try {
        	    $user = new User();
            	$user->load('id = ' .$userId);

         		$projPerm = new ProjectPermission();
            	$projPerm->load('userId = ' .$userId. ' AND projectId = ' .$projectId);

	            $role = new Role();
				$role->load('id = ' .$projPerm->getRoleId());

			} catch (Exception $e) {
				throw $e;
			}

            $permissions = $role->toArray();

            if($user->getAdmin()) // admin has access to everything
                return true;

            if(in_array($permission, $permissions))
                if($permissions[$permission] == true)
                    return true;

            return false;
        }

        /**
         *
         */
        static function getUserName($uid)
        {
            $ax = new Axon('User');
            $ax->load('id = ' .$uid);
            return $ax->name;
        }

        /*
         *
         */
        static function getTicketCount($milestone)
        {
            return F3::get('DB')->sql('SELECT state, COUNT(*) AS `count` FROM `Ticket` WHERE milestone = ' .$milestone. ' GROUP BY state');
        }
	}
