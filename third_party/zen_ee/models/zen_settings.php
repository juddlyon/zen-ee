<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Zen EE Module Settings Helper
 *
 * @package ExpressionEngine
 * @subpackage Addons
 * @category Module
 * @author Sebastian Brocher <seb@noctual.com>
 * @author Judd Lyon <judd@trifectainteractive.com>
 * @link http://juddlyon.github.com/zen-ee
 */

class Zen_settings {

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function __construct()
	{
		$this->EE =& get_instance();
		$this->dbprefix = $this->EE->db->dbprefix;
	}

	// ----------------------------------------------------------------

	/**
	* ADD SETTING
	*
	* if not present, insert record, otherwise update
	*
	* @return void
	*/
	public function add_setting($setting_name, $setting_value)
	{
		$setting_value = $this->EE->security->xss_clean($setting_value);

		$query = $this->EE->db->query("
			INSERT INTO " . $this->dbprefix . "zen_ee_settings(name, value)
			VALUES('$setting_name', '$setting_value')
		 	ON DUPLICATE KEY UPDATE value = '$setting_value';
		");
	}

	// ----------------------------------------------------------------

	/**
	* GET SETTING
	*
	* retrieve setting value or false
	*
	* @return $setting_row->name
	*/
	public function get_setting($setting_name)
	{
		$query = $this->EE->db->get_where('zen_ee_settings', array('name' => $setting_name));

		if ($query->num_rows() > 0)
		{
			$setting_row = $query->row();
			return $setting_row->value;
		}
		else
		{
			return FALSE;
		}
	}

	// ----------------------------------------------------------------

	/**
	* HAS ALL SETTINGS
	*
	* check if all setttings are filled out
	*
	* @return boolean
	*/
	public function has_all_settings()
	{
		$setting_list = array(
			'zencoder_api',
			'input_videos_dir',
			'input_videos_url',
			'output_videos_path',
			'output_videos_url'
		);

		foreach ($setting_list as $setting)
		{
			$this->get_setting($setting);
		}
	}

}
/* End of file zen_settings.php */
/* Location: /system/expressionengine/third_party/zen_ee/libraries/zen_settings.php */