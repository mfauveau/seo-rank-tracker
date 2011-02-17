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


class Seo_rank_tracker_mcp {
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function Seo_rank_tracker_mcp()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		$this->ch = curl_init();
		$this->data = array();
		$this->base_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=seo_rank_tracker';
		
		$this->data['search_engines'] = array(
			'google-us' => array('Google US', "http://www.google.com/"),
			'google-de' => array('Google Germany', "http://google.de/"),
			'google-be' => array('Google Belgium', "http://google.be/"),
			'google-es' => array('Google Spain', "http://www.google.es/"),
			'google-fr' => array('Google France', "http://www.google.fr/"),
			'google-nl' => array('Google Holland', "http://google.nl/"),
			'google-it' => array('Google Italy', "http://www.google.it/"),
			'google-pt' => array('Google Portugal', "http://google.pt/"),
			'google-ch' => array('Google Swiss', "http://google.ch/"),
			'google-cn' => array('Google China', "http://google.cn/"),
			'google-jp' => array('Google Japan', "http://google.jp/"),
			'google-mx' => array('Google Mexico', "http://google.com.mx/"),
			'google-ca' => array('Google Canada', "http://google.ca/"),
			'google-uk' => array('Google United Kingdom', "http://google.co.uk/")
		);
		
		$this->data['search_engines_dropdown'] = array();
		foreach($this->data['search_engines'] as $k => $v) {
			$this->data['search_engines_dropdown'][$k] = $v[0];	
		}
		
		

		// Get current site	
		$this->data['site_id'] = $this->EE->config->item('site_id');
		
	}
	
	function index() {
	
		$this->EE->load->helper('cookie');
		$this->EE->load->library('table');
		$this->EE->load->library('form_validation');
		
		
		$this->EE->load->helper('cookie');
		$cookie_prefix = $this->EE->config->item('cookie_prefix');
		if(empty($cookie_prefix)) {
			$cookie_prefix = "exp_";
		}
		
		
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$this->theme_url().'css/seo_rank_tracker.css" />');
		$this->EE->cp->add_to_head('<script type="text/javascript" src="'.$this->theme_url().'javascripts/seo_rank_tracker.js"></script> ');
		
		/* Page Title & Breadcrumb */
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('seo_rank_tracker_module_name'));
/* 	 	$this->EE->cp->set_right_nav(array('Paramètres & Préférences' => $this->base_url.AMP.'method=preferences')); */
	 	
	 	/* Variables */
	 	$this->data['form_action'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=seo_rank_tracker';
	 	
	 	/* Process Add Rank Form */
	 	
	 	$this->EE->form_validation->set_rules('q', 'Q', 'required|trim');
	 	
	 	if ($this->EE->form_validation->run() == FALSE)
		{
			//echo "my form";
		}
		else
		{
			//echo "success";
			

			$q = $this->EE->input->post('q');
			$url = "/";
			$search_engine = $this->EE->input->post('search_engine');
			
			$this->rank_track_now($q, $url, $search_engine);
			
			$this->EE->functions->set_cookie('search_engine_'.$this->data['site_id'], $search_engine);
			
			$cookie = array(
				'name'   => 'search_engine_'.$this->data['site_id'],
				'value'  => $search_engine,
				'expire' => '0',
				'path'   => '/',
				'prefix' => $cookie_prefix
			);
			
			set_cookie($cookie);
			
		}
	 	

	
		/* Load ranks */
		$this->data['ranks'] = $this->get_ranks();
		
	
		/* Load View */
		$this->data['cron_url'] = $this->EE->functions->fetch_site_index(0, 0).QUERY_MARKER.'ACT='.$this->EE->cp->fetch_action_id('Seo_rank_tracker', 'tracker_cron');

		return $this->EE->load->view('index', $this->data, TRUE);
	}
	

	
	function history() {
	

    
    	$this->EE->cp->add_to_head('<!--[if IE]><script language="javascript" type="text/javascript" src="'.$this->theme_url().'javascripts/flot/excanvas.min.js"></script><![endif]--> ');
	    //$this->EE->cp->add_to_head('<script language="javascript" type="text/javascript" src="'.$this->theme_url().'javascripts/flot/jquery.js"></script> ');
	    $this->EE->cp->add_to_head('<script language="javascript" type="text/javascript" src="'.$this->theme_url().'javascripts/flot/jquery.flot.js"></script> ');
	    $this->EE->cp->add_to_head('<script language="javascript" type="text/javascript" src="'.$this->theme_url().'javascripts/flot/jquery.flot.selection.js"></script> ');

		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$this->theme_url().'css/seo_rank_tracker.css" />');
		$this->EE->cp->add_to_head('<script type="text/javascript" src="'.$this->theme_url().'javascripts/seo_rank_tracker.js"></script> ');
		$this->EE->cp->add_to_head('<script type="text/javascript" src="'.$this->theme_url().'javascripts/seo_rank_tracker.js"></script> ');
		
	
		$this->EE->load->library('table');
	
		$ranks = array();
		$rank_id = $this->EE->input->get('rank_id');
		
		$this->EE->db->where('id', $rank_id);
		$query = $this->EE->db->get('seo_rank');
		
		if ($query->num_rows() > 0)
		{
		   	$r = $query->row(); 
		   	
	
			$this->EE->db->where('keywords', $r->keywords);
			$this->EE->db->where('search_engine', $r->search_engine);
			$this->EE->db->order_by('date', 'desc');
			$query = $this->EE->db->get('seo_rank');
			
			foreach($query->result() as $rank) {


			$rank->search_engine_full = $this->data['search_engines'][$rank->search_engine][0];
		
			$rank->date_full = strftime("%Y-%m-%d à %H:%M:%S", $rank->date);
			
				array_push($ranks, $rank);
			}
		}
		
		$this->data['ranks'] = $ranks;
		$this->data['rank'] = $r;
		
		
		
		$this->EE->cp->set_variable('cp_page_title', $r->keywords);
		$this->EE->cp->set_breadcrumb($this->base_url, $this->EE->lang->line('seo_rank_tracker_module_name'));
		
		return $this->EE->load->view('history', $this->data, TRUE);
	}
	
	function delete_rank() {
		$rank_id = $this->EE->input->get('rank_id');
		$this->EE->db->where('id', $rank_id);

		$query = $this->EE->db->get('seo_rank');

		if ($query->num_rows() > 0)
		{
		   $row = $query->row(); 
			
			$this->EE->db->where('keywords', $row->keywords);
			$this->EE->db->where('search_engine', $row->search_engine);
			$this->EE->db->delete('seo_rank');

			$this->EE->functions->redirect($this->base_url);

		}
		
		return true;
	}
	

	
	function rank_track_now($keywords, $url, $search_engine) {


		$query = $this->EE->site_model->get_site_system_preferences($this->data['site_id']);

		$prefs = unserialize(base64_decode($query->row('site_system_preferences')));
	
		//$this->rank_website = $this->EE->config->item('site_url');
		$this->rank_website = $prefs['site_url'];
//		echo $this->rank_website."<br />";
		$this->rank_keyword="";
		$this->rank_url = "";
		$this->rank_search_engine = $search_engine;
		$this->rank_start=0;
		$this->rank_records=false;
		$this->rank_found = false;
		$this->rank_found_page = 0;
		$this->rank_found_rank = 0;

		$url = $url;
		
		$this->rank_url = $url;

		$this->rank_keyword = $keywords;
		
		$this->rank_spider();
		
		if($this->rank_found) {
			$this->data['rank_found'] = $this->rank_found;
			$this->data['rank_found_page'] = $this->rank_found_page;
			$this->data['rank_found_rank'] = $this->rank_found_rank;
		}
		
		$this->rank_insert();
		

		return true;
	}
	
	

	function rank_spider() {

		$i=10;
		$c=1;
		
		while($c<=10) {			

			$search_url = $this->get_search_url($this->rank_keyword, $i, $this->rank_search_engine);
		
			$records = $this->rank_parse_results($search_url); 
			
			$count=count($records);
			
			for($k=0;$k<$count;$k++){
				$j=$k+1;
				$link=$records[$k][2];
				$link = $this->rank_clean_url($link);

				if($this->rank_clean_url($this->rank_website)==$link){
					$keyword = $this->rank_keyword;
					$domain=$this->rank_website;
					$this->rank_found = true;
					$this->rank_found_page = $c;
					$this->rank_found_rank = $j;
					//echo "a";
					return;
				}			
			}
			$c++;
		}	
		
		if($this->rank_found_page==false){
			$this->rank_found = false;
			//echo "b";

		} else {
			$this->rank_found = true;
			//echo "c";

		}
			
	}
	
	
	function rank_insert() {
		$rank = ((($this->rank_found_page - 1) * 10)+$this->rank_found_rank);
		if($rank < 0) {
			$rank = 0;
		}
		$this->rank = $rank;
		$data = array(
			'keywords' => $this->rank_keyword,
			'site_id' => $this->data['site_id'],
			'url' => $this->rank_url,
			'search_engine' => $this->rank_search_engine,
			'rank' => $rank,
			'date' => time()
		);
		
		$this->EE->db->insert('seo_rank', $data);
	}
	
	function rank_clean_url($link) {
		$link=strip_tags($link);
		$link=str_replace("http://www.","",$link);
		$link=str_replace("http://","",$link);
		$link=str_replace("www.","",$link);
		$pos=strpos($link, "/");
		$link=trim(substr($link,0,$pos));
		return $link;
	}	
	
	
	private function get_search_url($keyword, $start, $search_engine){

		$keyword=trim($keyword);
		$keyword=urlencode($keyword);
		$ret = $this->data['search_engines'][$search_engine][1];
		$ret .= "search?start=".$this->rank_start."&q=$keyword";
		$this->rank_start=$this->rank_start+$start;	
		return $ret;
	}
	
	private function rank_parse_results($url){
		$matches=array();
		$pattern='/<div class="s"(.*)\<cite\>(.*)\<\/cite\>/Uis';
		$html=$this->rank_get_result_page($url);
		preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);
		return $matches;
	}
	
	private function rank_get_result_page($url){
		$returnStr="";
		$fp=fopen($url, "r") or die("ERROR: Invalid search URL");
		while (!feof($fp)) {
			$returnStr.=fgetc($fp);
		}
		fclose($fp);
		return $returnStr;
	}
	
	function get_ranks() {
		$ranks = array();
		$this->EE->db->where('site_id', $this->data['site_id']);
		$this->EE->db->order_by('date', 'desc');
		$this->EE->db->group_by(array("search_engine", "keywords"));
		$query = $this->EE->db->get('seo_rank');
		$result = $query->result();
/*
		echo "<pre>";
		var_dump($result);
		echo "</pre>";
		die();
*/
		foreach($result as $key => $rank) {
		
			$latest_rank = $this->latest_rank($rank->id);
			
			/* find latest date */
			$rank->date = $latest_rank->date;
			
			/* find latest rank */
			$rank->rank = $latest_rank->rank;
		
			$rank->search_engine_full = $this->data['search_engines'][$rank->search_engine][0];
		
			$rank->date_full = strftime("%Y-%m-%d à %H:%M:%S", $rank->date);
		
			$rank->history_link = '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=seo_rank_tracker'.AMP.'method=history'.AMP.'rank_id='.$rank->id.'">'.$rank->keywords.'</a>';
			
			$rank->delete_link = '<a rel="'.$rank->id.'" class="refresh" href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=seo_rank_tracker'.AMP.'method=rank_tracker_ajax'.AMP.'rank_id='.$rank->id.'">Rafraîchir</a><a rel="'.$rank->id.'" class="delete" href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=seo_rank_tracker'.AMP.'method=delete_rank'.AMP.'rank_id='.$rank->id.'">Supprimer</a>';
			$ranks[$key] = $rank;

		}
		
		return $ranks;
	}
	
	function rank_tracker_ajax() {
		$rank_id = $this->EE->input->get('rank_id');
		$ret_rank = array();
		
		
		$this->EE->db->where('id', $rank_id);
		$query = $this->EE->db->get('seo_rank');
		
		if ($query->num_rows() > 0)
		{
		   	$r = $query->row(); 
			$rank = $this->rank_track_now($r->keywords, $r->url, $r->search_engine);
			if($this->rank == 0) {
				$ret_rank['rank'] = "Not in top 100";
			} else {
				$ret_rank['rank'] =  $this->rank;
			}
			$ret_rank['date'] = strftime("%Y-%m-%d à %H:%M:%S", time());
		}
		
		echo json_encode($ret_rank);
		
		die();
	}
	
	function latest_rank($rank_id) {
	
		$this->EE->db->where('id', $rank_id);
		
		$query = $this->EE->db->get('seo_rank');

		if ($query->num_rows() > 0)
		{
		   $row = $query->row();
		   
		    $this->EE->db->where('keywords', $row->keywords);
		    $this->EE->db->where('search_engine', $row->search_engine);
		    $this->EE->db->order_by('date', 'desc');
		    $this->EE->db->limit(1);
		
			$query2 = $this->EE->db->get('seo_rank');
	
			if ($query2->num_rows() > 0)
			{
			   $row2 = $query2->row();
			   return $row2;
			}
		   
		}
		
		return false;
	}
	
	private function theme_url()
	{
		$url = $this->EE->config->item('theme_folder_url')."third_party/seo_rank_tracker/";
		return $url;
	}
}

?>