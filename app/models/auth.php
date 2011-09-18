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
		App::get()->session->clear();
		$this->info = (object)array(
			'login' => $login,
			'password' => $password,
			);
		App::get()->session->set_encrypted('auth_info', $this->info);
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