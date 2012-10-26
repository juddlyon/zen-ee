<!--
  JOBS CONTROL PANEL PAGE
-->
<table class="mainTable zen_ee_table">
  <thead>
    <tr>
        <th>
          <?= $video_name_label ?>
        </th>
        <th>
          <?= $label_label ?>
        </th>
        <th>
          <?= $status_label ?>
        </th>
        <th>
          <?= $zencoder_job_id_label ?>
        </th>
        <th>
        	<?= $details_label ?>
        </th>
    </tr>
  </thead>
  <tbody class="pajinate">
    <? foreach($jobs as $job) { ?>
      <tr>
        <td><strong><?= $job['video_name'] ?></strong></td>
				<td class="format"><span><?= $job['label'] ?></span></td>
        <td class="status <?= strtolower($job['status']) ?>"><i></i> <?= $job['status'] ?></td>
        <td><?= $job['zencoder_job_id'] ?></td>
        <td>
        	<a href="#" class="view-details">View</a>
					<div class="details" title="Video Details">
					  <table class="mainTable zen_ee_table">
					  	<tr>
					  		<th colspan="3"><?= $job['video_name'] ?> (<?= $job['label'] ?>)</th>
					  	</tr>
				      <tr>
				        <td><?= $output_video_path_label ?>:</td>
				        <td><input value="<?= $job['output_video_url'] ?>" /></td>
	          		<? if ($job['output_thumbnail_url'] != '') { ?>
				        	<td rowspan="4">
	            			<img src="<?= $job['output_thumbnail_url'] ?>" width="90" height="90" class="zen_thumb" />
				        	</td>
			          <? } ?>
				      </tr>
				      <tr>
								<td><?= $zencoder_job_output_id_label ?>:</td>
				        <td><?= $job['zencoder_job_output_id'] ?></td>
				      </tr>
				      <tr>
				        <td><?= $input_video_url_label ?>:</td>
				        <td><input value="<?= $job['input_url'] ?>" /></td>
				      </tr>
					  </table>
					</div><!-- /.details -->
       	</td>
      </tr>
    <? } ?>
  </tbody>
</table>

<div class="pagination"></div><!-- /.pagination -->