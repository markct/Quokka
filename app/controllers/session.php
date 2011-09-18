<?php

class SessionController {

	function index() {
		App::get()->redirect('session/create');
	}

	function create() {
		$app = App::get();
		if (($l = @$_POST['login']) && $app->auth->authenticate($l, @$_POST['password'])) {
			$app->redirect('entries');
		}
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