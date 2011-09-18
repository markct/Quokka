<?php

// Yes, it would be simpler to use $_SESSION, but abstracting it allows for drop-in replacement.

class SessionModel {

	function __construct() {
		if (!@$_SERVER['HTTPS']) {
			header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			die(1);
		}
		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 1);
		ini_set('session.cookie_secure', 1);
		session_name('timely');
		session_set_cookie_params(0, App::get()->root_url);
		session_start();
	}

	function get($key) {
		return array_key_exists($key, $_SESSION)? $_SESSION[$key] : null;
	}

	function get_encrypted($key) {
		if (!array_key_exists($key, $_SESSION)) return null;

		$value = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, substr(App::get()->config->get('encryption_key'), 0, 32), $_SESSION[$key], MCRYPT_MODE_ECB);

		$block = mcrypt_get_block_size('des', 'ecb');
		$pad = ord($value[($len = strlen($value)) - 1]);
		return @unserialize(substr($value, 0, strlen($value) - $pad));
	}
	
	function set($key, $value) {
		$_SESSION[$key] = $value;
	}

	function set_encrypted($key, $value) {
		$value = serialize($value);
		$block = mcrypt_get_block_size('des', 'ecb');
		$pad = $block - (strlen($value) % $block);
		$value .= str_repeat(chr($pad), $pad);

		$_SESSION[$key] = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, substr(App::get()->config->get('encryption_key'), 0, 32), $value, MCRYPT_MODE_ECB);
	}

	function clear() {
		$_SESSION = array();
		session_destroy();
		session_start();
	}

	function destroy() {
		$_SESSION = array();
	    $p = session_get_cookie_params();
	    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
		session_destroy();
	}

}