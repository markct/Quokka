<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<title><?= @$title? strip_tags($title).' - ' : '' ?>Quokka</title>
	<link rel="icon" href="<?= App::get()->url('favicon.ico') ?>" type="image/x-icon">
	<link rel="shortcut icon" href="<?= App::get()->url('favicon.ico') ?>" type="image/x-icon">
	<link rel="stylesheet" href="<?= App::get()->url('skin/main.css').'?'.@filemtime('skin/main.css') ?>" type="text/css" media="screen" charset="utf-8">
	<script type="text/javascript">
		var root_url = <?= json_encode(App::get()->root_url) ?>;
	</script>
</head><body>

<div class="container clearfix">
	<div class="header">
		<div>
		<? if ($user_info = App::get()->auth->info()): ?>
			Logged in as <?= $user_info->name ?>
			<a href="<?= App::get()->url('session/destroy') ?>">Log Out</a>
		<? endif ?>
		</div>
		<h1><?= @$title ?></h1>
	</div>
	<?= $view ?>
</div>

</body></html>