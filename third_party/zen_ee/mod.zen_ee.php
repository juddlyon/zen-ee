<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

// include Zencoder PHP lib & config
require_once('zencoder-php/Services/Zencoder.php');

/**
 * ZEN EE
 *
 * encode videos via the Zencoder API within EE, insert
 * via Fieldtype
 *
 * @package zen_ee
 * @version 1.0
 * @author Sebastian Brocher <seb@noctual.com>
 * @author Judd Lyon <judd@trifectainteractive.com>
 * @link http://zen.trifectainteractive.com/docs
 * @see http://zencoder.com
 * @copyright Trifecta Interactive 2012
 */

class Zen_ee {

	/**
	*	CONSTRUCTOR
	*/
	public function __construct($str = '')
	{
		$this->EE =& get_instance();
	}

	/**
	*	UPDATE JOB STATUS
	*
	* field job status ping from Zencoder and update database
	*/
	public function update_job_status()
	{
		// load zen_settings lib
		$this->EE->load->library('zen_settings');

		// get API key from module settings
		$api_key = $this->EE->zen_settings->get_setting($this->EE->db, 'zencoder_api');

		// init new Zencoder object
	  	$zencoder = new Services_Zencoder($api_key);

	  	// catch notification
	  	$notification = $zencoder->notifications->parseIncoming();

		// handle notifications
		switch ($notification->output->state) {
		  	// finished output thumbnail
	  		case 'finished':

	  		// grab paths from notification
		  	$outpath_vid = $notification->output->url;
		  	$outpath_thumb = $notification->output->thumbnails[0]['images'][0]['url'];

		  	// convert paths to URLs
	  		$output_video_url = $this->path_to_url($outpath_vid);
	  		$image_url = $this->path_to_url($outpath_thumb);

			// write finished status
			$this->update_job_with($notification->output->id, $output_video_url, $image_url, 'Finished');
	  		break;

	  	default:
			$this->update_job_status_with($notification->output->id, ucfirst($notification->output->state));
	  		break;
	  } // end switch
	} // end  update_job_status

	/**
	* PATH TO URL
	*
	* converts path to URL for output in template
	*
	* @param $path
	* @return URL
	*/
	public function path_to_url($path)
	{
		$url_pieces = explode('/', $path);
		$output_filename = end($url_pieces);

		$output_url = $this->EE->zen_settings->get_setting($this->EE->db, 'output_videos_url');

		return $output_url . $output_filename;
	}

	/**
	* UPDATE JOB STATUS WITH
	*
	* write rec'd status to EE DB
	*/
	public function update_job_status_with($zencoder_job_output_id, $status)
	{
		// clean, only digits
		$zencoder_job_output_id = preg_replace('/[^0-9]/', '', $zencoder_job_output_id);

		$data = array('status' => $this->EE->db->escape_str($status));
		$sql = $this->EE->db->update_string($this->EE->db->dbprefix . 'zen_ee_jobs', $data, "zencoder_job_output_id = '" . $zencoder_job_output_id . "'");
		$this->EE->db->query($sql);
	}

	/**
	* UPDATE JOB WITH
	*
	* write job status, video URL, output URL to DB
	*/
	public function update_job_with($zencoder_job_output_id, $video_url, $thumbnail_url, $status)
	{
		// clean, only digits
		$zencoder_job_output_id = preg_replace('/[^0-9]/', '', $zencoder_job_output_id);

		$data = array(
			'output_video_url' => $this->EE->security->xss_clean($video_url),
			'output_thumbnail_url' => $this->EE->security->xss_clean($thumbnail_url),
			'status' => $this->EE->security->xss_clean($status)
		);

		// query
		$sql = $this->EE->db->update_string($this->EE->db->dbprefix . 'zen_ee_jobs', $data, "zencoder_job_output_id = '" . $zencoder_job_output_id . "'");

		// run query
		$this->EE->db->query($sql);
	}

} // end Zen_ee class

/* EOF mod.zen_ee.php */