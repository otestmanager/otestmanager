<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Project_setup
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Project_setup extends Controller
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
		$this->load->model('project_setup_m');
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
	*	===============================================
	*				Load View Page
	*	===============================================
	*/

	/**
	* Function project_setup_main
	*
	* @return string
	*/
	public function project_setup_main()
	{
		$this->load->view('project_setup/project_setup_v');
	}

	/**
	* Function project_setup_user
	*
	* @return string
	*/
	public function project_setup_user()
	{
		$this->load->view('project_setup/project_setup_user_v');
	}

	/**
	* Function project_setup_id_rule
	*
	* @return string
	*/
	public function project_setup_id_rule()
	{
		$this->load->view('project_setup/project_setup_id_rule_v');
	}

	/**
	* Function project_setup_code
	*
	* @return string
	*/
	public function project_setup_code()
	{
		$this->load->view('project_setup/project_setup_code_v');
	}

	/**
	* Function project_setup_workflow
	*
	* @return string
	*/
	public function project_setup_workflow()
	{
		if($this->input->post('project_seq', TRUE)){
			$data['project_seq'] = $this->input->post('project_seq', TRUE);
		}
		$this->load->view('project_setup/project_setup_workflow_v',$data);
	}

	/**
	* Function project_setup_userform
	*
	* @return string
	*/
	public function project_setup_userform()
	{
		$this->load->view('project_setup/project_setup_userform_v');
	}

	/**
	* Function project_setup_notification
	*
	* @return string
	*/
	public function project_setup_notification()
	{
		$this->load->view('project_setup/project_setup_notification_v');
	}

	/**
	*	===============================================
	*				Load View Page END
	*	===============================================
	*/


	/**
		Project Info
	*/
	/**
	* Function project_info
	*
	* @return string
	*/
	public function project_info()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE)
		);

		echo $this->return_json($this->project_setup_m->project_info($data));
	}

	/**
		Project Update
	*/
	/**
	* Function project_update
	*
	* @return string
	*/
	public function project_update()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'pr_name' => $this->input->post('pr_name', TRUE),
			'pr_startdate' => $this->input->post('pr_startdate', TRUE),
			'pr_enddate' => $this->input->post('pr_enddate', TRUE),
			'pr_description' => $this->input->post('pr_description', TRUE),
			'userfrom_list' => json_decode($this->input->post('userfrom_list', TRUE)),
			'code_list' => json_decode($this->input->post('code_list', TRUE))
		);

		echo $this->return_json($this->project_setup_m->project_update($data));
	}

	/**
		Project Setup : User
	*/
	/**
	* Function project_userlist
	*
	* @return string
	*/
	public function project_userlist()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE)
		);

		echo $this->return_json($this->project_setup_m->project_userlist($data));
	}

	/**
	* Function create_user
	*
	* @return string
	*/
	public function create_user()
	{
		$userlist = json_decode($this->input->post('userlist', TRUE));
		$rolelist = json_decode($this->input->post('rolelist', TRUE));
		$data = array(
			'pr_seq'	=> $this->input->post('pr_seq', TRUE),
			'userlist'	=> $userlist,
			'rolelist'	=> $rolelist
		);

		echo $this->project_setup_m->create_user($data);
	}

	/**
	* Function update_user
	*
	* @return string
	*/
	public function update_user()
	{
		$userlist = json_decode($this->input->post('userlist', TRUE));
		$rolelist = json_decode($this->input->post('rolelist', TRUE));
		$data = array(
			'pm_seq'	=> $this->input->post('pm_seq', TRUE),
			'pr_seq'	=> $this->input->post('pr_seq', TRUE),
			'userlist'	=> $userlist,
			'rolelist'	=> $rolelist
		);

		echo $this->project_setup_m->update_user($data);
	}

	/**
	* Function delete_user
	*
	* @return string
	*/
	public function delete_user()
	{
		$data = array(
			'pm_seq'	=> $this->input->post('pm_seq', TRUE)
		);

		echo $this->project_setup_m->delete_user($data);
	}

	/**
		Project Setup : UserForm
	*/
	/**
	* Function userform_list
	*
	* @return string
	*/
	public function userform_list()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq',TRUE),
			'pc_category' => $this->input->post('pc_category',TRUE),
			'pc_is_use'	=>	$this->input->post('pc_is_use',TRUE)
		);

		echo $this->return_json($this->project_setup_m->userform_list($data));
	}

	/**
	* Function create_userform
	*
	* @return string
	*/
	public function create_userform()
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$pc_is_required = ($this->input->post('pc_is_required', TRUE)==='true')?'Y':'N';
		$pc_is_display = ($this->input->post('pc_is_display', TRUE)==='true')?'Y':'N';

		$is_use = $this->input->post('pc_is_use', TRUE);
		if(isset($is_use)){
			$pc_is_use = ($this->input->post('pc_is_use', TRUE)==='true')?'Y':'N';
		}else{
			$pc_is_use = 'Y';
		}

		$data = array(
			'otm_project_pr_seq' => $this->input->post('otm_project_pr_seq', TRUE),
			'pc_category' => $this->input->post('pc_category', TRUE),
			'pc_formtype' => $this->input->post('pc_formtype', TRUE),
			'pc_name' => $this->input->post('pc_name', TRUE),
			'pc_is_required' => $pc_is_required,
			'pc_is_display' => $pc_is_display,
			'pc_is_use' => $pc_is_use,
			'pc_default_value' => $this->input->post('pc_default_value', TRUE),
			'pc_content' => $this->input->post('pc_content', TRUE),
			'writer' => $writer,
			'regdate' => $date,
			'last_writer' => '',
			'last_update' => ''
		);

		echo $this->project_setup_m->create_userform($data);
	}

	/**
	* Function update_userform
	*
	* @return string
	*/
	public function update_userform()
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$pc_is_required = ($this->input->post('pc_is_required', TRUE)==='true')?'Y':'N';
		$pc_is_display = ($this->input->post('pc_is_display', TRUE)==='true')?'Y':'N';

		$is_use = $this->input->post('pc_is_use', TRUE);
		if(isset($is_use)){
			$pc_is_use = ($this->input->post('pc_is_use', TRUE)==='true')?'Y':'N';
		}else{
			$pc_is_use = 'Y';
		}

		$data = array(
			'pc_seq' => $this->input->post('pc_seq', TRUE),
			'pc_category' => $this->input->post('pc_category', TRUE),
			'pc_formtype' => $this->input->post('pc_formtype', TRUE),
			'pc_name' => $this->input->post('pc_name', TRUE),
			'pc_is_required' => $pc_is_required,
			'pc_is_display' => $pc_is_display,
			'pc_is_use' => $pc_is_use,
			'pc_default_value' => $this->input->post('pc_default_value', TRUE),
			'pc_content' => $this->input->post('pc_content', TRUE),
			'last_writer' => $writer,
			'last_update' => $date
		);

		echo $this->project_setup_m->update_userform($data);
	}

	/**
	* Function delete_userform
	*
	* @return string
	*/
	public function delete_userform()
	{
		$pc_list = $this->input->post('pc_list', TRUE);
		$pc_list = json_decode($pc_list);
		$data = array(
			'pc_list' => $pc_list
		);

		echo $this->project_setup_m->delete_userform($data);
	}

	/**
	* Function option_list
	*
	* @return string
	*/
	public function option_list()
	{
		if($this->input->post('pc_seq', TRUE)){
			$data = array(
				'pc_seq' => $this->input->post('pc_seq', TRUE),
			);

			$option_data = $this->project_setup_m->option_list($data);
			$temp = json_decode($option_data);
			echo $this->return_json($temp);
		}
		else{
			echo '{success:true,totalCount: 0, data:[]}';
		}
	}

	/**
	* Function update_sort_userlist
	*
	* @return none
	*/
	public function update_sort_list()
	{
		$data = array(
			'userform_list' => $this->input->post('userform_list', TRUE)
		);

		print $this->project_setup_m->update_sort_list($data);
	}


	/**
		Project Setup : Code
	*/
	/**
	* Function code_list
	*
	* @param array $type Array Data
	*
	* @return string
	*/
	public function code_list($type)
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'type' => $type
		);
		$list = $this->project_setup_m->code_list($data);
		echo $this->return_json($list);
	}

	/**
	* Function create_code
	*
	* @return string
	*/
	public function create_code()
	{
		$pco_is_required = ($this->input->post('pco_is_required', TRUE)==='true')?'Y':'N';
		$pco_is_default = ($this->input->post('pco_is_default', TRUE)==='true')?'Y':'N';
		$data = array(
			'otm_project_pr_seq' => $this->input->post('pr_seq', TRUE),
			'pco_type' => $this->input->post('pco_type', TRUE),
			'pco_name' => $this->input->post('pco_name', TRUE),
			'pco_is_required' => $pco_is_required,
			'pco_is_default' => $pco_is_default
		);

		echo $this->project_setup_m->create_code($data);
	}

	/**
	* Function update_code
	*
	* @return string
	*/
	public function update_code()
	{
		$pco_is_required = ($this->input->post('pco_is_required', TRUE)==='true')?'Y':'N';
		$pco_is_default = ($this->input->post('pco_is_default', TRUE)==='true')?'Y':'N';
		$data = array(
			'otm_project_pr_seq' => $this->input->post('pr_seq', TRUE),
			'pco_seq' => $this->input->post('pco_seq', TRUE),
			'pco_type' => $this->input->post('pco_type', TRUE),
			'pco_name' => $this->input->post('pco_name', TRUE),
			'pco_is_required' => $pco_is_required,
			'pco_is_default' => $pco_is_default
		);

		echo $this->project_setup_m->update_code($data);
	}

	/**
	* Function update_sort_code
	*
	* @return string
	*/
	public function update_sort_code()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'pco_type' => $this->input->post('pco_type', TRUE),
			'pco_list' => $this->input->post('pco_list', TRUE)
		);

		print $this->project_setup_m->update_sort_code($data);
	}

	/**
	* Function delete_code
	*
	* @return string
	*/
	public function delete_code()
	{
		$pco_list = $this->input->post('pco_list', TRUE);
		$pco_list = json_decode($pco_list);
		$data = array(
			'otm_project_pr_seq' => $this->input->post('pr_seq', TRUE),
			'pco_list' => $pco_list
		);

		echo $this->project_setup_m->delete_code($data);
	}

	/**
	* Function code_list_workflow
	*
	* @param array $type Post Data
	*
	* @return string
	*/
	public function code_list_workflow($type)
	{
		$data = array(
			'type' => 'status',
			'project_seq' => $this->input->post('project_seq', TRUE),
			'rp_seq' => $this->input->post('rp_seq', TRUE)
		);

		$value_info = $this->project_setup_m->code_list_workflow_valuefield($data);
		$list = $this->project_setup_m->code_list_workflow($data, $value_info);

		echo '{success:true,totalCount: '.count($list).', head:'.json_encode($value_info).', data:'.json_encode($list).'}';
	}

	/**
	* Function update_workflow
	*
	* @return string
	*/
	public function update_workflow()
	{
		$data = array(
			//'project_seq' => $this->input->post('project_seq', TRUE),
			'workflow_data' => $this->input->post('workflow_data', TRUE)
		);

		print $this->project_setup_m->update_workflow($data);
	}

	/**
	* Function user_project_role
	*
	* @return string
	*/
	public function user_project_role()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'pc_category' => $this->input->post('pc_category', TRUE)
		);

		$list = $this->project_setup_m->user_project_role($data);
		$defect_workflow = $this->project_setup_m->project_defect_workflow($data);

		echo '{success:true,totalCount:'.count($list).',data:'.json_encode($list).',defect_workflow:'.json_encode($defect_workflow).'}';
	}

	/**
	* Function system_setup_tree_list
	*
	* @return string
	*/
	public function system_setup_tree_list()
	{
		$data = array(
			'node' => $this->input->post('node', TRUE)
		);
		$list = $this->project_setup_m->system_setup_tree_list($data);

		echo '{success:true, data:'.json_encode($list).'}';
	}


	/**
	========================================
		Project ID Rule
	========================================
	*/

	/**
	* Function id_rule_list
	*
	* @param array $type Post Data
	*
	* @return string
	*/
	public function id_rule_list($type)
	{
		$data = array(
			'type' => $type,
			'xtype' => $this->input->post('xtype', TRUE),
			'pr_seq' => $this->input->post('pr_seq', TRUE)
		);
		$list = $this->project_setup_m->id_rule_list($data);
		echo $this->return_json($list);
	}

	/**
	* Function check_id_rule
	*
	* @param array $type Post Data
	*
	* @return string
	*/
	public function check_id_rule()
	{
		$pco_is_required = ($this->input->post('pco_is_required', TRUE)==='true')?'Y':'N';
		$pco_is_default = ($this->input->post('pco_is_default', TRUE)==='true')?'Y':'N';

		$data = array(
			'action_type' => $this->input->post('action_type', TRUE),
			'otm_project_pr_seq' => $this->input->post('pr_seq', TRUE),
			'pco_seq' => $this->input->post('pco_seq', TRUE),
			'pco_type' => $this->input->post('pco_type', TRUE),
			'pco_name' => $this->input->post('pco_name', TRUE),
			'pco_is_required' => $pco_is_required,
			'pco_is_default' => $pco_is_default,
			'pco_position' => $this->input->post('pco_position',TRUE),
			'pco_default_value' => $this->input->post('pco_default_value',TRUE),
			'pco_color' => $this->input->post('pco_color',TRUE),
			'check' => true
		);

		$list = $this->project_setup_m->check_id_rule($data);
		echo $this->return_json($list);
	}


	/**
	* Function create_id_rule
	*
	* @return string
	*/
	public function create_id_rule()
	{
		$pco_is_required = ($this->input->post('pco_is_required', TRUE)==='true')?'Y':'N';
		$pco_is_default = ($this->input->post('pco_is_default', TRUE)==='true')?'Y':'N';
		$data = array(
			'otm_project_pr_seq' => $this->input->post('pr_seq', TRUE),
			'pco_type' => $this->input->post('pco_type', TRUE),
			'pco_name' => $this->input->post('pco_name', TRUE),
			'pco_is_required' => $pco_is_required,
			'pco_is_default' => $pco_is_default,
			'pco_position' => $this->input->post('pco_position',TRUE),
			'pco_default_value' => $this->input->post('pco_default_value',TRUE),
			'pco_color' => $this->input->post('pco_color',TRUE)
		);

		echo $this->project_setup_m->create_id_rule($data);
	}

	/**
	* Function update_id_rule
	*
	* @return string
	*/
	public function update_id_rule()
	{
		$pco_is_required = ($this->input->post('pco_is_required', TRUE)==='true')?'Y':'N';
		$pco_is_default = ($this->input->post('pco_is_default', TRUE)==='true')?'Y':'N';
		$data = array(
			'action_type' => $this->input->post('action_type', TRUE),
			'otm_project_pr_seq' => $this->input->post('pr_seq', TRUE),
			'pco_seq' => $this->input->post('pco_seq', TRUE),
			'pco_type' => $this->input->post('pco_type', TRUE),
			'pco_name' => $this->input->post('pco_name', TRUE),
			'pco_is_required' => $pco_is_required,
			'pco_is_default' => $pco_is_default,
			'pco_position' => $this->input->post('pco_position',TRUE),
			'pco_default_value' => $this->input->post('pco_default_value',TRUE),
			'pco_color' => $this->input->post('pco_color',TRUE),
			'before_name' => $this->input->post('before_name',TRUE),
			'check' => $this->input->post('check',TRUE),
			'check_type' => $this->input->post('check_type',TRUE)
		);

		echo $this->project_setup_m->update_id_rule($data);
	}

	/**
	* Function delete_id_rule
	*
	* @return string
	*/
	public function delete_id_rule()
	{
		$pco_list = $this->input->post('pco_list', TRUE);
		$pco_list = json_decode($pco_list);
		$data = array(
			'otm_project_pr_seq' => $this->input->post('pr_seq', TRUE),
			'pco_list' => $pco_list,
			'action_type' => $this->input->post('action_type', TRUE),
			'pco_type'	=> $this->input->post('pco_type', TRUE)
		);

		echo $this->project_setup_m->delete_id_rule($data);
	}

}
//End of file Project_setup.php
//Location: ./controllers/Project_setup.php