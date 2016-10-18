<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class System_setup
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class System_setup extends Controller
{
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->view('system_setup/store');
	}

	/**
	* Function index
	*
	* @return none
	*/
	public function index()
	{

	}

	/**
	* Function user
	*
	* @return none
	*/
	public function user()
	{
		$this->load->view('system_setup/user_v');
	}

	/**
	* Function group
	*
	* @return none
	*/
	public function group()
	{
		$this->load->view('system_setup/group_v');
	}

	/**
	* Function role
	*
	* @return none
	*/
	public function role()
	{
		$this->load->view('system_setup/role_v');
	}

	/**
	* Function code
	*
	* @return none
	*/
	public function code()
	{
		$this->load->view('system_setup/code_v');
	}

	/**
	* Function workflow
	*
	* @return none
	*/
	public function workflow()
	{
		$this->load->view('system_setup/workflow_v');
	}

	/**
	* Function userform
	*
	* @return none
	*/
	public function userform()
	{
		$this->load->view('system_setup/userform_v');
	}

	/**
	* Function id_rule
	*
	* @return none
	*/
	public function id_rule()
	{
		$this->load->view('system_setup/id_rule_v');
	}

	/**
	* Function addon
	*
	* @return none
	*/
	public function addon()
	{
		$this->load->view('system_setup/addon_v');
	}

	/**
	* Function notification
	*
	* @return none
	*/
	public function notification()
	{
		$this->load->view('system_setup/notification_v');
	}

	/**
	* Function plugin
	*
	* @return none
	*/
	public function plugin()
	{
		$this->load->view('system_setup/plugin_v');
	}
}
//End of file System_setup.php
//Location: ./controllers/System_setup.php