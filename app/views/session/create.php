<p>Use your Assembla credentials, even if you normally use OpenID (Google or Yahoo!) to log in to Assembla.</p>
<form method="post">
	<input type="hidden" name="tz_offset" id="ff_tz_offset" value="" />
	<input type="hidden" name="tz_offset_is_dst" id="ff_tz_offset_is_dst" value="" />

	<label for="ff_login">Login ID:</label>
	<input name="login" id="ff_login" type="text" />
	<label for="ff_password">Password:</label>
	<input name="password" id="ff_password" type="password" />

	<input value="Go" type="submit" />
</form>

<script type="text/javascript">
(function() {
	var today = new Date(), offset = today.getTimezoneOffset();
	document.getElementById('ff_tz_offset').value = offset * -60;
	document.getElementById('ff_tz_offset_is_dst').value = (offset != (new Date(today.getFullYear(), 0)).getTimezoneOffset())? '1' : '0';
})();
</script>