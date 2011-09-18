<?php

class EntriesController {

	function index() {
		if (isset($_POST['space_id'])) {
			$begin = strtotime(@$_POST['date'].' '.@$_POST['begin']);
			$end = strtotime(@$_POST['date'].' '.@$_POST['end']);
			App::get()->session->set('state', array(
				'space_id' => @$_POST['space_id'],
				'date' => date('F j, Y', $end),
				'begin' => date('g:i a', $end),
				));
			$r = App::get()->assembla->req('user/time_entries', array('task',
				'description' => @$_POST['description'],
				'hours' => ($end - $begin)/3600,
				'space_id' => @$_POST['space_id'],
				'ticket_id' => @$_POST['ticket_id'],
				'begin_at' => date('r', $begin),
				'end_at' => date('r', $end),
				));
			if ($r->status == 201) App::get()->redirect('entries');
			else die('Request failed.<pre>'.print_r($r,1).'</pre>');
		}

		$state = App::get()->session->get('state');
		if (!is_array($state)) $state = array();
		$state += array(
			'space_id' => '',
			'date' => date('F j, Y'),
			'begin' => '',
			'end' => '',
			'description' => '',
			);

		App::get()->load_view('entries/index', array(
			'title' => 'Time Entries',
			'entries' => App::get()->assembla->getEntriesList(),
			'state' => $state,
			));
	}

}