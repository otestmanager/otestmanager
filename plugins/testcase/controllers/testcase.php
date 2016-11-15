<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Testcase
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/

class Testcase extends Controller {
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('testcase/testcase_m');
		$this->load->library('File_Form');
		$this->load->library('History');
	}

	public function install()
	{
		$this->migration->install();
	}

	public function version()
	{
		$data = $this->data;
		$version = $data['param'];
		$this->migration->check_version($version);
	}

	function testcase_role()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('project_seq', TRUE),
			'pc_category' => 'ID_TC',
			'type' => 'view'
		);
		$this->load->model('project_setup_m');
		$testcase_role = $this->project_setup_m->user_project_role($data);

		for($i=0; $i<count($testcase_role); $i++){
			if($testcase_role[$i]->pmi_value === '1'){
				$role[$testcase_role[$i]->pmi_name] = true;
			}
		}
		return $role;
	}

	/**
	* Function testcase
	*
	* @return string
	*/
	function testcase($data = array())
	{
		$this->data = $data;

		if($data['function'] != 'install')
		{
			$check = $this->migration->_check_module('testcase');
			if($check === FALSE)
			{
				show_error('Module was not established. <br><br>Module Install : <a href="/index.php/Plugin_view/'.$data['module_directory'].'/install">Click</a>');
			}
		}

		$data['view'] = $data['module_directory']."_v";
		$data['skin_dir'] = "plugins/".$data['module_directory']."/views/".$data['skin'];

		if($data['function']){
			return $this->$data['function']();
			exit;
		}else{
			return render($data);
		}
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
	* Function plan_list
	*
	* @return string
	*/
	function plan_list()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE)
		);

		echo $this->return_json($this->testcase_m->plan_list($data));
	}

	/**
	* Function create_plan
	*
	* @return string
	*/
	function create_plan()
	{
		$data = array(
			'project_seq' => $this->input->post('project_seq', TRUE),
			'tp_subject' => $this->input->post('tp_subject', TRUE),
			'tp_description' => $this->input->post('tp_description', TRUE),
			'tp_startdate' => $this->input->post('tp_startdate', TRUE),
			'tp_enddate' => $this->input->post('tp_enddate', TRUE),
			'tp_status' => $this->input->post('tp_status', TRUE)
		);

		echo $this->testcase_m->create_plan($data);
	}

	/**
	* Function update_plan
	*
	* @return string
	*/
	function update_plan()
	{
		$data = array(
			'tp_seq' => $this->input->post('tp_seq', TRUE),
			'tp_subject' => $this->input->post('tp_subject', TRUE),
			'tp_description' => $this->input->post('tp_description', TRUE),
			'tp_startdate' => $this->input->post('tp_startdate', TRUE),
			'tp_enddate' => $this->input->post('tp_enddate', TRUE),
			'tp_status' => $this->input->post('tp_status', TRUE)
		);

		echo $this->testcase_m->update_plan($data);
	}

	/**
	* Function delete_plan
	*
	* @return string
	*/
	function delete_plan()
	{
		$data = array(
			'tp_seq' => $this->input->post('tp_seq', TRUE)
		);

		echo $this->testcase_m->delete_plan($data);
	}


	/**
	* Function testcase_tree_list
	*
	* @return string
	*/
	function testcase_tree_list()
	{
		$role = $this->testcase_role();

		if(isset($role['tc_view_all']) || $this->session->userdata('mb_is_admin') === 'Y'){
			$v_role = 'all';
		}else if(isset($role['tc_view'])){
			$v_role = 'writer';
		}else{
			$v_role = '';
		}

		$data = array(
			'project_seq' => $this->input->post_get('project_seq', TRUE),
			'tcplan' => $this->input->post_get('tcplan', TRUE),
			'node' => $this->input->post_get('node', TRUE),
			'role' => $v_role
		);

		echo json_encode($this->testcase_m->testcase_tree_list($data));
	}

	/**
	* Function create_testcase
	*
	* @return json
	*/
	public function create_testcase()
	{
		if(isset($_FILES['form_file']))
		{
			$return_msg = $this->File_Form->file_chk('');
			switch($return_msg){
				case "max_size_over":
					print "{success:false,msg:'File Size Over (Max : 20M)'}";
					exit;
				break;
				case "not_support_extension":
				break;
			}
		}

		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'tp_seq' => $this->input->post('tp_seq', TRUE),
			'tc_seq' => $this->input->post('tc_seq', TRUE),
			'tl_seq' => $this->input->post('tl_seq', TRUE),
			'type' => $this->input->post('type', TRUE),
			'pid' => $this->input->post('pid', TRUE),
			'tc_out_id' => $this->input->post('out_id', TRUE),
			'tc_subject' => $this->input->post('tc_subject', TRUE),
			'tc_precondition' => $this->input->post('tc_precondition', TRUE),
			'tc_testdata' => $this->input->post('tc_testdata', TRUE),
			'tc_procedure' => $this->input->post('tc_procedure', TRUE),
			'tc_expected_result' => $this->input->post('tc_expected_result', TRUE),
			'tc_description' => $this->input->post('tc_description', TRUE),
			'custom_form' => $this->input->post('custom_form',true)
		);

		echo $this->testcase_m->create_testcase($data);
	}

	/**
	* Function import_testcase_csv
	*
	* @return json
	*/
	public function import_testcase_csv()
	{
		$filename = $_FILES['form_file']['name'];

		if(substr(trim($filename),-3) != 'csv'){
			echo "{success:false, msg:\"File type is not correct.\"}";
			exit;
		}else{
			$data = array(
				'pr_seq' => $this->input->post('project_seq', TRUE)
			);
			echo $this->testcase_m->import_testcase_csv($data);
		}

		exit;
	}

	/**
	* Function update_testcase
	*
	* @return json
	*/
	public function update_testcase()
	{
		$role = $this->testcase_role();

		if(isset($_FILES['form_file']))
		{
			$return_msg = $this->File_Form->file_chk('');
			switch($return_msg){
				case "max_size_over":
					print "{success:false,msg:'File Size Over (Max : 20M)'}";
					exit;
				break;
				case "not_support_extension":
				break;
			}
		}


		if(isset($role['tc_edit_all']) || $this->session->userdata('mb_is_admin') === 'Y'){
			//$v_role = 'all';
		}else if(isset($role['tc_edit'])){
			//$v_role = 'writer';
			if($this->session->userdata('mb_email') !== $this->input->post('writer',true))
			{
				print "{success:false,msg:'No authority. edit testcase'}";
				exit;
			}
		}else{
			//$v_role = '';
			print "{success:false,msg:'No authority. edit testcase'}";
			exit;
		}

		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'tp_seq' => $this->input->post('tp_seq', TRUE),
			'tc_seq' => $this->input->post('tc_seq', TRUE),
			'tl_seq' => $this->input->post('tl_seq', TRUE),
			'type' => $this->input->post('type', TRUE),
			'pid' => $this->input->post('pid', TRUE),
			'tc_out_id' => $this->input->post('out_id', TRUE),
			'tc_subject' => $this->input->post('tc_subject', TRUE),
			'tc_precondition' => $this->input->post('tc_precondition', TRUE),
			'tc_testdata' => $this->input->post('tc_testdata', TRUE),
			'tc_procedure' => $this->input->post('tc_procedure', TRUE),
			'tc_expected_result' => $this->input->post('tc_expected_result', TRUE),
			'tc_description' => $this->input->post('tc_description', TRUE),
			'custom_form' => $this->input->post('custom_form',true)
		);

		echo $this->testcase_m->update_testcase($data);
	}


	/**
	* Function delete_testcase
	*
	* @return json
	*/
	public function delete_testcase()
	{
		$list = json_decode($this->input->post('list', TRUE));

		$role = $this->testcase_role();

		if(isset($role['tc_delete_all']) || $this->session->userdata('mb_is_admin') === 'Y'){
			//$v_role = 'all';
		}else if(isset($role['tc_delete'])){
			//$v_role = 'writer';

			$writer = json_decode($this->input->post('writer', TRUE));
			$return_list = '';
			for($i=0; $i<count($writer); $i++){
				if(isset($writer[$i]) && ($this->session->userdata('mb_email') !== $writer[$i]))
				{
					$return_list .= $list[$i].',';

				}
			}
			if($return_list !== ''){
				print "{success:false,msg:'No authority. delete testcase (".$return_list.")'}";
				exit;
			}
		}else{
			//$v_role = '';
			print "{success:false,msg:'No authority. delete testcase.'}";
			exit;
		}

		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'tp_seq' => $this->input->post('tp_seq', TRUE),
			'list' => json_decode($this->input->post('list', TRUE))
		);

		echo $this->testcase_m->delete_testcase($data);
	}

	/**
	* Function move_testcase
	*
	* @return json
	*/
	public function move_testcase()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'target_id' => $this->input->post('target_id', TRUE),
			'position' => $this->input->post('position', TRUE),
			'target_type' => $this->input->post('target_type', TRUE),
			'select_id' => json_decode($this->input->post('select_id', TRUE))
		);

		echo $this->testcase_m->move_testcase($data);
	}

	/**
	* Function get_testcase_info
	*
	* @return json
	*/
	public function get_testcase_info()
	{
		$data = array(
			'id' => $this->input->post('id', TRUE),
			'tl_seq' => $this->input->post('tl_seq', TRUE),
			'action_type' => $this->input->post('action_type', TRUE),
			'pr_seq' => $this->input->post('pr_seq', TRUE)
		);

		$info_data = $this->testcase_m->get_testcase_info($data);
		echo $this->return_json($info_data);
	}

	/**
	* Function get_project_code_tc_result
	*
	* @return json
	*/
	public function get_project_code_tc_result()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE)
		);

		$list = $this->testcase_m->get_project_code_tc_result($data);
		echo $this->return_json($list);
	}

	/**
	* Function create_execute_result
	*
	* @return json
	*/
	public function create_execute_result()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('tl_seq','Testcase seq', 'required');

		if($this->form_validation->run() == TRUE){
			$data = array(
				'tl_seq' => $this->input->post('tl_seq', TRUE),
				'result_value' => $this->input->post('result_value', TRUE),
				'result_text' => $this->input->post('result_text', TRUE),
				'content' => $this->input->post('content', TRUE)
			);

			echo $this->testcase_m->create_execute_result($data);
		}else{
			print "{success:false,msg:'".trim(strip_tags(validation_errors()))."'}";
			exit;
		}
	}

	/**
	* Function assign_testcase
	*
	* @return json
	*/
	public function assign_testcase()
	{
		$data = array(
			'tp_seq' => $this->input->post('tp_seq', TRUE),
			'deadline_date' => $this->input->post('deadline_date', TRUE),
			'assign_to' => $this->input->post('assign_to', TRUE),
			'tl_seq_list' => json_decode($this->input->post('tl_seq_list', TRUE))
		);

		echo $this->testcase_m->assign_testcase($data);
	}


	/**
	* Function create_testcase_link
	*
	* @return json
	*/
	public function create_testcase_link()
	{
		$data = array(
			'tp_seq' => $this->input->post('tp_seq', TRUE),
			'pid' => $this->input->post('pid', TRUE),
			'tc_seq_list' => json_decode($this->input->post('tc_seq_list', TRUE)),
			'tc_id_list' => json_decode($this->input->post('tc_id_list', TRUE))
		);

		echo $this->testcase_m->create_testcase_link($data);
	}

	/**
	* Function move_testcase_target
	*
	* @return json
	*/
	public function move_testcase_target()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'tc_plan' => $this->input->post('tc_plan', TRUE),
			'target_tc_seq' => $this->input->post('target_tc_seq', TRUE),
			'target_id' => $this->input->post('target_id', TRUE),
			'target_type' => $this->input->post('target_type', TRUE),
			'position' => $this->input->post('position', TRUE),
			'select_id' => json_decode($this->input->post('select_id', TRUE))
		);

		echo $this->testcase_m->move_testcase_target($data);
	}

	/**
	* Function testcase_list_export
	*
	* @return string
	*/
	function testcase_list_export(){
		$data = array(
			'pr_seq' => $this->input->post_get('project_seq',true),
			'tcplan' => $this->input->post_get('tcplan',true),
			'limit' => $this->input->post_get('limit',true),
			'page' => $this->input->post_get('page',true),
			'start' => $this->input->post_get('start',true),
			'sfl' => $this->input->post_get('sfl',true),
			'stx' => $this->input->post_get('stx',true)
		);

		$testcase_list = $this->testcase_m->testcase_list_export($data);
		return $testcase_list;
		exit;
	}

	/**
	* Function copy_comtestcase
	*
	* @return string
	*/
	function copy_comtestcase(){
		$data = array(
			'project_seq' => $this->input->post_get('project_seq',true),
			'p_seq' => $this->input->post_get('p_seq',true),
			'v_seq' => $this->input->post_get('v_seq',true)
		);

		$return_value = $this->testcase_m->copy_comtestcase($data);
		return $return_value;
		exit;
	}

	/**
	* Function copy_comtestcase
	*
	* @return string
	*/
	function input_item_list(){
		$data = array(
			'pr_seq' => $this->input->post_get('project_seq',true)
		);

		$return_value = $this->testcase_m->input_item_list($data);
		print $return_value;
		exit;
	}
}
//End of file testcase.php
//Location: ./controllers/testcase.php
?>