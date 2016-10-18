<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Report_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/

class Report_m extends CI_Model {
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();

		$this->tmp_array = Array();
		$this->root_array = Array();
		$this->return_array = Array();
		$this->location_path = Array();
	}

	function get_report_column($data)
	{
		$mb_lang = $this->session->userdata('mb_lang');

		$pr_seq = $data['project_seq'];
		$type = $data['code'];
		$str_sql = "select
						pco_seq as dataIndex,
						pco_name as name
					from otm_project_code where pco_type='$type' and otm_project_pr_seq='$pr_seq'";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}

		$tot_arr = array();
		$tot_arr['dataIndex'] = "tot";

		switch($mb_lang){
			case "ko":
				$tot_arr['name'] = "전체";
			break;
			default:
				$tot_arr['name'] = "Total";
			break;
		}
		array_unshift($arr, $tot_arr);

		return $arr;
	}

	function get_report_defect_status_data($data,$column_data)
	{
		$row_column="";
		for($i=1;$i<sizeof($column_data);$i++){
			$idx = $column_data[$i]->dataIndex;

			$row_column .= " ,sum(case when status=$idx then 1 else 0 end) as val_$idx";
		}

		$pr_seq = $data['project_seq'];
		$tp_seq = $data['tp_seq'];
		$start_date = date("Y-m-d",strtotime($data['start_date']));
		$end_date = date("Y-m-d",strtotime($data['end_date']));

		if($tp_seq > 0){
			$plan_sql = "
				select
					od.*
				from
					otm_defect as od, otm_testcase_result as otr, otm_testcase_link as otl
				where
					od.otm_project_pr_seq='$pr_seq' and
					od.df_seq=otr.otm_defect_df_seq and
					otl.tl_seq = otr.otm_testcase_link_tl_seq and
					otl.otm_testcase_plan_tp_seq='$tp_seq' and
					date_format(od.regdate,'%Y-%m-%d')>='$start_date' and date_format(od.regdate,'%Y-%m-%d')<='$end_date'
			";
		}else{
			$plan_sql = "select * from otm_defect where otm_project_pr_seq='$pr_seq' and date_format(regdate,'%Y-%m-%d')>='$start_date' and date_format(regdate,'%Y-%m-%d')<='$end_date' ";
		}

		$str_sql = "
			select
				count(*) as tot
				$row_column
			from
			(
				select max(dc_current_status_co_seq) as status from
				(
					select * from
					(
						$plan_sql
					) as a
					left outer join
					otm_defect_assign as b
					on
					a.df_seq=b.otm_defect_df_seq
				) as a
				group by a.otm_defect_df_seq
			) as a
		";

		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}
		return $arr;
	}

	function get_report_defect_severity_data($data,$column_data)
	{
		$row_column="";
		for($i=1;$i<sizeof($column_data);$i++){
			$idx = $column_data[$i]->dataIndex;

			$row_column .= " ,sum(case when df_severity=$idx then 1 else 0 end) as val_$idx";
		}

		$pr_seq = $data['project_seq'];
		$tp_seq = $data['tp_seq'];
		$start_date = date("Y-m-d",strtotime($data['start_date']));
		$end_date = date("Y-m-d",strtotime($data['end_date']));

		if($tp_seq > 0){
			$plan_sql = "
				select
					od.*
				from
					otm_defect as od, otm_testcase_result as otr, otm_testcase_link as otl
				where
					od.otm_project_pr_seq='$pr_seq' and
					od.df_seq=otr.otm_defect_df_seq and
					otl.tl_seq = otr.otm_testcase_link_tl_seq and
					otl.otm_testcase_plan_tp_seq='$tp_seq' and
					date_format(od.regdate,'%Y-%m-%d')>='$start_date' and date_format(od.regdate,'%Y-%m-%d')<='$end_date'
			";
		}else{
			$plan_sql = "select * from otm_defect where otm_project_pr_seq='$pr_seq' and date_format(regdate,'%Y-%m-%d')>='$start_date' and date_format(regdate,'%Y-%m-%d')<='$end_date'";
		}

		$str_sql = "
			select
				count(*) as tot
				$row_column
			from
			(
				$plan_sql
			) as a
		";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}
		return $arr;
	}

	function get_report_defect_priority_data($data,$column_data)
	{
		$row_column="";
		for($i=1;$i<sizeof($column_data);$i++){
			$idx = $column_data[$i]->dataIndex;

			$row_column .= " ,sum(case when df_priority=$idx then 1 else 0 end) as val_$idx";
		}

		$pr_seq = $data['project_seq'];
		$tp_seq = $data['tp_seq'];
		$start_date = date("Y-m-d",strtotime($data['start_date']));
		$end_date = date("Y-m-d",strtotime($data['end_date']));

		if($tp_seq > 0){
			$plan_sql = "
				select
					od.*
				from
					otm_defect as od, otm_testcase_result as otr, otm_testcase_link as otl
				where
					od.otm_project_pr_seq='$pr_seq' and
					od.df_seq=otr.otm_defect_df_seq and
					otl.tl_seq = otr.otm_testcase_link_tl_seq and
					otl.otm_testcase_plan_tp_seq='$tp_seq' and
					date_format(od.regdate,'%Y-%m-%d')>='$start_date' and date_format(od.regdate,'%Y-%m-%d')<='$end_date'
			";
		}else{
			$plan_sql = "select * from otm_defect where otm_project_pr_seq='$pr_seq' and date_format(regdate,'%Y-%m-%d')>='$start_date' and date_format(regdate,'%Y-%m-%d')<='$end_date'";
		}
		$str_sql = "
			select
				count(*) as tot
				$row_column
			from
			(
				$plan_sql
			) as a
		";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}
		return $arr;
	}

	function get_report_defect_frequency_data($data,$column_data)
	{
		$row_column="";
		for($i=1;$i<sizeof($column_data);$i++){
			$idx = $column_data[$i]->dataIndex;

			$row_column .= " ,sum(case when df_frequency=$idx then 1 else 0 end) as val_$idx";
		}

		$pr_seq = $data['project_seq'];
		$tp_seq = $data['tp_seq'];
		$start_date = date("Y-m-d",strtotime($data['start_date']));
		$end_date = date("Y-m-d",strtotime($data['end_date']));

		if($tp_seq > 0){
			$plan_sql = "
				select
					od.*
				from
					otm_defect as od, otm_testcase_result as otr, otm_testcase_link as otl
				where
					od.otm_project_pr_seq='$pr_seq' and
					od.df_seq=otr.otm_defect_df_seq and
					otl.tl_seq = otr.otm_testcase_link_tl_seq and
					otl.otm_testcase_plan_tp_seq='$tp_seq' and
					date_format(od.regdate,'%Y-%m-%d')>='$start_date' and date_format(od.regdate,'%Y-%m-%d')<='$end_date'
			";
		}else{
			$plan_sql = "select * from otm_defect where otm_project_pr_seq='$pr_seq' and date_format(regdate,'%Y-%m-%d')>='$start_date' and date_format(regdate,'%Y-%m-%d')<='$end_date'";
		}
		$str_sql = "
			select
				count(*) as tot
				$row_column
			from
			(
				$plan_sql
			) as a
		";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}
		return $arr;
	}

	function get_report_plan_testcase_data($data)
	{
		$pr_seq = $data['project_seq'];
		$str_sql = "select
					tp_subject as plan,
					tc.cnt as val_tc_cnt,
					result.cnt as val_execute_cnt
				from
					otm_testcase_plan as a
				left outer join
				(
					select a.*, count(*) as cnt
					from
						otm_testcase as b,
						otm_testcase_link as a
					where
						a.otm_testcase_tc_seq = b.tc_seq
						and b.tc_is_task = 'file'
						and b.otm_project_pr_seq = '$pr_seq'
					group by otm_testcase_plan_tp_seq
				) as tc
				on  a.tp_seq=tc.otm_testcase_plan_tp_seq
				left outer join
				(
					select a.*, c.*, count(*) as cnt
					from
						otm_testcase as b,
						otm_testcase_link as a,
						otm_testcase_result as c
					where
						a.otm_testcase_tc_seq = b.tc_seq
						and b.tc_is_task = 'file'
						and b.otm_project_pr_seq = '$pr_seq'
						and tl_seq=c.otm_testcase_link_tl_seq
					group by otm_testcase_plan_tp_seq
				) as result
				on  a.tp_seq=result.otm_testcase_plan_tp_seq
				where
					a.otm_project_pr_seq='$pr_seq'
				group by tp_seq
			";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}
		return $arr;
	}

	function get_report_plan_defect_data($data)
	{
		$pr_seq = $data['project_seq'];
		$str_sql = "select
				tp_subject as plan,
				count(df_seq) as val_defect_cnt
			from
				otm_testcase_plan as a
			left outer join
				otm_testcase_link as b
			on  a.tp_seq=b.otm_testcase_plan_tp_seq
			left outer join
				otm_testcase_result as c
			on b.tl_seq=c.otm_testcase_link_tl_seq
			left outer join
				otm_defect as d
			on c.tr_seq=d.otm_testcase_result_tr_seq
			where
				a.otm_project_pr_seq='$pr_seq'
			group by tp_seq";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}
		return $arr;
	}

	function get_report_defect_export($data)
	{
		$pr_seq = $data['project_seq'];

		$column_data = $this->get_report_column($data);

		$row_column="";
		for($i=1;$i<sizeof($column_data);$i++){
			$idx = $column_data[$i]->dataIndex;
			$name = $column_data[$i]->name;
			if($data['code'] === 'status'){
				$row_column .= " ,sum(case when status=$idx then 1 else 0 end) as '$name'";
			}else if($data['code'] === 'priority'){
				$row_column .= " ,sum(case when df_priority=$idx then 1 else 0 end) as '$name'";
			}else if($data['code'] === 'severity'){
				$row_column .= " ,sum(case when df_severity=$idx then 1 else 0 end) as '$name'";
			}
		}

		switch($data['code']){
			case 'status':
				$str_sql = "
					select
						count(*) as Total
						$row_column
					from
					(
						select max(dc_current_status_co_seq) as status from
						(
							select * from
							(
								select
									*
								from
								otm_defect
								where
								otm_project_pr_seq='$pr_seq'
							) as a
							left outer join
							otm_defect_assign as b
							on
							a.df_seq=b.otm_defect_df_seq
						) as a
						group by a.otm_defect_df_seq
					) as a
				";

				break;
			case 'priority':
			case 'severity':
				$str_sql = "
					select
						count(*) as Total
						$row_column
					from
						otm_defect
					where
						otm_project_pr_seq='$pr_seq';
				";

				break;
		}

		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}
		return $arr;
	}

	function get_report_plan_export($data)
	{
		$pr_seq = $data['project_seq'];
		$mb_lang = $this->session->userdata('mb_lang');

		switch($data['code']){
			case 'testcase':

				if($mb_lang === "ko"){
					$select_sql = "
						tp_subject as '차수',
						count(tl_seq) as '테스트케이스 수',
						count(tr_seq) as '실행 수'	";
				}else{
					$select_sql = "
						tp_subject as 'Plan',
						count(tl_seq) as 'Testcase Count',
						count(tr_seq) as 'Execution Count'	";
				}
				$str_sql = "select
						$select_sql
					from
						otm_testcase_plan as a
					left outer join
						(
							select a.*
							from
								otm_testcase_link as a,
								otm_testcase as b
							where
								a.otm_testcase_tc_seq = b.tc_seq
								and b.tc_is_task = 'file'
								and b.otm_project_pr_seq = '$pr_seq'
						) as b
					on  a.tp_seq=b.otm_testcase_plan_tp_seq
					left outer join
						otm_testcase_result as c
					on b.tl_seq=c.otm_testcase_link_tl_seq
					where
						a.otm_project_pr_seq='$pr_seq'
					group by tp_seq";
				break;
			case 'defect':
				if($mb_lang === "ko"){
					$select_sql = "
						tp_subject as '차수',
						count(df_seq) as '결함 수'		";
				}else{
					$select_sql = "
						tp_subject as 'Plan',
						count(df_seq) as 'Defect Count'		";
				}
				$str_sql = "select
						$select_sql

					from
						otm_testcase_plan as a
					left outer join
						otm_testcase_link as b
					on  a.tp_seq=b.otm_testcase_plan_tp_seq
					left outer join
						otm_testcase_result as c
					on b.tl_seq=c.otm_testcase_link_tl_seq
					left outer join
						otm_defect as d
					on c.tr_seq=d.otm_testcase_result_tr_seq
					where
						a.otm_project_pr_seq='$pr_seq'
					group by tp_seq";
				break;
		}

		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}
		return $arr;
	}

	function get_plan_tc_result_columns($data){
		$pr_seq = $data['pr_seq'];

		$subColumn_arr = array();

		$str_sql = "select * from otm_project_code where pco_type='tc_result' and otm_project_pr_seq='$pr_seq'";
		$query = $this->db->query($str_sql);
		$i=0;
		foreach ($query->result() as $row)
		{
			$subColumn_arr[$i]['dataIndex'] = $row->pco_seq;
			$subColumn_arr[$i]['name'] = $row->pco_name;
			$i++;
		}

		$str_sql = "select tp_seq,tp_subject from otm_testcase_plan where otm_project_pr_seq='$pr_seq' order by tp_seq desc";
		$query = $this->db->query($str_sql);
		$i=0;
		foreach ($query->result() as $row)
		{
			$arr[$i]['plan_seq'] = $row->tp_seq;
			$arr[$i]['plan_name'] = $row->tp_subject;
			$arr[$i]['subColumn'] = $subColumn_arr;
			$i++;
		}
		return $arr;
	}

	function get_testcase_result_summary_columns($data)
	{
		$pr_seq = $data['pr_seq'];

		$subColumn_arr = array();

		$str_sql = "select * from otm_project_code where pco_type='tc_result' and otm_project_pr_seq='$pr_seq'";
		$query = $this->db->query($str_sql);
		$i=0;
		foreach ($query->result() as $row)
		{
			$subColumn_arr[$i]['dataIndex'] = $row->pco_seq;
			$subColumn_arr[$i]['name'] = $row->pco_name;
			$i++;
		}
		switch($this->session->userdata('mb_lang')){
			case "ko":
				$arr[0]['plan_name'] = '테스트케이스 실행결과';
			break;
			case "en":
			default:
				$arr[0]['plan_name'] = 'Run the TestCase result';
			break;
		}
		$arr[0]['subColumn'] = $subColumn_arr;

		return $arr;
	}

	function get_testcase_result_summary_data($data,$plan_tc_result_columns="")
	{
		$pr_seq = $data['pr_seq'];

		$field_array = array();
		$subQuery = "";
		if(sizeof($plan_tc_result_columns)>0){
			for($i=0;$i<sizeof($plan_tc_result_columns);$i++){
				$plan_info = $plan_tc_result_columns[$i];

				for($j=0;$j<sizeof($plan_info['subColumn']);$j++){
					$pco_seq = $plan_info['subColumn'][$j]['dataIndex'];
					$field_name = "_".$pco_seq;

					array_push($field_array,$field_name);
					$subQuery .= ",sum(case when otr.otm_project_code_pco_seq=$pco_seq then 1 else 0 end) as '$field_name'";
				}
			}
		}
		$str_sql = "
			select a.*,b.tc_cnt
			from
			(
				select
					tp_seq,tp_subject,date_format(tp_startdate,'%Y-%m-%d') as startdate,date_format(tp_enddate,'%Y-%m-%d') as enddate,tc_info.*
				from
				otm_testcase_plan as op
				left outer join
				(
					select
						tc_info.otm_testcase_plan_tp_seq
						$subQuery
					from
					otm_testcase_result as otr,
					(
						select
							ot.tc_seq,ol.otm_testcase_plan_tp_seq,ol.tl_seq
						from
						otm_testcase as ot, otm_testcase_link as ol
						where
						ot.otm_project_pr_seq='$pr_seq' and
						ot.tc_seq=ol.otm_testcase_tc_seq and
						ot.tc_is_task='file'
					) as tc_info
					where
					otr.otm_testcase_link_tl_seq=tc_info.tl_seq
					group by tc_info.otm_testcase_plan_tp_seq
				) as tc_info
				on
				op.tp_seq=tc_info.otm_testcase_plan_tp_seq
				where op.otm_project_pr_seq='$pr_seq'
			) as a,
			(
				select
					ol.otm_testcase_plan_tp_seq,
					count(*) as tc_cnt
				from
				otm_testcase as ot, otm_testcase_link as ol
				where
				ot.otm_project_pr_seq='$pr_seq' and
				ot.tc_seq=ol.otm_testcase_tc_seq and
				ot.tc_is_task='file'
				group by otm_testcase_plan_tp_seq
			) as b
			where
			a.tp_seq=b.otm_testcase_plan_tp_seq
		";

		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}
		return $arr;
	}

	function get_report_plan_result_summary_data($data,$plan_tc_result_columns="")
	{
		$pr_seq = $data['pr_seq'];
		$tp_seq = $data['tp_seq'];

		$field_array = array();
		$subQuery = "";
		if(sizeof($plan_tc_result_columns)>0){
			for($i=0;$i<sizeof($plan_tc_result_columns);$i++){
				$plan_info = $plan_tc_result_columns[$i];

				for($j=0;$j<sizeof($plan_info['subColumn']);$j++){
					$pco_seq = $plan_info['subColumn'][$j]['dataIndex'];
					$field_name = "_".$pco_seq;

					array_push($field_array,$field_name);
					$subQuery .= ",sum(case when otr.otm_project_code_pco_seq=$pco_seq then 1 else 0 end) as '$field_name'";
				}
			}
		}
		$tp_quy = "";
		if($tp_seq > 0){
			$tp_quy = " and ol.otm_testcase_plan_tp_seq='$tp_seq'";
		}


		$str_sql = "
			select count(b.tc_cnt) as tc_cnt,a.* from
			(
				select
					count(*) as result_cnt
					$subQuery
				from
				otm_testcase_result as otr,
				(
					select
						ot.tc_seq,ol.tl_seq
					from
					otm_testcase as ot, otm_testcase_link as ol
					where
					ot.otm_project_pr_seq='$pr_seq' and
					ot.tc_seq=ol.otm_testcase_tc_seq and
					ot.tc_is_task='file' $tp_quy
					group by tc_seq,tl_seq
				) as tc_info
				where
				otr.otm_testcase_link_tl_seq=tc_info.tl_seq
			) as a,
			(
				select
					sum(tc_seq) as tc_cnt
				from
				otm_testcase as ot, otm_testcase_link as ol
				where
				ot.otm_project_pr_seq='$pr_seq' and
				ot.tc_seq=ol.otm_testcase_tc_seq and
				ot.tc_is_task='file' $tp_quy
				group by tc_seq
			) as b
		";

		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}
		return $arr;
	}

	function get_location($pid){
		$exp_pid = explode('_', $pid);

		if($exp_pid[1] === '0' || $exp_pid[1] === ''){
			$location_reverse = array_reverse($this->location_path);
			return implode("/",$location_reverse);
		}
		for($j=0;$j<sizeof($this->tmp_array);$j++){
			if($this->tmp_array[$j]['id'] == $pid){
				array_push($this->location_path,$this->tmp_array[$j]['subject']);
				return $this->get_location($this->tmp_array[$j]['pid']);
				break;
			}
		}
	}

	function putTestCaseItem($id){
		$my_pid=0;
		for($j=0;$j<sizeof($this->tmp_array);$j++){
			if($this->tmp_array[$j]['pid'] == $id){
				$my_pid = $this->tmp_array[$j]['pid'];
				break;
			}
		}
		for($j=0;$j<sizeof($this->tmp_array);$j++){
			if($this->tmp_array[$j]['pid'] == $id){
				$this->tmp_array[$j]['location'] = $this->get_location($my_pid);
				$this->location_path=array();

				array_push($this->return_array,$this->tmp_array[$j]);
				$this->putTestCaseItem($this->tmp_array[$j]['id']);
			}
		}
	}

	function get_plan_tc_result_data($data,$plan_tc_result_columns="")
	{
		$pr_seq = $data['pr_seq'];
		$field_array = array();
		$subQuery = "";
		if(sizeof($plan_tc_result_columns)>0){
			for($i=0;$i<sizeof($plan_tc_result_columns);$i++){
				$plan_info = $plan_tc_result_columns[$i];
				$plan_seq = $plan_info['plan_seq'];

				for($j=0;$j<sizeof($plan_info['subColumn']);$j++){
					$pco_seq = $plan_info['subColumn'][$j]['dataIndex'];
					$field_name = "_".$plan_seq."_".$pco_seq;

					array_push($field_array,$field_name);

					$subQuery .= ",sum(case when otr.otm_project_code_pco_seq=$pco_seq and otl.otm_testcase_plan_tp_seq=$plan_seq then 1 else 0 end) as '$field_name'";
				}

			}
		}
		//return $subQuery;

		/*$str_sql = "
			select a.*,b.df_cnt,c.close_cnt from
			(
				select * from
				(
					select tc_seq,tc_subject,tc_inp_id,tc_inp_pid,tc_is_task,tc_out_id from otm_testcase where otm_project_pr_seq='$pr_seq'
				) as a
				left outer join
				(
					select a.*,b.pco_name as last_result,b.pco_seq,b.group_pco_seq from
					(
						select
							otl.otm_testcase_tc_seq
							,max(otr.otm_testcase_link_tl_seq) as tl_seq
							$subQuery
						from
							otm_project_code as opc,otm_testcase_result as otr, otm_testcase_link otl
						where
							opc.otm_project_pr_seq='$pr_seq' and
							opc.pco_type='tc_result' and
							otl.tl_seq=otr.otm_testcase_link_tl_seq and
							opc.pco_seq=otr.otm_project_code_pco_seq
						group by otl.otm_testcase_tc_seq
					) as a
					left outer join
					(
						select
							a.*,group_concat(pco_seq) as group_pco_seq
						from
						(
							select
								ot.tc_seq,opc.pco_name,otr.otm_testcase_link_tl_seq,pco_seq
							from
							otm_project_code as opc, otm_testcase_result as otr, otm_testcase_link as otl,otm_testcase as ot
							where
								opc.pco_seq=otr.otm_project_code_pco_seq and
								opc.otm_project_pr_seq='$pr_seq' and
								opc.pco_type='tc_result' and
								otl.tl_seq=otr.otm_testcase_link_tl_seq and
								ot.tc_seq = otl.otm_testcase_tc_seq and
								ot.otm_project_pr_seq='$pr_seq'
							order by tc_seq asc,otr.regdate desc
						) as a
						group by a.tc_seq
					) as b
					on
					a.otm_testcase_tc_seq = b.tc_seq
				) as b
				on
				a.tc_seq=b.otm_testcase_tc_seq
			) as a
			left outer join
			(
				select
					otl.otm_testcase_tc_seq,
					count(*) as df_cnt
				from
					otm_defect as od, otm_testcase_result as otr, otm_testcase_link as otl
				where
					od.otm_project_pr_seq = '$pr_seq' and
					od.df_seq = otr.otm_defect_df_seq and
					otr.otm_testcase_link_tl_seq = otl.tl_seq
				group by otl.otm_testcase_tc_seq
			) as b
			on
			a.tc_seq=b.otm_testcase_tc_seq
			left outer join
			(
				select
					otl.otm_testcase_tc_seq as tc_seq,count(*) as close_cnt
				from
					otm_defect as od, otm_project_code as opc, otm_defect_assign oda,otm_testcase_result otr,otm_testcase_link otl
				where
					od.otm_project_pr_seq='$pr_seq' and
					opc.otm_project_pr_seq='$pr_seq' and
					opc.pco_type='status' and
					opc.pco_is_required='Y' and
					oda.dc_current_status_co_seq=opc.pco_seq and
					od.df_seq = oda.otm_defect_df_seq and
					od.df_seq = otr.otm_defect_df_seq and
					otr.otm_testcase_link_tl_seq=otl.tl_seq
				group by otl.otm_testcase_tc_seq
			) as c
			on
			a.tc_seq=c.tc_seq
		";*/

		$str_sql = "
			select a.*,b.df_cnt,c.close_cnt from
			(
				select * from
				(
					select tc_seq,tc_subject,tc_inp_id,tc_inp_pid,tc_is_task,tc_out_id from otm_testcase where otm_project_pr_seq='$pr_seq'
				) as a
				left outer join
				(
					select a.*,b.pco_name as last_result,b.pco_seq from
					(
						select
							otl.otm_testcase_tc_seq
							,max(otr.otm_testcase_link_tl_seq) as tl_seq
							$subQuery
						from
							otm_project_code as opc,otm_testcase_result as otr, otm_testcase_link otl
						where
							opc.otm_project_pr_seq='$pr_seq' and
							opc.pco_type='tc_result' and
							otl.tl_seq=otr.otm_testcase_link_tl_seq and
							opc.pco_seq=otr.otm_project_code_pco_seq
						group by otl.otm_testcase_tc_seq
					) as a
					left outer join
					(
						select
							a.*
						from
						(
							select
								ot.tc_seq,opc.pco_name,otr.otm_testcase_link_tl_seq,pco_seq
							from
							otm_project_code as opc, otm_testcase_result as otr, otm_testcase_link as otl,otm_testcase as ot
							where
								opc.pco_seq=otr.otm_project_code_pco_seq and
								opc.otm_project_pr_seq='$pr_seq' and
								opc.pco_type='tc_result' and
								otl.tl_seq=otr.otm_testcase_link_tl_seq and
								ot.tc_seq = otl.otm_testcase_tc_seq and
								ot.otm_project_pr_seq='$pr_seq'
							order by tc_seq asc,otr.regdate desc
						) as a
						group by a.tc_seq
					) as b
					on
					a.otm_testcase_tc_seq = b.tc_seq
				) as b
				on
				a.tc_seq=b.otm_testcase_tc_seq
			) as a
			left outer join
			(
				select
					otl.otm_testcase_tc_seq,
					count(*) as df_cnt
				from
					otm_defect as od, otm_testcase_result as otr, otm_testcase_link as otl
				where
					od.otm_project_pr_seq = '$pr_seq' and
					od.df_seq = otr.otm_defect_df_seq and
					otr.otm_testcase_link_tl_seq = otl.tl_seq
				group by otl.otm_testcase_tc_seq
			) as b
			on
			a.tc_seq=b.otm_testcase_tc_seq
			left outer join
			(
				select
					otl.otm_testcase_tc_seq as tc_seq,count(*) as close_cnt
				from
					otm_defect as od, otm_project_code as opc, otm_defect_assign oda,otm_testcase_result otr,otm_testcase_link otl
				where
					od.otm_project_pr_seq='$pr_seq' and
					opc.otm_project_pr_seq='$pr_seq' and
					opc.pco_type='status' and
					opc.pco_is_required='Y' and
					oda.dc_current_status_co_seq=opc.pco_seq and
					od.df_seq = oda.otm_defect_df_seq and
					od.df_seq = otr.otm_defect_df_seq and
					otr.otm_testcase_link_tl_seq=otl.tl_seq
				group by otl.otm_testcase_tc_seq
			) as c
			on
			a.tc_seq=c.tc_seq
		";


		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$temp_arr['location']	= "";
			$temp_arr['tc_out_id']			= $row->tc_out_id;
			$temp_arr['tc_seq']		= $row->tc_seq;
			$temp_arr['pid']		= $row->tc_inp_pid;
			$temp_arr['subject']	= $row->tc_subject;
			$temp_arr['id']			= $row->tc_inp_id;

			$temp_arr['tc_is_task']	= $row->tc_is_task;
			$temp_arr['pco_seq']	= $row->pco_seq;
			//$temp_arr['group_pco_seq']	= $row->group_pco_seq;
			$temp_arr['df_cnt']	= $row->df_cnt;
			$temp_arr['close_cnt']	= $row->close_cnt;
			$temp_arr['last_result']	= $row->last_result;

			for($i=0;$i<sizeof($field_array);$i++){
				$temp_arr[$field_array[$i]]	= $row->$field_array[$i];
			}

			$exp_pid = explode('_', $temp_arr['pid']);

			if($exp_pid[1] === '0'){
				array_push($this->root_array,$temp_arr);
			}
			array_push($this->tmp_array,$temp_arr);
		}

		for($i=0;$i<sizeof($this->root_array);$i++){
			array_push($this->return_array,$this->root_array[$i]);

			$this->putTestCaseItem($this->root_array[$i]['id']);
		}
		$folder_except = array();
		for($i=0;$i<sizeof($this->return_array);$i++){
			if($this->return_array[$i]['tc_is_task'] != "folder"){
				unset($this->return_array[$i]['pid']);
				unset($this->return_array[$i]['tc_is_task']);
				array_push($folder_except,$this->return_array[$i]);
			}
		}
		return $folder_except;
	}

	function get_plan_defect_data($data){
		$pr_seq = $data['pr_seq'];
		$tp_seq = $data['tp_seq'];

		$str_sql_code = "select * from otm_project_code where otm_project_pr_seq='$pr_seq'";
		$query = $this->db->query($str_sql_code);
		foreach ($query->result() as $row)
		{
			$code_arr[$row->pco_seq]['pco_name'] = $row->pco_name;
		}

		$where_quy = "";
		if($tp_seq){
			$where_quy = " and tp_seq='$tp_seq'";
		}else{
			$where_quy = " and tp_seq is null";
		}

		$str_sql="
			select a.* from
			(
				select
					a.*,b.mb_name as writer_name
				from
				(
					select
						a.*,b.mb_name as df_assign_member
					from
					(
						select
							a.*,b.dc_to,b.dc_current_status_co_seq as df_status
						from
						(
							select
								a.df_seq,a.otm_project_pr_seq,a.otm_testcase_result_tr_seq,a.df_id,a.df_subject,
								a.df_severity,a.df_priority,a.df_frequency,a.writer,a.regdate,a.last_writer,a.last_update,
								(select max(dc_seq) from otm_defect_assign where a.df_seq=otm_defect_df_seq) as dc_seq,
								b.otm_testcase_link_tl_seq as tl_seq,
								c.otm_testcase_tc_seq as tc_seq,
								d.tc_inp_id,
								d.tc_out_id,
								tp.tp_subject,tp.tp_seq
							from
							otm_defect as a
							left outer join
							otm_testcase_result as b
							on a.otm_testcase_result_tr_seq=b.tr_seq
							left outer join
							otm_testcase_link as c
							on b.otm_testcase_link_tl_seq = c.tl_seq
							left outer join
							otm_testcase as d
							on d.tc_seq=c.otm_testcase_tc_seq
							left outer join
							otm_testcase_plan as tp
							on tp.tp_seq = c.otm_testcase_plan_tp_seq
						) as a
						left outer join
							otm_defect_assign as b
						on
							a.df_seq=b.otm_defect_df_seq and a.dc_seq=b.dc_seq
						order by df_seq desc
					) as a
					left outer join
						otm_member as b
					on
						a.dc_to=b.mb_email
				) as a
				left outer join
					otm_member as b
				on
					a.writer=b.mb_email
			) as a
			where a.otm_project_pr_seq='$pr_seq' $where_quy
		";
		$str_sql_cnt = "select count(*) as cnt from ($str_sql) as a";
		$query = $this->db->query($str_sql_cnt);
		$cnt_result = $query->result();

		$str_sql = "select * from ($str_sql) as a order by a.df_seq desc";// $limitSql
		$query = $this->db->query($str_sql);
		$i=0;
		foreach ($query->result() as $row)
		{
			$arr[$i] = $row;
			$arr[$i]->status_name = $code_arr[$row->df_status]['pco_name'];
			$arr[$i]->severity_name = $code_arr[$row->df_severity]['pco_name'];
			$arr[$i]->priority_name = $code_arr[$row->df_priority]['pco_name'];
			$arr[$i]->frequency_name = $code_arr[$row->df_frequency]['pco_name'];
			$i++;
		}
		return "{success:true,totalCount: ".$cnt_result[0]->cnt.", data:".json_encode($arr)."}";
	}

	function get_defect_data($data)
	{
		$pr_seq = $data['pr_seq'];
		$tp_seq = $data['tp_seq'];

		$start = $data['start'];
		$limit = $data['limit'];
		if($start != null && $limit != null){
			$limitSql = " limit $limit OFFSET $start ";
		}else{
			$limitSql = "";
		}

		$str_sql_code = "select * from otm_project_code where otm_project_pr_seq='$pr_seq'";
		$query = $this->db->query($str_sql_code);
		foreach ($query->result() as $row)
		{
			$code_arr[$row->pco_seq]['pco_name'] = $row->pco_name;
		}

		$str_sql="
			select a.*,b.tc_cnt from
			(
				select a.* from
				(
					select
						a.*,b.mb_name as writer_name
					from
					(
						select
							a.*,b.mb_name as df_assign_member
						from
						(
							select
								a.*,b.dc_to,b.dc_current_status_co_seq as df_status
							from
							(
								select
									a.df_seq,a.otm_project_pr_seq,a.otm_testcase_result_tr_seq,a.df_id,a.df_subject,
									a.df_severity,a.df_priority,a.df_frequency,a.writer,a.regdate,a.last_writer,a.last_update,
									(select max(dc_seq) from otm_defect_assign where a.df_seq=otm_defect_df_seq) as dc_seq,
									b.otm_testcase_link_tl_seq as tl_seq,
									c.otm_testcase_tc_seq as tc_seq,
									d.tc_inp_id,
									d.tc_out_id,
									tp.tp_subject,tp.tp_seq
								from
								(select * from otm_defect where otm_project_pr_seq='$pr_seq') as a
								left outer join
								otm_testcase_result as b
								on a.otm_testcase_result_tr_seq=b.tr_seq
								left outer join
								otm_testcase_link as c
								on b.otm_testcase_link_tl_seq = c.tl_seq
								left outer join
								(select * from otm_testcase where otm_project_pr_seq='$pr_seq') as d
								on d.tc_seq=c.otm_testcase_tc_seq
								left outer join
								(select * from otm_testcase_plan where otm_project_pr_seq='$pr_seq') as tp
								on tp.tp_seq = c.otm_testcase_plan_tp_seq
							) as a
							left outer join
								otm_defect_assign as b
							on
								a.df_seq=b.otm_defect_df_seq and a.dc_seq=b.dc_seq
							order by df_seq desc
						) as a
						left outer join
							otm_member as b
						on
							a.dc_to=b.mb_email
					) as a
					left outer join
						otm_member as b
					on
						a.writer=b.mb_email
				) as a
				where a.otm_project_pr_seq='$pr_seq'
			) as a
			left outer join
			(
				select
					otr.otm_defect_df_seq,count(*) as tc_cnt
				from
					(select * from otm_testcase where otm_project_pr_seq='$pr_seq') as ot, otm_testcase_link as otl, otm_testcase_result otr
				where
				ot.otm_project_pr_seq='$pr_seq' and
				ot.tc_seq=otl.otm_testcase_tc_seq and
				otl.tl_seq = otr.otm_testcase_link_tl_seq
				group by otr.otm_defect_df_seq
			) as b
			on a.df_seq=b.otm_defect_df_seq
		";

		$str_sql_cnt = "select count(*) as cnt from ($str_sql) as a";
		$query = $this->db->query($str_sql_cnt);
		$cnt_result = $query->result();

		$str_sql = "select * from ($str_sql) as a order by a.df_seq desc $limitSql";// $limitSql
		$query = $this->db->query($str_sql);
		$i=0;
		foreach ($query->result() as $row)
		{
			$arr[$i] = $row;
			$arr[$i]->status_name = $code_arr[$row->df_status]['pco_name'];
			$arr[$i]->severity_name = $code_arr[$row->df_severity]['pco_name'];
			$arr[$i]->priority_name = $code_arr[$row->df_priority]['pco_name'];
			$arr[$i]->frequency_name = $code_arr[$row->df_frequency]['pco_name'];
			$i++;
		}
		if($data['return_type'] == "array"){
			return $arr;
		}else{
			return "{success:true,totalCount: ".$cnt_result[0]->cnt.", data:".json_encode($arr)."}";
		}
	}

	function get_plan_testcase_result_list($data,$plan_tc_result_columns="")
	{
		$pr_seq = $data['pr_seq'];
		$tp_seq = $data['tp_seq'];

		$field_array = array();
		$subQuery = "";

		if(sizeof($plan_tc_result_columns)>0){
			for($i=0;$i<sizeof($plan_tc_result_columns);$i++){
				$plan_info = $plan_tc_result_columns[$i];
				$plan_seq = $plan_info['plan_seq'];

				for($j=0;$j<sizeof($plan_info['subColumn']);$j++){
					$pco_seq = $plan_info['subColumn'][$j]['dataIndex'];
					$field_name = "_".$pco_seq;
					array_push($field_array,$field_name);
					$subQuery .= ",sum(case when otr.otm_project_code_pco_seq=$pco_seq and otl.otm_testcase_plan_tp_seq=$tp_seq then 1 else 0 end) as '$field_name'";
				}
			}
		}

		/*$str_sql = "
			select a.*,b.df_cnt,c.close_cnt from
			(
				select * from
				(
					select
						ot.tc_seq,ot.tc_subject,ot.tc_inp_id,otl.tl_inp_pid,ot.tc_is_task,ot.tc_out_id
					from
						otm_testcase as ot, otm_testcase_link as otl
					where
						otm_project_pr_seq='$pr_seq' and
						ot.tc_seq=otl.otm_testcase_tc_seq and
						otl.otm_testcase_plan_tp_seq='$tp_seq'
					order by otl.tl_ord asc
				) as a
				left outer join
				(
					select a.*,b.pco_name as last_result,b.pco_seq,b.group_pco_seq from
					(
						select
							otl.otm_testcase_tc_seq
							$subQuery
						from
							otm_project_code as opc,otm_testcase_result as otr, otm_testcase_link otl
						where
							opc.otm_project_pr_seq='$pr_seq' and
							opc.pco_type='tc_result' and
							otl.tl_seq=otr.otm_testcase_link_tl_seq and
							opc.pco_seq=otr.otm_project_code_pco_seq and
							otl.otm_testcase_plan_tp_seq='$tp_seq'
						group by otl.otm_testcase_tc_seq
					) as a
					left outer join
					(
						select
							a.*,group_concat(pco_seq) as group_pco_seq
						from
						(
							select
								ot.tc_seq,opc.pco_name,otr.otm_testcase_link_tl_seq,pco_seq
							from
							otm_project_code as opc, otm_testcase_result as otr, otm_testcase_link as otl,otm_testcase as ot
							where
								opc.pco_seq=otr.otm_project_code_pco_seq and
								opc.otm_project_pr_seq='$pr_seq' and
								opc.pco_type='tc_result' and
								otl.tl_seq=otr.otm_testcase_link_tl_seq and
								ot.tc_seq = otl.otm_testcase_tc_seq and
								ot.otm_project_pr_seq='$pr_seq' and
								otl.otm_testcase_plan_tp_seq='$tp_seq'
							order by tc_seq asc,otr.regdate desc
						) as a
						group by a.tc_seq
					) as b
					on
					a.otm_testcase_tc_seq = b.tc_seq
				) as b
				on
				a.tc_seq=b.otm_testcase_tc_seq
			) as a
			left outer join
			(
				select
					otl.otm_testcase_tc_seq,
					count(*) as df_cnt
				from
					otm_defect as od, otm_testcase_result as otr, otm_testcase_link as otl
				where
					od.otm_project_pr_seq = '$pr_seq' and
					od.df_seq = otr.otm_defect_df_seq and
					otr.otm_testcase_link_tl_seq = otl.tl_seq and
					otl.otm_testcase_plan_tp_seq='$tp_seq'
				group by otl.otm_testcase_tc_seq
			) as b
			on
			a.tc_seq=b.otm_testcase_tc_seq
			left outer join
			(
				select
					otl.otm_testcase_tc_seq as tc_seq,count(*) as close_cnt
				from
					otm_defect as od, otm_project_code as opc, otm_defect_assign oda,otm_testcase_result otr,otm_testcase_link otl
				where
					od.otm_project_pr_seq='$pr_seq' and
					opc.otm_project_pr_seq='$pr_seq' and
					opc.pco_type='status' and
					opc.pco_is_required='Y' and
					oda.dc_current_status_co_seq=opc.pco_seq and
					od.df_seq = oda.otm_defect_df_seq and
					od.df_seq = otr.otm_defect_df_seq and
					otr.otm_testcase_link_tl_seq=otl.tl_seq and
					otl.otm_testcase_plan_tp_seq='$tp_seq'
				group by otl.otm_testcase_tc_seq
			) as c
			on
			a.tc_seq=c.tc_seq
		";*/

		$str_sql = "
			select a.*,b.df_cnt,c.close_cnt from
			(
				select * from
				(
					select
						ot.tc_seq,ot.tc_subject,ot.tc_inp_id,otl.tl_inp_pid,ot.tc_is_task,ot.tc_out_id
					from
						otm_testcase as ot, otm_testcase_link as otl
					where
						otm_project_pr_seq='$pr_seq' and
						ot.tc_seq=otl.otm_testcase_tc_seq and
						otl.otm_testcase_plan_tp_seq='$tp_seq'
					order by otl.tl_ord asc
				) as a
				left outer join
				(
					select a.*,b.pco_name as last_result,b.pco_seq from
					(
						select
							otl.otm_testcase_tc_seq
							$subQuery
						from
							otm_project_code as opc,otm_testcase_result as otr, otm_testcase_link otl
						where
							opc.otm_project_pr_seq='$pr_seq' and
							opc.pco_type='tc_result' and
							otl.tl_seq=otr.otm_testcase_link_tl_seq and
							opc.pco_seq=otr.otm_project_code_pco_seq and
							otl.otm_testcase_plan_tp_seq='$tp_seq'
						group by otl.otm_testcase_tc_seq
					) as a
					left outer join
					(
						select
							a.*
						from
						(
							select
								ot.tc_seq,opc.pco_name,otr.otm_testcase_link_tl_seq,pco_seq
							from
							otm_project_code as opc, otm_testcase_result as otr, otm_testcase_link as otl,otm_testcase as ot
							where
								opc.pco_seq=otr.otm_project_code_pco_seq and
								opc.otm_project_pr_seq='$pr_seq' and
								opc.pco_type='tc_result' and
								otl.tl_seq=otr.otm_testcase_link_tl_seq and
								ot.tc_seq = otl.otm_testcase_tc_seq and
								ot.otm_project_pr_seq='$pr_seq' and
								otl.otm_testcase_plan_tp_seq='$tp_seq'
							order by tc_seq asc,otr.regdate desc
						) as a
						group by a.tc_seq
					) as b
					on
					a.otm_testcase_tc_seq = b.tc_seq
				) as b
				on
				a.tc_seq=b.otm_testcase_tc_seq
			) as a
			left outer join
			(
				select
					otl.otm_testcase_tc_seq,
					count(*) as df_cnt
				from
					otm_defect as od, otm_testcase_result as otr, otm_testcase_link as otl
				where
					od.otm_project_pr_seq = '$pr_seq' and
					od.df_seq = otr.otm_defect_df_seq and
					otr.otm_testcase_link_tl_seq = otl.tl_seq and
					otl.otm_testcase_plan_tp_seq='$tp_seq'
				group by otl.otm_testcase_tc_seq
			) as b
			on
			a.tc_seq=b.otm_testcase_tc_seq
			left outer join
			(
				select
					otl.otm_testcase_tc_seq as tc_seq,count(*) as close_cnt
				from
					otm_defect as od, otm_project_code as opc, otm_defect_assign oda,otm_testcase_result otr,otm_testcase_link otl
				where
					od.otm_project_pr_seq='$pr_seq' and
					opc.otm_project_pr_seq='$pr_seq' and
					opc.pco_type='status' and
					opc.pco_is_required='Y' and
					oda.dc_current_status_co_seq=opc.pco_seq and
					od.df_seq = oda.otm_defect_df_seq and
					od.df_seq = otr.otm_defect_df_seq and
					otr.otm_testcase_link_tl_seq=otl.tl_seq and
					otl.otm_testcase_plan_tp_seq='$tp_seq'
				group by otl.otm_testcase_tc_seq
			) as c
			on
			a.tc_seq=c.tc_seq
		";

		$query = $this->db->query($str_sql);

		foreach ($query->result() as $row)
		{
			$temp_arr['location']	= "";
			$temp_arr['tc_seq']			= $row->tc_seq;
			$temp_arr['id']			= $row->tc_inp_id;
			$temp_arr['tc_out_id']			= $row->tc_out_id;
			$temp_arr['pid']		= $row->tl_inp_pid;
			$temp_arr['subject']	= $row->tc_subject;
			$temp_arr['tc_is_task']	= $row->tc_is_task;
			$temp_arr['df_cnt']		= $row->df_cnt;
			$temp_arr['close_cnt']	= $row->close_cnt;
			$temp_arr['last_result']	= $row->last_result;
			$temp_arr['pco_seq']	= $row->pco_seq;
			//$temp_arr['group_pco_seq']	= $row->group_pco_seq;

			for($i=0;$i<sizeof($field_array);$i++){
				$temp_arr[$field_array[$i]]	= $row->$field_array[$i];
			}

			$exp_pid = explode('_', $temp_arr['pid']);

			if($exp_pid[1] === '0'){
				array_push($this->root_array,$temp_arr);
			}
			array_push($this->tmp_array,$temp_arr);
		}

		for($i=0;$i<sizeof($this->root_array);$i++){
			array_push($this->return_array,$this->root_array[$i]);

			$this->putTestCaseItem($this->root_array[$i]['id']);
		}
		$folder_except = array();
		for($i=0;$i<sizeof($this->return_array);$i++){
			if($this->return_array[$i]['tc_is_task'] != "folder"){
				unset($this->return_array[$i]['pid']);
				unset($this->return_array[$i]['tc_is_task']);
				array_push($folder_except,$this->return_array[$i]);
			}
		}
		return $folder_except;
	}

	function get_defect_scurve_list($data){
		$pr_seq = $data['pr_seq'];
		$data_unit = $data['data_unit'];
		$data_unit_arr[0]['unit'] = $data_unit;

		$startdate = $data['sdate'];
		$enddate = $data['edate'];
		$startdate = date("Y-m-d H:i:s",strtotime ("-1 days", strtotime($startdate)));

		$groupQuy="";
		$fieldQuy="";
		switch($data_unit){
			case "year":
				$fieldQuy = "'%Y'";
				$groupQuy = "'%Y'";
			break;
			case "month":
				$fieldQuy = "'%Y-%m'";
				$groupQuy = "'%Y%m'";
			break;
			case "week":
				$fieldQuy = "'%Y-%m-%U(week)'";
				$groupQuy = "'%Y%m%U'";
			break;
			case "day":
			default:
				$fieldQuy = "'%Y-%m-%d'";
				$groupQuy = "'%Y%m%d'";
			break;
		}
		$str_sql = "
					select a.date as name,b.new_cnt as field1,c.close_cnt as field2
					from
					(
						select date_format(a.date,$fieldQuy) as date from
						(select adddate('1970-01-01',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) date from
							(select 0 t0 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
							(select 0 t1 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
							(select 0 t2 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
							(select 0 t3 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
							(select 0 t4 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) as a
						where date between '$startdate' and '$enddate'
						group by date_format(date,$groupQuy)
					) as a
					left outer join
					(
						select
							date_format(regDate,$fieldQuy) as date,
							count(*) as new_cnt
						from
						otm_defect
						where
						otm_project_pr_seq='$pr_seq' and regdate>='$startdate' and regdate<='$enddate'
						group by date_format(regDate,$groupQuy)
					) as b
					on
					a.date=b.date
					left outer join
					(
						select
							date_format(last_update,$fieldQuy) as date,
							count(*) as close_cnt
						from
						(
							select
								a.*,b.dc_current_status_co_seq as df_status
							from
							(
								select
									df_seq,last_update,(select max(dc_seq) from otm_defect_assign where df_seq=otm_defect_df_seq) as dc_seq
								from
									otm_defect
								where
									otm_project_pr_seq='$pr_seq'
							) as a
							left outer join
							otm_defect_assign as b
							on
								a.df_seq=b.otm_defect_df_seq and a.dc_seq=b.dc_seq
							order by df_seq desc
						) as a,
						(
							select pco_seq from otm_project_code where otm_project_pr_seq='$pr_seq' and pco_type='status' and pco_is_required='Y'
						) as b
						where a.df_status=b.pco_seq
						group by date_format(last_update,$groupQuy)
					) as c
					on
					a.date=c.date
		";

		$str_sql_cnt = "select count(*) as cnt from ($str_sql) as a";
		$query = $this->db->query($str_sql_cnt);
		$cnt_result = $query->result();

		$query = $this->db->query($str_sql);

		$arr = array();
		$arr[0]['name'] = "Start";
		$arr[0]['field1'] = "0";
		$arr[0]['field2'] = "0";
		$i=1;

		foreach ($query->result() as $row)
		{
			$arr[$i]['name'] = $row->name;
			$arr[$i]['field1'] = $arr[$i-1]['field1']+$row->field1;
			$arr[$i]['field2'] = $arr[$i-1]['field2']+$row->field2;

			$i++;
		}
		$arr[$i]['name'] = "End";
		$arr[$i]['field1'] = $arr[$i-1]['field1'];
		$arr[$i]['field2'] = $arr[$i-1]['field2'];

		if($data['return_type'] == "array"){
			return $arr;
		}else{
			return "{success:true,totalCount:153,unitInfo:".json_encode($data_unit_arr).", data:".json_encode($arr)."}";
		}
	}

	function get_suite_defect_distribution_list($data){
		$pr_seq = $data['pr_seq'];
		$tp_seq = $data['tp_seq'];

		$str_sql = "
			select
				otl.tl_inp_pid as tl_pid,count(*) as df_cnt
			from
				otm_defect as od,otm_testcase_result as otr,otm_testcase_link as otl
			where
				od.otm_project_pr_seq='$pr_seq' and
				od.otm_testcase_result_tr_seq = otr.tr_seq and
				otl.tl_seq=otr.otm_testcase_link_tl_seq and
				otl.otm_testcase_plan_tp_seq='$tp_seq'
			group by otl.tl_inp_pid
		";
		$defect_cnt_arr = "";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$defect_cnt_arr[$row->tl_pid]	= $row->df_cnt;
		}

		$str_sql = "
			select
				ot.tc_seq,ot.tc_subject,ot.tc_inp_id,otl.tl_inp_pid,ot.tc_is_task
			from
				otm_testcase as ot, otm_testcase_link as otl
			where
				otm_project_pr_seq='$pr_seq' and
				ot.tc_seq=otl.otm_testcase_tc_seq and
				otl.otm_testcase_plan_tp_seq='$tp_seq'  and
				ot.tc_is_task='folder'
			order by otl.tl_ord asc
		";

		$query = $this->db->query($str_sql);

		foreach ($query->result() as $row)
		{
			$temp_arr['location']	= "";
			$temp_arr['id']			= $row->tc_inp_id;
			$temp_arr['pid']		= $row->tl_inp_pid;
			$temp_arr['subject']	= $row->tc_subject;
			$temp_arr['tc_is_task']	= $row->tc_is_task;
			$temp_arr['cnt']		= 0;

			$exp_pid = explode('_', $temp_arr['pid']);

			if($exp_pid[1] === '0'){
				array_push($this->root_array,$temp_arr);
			}
			array_push($this->tmp_array,$temp_arr);
		}
		for($i=0;$i<sizeof($this->root_array);$i++){
			array_push($this->return_array,$this->root_array[$i]);

			$this->putTestCaseItem($this->root_array[$i]['id']);
		}
		$folder_except = array();
		for($i=0;$i<sizeof($this->return_array);$i++){
			if($this->return_array[$i]['tc_is_task'] == "folder"){
				unset($this->return_array[$i]['pid']);
				unset($this->return_array[$i]['tc_is_task']);
				array_push($folder_except,$this->return_array[$i]);
			}
		}
		for($i=0;$i<sizeof($folder_except);$i++){
			if($folder_except[$i]['location']){
				$folder_except[$i]['name'] = "/".$folder_except[$i]['location']."/".$folder_except[$i]['subject'];
			}else{
				$folder_except[$i]['name'] = $folder_except[$i]['location']."/".$folder_except[$i]['subject'];
			}
			unset($folder_except[$i]['subject']);

			if($defect_cnt_arr[$folder_except[$i]['id']]){
				$folder_except[$i]['cnt'] = $defect_cnt_arr[$folder_except[$i]['id']];
			}
		}

		if($data['return_type'] == "array"){
			return $folder_except;
		}else{
			return '{success:true,data:'.json_encode($folder_except).'}';
		}
	}

	function get_defect_data_excel($data)
	{
		$return_array_excel = array();
		$data['return_type'] = "array";
		$temp_array = $this->get_defect_data($data);
		for($i=0;$i<sizeof($temp_array);$i++){
			$return_array_excel[$i]['tp_subject'] = $temp_array[$i]->tp_subject;
			$return_array_excel[$i]['df_id'] = $temp_array[$i]->df_id;
			$return_array_excel[$i]['df_subject'] = $temp_array[$i]->df_subject;

			$return_array_excel[$i]['tc_cnt'] = $temp_array[$i]->tc_cnt;
			$return_array_excel[$i]['df_assign_member'] = $temp_array[$i]->df_assign_member;

			$return_array_excel[$i]['status_name'] = $temp_array[$i]->status_name;
			$return_array_excel[$i]['severity_name'] = $temp_array[$i]->severity_name;
			$return_array_excel[$i]['priority_name'] = $temp_array[$i]->priority_name;
			$return_array_excel[$i]['frequency_name'] = $temp_array[$i]->frequency_name;
			$return_array_excel[$i]['regdate'] = $temp_array[$i]->regdate;
			$return_array_excel[$i]['writer_name'] = $temp_array[$i]->writer_name;
		}
		return $return_array_excel;
	}

	function get_defect_scurve_excel($data)
	{
		$data['return_type'] = 'array';
		return $this->get_defect_scurve_list($data);
	}

	function get_suite_defect_distribution_excel($data)
	{
		$data['return_type'] = 'array';

		$suite_defect_distribution = $this->get_suite_defect_distribution_list($data);

		for($i=0;$i<sizeof($suite_defect_distribution);$i++){
			unset($suite_defect_distribution[$i]['id']);
			unset($suite_defect_distribution[$i]['location']);

			$suite_defect_distribution[$i]['location'] = $suite_defect_distribution[$i]['name'];
			$suite_defect_distribution[$i]['df_cnt'] = $suite_defect_distribution[$i]['cnt'];

			unset($suite_defect_distribution[$i]['name']);
			unset($suite_defect_distribution[$i]['cnt']);
		}
		return $suite_defect_distribution;
	}

	function get_plan_tc_result_grid_excel($data){
		$return_array = array();

		$plan_tc_result_columns	= $this->get_plan_tc_result_columns($data);
		$plan_tc_result_data	= $this->get_plan_tc_result_data($data,$plan_tc_result_columns);

		$pr_seq = $data['pr_seq'];

		//$this->db->query("insert into test set content='$pr_seq'");

		$str_sql_code = "select tp_seq,tp_subject from otm_testcase_plan where otm_project_pr_seq='$pr_seq' order by tp_seq desc";
		$query = $this->db->query($str_sql_code);
		$i=0;
		foreach ($query->result() as $row)
		{
			$tp_arr[$i]['tp_seq'] = "_".$row->tp_seq;
			$tp_arr[$i]['tp_subject'] = $row->tp_subject;
			$i++;
		}

		$str_sql_code = "select pco_seq,pco_name from otm_project_code where otm_project_pr_seq='$pr_seq' and pco_type='tc_result'";
		$query = $this->db->query($str_sql_code);
		$i=0;
		foreach ($query->result() as $row)
		{
			$code_arr[$i]['pco_seq'] = "_".$row->pco_seq;
			$code_arr[$i]['pco_name'] = $row->pco_name;
			$i++;
		}

		$merge_arr = "";
		$k=0;
		for($i=0;$i<sizeof($tp_arr);$i++){
			for($j=0;$j<sizeof($code_arr);$j++){
				$merge_arr[$k]['seq'] = $tp_arr[$i]['tp_seq'].$code_arr[$j]['pco_seq'];
				$merge_arr[$k]['name'] = $tp_arr[$i]['tp_subject']."_".$code_arr[$j]['pco_name'];
				$k++;
			}
		}

		for($i=0;$i<sizeof($plan_tc_result_data);$i++){
			unset($plan_tc_result_data[$i]['tc_seq']);
			unset($plan_tc_result_data[$i]['id']);
			unset($plan_tc_result_data[$i]['pco_seq']);
			unset($plan_tc_result_data[$i]['group_pco_seq']);

			for($j=0;$j<sizeof($merge_arr);$j++)
			{
				$plan_tc_result_data[$i][$merge_arr[$j]['name']] = $plan_tc_result_data[$i][$merge_arr[$j]['seq']];
				unset($plan_tc_result_data[$i][$merge_arr[$j]['seq']]);
			}
		}

		return $plan_tc_result_data;
	}

	function get_plan_testcase_result_excel($data)
	{
		$pr_seq = $data['pr_seq'];
		$testcase_result_columns	= $this->report_m->get_testcase_result_summary_columns($data);
		if($data['tp_seq']){
			$plan_testcase_result = $this->report_m->get_plan_testcase_result_list($data,$testcase_result_columns);

			$str_sql_code = "select pco_seq,pco_name from otm_project_code where otm_project_pr_seq='$pr_seq' and pco_type='tc_result'";
			$query = $this->db->query($str_sql_code);
			$i=0;
			foreach ($query->result() as $row)
			{
				$code_arr[$i]['pco_seq'] = "_".$row->pco_seq;
				$code_arr[$i]['pco_name'] = $row->pco_name;
				$i++;
			}

			$return_arr = array();
			for($i=0;$i<sizeof($plan_testcase_result);$i++){
				unset($plan_testcase_result[$i]['tc_seq']);
				unset($plan_testcase_result[$i]['id']);
				unset($plan_testcase_result[$i]['pco_seq']);
				unset($plan_testcase_result[$i]['group_pco_seq']);

				for($j=0;$j<sizeof($code_arr);$j++)
				{
					$plan_testcase_result[$i][$code_arr[$j]['pco_name']] = $plan_testcase_result[$i][$code_arr[$j]['pco_seq']];
					unset($plan_testcase_result[$i][$code_arr[$j]['pco_seq']]);
				}

				array_push($return_arr,$plan_testcase_result[$i]);
			}

			return $return_arr;
		}else{
			return "";
		}
	}

	function get_defect_from_testcase_list($data)
	{
		$pr_seq = $data['pr_seq'];
		$df_seq = $data['df_seq'];

		/*사용자 연관 배열*/
		$str_sql_code = "select * from otm_member";
		$query = $this->db->query($str_sql_code);
		foreach ($query->result() as $row)
		{
			$member_arr[$row->mb_email]['mb_name'] = $row->mb_name;
		}

		$str_sql_code = "select * from otm_project_code where otm_project_pr_seq='$pr_seq'";
		$query = $this->db->query($str_sql_code);
		foreach ($query->result() as $row)
		{
			$code_arr[$row->pco_seq]['pco_name'] = $row->pco_name;
		}

		$str_sql = "
			select
				a.*,b.pco_name as last_result
			from
			(
				select
					ot.*,otp.tp_subject,otl.otm_testcase_tc_seq,
					otr.otm_project_code_pco_seq,
					otr.writer as execution_writer,
					otr.regdate as execution_regdate,
					otl.tl_assign_to as assign_to
				from
					otm_testcase as ot, otm_testcase_link as otl, otm_testcase_result as otr,otm_testcase_plan as otp
				where
					ot.otm_project_pr_seq = '$pr_seq' and
					ot.tc_seq = otl.otm_testcase_tc_seq and
					otl.tl_seq = otr.otm_testcase_link_tl_seq and
					otp.tp_seq = otl.otm_testcase_plan_tp_seq and
					otm_defect_df_seq='$df_seq'
			) as a
			left outer join
			(
				select
					ot.tc_seq,opc.pco_name,otr.otm_testcase_link_tl_seq,pco_seq,group_concat(pco_seq) as group_pco_seq
				from
					otm_project_code as opc, otm_testcase_result as otr, otm_testcase_link as otl,otm_testcase as ot
				where
					opc.pco_seq=otr.otm_project_code_pco_seq and
					opc.otm_project_pr_seq='$pr_seq' and
					opc.pco_type='tc_result' and
					otl.tl_seq=otr.otm_testcase_link_tl_seq and
					ot.tc_seq = otl.otm_testcase_tc_seq and
					ot.otm_project_pr_seq='$pr_seq'
				group by ot.tc_seq
			) as b
			on
			a.otm_testcase_tc_seq=b.tc_seq
			order by execution_regdate desc
		";

		$i=0;
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[$i] = $row;
			$arr[$i]->writer_name = $member_arr[$row->writer]['mb_name'];
			$arr[$i]->pco_name = $code_arr[$row->otm_project_code_pco_seq]['pco_name'];
			$arr[$i]->execution_writer = $member_arr[$row->execution_writer]['mb_name'];
			$arr[$i]->assign_to = $member_arr[$row->assign_to]['mb_name'];
			$i++;
		}
		if($data['return_type'] == "array"){
			return $arr;
		}else{
			return "{success:true,totalCount: ".$i.", data:".json_encode($arr)."}";
		}
	}

	function get_testcase_from_defect_list($data)
	{
		$pr_seq = $data['pr_seq'];
		$tp_seq = $data['tp_seq'];
		$tc_seq = $data['tc_seq'];
		$type = $data['type']; // all, close (모든것, 종료된것)

		/*사용자 연관 배열*/
		$str_sql_code = "select * from otm_member";
		$query = $this->db->query($str_sql_code);
		foreach ($query->result() as $row)
		{
			$member_arr[$row->mb_email]['mb_name'] = $row->mb_name;
		}

		$df_status_close_seq = 0;

		/*프로젝트 코드 연관 배열*/
		$str_sql_code = "select * from otm_project_code where otm_project_pr_seq='$pr_seq'";
		$query = $this->db->query($str_sql_code);
		foreach ($query->result() as $row)
		{
			if($row->pco_type == 'status' && $row->pco_is_required == 'Y'){
				$df_status_close_seq = $row->pco_seq;
			}

			$code_arr[$row->pco_seq]['pco_name'] = $row->pco_name;
		}

		$tp_sql = "";
		if($tp_seq > 0){
			$tp_sql = " and otl.otm_testcase_plan_tp_seq='$tp_seq'";
		}
		$str_sql = "
			select
				a.*,b.dc_to,
				b.dc_current_status_co_seq as df_status
			from
			(
				select
					a.*,b.tp_subject
				from
				(
					select
						od.*,otl.otm_testcase_plan_tp_seq,
						(select max(dc_seq) from otm_defect_assign where od.df_seq=otm_defect_df_seq) as dc_seq
					from
						otm_defect od, otm_testcase_result otr,otm_testcase_link otl
					where
						od.otm_project_pr_seq='$pr_seq' and
						od.df_seq=otr.otm_defect_df_seq and
						otl.tl_seq=otr.otm_testcase_link_tl_seq and
						otl.otm_testcase_tc_seq='$tc_seq' $tp_sql
				) as a
				left outer join
				otm_testcase_plan as b
				on
				a.otm_testcase_plan_tp_seq=b.tp_seq
			) as a
			left outer join
			otm_defect_assign as b
			on
			a.df_seq=b.otm_defect_df_seq and a.dc_seq=b.dc_seq
		";

		$query = $this->db->query($str_sql);
		$i=0;
		foreach ($query->result() as $row)
		{
			$_is_input = false;

			if($type == "close"){
				if($df_status_close_seq == $row->df_status){
					$_is_input = true;
				}
			}else{
				$_is_input = true;
			}

			if($_is_input){
				$arr[$i] = $row;
				$arr[$i]->status_name = $code_arr[$row->df_status]['pco_name'];
				$arr[$i]->severity_name = $code_arr[$row->df_severity]['pco_name'];
				$arr[$i]->priority_name = $code_arr[$row->df_priority]['pco_name'];
				$arr[$i]->frequency_name = $code_arr[$row->df_frequency]['pco_name'];

				$arr[$i]->assign_name = $member_arr[$row->dc_to]['mb_name'];
				$arr[$i]->writer_name = $member_arr[$row->writer]['mb_name'];
				$i++;
			}
		}
		if($data['return_type'] == "array"){
			return $arr;
		}else{
			return "{success:true,totalCount: ".$i.", data:".json_encode($arr)."}";
		}
	}

	function get_testcase_result_list($data)
	{
		$pr_seq = $data['pr_seq'];
		$tc_seq = $data['tc_seq'];
		$tp_seq = $data['tp_seq'];


		/*사용자 연관 배열*/
		$str_sql_code = "select * from otm_member";
		$query = $this->db->query($str_sql_code);
		foreach ($query->result() as $row)
		{
			$member_arr[$row->mb_email]['mb_name'] = $row->mb_name;
		}

		/*프로젝트 코드 연관 배열*/
		$str_sql_code = "select * from otm_project_code where otm_project_pr_seq='$pr_seq'";
		$query = $this->db->query($str_sql_code);
		foreach ($query->result() as $row)
		{
			$code_arr[$row->pco_seq]['pco_name'] = $row->pco_name;
		}
		$tp_sql = "";
		if($tp_seq){
			$tp_sql = " and otl.otm_testcase_plan_tp_seq='$tp_seq'";
		}

		$str_sql = "
			select
				a.*, b.df_subject, b.df_id, b.df_severity, b.df_priority, b.df_frequency, c.dc_current_status_co_seq as df_status,c.dc_to
			from
			(
				select
					otr.tr_seq,ot.tc_seq,ot.writer as execution_user,opc.pco_name,otr.regdate,otp.tp_subject,otr.otm_defect_df_seq,
					(select max(dc_seq) from otm_defect_assign where otr.otm_defect_df_seq=otm_defect_df_seq) as dc_seq
				from
					otm_testcase as ot,
					otm_testcase_plan as otp,
					otm_testcase_link as otl,
					otm_testcase_result as otr,
					otm_project_code as opc
				where
					ot.otm_project_pr_seq='$pr_seq' and
					ot.tc_seq = '$tc_seq' and
					otl.otm_testcase_tc_seq ='$tc_seq' and
					otp.tp_seq = otl.otm_testcase_plan_tp_seq and
					otl.tl_seq = otr.otm_testcase_link_tl_seq and
					opc.pco_seq= otr.otm_project_code_pco_seq and
					opc.pco_type='tc_result' $tp_sql
				order by  otr.regdate desc
			) as a
			left outer join
			(
				select
					otr.tr_seq,otl.otm_testcase_tc_seq,od.df_subject,od.df_id,od.df_severity,od.df_priority,od.df_frequency
				from
				otm_defect as od, otm_testcase_result otr, otm_testcase_link as otl
				where
					od.otm_project_pr_seq='$pr_seq' and
					od.df_seq=otr.otm_defect_df_seq and
					otr.otm_testcase_link_tl_seq = otl.tl_seq and
					otl.otm_testcase_tc_seq='$tc_seq'
			) as b
			on
			a.tr_seq=b.tr_seq

			left outer join
				otm_defect_assign as c
			on
				a.otm_defect_df_seq=c.otm_defect_df_seq
				and
				a.dc_seq=c.dc_seq
		";

		$str_sql_cnt = "select count(*) as cnt from ($str_sql) as a";
		$query = $this->db->query($str_sql_cnt);
		$cnt_result = $query->result();

		$str_sql = "select * from ($str_sql) as a ";
		$query = $this->db->query($str_sql);
		$i=0;
		foreach ($query->result() as $row)
		{
			$arr[$i] = $row;
			$arr[$i]->execution_user_name = $member_arr[$row->execution_user]['mb_name'];
			$arr[$i]->status_name = $code_arr[$row->df_status]['pco_name'];
			$arr[$i]->severity_name = $code_arr[$row->df_severity]['pco_name'];
			$arr[$i]->priority_name = $code_arr[$row->df_priority]['pco_name'];
			$arr[$i]->frequency_name = $code_arr[$row->df_frequency]['pco_name'];

			$arr[$i]->assign_name = $member_arr[$row->dc_to]['mb_name'];

			$i++;
		}
		if($data['return_type'] == "array"){
			return $arr;
		}else{
			return "{success:true,totalCount: ".$cnt_result[0]->cnt.", data:".json_encode($arr)."}";
		}
	}
}
//End of file report_m.php
//Location: ./models/report_m.php