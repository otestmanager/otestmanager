<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Otm
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Otm extends Controller
{
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();

		if($this->router->method != 'install')
		{
			$check = $this->migration->_check_module();
			if($check === FALSE)
			{
				show_error('모듈이 설치되어 있지 않습니다. <br><br>설치하기 : <a href="./index.php/Otm/install">INSTALL</a>');
			}
		}
		$this->load->model('otm_m');
		$this->load->library('History');
	}

	public function install()
	{
		$this->migration->install();
	}

	public function version($version = NULL)
	{
		$this->migration->check_version($version);
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
	* Function index
	*
	* @return none
	*/
	public function index()
	{
		if($this->session->userdata('logged_in') === TRUE){
			$this->load->model('plugin_m');
			$plugin_list = $this->plugin_m->Plugin_list();
			//foreach ($plugin_list['migrations'] as $plugin=>$v) {
				//echo json_encode($v);
			//	if($v['subpage'] && $v['subpage'] === 'true'){
					//echo $v['service_id'].' : ';
					//echo $v['subpage'].' : ';
			//		}
			//	}
			//}

			$this->load->view('head_v');
			$this->load->view('view_controller');
			$this->load->view('project_store',$plugin_list);
			$this->load->view('store');
			$this->load->view('otm_v');
			$this->load->view('footer_v');
		}
		else{
			$this->load->view('login_v');
		}
	}

	/**
	* Function dashboard
	*
	* @param array $type Array Data
	*
	* @return view
	*/
	public function dashboard($type)
	{
		$data['type'] = $type;
		$data['id'] = $type;
		$this->load->view('dashboard_v', $data);
	}

	/**
	* Function project_list
	*
	* @return string
	*/
	public function project_list()
	{
		$data = array(
			'start' => $this->input->get_post('start', TRUE),
			'limit' => $this->input->get_post('limit', TRUE)
		);
		$projectCnt = $this->otm_m->project_totalCnt();
		$projectlist = $this->otm_m->project_list($data);

		echo '{success:true,totalCount: '.$projectCnt[0]->cnt.', data:'.json_encode($projectlist).'}';
	}

	/**
	* Function create_project
	*
	* @return string
	*/
	public function create_project()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('project_name', '프로젝트 명', 'required|min_length[2]|max_length[100]');
		$this->form_validation->set_rules('project_startdate', '시작일', 'required');

		if($this->form_validation->run() === TRUE){
			$project_data = array(
				'pg_seq' => $this->input->post('pg_seq', TRUE),
				'project_name' => $this->input->post('project_name', TRUE),
				'project_startdate' => $this->input->post('project_startdate', TRUE),
				'project_enddate' => $this->input->post('project_enddate', TRUE),
				'project_description' => $this->input->post('project_description', TRUE)
			);

			$is_duplicate = $this->otm_m->duplicate_project($project_data);

			if($is_duplicate){
				echo "{success:true,msg:'Duplicate'}";
			}else{
				echo $this->otm_m->create_project($project_data);
			}
		}
		else{
			echo validation_errors();
			exit;
		}
	}

	/**
	* Function update_project
	*
	* @return string
	*/
	public function update_project()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('project_seq', 'Project Seq', 'required');
		$this->form_validation->set_rules('project_name', 'Project Name', 'required|min_length[2]|max_length[100]');//is_unique[user.email]
		$this->form_validation->set_rules('project_startdate', 'Start Date', 'required');


		if($this->form_validation->run() === TRUE){
			$project_data = array(
				'project_seq' => $this->input->post('project_seq', TRUE),
				'project_name' => $this->input->post('project_name', TRUE),
				'project_startdate' => $this->input->post('project_startdate', TRUE),
				'project_enddate' => $this->input->post('project_enddate', TRUE),
				'project_description' => $this->input->post('project_description', TRUE)
			);

			echo $this->otm_m->update_project($project_data);
		}
		else{
			echo validation_errors();
			exit;
		}
	}

	/**
	* Function delete_project
	*
	* @return string
	*/
	public function delete_project()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('project_seq', 'Project', 'required');
		if($this->form_validation->run() === TRUE){
			$project_data = array(
				'project_seq' => $this->input->post('project_seq', TRUE)
			);
			echo $this->otm_m->delete_project($project_data);
		}
		else{
			echo validation_errors();
			exit;
		}
	}

	/**
	* Function copy_project
	*
	* @return string
	*/
	public function copy_project()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('copy_subject', '프로젝트 명', 'required|min_length[2]|max_length[100]');
		$this->form_validation->set_rules('copy_startdate', '시작일', 'required');
		$this->form_validation->set_rules('target_pr_seq', '그룹', 'required');

		if($this->form_validation->run() === TRUE){
			$project_data = array(
				'target_pr_seq' => $this->input->post('target_pr_seq', TRUE),
				'select_group_seq' => $this->input->post('select_group_seq', TRUE),
				'copy_subject' => $this->input->post('copy_subject', TRUE),
				'copy_startdate' => $this->input->post('copy_startdate', TRUE),
				'copy_enddate' => $this->input->post('copy_enddate', TRUE),
				'copy_description' => $this->input->post('copy_description', TRUE),
				'copy_tc_chk' => $this->input->post('copy_tc_chk', TRUE),
				'copy_def_chk' => $this->input->post('copy_def_chk', TRUE),
				'copy_filedoc_chk' => $this->input->post('copy_filedoc_chk', TRUE),
				'copy_pjtmem_chk' => $this->input->post('copy_pjtmem_chk', TRUE),
				'copy_pjtidrule_chk' => $this->input->post('copy_pjtidrule_chk', TRUE),
				'copy_pjtcode_chk' => $this->input->post('copy_pjtcode_chk', TRUE),
				'copy_pjtuserform_chk' => $this->input->post('copy_pjtuserform_chk', TRUE),
				'copy_pjtlifecycle_chk' => $this->input->post('copy_pjtlifecycle_chk', TRUE)
			);
			echo $this->otm_m->copy_project($project_data);
		}
		else{
			echo validation_errors();
			exit;
		}
	}

	/**
	* Function project_tree_list
	*
	* @return string
	*/
	public function project_tree_list()
	{
		$node_info = array(
			'node' => $this->input->post_get('node', TRUE)
		);

		$service_category = explode('_', $this->input->post_get('node', TRUE));


		$this->load->model('plugin_m');
		$plugin_list = $this->plugin_m->Plugin_list();
		//echo count($plugin_list['migrations']);

		foreach ($plugin_list['migrations'] as $plugin=>$v) {
			//echo json_encode($v);
			if($v['subpage'] && $v['subpage'] === 'true'){
				//echo $v['service_id'].' : ';
				//echo $v['subpage'].' : ';

				if($v['service_id'] === $service_category[0]){
					//echo $v['service_id'];
					$this->load->model($v['service_id'].'/'.$v['service_id'].'_m','temp_tree_list_m');
					$result = $this->temp_tree_list_m->subpage_tree_list($node_info);
					echo json_encode($result);
					return;
				}
			}
			//$module = str_replace('\\', '', $v[1]);
			//$module =  str_replace('/', '', $module);
			//echo ' : '.$module;
		}

		if($service_category[0] === 'testcase'){
			$this->load->model('testcase/testcase_m');
			$result = $this->testcase_m->plan_tree_list($node_info);
		}else{
			$result = $this->otm_m->project_tree_list($node_info);
		}

		echo json_encode($result);
	}

	/**
	* Function permission_list
	*
	* @return string
	*/
	public function permission_list()
	{
		$permission_list = $this->otm_m->permission_list();
		echo $this->return_json($permission_list);
	}

	/**
	* Function project_view
	*
	* @return string
	*/
	public function project_view()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('project_seq', 'Project Seq', 'required');
		if($this->form_validation->run() === TRUE){
			$project_data = array(
				'project_seq' => $this->input->post('project_seq', TRUE)
			);

			echo $this->return_json($this->otm_m->project_view($project_data));
		}
		else{
			echo validation_errors();
			exit;
		}
	}

	/**
	* Function redmine_issue_create
	*
	* @return string
	*/
	public function redmine_issue_create()
	{
		$redmine_data = file_get_contents("http://redmine.sten.kr:801/redmine/issues.json?key=dffa64f569e9ecc9d1173607d82ef08242368392");
		print $redmine_data;
	}

	/**
	* Function project_list_export
	*
	* @return string
	*/
	public function project_list_export()
	{
		$data = array(
			'start' => $this->input->get_post('start', TRUE),
			'limit' => $this->input->get_post('limit', TRUE)
		);

		$projectlist = $this->otm_m->project_list_export($data);

		echo $projectlist;
	}

	/**
	* Function otm_database_backup
	*
	* @return string
	*/
	public function otm_database_backup()
	{
		print $this->otm_m->otm_database_backup($data);
	}


	/**
	* Function project_dashboard
	*
	* @param array $type Array Data
	*
	* @return view
	*/
	public function project_dashboard($type = 'dashboard')
	{
		$data['type'] = $type;
		$data['id'] = $type;
		$this->load->view('project_dashboard_v', $data);
	}

	/**
	* Function project_dashboard_list
	*
	* @return string
	*/
	public function project_dashboard_list()
	{
		$node_info = array(
			'node' => $this->input->post_get('node', TRUE)
		);

		$result = $this->otm_m->project_dashboard_list($node_info);
		echo json_encode($result);
	}

	/**
	* Function project_dashboard_group_list
	*
	* @return string
	*/
	public function project_dashboard_group_list()
	{
		$node_info = array(
			'node' => $this->input->post_get('node', TRUE),
			'return_type' => 'group'
		);

		$result = $this->otm_m->project_dashboard_list($node_info);
		echo json_encode($result);
	}

	/**
	* Function create_project_group
	*
	* @return string
	*/
	public function create_project_group()
	{
		$node_info = array(
			'node' => $this->input->post_get('node', TRUE),
			'pg_name' => $this->input->post_get('pg_name', TRUE)
		);

		echo $this->otm_m->create_project_group($node_info);
	}

	/**
	* Function update_project_group
	*
	* @return string
	*/
	public function update_project_group()
	{
		$node_info = array(
			'node' => $this->input->post_get('node', TRUE),
			'pg_name' => $this->input->post_get('pg_name', TRUE)
		);

		echo $this->otm_m->update_project_group($node_info);
	}

	/**
	* Function delete_project_group
	*
	* @return string
	*/
	public function delete_project_group()
	{
		$node_info = array(
			'node' => $this->input->post_get('node', TRUE)
		);

		echo $this->otm_m->delete_project_group($node_info);
	}


	/**
	* Function move_project_group
	*
	* @return string
	*/
	public function move_project_group()
	{
		$data = array(
			'target_id' => $this->input->post('target_id', TRUE),
			'position' => $this->input->post('position', TRUE),
			'select_id' => json_decode($this->input->post('select_id', TRUE))
		);

		echo $this->otm_m->move_project_group($data);
	}


	/**
	* Function user_project_include_info
	*
	* @return string
	*/
	public function user_project_include_info()
	{
		$node_info = array(
			'node' => $this->input->post_get('node', TRUE),
			'mb_email' => $this->input->post_get('mb_email', TRUE)
		);

		$result = $this->otm_m->user_project_include_info($node_info);
		echo json_encode($result);
	}

}
//End of file Otm.php
//Location: ./controllers/Otm.php