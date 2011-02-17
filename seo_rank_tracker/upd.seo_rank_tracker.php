<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



/**
 * ExpressionEngine SEO Rank Tracker Module
 *
 * @package			SEO Rank Tracker
 * @subpackage		Modules
 * @category		Modules
 * @author			Benjamin David
 * @link			http://dukt.fr/en/addons/seo-rank-tracker/
 */

class Seo_rank_tracker_upd {

	var $version = '1.0b1';
	
	function Seo_rank_tracker_upd()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}


	// --------------------------------------------------------------------

	/**
	 * Module Installer
	 *
	 * @access	public
	 * @return	bool
	 */	
	function install()
	{
		$this->EE->load->dbforge();

		$data = array(
			'module_name' => 'Seo_rank_tracker',
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);

		$this->EE->db->insert('modules', $data);
		
		
		/* seo ranks table */

		$fields = array(
						'id'			=> array('type' 		 => 'int',
													'constraint'	 => '10',
													'unsigned'		 => TRUE,
													'auto_increment' => TRUE),
						'keywords'		=> array('type' => 'text'),
						'url'			=> array('type' => 'text'),
						'rank'			=> array('type' 		 => 'int',
													'constraint'	 => '10'),
													
						'search_engine'			=> array('type' 		 => 'varchar',
													'constraint'	 => '200'),
						'date'			=> array('type' => 'varchar', 'constraint' => '30'),
						);

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('seo_rank');
		
		
		$data = array(
			'class'		=> 'Seo_rank_tracker' ,
			'method'	=> 'tracker_cron'
		);
		
		$this->EE->db->insert('actions', $data);
		
		return TRUE;

	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Module Uninstaller
	 *
	 * @access	public
	 * @return	bool
	 */
	function uninstall()
	{
		$this->EE->load->dbforge();

		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => 'Seo_rank_tracker'));

		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');

		$this->EE->db->where('module_name', 'Seo_rank_tracker');
		$this->EE->db->delete('modules');
		
		
		$this->EE->db->where('class', 'Seo_rank_tracker');
		$this->EE->db->delete('actions');

		$this->EE->dbforge->drop_table('seo_rank');

		return TRUE;
	}



	// --------------------------------------------------------------------

	/**
	 * Module Updater
	 *
	 * @access	public
	 * @return	bool
	 */	
	
	function update($current='')
	{
		return TRUE;
	}
	
}
/* END Class */

/* End of file upd.download.php */
/* Location: ./system/expressionengine/third_party/modules/download/upd.download.php */