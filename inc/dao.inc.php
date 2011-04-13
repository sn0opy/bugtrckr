<?php

	class Dao
	{
		/**
		 *
		 */
		static function getMilestones($stmt)
		{
			$result = array();
 
			$db = new DB(F3::get('DB.dsn'));

			$milestones = $db->sql("SELECT id FROM Milestone WHERE $stmt");

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

			$db = new DB(F3::get('DB.dsn'));

			$tickets = $db->sql("SELECT id FROM Ticket WHERE $stmt");

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

			$db = new DB(F3::get('DB.dsn'));

			$activities = $db->sql("SELECT id FROM Activity WHERE $stmt");

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

			$db = new DB(F3::get('DB.dsn'));

			$projects = $db->sql("SELECT id FROM Project WHERE $stmt");

			foreach($projects as $i=>$project)
			{
				$result[$i] = new Project();
				$result[$i]->load("id = $project[id]");
			}

			return $result;
		}


	}
