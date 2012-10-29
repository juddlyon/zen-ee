<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Zen EE Module Control Panel File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author Sebastian Brocher <seb@noctual.com>
 * @author Judd Lyon <judd@trifectainteractive.com>
 * @link		http://juddlyon.github.com/zen-ee
 */

// include Zencoder PHP lib
require_once('vendor/zencoder-php/Services/Zencoder.php');

class Zen_ee_mcp {

	private $base_url;

  function __construct()
  {
		// EE super object
		$this->EE =& get_instance();

		// define base url for module
		$this->base_url = $this->data['base_url'] = BASE . AMP .'C=addons_modules&amp;M=show_module_cp&amp;module=zen_ee';

		// right navigation buttons
		$right_nav = array(
	    	$this->EE->lang->line('videos') => $this->base_url . AMP .'method=videos',
				$this->EE->lang->line('jobs') => $this->base_url . AMP .'method=jobs',
	    	$this->EE->lang->line('settings') => $this->base_url . AMP . 'method=settings'
		);

		$this->EE->cp->set_right_nav($right_nav);

		// load helpers/libs
		$this->EE->load->helper('form');

		$load_libs = array(
			'javascript',
			'table',
			'form_validation',
			'zen_settings'
		);

		$this->EE->load->library($load_libs);

		 // CSS
		$this->EE->cp->add_to_head('<link rel="stylesheet" href="' . URL_THIRD_THEMES . 'zen_ee/css/zen_ee.css">');

		// JS
		$this->EE->cp->add_to_foot('
			<script type="text/javascript" src="' . URL_THIRD_THEMES . 'zen_ee/js/jquery.pajinate.js"></script>
			<script type="text/javascript" src="' . URL_THIRD_THEMES . 'zen_ee/js/zen_ee.js"></script>'
		);
  } // end constructor

	// ----------------------------------------------------------------

  /*
  * INDEX
  *
  * landing page upon install
  */
	public function index()
	{
		$this->EE->functions->redirect($this->base_url . AMP .'method=videos');
	}

	// ----------------------------------------------------------------

  /*
  * JOBS
  */
	public function jobs()
	{
		// page title
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('jobs'));

		// set breadcrumbs
		$this->EE->cp->set_breadcrumb($this->base_url, $this->EE->lang->line('zen_ee_module_name'));

		// retrieve jobs list
		$jobs = array();

		$sql_get_jobs = '
			SELECT *
			FROM ' . $this->EE->db->dbprefix . 'zen_ee_jobs
			ORDER BY id DESC
		';

		$results = $this->EE->db->query($sql_get_jobs);

		if ($results->num_rows() > 0)
		{
			foreach ($results->result_array() as $row)
		  	{
					$jobs[] = $row;
		  	}
		}

		// view variables
		$vars = array(
			'jobs' => $jobs,
			'zencoder_job_id_label' => $this->EE->lang->line('zencoder_job_id_label'),
			'zencoder_job_output_id_label' => $this->EE->lang->line('zencoder_job_output_id_label'),
			'input_video_url_label'	=> $this->EE->lang->line('input_video_url_label'),
			'label_label' => $this->EE->lang->line('label_label'),
			'status_label' => $this->EE->lang->line('status_label'),
			'output_video_path_label' => $this->EE->lang->line('output_video_path_label'),
			'output_thumbnail_url_label' => $this->EE->lang->line('output_thumbnail_url_label'),
			'video_name_label' => $this->EE->lang->line('video_name_label'),
			'details_label' => $this->EE->lang->line('details_label')
		);

		// load view
		return $this->EE->load->view('jobs', $vars, TRUE);
	}

	// ----------------------------------------------------------------

  /*
  * VIDEOS
  */
	public function videos()
	{
		// redirect to settings page if missing settings
		if (! $this->EE->zen_settings->has_all_settings($this->EE->db))
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('incomplete_settings'));
			$this->EE->functions->redirect($this->base_url . AMP .'method=settings');
			return;
		}

		// page title
	  	$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('videos'));

		// set breadcrumbs
		$this->EE->cp->set_breadcrumb($this->base_url, $this->EE->lang->line('zen_ee_module_name'));

		// retrieve file list
		$files = array();

		$input_videos_dir = $this->EE->zen_settings->get_setting($this->EE->db, 'input_videos_dir');

		$input_videos_url = $this->EE->zen_settings->get_setting($this->EE->db, 'input_videos_url');

		$hidden_field_ref_number = 1;

		if ($handle = opendir($input_videos_dir))
		{
			while (false !== ($entry = readdir($handle)))
			{
				$filepath = $input_videos_dir.$entry;
				$url = $input_videos_url . $entry;
				if ($entry != "." && $entry != ".." && !is_dir($filepath))
				{
        	$files[] = array(
          	'modal_class' => 'modal-'. $hidden_field_ref_number,
						'filename' => $entry,
						'filepath' => $filepath,
						'form_submit' => form_submit(
							array(
								'value' => $this->EE->lang->line('encode_submit'),
								'name' => $hidden_field_ref_number,
								'class' => 'submit'
							)
						),
						'form_hidden'	=> form_hidden("hidden_" . $hidden_field_ref_number, $url)
					);
			  }
				$hidden_field_ref_number += 1;
			}
			closedir($handle);
		} // end file listing

		// view variables
		$vars = array(
			'form_open' => form_open('C=addons_modules&amp;M=show_module_cp&amp;module=zen_ee' . AMP . 'method=create_encoding_job', array('class' => 'form', 'id' => 'videos')),
			'files' => $files,
			'form_video_name_label' => form_label($this->EE->lang->line('video_name_label'), 'video_name', array()),
			'form_video_name' => form_input(
				array(
					'name' => 'video_name',
					'value' => '',
					'class' => 'zen_med'
				)
			),
			'form_width_label' => form_label($this->EE->lang->line('width_label'), 'width', array()),
			'form_width' => form_input(
				array(
					'name' => 'width',
					'value' => '720',
					'class' => 'zen_med'
				)
			),
			'form_height_label' => form_label($this->EE->lang->line('height_label'), 'height', array()),
			'form_height' => form_input(
				array(
					'name' => 'height',
					'value' => '480',
					'class' => 'zen_med'
					)
				),
			'form_thumb_time_label' => form_label($this->EE->lang->line('thumb_time_label'), 'thumb_time', array()),
			'form_thumb_time' => form_input(
				array(
					'name' => 'thumb_time',
					'value' => '1',
					'class' => 'zen_med'
				)
			),
			'form_alternate_video_url_label' => form_label($this->EE->lang->line('alternate_video_url_label'), 'alternate_video_url', array()),
			'form_alternate_video_url' => form_hidden('alternate_video_url', ''),
			'form_submit_alternate'	=> form_submit(
				array(
					'value' => $this->EE->lang->line('encode_submit'),
					'name' => 'alternate',
					'class' => 'submit',
					'id' => 'form_submit_alternate'
				)
			),
			'form_close' => form_close(),
			'file_label' => $this->EE->lang->line('file_label'),
			'actions_label' => $this->EE->lang->line('actions_label')
		);

		// load view
		return $this->EE->load->view('videos', $vars, TRUE);
	} // end  videos

	/*
  * SETTINGS
  */
	public function settings()
	{
		// page title
	  	$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('settings'));

		// set breadcrumbs
		$this->EE->cp->set_breadcrumb($this->base_url, $this->EE->lang->line('zen_ee_module_name'));

		// set form variables
		$input_videos_dir = $this->EE->zen_settings->get_setting($this->EE->db, 'input_videos_dir');

		if ($input_videos_dir == '')
		{
			$input_videos_dir = str_ireplace('themes/', '', $this->EE->config->item('theme_folder_path'));
		}

		$input_videos_url = $this->EE->zen_settings->get_setting($this->EE->db, 'input_videos_url');

		if ($input_videos_url == '')
		{
			$input_videos_url =  $this->EE->config->item('base_url');
		}

		$enable_test_mode_db_value = $this->EE->zen_settings->get_setting($this->EE->db, 'enable_test_mode');

		$form = array(
			'open' => form_open('C=addons_modules&amp;M=show_module_cp&amp;module=zen_ee'. AMP .'method=update_settings', array('class' => 'form', 'id' => 'module_settings')),
			'api_key_label' => form_label($this->EE->lang->line('api_key_label'), 'zencoder_api', array()),
			'api_key' => form_input(
				array(
					'name' => 'zencoder_api',
					'value' => $this->EE->zen_settings->get_setting($this->EE->db, 'zencoder_api'),
					'class' => 'zen_med'
				)
			),
			'input_videos_dir_label' => form_label($this->EE->lang->line('input_videos_dir_label'), 'input_videos_dir', array()),
			'input_videos_dir' => form_input(
				array(
					'name' => 'input_videos_dir',
					'value' => $input_videos_dir,
					'class' => 'zen_med'
				)
			),
			'input_videos_url_label' => form_label($this->EE->lang->line('input_videos_url_label'), 'input_videos_url', array()),
			'input_videos_url' => form_input(
				array(
					'name' => 'input_videos_url',
					'value' => $input_videos_url,
					'class' => 'zen_med'
				)
			),
			'output_videos_path_label' => form_label($this->EE->lang->line('output_videos_path_label'), 'output_videos_path', array()),
			'output_videos_path' => form_input(
				array(
					'name' => 'output_videos_path',
					'value' => $this->EE->zen_settings->get_setting($this->EE->db, 'output_videos_path'),
					'class' => 'zen_med'
				)
			),
			'output_videos_url_label' => form_label($this->EE->lang->line('output_videos_url_label'), 'output_videos_url', array()),
			'output_videos_url' => form_input(
				array(
					'name' => 'output_videos_url',
					'value' => $this->EE->zen_settings->get_setting($this->EE->db, 'output_videos_url'),
					'class' => 'zen_med'
				)
			),
			'enable_test_mode_label' => form_label($this->EE->lang->line('enable_test_mode_label'), 'enable_test_mode', array()),
			'enable_test_mode_true' => form_radio(
				array(
					'name' => 'enable_test_mode',
					'value' => 'On',
					'class' => 'zen_check',
					'checked' => ($enable_test_mode_db_value == 'On' || $enable_test_mode_db_value == '')
				)
			),
			'enable_test_mode_false' => form_radio(
				array(
					'name' => 'enable_test_mode',
					'value' => 'Off',
					'class' => 'zen_check',
					'checked' => ($enable_test_mode_db_value == 'Off')
				)
			),
			'submit' => form_submit(
				array(
					'value' => $this->EE->lang->line('submit'),
					'submit' => 'submit',
					'class' => 'submit'
				)
			),
			'close' => form_close()
		);

		// view variables
		$vars = array_merge(
			array(
				'form' => $form
			),
			array(
				'table' => $this->EE->table->generate()
			),
			array(
				'preference_label' => $this->EE->lang->line('preference_label'),
				'setting_label' => $this->EE->lang->line('setting_label')
			)
		);

		// load view
		return $this->EE->load->view('settings', $vars, TRUE);
	} // end settings

	// ----------------------------------------------------------------

	/**
	* PROCESS SETTINGS FORM POST
	*/
	public function update_settings()
	{
		$api_key = $this->EE->input->get_post('zencoder_api');
		$input_videos_dir = $this->ensure_trailing_slash($this->EE->input->get_post('input_videos_dir'));
		$input_videos_url = $this->ensure_trailing_slash($this->EE->input->get_post('input_videos_url'));
		$output_videos_path = $this->ensure_trailing_slash($this->EE->input->get_post('output_videos_path'));
		$output_videos_url = $this->ensure_trailing_slash($this->EE->input->get_post('output_videos_url'));
		$enable_test_mode = $this->EE->input->get_post('enable_test_mode');

		$valid = TRUE;

		if ($valid)
		{
			$valid = $this->EE->zen_settings->add_setting($this->EE->db, 'zencoder_api', $api_key);
			$valid = $valid && $this->EE->zen_settings->add_setting($this->EE->db, 'input_videos_dir', $input_videos_dir);
			$valid = $valid && $this->EE->zen_settings->add_setting($this->EE->db, 'input_videos_url', $input_videos_url);
			$valid = $valid && $this->EE->zen_settings->add_setting($this->EE->db, 'output_videos_path', $output_videos_path);
			$valid = $valid && $this->EE->zen_settings->add_setting($this->EE->db, 'output_videos_url', $output_videos_url);
			$valid = $valid && $this->EE->zen_settings->add_setting($this->EE->db, 'enable_test_mode', $enable_test_mode);
		}

		// flash message
		if ($valid == TRUE)
		{
			$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('settings_update_success'));
		}
		else
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('settings_update_failure'));
		}

		// redirect to settings
		$this->EE->functions->redirect($this->base_url . AMP .'method=settings');
	} // end update_settings

	// ----------------------------------------------------------------

	/**
	* PROCESS VIDEOS FORM POST
	*/
	public function create_encoding_job()
	{

		try {

			// quick validation before creating job
			$encode_job_fields = array(
				array(
					'field' => 'video_name',
					'label' => $this->EE->lang->line('video_name_label'),
					'rules' => 'required'
				),
				array(
					'field' => 'width',
					'label' => $this->EE->lang->line('width_label'),
					'rules' => 'required'
				),
				array(
					'field' => 'height',
					'label' => $this->EE->lang->line('height_label'),
					'rules' => 'required'
				),
				array(
					'field' => 'thumb_time',
					'label' => $this->EE->lang->line('thumb_time_label'),
					'rules' => 'required'
				)
			);

			// set validation
			$this->EE->form_validation->set_rules($encode_job_fields);

			// run validation, show errors on fail
			if (! $this->EE->form_validation->run())
			{
				$this->EE->session->set_flashdata('message_failure', validation_errors());
				$this->EE->functions->redirect($this->base_url . AMP .'method=videos');
			}

			// video encode form post vars
			$video_name = $this->EE->input->get_post('video_name');
			$width = $this->EE->input->get_post('width');
			$height = $this->EE->input->get_post('height');
			$thumb_time = $this->EE->input->get_post('thumb_time');

			// file_video_url or alt must not be empty and needs a valid url
			$submit_name = array_search($this->EE->lang->line('encode_submit'), $_POST);

			// alternate = user specified
			if ($submit_name == 'alternate')
			{
				$input_url = $this->EE->input->get_post('alternate_video_url');
			}
			else
			{
				// hidden field w/ actual URL
				$input_url = $this->EE->input->get_post('hidden_' . $submit_name);
			}

			// scrub vars (thanks @rbanh)
      $video_name = $this->EE->security->xss_clean($video_name);
      $width = $this->EE->security->xss_clean($width);
      $height = $this->EE->security->xss_clean($height);
      $thumb_time = $this->EE->security->xss_clean($thumb_time);
      $input_url = $this->EE->security->xss_clean($input_url);

			$valid = TRUE;

			// process form
			if ($valid)
			{
				$api_key = $this->EE->zen_settings->get_setting($this->EE->db, 'zencoder_api');
				$output_path = $this->EE->zen_settings->get_setting($this->EE->db, 'output_videos_path');
				$enable_test_mode = $this->EE->zen_settings->get_setting($this->EE->db, 'enable_test_mode');

				$zencoder_jobs = $this->create_zencoder_encode_job($input_url, $output_path, $api_key, $video_name, $width, $height, $thumb_time, $enable_test_mode);

				if ($zencoder_jobs == NULL)
				{
					$valid = FALSE;
				}
				else
				{
					// first element is the zencoder job id
					$job_id = $zencoder_jobs[0];

					// 2nd element = array w/ labels => job_output_ids for each output
					$jobs = $zencoder_jobs[1];

					// store all output jobs in DB
					foreach ($jobs as $label => $job_output_id)
					{
						$valid = $valid && $this->add_jobs($video_name, $job_id, $job_output_id, $label, $input_url, $width, $height);
					}
				} // end exception

			} // end create_encoding_job

			// flash message
			if ($valid == TRUE)
			{
				$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('create_encoding_success'));
			}
			else
			{
				$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('create_encoding_failure'));
			}
		}
		catch (Services_Zencoder_Exception $e)
		{
			echo '<pre>';
			var_dump($e);
			echo '</pre>';

			$this->EE->session->set_flashdata('message_failure', $e->getMessage());
		} // end exception

		// redirect to videos
		$this->EE->functions->redirect($this->base_url . AMP .'method=videos');

	} // end create_encoding_job

	// ----------------------------------------------------------------

	/**
	* ADD JOB
	*
	* adds job w/ created status, sets Zencoder's job output ID & vid input URL
	*
	* @return boolean
	*/
	public function add_jobs($video_name, $zencoder_job_id, $zencoder_job_output_id, $zencoder_job_output_label, $input_url, $width, $height)
	{
		$data = array(
			'video_name' => $video_name,
			'zencoder_job_id' => $zencoder_job_id,
			'zencoder_job_output_id' => $zencoder_job_output_id,
			'label' => $zencoder_job_output_label,
			'input_url' => $input_url,
			'width' => $width,
			'height' => $height,
			'status' => 'Created'
		);

		$this->EE->db->insert('zen_ee_jobs', $data);

		if ($this->EE->db->_error_number() == 0)
		{
			return TRUE;
		}

		return FALSE;
	}

	// ----------------------------------------------------------------

	/**
	* CREATE NEW ENCODING JOB
	*
	* @return array|NULL
	*/
	public function create_zencoder_encode_job($intput_url, $output_path, $api_key, $video_name, $width, $height, $thumb_time, $enable_test_mode)
	{
		// get update job status url from action id
		$update_job_status_url = $this->EE->functions->fetch_site_index(0, 0) . QUERY_MARKER . 'ACT=' . $this->EE->cp->fetch_action_id('Zen_ee', 'update_job_status');

	   	$zencoder = new Services_Zencoder($api_key);

		// cheap filename sanitization
		$filename = strtolower(preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $video_name));

		// construct video dimension
		$video_dimension = $width . "x" . $height;

		// new encoding job
		$encoding_props = array(
			"test" => "$enable_test_mode == 'On' || $enable_test_mode == '' ? 'true' : 'false'",
			"input" => "' . $intput_url .'",
			"output" => array(
				/* OUTPUT 1 */
				array(
					"public" => 1,
					"filename" => "' . $filename . '.mp4",
					"device_profile" => "mobile/baseline",
					"base_url" => "' . $output_path . '",
					"format" => "mp4",
					"label" => "mp4",
					"video_codec" => "h264",
					"audio_codec" => "aac",
					"thumbnails" => array(
						"number" => 1,
						"format" => "jpg",
						"aspect_mode" => "crop",
					  	"size" => "' . $video_dimension . '",
					 	"base_url" => "' . $output_path . '",
						"filename" => "tn_' . $filename . '",
						"times" => array(' . $thumb_time . ')
					), // thumbnails
				"notifications" => array(
					array(
			 			"url" => "' . $update_job_status_url . '",
			  			"format" => "json"
					) //notifications 2
				), // notifications 1
				), // output 1
				/* OUTPUT 2 */
				array(
					"public" => 1,
					"filename" => "' . $filename . '.webm",
				 	"device_profile" => "mobile/baseline",
				 	"base_url" => "' . $output_path . '",
				 	"format" => "webm",
				 	"label" => "webm",
				 	"video_codec" => "vp8",
				 	"audio_codec" => "vorbis",
				 	"thumbnails" => array(
					  	"number" => 1,
					  	"format" => "jpg",
					  	"aspect_mode" => "crop",
					  	"size" => "' . $video_dimension . '",
						 "base_url" => "' . $output_path . '",
						"filename" => "tn_' . $filename . '",
						"times" => array(' . $thumb_time . ')
					 ), // thumbnails
					"notifications" => array(
					 	"url" => "' . $update_job_status_url . '",
					  	"format" => "json"
					) // notifications
				 ) // output 2
			) // output array
		); // encoding specs

		 // TESTING STRING
		 //  $encoding_str = '
			// {
			// 	"test": ' . ($enable_test_mode == 'On' || $enable_test_mode == '' ? 'true' : 'false') . ',
			// 	"input": "' . $intput_url .'",
			//  	"output": [
			// 	{
			// 		"public": 1,
			// 		"filename": "' . $filename . '.mp4",
			// 		"device_profile": "mobile/baseline",
			// 		"base_url": "' . $output_path . '",
			// 		"format": "mp4",
			// 		"label": "mp4",
			// 		"video_codec": "h264",
			// 		"audio_codec": "aac",
			// 		"thumbnails": {
			// 			"number": 1,
			// 			"format": "jpg",
			// 			"aspect_mode": "crop",
			// 		    	"size": "' . $video_dimension . '",
			// 		  	"base_url": "' . $output_path . '",
			// 			"filename": "tn_' . $filename . '",
			// 			"times": [' . $thumb_time . ']
			// 	  },
			// 	  "notifications": [
			// 	    {
			// 	      "url": "' . $update_job_status_url . '",
			// 	      "format": "json"
			// 	    }
			// 	  ] // notifications
			// 	},
			// 	{
			// 		"public": 1,
			// 		"filename": "' . $filename . '.webm",
			// 	  	"device_profile": "mobile/baseline",
			// 	  	"base_url": "' . $output_path . '",
			// 	  	"format": "webm",
			// 	  	"label": "webm",
			// 	  	"video_codec": "vp8",
			// 	  	"audio_codec": "vorbis",
			// 	  	"thumbnails": {
			// 	    		"number": 1,
			// 	    		"format": "jpg",
			// 	    		"aspect_mode": "crop",
			// 	    		"size": "' . $video_dimension . '",
			// 		  	"base_url": "' . $output_path . '",
			// 			"filename": "tn_' . $filename . '",
			// 			"times": [' . $thumb_time . ']
			// 	  },
			// 	  "notifications": [
			// 	    {
			// 	      "url": "' . $update_job_status_url . '",
			// 	      "format": "json"
			// 	    }
			// 	  ] // notifications
			// 	}
			// 	] // output
			// }';

			// convert to json
			$encoding_props_json = json_encode($encoding_props);

			$encoding_job = $zencoder->jobs->create($encoding_props_json);

			// return array w/ first element zencoder job id, second  array with (label => job_id) for each output if successful, otherwise NULL
			if ($encoding_job)
			{
				return array(
					$encoding_job->id,
					array(
						'mp4' => $encoding_job->outputs['mp4']->id,
						'webm' => $encoding_job->outputs['webm']->id
					)
				);
			}

		 	foreach ($encoding_job->errors as $error)
		 	{
		 		// rbanh note: should you just throw an exception here? since you're catching it on the create_encoding_job function anyway.
		  	echo $error."\n";
		  }

			exit;

			return NULL;
	} // end

	// ----------------------------------------------------------------

	/**
	* ENSURE TRAILING SLASH
	*
	* @return string
	*/
	private function ensure_trailing_slash($path)
	{
		$last_char = substr($path, -1);

		if ($last_char != '/') {
			$path = $path . '/';
		}

		return $path;
	}

}
/* End of file mcp.zen_ee.php */
/* Location: /system/expressionengine/third_party/zen_ee/mcp.zen_ee.php */