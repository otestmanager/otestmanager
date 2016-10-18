<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class User
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class User extends Controller
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
		$this->load->model('user_m');
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
	* Function userlist
	*
	* @return none
	*/
	public function userlist()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'user_search_status' => $this->input->post('status', TRUE),
			'user_search_group' => $this->input->post('group', TRUE),
			'user_search_searchfield' => $this->input->post('searchField', TRUE),
			'user_search_searchtext' => $this->input->post('searchText', TRUE),
			'start' => $this->input->get_post('start', TRUE),
			'limit' => $this->input->get_post('limit', TRUE)
		);
		echo $this->user_m->userlist($data);
	}

	/**
	* Function user_view
	*
	* @return none
	*/
	public function user_view()
	{
		echo $this->return_json($this->user_m->user_view());
	}

	/**
	* Function language_update
	*
	* @return none
	*/
	public function language_update()
	{
		if($this->input->post('mb_lang', TRUE)){
			$data = array(
				'mb_lang' => $this->input->post('mb_lang', TRUE)
			);

			if($this->user_m->language_update($data)){
				$this->session->set_userdata('mb_lang', $data['mb_lang']);
				return "{success:true}";
			}else{
				return "";
			}
		}else{
			return "";
		}
	}

	/**
	* Function check_form
	*
	* @param string $type Get Data
	*
	* @return none
	*/
	public function check_form($type)
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('mb_email', 'Email', 'required|valid_email'); //is_unique[user.email]
		$this->form_validation->set_rules('mb_name', 'Name', 'required|min_length[2]|max_length[20]');

		if($type !== 'update' OR $this->input->post('mb_pw')){
			$this->form_validation->set_rules('mb_pw', 'Password', 'required|min_length[6]|max_length[30]|matches[mb_pw_re]');//alpha_numeric
			$this->form_validation->set_rules('mb_pw_re', 'Password', 'required');
		}

		if($this->form_validation->run() === TRUE){
			return TRUE;
		}
		else{
			echo validation_errors();
			exit;
		}
	}

	/**
	* Function create_user
	*
	* @return none
	*/
	public function create_user()
	{
		$mb_is_admin = ($this->input->post('mb_is_admin', TRUE)==='true')?'Y':'N';
		$mb_is_approved = ($this->input->post('mb_is_approved', TRUE)==='true')?'Y':'N';

		$data = array(
			'mb_email' => $this->input->post('mb_email', TRUE),
			'mb_pw' => $this->input->post('mb_pw', TRUE),
			'mb_name' => $this->input->post('mb_name', TRUE),
			'mb_tel' => $this->input->post('mb_tel', TRUE),
			'mb_is_admin' => $mb_is_admin,
			'mb_is_approved' => $mb_is_approved,
			'mb_memo' => $this->input->post('mb_memo', TRUE)
		);

		if($this->input->post('mb_pw', TRUE)){
			if($this->check_form('mb_pw')){
				echo $this->user_m->create_user($data);
			}
		}
		else{
			echo $this->user_m->create_user($data);
		}
	}

	/**
	* Function update_user
	*
	* @return none
	*/
	public function update_user()
	{
		if($this->check_form('update')){
			$mb_is_admin = ($this->input->post('mb_is_admin', TRUE)==='true')?'Y':'N';
			$mb_is_approved = ($this->input->post('mb_is_approved', TRUE)==='true')?'Y':'N';

			$data = array(
				'mb_email' => $this->input->post('mb_email', TRUE),
				'mb_pw' => $this->input->post('mb_pw', TRUE),
				'mb_name' => $this->input->post('mb_name', TRUE),
				'mb_tel' => $this->input->post('mb_tel', TRUE),
				'mb_is_admin' => $mb_is_admin,
				'mb_is_approved' => $mb_is_approved,
				'mb_memo' => $this->input->post('mb_memo', TRUE)
			);

			echo $this->user_m->update_user($data);
		}
	}

	/**register_form
	* Function register_update
	*
	* @return none
	*/
	public function register_update()
	{
		if($this->check_form('update')){
			$data = array(
				'mb_email' => $this->input->post('mb_email', TRUE),
				'mb_pw' => $this->input->post('mb_pw', TRUE),
				'mb_name' => $this->input->post('mb_name', TRUE),
				'mb_tel' => $this->input->post('mb_tel', TRUE),
				'mb_memo' => $this->input->post('mb_memo', TRUE)
			);

			echo $this->user_m->register_update($data);
		}
	}


	/**
	* Function delete_user
	*
	* @return none
	*/
	public function delete_user()
	{
		$data = array(
			'mb_email' => $this->input->post('mb_email', TRUE),
			'mb_pw' => $this->input->post('mb_pw', TRUE)
		);

		echo $this->user_m->delete_user($data);
	}

}
//End of file User.php
//Location: ./controllers/User.php