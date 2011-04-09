<?php

$ticket_state = array(
	'1' => F3::get('LANG.NEW'),
	'5' => F3::get('LANG.CLOSED')
);

$ticket_type = array(
	'1' => F3::get('LANG.BUG'),
	'2' => F3::get('LANG.FEATURE')
);


F3::set('ticket_state', $ticket_state);
F3::set('ticket_type', $ticket_type);
