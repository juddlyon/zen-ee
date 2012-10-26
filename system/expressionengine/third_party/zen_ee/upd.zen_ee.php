<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include configuration
require(PATH_THIRD .'zen_ee/config.php');


/**
* ZEN EE UPD
*
* install, update, uninstall
*/
class Zen_ee_upd {

	public $version = ZEN_EE_VERSION;

	/**
	* CONSTRUCTOR
	*
	* init EE
	*/
  function __construct()
  {
   	$this->EE =& get_instance();
  }

  /**
  * INSTALL
  *
  * add record to exp_modules, create zen_ee tables
  * @return TRUE
  */
	function install()
	{
		// load CI DB Forge utility
		$this->EE->load->dbforge();

		// add module info to exp_modules
		$data = array(
		    'module_name' => ZEN_EE_CLASS_NAME,
		    'module_version' => ZEN_EE_VERSION,
		    'has_cp_backend' => 'y',
		    'has_publish_fields' => 'n'
		);

		$this->EE->db->insert('modules', $data);

		// create zen_ee tables
		$fields = array(
			'id' => array(
				'type' => 'int',
				'constraint' => '10',
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
		  'name' => array(
		  	'type' => 'varchar',
		  	'constraint' => '255',
		  	'null' => FALSE
		  ),
		  'value' => array(
		  	'type' => 'varchar',
		  	'constraint' => '255',
		  	'null' => FALSE,
		  	'default' => ''
		  )
		);

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('zen_ee_settings');
		$this->EE->db->query('CREATE UNIQUE INDEX zen_ee_settings_unique_value ON ' . $this->EE->db->dbprefix . 'zen_ee_settings(name);');

		// create zen_ee_jobs table
		$fields = array(
			'id' => array(
				'type' => 'int',
				'constraint' => '10',
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'video_name' => array(
				'type' => 'varchar',
				'constraint' => '255',
				'null' => FALSE
			),
			'zencoder_job_id'	=> array(
				'type' => 'varchar',
				'constraint' => '255',
				'null' => FALSE
			),
			'zencoder_job_output_id' => array(
				'type' => 'varchar',
				'constraint' => '255',
				'null' => FALSE
			),
			'label' => array(
				'type' => 'varchar',
				'constraint' => '255',
				'null' => FALSE
			),
			'input_url' => array(
				'type' => 'varchar',
				'constraint' => '255',
				'null' => FALSE
			),
			'output_video_url' => array(
				'type' => 'varchar',
				'constraint' => '255',
				'null' => FALSE
			),
			'output_thumbnail_url' => array(
				'type' => 'varchar',
				'constraint' => '255',
				'null' => FALSE
			),			
			'height' => array(
				'type' => 'int',
				'constraint' => '10',
				'unsigned' => TRUE,
				'null' => FALSE				
			),
			'width' => array(
				'type' => 'int',
				'constraint' => '10',
				'unsigned' => TRUE,
				'null' => FALSE								
			),						
			'status' => array(
				'type' => 'varchar',
				'constraint' => '50',
				'null' => FALSE
			)
		);
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('zen_ee_jobs');


		// ADD ACTION ID
		$data = array(
		  'class' => ZEN_EE_CLASS_NAME,
		  'method' => 'update_job_status'
		);
		$this->EE->db->insert('actions', $data);


		return TRUE;
	} // end install


	/**
	* UPDATE
	*
	* compares filesystem version to DB version
	*
	* @return boolean
	*/
	function update($current = '')
	{
		if (version_compare($current, '1.0', '='))
	    {
	    	return FALSE;
	    }

	    if (version_compare($current, '1.0', '<'))
	    {
	    	// RUN UPDATE
	    }

	  return TRUE;
	} // end update


	/**
	* UNINSTALL
	*
	* deletes record(s) from exp_modules, exp_actions, & exp_zen_ee
	*
	* @return TRUE
	*/
	function uninstall()
	{
		$this->EE->load->dbforge();

	  $this->EE->db->select('module_id');
	  $query = $this->EE->db->get_where('modules', array('module_name' => ZEN_EE_CLASS_NAME));

	  // delete from exp_modules
	  $this->EE->db->where('module_name', ZEN_EE_CLASS_NAME);
	  $this->EE->db->delete('modules');

		// drop zen_ee_settings
		$this->EE->dbforge->drop_table('zen_ee_settings');

		// drop zen_ee_jobs
		$this->EE->dbforge->drop_table('zen_ee_jobs');

		// delete update_job_status action
	  $this->EE->db->where('class', ZEN_EE_CLASS_NAME);
	  $this->EE->db->delete('actions');

		return TRUE;
	} // end uninstall

} // end Zen_ee_upd class

/*EOF upd.zen_ee.php */