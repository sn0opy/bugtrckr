<?php

F3::set('ticket_state', array(
	'1' => F3::get('lng.new'),
	'2' => F3::get('lng.assigned'),
	'3' => F3::get('lng.inwork'),
	'4' => F3::get('lng.testing'),
	'5' => F3::get('lng.closed')
));

F3::set('ticket_type', array(
	'1' => F3::get('lng.bug'),
	'2' => F3::get('lng.feature'),
));

F3::set('ticket_priority', array(
	'1' => F3::get('lng.veryhigh'),
	'2' => F3::get('lng.high'),
	'3' => F3::get('lng.normal'),
	'4' => F3::get('lng.low'),
	'5' => F3::get('lng.verylow')
));
