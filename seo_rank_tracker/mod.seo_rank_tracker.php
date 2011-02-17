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

class Seo_rank_tracker {
	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function Seo_rank_tracker()
	{
		$this->EE =& get_instance();
		
		$out = "";
		$this->return_data = $out;
		
		
		$this->data['search_engines'] = array(
			'google-fr' => array('Google France', "http://www.google.fr/"),
			'google-us' => array('Google US', "http://www.google.com/"),
			'google-es' => array('Google Espagne', "http://www.google.es/"),
			'google-it' => array('Google Italie', "http://www.google.it/"),
		);
	}
	
	
	function tracker_cron() {
		/* lancer un rafraichissement complet */

		$ranks = array();

		$this->EE->db->order_by('date', 'desc');
		$this->EE->db->group_by(array("search_engine", "keywords"));
		$query = $this->EE->db->get('seo_rank');
		
		if($query->num_rows() > 0) {
		
			$result = $query->result();
			foreach($result as $r) {
				
				$this->rank_track_now($r->keywords, $r->url, $r->search_engine);
			}
		}



		die();
	}
	

	
	function rank_track_now($keywords, $url, $search_engine) {
	
		$this->rank_website = $this->EE->config->item('site_url');
		$this->rank_website = "http://dukt.fr/";
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
			'url' => $this->rank_url,
			'search_engine' => $this->rank_search_engine,
			'rank' => $rank,
			'date' => time()
		);
		
		$this->EE->db->insert('seo_rank', $data);
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
	
	function rank_clean_url($link) {
		$link=strip_tags($link);
		$link=str_replace("http://www.","",$link);
		$link=str_replace("http://","",$link);
		$link=str_replace("www.","",$link);
		$pos=strpos($link, "/");
		$link=trim(substr($link,0,$pos));
		return $link;
	}	

}

/* End of file mod.downloader.php */
/* Location: ./system/expressionengine/third_party/downloader/mod.downloader.php */