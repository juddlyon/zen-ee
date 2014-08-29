<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Zen EE Module Fieldtype
 *
 * @package ExpressionEngine
 * @subpackage Addons
 * @category Module
 * @author Sebastian Brocher <seb@noctual.com>
 * @author Judd Lyon <judd@trifectainteractive.com>
 * @link http://juddlyon.github.com/zen-ee
 */

// include config
require(PATH_THIRD .'zen_ee/config.php');

class Zen_ee_ft extends EE_Fieldtype {

	/**
	* PROPERTIES
	*/
	public $info = array(
		'name' => ZEN_EE_NAME,
		'version' => ZEN_EE_VERSION
	);
	public $table;
	public $has_array_data = TRUE;

	/**
	* CONSTRUCTOR
	*/
	function __construct()
	{
		parent::__construct();		
		$this->table = $this->EE->db->dbprefix . "zen_ee_jobs";
	}

	// ----------------------------------------------------------------

	/**
	* INSTALL
	*/
	public function install()
	{
		return array();
	}

	// ----------------------------------------------------------------

	/**
	* DISPLAY FIELDS
	*/
	public function display_field($data, $cell = FALSE)
	{
		// load form helper
		$this->EE->load->helper('form');

		// get fields from DB
		$sql_get_fields = "
			SELECT video_name, zencoder_job_id
			FROM $this->table
			WHERE status = 'Finished'
				AND zencoder_job_id
			NOT IN (SELECT DISTINCT(zencoder_job_id) FROM $this->table WHERE status != 'Finished')
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

	// ----------------------------------------------------------------

	/**
	* REPLACE TAG
	*
	* grabs completed jobs and prepares variables for use in template
	*/
	public function replace_tag($data, $params = array(), $tagdata = TRUE)
	{
		$video_query = $this->EE->db->get_where('zen_ee_jobs', array('zencoder_job_id' => $data));

		$vars = array();
		$count = 0;

		foreach ($video_query->result_array() AS $row)
		{
			// assign vars on first loop
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

		$tagdata = $this->EE->functions->prep_conditionals($tagdata, $vars);
		$variables = $this->EE->functions->var_swap($tagdata, $vars);

		return $variables;
	} // end replace_tag

	// ----------------------------------------------------------------

	/**
	* MATRIX SUPPORT
	*/
	public function display_cell($cell_data)
	{
		return $this->display_field($cell_data, TRUE);
	}

	// ----------------------------------------------------------------

	/**
	* LOW VARIABLES SUPPORT
	*/
	public function display_var_field($var_data)
	{
		return $this->display_field($var_data);
	}

}
/* End of file ft.zen_ee.php */
/* Location: /system/expressionengine/third_party/zen_ee/ft.zen_ee.php */