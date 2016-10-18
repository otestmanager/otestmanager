<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Group
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Group extends Controller
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
		$this->load->model('group_m');
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
	* Function grouplist
	*
	* @return string
	*/
	public function grouplist()
	{
		$grouplist = $this->group_m->grouplist();
		echo $this->return_json($grouplist);
	}

	/**
	* Function create_group
	*
	* @return string
	*/
	public function create_group()
	{
		$data = array(
			'gr_name' => $this->input->post('gr_name', TRUE),
			'gr_content' => $this->input->post('gr_content', TRUE)
		);

		echo $this->group_m->create_group($data);
	}

	/**
	* Function update_group
	*
	* @return string
	*/
	public function update_group()
	{
		$data = array(
			'gr_seq' => $this->input->post('gr_seq', TRUE),
			'gr_name' => $this->input->post('gr_name', TRUE),
			'gr_content' => $this->input->post('gr_content', TRUE)
		);

		echo $this->group_m->update_group($data);
	}

	/**
	* Function delete_group
	*
	* @return string
	*/
	public function delete_group()
	{
		$data = array(
			'gr_seq' => $this->input->post('gr_seq', TRUE)
		);

		echo $this->group_m->delete_group($data);
	}

	/**
	* Function group_userlist
	*
	* @return string
	*/
	public function group_userlist()
	{
		$data = array(
			'gr_seq' => $this->input->post('gr_seq', TRUE)
		);

		echo $this->return_json($this->group_m->group_userlist($data));
	}

	/**
	* Function insert_group_user
	*
	* @return string
	*/
	public function insert_group_user()
	{
		$userlist = $this->input->post('userlist', TRUE);
		if($userlist !== 'all'){
			$userlist = json_decode($userlist);
		}
		$data = array(
			'gr_seq' => $this->input->post('gr_seq', TRUE),
			'userlist' => $userlist
		);

		echo $this->group_m->insert_group_user($data);
	}

	/**
	* Function delete_group_user
	*
	* @return string
	*/
	function delete_group_user()
	{
		$userlist = $this->input->post('userlist', TRUE);
		$userlist = json_decode($userlist);
		$data = array(
			'gr_seq' => $this->input->post('gr_seq', TRUE),
			'userlist' => $userlist
		);

		echo $this->group_m->delete_group_user($data);
	}
}
//End of file Group.php
//Location: ./controllers/Group.php