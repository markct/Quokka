var Entries = {
	init: function() {
		var begin = jQuery('#ff_begin'),
			end = jQuery('#ff_end');
		if (begin.val()) end.focus();
		else begin.focus();
		jQuery('.delete_link').click(function() {
			return confirm('Are you sure?');
		});
		jQuery('#ff_spaces').change(function() {
			var el = jQuery('#tickets_wrap').html('')
			if (this.value) el.load(root_url + 'tickets/listing', { space: this.value });
		}).change();
		jQuery('#tickets_reset').live('click', function() {
			var orig_value = jQuery('#ff_ticket_id option:selected').val();
			jQuery('#tickets_wrap').html('').load(root_url + 'tickets/listing', {
				space: jQuery('#ff_spaces').val(),
				reset: Math.random()
			}, function() {
				jQuery('#ff_ticket_id').val(orig_value);
			});
		});
		jQuery('.time_entry_form').submit(function() {
			var valid = 1, clear = function() {
				if (this.value) jQuery(this).unbind('change', clear).css('background-color', 'white');
			};
			jQuery('input[type="text"]').each(function() {
				if (this.value) return true;
				valid = 0;
				jQuery(this).css('background-color', '#ffdddd').change(clear);
			});
			if (!valid) return false;
		});
	},
	getNow: function() {
		var d = new Date(),
			h = d.getHours(),
			m = d.getMinutes(),
			pm = h > 12;
		return (pm? h-12 : h) + ':' + (m<10? '0'+m : m) + ' ' + (pm? 'pm' : 'am');
	}
};