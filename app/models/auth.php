<?php

class AuthModel {

	private $info = null;

	function __construct() {
		$app = App::get();
		if (@$app->slugs[0] != 'session' && !$this->info()) {
			$app->redirect('session/create');
		}
		if (isset($this->info->tz_name)) date_default_timezone_set($this->info->tz_name);
	}

	function authenticate($data) {
		if (!@$data['login'] || !@$data['password']) return false;
		$app = App::get();
		$app->session->clear();
		$this->info = (object)$data;
		$r = $app->assembla->req('user/best_profile');
		if ($r->status != 200 || @$r->response->login != $data['login']) {
			$this->info = null;
			return false;
		}
		foreach (array('id', 'login', 'name', 'email') as $f) {
			if ($v = (string)@$r->response->{$f}) $this->info->{$f} = $v;
			else if (!isset($this->info->{$f})) $this->info->{$f} = false;
		}
		if (isset($this->info->tz_offset)) {
			$this->info->tz_name = @timezone_name_from_abbr('', $this->info->tz_offset, @$this->info->tz_offset_is_dst);
			date_default_timezone_set($this->info->tz_name);
		}
		$app->session->set_encrypted('auth_info', $this->info);
		return true;
	}

	function info() {
		if (!$this->info) $this->info = App::get()->session->get_encrypted('auth_info');
		return $this->info;
	}

	function deauthenticate() {
		$this->info = null;
		App::get()->session->destroy();
	}

}