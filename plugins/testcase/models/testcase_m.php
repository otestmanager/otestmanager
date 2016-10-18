<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Testcase_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Testcase_m extends CI_Model {

	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->load->library('File_Form');

		$this->tmp_array = Array();
		$this->root_array = Array();
		$this->return_array = Array();
		$this->location_path = Array();
	}


	/**
	* Function subpage_tree_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function subpage_tree_list($data)
	{
		$temp_arr = array();
		$node = $data['node'];
		$node = explode('_', $node);

		if($node[0] === 'testcase'){

			$this->db->from('otm_testcase_plan');
			$this->db->where('otm_project_pr_seq',$node[1]);
			$this->db->order_by('tp_seq asc');
			$query = $this->db->get();

			$i=0;
			$temp_arr[$i]['pr_seq'] = $node[1];
			$temp_arr[$i]['tp_seq'] = '';
			$temp_arr[$i]['pid'] = $data['node'];
			$temp_arr[$i]['id'] = 'backlog_'.$node[1];
			$temp_arr[$i]['text'] = 'backlog';
			$temp_arr[$i]['type'] = 'testcase_plan';
			$temp_arr[$i]['iconCls'] = 'ico-backlog';
			$temp_arr[$i]['leaf'] = TRUE;
			$i++;
			foreach ($query->result() as $temp_row)
			{
				$temp_arr[$i]['pr_seq'] = $node[1];
				$temp_arr[$i]['tp_seq'] = $temp_row->tp_seq;
				$temp_arr[$i]['pid'] = $data['node'];
				$temp_arr[$i]['id'] = 'tcplan_'.$temp_row->tp_seq;
				$temp_arr[$i]['text'] = $temp_row->tp_subject;
				$temp_arr[$i]['type'] = 'testcase_plan';
				$temp_arr[$i]['iconCls'] = 'ico-plan';
				$temp_arr[$i]['description'] = $temp_row->tp_description;
				$temp_arr[$i]['startdate'] = $temp_row->tp_startdate;
				$temp_arr[$i]['enddate'] = $temp_row->tp_enddate;
				$temp_arr[$i]['status'] = $temp_row->tp_status;
				$temp_arr[$i]['writer'] = $temp_row->writer;
				$temp_arr[$i]['regdate'] = $temp_row->regdate;
				$temp_arr[$i]['last_writer'] = $temp_row->last_writer;
				$temp_arr[$i]['last_update'] = $temp_row->last_update;
				$temp_arr[$i]['leaf'] = TRUE;
				$i++;
			}
		}
		return $temp_arr;
	}


	function duplicate_check($data){

		if($data['select_key'] != ''){
			$this->db->select($data['select_key']);
		}

		$this->db->from($data['table']);

		for($i=0; $i<count($data['key']); $i++)
		{
			$this->db->where($data['key'][$i]['column'],$data['key'][$i]['value']);
		}

		if($data['update_key'] != ''){
			$this->db->where($data['update_key']['column'].' !=',$data['update_key']['value']);
		}
		$query = $this->db->get();
		if($query->result()){
			if($data['select_key'] != ''){
				foreach ($query->result() as $row)
				{
					return $row->$data['select_key'];
				}
			}else{
				return 'Duplicate Value : "'.$data['key'][0]['value'].'"';

			}
		}
	}

	/**
	* Function get_id
	*
	* @param array $type Post Data
	*
	* @return string
	*/
	public function get_id($data)
	{
		$this->db->where('pco_type', 'tc_id_rule');
		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->where('pco_is_default', 'Y');
		$query = $this->db->get('otm_project_code');

		$tc_id_rule = $query->result();

		$pco_name = $tc_id_rule[0]->pco_name;
		$pco_default_value = $tc_id_rule[0]->pco_default_value;

		/*
		 * 0 : text
		 * 1 : '-' or '_'
		 * 2 : date
		 * 3 : '-' or '_'
		 * 4 : id number length
		 */
		$rule_arr = explode(',',$pco_default_value);

		$mb_lang = $this->session->userdata('mb_lang');

		$yoil = date('D');
		$yoil_ko = array("일","월","화","수","목","금","토");
		if($mb_lang === "ko"){
			$yoil = $yoil_ko[date('w')];
		}

		switch($rule_arr[2])
		{
			case "yyyy-mm-dd":
				$date = date('Y-m-d');
				break;
			case "yy-mm-dd":
				$date = date('y-m-d');
				break;
			case "mm-dd":
				$date = date('m-d');
				break;
			case "yyyy-mm-dd DayOfWeek":
				$date = date('Y-m-d').$yoil;
				break;
			case "yy-mm-dd DayOfWeek":
				$date = date('y-m-d').$yoil;
				break;
			case "mm-dd DayOfWeek":
				$date = date('m-d').$yoil;
				break;
		}
		$pco_name= str_replace($rule_arr[2],$date,$pco_name);

		/*
		*	ID Number
		*/
		$num_length = strlen($rule_arr[count($rule_arr)-1]);

		$this->db->select_max('id_seq');
		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->where('id_type', 'testcase');
		$max_seq = $this->db->get('otm_id_generator')->result();

		if($max_seq[0]->id_seq && $max_seq[0]->id_seq >= 1){
			$max_seq = $max_seq[0]->id_seq + 1;

			if($num_length < strlen($max_seq)){
				return 'over_num';
			}

			$modify_array = array('id_seq'=>$max_seq);
			$where = array(	'otm_project_pr_seq'=>$data['pr_seq'],
							'id_type'=>'testcase');
			$this->db->update('otm_id_generator',$modify_array,$where);

		}else{
			$max_seq = 1;

			$this->db->set('otm_project_pr_seq',	$data['pr_seq']);
			$this->db->set('id_type',				'testcase');
			$this->db->set('id_seq',				$max_seq);
			$this->db->insert('otm_id_generator');
		}

		$df_id_num = sprintf("%0".$num_length."d", $max_seq);
		return substr_replace($pco_name, $df_id_num, strlen($pco_name)-$num_length, $num_length);
	}

	function get_member_info($data)
	{
		$temp_arr = array();

		$this->db->from('otm_member');

		if(isset($data['mb_email'])){
			$this->db->where('mb_email',$data['mb_email']);
		}
		else if(isset($data['mb_name'])){
			$this->db->where('mb_name',$data['mb_name']);
		}
		else{
		}

		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$temp_arr = $temp_row;
		}

		return $temp_arr;
	}

	/**
	* Function plan_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function plan_list($data)
	{
		$temp_arr = array();
		$pr_seq = $data['pr_seq'];

		if($pr_seq AND $pr_seq > 0){
			$this->db->from('otm_testcase_plan');
			$this->db->where('otm_project_pr_seq',$pr_seq);
			$this->db->order_by('tp_seq asc');
			$query = $this->db->get();

			$i=0;
			foreach ($query->result() as $temp_row)
			{
				$temp_arr[$i]['pr_seq'] = $pr_seq;
				$temp_arr[$i]['tp_seq'] = $temp_row->tp_seq;
				$temp_arr[$i]['text'] = $temp_row->tp_subject;
				$i++;
			}
		}
		return $temp_arr;
	}

	/**
	* Function plan_tree_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function plan_tree_list($data)
	{
		$temp_arr = array();
		$node = $data['node'];
		$node = explode('_', $node);

		if($node[0] === 'testcase'){

			$this->db->from('otm_testcase_plan');
			$this->db->where('otm_project_pr_seq',$node[1]);
			$this->db->order_by('tp_seq asc');
			$query = $this->db->get();

			$i=0;
			$temp_arr[$i]['pr_seq'] = $node[1];
			$temp_arr[$i]['tp_seq'] = '';
			$temp_arr[$i]['pid'] = $data['node'];
			$temp_arr[$i]['id'] = 'backlog_'.$node[1];
			$temp_arr[$i]['text'] = 'backlog';
			$temp_arr[$i]['type'] = 'testcase_plan';
			$temp_arr[$i]['iconCls'] = 'ico-backlog';
			$temp_arr[$i]['leaf'] = TRUE;
			$i++;
			foreach ($query->result() as $temp_row)
			{
				$temp_arr[$i]['pr_seq'] = $node[1];
				$temp_arr[$i]['tp_seq'] = $temp_row->tp_seq;
				$temp_arr[$i]['pid'] = $data['node'];
				$temp_arr[$i]['id'] = 'tcplan_'.$temp_row->tp_seq;
				$temp_arr[$i]['text'] = $temp_row->tp_subject;
				$temp_arr[$i]['type'] = 'testcase_plan';
				$temp_arr[$i]['iconCls'] = 'ico-plan';
				$temp_arr[$i]['description'] = $temp_row->tp_description;
				$temp_arr[$i]['startdate'] = $temp_row->tp_startdate;
				$temp_arr[$i]['enddate'] = $temp_row->tp_enddate;
				$temp_arr[$i]['status'] = $temp_row->tp_status;
				$temp_arr[$i]['writer'] = $temp_row->writer;
				$temp_arr[$i]['regdate'] = $temp_row->regdate;
				$temp_arr[$i]['last_writer'] = $temp_row->last_writer;
				$temp_arr[$i]['last_update'] = $temp_row->last_update;
				$temp_arr[$i]['leaf'] = TRUE;
				$i++;
			}
		}
		return $temp_arr;
	}

	/**
	* Function create_plan
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_plan($data)
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$insert_array['otm_project_pr_seq'] = $data['project_seq'];
		$insert_array['tp_subject'] = $data['tp_subject'];
		$insert_array['tp_description'] = $data['tp_description'];
		$insert_array['tp_startdate'] = $data['tp_startdate'];
		$insert_array['tp_enddate'] = $data['tp_enddate'];
		$insert_array['tp_status'] = $data['tp_status'];
		$insert_array['writer'] = $writer;
		$insert_array['regdate'] = $date;
		$insert_array['last_writer'] = $writer;
		$insert_array['last_update'] = $date;

		$this->db->insert('otm_testcase_plan', $insert_array);
		$result = $this->db->insert_id();

		return $result;
	}

	/**
	* Function update_product
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_plan($data)
	{
		$date = date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$modify_array = array(
			'tp_subject' => $data['tp_subject'],
			'tp_description' => $data['tp_description'],
			'tp_startdate' => $data['tp_startdate'],
			'tp_enddate' => $data['tp_enddate'],
			'tp_status' => $data['tp_status'],
			'last_writer' => $writer,
			'last_update' => $date
		);
		$this->db->where('tp_seq', $data['tp_seq']);
		$this->db->update('otm_testcase_plan', $modify_array);

		return $data['tp_seq'];
	}

	/**
	* Function delete_plan
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_plan($data)
	{
		$delete_array = array(
			'tp_seq' => $data['tp_seq']
		);
		$result = $this->db->delete('otm_testcase_plan', $delete_array);

		return $result;
	}

	/**
	* Function testcase_tree_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function testcase_tree_list($data)
	{
		$mb_email = $this->session->userdata('mb_email');

		$temp_arr = array();
		$node = $data['node'];
		$node = explode('_', $node);
		$plan = $data['tcplan'];
		$plan = explode('_', $plan); //[0]: type, [1] : plan seq
		$pr_seq = $data['project_seq'];

		/**
			Get OTM Mamber Data
		*/
		$member_list = array();
		$this->db->from('otm_member');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$member_list[$row->mb_email] = $row->mb_name;
		}

		$p_customform = array();

		// 사용자 정의값을 쿼리에서 프로그램으로 변경 kslovee 2015-12-07
		$custom_arr = array();
		$str_sql = "
			select
				pc_seq,otm_project_pr_seq,pc_name,
				b.otm_testcase_tc_seq,b.tcv_custom_value as tcv_custom_value
			from
			(
				select * from otm_project_customform where otm_project_pr_seq='$pr_seq' and pc_category='ID_TC' and pc_is_use='Y'
			) as a,
			otm_testcase_custom_value as b
			where
				a.pc_seq=b.otm_project_customform_pc_seq
		";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$custom_arr[$row->otm_testcase_tc_seq]["_".$row->pc_seq] = $row;
		}

		$result_arr = array();
		if($plan[1] >= 1){
			$tp_seq = $plan[1];

			$str_sql = "
				select
					a.*,pco_name,mb_name
				from
				(
					select
						otl.otm_testcase_tc_seq as tc_seq,otl.tl_seq,otr.otm_project_code_pco_seq,otr.regdate as result_date,writer as result_writer
					from
						otm_testcase_link as otl,
						otm_testcase_result as otr
					where
					otl.tl_seq=otr.otm_testcase_link_tl_seq and otl.otm_testcase_plan_tp_seq='$tp_seq'
					order by otr.regdate desc
				) as a
				left outer join
					otm_project_code as pco
				ON
					a.otm_project_code_pco_seq=pco.pco_seq
				left outer join
					otm_member as om
				on
					a.result_writer=om.mb_email
				group by tl_seq
			";
			$query = $this->db->query($str_sql);
			foreach ($query->result() as $row)
			{
				$result_arr[$row->tc_seq] = $row;
			}
		}

		$this->db->select('pc_seq,pc_name');
		$this->db->from('otm_project_customform');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('pc_category','ID_TC');
		$this->db->where('pc_is_use','Y');

		$this->db->order_by('pc_category', 'desc');
		$this->db->order_by('ABS(pc_1)', 'asc');
		$this->db->order_by('pc_seq', 'asc');

		$query = $this->db->get();
		$str_select = "";
		$cnt = 0;

		$column_arr = array();

		foreach ($query->result() as $row)
		{
			$cnt++;
			array_push($column_arr,"_".$row->pc_seq);
		}

		if($node[0] !== 'root'){
			$this->db->select('tc.*,tl.*,tc.writer as mb_name, tl.tl_assign_to as assign_name');
			$this->db->from('otm_testcase as tc');
			$this->db->join('otm_testcase_link as tl','tl.otm_testcase_tc_seq=tc.tc_seq','left');
			$this->db->where('tc.otm_project_pr_seq',$pr_seq);

			if($plan[0] === 'backlog'){
				if(isset($data['role']) && $data['role'] !=''){
					if($data['role'] === 'all'){
					}else if($data['role'] === 'writer'){
						$where = " (tc.writer = '$mb_email' or tc.tc_is_task = 'folder') ";
						$this->db->where($where);
					}
				}else{
					return $temp_arr;
				}

				$this->db->where('tc.tc_inp_pid',$data['node']);
				$this->db->group_by('tc.tc_seq');
				$this->db->order_by('tc_ord asc');

			}
			else {
				$this->db->where('tl.otm_testcase_plan_tp_seq',$plan[1]);
				$this->db->where('tl.tl_inp_pid',$data['node']);

				if(isset($data['role']) && $data['role'] !=''){
					if($data['role'] === 'all'){

					}else if($data['role'] === 'writer'){
						$where = " (tc.writer = '$mb_email' or tl.tl_assign_to = '$mb_email' or tc.tc_is_task = 'folder') ";
						$this->db->where($where);
					}else{
						$this->db->where('tc.tc_is_task','folder');
					}
				}else{
					return $temp_arr;
				}
				$this->db->group_by('tc.tc_seq');
				$this->db->order_by('tl_ord asc');
			}

			$query = $this->db->get();
			$i=0;
			foreach ($query->result() as $temp_row)
			{
				$temp_arr[$i]['pr_seq'] = $temp_row->otm_project_pr_seq;
				$temp_arr[$i]['tp_seq'] = $data['tcplan'];
				$temp_arr[$i]['tc_seq'] = $temp_row->tc_seq;
				if($plan[0] === 'backlog'){
					$temp_arr[$i]['tl_seq'] = '';
				}else{
					$temp_arr[$i]['tl_seq'] = $temp_row->tl_seq;
				}
				$temp_arr[$i]['pid'] = $temp_row->tc_inp_pid;
				$temp_arr[$i]['id'] = $temp_row->tc_inp_id;
				$temp_arr[$i]['out_id'] = $temp_row->tc_out_id;
				$temp_arr[$i]['text'] = $temp_row->tc_subject;
				$temp_arr[$i]['type'] = $temp_row->tc_is_task;

				$temp_arr[$i]['writer_name'] = $member_list[$temp_row->mb_name];
				$temp_arr[$i]['writer'] = $temp_row->writer;
				$temp_arr[$i]['regdate'] = $temp_row->regdate;
				$temp_arr[$i]['last_writer'] = $temp_row->last_writer;
				$temp_arr[$i]['last_update'] = $temp_row->last_update;
				$temp_arr[$i]['leaf'] = ($temp_row->tc_is_task === 'folder')?FALSE:TRUE;

				$temp_arr[$i]['assign_name'] = $member_list[$temp_row->assign_name];
				$temp_arr[$i]['assign_to'] = $temp_row->tl_assign_to;
				$temp_arr[$i]['deadline_date'] = $temp_row->tl_assign_deadline_date;

				$temp_arr[$i]['result_date'] = $result_arr[$temp_row->tc_seq]->result_date;
				$temp_arr[$i]['result_value'] = $result_arr[$temp_row->tc_seq]->pco_name;
				$temp_arr[$i]['result_writer'] = $result_arr[$temp_row->tc_seq]->last_writer;
				$temp_arr[$i]['result_writer_name'] = $result_arr[$temp_row->tc_seq]->mb_name;

				for($k=0;$k<sizeof($column_arr);$k++){
					$temp_arr[$i][$column_arr[$k]] = $custom_arr[$temp_row->tc_seq][$column_arr[$k]]->tcv_custom_value;
				}

				$i++;
			}
		}else{
			if($plan[0] === 'backlog'){
				$this->db->select("tc.*,tc.writer as mb_name");
				$this->db->from('otm_testcase tc');
				$this->db->where('tc.otm_project_pr_seq',$pr_seq);
				$this->db->where('tc.otm_project_pr_seq',$plan[1]);
				$this->db->where('tc.tc_inp_pid','tc_0');
				$this->db->group_by('tc.tc_seq');

				if(isset($data['role']) && $data['role'] !=''){
					if($data['role'] === 'all'){
					}else if($data['role'] === 'writer'){
						$where = " (tc.writer = '$mb_email' or tc.tc_is_task = 'folder') ";
						$this->db->where($where);
					}
				}else{
					return $temp_arr;
				}

				$this->db->order_by('tc.tc_ord asc');
				$query = $this->db->get();

				$i=0;
				foreach ($query->result() as $temp_row)
				{
					$temp_arr[$i]['pr_seq'] = $temp_row->otm_project_pr_seq;
					$temp_arr[$i]['tp_seq'] = '';
					$temp_arr[$i]['tc_seq'] = $temp_row->tc_seq;
					$temp_arr[$i]['tl_seq'] = '';
					$temp_arr[$i]['pid'] = $temp_row->tc_inp_pid;
					$temp_arr[$i]['id'] = $temp_row->tc_inp_id;
					$temp_arr[$i]['out_id'] = $temp_row->tc_out_id;
					$temp_arr[$i]['text'] = $temp_row->tc_subject;
					$temp_arr[$i]['type'] = $temp_row->tc_is_task;

					$temp_arr[$i]['writer_name'] = $member_list[$temp_row->mb_name];
					$temp_arr[$i]['writer'] = $temp_row->writer;
					$temp_arr[$i]['regdate'] = $temp_row->regdate;
					$temp_arr[$i]['last_writer'] = $temp_row->last_writer;
					$temp_arr[$i]['last_update'] = $temp_row->last_update;
					$temp_arr[$i]['leaf'] = ($temp_row->tc_is_task === 'folder')?FALSE:TRUE;

					for($k=0;$k<sizeof($column_arr);$k++){
						$temp_arr[$i][$column_arr[$k]] = $custom_arr[$temp_row->tc_seq][$column_arr[$k]]->tcv_custom_value;
					}
					$i++;
				}
			}else if($plan[0] === 'tcplan'){

				$this->db->select('tl.*,tc.*, tc.writer as writer_name, tl.tl_assign_to as mb_name');
				$this->db->from('otm_testcase_link as tl');
				$this->db->join('otm_testcase as tc','tl.otm_testcase_tc_seq=tc.tc_seq');
				$this->db->where('otm_testcase_plan_tp_seq',$plan[1]);
				$this->db->where('tl.tl_inp_pid','tc_0');
				$this->db->where('tc.otm_project_pr_seq',$pr_seq);

				if(isset($data['role']) && $data['role'] !=''){
					if($data['role'] === 'all'){

					}else if($data['role'] === 'writer'){
						$where = " (tc.writer = '$mb_email' or tl.tl_assign_to = '$mb_email' or tc.tc_is_task = 'folder') ";
						$this->db->where($where);
					}else{
						$this->db->where('tc.tc_is_task','folder');
					}
				}else{
					return $temp_arr;
				}
				$this->db->group_by('tc.tc_seq');
				$this->db->order_by('tl_ord asc');
				$query = $this->db->get();

				$i=0;
				foreach ($query->result() as $temp_row)
				{
					$temp_arr[$i]['pr_seq'] = $data['project_seq'];
					$temp_arr[$i]['tp_seq'] = $data['tcplan'];
					$temp_arr[$i]['tc_seq'] = $temp_row->tc_seq;
					$temp_arr[$i]['tl_seq'] = $temp_row->tl_seq;
					$temp_arr[$i]['pid'] = $temp_row->tl_inp_pid;
					$temp_arr[$i]['id'] = $temp_row->tc_inp_id;
					$temp_arr[$i]['out_id'] = $temp_row->tc_out_id;
					$temp_arr[$i]['text'] = $temp_row->tc_subject;
					$temp_arr[$i]['type'] = $temp_row->tc_is_task;

					$temp_arr[$i]['writer_name'] = $member_list[$temp_row->writer_name];
					$temp_arr[$i]['writer'] = $temp_row->writer;
					$temp_arr[$i]['regdate'] = $temp_row->regdate;
					$temp_arr[$i]['last_writer'] = $temp_row->last_writer;
					$temp_arr[$i]['last_update'] = $temp_row->last_update;
					$temp_arr[$i]['leaf'] = ($temp_row->tc_is_task === 'folder')?FALSE:TRUE;

					$temp_arr[$i]['assign_name'] = $member_list[$temp_row->mb_name];
					$temp_arr[$i]['assign_to'] = $temp_row->tl_assign_to;
					$temp_arr[$i]['deadline_date'] = $temp_row->tl_assign_deadline_date;

					$temp_arr[$i]['result_date'] = $result_arr[$temp_row->tc_seq]->result_date;
					$temp_arr[$i]['result_value'] = $result_arr[$temp_row->tc_seq]->pco_name;
					$temp_arr[$i]['result_writer'] = $result_arr[$temp_row->tc_seq]->last_writer;
					$temp_arr[$i]['result_writer_name'] = $result_arr[$temp_row->tc_seq]->mb_name;

					for($k=0;$k<sizeof($column_arr);$k++){
						$temp_arr[$i][$column_arr[$k]] = $custom_arr[$temp_row->tc_seq][$column_arr[$k]]->tcv_custom_value;
					}
					$i++;
				}
			}
		}

		return $temp_arr;
	}


	/**
	* Function get_ord_maxval
	*
	* @return double
	*/
	private function get_ord_maxval($table,$pr_seq,$plan,$pid)
	{
		$str_sql="";
		if($table == "otm_testcase"){
			$str_sql = "select max(tc_ord) as max_ord from otm_testcase where otm_project_pr_seq='$pr_seq' and tc_inp_pid='$pid'";
		}else{
			$str_sql = "select max(tl_ord) as max_ord from otm_testcase_link where otm_testcase_plan_tp_seq='$plan' and tl_inp_pid='$pid'";
		}
		$query = $this->db->query($str_sql);
		$max_result = $query->result();

		if($max_result[0]->max_ord >= 1){
			$max_num = $max_result[0]->max_ord;
			$max_num++;
		}else{
			$max_num = 1;
		}
		return $max_num;
	}


	/**
	* Function create_testcase
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_testcase($data)
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');
		$writer_name = $this->session->userdata('mb_name');

		$insert_array['otm_project_pr_seq'] = $data['pr_seq'];
		$insert_array['tc_subject'] = $data['tc_subject'];
		$insert_array['tc_description'] = $data['tc_description'];
		if(isset($data['pid']) && $data['pid'] === 'root') $data['pid'] = 'tc_0';
		$insert_array['tc_inp_pid'] = ($data['pid'])?$data['pid']:'tc_0';
		$temp_tl_seq = $data['tl_seq'];

		if(isset($data['tc_ord'])){
			$tc_ord = $data['tc_ord'];
		}else{
			$tc_ord = $this->get_ord_maxval("otm_testcase",$data["pr_seq"],"",$insert_array["tc_inp_pid"]);
		}

		$insert_array['tc_ord'] = $tc_ord;
		$insert_array['writer'] = $writer;
		$insert_array['regdate'] = $date;
		$insert_array['last_writer'] = '';
		$insert_array['last_update'] = '';
		$custom_form = $data['custom_form'];

		$pid = $insert_array['tc_inp_pid'];

		if($data['type'] === 'suite'){
			$id_head = 'ts_';
			$insert_array['tc_is_task'] = 'folder';
			$tc_leaf  = false;
			$tc_type = 'folder';
		}else{
			$id_head = 'tc_';
			$insert_array['tc_is_task'] = 'file';
			$insert_array['tc_precondition'] = $data['tc_precondition'];
			$insert_array['tc_testdata'] = $data['tc_testdata'];
			$insert_array['tc_procedure'] = $data['tc_procedure'];
			$insert_array['tc_expected_result'] = $data['tc_expected_result'];
			$tc_leaf  = true;
			$tc_type = 'file';
		}

		if($data['type'] === 'suite'){
			$tc_out_id = $id_head.$result;
			$insert_array['tc_out_id'] = $id_head.$result;
		}else{

			if($data['tc_out_id']){
				$tc_out_id = $data['tc_out_id'];
				$insert_array['tc_out_id'] = $data['tc_out_id'];
			}else{
				$tc_out_id = $this->get_id($data);
				if($tc_out_id !== 'over_num'){
					$insert_array['tc_out_id'] = $tc_out_id;
				}else{
					$data['msg'] = $tc_out_id;
					return "{success:true, data:".json_encode($data)."}";
				}
			}
		}

		$this->db->insert('otm_testcase', $insert_array);
		$result = $this->db->insert_id();
		$tc_seq = $result;

		if($data['type'] === 'suite'){
			$tc_out_id = $id_head.$result;
			$modify_array['tc_out_id'] = $id_head.$result;
		}

		$modify_array['tc_inp_id'] = $id_head.$result;
		$modify_array['tc_ord'] = $tc_seq;
		$where = array('tc_seq'=>$result);
		$this->db->update('otm_testcase', $modify_array, $where);

		$custom_arr = json_decode($custom_form);

		if(sizeof($custom_arr) >= 1){
			for($i=0;$i<sizeof($custom_arr);$i++){
				$form_seq = $custom_arr[$i]->seq;
				$form_type = $custom_arr[$i]->type;
				$form_value = $custom_arr[$i]->value;

				$this->create_custom_value($tc_seq,$form_seq,$form_type,$form_value);
			}
		}

		if(isset($data['return_key'])){
			return $tc_seq;
		}

		if(isset($data['call_function']) && ($data['call_function'] === "import_testcase_csv")){
			return $tc_seq;
		}

		if($data['tp_seq'] != ''){
			$insert_array = array();
			$insert_array['otm_testcase_plan_tp_seq'] = $data['tp_seq'];
			$insert_array['otm_testcase_tc_seq'] = $result;
			$insert_array['tl_inp_pid'] = ($data['pid'])?$data['pid']:'tc_0';

			$tl_ord = $this->get_ord_maxval('otm_testcase_link','',$data['tp_seq'],$insert_array['tl_inp_pid']);

			$insert_array['tl_ord'] = $tl_ord;

			$this->db->insert('otm_testcase_link', $insert_array);
			$result = $this->db->insert_id();
			$temp_tl_seq = $result;
		}

		if(isset($_FILES['form_file']))
		{
			for($i=0;$i<sizeof($_FILES['form_file']['name']);$i++){
				if($_FILES['form_file']['name'][$i]){
					$file_data = array();
					$file_data['source']['name'] = $_FILES['form_file']['name'][$i];
					$file_data['source']['tmp_name'] = $_FILES['form_file']['tmp_name'][$i];
					$file_data['source']['size'] = $_FILES['form_file']['size'][$i];
					$file_data['source']['type'] = $_FILES['form_file']['type'][$i];

					$file_data['category'] = 'ID_TC';
					$file_data['pr_seq'] = $data['pr_seq'];
					$file_data['target_seq'] = $tc_seq;
					$file_data['of_no'] = $i;

					$this->File_Form->file_upload($file_data);
				}
			}
		}

		$return_data = array();
		$return_data['pr_seq']		= $data['pr_seq'];
		$return_data['tp_seq']		= $data['tp_seq'];
		$return_data['tc_seq']		= $tc_seq;
		$return_data['tl_seq']		= $temp_tl_seq;
		$return_data['pid']			= $pid;
		$return_data['id']			= $modify_array['tc_inp_id'];
		$return_data['out_id']		= $tc_out_id;
		$return_data['text']		= $data['tc_subject'];
		$return_data['type']		= $tc_type;
		$return_data['writer_name'] = $writer_name;
		$return_data['writer']		= $writer;
		$return_data['regdate']		= $date;
		$return_data['last_writer'] = $writer;
		$return_data['last_update'] = $date;
		$return_data['leaf']		= $tc_leaf;

		if(sizeof($custom_arr) >= 6){
			for($i=5;$i<sizeof($custom_arr);$i++){
				$return_data['_'.$custom_arr[$i]->seq] = $custom_arr[$i]->value;
			}
		}

		return "{success:true, data:'".json_encode($return_data)."'}";
	}

	/**
	* Function update_testcase
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_testcase($data)
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');
		$writer_name = $this->session->userdata('mb_name');

		$modify_array['tc_subject'] = $data['tc_subject'];
		$modify_array['tc_description'] = $data['tc_description'];
		$modify_array['last_writer'] = $writer;
		$modify_array['last_update'] = $date;

		$modify_array['tc_precondition'] = $data['tc_precondition'];
		$modify_array['tc_testdata'] = $data['tc_testdata'];
		$modify_array['tc_procedure'] = $data['tc_procedure'];
		$modify_array['tc_expected_result'] = $data['tc_expected_result'];
		$custom_form = $data['custom_form'];

		if($data['type'] === 'suite'){
			$id_head = 'ts_';
		}
		else{
			$id_head = 'tc_';
		}

		$history = array();
		$history['pr_seq'] = $data['pr_seq'];
		$history['tc_seq'] = $data['tc_seq'];
		$history['details'] = array();

		$this->db->from('otm_testcase');
		$this->db->where('tc_seq',$data['tc_seq']);

		$query = $this->db->get();
		$cnt = 0;
		foreach ($query->result() as $row)
		{
			if($data['tc_subject'] !== $row->tc_subject){
				$history['details'][$cnt]['action_type'] = 'tc_subject';
				$history['details'][$cnt]['old_value'] = $row->tc_subject;
				$history['details'][$cnt]['value'] = $data['tc_subject'];
				$cnt++;
			}
			if($data['tc_description'] !== $row->tc_description){
				$history['details'][$cnt]['action_type'] = 'tc_description';
				$history['details'][$cnt]['old_value'] = $row->tc_description;
				$history['details'][$cnt]['value'] = $data['tc_description'];
				$cnt++;
			}

			$data['tc_seq'] = $row->tc_seq;
			$history['tc_seq'] = $row->tc_seq;
		}

		$where = array('tc_seq'=>$data['tc_seq']);
		$this->db->update('otm_testcase', $modify_array, $where);

		$custom_arr = json_decode($custom_form);
		if(sizeof($custom_arr) >= 1){
			for($i=0;$i<sizeof($custom_arr);$i++){
				$form_name = $custom_arr[$i]->name;
				$form_seq = $custom_arr[$i]->seq;
				$form_type = $custom_arr[$i]->type;
				$form_value = $custom_arr[$i]->value;

				$this->db->from('otm_testcase_custom_value');
				$this->db->where('otm_testcase_tc_seq',$data['tc_seq']);
				$this->db->where('otm_project_customform_pc_seq',$form_seq);
				$query = $this->db->get();
				$result = $query->result();
				if($result){
					foreach ($result as $tmp_fomarr)
					{
						if($form_value !== $tmp_fomarr->tcv_custom_value){
							$history['details'][$cnt]['action_type'] = $form_name;
							$history['details'][$cnt]['old_value'] = $tmp_fomarr->tcv_custom_value;
							$history['details'][$cnt]['value'] = $form_value;
							$cnt++;
						}
					}
					$this->update_custom_value($data['tc_seq'],$form_seq,$form_type,$form_value);

				}else{
					if($form_value !== ''){
						$history['details'][$cnt]['action_type'] = $form_name;
						$history['details'][$cnt]['old_value'] = '';
						$history['details'][$cnt]['value'] = $form_value;
						$cnt++;
					}

					$this->create_custom_value($data['tc_seq'],$form_seq,$form_type,$form_value);
				}
			}
		}

		if(count($history['details']) > 0){
			$history['category']= 'testcase';
			$this->load->library('history');
			$this->history->history($history);
		}

		if(isset($data['return_key']) && $data['return_key'] !== ''){
			return $data['tc_seq'];
		}

		if(isset($_FILES['form_file']))
		{
			for($i=0;$i<sizeof($_FILES['form_file']['name']);$i++){
				if($_FILES['form_file']['name'][$i]){
					$file_data = array();
					$file_data['source']['name'] = $_FILES['form_file']['name'][$i];
					$file_data['source']['tmp_name'] = $_FILES['form_file']['tmp_name'][$i];
					$file_data['source']['size'] = $_FILES['form_file']['size'][$i];
					$file_data['source']['type'] = $_FILES['form_file']['type'][$i];

					$file_data['category'] = 'ID_TC';
					$file_data['pr_seq'] = $data['pr_seq'];
					$file_data['target_seq'] = $data['tc_seq'];
					$file_data['of_no'] = $i;

					$history['details'][$cnt]['action_type'] = 'tc_file';
					$history['details'][$cnt]['old_value'] = '';
					$history['details'][$cnt]['value'] = $file_data['source']['name'];
					$cnt++;

					$this->File_Form->file_upload($file_data);
				}
			}
		}

		$return_data = array();
		$return_data['pr_seq']		= $data['pr_seq'];
		$return_data['tp_seq']		= $data['tp_seq'];
		$return_data['tc_seq']		= $data['tc_seq'];
		$return_data['tl_seq']		= $data['tl_seq'];
		$return_data['pid']			= $data['pid'];
		$return_data['text']		= $data['tc_subject'];
		$return_data['last_writer'] = $writer_name;
		$return_data['last_update'] = $date;

		if(sizeof($custom_arr) >= 6){
			for($i=5;$i<sizeof($custom_arr);$i++){
				$return_data['_'.$custom_arr[$i]->seq] = $custom_arr[$i]->value;
			}
		}

		return "{success:true, data:'".json_encode($return_data)."'}";
	}

	/**
	* Function delete_testcase
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_testcase($data)
	{
		$pr_seq = $data['pr_seq'];
		$plan = explode('_', $data['tp_seq']);
		$tp_seq = $plan[1];

		for($i=0; $i<count($data['list']); $i++){

			if($data['tp_seq'] === '' || $plan[0] === 'backlog'){
				if(!isset($data['type']) || $data['type'][$i] === 'folder')
				{
					$this->db->select('tc_seq, tc_inp_id, tc_is_task');
					$this->db->where('tc_inp_pid', $data['list'][$i]);
					$this->db->where('otm_project_pr_seq', $pr_seq);
					$query = $this->db->get('otm_testcase');
					$result = $query->result();
					if($result){
						$temp_arr = array();
						$temp_arr2 = array();
						foreach ($result as $temp_row)
						{
							$temp_arr[] = $temp_row->tc_inp_id;
							$temp_arr2[] = $temp_row->tc_is_task;
						}
						$new_data = array(
							'pr_seq' => $pr_seq,
							'tp_seq' => $data['tp_seq'],
							'list' => $temp_arr,
							'type' => $temp_arr2
						);
						$this->delete_testcase($new_data);
					}
				}

				$tc_inp_id = explode('_', $data['list'][$i]);
				$tc_seq = $tc_inp_id[1];

				$this->delete_testcase_link(array(
						'pr_seq' => $pr_seq,
						'tp_seq' => 'backlog',
						'tc_seq' => $tc_seq,
						'tc_inp_id' => $data['list'][$i]
					));

				if($data['tp_seq'] === '' || $plan[0] === 'backlog'){

					$tc_seq = explode('_', $data['list'][$i]);
					$history['category']= 'testcase';
					$history['tc_seq']	= $tc_seq[1];
					$this->history->delete_history($history);

					$this->db->where('tc_inp_id', $data['list'][$i]);
					$this->db->where('otm_project_pr_seq', $pr_seq);
					$this->db->delete('otm_testcase');

					array_push($this->tmp_array, $tc_seq[1]);
				}
			}else{
				if(!isset($data['type']) || $data['type'][$i] === 'folder')
				{
					$this->db->where('otm_testcase_plan_tp_seq', $tp_seq);
					$this->db->where('tl_inp_pid', $data['list'][$i]);
					$this->db->join('otm_testcase as tc','tc.tc_seq=tl.otm_testcase_tc_seq','left');

					$this->db->join('otm_testcase_plan tp','tp.otm_project_pr_seq=tc.otm_project_pr_seq','left');
					$this->db->where('tc.otm_project_pr_seq', $pr_seq);
					$this->db->where('tp.tp_seq', $tp_seq);

					$query = $this->db->get('otm_testcase_link as tl');
					$result = $query->result();
					if($result){
						$temp_arr = array();
						$temp_arr2 = array();
						foreach ($result as $temp_row)
						{
							$temp_arr[] = $temp_row->tc_inp_id;
							$temp_arr2[] = $temp_row->tc_is_task;
						}
						$new_data = array(
							'pr_seq' => $pr_seq,
							'tp_seq' => $data['tp_seq'],
							'list' => $temp_arr,
							'type' => $temp_arr2
						);
						$this->delete_testcase($new_data);
					}
				}

				$tc_inp_id = explode('_', $data['list'][$i]);
				$tc_seq = $tc_inp_id[1];
				$tp_seq = $plan[1];

				$this->delete_testcase_link(array(
						'pr_seq' => $pr_seq,
						'tp_seq' => $tp_seq,
						'tc_seq' => $tc_seq,
						'tc_inp_id' => $data['list'][$i]
					));
			}
		}

		if($data['tp_seq'] === '' || $plan[0] === 'backlog'){
			$this->db->where_in('otm_testcase_tc_seq',$this->tmp_array);
			$this->db->delete('otm_testcase_custom_value');
		}

		return "{success:true,data:".json_encode($this->tmp_array)."}";
	}


	/**
	* Function delete_testcase_link
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_testcase_link($data)
	{
		$this->db->select('tc.tc_inp_id,tl.*');
		$this->db->join('otm_testcase as tc','tc.tc_seq=tl.otm_testcase_tc_seq','left');
		if($data['tp_seq'] !== 'backlog'){
			$this->db->join('otm_testcase_plan tp','tp.otm_project_pr_seq=tc.otm_project_pr_seq','left');
			$this->db->where('tp.tp_seq', $data['tp_seq']);
			$this->db->where('otm_testcase_plan_tp_seq', $data['tp_seq']);
		}

		if($data['pr_seq']){
			$this->db->where('tc.otm_project_pr_seq', $data['pr_seq']);
		}

		$this->db->where('tc_inp_id', $data['tc_inp_id']);
		$query = $this->db->get('otm_testcase_link as tl');

		$result = $query->result();
		if($result){
			foreach ($result as $temp_row)
			{
				$this->delete_execute_result($temp_row->tl_seq);

				$this->db->where('tl_seq', $temp_row->tl_seq);
				$this->db->delete('otm_testcase_link');
			}
		}
	}

	/**
	* Function move_testcase
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function move_testcase($data)
	{
		if(isset($data['target_id']) && $data['target_id'] === 'root') $data['target_id'] = 'tc_0';

		$pr_seq		= $data['pr_seq'];
		$target_type = $data['target_type'];
		$target_id = $data['target_id'];
		$position = $data['position'];

		if($target_type == "file"){
			$select_size = count($data['select_id']);

			$str_sql = "select otm_project_pr_seq,tc_inp_pid,tc_ord from otm_testcase where otm_project_pr_seq='$pr_seq' and tc_inp_id='$target_id'";
			$query = $this->db->query($str_sql);
			$result = $query->result();
			$tc_inp_pid = $result[0]->tc_inp_pid;
			$pr_seq = $result[0]->otm_project_pr_seq;
			$tc_ord = $result[0]->tc_ord;

			if($position == "before"){
				$str_sql = "update otm_testcase set tc_ord=tc_ord+$select_size where tc_inp_pid='$tc_inp_pid' and otm_project_pr_seq='$pr_seq' and tc_ord>='$tc_ord'";
			}else if($position == "after"){
				$str_sql = "update otm_testcase set tc_ord=tc_ord+$select_size where tc_inp_pid='$tc_inp_pid' and otm_project_pr_seq='$pr_seq' and tc_ord>'$tc_ord'";
				$tc_ord++;
			}
			$query = $this->db->query($str_sql);

			for($i=0; $i<count($data['select_id']); $i++){
				$modify_array['tc_inp_pid'] = $tc_inp_pid;
				$select_id = $data['select_id'][$i];

				$tc_ord += $i;
				$modify_array['tc_ord'] = $tc_ord;

				$where = array('tc_inp_id'=>$data['select_id'][$i]);
				$this->db->update('otm_testcase', $modify_array, $where);
			}
		}else{
			if($position == "before" || $position == "after"){
				$select_size = count($data['select_id']);
				$modify_array['tc_inp_pid'] = $data['target_id'];

				$str_sql = "select otm_project_pr_seq,tc_inp_pid,tc_ord from otm_testcase where otm_project_pr_seq='$pr_seq' and tc_inp_id='$target_id'";
				$query = $this->db->query($str_sql);
				$result = $query->result();
				$tc_inp_pid = $result[0]->tc_inp_pid;
				$pr_seq = $result[0]->otm_project_pr_seq;
				$tc_ord = $result[0]->tc_ord;

				$modify_array['tc_inp_pid'] = $tc_inp_pid;

				if($position == "before"){
					$str_sql = "update otm_testcase set tc_ord=tc_ord+$select_size where tc_inp_pid='$tc_inp_pid' and otm_project_pr_seq='$pr_seq' and tc_ord>='$tc_ord'";
				}else if($position=="after"){
					$str_sql = "update otm_testcase set tc_ord=tc_ord+$select_size where tc_inp_pid='$tc_inp_pid' and otm_project_pr_seq='$pr_seq' and tc_ord>'$tc_ord'";
					$tc_ord++;
				}
				$query = $this->db->query($str_sql);

			}else{
				$modify_array['tc_inp_pid'] = $data['target_id'];
			}

			for($i=0; $i<count($data['select_id']); $i++){
				$select_id = $data['select_id'][$i];

				$str_sql = "select otm_project_pr_seq,tc_inp_pid from otm_testcase where otm_project_pr_seq='$pr_seq' and tc_inp_id='$select_id'";
				$query = $this->db->query($str_sql);
				$result = $query->result();
				$tc_inp_pid = $result[0]->tc_inp_pid;
				$pr_seq = $result[0]->otm_project_pr_seq;

				if($position == "before" || $position == "after"){
					$tc_ord += $i;
				}else{
					$tc_ord = $this->get_ord_maxval('otm_testcase',$pr_seq,'',$data['target_id']);
				}

				$modify_array['tc_ord'] = $tc_ord;

				$where = array('tc_inp_id'=>$data['select_id'][$i]);
				$this->db->update('otm_testcase', $modify_array, $where);
			}
		}
		return 'ok';
	}

	/**
	* Function get_testcase_info
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function get_testcase_info($data)
	{
		$pr_seq = $data['pr_seq'];
		$temp_arr = array();
		$result_arr = array();

		if($data['tl_seq']){
			$this->db->select("tc.*,tl.*,case when tp_subject is null then 'backlog' else tp_subject end as plan_name");
			$this->db->from('otm_testcase as tc');
			$this->db->join('otm_testcase_link as tl','tl.otm_testcase_tc_seq=tc.tc_seq','left');
			$this->db->join('otm_testcase_plan as tp','tl.otm_testcase_plan_tp_seq=tp.tp_seq','left');
			$this->db->where('tl.tl_seq', $data['tl_seq']);
			$this->db->where('tc.otm_project_pr_seq', $pr_seq);
		}else{
			$this->db->select("tc.*,tl.*, 'backlog' as plan_name");
			$this->db->from('otm_testcase as tc');
			$this->db->join('otm_testcase_link as tl','tl.otm_testcase_tc_seq=tc.tc_seq','left');
			$this->db->where('tc.tc_inp_id', $data['id']);
			$this->db->where('tc.otm_project_pr_seq', $pr_seq);
		}

		$query = $this->db->get();

		foreach ($query->result() as $temp_row)
		{
			$temp_row->writer_name = $this->get_member_info(array('mb_email'=>$temp_row->writer))->mb_name;
			if(isset($temp_row->last_writer) && $temp_row->last_writer != ''){
				$temp_row->updater_name = $this->get_member_info(array('mb_email'=>$temp_row->last_writer))->mb_name;
			}

			if( ! $data['action_type']){
				$temp_arr['data'] = $temp_row;
				$this->db->select('tr.*,pco.*,df.df_id,df.df_subject,df.df_seq');
				$this->db->from('otm_testcase_result as tr');
				$this->db->join('otm_project_code as pco','tr.otm_project_code_pco_seq=pco.pco_seq','left');//and pco.otm_project_pr_seq="$pr_seq"
				$this->db->join('otm_defect as df','tr.otm_defect_df_seq=df.df_seq','left');
				$this->db->where('otm_testcase_link_tl_seq', $temp_row->tl_seq);
				$this->db->group_by('tr.tr_seq');
				$this->db->order_by('tr.tr_seq desc');
				$result_query = $this->db->get();

				foreach ($result_query->result() as $result_temp_row)
				{
					$result_temp_row->writer_name = $this->get_member_info(array('mb_email'=>$result_temp_row->writer))->mb_name;
					$result_arr[] = $result_temp_row;
				}

			}else{
				$temp_arr = $temp_row;
			}
		}

		$tc_seq = "";
		if( ! $data['action_type']){
			$temp_arr['tc_result'] = $result_arr;
			$tc_seq = $temp_arr['data']->tc_seq;
		}else{
			$tc_seq = $temp_arr->tc_seq;
		}

		$tmp_arr = "";
		$str_sql = "select
						otm_project_customform_pc_seq as seq,
						tcv_custom_type as formtype,
						tcv_custom_value as value
					from
					otm_testcase_custom_value
					where otm_testcase_tc_seq='$tc_seq'
		";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$tmp_arr[] = $row;
		}
		if( ! $data['action_type']){
			$temp_arr['df_customform'] = json_encode($tmp_arr);
		}else{
			$temp_arr->df_customform = json_encode($tmp_arr);
		}


		$project_search_str_sql = '';
		if($pr_seq != ''){
			$project_search_str_sql = "otm_project_pr_seq='$pr_seq' and ";
		}

		$file_arr = "";

		$str_sql = "select * from otm_file where $project_search_str_sql otm_category='ID_TC' and target_seq='$tc_seq' order by of_no asc";

		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$file_arr[] = $row;
		}
		if( ! $data['action_type']){
			$temp_arr['fileform'] = json_encode($file_arr);
		}else{
			$temp_arr->fileform = json_encode($file_arr);
		}

		$data['tc_seq'] = $tc_seq;

		if( ! $data['action_type']){
			$temp_arr['testcase_history'] = json_encode($this->view_testcase_history($data));
		}else{
			if($data['action_type'] !== 'edit'){
				$temp_arr->testcase_history = json_encode($this->view_testcase_history($data));
			}
		}

		return $temp_arr;
	}

	/**
	* Function view_testcase_history
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function view_testcase_history($data)
	{
		$tc_seq = $data['tc_seq'];

		$history = array();
		$this->db->select('th.*, m.mb_name');
		$this->db->from('otm_testcase_historys as th');
		$this->db->where('otm_testcase_tc_seq',$tc_seq);
		$this->db->join('otm_member as m','th.writer = m.mb_email','left');
		$this->db->order_by('th_seq asc');

		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$history_detail = array();
			$this->db->from('otm_testcase_history_details as thd');
			$this->db->where('otm_testcase_historys_th_seq',$row->th_seq);
			$this->db->order_by('thd_seq asc');

			$query2 = $this->db->get();
			foreach ($query2->result() as $row2)
			{
				if($row2->action_type === 'tc_description'){
					$row2->value = $this->common->diffline($row2->old_value, $row2->value);
					$row2->value = nl2br($row2->value);
				}
				$history_detail[] = $row2;
			}

			$row->detail = $history_detail;
			$history[] = $row;
		}

		return $history;
	}

	/**
	* Function get_project_code_tc_result
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function get_project_code_tc_result($data)
	{
		$temp_arr = array();
		$this->db->from('otm_project_code');
		$this->db->where('pco_type', 'tc_result');
		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->order_by('pco_position asc');

		$query = $this->db->get();

		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}
		return $temp_arr;
	}

	/**
	* Function create_execute_result
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_execute_result($data)
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');
		$writer_name = $this->session->userdata('mb_name');

		$insert_array['otm_testcase_link_tl_seq'] = $data['tl_seq'];
		$insert_array['otm_project_code_pco_seq'] = $data['result_value'];
		$insert_array['tr_description'] = $data['content'];
		$insert_array['writer'] = $writer;
		$insert_array['regdate'] = $date;
		$insert_array['last_writer'] = '';
		$insert_array['last_update'] = '';

		$this->db->insert('otm_testcase_result', $insert_array);
		$result = $this->db->insert_id();

		$return_data = array();
		$return_data['result_value']		= $data['result_text'];
		$return_data['result_writer']		= $writer;
		$return_data['result_writer_name']	= $writer_name;
		$return_data['result_date']			= $date;

		return "{success:true, data:'".json_encode($return_data)."'}";
	}

	/**
	* Function delete_execute_result
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_execute_result($tl_seq)
	{
		$this->db->where('otm_testcase_link_tl_seq', $tl_seq);
		$query = $this->db->get('otm_testcase_result');
		$result = $query->result();
		if($result){
			foreach ($result as $temp_row)
			{
				$modify_array['otm_testcase_result_tr_seq'] = '';
				$where = array('otm_testcase_result_tr_seq'=>$temp_row->tr_seq);
				$this->db->update('otm_defect', $modify_array, $where);
			}
		}

		$this->db->where('otm_testcase_link_tl_seq', $tl_seq);
		$this->db->delete('otm_testcase_result');
	}

	/**
	* Function assign_testcase
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function assign_testcase($data)
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$modify_array['tl_assign_from'] = $writer;
		$modify_array['tl_assign_to'] = $data['assign_to'];
		$modify_array['tl_assign_date'] = $date;
		$modify_array['tl_assign_deadline_date'] = $data['deadline_date'];

		for($i=0; $i<count($data['tl_seq_list']); $i++){
			$this->db->from('otm_testcase_link as tl');
			$this->db->join('otm_testcase as tc','tl.otm_testcase_tc_seq=tc.tc_seq');
			$this->db->where('tl.tl_seq', $data['tl_seq_list'][$i]);
			$query = $this->db->get();
			foreach ($query->result() as $temp_row)
			{
				$this->db->from('otm_testcase_link as tl');
				$this->db->where('tl.tl_inp_pid',$temp_row->tc_inp_id);
				$this->db->where('tl.otm_testcase_plan_tp_seq', $data['tp_seq']);
				$query = $this->db->get();
				$result = $query->result();
				if($result){
					$temp_arr = array();
					foreach ($result as $temp_row)
					{
						$temp_arr[] = $temp_row->tl_seq;
					}
					$new_data = array(
						'assign_to' => $data['assign_to'],
						'deadline_date' => $data['deadline_date'],
						'tl_seq_list' => $temp_arr
					);
					$this->assign_testcase($new_data);
				}

				if($temp_row->tc_is_task === 'file'){
					$where = array('tl_seq'=>$data['tl_seq_list'][$i]);
					$this->db->update('otm_testcase_link', $modify_array, $where);
				}
			}
		}

		$assign_to = $data['assign_to'];
		$str_sql = "select mb_name from otm_member where mb_email='$assign_to'";
		$query = $this->db->query($str_sql);
		$mb_result = $query->result();

		$return_data = array();
		$return_data['assign_name']		= $mb_result[0]->mb_name;
		$return_data['deadline_date']	= $modify_array['tl_assign_deadline_date'];

		return "{success:true, data:'".json_encode($return_data)."'}";
	}


	/**
	* Function create_testcase_link
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_testcase_link($data)
	{
		for($i=0; $i<count($data['tc_id_list']); $i++){

			$this->db->where('otm_testcase_plan_tp_seq', $data['tp_seq']);
			$this->db->where('otm_testcase_tc_seq', $data['tc_seq_list'][$i]);
			$query = $this->db->get('otm_testcase_link');
			$result = $query->result();
			if($result){
				$modify_array = array();
				$modify_array['otm_testcase_plan_tp_seq'] = $data['tp_seq'];
				$modify_array['tl_inp_pid'] = ($data['pid'])?$data['pid']:'tc_0';
				$modify_array['tl_ord'] = $this->get_ord_maxval('otm_testcase_link','',$data['tp_seq'],$modify_array['tl_inp_pid']);

				$where = array('tl_seq'=>$result[0]->tl_seq);
				$this->db->update('otm_testcase_link', $modify_array, $where);

				$result = $result[0]->tl_seq;
			}
			else{

				$insert_array = array();
				$insert_array['otm_testcase_plan_tp_seq'] = $data['tp_seq'];
				$insert_array['otm_testcase_tc_seq'] = $data['tc_seq_list'][$i];
				$insert_array['tl_inp_pid'] = ($data['pid'])?$data['pid']:'tc_0';

				$insert_array['tl_ord'] = $this->get_ord_maxval('otm_testcase_link','',$data['tp_seq'],$insert_array['tl_inp_pid']);

				$this->db->insert('otm_testcase_link', $insert_array);
				$result = $this->db->insert_id();
			}

			$this->db->select('tc_seq, tc_inp_id, tc_ord');
			$this->db->where('tc_inp_pid', $data['tc_id_list'][$i]);
			$this->db->order_by('tc_ord asc'); //2016-02-15 egkang(cafe:wr_id=392) : 차수복사 시 backlog 순서(위치)로 복사되도록 정렬

			$query = $this->db->get('otm_testcase');
			$result = $query->result();
			if($result){
				$temp_arr = array();
				$temp_arr_id = array();
				foreach ($result as $temp_row)
				{
					$temp_arr[] = $temp_row->tc_seq;
					$temp_arr_id[] = $temp_row->tc_inp_id;
				}
				$new_data = array(
					'tp_seq' => $data['tp_seq'],
					'pid' => $data['tc_id_list'][$i],
					'tc_seq_list' => $temp_arr,
					'tc_id_list' => $temp_arr_id
				);
				$this->create_testcase_link($new_data);
			}
		}
		return 'ok';
	}

	/**
	* Function move_testcase_target
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function move_testcase_target($data)
	{
		if(isset($data['target_id']) && $data['target_id'] === 'root') $data['target_id'] = 'tc_0';

		$pr_seq				= $data['pr_seq'];
		$tc_plan_seq		= $data['tc_plan'];
		$target_type		= $data['target_type'];
		$target_id			= $data['target_id'];
		$position			= $data['position'];
		$target_tc_seq		= $data['target_tc_seq'];

		if($target_type == "file"){
			$select_size = count($data['select_id']);

			$str_sql = "select otm_testcase_tc_seq,tl_inp_pid,tl_ord from otm_testcase_link where otm_testcase_plan_tp_seq='$tc_plan_seq' and otm_testcase_tc_seq='$target_tc_seq'";
			$query = $this->db->query($str_sql);
			$result = $query->result();
			$tl_inp_pid = $result[0]->tl_inp_pid;
			$otm_testcase_tc_seq = $result[0]->otm_testcase_tc_seq;
			$tl_ord = $result[0]->tl_ord;

			$modify_array['tl_inp_pid'] = $tl_inp_pid;

			if($position == "before"){
				$str_sql = "update otm_testcase_link set tl_ord=tl_ord+$select_size where tl_inp_pid='$tl_inp_pid' and otm_testcase_plan_tp_seq='$tc_plan_seq' and tl_ord>='$tl_ord'";
			}else if($position=="after"){
				$str_sql = "update otm_testcase_link set tl_ord=tl_ord+$select_size where tl_inp_pid='$tl_inp_pid' and otm_testcase_plan_tp_seq='$tc_plan_seq' and tl_ord>'$tl_ord'";
				$tl_ord++;
			}
			$query = $this->db->query($str_sql);

			for($i=0; $i<count($data['select_id']); $i++){
				$modify_array['tl_inp_pid'] = $tl_inp_pid;
				$tl_seq = $data['select_id'][$i]->tl_seq;

				$tl_ord += $i;
				$modify_array['tl_ord'] = $tl_ord;

				$where = array('tl_seq'=>$tl_seq);
				$this->db->update('otm_testcase_link', $modify_array, $where);
			}
		}else{
			if($position == "before" || $position == "after"){
				$select_size = count($data['select_id']);

				$str_sql = "select otm_testcase_tc_seq,tl_inp_pid,tl_ord from otm_testcase_link where otm_testcase_plan_tp_seq='$tc_plan_seq' and otm_testcase_tc_seq='$target_tc_seq'";
				$query = $this->db->query($str_sql);
				$result = $query->result();
				$tl_inp_pid = $result[0]->tl_inp_pid;
				$otm_testcase_tc_seq = $result[0]->otm_testcase_tc_seq;
				$tl_ord = $result[0]->tl_ord;

				$modify_array['tl_inp_pid'] = $tl_inp_pid;

				if($position == "before"){
					$str_sql = "update otm_testcase_link set tl_ord=tl_ord+$select_size where tl_inp_pid='$tl_inp_pid' and otm_testcase_plan_tp_seq='$tc_plan_seq' and tl_ord>='$tl_ord'";
				}else if($position=="after"){
					$str_sql = "update otm_testcase_link set tl_ord=tl_ord+$select_size where tl_inp_pid='$tl_inp_pid' and otm_testcase_plan_tp_seq='$tc_plan_seq' and tl_ord>'$tl_ord'";
					$tl_ord++;
				}
				$query = $this->db->query($str_sql);
			}else{
				$modify_array['tl_inp_pid'] = $target_id;
			}

			for($i=0; $i<count($data['select_id']); $i++){
				$tl_seq = $data['select_id'][$i]->tl_seq;

				$str_sql = "select otm_testcase_tc_seq,tl_inp_pid from otm_testcase_link where tl_seq='$tl_seq'";
				$query = $this->db->query($str_sql);
				$result = $query->result();
				$tl_inp_pid = $result[0]->tl_inp_pid;

				if($position == "before" || $position == "after"){
					$tl_ord += $i;
				}else{
					$tl_ord = $this->get_ord_maxval('otm_testcase_link','',$tc_plan_seq,$target_id);
				}

				$modify_array['tl_ord'] = $tl_ord;

				$where = array('tl_seq'=>$tl_seq);
				$this->db->update('otm_testcase_link', $modify_array, $where);
			}
		}
		return 'ok';
	}

	/**
	* Function create_custom_value
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_custom_value($tc_seq,$form_seq,$form_type,$form_value){
		$this->db->set('otm_testcase_tc_seq',			$tc_seq);
		$this->db->set('otm_project_customform_pc_seq',	$form_seq);
		$this->db->set('tcv_custom_type',				$form_type);
		$this->db->set('tcv_custom_value',				$form_value);

		$this->db->insert('otm_testcase_custom_value');
		$this->db->insert_id();
	}


	/**
	* Function update_custom_value
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_custom_value($df_seq,$form_seq,$form_type,$form_value){
		$modify_array = array(
			'tcv_custom_type'		=> $form_type,
			'tcv_custom_value'		=> $form_value
		);
		$where = array(	'otm_testcase_tc_seq'=>$df_seq,'otm_project_customform_pc_seq'=>$form_seq);
		$this->db->update('otm_testcase_custom_value',$modify_array,$where);
	}

	/**
	* Function is_custom_value
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function is_custom_value($tc_seq,$form_seq){
		$str_sql = "select count(*) as cnt from otm_testcase_custom_value
					where
						otm_testcase_tc_seq='$tc_seq' and
						otm_project_customform_pc_seq='$form_seq'
					";
		$query = $this->db->query($str_sql);
		$tmp_arr="";
		foreach ($query->result() as $row)
		{
			$tmp_arr = $row;
		}
		return $tmp_arr->cnt;
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


	function testcase_list_export($data)
	{
		$temp_arr = array();
		$plan = $data['tcplan'];
		$plan = explode('_', $plan); //[0]: type, [1] : plan seq
		$pr_seq = $data['pr_seq'];

		/**
			Get OTM Mamber Data
		*/
		$member_list = array();
		$this->db->from('otm_member');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$member_list[$row->mb_email] = $row->mb_name;
		}
		/**
			End : Get OTM Mamber Data
		*/

		/**
			Get UserForm Data
		*/
		$p_customform = array();

		$custom_arr = array();
		$this->db->select('pc_seq,otm_project_pr_seq,pc_name,b.otm_testcase_tc_seq,b.tcv_custom_value as tcv_custom_value');
		$this->db->from('otm_project_customform as a');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where("(pc_category='ID_TC' or pc_category='TC_ITEM')");
		$this->db->where('pc_is_use','Y');
		$this->db->join('otm_testcase_custom_value as b','a.pc_seq=b.otm_project_customform_pc_seq', 'left');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$custom_arr[$row->otm_testcase_tc_seq]["_".$row->pc_seq] = $row;
		}

		$column_arr = array();
		$this->db->select('pc_seq,pc_name,pc_category');
		$this->db->from('otm_project_customform');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('pc_is_use','Y');
		$this->db->where("(pc_category='ID_TC' or pc_category='TC_ITEM')");

		$this->db->order_by('pc_category', 'desc');
		$this->db->order_by('ABS(pc_1)', 'asc');
		$this->db->order_by('pc_seq', 'asc');

		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$cnt++;
			array_push($column_arr,array("_".$row->pc_seq,"_".$row->pc_name));
		}
		/*
			End : TC customform
		*/

		if($plan[0] === 'backlog'){
			$str_sql = "
					select
						tc_seq,tc_out_id,tc_inp_id,tc_inp_pid,tc_subject,tc_is_task, writer, regdate
					from
						otm_testcase as tc
					where
						tc.otm_project_pr_seq='$pr_seq'
					order by tc_ord asc
			";
		}else{
			$str_sql = "
				select a.* from
				(
					select
						a.tc_seq,a.tl_ord,tc_out_id,tc_inp_id,tc_inp_pid,tc_subject,tc_is_task,result_writer,result_value
					from
					(
						select a.*,group_concat(r.writer) as result_writer, group_concat(r.pco_name) as result_value from
						(
							select
								tc_seq,tc_out_id,tc_inp_id,tc_inp_pid,tc_subject,tc_is_task,tl_seq,tl_ord, tc.writer, tc.regdate
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
							select r.otm_testcase_link_tl_seq,r.writer,c.pco_name from otm_testcase_result as r, otm_project_code as c where otm_project_pr_seq='$pr_seq' and c.pco_seq=r.otm_project_code_pco_seq
						) as r
						on
						a.tl_seq=r.otm_testcase_link_tl_seq
						group by a.tl_seq
					) as a
					group by tc_seq
				) as a
				order by a.tl_ord asc
			";
		}
		$query = $this->db->query($str_sql);
		$result_length = 0;
		foreach ($query->result() as $temp_row)
		{
			$temp_arr['location'] = "";
			$temp_arr['tc_seq'] = $temp_row->tc_seq;

			$temp_arr['id'] = $temp_row->tc_inp_id;
			$temp_arr['tc_id'] = $temp_row->tc_out_id;
			$temp_arr['pid'] = $temp_row->tc_inp_pid;
			$temp_arr['subject'] = $temp_row->tc_subject;
			$temp_arr['tc_is_task'] = $temp_row->tc_is_task;

			for($k=0;$k<sizeof($column_arr);$k++){
				$temp_arr[$column_arr[$k][1]] = $custom_arr[$temp_row->tc_seq][$column_arr[$k][0]]->tcv_custom_value;
			}

			$temp_arr['writer'] = $member_list[$temp_row->writer];
			$temp_arr['regdate'] = substr($temp_row->regdate, 0, 10);

			$result_writer = "";
			$result_value = "";

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
		$folder_except = array();
		$suite_id=0;
		for($i=0;$i<sizeof($this->return_array);$i++){

			if($this->return_array[$i]['tc_is_task'] != "folder"){
				if(!$data['return_tc_seq']){
					unset($this->return_array[$i]['tc_seq']);
				}
				unset($this->return_array[$i]['id']);
				if(!$data['return_pid']){
					unset($this->return_array[$i]['pid']);
				}
				unset($this->return_array[$i]['tc_is_task']);

				array_push($folder_except,$this->return_array[$i]);
			}else{
				$suite_id = $this->return_array[$i]['tc_id'];
			}
		}
		return $folder_except;
	}


	/**
	* Function copy_comtestcase
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function copy_comtestcase($data)
	{
		$temp_arr = array();
		$project_seq = $data['project_seq'];
		$product_seq = $data['p_seq'];
		$vsrsion_seq = $data['v_seq'];

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$before	= array('/cts/', '/ctc/');
		$after	= array('ts', 'tc');

		$data['pr_seq'] = $data['project_seq'];
		$input_form = $this->input_item_list($data,"array");

		$new_inp_id = array();
		$new_inp_pid = array();

		if(isset($project_seq) && isset($product_seq) && isset($vsrsion_seq)){

			$this->db->from('otm_com_testcase');
			$this->db->where('otm_com_version_v_seq',$vsrsion_seq);
			$query = $this->db->get();

			foreach ($query->result() as $temp_row)
			{
				$this->db->from('otm_testcase');
				$this->db->where('otm_project_pr_seq',$project_seq);
				$this->db->where('tc_out_id',$temp_row->ct_out_id);
				$modify_query = $this->db->get();

				if($modify_query->num_rows() > 0){

					$modify_array['tc_subject'] = $temp_row->ct_subject;
					$modify_array['tc_is_task'] = $temp_row->ct_is_task;

					$modify_array['writer'] = $temp_row->writer;
					$modify_array['regdate'] = $temp_row->regdate;
					$modify_array['last_writer'] = $temp_row->last_writer;
					$modify_array['last_update'] = $temp_row->last_update;

					foreach ($modify_query->result() as $modify_temp_row)
					{
						$modify_where = array(
							'otm_project_pr_seq'=>$project_seq,
							'tc_seq'=>$modify_temp_row->tc_seq
							);
						$this->db->update('otm_testcase', $modify_array, $modify_where);

						/*
							테스트케이스 입력항목 업데이트
						*/
						$modify_custom_arr = array();
						for($k=1;$k<6;$k++){
							$kk = $k-1;
							switch($k)
							{
								case 1:
									$modify_custom_arr['tcv_custom_value'] = ($temp_row->ct_precondition)?$temp_row->ct_precondition:'';
									break;
								case 2:
									$modify_custom_arr['tcv_custom_value'] = ($temp_row->ct_testdata)?$temp_row->ct_testdata:'';
									break;
								case 3:
									$modify_custom_arr['tcv_custom_value'] = ($temp_row->ct_procedure)?$temp_row->ct_procedure:'';
									break;
								case 4:
									$modify_custom_arr['tcv_custom_value'] = ($temp_row->ct_expected_result)?$temp_row->ct_expected_result:'';
									break;
								case 5:
									$modify_custom_arr['tcv_custom_value'] = ($temp_row->ct_description)?$temp_row->ct_description:'';
									break;
							}

							$modify_custom_where = array(
								'otm_project_customform_pc_seq'=>$input_form[$kk]->pc_seq,
								'otm_testcase_tc_seq'=>$modify_temp_row->tc_seq
							);
							$this->db->update('otm_testcase_custom_value', $modify_custom_arr, $modify_custom_where);
						}
					}
					continue;
				}

				if($temp_row->ct_inp_pid === 'ctc_0'){
					$insert_array['tc_inp_pid'] = preg_replace($before, $after, $temp_row->ct_inp_pid);
				}else{
					$insert_array['tc_inp_pid'] = $temp_row->ct_inp_pid;
				}

				$insert_array['tc_inp_id'] = $temp_row->ct_inp_id;
				$insert_array['tc_out_id'] = $temp_row->ct_out_id;
				$insert_array['otm_project_pr_seq'] = $project_seq;
				$insert_array['tc_subject'] = $temp_row->ct_subject;
				$insert_array['tc_is_task'] = $temp_row->ct_is_task;
				$insert_array['tc_ord'] = $this->get_ord_maxval('otm_testcase',$project_seq,'',$insert_array['tc_inp_pid']);
				$insert_array['writer'] = $temp_row->writer;
				$insert_array['regdate'] = $temp_row->regdate;
				$insert_array['last_writer'] = $temp_row->last_writer;
				$insert_array['last_update'] = $temp_row->last_update;

				$this->db->insert('otm_testcase', $insert_array);
				$result = $this->db->insert_id();
				$tc_seq = $result;

				if($temp_row->ct_is_task === 'folder'){
					$id_head = 'ts_';
				}else{
					$id_head = 'tc_';
				}

				$modify_array['tc_inp_id'] = $id_head.$result;
				$where = array(
					'otm_project_pr_seq'=>$project_seq,
					'tc_seq'=>$result
					);
				$this->db->update('otm_testcase', $modify_array, $where);

				array_push($new_inp_id, array('id'=>$id_head.$result,'old_id'=>$temp_row->ct_inp_id));

				for($k=1;$k<6;$k++){
					$kk = $k-1;
					$custom_arr = array();
					$custom_arr['otm_testcase_tc_seq'] = $tc_seq;
					$custom_arr['otm_project_customform_pc_seq'] = $input_form[$kk]->pc_seq;
					$custom_arr['tcv_custom_type'] = $input_form[$kk]->pc_formtype;
					switch($k)
					{
						case 1:
							$custom_arr['tcv_custom_value'] = ($temp_row->ct_precondition)?$temp_row->ct_precondition:'';
							break;
						case 2:
							$custom_arr['tcv_custom_value'] = ($temp_row->ct_testdata)?$temp_row->ct_testdata:'';
							break;
						case 3:
							$custom_arr['tcv_custom_value'] = ($temp_row->ct_procedure)?$temp_row->ct_procedure:'';
							break;
						case 4:
							$custom_arr['tcv_custom_value'] = ($temp_row->ct_expected_result)?$temp_row->ct_expected_result:'';
							break;
						case 5:
							$custom_arr['tcv_custom_value'] = ($temp_row->ct_description)?$temp_row->ct_description:'';
							break;
					}

					$this->db->insert('otm_testcase_custom_value', $custom_arr);
				}
			}

			for($i=0; $i<count($new_inp_id); $i++)
			{
				$modify_array2['tc_inp_pid'] = $new_inp_id[$i]['id'];
				$where2 = array(
					'otm_project_pr_seq'=>$project_seq,
					'tc_inp_pid'=>$new_inp_id[$i]['old_id']
					);
				$this->db->update('otm_testcase', $modify_array2, $where2);

			}
		}
		return "success";
	}


	/**
	* Function input_item_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function input_item_list($data,$type="json")
	{
		$pr_seq = $data['pr_seq'];

		$arr = array();

		$str_sql = "select pc_seq,pc_name,pc_formtype,pc_category,pc_1 from otm_project_customform where otm_project_pr_seq='$pr_seq' and pc_category='TC_ITEM'";

		$str_sql_cnt = "select count(*) as cnt from ($str_sql) as a";
		$query = $this->db->query($str_sql_cnt);
		$cnt_result = $query->result();

		$str_sql = "select * from ($str_sql) as a order by a.pc_category desc, ABS(a.pc_1) asc, a.pc_seq asc";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}
		if($type == "array"){
			return $arr;
		}else{
			return "{success:true,totalCount: ".$cnt_result[0]->cnt.", data:".json_encode($arr)."}";
		}
	}


	/**
	* Function import
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	public function import($data)
	{
		echo "<script> top.myUpdateProgress(0,'Step 1 : Data Loading...');</script>";

		$userform_param = array(
			'pr_seq' => $data['pr_seq'],
			'pc_category' => 'ID_TC',
			'pc_is_use'	=>	'Y'
		);
		$userform_list = $this->load->model('project_setup_m')->userform_list($userform_param);

		echo "<script> top.myUpdateProgress(10,'Step 1 : Data Loading...');</script>";

		$worksheet	= $data['import_data'];
		unset($data['import_data']);

		echo "<script> top.myUpdateProgress(20,'Step 1 : Data Loading...');</script>";

		$highestRow	= $worksheet->getHighestRow();
		echo "<script> top.myUpdateProgress(30,'Step 1 : Data Loading...');</script>";

		$highestColumn      = $worksheet->getHighestColumn();
		echo "<script> top.myUpdateProgress(40,'Step 1 : Data Loading...');</script>";

		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		echo "<script> top.myUpdateProgress(50,'Step 1 : Data Loading...');</script>";

		echo "<script> top.myUpdateProgress(100,'Step 1 : Data Loading...');</script>";
		if($highestRow > 1001){
			$result_data['result'] = FALSE;
			$msg['over'] = 'Over Max Row(1000) : '.($highestRow -1);
			$result_data['msg'] = json_encode($msg);

			return $result_data;
		}
		if($highestColumnIndex > 50){
			$result_data['result'] = FALSE;
			$msg['over'] = 'Over Max Column(50) : '.$highestColumnIndex;
			$result_data['msg'] = json_encode($msg);
			return $result_data;
		}

		echo "<script> top.myUpdateProgress(0,'Step 2 : Data Checking...');</script>";

		$this->tmp_location_array = array();
		$tc_out_id = array();
		for ($row = 2; $row <= $highestRow; ++ $row) {
			$location_cell = $worksheet->getCellByColumnAndRow(0, $row);
			$location = $location_cell->getValue();
			$data['location'] = trim($location);
			$this->import_location($data);

			if($data['import_check_id']){
				$tc_id_cell = $worksheet->getCellByColumnAndRow(1, $row);
				$tc_id = $tc_id_cell->getValue();
				array_push($tc_out_id,trim($tc_id));
			}
			$tmp_per = (round(($row/$highestRow)*100) > 20)?(round(($row/$highestRow)*100)-20):0;

			echo "<script> top.myUpdateProgress(".$tmp_per.",'Step 2 : Data Checking...');</script>";
		}

		if($data['import_check_id']){
			$duplicate_id_array = array();
			$duplicate_seq_array = array();
			$this->db->select('tc_seq, tc_out_id');
			$this->db->from('otm_testcase');
			$this->db->where('tc_is_task','file');
			$this->db->where('otm_project_pr_seq',$data['pr_seq']);
			$this->db->where_in('tc_out_id',$tc_out_id);
			$query = $this->db->get();
			if($query->result()){
				foreach ($query->result() as $row)
				{
					array_push($duplicate_id_array,$row->tc_out_id);
					$duplicate_seq_array[$row->tc_out_id] = $row->tc_seq;
				}
			}
		}

		if(count($duplicate_id_array) > 0 && $data['update'] == false){

			$result_data['result'] = FALSE;
			$msg['duplicate_id'] = $duplicate_id_array;
			$result_data['msg'] = json_encode($msg);

			return $result_data;
		}

		for ($row = 2; $row <= $highestRow; ++ $row) {
			$custom_form_data = array();

			$col_array = array();
			$userform_no = 0;

			for ($col = 0; $col < $highestColumnIndex; ++ $col) {
				$custom_form_row_data = array();

				$cell = $worksheet->getCellByColumnAndRow($col, $row);
				$val = $cell->getValue();

				switch($col)
				{
					case 0:	$col_id = 'location';
							$col_array[$col_id] = trim($val);
						break;
					case 1:	$col_id = 'tc_id';
							$col_array[$col_id] = trim($val);
						break;
					case 2:	$col_id = 'tc_subject';
							$col_array[$col_id] = $val;
						break;
					default:
							if(isset($userform_list[$userform_no])){
								$custom_form_row_data['name'] = $userform_list[$userform_no]->pc_name;
								$custom_form_row_data['seq'] = $userform_list[$userform_no]->pc_seq;
								$custom_form_row_data['type'] = $userform_list[$userform_no]->pc_formtype;
								$custom_form_row_data['value'] = $val;
								array_push($custom_form_data,$custom_form_row_data);
								$userform_no++;
							}else{
								continue;
							}
						break;
				}
			}

			$tmp = $this->tmp_location_array;
			$pid = $tmp[$col_array['location']];

			if (count($duplicate_id_array) > 0 && in_array($col_array['tc_id'], $duplicate_id_array)) {
				$import_excel_data = array(
					'type' => 'file',
					'pr_seq' => $data['pr_seq'],
					'pid' => $pid,
					'tc_out_id' => $col_array['tc_id'],
					'tc_seq' => $duplicate_seq_array[$col_array['tc_id']],
					'tc_subject' => $col_array['tc_subject'],
					'tc_precondition' => $col_array['tc_precondition'],
					'tc_testdata' => $col_array['tc_testdata'],
					'tc_procedure' => $col_array['tc_procedure'],
					'tc_expected_result' => $col_array['tc_expected_result'],
					'tc_description' => $col_array['tc_description'],
					'tc_ord' => '1',
					'custom_form' => json_encode($custom_form_data),
					'return_key' => 'seq'
				);
				$tc_id = $this->update_testcase($import_excel_data);
			}else{
				$import_excel_data = array(
					'type' => 'file',
					'pr_seq' => $data['pr_seq'],
					'pid' => $pid,
					'tc_out_id' => $col_array['tc_id'],
					'tc_subject' => $col_array['tc_subject'],
					'tc_precondition' => $col_array['tc_precondition'],
					'tc_testdata' => $col_array['tc_testdata'],
					'tc_procedure' => $col_array['tc_procedure'],
					'tc_expected_result' => $col_array['tc_expected_result'],
					'tc_description' => $col_array['tc_description'],
					'tc_ord' => '1',
					'custom_form' => json_encode($custom_form_data),
					'return_key' => 'seq'
				);
				$seq = $this->create_testcase($import_excel_data);
			}

			echo "<script> top.myUpdateProgress(".round(($row/$highestRow)*100).",'Step 3 : Data Importing...(".$col_array['tc_id'].":".$row."/".$highestRow.")');</script>";
		}

		$result_data['result'] = TRUE;
		$result_data['msg'] = $highestRow;

		return $result_data;
	}

	/**
	* Function import_location
	*
	* @return array
	*/
	function import_location($data)
	{
		//Insert Suite
		$tmp = $this->tmp_location_array;

		if(isset($tmp[$data['location']]))
		{
			$pid = $tmp[$data['location']];
		}
		else
		{
			$location = explode('/',$data['location']);
			$pid = 'tc_0';

			for($i=0; $i<count($location); $i++)
			{
				$subject = trim($location[$i]);
				if(($subject === '' OR $subject === NULL)) continue;

				$duplicate['table'] = 'otm_testcase';
				$duplicate['key'] = array(
										array('column'=>'otm_project_pr_seq','value'=>$data['pr_seq']),
										array('column'=>'tc_subject','value'=>$subject),
										array('column'=>'tc_inp_pid','value'=>$pid),
										array('column'=>'tc_is_task','value'=>'folder')
									);
				$duplicate['update_key'] = '';
				$duplicate['select_key'] = 'tc_seq';

				$check_value = $this->duplicate_check($duplicate);
				if($check_value){
					$tc_seq = $check_value;
				}else{
					$create_data = array(
						'type' => 'suite',
						'pr_seq' => $data['pr_seq'],
						'pid' => $pid,
						'tc_out_id' => '',
						'tc_subject' => $subject,
						'tc_precondition' => '',
						'tc_testdata' => '',
						'tc_procedure' => '',
						'tc_expected_result' => '',
						'tc_description' => '',
						'tc_ord' => '1',
						'return_key' => 'seq'
					);

					$tc_seq = $this->create_testcase($create_data);
				}

				$pid = 'ts_'.$tc_seq;
			}

			$tmp[$data['location']] = $pid;
			$this->tmp_location_array = $tmp;
		}
	}


	/**
	* Function import_testcase_csv
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	public function import_testcase_csv($data)
	{
		$pr_seq = $data['pr_seq'];
		$filename = $_FILES['form_file']['name'];
		$tmp_file = $_FILES['form_file']['tmp_name'];
		$file_handle = fopen($tmp_file, "r");

		$input_form = $this->input_item_list($data,"array");

		$k = 0;
		$message = "";
		$csvFile = array();
		$columns = sizeof($input_form)+1;
		$HeadArray = array();
		$tmpStr="";
		$import_data=array();
		$conditionName=array();
		while (!feof($file_handle) ) {
			$tmp_text = "";
			$line_of_text = fgetcsv($file_handle, 1024);

			if($line_of_text[0]){
				array_push($csvFile,$line_of_text);

				if($k!=0){
					for($i=0;$i<$columns;$i++){
						$import_data[$k][$i]=$line_of_text[$i];
					}
				}
				$k++;
			}
		}

		for($i=1;$i<=sizeof($import_data);$i++){
			$tmp_data = array();
			$tmp_data['pr_seq'] = $pr_seq;
			$tmp_data['tc_subject']			= mb_convert_encoding(str_replace("\n","<br>",$import_data[$i][0]),'utf-8',"euckr");
			$tmp_data['pid']				= 0;
			$tmp_data['call_function']		= "import_testcase_csv";

			$tc_seq = $this->create_testcase($tmp_data);

			for($k=1;$k<$columns;$k++){
				$custom_value	= mb_convert_encoding($import_data[$i][$k],'utf-8',"euckr");

				$kk = $k-1;
				$custom_arr = array();
				$custom_arr['otm_testcase_tc_seq'] = $tc_seq;
				$custom_arr['otm_project_customform_pc_seq'] = $input_form[$kk]->pc_seq;
				$custom_arr['tcv_custom_type'] = $input_form[$kk]->pc_formtype;
				$custom_arr['tcv_custom_value'] = $custom_value;

				$this->db->insert('otm_testcase_custom_value', $custom_arr);
			}
		}
		return "{success:true}";
	}
}
//End of file testcase_m.php
//Location: ./models/testcase_m.php