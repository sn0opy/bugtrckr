<?php

	class Dao
	{
		/**
		 *
		 */
		static function getMilestones($stmt)
		{
			$result = array();
			$i = 0;
 
			$db = new DB(F3::get('DB.dsn'));

			$milestones = $db->sql("SELECT id FROM Milestone WHERE $stmt");

			foreach($milestones as $milestone)
			{
				$result[$i] = new Milestone();
				$result[$i]->load("id = $milestone[id]");
				$i++;
			}

			return $result;
		}

		/**
		 *
		 */
		static function getTickets($stmt)
		{
			$result = array();
			$i = 0;

			$db = new DB(F3::get('DB.dsn'));

			$tickets = $db->sql("SELECT id FROM Ticket WHERE $stmt");

			foreach($tickets as $ticket)
			{
				$result[$i] = new Ticket();
				$result[$i]->load("id = $ticket[id]");
				$i++;
			}

			return $result;
		}


	}
