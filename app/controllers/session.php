<?php

class SessionController {

	function index() {
		App::get()->redirect('session/create');
	}

	function create() {
		$app = App::get();
		if ($app->auth->authenticate($_POST)) $app->redirect('entries');
		$app->load_view('session/create', array(
			'title' => 'Log In',
			));
	}

	function destroy() {
		$app = App::get();
		$app->auth->deauthenticate();
		$app->redirect();
	}

}