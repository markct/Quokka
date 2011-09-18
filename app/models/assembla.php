<?php

class AssemblaModel {

	function loadTickets($space_id, $reset=false) {
		$all_tickets = App::get()->session->get('tickets');
		if (!is_array($all_tickets)) $all_tickets = array();
		if (!$reset && ($tickets = @$all_tickets[$space_id])) return $tickets;

		$r = $this->req('spaces/'.$space_id.'/tickets/report/3');
		if (!@$r->response->ticket) return array();

		$tickets = array();
		foreach ($r->response->ticket as $t) {
			$user = $t->{'assigned-to'};
			$assigned_to = (string)@$user->id;
			if ((string)@$user->login == App::get()->auth->info()->login) $user_id = $assigned_to;

			$id = (string)$t->id;
			$ticket = (object)array(
				'id' => $id,
				// 'status' => (string)$t->status,
				'status_name' => (string)$t->{'status-name'},
				'number' => (string)$t->number,
				'summary' => (string)$t->summary,
				);
			if (!isset($tickets[$assigned_to])) $tickets[$assigned_to] = (object)array(
				'name' => (string)@$user->name,
				'login' => (string)@$user->login,
				'tickets' => array(),
				);
			$tickets[$assigned_to]->tickets[$id] = $ticket;
		}

		if (isset($user_id)) {
			$my_tickets = array($user_id => $tickets[$user_id]);
			unset($tickets[$user_id]);
			$tickets = $my_tickets + $tickets;
		}

		foreach ($tickets as $aid => $x) usort($x->tickets, array(self, 'cmpTickets'));

		$all_tickets[$space_id] = $tickets;
		App::get()->session->set('tickets', $all_tickets);
		return $tickets;
	}

	private function cmpTickets($a, $b) {
		if ($a->status_name == $b->status_name) {
			if ($a->number == $b->number) return 0;
			return ($a->number > $b->number)? 1 : -1;
		}
		return ($a->status_name > $b->status_name)? 1 : -1;
	}

	function loadSpaces() {
		if ($spaces = App::get()->session->get('spaces')) return $spaces;
		$r = $this->req('spaces/my_tool_spaces/14');
		if (!@$r->response->space) return array();
		$spaces = array();
		foreach ($r->response->space as $s) {
			$spaces[(string)$s->id] = (string)$s->name;
		}
		App::get()->session->set('spaces', $spaces);
		return $spaces;
	}

	function getSpaceName($space_id) {
		$spaces = $this->loadSpaces();
		return @$spaces[$space_id];
	}

	function getTicket($space_id, $ticket_id) {
		static $ticketsById = array();
		if (!isset($ticketsById[$space_id])) {
			$temp = array();
			$tickets = $this->loadTickets($space_id);
			foreach($tickets as $user) foreach($user->tickets as $t) $temp[$t->id] = $t;
			$ticketsById[$space_id] = $temp;
		}
		return @$ticketsById[$space_id][$ticket_id];
	}

	function getEntriesList() {
		/* There are three ways I have tried to do this:
		1) Send a GET request to /user/time_entries with from_date and until_date set. This does not work because the server ignores the from_date and until_date parameters.
		2) Send a GET request to /spaces/[space_id]/time_entries/export.xml with from_date and until_date set, and multiple time_space_id[]. This does not work, because when I send multiple time_space_id[], I get no results.
		3) Send a GET request to /user/export_tasks with from_date and until_date set, and and format set to "xml" (request must be made via SSL). I get data when I set format to "csv", but I get a 404 error when I set format to "xml". I need the XML format because the CSV report does not contain all the fields I need (like ticket number, space ID, etc).
		*/
		$r = App::get()->assembla->req('user/time_entries', array(
			'from_date' => date('Y-m-d', time()-1209600),
			'until_date' => date('Y-m-d'),
			), 'GET');
		if (!@$r->response->task) return array();
		$entries = array();
		foreach ($r->response->task as $t) {
			$entries[] = (object)array(
				'id' => (string)@$t->{'id'},
				'space_id' => (string)@$t->{'space-id'},
				'ticket_id' => (string)@$t->{'ticket-id'},
				'begin_at' => (string)@$t->{'begin-at'},
				'end_at' => (string)@$t->{'end-at'},
				'hours' => (float)@$t->{'hours'},
				'description' => (string)@$t->{'description'},
				'ticket_url' => (string)@$t->{'url'},
				);
		}
		usort($entries, function($a, $b) {
			if ($a->begin_at == $b->begin_at) return 0;
			return ($a->begin_at > $b->begin_at)? -1 : 1;
		});
		return $entries;
	}

	function req($uri, $data=false, $method=false) {
		static $c = false;

		if ($method === false) $method = $data? 'POST' : 'GET';

		$postfields = null;
		if ($data) {
			if ($method == 'GET') $uri .= '?'.http_build_query($data);
			else $postfields = $this->createXML($data);
		}

		$user = App::get()->auth->info();

		if (!is_resource($c)) $c = curl_init();
		curl_setopt_array($c, array(
			CURLOPT_URL => 'http://www.assembla.com/'.$uri,
			CURLOPT_USERPWD => $user->login.':'.$user->password,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array(
				'Accept: application/xml',
				'Content-Type: application/xml',
				),
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => $postfields,
			));
		$r = curl_exec($c);
		$s = curl_getinfo($c, CURLINFO_HTTP_CODE);

	    if (trim($r)) $xml = @simplexml_load_string($r);
		else $xml = false;

		return (object)array(
			'status' => $s,
			'response' => $xml,
			'raw' => $xml? false : $r,
			);
	}

	static function createXML($content, $node_name=false) {
		if (is_object($content)) $content = (array)$content;

		$c = '';
		if (is_array($content)) {
			if ($node_name === false && array_key_exists(0, $content)) {
				$node_name = $content[0];
				unset($content[0]);
			}
			foreach ($content as $k => $v) $c .= self::createXML($k, $v);
		}
		else if (is_bool($content)) $c = $content? 'true' : 'false';
		else $c = htmlspecialchars($content);

		return $node_name? '<'.$node_name.'>'.$c.'</'.$node_name.'>' : $c;
	}

}