<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Role
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Role extends Controller
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
		$this->load->model('role_m');
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
	* Function role_list
	*
	* @return none
	*/
	public function role_list()
	{
		$rolelist = $this->role_m->role_list();
		echo $this->return_json($rolelist);
	}

	/**
	* Function create_role
	*
	* @return none
	*/
	public function create_role()
	{
		$data = array(
			'rp_name' => $this->input->post('role_name', TRUE),
			'permission' => $this->input->post('permission', TRUE)
		);

		echo $this->role_m->create_role($data);
	}

	/**
	* Function update_role
	*
	* @return none
	*/
	public function update_role()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('role_seq', 'Role Seq', 'required');
		$this->form_validation->set_rules('role_name', 'Role Name', 'required|min_length[2]|max_length[100]');

		if($this->form_validation->run() === TRUE){
			$data = array(
				'rp_seq' => $this->input->post('role_seq', TRUE),
				'rp_name' => $this->input->post('role_name', TRUE),
				'permission' => $this->input->post('permission', TRUE)
			);

			echo $this->role_m->update_role($data);
		}
		else{
			echo validation_errors();
			exit;
		}
	}

	/**
	* Function get_permission
	*
	* @return none
	*/
	public function get_permission()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('rp_seq', 'Role Seq', 'required');

		if($this->form_validation->run() === TRUE){
			$data = array(
				'rp_seq' => $this->input->post('rp_seq', TRUE)
			);
			$permission_list = $this->role_m->get_permission($data);
			echo $this->return_json($permission_list);
		}
		else{
			echo validation_errors();
			exit;
		}
	}

	/**
	* Function delete_role
	*
	* @return none
	*/
	public function delete_role()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('rp_seq', 'Role Seq', 'required');
		if($this->form_validation->run() === TRUE){
			$data = array(
				'rp_seq' => $this->input->post('rp_seq', TRUE)
			);
			echo $this->role_m->delete_role($data);

		}
		else{
			echo validation_errors();
			exit;
		}
	}

}
//End of file Role.php
//Location: ./controllers/Role.php