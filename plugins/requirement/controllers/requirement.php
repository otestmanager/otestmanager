<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Defect
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/

class Requirement extends Controller {
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('requirement/requirement_m');
		$this->load->library('File_Form');
		$this->load->library('History');
	}

	public function install()
	{
		return $this->migration->install();
	}

	function version()
	{
		$data = $this->data;
		$version = $data['param'];
		$this->migration->check_version($version);
	}
	function requirement($data = array())
	{
		$this->data = $data;

		if($data['function'] != 'install')
		{
			$check = $this->migration->_check_module('storage');
			if($check === FALSE)
			{
				//show_error('Module was not established. <br><br>Module Install : <a href="/index.php/Plugin_view/'.$data['module_directory'].'/install">Click</a>');
			}
		}

		$data['view'] = $data['module_directory']."_v";
		$data['skin_dir'] = "plugins/".$data['module_directory']."/views/".$data['skin'];

		if($data['function']){
			return $this->$data['function']();
			exit;
		}else{
			$data['mb_lang'] = $this->session->userdata('mb_lang');
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
	* Function requirement_list
	*
	* @return json
	*/
	function requirement_list()
	{

		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'limit' => $this->input->post('limit',true),
			'page' => $this->input->post('page',true),
			'start' => $this->input->post('start',true)
		);

		print $this->requirement_m->requirement_list($data);
		exit;
		
		//$requirement_list = $this->requirement_m->requirement_list($data);
		//return $this->return_json($requirement_list);


		//return '{success:true,totalCount:3, data:[{"req_subject":"aaa","req_id":"111","req_status":"aaa","req_riskarea":"aaa","req_creator":"aaa","req_date":"2016-08-01"},{"req_subject":"bbb","req_id":"222","req_status":"bbb","req_riskarea":"bbb","req_creator":"bbb","req_date":"2016-08-01"},{"req_subject":"ccc","req_id":"333","req_status":"ccc","req_riskarea":"ccc","req_creator":"ccc","req_date":"2016-08-01"}]}';
	}


	/**
	* Function create_requirement
	*
	* @return json
	*/
	function create_requirement()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('pr_seq','Project Seq', 'required');
		$this->form_validation->set_rules('req_subjectForm','Requirement Subject', 'required');

		if($this->form_validation->run() == TRUE){
			$req_data = array(
				'pr_seq' => $this->input->post('pr_seq',true),
				'req_seq' => $this->input->post('req_seqForm',true),
				'req_subject' => $this->input->post('req_subjectForm',true),
				'req_description' => $this->input->post('req_descriptionForm',true),
				'req_priority' => $this->input->post('req_priorityForm',true),
				'req_difficulty' => $this->input->post('req_difficultyForm',true),
				'req_accept' => $this->input->post('req_acceptForm',true),
				'custom_form' => $this->input->post('custom_form',true)
			);

			if(isset($_FILES['form_file']))
			{
				$return_msg = $this->File_Form->file_chk('');
				switch($return_msg){
					case "max_size_over":
						return "{success:false,msg:'File Size Over (Max : 20M)'}";
						exit;
					break;
					case "not_support_extension":
					break;
				}
			}

			$req_seq = $this->requirement_m->create_requirement($req_data);

			if($req_seq){
				return "{success:true,data:".json_encode($req_seq)."}";
			}else{
				return "{success:false}";
			}
		}else{
			return "{success:false,msg:'".trim(strip_tags(validation_errors()))."'}";
			exit;
		}
	}


	/**
	* Function update_requirement
	*
	* @return json
	*/
	function update_requirement()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('pr_seq','Project Seq', 'required');
		$this->form_validation->set_rules('req_subjectForm','Requirement Subject', 'required');

		if($this->form_validation->run() == TRUE){
			$req_data = array(
				'pr_seq' => $this->input->post('pr_seq',true),
				'req_seq' => $this->input->post('req_seqForm',true),
				'req_subject' => $this->input->post('req_subjectForm',true),
				'req_description' => $this->input->post('req_descriptionForm',true),
				'req_priority' => $this->input->post('req_priorityForm',true),
				'req_difficulty' => $this->input->post('req_difficultyForm',true),
				'req_accept' => $this->input->post('req_acceptForm',true),
				'custom_form' => $this->input->post('custom_form',true)
			);

			if(isset($_FILES['form_file']))
			{
				$return_msg = $this->File_Form->file_chk('');
				switch($return_msg){
					case "max_size_over":
						return "{success:false,msg:'File Size Over (Max : 20M)'}";
						exit;
					break;
					case "not_support_extension":
					break;
				}
			}

			$req_seq = $this->requirement_m->update_requirement($req_data);

			if($req_seq){
				return "{success:true,data:".json_encode($req_seq)."}";
			}else{
				return "{success:false}";
			}
		}else{
			return "{success:false,msg:'".trim(strip_tags(validation_errors()))."'}";
			exit;
		}	
	}
	
	
	/**
	* Function delete_requirement
	*
	* @return bool
	*/
	function delete_requirement()
	{
		$req_list = json_decode($this->input->post('req_list', TRUE));
		$data = array(
			'pr_seq' => $this->input->post('pr_seq',true),
			'req_list' => $req_list
		);

		return $this->requirement_m->delete_requirement($data);
	}


	/**
	* Function assign_requirement
	*
	* @return string
	*/
	public function assign_requirement()
	{
		$data = array(
			'assign_to' => $this->input->post('assign_to', TRUE),
			'req_list' => json_decode($this->input->post('req_list', TRUE))
		);

		return $this->requirement_m->assign_requirement($data);
	}


	/**
	* Function requirement_info
	*
	* @return json
	*/
	function requirement_info()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'req_seq' => $this->input->post('req_seq', TRUE),
		);
		
		$info_data = $this->requirement_m->requirement_info($data);
		return $this->return_json($info_data);
	}


	/**
	* Function get_requirement_info
	*
	* @return json
	*/
	function get_requirement_info()
	{
		$data = array(
			'seq' => $this->input->post('seq', TRUE),
			'action_type' => $this->input->post('action_type', TRUE),
			'pr_seq' => $this->input->post('pr_seq', TRUE)
		);
		
		$info_data = $this->requirement_m->get_requirement_info($data);
		return $this->return_json($info_data);
	}


	/**
	* Function export
	*
	* @return string
	*/
	function export(){
		$data = array(
			'project_seq' => $this->input->post_get('project_seq',true),
			'limit' => $this->input->post_get('limit',true),
			'page' => $this->input->post_get('page',true),
			'start' => $this->input->post_get('start',true)
		);

		$list = $this->requirement_m->export($data);
		return $list;
		exit;
	}
	
}
//End of file requirement.php
//Location: ./controllers/requirement.php
?>