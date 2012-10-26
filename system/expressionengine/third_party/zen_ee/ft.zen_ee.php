<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include config
require(PATH_THIRD .'zen_ee/config.php');

/**
* ZEN EE FIELDTYPE
*
* allows user to select successfully encoded videos via dropdown menu
*/
class Zen_ee_ft extends EE_Fieldtype {

	/**
	* PROPERTIES
	*/
	public $info = array(
		'name' => ZEN_EE_NAME,
		'version'	=> ZEN_EE_VERSION
	);

	public $table;

	public $has_array_data = TRUE;

	/**
	* CONSTRUCTOR
	*/
	function __construct()
	{
		parent::EE_Fieldtype();

		$this->table = $this->EE->db->dbprefix . "zen_ee_jobs";
	}

	/**
	* INSTALL
	*/
	public function install()
	{
		return array();
	}

	/**
	* DISPLAY FIELDS
	*/
	public function display_field($data, $cell = FALSE)
	{
		// load form helper
		$this->EE->load->helper('form');

		// get fields from DB
		// note: use double-quotes on SQL to enable var expansion
		$sql_get_fields = "
			SELECT video_name, zencoder_job_id
			FROM $this->table
			WHERE status = 'Finished'
				AND zencoder_job_id NOT IN (SELECT DISTINCT(zencoder_job_id)
					FROM $this->table WHERE status <> 'Finished')
			GROUP BY zencoder_job_id
			ORDER BY video_name ASC
		";
		$query = $this->EE->db->query($sql_get_fields);

		// generate drop down
		$options = array('' => '--');

		foreach ($query->result_array() AS $row)
		{
			$options[$row['zencoder_job_id']] = $row['video_name'];
		}

		// field name depending on Matrix cell or not
		$field_name = $cell ? $this->cell_name : $this->field_name;

		return form_dropdown($field_name, $options, $data);
	} // end display_field

	/**
	* REPLACE TAG
	*
	* grabs completed jobs and prepares variables for use in template
	*/
	public function replace_tag($data, $params = array(), $tagdata = TRUE)
	{

		$video_query = $this->EE->db->query("SELECT * FROM $this->table WHERE zencoder_job_id = $data");

		$vars = array();

		$count = 0;
		foreach ($video_query->result_array() AS $row)
		{
			if ($count == 0)
			{
				$vars["thumb_url"] = $row['output_thumbnail_url'];
				$vars["input_url"] = $row['input_url'];
				$vars["name"] = $row['video_name'];
				$vars["zencoder_job_id"] = $row['zencoder_job_id'];
				$vars["width"] = $row['width'];
				$vars["height"] = $row['height'];
			}

      		$label = $row['label'];
			$vars[$label . "_url"] = $row['output_video_url'];
			$vars[$label . "_status"] = $row['status'];
			$vars[$label . "_zencoder_job_output_id"] = $row['zencoder_job_output_id'];

			$count++;
		}

		$tmp = $this->EE->functions->prep_conditionals($tagdata, $vars);
		$chunk = $this->EE->functions->var_swap($tmp, $vars);

		// encode_ee_tags breaks ability to run plugins w/in tag pair
		// $chunk = $this->EE->functions->encode_ee_tags($chunk);

		return $chunk;
	} // end replace_tag

	/**
	* MATRIX SUPPORT
	*/
	public function display_cell($cell_data)
	{
		return $this->display_field($cell_data, TRUE);
	}

	/**
	* LOW VARIABLES SUPPORT
	*/
	public function display_var_field($var_data)
	{
		return $this->display_field($var_data);
	}

} // end Zen_ee_ft class

/* END ft.zen_ee.php */