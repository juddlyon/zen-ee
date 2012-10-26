<!--
  SETTINGS CONTROL PANEL PAGE
-->
<?= $form['open']; ?>
	<table class="mainTable zen_ee_table z40">
		<tr>
			<th>
				<?= $preference_label ?>
			</th>
			<th>
				<?= $setting_label ?>
			</th>
		</tr>
		<tr>
			<td>
				<?= $form['api_key_label']; ?>
			</td>
			<td>
				<?= $form['api_key']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?= $form['input_videos_dir_label']; ?>
			</td>
			<td>
				<?= $form['input_videos_dir']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?= $form['input_videos_url_label']; ?>
			</td>
			<td>
				<?= $form['input_videos_url']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?= $form['output_videos_path_label']; ?>
			</td>
			<td>
				<?= $form['output_videos_path']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?= $form['output_videos_url_label']; ?>
			</td>
			<td>
				<?= $form['output_videos_url']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?= $form['enable_test_mode_label']; ?>
			</td>
			<td>
				<?= $form['enable_test_mode_true']; ?> On
				<?= $form['enable_test_mode_false']; ?> Off
			</td>
		</tr>
	</table>
	<?= $form['submit']; ?>
<?= $form['close']; ?>