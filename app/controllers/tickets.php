<?php

class TicketsController {

	function listing() {
		App::get()->load_view('tickets/listing', array(
			'tickets' => App::get()->assembla->loadTickets(@$_POST['space'], @$_POST['reset']),
		), false);
	}

}