<select id="ff_ticket_id" name="ticket_id">
	<option value="">No ticket</option>
<? foreach($tickets as $user): ?>
	<optgroup label="<?= $user->name? htmlspecialchars($user->name) : 'Nobody' ?>">
	<? foreach($user->tickets as $t): ?>
		<option value="<?= htmlspecialchars($t->id) ?>"><?= htmlspecialchars($t->number.' ('.$t->status_name.') '.substr($t->summary, 0, 50)) ?></option>
	<? endforeach ?>		
	</optgroup>
<? endforeach ?>
</select>
<a id="tickets_reset" href="#" onclick="return false">Refresh</a>