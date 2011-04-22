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

			foreach($milestones as $i=>$milestone)
			{
				$result[$i] = new Milestone();
				$result[$i]->load("id = $milestone[id]");
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

			foreach($tickets as $i=>$ticket)
			{
				$result[$i] = new Ticket();
				$result[$i]->load("id = $ticket[id]");
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

			foreach($activities as $i=>$activity)
			{
				$result[$i] = new Activity();
				$result[$i]->load("id = $activity[id]");
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

			foreach($projects as $i=>$project)
			{
				$result[$i] = new Project();
				$result[$i]->load("id = $project[id]");
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

			foreach($users as $i=>$user)
			{
				$result[$i] = new User();
				$result[$i]->load("id = $user[id]");
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

			$user = new User();
			$user->load("id = $userId");

			$activity = new Activity();

			$activity->setDescription($user->getName() ." $message");
			$activity->setProject($projectId);
			$activity->setUser($userId);

			$activity->save();
		}

        /**
         *
         */
        static function getPermission($permission)
        {
            $userId = F3::get('SESSION.userId');
            $projectId = F3::get('SESSION.project');

            $user = new User();
            $user->load('id = ' .$userID);

            $projPerm = new ProjectPermission();
            $projPerm->load('userId = ' .$userId. ' AND projectId = ' .$projectId);

            $role = new Role();
            $role->load('id = ' .$projPerm->getRoleId());

            $permissions = $role->toArray();
            
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
