<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Tracking_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/

class Tracking_m extends CI_Model {

	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();

		$this->load->library('history');

		$this->tmp_array = Array();
		$this->root_array = Array();
		$this->return_array = Array();
		$this->location_path = Array();
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

	/**
	* Function testcase_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function testcase_list($data)
	{
		$temp_arr = array();
		$plan = $data['tcplan'];
		$plan = explode('_', $plan); //[0]: type, [1] : plan seq
		$pr_seq = $data['pr_seq'];

		/**
			Get UserForm Data
		*/
		$p_customform = array();
		$p_tc_item = array();

		$this->db->select('pc_seq,pc_name,pc_category');
		$this->db->from('otm_project_customform');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('pc_is_use','Y');

		$query = $this->db->get();

		$str_select = "";
		$cnt = 0;
		foreach ($query->result() as $row)
		{
			$cnt++;
			$str_select .= ",MAX(IF(cv.pc_seq = '".$row->pc_seq."', cv.tcv_custom_value, NULL)) as 'pc_".$row->pc_name."'";
			if($row->pc_category === 'ID_TC'){
				array_push($p_customform,$row->pc_name);
			}else if($row->pc_category === 'TC_ITEM'){
				array_push($p_tc_item,$row->pc_name);
			}
		}

		$tracking_sql = "
			,(
				select group_concat(otm_defect_df_seq) as tracking_id
				from
				otm_testcase_result as tr
				where tr.otm_testcase_link_tl_seq = a.tl_seq
				and otm_defect_df_seq != ''
			) as tracking_id
		";

		$str_sql = "
			select
				(select mb_name from otm_member where mb_email = a.tc_writer) as tc_writer,
				a.tc_regdate,
				a.tl_seq,a.tc_seq,tc_out_id,tc_inp_id,tc_inp_pid,tc_subject,tc_is_task,result_writer,result_value,
				a.result_df_link
				$str_select
				$tracking_sql
			from
			(
				select a.*,group_concat(r.writer) as result_writer, group_concat(r.pco_name) as result_value,
					group_concat(r.otm_defect_df_seq) as result_df_link
				from
				(
					select
						tc_seq,tc_out_id,tc_inp_id,tl_inp_pid as tc_inp_pid,tc_subject,tc_is_task,tl_seq,writer as tc_writer,regdate as tc_regdate
					from
					otm_testcase as tc, otm_testcase_link tl
					where
						tc.otm_project_pr_seq='$pr_seq' and
						tc.tc_seq=tl.otm_testcase_tc_seq and
						tl.otm_testcase_plan_tp_seq='$plan[1]'
					order by tl_ord asc
				) as a
				left outer join
				(
					select r.otm_testcase_link_tl_seq,r.otm_defect_df_seq,r.writer,c.pco_name from otm_testcase_result as r, otm_project_code as c where otm_project_pr_seq='$pr_seq' and c.pco_seq=r.otm_project_code_pco_seq
				) as r
				on
				a.tl_seq=r.otm_testcase_link_tl_seq
				group by a.tl_seq
			) as a
			left outer join
			(
				select
					pc_seq,otm_project_pr_seq,pc_name,
					b.otm_testcase_tc_seq,b.tcv_custom_value
				from
				(
				select * from otm_project_customform where otm_project_pr_seq='$pr_seq' and (pc_category='ID_TC' or pc_category='TC_ITEM') and pc_is_use='Y'
				) as a,
				otm_testcase_custom_value as b
				where a.pc_seq=b.otm_project_customform_pc_seq
			) as cv
			on
			a.tc_seq = cv.otm_testcase_tc_seq
			group by tc_seq
		";

		$query = $this->db->query($str_sql);
		$result_length = 0;
		foreach ($query->result() as $temp_row)
		{
			$temp_arr['location'] = "";
			$temp_arr['tl_seq'] = $temp_row->tl_seq;
			$temp_arr['id'] = $temp_row->tc_inp_id;
			$temp_arr['tc_id'] = $temp_row->tc_out_id;
			$temp_arr['pid'] = $temp_row->tc_inp_pid;
			$temp_arr['subject'] = $temp_row->tc_subject;
			$temp_arr['tc_is_task'] = $temp_row->tc_is_task;

			$temp_arr['tracking_id'] = $temp_row->tracking_id;
			$temp_arr['result_df_link'] = $temp_row->result_df_link;

			$temp_arr['writer_name'] = $temp_row->tc_writer;
			$temp_arr['regdate'] = $temp_row->tc_regdate;

			for($i=0; $i<count($p_tc_item); $i++){
				$p_tc_item_name = 'pc_'.$p_tc_item[$i];

				$temp_arr[$p_tc_item[$i]] = $temp_row->$p_tc_item_name;
			}

			for($i=0; $i<count($p_customform); $i++){
				$p_customform_name = 'pc_'.$p_customform[$i];
				$temp_arr[$p_customform[$i]] = $temp_row->$p_customform_name;
			}
			$result_writer = "";
			$result_value = "";

			for($i=0;$i<$result_length;$i++){
				$tmp_name = "Member".$i;
				$tmp_result = "Result".$i;
				$temp_arr[$tmp_name] = "";
				$temp_arr[$tmp_result] = "";
			}

			$result_writer = explode(",",$temp_row->result_writer);
			$result_value = explode(",",$temp_row->result_value);

			for($i=0;$i<count($result_writer);$i++){
				$tmp_name = "Member".$i;
				$tmp_result = "Result".$i;

				$temp_arr[$tmp_name] = $result_writer[$i];
				$temp_arr[$tmp_result] = $result_value[$i];
			}

			$result_length = count($result_writer);

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
		$return_array = array();
		for($i=0;$i<sizeof($this->return_array);$i++){
			if($this->return_array[$i]['tc_is_task'] != "folder"){
				unset($this->return_array[$i]['id']);
				unset($this->return_array[$i]['pid']);
				unset($this->return_array[$i]['tc_is_task']);
				array_push($return_array,$this->return_array[$i]);
			}
		}
		return $return_array;
	}

	/**
	* Function defect_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function defect_list($data){
		$pr_seq = $data['project_seq'];


		/**
			Get UserForm Data
		*/
		$p_customform = array();

		$this->db->select('pc_seq,pc_name');
		$this->db->from('otm_project_customform');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('pc_category','ID_DEFECT');
		$this->db->where('pc_is_use','Y');

		$query = $this->db->get();

		$str_select = "";
		$cnt = 0;
		foreach ($query->result() as $row)
		{
			$cnt++;
			$str_select .= ",MAX(IF(cv.pc_seq = '".$row->pc_seq."', cv.cv_custom_value, NULL)) as '_".$row->pc_seq."'";
		}

		$join_sql = " left join (
			select
				pc_seq,otm_project_pr_seq,pc_name,
				b.otm_defect_df_seq,b.cv_custom_value
			from
			(
			select * from otm_project_customform where otm_project_pr_seq='$pr_seq' and pc_category='ID_DEFECT' and pc_is_use='Y'
			) as a,
			otm_defect_custom_value as b
			where a.pc_seq=b.otm_project_customform_pc_seq
		) as cv on a.df_seq = cv.otm_defect_df_seq
		group by a.df_seq";


		$arr = array();

		$tracking_sql = "
			,(
				select group_concat(tc_out_id) as tracking_id
				from
				otm_testcase_result as tr
				left join
				otm_testcase_link as tl
				on tr.otm_testcase_link_tl_seq=tl.tl_seq
				left join
				otm_testcase as tc
				on tl.otm_testcase_tc_seq=tc.tc_seq
				where otm_defect_df_seq = a.df_seq
			) as tracking_id
		";

		$str_sql = "
		select
			a.*
			$str_select
			$tracking_sql
		from
		(
			select
				a.*
			from
			(
				select a.*, link.result_tl_link from
				(
					select
						a.*,b.mb_name as writer_name,
						co_1.pco_name as status_name,
						co_2.pco_name as severity_name,
						co_3.pco_name as priority_name,
						co_4.pco_name as frequency_name
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
									tp.tp_subject
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
								where a.otm_project_pr_seq='$pr_seq'
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
					left outer join
						otm_project_code as co_1
					on
						co_1.pco_seq=a.df_status
					left outer join
						otm_project_code as co_2
					on
						co_2.pco_seq=a.df_severity
					left outer join
						otm_project_code as co_3
					on
						co_3.pco_seq=a.df_priority
					left outer join
						otm_project_code as co_4
					on
						co_4.pco_seq=a.df_frequency
				) as a
				left outer join
					(
						select
							r.*, group_concat(r.otm_testcase_link_tl_seq) as result_tl_link
						from
							otm_defect as d
						left outer join
							otm_testcase_result as r
						on d.df_seq=r.otm_defect_df_seq
						where d.otm_project_pr_seq='$pr_seq'
						group by r.otm_defect_df_seq
					) as link
				on link.otm_defect_df_seq = a.df_seq
				where a.otm_project_pr_seq='$pr_seq'
			)as a
		)as a
		$join_sql
		";

		$str_sql_cnt = "select count(*) as cnt from ($str_sql) as a";
		$query = $this->db->query($str_sql_cnt);
		$cnt_result = $query->result();

		$limitSql = "";

		$start = $data['start'];
		$limit = $data['limit'];
		if($start != null && $limit != null){
			$limitSql = " limit $limit OFFSET $start ";
		}else{
			$limitSql = "";
		}

		$order_by_sql = " order by a.df_seq desc ";
		$sort = $data['sort'][0];

		if($sort){
			$order_by_sql = " order by ";
			foreach($sort as $row => $v){
				$order_by_sql .= $v.' ';
			}
		}

		$str_sql = "select * from ($str_sql) as a $order_by_sql $limitSql";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}
		return "{success:true,totalCount: ".$cnt_result[0]->cnt.", data:".json_encode($arr)."}";
	}

	/**
	* Function set_link
	*
	* @return string
	*/
	function set_link($data)
	{
		$history = array();
		$history['pr_seq'] = $data['pr_seq'];
		$history['details']	= array();
		$cnt = 0;

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');
		$writer_name = $this->session->userdata('mb_name');

		$result_value = $data['result_value'];
		$result_msg = $data['result_msg'];

		$tl_list = json_decode($data['tl_seq']);
		$df_list = json_decode($data['df_seq']);

		for($i=0; $i<count($tl_list); $i++)
		{
			$duplicate_df_array = array();
			$tl_seq = $tl_list[$i];

			$this->db->select('tr.*');
			$this->db->from('otm_testcase_result as tr');
			$this->db->where('tr.otm_testcase_link_tl_seq', $tl_seq);
			$this->db->where_in('tr.otm_defect_df_seq', $df_list);
			$query = $this->db->get();

			$result = $query->result();
			if($result){
				foreach ($result as $temp_row)
				{
					$modify_array = array(
						'otm_project_code_pco_seq' => $data['result_value'],
						'tr_description' => $data['result_msg'],
						'last_writer' => $writer,
						'last_update' => $date
					);
					$where = array('tr_seq'=>$temp_row->tr_seq);
					$this->db->update('otm_testcase_result',$modify_array,$where);

					array_push($duplicate_df_array,$temp_row->otm_defect_df_seq);
				}

				for($j=0; $j<count($df_list); $j++)
				{
					$df_seq = $df_list[$j];

					if (in_array($df_seq, $duplicate_df_array)){
					}else{
						$insert_array['otm_testcase_link_tl_seq'] = $tl_seq;
						$insert_array['otm_project_code_pco_seq'] = $data['result_value'];
						$insert_array['tr_description'] = $data['result_msg'];
						$insert_array['writer'] = $writer;
						$insert_array['regdate'] = $date;
						$insert_array['last_writer'] = '';
						$insert_array['last_update'] = '';
						$insert_array['otm_defect_df_seq'] = $df_seq;

						$this->db->insert('otm_testcase_result', $insert_array);
						$result = $this->db->insert_id();

						$this->db->select('tr.*, df.df_id, tc.tc_out_id,tc.tc_seq');
						$this->db->from('otm_testcase_result as tr');
						$this->db->join('otm_defect as df','tr.otm_defect_df_seq=df.df_seq', 'left');
						$this->db->join('otm_testcase_link as tl','tr.otm_testcase_link_tl_seq=tl.tl_seq', 'left');
						$this->db->join('otm_testcase as tc','tl.otm_testcase_tc_seq=tc.tc_seq', 'left');
						$this->db->where('tr.tr_seq', $result);
						$query = $this->db->get();

						$result = $query->result();
						if($result){
							foreach ($result as $temp_row)
							{
								$history['category']	= 'testcase';
								//$history['tc_seq']		= $tl_seq;
								$history['tc_seq']		= $temp_row->tc_seq;//$tl_seq;
								$history['details']	= array();
								$history['details'][$cnt]['action_type']= 'tracking_tc';
								$history['details'][$cnt]['old_value']	= $temp_row->df_id;
								$history['details'][$cnt]['value']		= 'set_link';
								$this->history->history($history);

								$history['category']	= 'defect';
								$history['df_seq']		= $temp_row->otm_defect_df_seq;
								$history['details']	= array();
								$history['details'][$cnt]['action_type']= 'tracking_df';
								$history['details'][$cnt]['old_value']	= $temp_row->tc_out_id;
								$history['details'][$cnt]['value']		= 'set_link';
								$this->history->history($history);
							}
						}
					}
				}
			}else{
				for($j=0; $j<count($df_list); $j++)
				{
					$df_seq = $df_list[$j];

					if (in_array($df_seq, $duplicate_df_array)){
					}else{
						$insert_array['otm_testcase_link_tl_seq'] = $tl_seq;
						$insert_array['otm_project_code_pco_seq'] = $data['result_value'];
						$insert_array['tr_description'] = $data['result_msg'];
						$insert_array['writer'] = $writer;
						$insert_array['regdate'] = $date;
						$insert_array['last_writer'] = '';
						$insert_array['last_update'] = '';
						$insert_array['otm_defect_df_seq'] = $df_seq;

						$this->db->insert('otm_testcase_result', $insert_array);
						$result = $this->db->insert_id();

						$this->db->select('tr.*, df.df_id, tc.tc_out_id,tc.tc_seq');
						$this->db->from('otm_testcase_result as tr');
						$this->db->join('otm_defect as df','tr.otm_defect_df_seq=df.df_seq', 'left');
						$this->db->join('otm_testcase_link as tl','tr.otm_testcase_link_tl_seq=tl.tl_seq', 'left');
						$this->db->join('otm_testcase as tc','tl.otm_testcase_tc_seq=tc.tc_seq', 'left');
						$this->db->where('tr.tr_seq', $result);
						$query = $this->db->get();

						$result = $query->result();
						if($result){
							foreach ($result as $temp_row)
							{
								$history['category']	= 'testcase';
								//$history['tc_seq']		= $tl_seq;
								$history['tc_seq']		= $temp_row->tc_seq;
								$history['details']	= array();
								$history['details'][$cnt]['action_type']= 'tracking_tc';
								$history['details'][$cnt]['old_value']	= $temp_row->df_id;
								$history['details'][$cnt]['value']		= 'set_link';
								$this->history->history($history);

								$history['category']	= 'defect';
								$history['df_seq']		= $temp_row->otm_defect_df_seq;
								$history['details']	= array();
								$history['details'][$cnt]['action_type']= 'tracking_df';
								$history['details'][$cnt]['old_value']	= $temp_row->tc_out_id;
								$history['details'][$cnt]['value']		= 'set_link';
								$this->history->history($history);
							}
						}
					}
				}
			}
		}

		return $result;
	}

	/**
	* Function set_unlink
	*
	* @return string
	*/
	function set_unlink($data)
	{
		$history = array();
		$history['pr_seq'] = $data['pr_seq'];
		$history['details']	= array();
		$cnt = 0;

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');
		$writer_name = $this->session->userdata('mb_name');

		$result_value = $data['result_value'];
		$result_msg = $data['result_msg'];

		$tl_list = json_decode($data['tl_seq']);
		$df_list = json_decode($data['df_seq']);

		for($i=0; $i<count($tl_list); $i++)
		{
			$duplicate_df_array = array();
			$tl_seq = $tl_list[$i];
			$this->db->select('tr.*, df.df_id, tc.tc_out_id,tc.tc_seq');
			$this->db->from('otm_testcase_result as tr');
			$this->db->join('otm_defect as df','tr.otm_defect_df_seq=df.df_seq', 'left');
			$this->db->join('otm_testcase_link as tl','tr.otm_testcase_link_tl_seq=tl.tl_seq', 'left');
			$this->db->join('otm_testcase as tc','tl.otm_testcase_tc_seq=tc.tc_seq', 'left');
			$this->db->where('tr.otm_testcase_link_tl_seq', $tl_seq);
			$this->db->where_in('tr.otm_defect_df_seq', $df_list);
			$query = $this->db->get();

			$result = $query->result();
			if($result){
				foreach ($result as $temp_row)
				{
					$modify_array = array(
						'otm_defect_df_seq' => '',
						'last_writer' => $writer,
						'last_update' => $date
					);
					$where = array('tr_seq'=>$temp_row->tr_seq);
					$this->db->update('otm_testcase_result',$modify_array,$where);

					$history['category']	= 'testcase';
					$history['tc_seq']		= $temp_row->tc_seq;
					$history['details']	= array();
					$history['details'][$cnt]['action_type']= 'tracking_tc';
					$history['details'][$cnt]['old_value']	= $temp_row->df_id;
					$history['details'][$cnt]['value']		= 'set_unlink';
					$this->history->history($history);

					$history['category']	= 'defect';
					$history['df_seq']		= $temp_row->otm_defect_df_seq;
					$history['details']	= array();
					$history['details'][$cnt]['action_type']= 'tracking_df';
					$history['details'][$cnt]['old_value']	= $temp_row->tc_out_id;
					$history['details'][$cnt]['value']		= 'set_unlink';
					$this->history->history($history);
				}
			}
		}

		return $result;
	}

	/**
	* Function tracking_history
	*
	* @return
	*/
	function tracking_history($history)
	{
		$history['category']	= 'testcase';
		$history['tc_seq']		= $data['tc_seq'];
		$this->history->history($history);

		$history['category']	= 'defect';
		$history['df_seq']		= $data['df_seq'];
		$this->history->history($history);
	}
}
//End of file tracking_m.php
//Location: ./models/tracking_m.php
?>