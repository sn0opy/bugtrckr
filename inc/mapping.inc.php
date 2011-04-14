<?php

$ticket_state = array(
	'1' => F3::get('LANG.NEW'),
	'5' => F3::get('LANG.CLOSED')
);

$ticket_type = array(
	'1' => F3::get('LANG.BUG'),
	'2' => F3::get('LANG.FEATURE'),
);

$ticket_priority = array(
	'1' => F3::get('LANG.VERYHIGH'),
	'2' => F3::get('LANG.HIGH'),
	'3' => F3::get('LANG.NORMAL'),
	'4' => F3::get('LANG.LOW'),
	'5' => F3::get('LANG.VERYLOW')
);


F3::set('ticket_state', $ticket_state);
F3::set('ticket_type', $ticket_type);
F3::set('ticket_priority', $ticket_priority);
