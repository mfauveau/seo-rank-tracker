<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Seo_rank_tracker_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
    
    function get_search_engines()
    {
		$search_engines = array(
			'google-us' => array('Google US', "http://www.google.com/"),
			'google-at' => array('Google Austria', "http://www.google.at/"),
			'google-be' => array('Google Belgium', "http://google.be/"),
			'google-ca' => array('Google Canada', "http://google.ca/"),
			'google-cn' => array('Google China', "http://google.cn/"),
			'google-cz' => array('Google Czech Republic', "http://www.google.cz/"),
			'google-dk' => array('Google Danmark', "http://google.dk/"),
			'google-fr' => array('Google France', "http://www.google.fr/"),
			'google-de' => array('Google Germany', "http://google.de/"),
			'google-nl' => array('Google Holland', "http://google.nl/"),
			'google-ie' => array('Google Ireland', "http://www.google.ie/"),
			'google-it' => array('Google Italy', "http://www.google.it/"),
			'google-jp' => array('Google Japan', "http://google.jp/"),
			'google-mx' => array('Google Mexico', "http://google.com.mx/"),
			'google-pl' => array('Google Poland', "http://google.pl/"),
			'google-pt' => array('Google Portugal', "http://google.pt/"),
			'google-es' => array('Google Spain', "http://www.google.es/"),
			'google-ch' => array('Google Switzerland', "http://google.ch/"),
			'google-uk' => array('Google United Kingdom', "http://google.co.uk/")
		);
		
		return $search_engines;
    }

}

?>