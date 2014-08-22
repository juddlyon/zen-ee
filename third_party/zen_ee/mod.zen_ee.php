<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Zen EE Module
 *
 * @package ExpressionEngine
 * @subpackage Addons
 * @category Module
 * @author Sebastian Brocher <seb@noctual.com>
 * @author Judd Lyon <judd@trifectainteractive.com>
 * @link http://juddlyon.github.com/zen-ee
 */

// include Zencoder PHP lib & config
require_once('libraries/zencoder-php/Services/Zencoder.php');

class Zen_ee {

	/**
	*	CONSTRUCTOR
	*/
	public function __construct()
	{
		$this->EE =& get_instance();
	}

	// ----------------------------------------------------------------

	/**
	*	UPDATE JOB STATUS
	*
	* field job status ping from Zencoder and update database
	*/
	public function update_job_status()
	{
		// load zen_settings model
		$this->EE->load->model('zen_settings');

		// get API key from module settings
		$api_key = $this->EE->zen_settings->get_setting('zencoder_api');

		// init new Zencoder object
	  $zencoder = new Services_Zencoder($api_key);

	  // catch notification
	  $notification = $zencoder->notifications->parseIncoming();

		// get vid type
		reset($notification->job->outputs);
		$type = key($notification->job->outputs);

		// handle notifications
		switch ($notification->job->outputs[$type]->state) {
	  	// finished output thumbnail
  		case 'finished':
				$outpath_vid = $notification->job->outputs[$type]->url;
        $outpath_thumb = $notification->job->outputs[$type]->thumbnails[0]->images[0]->url;

		  	// convert paths to URLs
	  		$output_video_url = $this->path_to_url($outpath_vid);
	  		$image_url = $this->path_to_url($outpath_thumb);

				// write finished status
				$this->update_job_with($notification->job->outputs[$type]->id, $output_video_url, $image_url, 'Finished');
  			break;

  		default:
				$this->update_job_status_with($notification->job->outputs[$type]->id, ucfirst($notification->job->outputs[$type]->state));
  			break;
	  } // end switch
	} // end  update_job_status

	// ----------------------------------------------------------------

	/**
	* PATH TO URL
	*
	* converts path to URL for output in template
	*
	* @param $path
	* @return string URL
	*/
	public function path_to_url($path)
	{
		$url_pieces = explode('/', $path);
		$output_filename = end($url_pieces);

		$output_url = $this->EE->zen_settings->get_setting('output_videos_url');

		return $output_url . $output_filename;
	}

	/**
	* UPDATE JOB STATUS WITH
	*
	* write rec'd status to EE DB
	*/
	public function update_job_status_with($zencoder_job_output_id, $status)
	{
		$zencoder_job_output_id = preg_replace('/[^0-9]/', '', $zencoder_job_output_id);

		$data = array('status' => $this->EE->db->escape_str($status));

		$sql = $this->EE->db->update_string('zen_ee_jobs', $data, "zencoder_job_output_id = '" . $zencoder_job_output_id . "'");

		$this->EE->db->query($sql);
	}

	// ----------------------------------------------------------------

	/**
	* UPDATE JOB WITH
	*
	* write job status, video URL, output URL to DB
	*/
	public function update_job_with($zencoder_job_output_id, $video_url, $thumbnail_url, $status)
	{
		$zencoder_job_output_id = preg_replace('/[^0-9]/', '', $zencoder_job_output_id);

		$data = array(
			'output_video_url' => $this->EE->security->xss_clean($video_url),
			'output_thumbnail_url' => $this->EE->security->xss_clean($thumbnail_url),
			'status' => $this->EE->security->xss_clean($status)
		);

		$sql = $this->EE->db->update_string('zen_ee_jobs', $data, "zencoder_job_output_id = '" . $zencoder_job_output_id . "'");

		$this->EE->db->query($sql);
	}

}
/* End of file mod.zen_ee.php */
/* Location: /system/expressionengine/third_party/zen_ee/mod.zen_ee.php */