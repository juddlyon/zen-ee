<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* ZEN EE SETTINGS
*
* helper functions to add, get, and check for module settings
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
	}

	/**
	* ADD SETTING
	*
	* if not present, insert record, otherwise update
	*
	* @return boolean
	*/
	public function add_setting($db_handle, $setting_name, $setting_value)
	{
		$db_handle->query("INSERT INTO " . $db_handle->dbprefix . "zen_ee_settings(name, value) VALUES('$setting_name','$setting_value')
		 	ON DUPLICATE KEY UPDATE value='$setting_value';");

		if ($db_handle->_error_number() == 0)
		{
			return TRUE;
		}

		return FALSE;
	}


	/**
	* GET SETTING
	*
	* retrieve setting value
	*
	* @return ''|$setting_value
	*/
	public function get_setting($db_handle, $setting_name)
	{
		$query = $db_handle->get_where($db_handle->dbprefix . 'zen_ee_settings', array('name' => $setting_name), 1);

		$setting_value = $query->row('value');

		if ($setting_value == NULL) {
			return '';
		}
		return $setting_value;
	}


	/**
	* HAS ALL SETTINGS
	*
	* check if all setttings are filled out
	*
	* @return boolean
	*/
	public function has_all_settings($db_handle)
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
				if ($this->get_setting($db_handle, $setting) == '')
				{
					return FALSE;
				}
			}

		return TRUE;
	}


}
/* EOF settings.php */