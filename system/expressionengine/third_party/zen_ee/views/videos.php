<!--
	VIDEOS CONTROL PANEL PAGE
-->
<!-- file list table -->
<table class="mainTable zen_ee_table z50">
	<thead>
		<tr>
			<th>Filename</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody class="pajinate">
		<!-- loop through & list files -->
		<? foreach($files as $file_array) { ?>
		<tr>
			<td>
				<?= $file_array['filename'] ?>
			</td>
			<td>
				<a
					href="#"
					data-modal="<?= $file_array['modal_class']; ?>"
					class="trig submit"
				>
					Encode
				</a>
			</td>
		</tr>
		<!-- modal -->
		<div
			id="<?= $file_array['modal_class']; ?>"
			class="modal"
			title="<?= $file_array['filename'] ?>"
		>
		<?= $form_open ?>
			<?= $file_array['form_hidden']; ?>

			<?= $form_video_name_label ?>
			<?= $form_video_name ?>

			<?= $form_width_label ?>
			<?= $form_width ?>

			<?= $form_height_label ?>
			<?= $form_height ?>

			<?= $form_thumb_time_label ?>
			<?= $form_thumb_time ?>

			<?= $file_array['form_submit']; ?>

			<?= $form_close ?>
		</div><!-- /.modal -->
		<? } ?>
	</tbody>
</table>

<!-- pagination controls -->
<div class="pagination"></div><!-- /.pagination -->

<!-- alternate video input -->
<table class="mainTable zen_ee_table z50 alt_vid_in">
	<thead>
		<tr>
			<th><?= $form_alternate_video_url_label ?></th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<p>Enter the URL to an externally hosted video file to encode:</p>
				<input type="text" id="alt_vid_url" class="zen_med" />
			</td>
			<td>
				<a
					href="#"
					data-modal="alternate_video_url"
					class="trig submit"
				>
					Encode
				</a>
			</td>
		</tr>
	</tbody>
</table>
<!-- alternate video url modal -->
<div
	id="alternate_video_url"
	class="modal"
	title='<?= $form_alternate_video_url_label ?> Settings'
>
	<?= $form_open ?>
		<?= $form_alternate_video_url ?>

		<?= $form_video_name_label ?>
		<?= $form_video_name ?>

		<?= $form_width_label ?>
		<?= $form_width ?>

		<?= $form_height_label ?>
		<?= $form_height ?>

		<?= $form_thumb_time_label ?>
		<?= $form_thumb_time ?>

		<?= $form_submit_alternate ?>

	<?= $form_close ?>
</div><!-- /.modal -->