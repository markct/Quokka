<form class="time_entry_form" method="post">
	<div>
		<select name="space_id" id="ff_space_id">
			<option value="">-- space --</option>
		<? foreach(App::get()->assembla->loadSpaces() as $v => $l): ?>
			<option value="<?= htmlspecialchars($v) ?>"<?= $v==$state['space_id']? ' selected="selected"' : '' ?>><?= htmlspecialchars($l) ?></option>
		<? endforeach ?>
		</select>

		<span id="tickets_wrap"></span>
	</div>

	<div>
		<label for="ff_date">Date:</label>
		<input name="date" id="ff_date" type="text" value="<?= htmlspecialchars($state['date']) ?>" />

		&nbsp; <label for="ff_begin">Begin:</label>
		<input name="begin" id="ff_begin" type="text" value="<?= htmlspecialchars($state['begin']) ?>" />
		<a href="#" onclick="Entries.setNow('#ff_date','#ff_begin');return false">now</a>

		&nbsp; <label for="ff_end">End:</label>
		<input name="end" id="ff_end" type="text" value="<?= htmlspecialchars($state['end']) ?>" />
		<a href="#" onclick="Entries.setNow('#ff_date','#ff_end');return false">now</a>

		&nbsp; <label for="ff_description">Note:</label>
		<input name="description" id="ff_description" type="text" value="<?= htmlspecialchars($state['description']) ?>" />

		<input type="submit" value="Save" />
	</div>
</form>

<table class="entries"><tbody>
<? foreach($entries as $e): ?>
	<tr>
		<td><?= $e->description ?></td>
		<td><?= App::get()->assembla->getSpaceName($e->space_id) ?></td>
		<td style="width:30%"><?
			if (!$e->ticket_id) echo '';
			else if ($t = App::get()->assembla->getTicket($e->space_id, $e->ticket_id)) {
				echo '<a href="https://www.assembla.com'.$e->ticket_url.'">'
					.htmlspecialchars($t->number.' ('.$t->status_name.')')
					.'</a> '.htmlspecialchars($t->summary);
			} else if ($e->ticket_url) echo '<a href="https://www.assembla.com'.$e->ticket_url.'">Inactive ticket</a>';
			else echo 'Unknown ticket';
		?></td>
		<td><?= $e->hours ?></td>
		<td><?= date('n/j g:i a', strtotime($e->begin_at)).' - '.date('g:i a', strtotime($e->end_at)) ?></td>
		<td>
			&nbsp; <a class="edit_link" href="https://www.assembla.com/user/time_entry_edit/<?= $e->id ?>">Edit</a>
			&nbsp; <a class="delete_link" href="https://www.assembla.com/user/time_entry_delete/<?= $e->id ?>">Delete</a>
		</td>
	</tr>
<? endforeach ?>
</tbody></table>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js"></script>
<script type="text/javascript" src="<?= App::get()->url('skin/entries.js').'?'.@filemtime('skin/entries.js') ?>"></script>
<script type="text/javascript">
	jQuery(function($) { Entries.init(); });
</script>