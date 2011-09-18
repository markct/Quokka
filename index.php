<?php

class App {

	static private $instance = null;
	function get() {
		// get the framework instance
		if (!self::$instance) self::$instance = new self;
		return self::$instance;
	}

	public $slugs, $root_url = '/';

	function __construct() {
		// load and filter the URI
		$uri = trim($_SERVER['PATH_INFO'], '/');
		if (!$uri) $this->slugs = array();
		else $this->slugs = explode('/', $uri);
		foreach ($this->slugs as $s) if (!preg_match('|^[a-z 0-9~%.:_\-]+$|i', $s)) {
			die('The URI you submitted has disallowed characters.');
		}
	}

	function start() {
		date_default_timezone_set('America/New_York');

		// load basic components
		$this->load_model('config');

		$this->root_url = $this->config->get('root_url');

		$this->load_model('session');
		$this->load_model('auth');
		$this->load_model('assembla');

		// route to a controller
		$controller_name = @$this->slugs[0]? $this->slugs[0] : $this->config->get('default_controller');
		if (!is_file('app/controllers/'.$controller_name.'.php')) $this->show_404();
		require_once 'app/controllers/'.$controller_name.'.php';
		$controller_name .= 'Controller';
		if (!class_exists($controller_name)) $this->show_404();
		$controller = new $controller_name;
		$action_name = @$this->slugs[1]? $this->slugs[1] : 'index';
		if (!method_exists($controller, $action_name)) $this->show_404();
		call_user_func_array(array($controller, $action_name), array_slice($this->slugs, 2));
	}

	function load_model($name, $options=false, $class=false) {
		if (isset($this->{$name})) return;
		if ($class && class_exists($class)) {
			$this->{$name} = new $class($options);
			return;
		}
		if (!$class) $class = $name;
		if (!is_file('app/models/'.$class.'.php')) die('File "models/'.htmlspecialchars($class).'.php" not found.');
		require_once 'app/models/'.$class.'.php';
		$class = ucfirst($class.'Model');
		if (!class_exists($class)) die('Class "'.htmlspecialchars($class).'" not found.');
		$this->{$name} = new $class($options);
	}

	function load_view($__app_name, $__app_data=array(), $__app_template='default') {
		if ($__app_template && !is_file('app/views/templates/'.$__app_template.'.php')) die('Template '.htmlspecialchars($__app_template).' not found.');
		if (!is_file('app/views/'.$__app_name.'.php')) die('View '.htmlspecialchars($__app_name).' not found.');
		extract($__app_data, EXTR_SKIP);
		unset($__app_data, $__app_return);

		ob_start();
		include 'app/views/'.$__app_name.'.php';
		$view = ob_get_clean();
		if ($__app_template) include 'app/views/templates/'.$__app_template.'.php';
		else echo $view;
	}

	function url($uri='') {
		return $this->root_url.trim($uri, '/');
	}

	function redirect($uri='') {
		header('Location: '.$this->url($uri));
		die();
	}

	function show_404() {
		header('HTTP/1.0 404 Not Found');
		if (!is_file('app/views/errors/404.php')) die('Page not found.');
		$this->load_view('errors/404');
		die();
	}

}

App::get()->start();