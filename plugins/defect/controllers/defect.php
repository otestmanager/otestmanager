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

class Defect extends Controller {
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('defect/defect_m');
		$this->load->library('File_Form');
		$this->load->library('History');
	}

	public function install()
	{
		$this->migration->install();
	}

	function version()
	{
		$data = $this->data;
		$version = $data['param'];
		$this->migration->check_version($version);
	}

	/**
	* Function defect_role
	*
	* @return array
	*/
	function defect_role()
	{
		$data = array(
			'pr_seq' => $this->input->post('project_seq', TRUE),
			'pc_category' => 'ID_DEFECT',
			'type' => 'view'
		);
		$this->load->model('project_setup_m');
		$defect_role = $this->project_setup_m->user_project_role($data);

		for($i=0; $i<count($defect_role); $i++){
			if($defect_role[$i]->pmi_value === '1'){
				$role[$defect_role[$i]->pmi_name] = true;
			}
		}
		return $role;
	}


	/**
	* Function defect_dashboard
	*
	* @param array $type Array Data
	*
	* @return view
	*/
	public function defect_dashboard()
	{
		$data = $this->data;
		$data['view'] = "defect_dashboard_v";
		$data['skin_dir'] = "plugins/defect/views/".$data['skin'];

		return render($data);
	}


	/**
	* Function defect
	*
	* @return string
	*/
	function defect($data = array())
	{
		$this->data = $data;

		if($data['function'] != 'install')
		{
			$check = $this->migration->_check_module('defect');
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
	* Function defect_list
	*
	* @return string
	*/
	function defect_list()
	{
		$role = $this->defect_role();

		if(isset($role['defect_view_all']) || $this->session->userdata('mb_is_admin') === 'Y'){
			$v_role = 'all';
		}else if(isset($role['defect_view'])){
			$v_role = 'writer';
		}else{
			$v_role = '';
		}
		$sort = $this->input->post('sort',true);
		$data = array(
			'project_seq' => $this->input->post('project_seq',true),
			'limit' => $this->input->post('limit',true),
			'page' => $this->input->post('page',true),
			'start' => $this->input->post('start',true),
			'search_array' => $this->input->post('search_array',true),
			'sort' => ($sort)?json_decode($sort):null,
			'role' => $v_role
		);

		return $this->defect_m->defect_list($data);
		exit;
	}


	/**
	* Function create_defect
	*
	* @return string
	*/
	function create_defect(){
		$this->load->library('form_validation');

		$this->form_validation->set_rules('project_seq','Project Seq', 'required');
		$this->form_validation->set_rules('defect_subjectForm','Defect Subject', 'required');
		$this->form_validation->set_rules('defect_descriptionForm','Defect Description', 'required');

		if($this->form_validation->run() == TRUE){
			$defect_data = array(
				'pr_seq' => $this->input->post('project_seq',true),
				'tr_seq' => $this->input->post('tr_seq',true),
				'd_subject' => $this->input->post('defect_subjectForm',true),
				'd_description' => $this->input->post('defect_descriptionForm',true),
				'd_severity' => $this->input->post('defect_severityForm',true),
				'd_priority' => $this->input->post('defect_priorityForm',true),
				'd_frequency' => $this->input->post('defect_frequencyForm',true),
				'd_status' => $this->input->post('defect_statusForm',true),
				'd_assign_member' => $this->input->post('defect_assign_member',true),
				'd_start_date' => $this->input->post('defect_start_date',true),
				'd_end_date' => $this->input->post('defect_end_date',true),
				'custom_form' => $this->input->post('custom_form',true)
			);

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

			$df_seq = $this->defect_m->create_defect($defect_data);

			if($df_seq){
				print "{success:true,data:".json_encode($df_seq)."}";
			}else{
				print "{success:false}";
			}
		}else{
			print "{success:false,msg:'".trim(strip_tags(validation_errors()))."'}";
			exit;
		}
	}

	/**
	* Function update_defect
	*
	* @return string
	*/
	function update_defect(){

		$role = $this->defect_role();

		if(isset($role['defect_edit_all']) || $this->session->userdata('mb_is_admin') === 'Y'){
			$v_role = 'all';
		}else if(isset($role['defect_edit'])){
			$v_role = 'writer';
			if($this->session->userdata('mb_email') !== $this->input->post('writer',true) && $this->session->userdata('mb_email') !== $this->input->post('defect_assign_member',true))
			{
				print "{success:false,msg:'No authority. edit defect'}";
				exit;
			}
		}else{
			$v_role = '';
			print "{success:false,msg:'No authority. edit defect'}";
			exit;
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('project_seq','Project Seq', 'required');
		$this->form_validation->set_rules('defect_seqForm','Defect Seq', 'required');
		$this->form_validation->set_rules('defect_subjectForm','Defect Subject', 'required');

		if($this->form_validation->run() == TRUE){
			$defect_data = array(
				'pr_seq' => $this->input->post('project_seq',true),
				'd_seq' => $this->input->post('defect_seqForm',true),
				'd_subject' => $this->input->post('defect_subjectForm',true),
				'd_description' => $this->input->post('defect_descriptionForm',true),
				'd_severity' => $this->input->post('defect_severityForm',true),
				'd_priority' => $this->input->post('defect_priorityForm',true),
				'd_frequency' => $this->input->post('defect_frequencyForm',true),
				'd_status' => $this->input->post('defect_statusForm',true),
				'd_assign_member' => $this->input->post('defect_assign_member',true),
				'd_start_date' => $this->input->post('defect_start_date',true),
				'd_end_date' => $this->input->post('defect_end_date',true),
				'custom_form' => $this->input->post('custom_form',true),
				'role' => $v_role
			);
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

			$df_seq = $this->defect_m->update_defect($defect_data);
			if($df_seq){
				print "{success:true}";
			}else{
				print "{success:false}";
			}
		}else{
			print "{success:false,msg:'".trim(strip_tags(validation_errors()))."'}";
			exit;
		}
	}

	/**
	* Function update_defect_dashboard
	*
	* @return string
	*/
	function update_defect_dashboard(){

		$role = $this->defect_role();

		if(isset($role['defect_edit_all']) || $this->session->userdata('mb_is_admin') === 'Y'){
			$v_role = 'all';
		}else if(isset($role['defect_edit'])){
			$v_role = 'writer';
			if($this->session->userdata('mb_email') !== $this->input->post('writer',true) && $this->session->userdata('mb_email') !== $this->input->post('defect_dashboard_assign_member',true))
			{
				print "{success:false,msg:'No authority. edit defect'}";
				exit;
			}
		}else{
			$v_role = '';
			print "{success:false,msg:'No authority. edit defect'}";
			exit;
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('project_seq','Project Seq', 'required');
		$this->form_validation->set_rules('defect_dashboard_seqForm','Defect Seq', 'required');
		$this->form_validation->set_rules('defect_dashboard_subjectForm','Defect Subject', 'required');

		if($this->form_validation->run() == TRUE){
			$defect_data = array(
				'pr_seq' => $this->input->post('project_seq',true),
				'd_seq' => $this->input->post('defect_dashboard_seqForm',true),
				'd_subject' => $this->input->post('defect_dashboard_subjectForm',true),
				'd_description' => $this->input->post('defect_dashboard_descriptionForm',true),
				'd_severity' => $this->input->post('defect_dashboard_severityForm',true),
				'd_priority' => $this->input->post('defect_dashboard_priorityForm',true),
				'd_frequency' => $this->input->post('defect_dashboard_frequencyForm',true),
				'd_status' => $this->input->post('defect_dashboard_statusForm',true),
				'd_assign_member' => $this->input->post('defect_dashboard_assign_member',true),
				'd_start_date' => $this->input->post('defect_dashboard_start_date',true),
				'd_end_date' => $this->input->post('defect_dashboard_end_date',true),
				'custom_form' => $this->input->post('custom_form',true),
				'role' => $v_role
			);
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

			$df_seq = $this->defect_m->update_defect($defect_data);
			if($df_seq){
				print "{success:true}";
			}else{
				print "{success:false}";
			}
		}else{
			print "{success:false,msg:'".trim(strip_tags(validation_errors()))."'}";
			exit;
		}
	}

	/**
	* Function delete_defect
	*
	* @return string
	*/
	function delete_defect(){

		$role = $this->defect_role();

		if(isset($role['defect_delete_all']) || $this->session->userdata('mb_is_admin') === 'Y'){
			$v_role = 'all';
		}else if(isset($role['defect_delete'])){
			$v_role = 'writer';
			if($this->session->userdata('mb_email') !== $this->input->post('writer',true))
			{
				print "No authority. delete defect";
				exit;
			}
		}else{
			$v_role = '';
			print "No authority. delete defect";
			exit;
		}

		$this->load->library('form_validation');
		$this->form_validation->set_rules('project_seq','Project Seq', 'required');

		$df_list = json_decode($this->input->post('df_list', TRUE));

		if($this->form_validation->run() == TRUE){
			$data = array(
				'project_seq' => $this->input->post('project_seq',true),
				'df_list' => $df_list
			);
			echo $this->defect_m->delete_defect($data);

		}else{
			echo validation_errors();
			exit;
		}
	}

	/**
	* Function view_defect
	*
	* @return string
	*/
	function view_defect()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('df_seq','Defect Seq', 'required');
		if($this->form_validation->run() == TRUE){
			$defect_data = array(
				'df_seq' => $this->input->post('df_seq',true),
				'pr_seq' => $this->input->post('pr_seq',true)
			);

			echo "{success:true, data:".json_encode($this->defect_m->view_defect($defect_data))."}";
		}else{
			echo validation_errors();
			exit;
		}
	}

	/**
	* Function code_list
	*
	* @return string
	*/
	function code_list()
	{
		$data = array(
			'project_seq' => $this->input->post('project_seq',true)
		);

		$code_list = $this->defect_m->code_list($data);
		return "{success:true,totalCount: ".count($code_list).", data:".json_encode($code_list)."}";

		exit;
	}

	/**
	* Function mantis connect
	*
	* @return string
	*/
	function mantis_connect()
	{
		define('MANTISCONNECT_URL', $this->config->item('mantis_url'));

		// the username/password of the user account to use for calls
		define('USERNAME', $this->config->item('mantis_id'));
		define('PASSWORD', $this->config->item('mantis_pw'));

		// ------------------------------------------------

		parse_str($_SERVER['QUERY_STRING'], $args);

		// get SOAP function name to call
		if (!isset($args['name'])) {
			die("No name specified.");
		}
		$function_name = $args['name'];

		// remove function name from arguments
		unset($args['name']);

		// prepend username/passwords to arguments
		$args = array_merge(
			array(
				'username' => USERNAME,
				'password' => PASSWORD
			),
			$args
		);

		// connect and do the SOAP call
		try {
			$client = new SoapClient(MANTISCONNECT_URL . '?wsdl');

			$result = $client->__soapCall($function_name, $args);
		} catch (SoapFault $e) {
			$result = array(
				'error' => $e->faultstring
			);
		}

		$project_seq = $this->input->post('project_seq',true);

		$str_sql = "select pco_seq,pco_name,pco_type from otm_project_code where otm_project_pr_seq='$project_seq' order by pco_seq";
		$query = $this->db->query($str_sql);
		$k = 0;
		$project_code = array();
		foreach ($query->result() as $row)
		{
			$project_code[$k] = $row;
			if($row->pco_type == 'severity'){
				$severity = $row->pco_seq;
			}
			if($row->pco_type == 'priority'){
				$priority = $row->pco_seq;
			}
			if($row->pco_type == 'frequency'){
				$frequency = $row->pco_seq;
			}
			if($row->pco_type == 'status'){
				$status = $row->pco_seq;
			}
			$k++;
		}

		for($i=0; $i<count($result); $i++)
		{
			for($j=0; $j<count($project_code); $j++)
			{
				switch($project_code[$j]->pco_type)
				{
					case "status":
						if($project_code[$j]->pco_name == $result[$i]->status->name){
							$status = $project_code[$j]->pco_seq;
						}
						break;
					case "severity":
						if($project_code[$j]->pco_name == $result[$i]->severity->name){
							$severity = $project_code[$j]->pco_seq;
						}
						break;
					case "priority":
						if($project_code[$j]->pco_name == $result[$i]->priority->name){
							$priority = $project_code[$j]->pco_seq;
						}
						break;
					case "frequency":
						if($project_code[$j]->pco_name == $result[$i]->reproducibility->name){
							$frequency = $project_code[$j]->pco_seq;
						}
						break;
				}
			}

			$defect_data = array(
				'pr_seq' => $this->input->post('project_seq',true),
				'tr_seq' => "",
				'd_subject' => $result[$i]->summary,
				'd_description' => $result[$i]->description,
				'd_severity' => $severity,
				'd_priority' => $priority,
				'd_frequency' => $frequency,
				'd_status' => $status,
				'writer' => $result[$i]->reporter->email,
				'regdate' => $result[$i]->date_submitted,
				'last_update' => $result[$i]->last_updated
			);

			$df_seq = $this->defect_m->create_defect($defect_data);
		}

		return "ok";
	}

	/**
	* Function defect_list_export
	*
	* @return string
	*/
	function defect_list_export(){
		$data = array(
			'project_seq' => $this->input->post_get('project_seq',true),
			'limit' => $this->input->post_get('limit',true),
			'page' => $this->input->post_get('page',true),
			'start' => $this->input->post_get('start',true),
			'sfl' => $this->input->post_get('sfl',true),
			'stx' => $this->input->post_get('stx',true)
		);

		$defect_list = $this->defect_m->defect_list_export($data);
		return $defect_list;
		exit;
	}


	/**
	* Function defect_dashboard_list
	*
	* @return string
	*/
	function defect_dashboard_list(){

		$sort = $this->input->post('sort',true);
		$data = array(
			'limit' => $this->input->post('limit',true),
			'page' => $this->input->post('page',true),
			'start' => $this->input->post('start',true),
			'defect_list_option' => $this->input->post('defect_list_option',true),
			'search_array' => $this->input->post('search_array',true),
			'sort' => ($sort)?json_decode($sort):null
		);

		return $this->defect_m->defect_dashboard_list($data);
	}


	/**
	* Function send_mail
	*
	* @return string
	*/
	function send_mail(){
		$data = array(
			'project_seq' => $this->input->post_get('project_seq',true),
			'user_list' => json_decode($this->input->post_get('user_list',true)),
			'defect_list' => json_decode($this->input->post_get('defect_list',true)),
			'subject' => $this->input->post_get('subject',true),
			'content' => $this->input->post_get('content',true)
		);

		$result = $this->defect_m->send_mail($data);
		return $result;
	}
}
//End of file defect.php
//Location: ./controllers/defect.php
?>