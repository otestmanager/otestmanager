<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Plugin
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Plugin extends Controller
{
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('plugin_m');
	}

	/**
	* Function return_json
	*
	* @param array $temp_arr Array Data
	*
	* @return string
	*/
	public function return_json($temp_arr)
	{
		return '{success:true,totalCount: '.count($temp_arr).', data:'.json_encode($temp_arr).'}';
	}

	/**
	* Function core_info
	*
	* @return none
	*/
	public function core_info()
	{
		echo $this->plugin_m->core_info();
	}

	/**
	* Function Plugin_list
	*
	* @return none
	*/
	public function plugin_list()
	{
		$service_info = $this->plugin_m->plugin_list();

		$migrations = $service_info['migrations'];
		$core_info = $service_info['core_info'];	
		echo '{success:true,totalCount: '.count($migrations).', data:'.json_encode($migrations).', core:'.json_encode($core_info).'}';
	}
}
//End of file Plugin.php
//Location: ./controllers/Plugin.php 