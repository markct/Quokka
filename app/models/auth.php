<?php

class AuthModel {

	private $info = null;

	function __construct() {
		$app = App::get();
		if (@$app->slugs[0] != 'session' && !$this->info()) {
			$app->redirect('session/create');
		}
	}

	function authenticate($login, $password) {
		$app = App::get();
		$app->session->clear();
		$this->info = (object)array(
			'login' => $login,
			'password' => $password,
			);
		$r = $app->assembla->req('user/best_profile');
		if ($r->status != 200 || @$r->response->login != $login) {
			$this->info = null;
			return false;
		}
		$this->info = (object)array(
			'id' => (string)@$r->response->id,
			'login' => (string)@$r->response->login,
			'name' => (string)@$r->response->name,
			'email' => (string)@$r->response->email,
			'password' => $password,
			);
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