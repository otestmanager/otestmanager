<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Report
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
 
class Report extends Controller {
	function __construct()
	{
		parent::__construct();	
		$this->load->database();
		$this->load->model('report/report_m');
	}

	function report($data = array())
	{		
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


	function report_list()
	{
		switch($this->session->userdata('mb_lang')){
			case "ko":
				print '{success:true,totalCount: 5, data:[
					{"category":"결함",				"title":"결함 상태 정보",				"id":"df_001"},
					{"category":"결함",				"title":"결함 심각도 정보",				"id":"df_002"},
					{"category":"결함",				"title":"결함 우선순위 정보",			"id":"df_003"},
					{"category":"테스트케이스",		"title":"차수별 테스트케이스 정보",		"id":"tc_001"},		
					{"category":"테스트케이스",		"title":"차수별 결함 발생 정보",		"id":"tc_002"}
				]}';
			break;
			case "en":
			default:
				print '{success:true,totalCount: 5, data:[
					{"category":"Defect",		"title":"Status of defects",						"id":"df_001"},
					{"category":"Defect",		"title":"Severity status of defects",				"id":"df_002"},
					{"category":"Defect",		"title":"Priority status of defects",				"id":"df_003"},
					{"category":"TestCase",		"title":"Test case information per plan",			"id":"tc_001"},		
					{"category":"TestCase",		"title":"Defect occurrence information per plan",	"id":"tc_002"}
				]}';				
			break;			
		}
		exit;		
	}

	function get_report_tc_list()
	{
		switch($this->session->userdata('mb_lang')){
			case "ko":
				print '{success:true,totalCount: 4, data:[
					{"title":"차수별 테스트케이스 정보",	"id":"tc_001"},		
					{"title":"차수별 결함 발생 정보",		"id":"tc_002"},
					{"title":"Radar Sample",				"id":"tc_003"},
					{"title":"Gauge Sample",				"id":"tc_004"}
				]}';
			break;
			case "en":
			default:
				print '{success:true,totalCount: 4, data:[
					{"title":"Test case information per plan",			"id":"tc_001"},		
					{"title":"Defect occurrence information per plan",	"id":"tc_002"},
					{"title":"Radar Sample",		"id":"tc_003"},
					{"title":"Gauge Sample",		"id":"tc_004"}
				]}';		
			break;
		}
	}

	function get_report_defect_list()
	{
		/*
		가로 막대형 : horizontal_bar
		세로 막대형 : vertical_bar
		원형		: pie
		*/
		switch($this->session->userdata('mb_lang')){
			case "ko":
				print '{success:true,totalCount: 3, data:[
					{"title":"결함 상태 정보",				"id":"df_001",		"chart_type":"{horizontal_bar,vertical_bar,pie}"},
					{"title":"결함 심각도 정보",			"id":"df_002",		"chart_type":"{horizontal_bar,vertical_bar,pie}"},
					{"title":"결함 우선순위 정보",			"id":"df_003",		"chart_type":"{horizontal_bar,vertical_bar,pie}"}					
				]}';
			break;
			case "en":
			default:
				print '{success:true,totalCount: 3, data:[
					{"title":"Status of defects",				"id":"df_001",	"chart_type":"{horizontal_bar,vertical_bar,pie}"},
					{"title":"Severity status of defects",		"id":"df_002",	"chart_type":"{horizontal_bar,vertical_bar,pie}"},
					{"title":"Priority status of defects",		"id":"df_003",	"chart_type":"{horizontal_bar,vertical_bar,pie}"}					
				]}';		
			break;
		}
	}

	function get_report_defect_status()//결함 상태 정보
	{
		$data = array(
			'project_seq' => $this->input->post('project_seq',true),
			'tp_seq' => $this->input->post('tp_seq',true),
			'start_date' => $this->input->post('start_date',true),
			'end_date' => $this->input->post('end_date',true),
			'code'=>'status'
		);
		$report_defect_column	= $this->report_m->get_report_column($data);
		$report_defect_data		= $this->report_m->get_report_defect_status_data($data,$report_defect_column);

		return "{success:true,columns: ".json_encode($report_defect_column).", data:".json_encode($report_defect_data)."}";
	}

	function get_report_defect_severity()//결함 심각도 정보
	{
		$data = array(
			'project_seq' => $this->input->post('project_seq',true),
			'tp_seq' => $this->input->post('tp_seq',true),
			'start_date' => $this->input->post('start_date',true),
			'end_date' => $this->input->post('end_date',true),
			'code'=>'severity'
		);
		$report_defect_column	= $this->report_m->get_report_column($data);
		$report_defect_data		= $this->report_m->get_report_defect_severity_data($data,$report_defect_column);
		
		return "{success:true,columns: ".json_encode($report_defect_column).", data:".json_encode($report_defect_data)."}";
	}

	function get_report_defect_priority()//결함 우선순위 정보
	{
		$data = array(
			'project_seq' => $this->input->post('project_seq',true),
			'tp_seq' => $this->input->post('tp_seq',true),
			'start_date' => $this->input->post('start_date',true),
			'end_date' => $this->input->post('end_date',true),
			'code'=>'priority'
		);
		$report_defect_column	= $this->report_m->get_report_column($data);
		$report_defect_data		= $this->report_m->get_report_defect_priority_data($data,$report_defect_column);
				
		return "{success:true,columns: ".json_encode($report_defect_column).", data:".json_encode($report_defect_data)."}";
	}

	function get_report_defect_frequency()//결함 재현빈도
	{
		$data = array(
			'project_seq' => $this->input->post('project_seq',true),
			'tp_seq' => $this->input->post('tp_seq',true),
			'start_date' => $this->input->post('start_date',true),
			'end_date' => $this->input->post('end_date',true),
			'code'=>'frequency'
		);
		$report_defect_column	= $this->report_m->get_report_column($data);
		$report_defect_data		= $this->report_m->get_report_defect_frequency_data($data,$report_defect_column);
				
		return "{success:true,columns: ".json_encode($report_defect_column).", data:".json_encode($report_defect_data)."}";
	}

	function get_report_plan_result_summary()//실행 결과 요약
	{
		$data = array(
			'pr_seq' => $this->input->post('project_seq',true),
			'tp_seq' => $this->input->post('tp_seq',true)
		);

		$plan_tc_result_columns	= $this->report_m->get_testcase_result_summary_columns($data);
		$plan_tc_result_data		= $this->report_m->get_report_plan_result_summary_data($data,$plan_tc_result_columns);

		//print $plan_tc_result_data;
		//exit;
				
		return "{success:true,columns: ".json_encode($plan_tc_result_columns).", data:".json_encode($plan_tc_result_data)."}";
	}

	function get_report_plan_testcase()//차수별 테스트케이스 정보
	{
		$mb_lang = $this->session->userdata('mb_lang');

		$data = array(
			'project_seq' => $this->input->post('project_seq',true)
		);

		$report_defect_data = $this->report_m->get_report_plan_testcase_data($data);
		if(!isset($report_defect_data)){			
			$report_defect_data = array();
		}
		
		switch($mb_lang){
			case "ko":
				return '{success:true,columns: [{"dataIndex":"plan","name":"차수"},{"dataIndex":"tc_cnt","name":"테스트케이스 수"},{"dataIndex":"execute_cnt","name":"실행 수"}], data:'.json_encode($report_defect_data).'}';
			break;
			default:
				return '{success:true,columns: [{"dataIndex":"plan","name":"Plan"},{"dataIndex":"tc_cnt","name":"TestCase Count"},{"dataIndex":"execute_cnt","name":"Execution Count"}], data:'.json_encode($report_defect_data).'}';
			break;
		
		}	
	}

	function get_report_plan_defect()//차수별 결함 발생 정보
	{
		$mb_lang = $this->session->userdata('mb_lang');

		$data = array(
			'project_seq' => $this->input->post('project_seq',true)
		);
		$report_defect_data	= $this->report_m->get_report_plan_defect_data($data);
		if(!isset($report_defect_data)){			
			$report_defect_data = array();
		}

		switch($mb_lang){
			case "ko":
				return '{success:true,columns: [{"dataIndex":"plan","name":"차수"},{"dataIndex":"defect_cnt","name":"결함 수"}], data:'.json_encode($report_defect_data).'}';
			break;
			default:
				return '{success:true,columns: [{"dataIndex":"plan","name":"Plan"},{"dataIndex":"defect_cnt","name":"Defect Count"}], data:'.json_encode($report_defect_data).'}';
			break;
		
		}
	}

	function get_radar()
	{		
		return '{success:true,columns:[{"dataIndex":"data1","name":"data1"},{"dataIndex":"data2","name":"data2"}],data:[{"name":"aaa","data1":"1","data2":"2","data3":"3"},{"name":"bbb","data1":"2","data2":"4","data3":"8"},{"name":"ccc","data1":"7","data2":"3","data3":"2"},{"name":"ddd","data1":"2","data2":"6","data3":"9"},{"name":"eee","data1":"4","data2":"2","data3":"8"}]}';
	}

	function get_gauge()
	{
		return '{success:true,columns:[{"dataIndex":"data1","name":"data1"},{"dataIndex":"data2","name":"data2"}],data:[{"name":"aaa","data1":"55"}]}';
	}

	function report_export()
	{
		$mb_lang = $this->session->userdata('mb_lang');
		$report_data = array();
		$report_id = $this->input->post_get('report_id',true);
		
		$report = explode ("/", $report_id);
		for($i=0; $i<count($report); $i++)
		{
			switch($report[$i])
			{
				case "df_001":
					$data = array(
						'project_seq' => $this->input->post_get('project_seq',true),
						'code'=>'status'
					);
					$report_defect_data	= $this->report_m->get_report_defect_export($data);
					if($mb_lang === "ko"){
						array_push($report_data,array('title'=>'결함 상태 정보'));
					}else{
						array_push($report_data,array('title'=>'Status of Defects'));
					}
					array_push($report_data,$report_defect_data);

					break;
				case "df_002":
					$data = array(
						'project_seq' => $this->input->post_get('project_seq',true),
						'code'=>'severity'
					);
					$report_defect_data	= $this->report_m->get_report_defect_export($data);
					if($mb_lang === "ko"){
						array_push($report_data,array('title'=>'결함 심각도 정보'));
					}else{
						array_push($report_data,array('title'=>'Severity status of Defects'));
					}
					array_push($report_data,$report_defect_data);

					break;
				case "df_003":
					$data = array(
						'project_seq' => $this->input->post_get('project_seq',true),
						'code'=>'priority'
					);
					$report_defect_data	= $this->report_m->get_report_defect_export($data);
					if($mb_lang === "ko"){
						array_push($report_data,array('title'=>'결함 우선순위 정보'));
					}else{
						array_push($report_data,array('title'=>'Priority status of Defects'));
					}
					array_push($report_data,$report_defect_data);

					break;
				case "tc_001":
					$data = array(
						'project_seq' => $this->input->post_get('project_seq',true),
						'code' => 'testcase'
					);
					
					$report_plan_data = $this->report_m->get_report_plan_export($data);
					if($mb_lang === "ko"){
						array_push($report_data,array('title'=>'차수별 테스트케이스 정보'));
					}else{
						array_push($report_data,array('title'=>'Testcase information per plan'));
					}
					array_push($report_data,$report_plan_data);
					
					break;
				case "tc_002":
					$data = array(
						'project_seq' => $this->input->post_get('project_seq',true),
						'code' => 'defect'
					);
					
					$report_plan_data = $this->report_m->get_report_plan_export($data);
					if($mb_lang === "ko"){
						array_push($report_data,array('title'=>'차수별 결함 발생 정보'));
					}else{
						array_push($report_data,array('title'=>'Defect occurrence information per plan'));
					}					
					array_push($report_data,$report_plan_data);

					break;
			}
		}
		return $report_data;
	}	

	function get_plan_tc_result_list()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq',true)
		);
		
		$plan_tc_result_columns	= $this->report_m->get_plan_tc_result_columns($data);
		$plan_tc_result_data	= $this->report_m->get_plan_tc_result_data($data,$plan_tc_result_columns);
		
		print "{success:true,columns: ".json_encode($plan_tc_result_columns).", data:".json_encode($plan_tc_result_data)."}";		
	}

	function get_testcase_result_summary_list()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq',true)
		);

		$testcase_result_summary_columns	= $this->report_m->get_testcase_result_summary_columns($data);
		$testcase_result_summary_data		= $this->report_m->get_testcase_result_summary_data($data,$testcase_result_summary_columns);
		
		print "{success:true,columns: ".json_encode($testcase_result_summary_columns).", data:".json_encode($testcase_result_summary_data)."}";
	}

	function get_plan_defect_list()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq',true),
			'tp_seq' => $this->input->post('tp_seq',true)
		);
		return $this->report_m->get_plan_defect_data($data);
	}

	function get_defect_list()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq',true),
			'limit' => $this->input->post('limit',true),
			'page' => $this->input->post('page',true),
			'start' => $this->input->post('start',true)
		);
		return $this->report_m->get_defect_data($data);
	}

	function get_plan_testcase_result_column()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq',true)
		);
		$testcase_result_columns	= $this->report_m->get_testcase_result_summary_columns($data);
		print "{success:true,columns: ".json_encode($testcase_result_columns).", data:[]}";
	}

	function get_plan_testcase_result_list()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq',true),
			'tp_seq' => $this->input->post('tp_seq',true)
		);
		if($data['tp_seq']){
			$testcase_result_columns	= $this->report_m->get_testcase_result_summary_columns($data);
			$plan_testcase_result = $this->report_m->get_plan_testcase_result_list($data,$testcase_result_columns);
			
			print "{success:true, data:".json_encode($plan_testcase_result)."}";
		}else{
			return '{success:true,totalCount:0,data:[]}';
		}
	}

	function get_defect_scurve_list()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq',true),
			'sdate' => $this->input->post('sdate',true),
			'edate' => $this->input->post('edate',true),
			'data_unit' => $this->input->post('data_unit',true)
		);
		return $this->report_m->get_defect_scurve_list($data);		
	}

	function get_suite_defect_distribution_list()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq',true),
			'tp_seq' => $this->input->post('tp_seq',true)
		);
		
		if($data['tp_seq']){
			return $this->report_m->get_suite_defect_distribution_list($data);
		}else{
			return '{success:true,totalCount:0,data:[]}';
		}		
	}

	function report_export_v1()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('project_seq',true),
			'tp_seq' => $this->input->post_get('tp_seq',true),
			'report_type' => $this->input->post_get('report_type',true),
			'sdate' => $this->input->post_get('sdate',true),
			'edate' => $this->input->post_get('edate',true),
			'data_unit' => $this->input->post_get('data_unit',true)
		);
		
	
		$report_data = array();
		switch($data['report_type'])
		{
			case "plan_tc_result_grid":
				$tmp_data = array();	
				$tmp_data = $this->report_m->get_plan_tc_result_grid_excel($data);
				
				array_push($report_data,$tmp_data);				
			break;

			case "defect_list_grid":
				$tmp_data = $this->report_m->get_defect_data_excel($data);
				array_push($report_data,$tmp_data);
			break;
			case "defect_scurve_grid":
				$tmp_data = $this->report_m->get_defect_scurve_excel($data);
				array_push($report_data,$tmp_data);
			break;
			case "suite_defect_distribution_grid":
				$tmp_data = $this->report_m->get_suite_defect_distribution_excel($data);
				array_push($report_data,$tmp_data);
			break;
			case "plan_testcase_result_grid":
				$tmp_data = $this->report_m->get_plan_testcase_result_excel($data);
				array_push($report_data,$tmp_data);
			break;
			case "risk_defect_summary_grid":
				$tmp_data = $this->report_m->get_risk_defect_summary_excel($data);
				array_push($report_data,$tmp_data);
			break;
			case "risk_tcresult_summary_grid":
				$tmp_data = $this->report_m->get_risk_tcresult_summary_excel($data);
				array_push($report_data,$tmp_data);
			case "risk_defect_into_panel":
				$tmp_data = $this->report_m->get_risk_defect_into_excel($data);
				array_push($report_data,$tmp_data);
			break;
			default:
			break;
		}
		return $report_data;
	}

	function get_defect_from_testcase_list()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq',true),
			'df_seq' => $this->input->post_get('df_seq',true)
		);

		if($data['pr_seq'] && $data['df_seq']){
			return $this->report_m->get_defect_from_testcase_list($data);
		}else{
			return '{success:true,totalCount:0,data:[]}';
		}		
	}

	function get_testcase_from_defect_list()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq',true),
			'tp_seq' => $this->input->post_get('tp_seq',true),
			'tc_seq' => $this->input->post_get('tc_seq',true),
			'type' => $this->input->post_get('type',true)
		);

		if($data['pr_seq'] && $data['tc_seq']){
			return $this->report_m->get_testcase_from_defect_list($data);
		}else{
			return '{success:true,totalCount:0,data:[]}';
		}		
	}

	function get_testcase_result_list()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq',true),
			'tc_seq' => $this->input->post_get('tc_seq',true),
			'tp_seq' => $this->input->post_get('tp_seq',true)
		);

		if($data['pr_seq'] && $data['tc_seq']){
			return $this->report_m->get_testcase_result_list($data);
		}else{
			return '{success:true,totalCount:0,data:[]}';
		}		
	}

	function get_risk_defect_summary()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq',true),
			'tp_seq' => $this->input->post_get('tp_seq',true)
		);

		if($data['pr_seq']){
			return $this->return_json($this->report_m->get_risk_defect_summary($data));						
		}else{
			return '{success:true,totalCount:0,data:[]}';
		}

		//return '{success:true,data:[{name:"STA","open_cnt":"1","close_cnt":"2","total_cnt":"3"},{name:"STTA","open_cnt":"3","close_cnt":"4","total_cnt":"7"},{name:"ITA","open_cnt":"2","close_cnt":"2","total_cnt":"4"},{name:"FTA","open_cnt":"7","close_cnt":"6","total_cnt":"13"}]}';
	}

	function get_risk_tcresult_summary()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq',true),
			'tp_seq' => $this->input->post_get('tp_seq',true)
		);

		if($data['pr_seq']){
			//return $this->report_m->get_risk_tcresult_summary($data);
			return $this->return_json($this->report_m->get_risk_tcresult_summary($data));
		}else{
			return '{success:true,totalCount:0,data:[]}';
		}
	}

	function get_risk_defect_info()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq',true),
			'tp_seq' => $this->input->post_get('tp_seq',true)
		);

		if($data['pr_seq']){
			return $this->return_json($this->report_m->get_risk_defect_info($data));
		}else{
			return '{success:true,totalCount:0,data:[]}';
		}

		return '{success:true,totalCount: 8, data:[{"risk_area":"STA","df_subject":"결함1","df_status":"신규","df_author":"관리자","df_writer":"관리자","df_regdate":"2016-08-01"},{"risk_area":"STA","df_subject":"결함2","df_status":"개설","df_author":"관리자","df_writer":"관리자","df_regdate":"2016-08-01"},{"risk_area":"STTA","df_subject":"결함3","df_status":"완료","df_author":"관리자","df_writer":"관리자","df_regdate":"2016-08-01"},{"risk_area":"FTA","df_subject":"결함4","df_status":"완료","df_author":"관리자","df_writer":"관리자","df_regdate":"2016-08-01"},{"risk_area":"STA","df_subject":"결함5","df_status":"신규","df_author":"관리자","df_writer":"관리자","df_regdate":"2016-08-01"},{"risk_area":"FTA","df_subject":"결함6","df_status":"개설","df_author":"관리자","df_writer":"관리자","df_regdate":"2016-08-01"},{"risk_area":"STA","df_subject":"결함7","df_status":"신규","df_author":"관리자","df_writer":"관리자","df_regdate":"2016-08-01"},{"risk_area":"ITA","df_subject":"결함8","df_status":"완료","df_author":"관리자","df_writer":"관리자","df_regdate":"2016-08-01"},]}';
	}
}
//End of file report.php
//Location: ./controllers/report.php 
?>