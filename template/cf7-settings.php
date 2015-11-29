<form method="post">
	<table class="form-table">
		<tr>
			<th>
				<?php echo __('SMS Service API',CF7SI_TXT); ?> <br/><br/>
				<?php echo __('Usable Codes : ',CF7SI_TXT); ?> <br/>
				<address>{MOBILENUMBER}</address>
				<address>{MESSAGE}</address>			
			</th>
			<td>
				<textarea rows="4" cols="85" name="api_urls"><?php echo get_option(CF7SI_DB_SLUG.'api_urls',''); ?></textarea>
				<p class="description">Example : http://example.com/smsAPI/send.php?mobile={MOBILENUMBER}&message={MESSAGE}&senderID=XXX&username=xxx&password=xxx&route=3</p>
			</td>
		</tr>
		<tr>
			<td><input type="submit" name="save_api_settings" value="Update API" class="button button-primary" /> </td>
		</tr>
	</table>
</form>
