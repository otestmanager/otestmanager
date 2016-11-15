<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Defect_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/

class Defect_m extends CI_Model {

	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->library('File_Form');
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
		$this->db->where('pco_type', 'df_id_rule');
		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->where('pco_is_default', 'Y');
		$query = $this->db->get('otm_project_code');

		$df_id_rule = $query->result();

		if($df_id_rule){
		}else{
			return "empty";
		}

		$pco_name = $df_id_rule[0]->pco_name;
		$pco_default_value = $df_id_rule[0]->pco_default_value;

		/*
		 * 0 : text
		 * 1 : '-' or '_'
		 * 2 : date
		 * 3 : '-' or '_'
		 * 4 : id number length
		 */
		$rule_arr = preg_split('/,/',$pco_default_value);

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
		$this->db->where('id_type', 'defect');
		$max_seq = $this->db->get('otm_id_generator')->result();

		if($max_seq[0]->id_seq && $max_seq[0]->id_seq >= 1){
			$max_seq = $max_seq[0]->id_seq + 1;

			if($num_length < strlen($max_seq)){
				return 'over_num';
			}

			$modify_array = array('id_seq'=>$max_seq);
			$where = array(	'otm_project_pr_seq'=>$data['pr_seq'],
							'id_type'=>'defect');
			$this->db->update('otm_id_generator',$modify_array,$where);

		}else{
			$max_seq = 1;

			$this->db->set('otm_project_pr_seq',	$data['pr_seq']);
			$this->db->set('id_type',				'defect');
			$this->db->set('id_seq',				$max_seq);
			$this->db->insert('otm_id_generator');

		}

		$df_id_num = sprintf("%0".$num_length."d", $max_seq);
		return substr_replace($pco_name, $df_id_num, strlen($pco_name)-$num_length, $num_length);
	}


	/**
	* Function defect_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function defect_list($data)
	{
		$pr_seq = $data['project_seq'];
		$start = $data['start'];
		$limit = $data['limit'];
		if($start != null && $limit != null){
			$limitSql = " limit $limit OFFSET $start ";
		}else{
			$limitSql = "";
		}

		/**
			Get Project Code Data
		*/
		$p_code = array();
		$this->db->from('otm_project_code as a');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$p_code[$row->pco_seq] = $row->pco_name;
		}

		/**
			Get Project Member
		*/
		$member_arr = array();
		$this->db->from('otm_member');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$member_arr[$row->mb_email] = $row->mb_name;
		}

		/**
			Get UserForm Data
		*/
		//$p_customform = array();

		$custom_arr = array();
		$this->db->select('pc_seq,otm_project_pr_seq,pc_name,b.otm_defect_df_seq,b.cv_custom_value as cv_custom_value');
		$this->db->from('otm_project_customform as a');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('pc_category','ID_DEFECT');
		$this->db->where('pc_is_use','Y');
		$this->db->join('otm_defect_custom_value as b','a.pc_seq=b.otm_project_customform_pc_seq', 'left');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$custom_arr[$row->otm_defect_df_seq]["_".$row->pc_seq] = $row;
		}

		$this->db->select('pc_seq,pc_name,pc_formtype');
		$this->db->from('otm_project_customform');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('pc_category','ID_DEFECT');
		$this->db->where('pc_is_use','Y');
		$this->db->order_by('ABS(pc_1)', 'asc');

		$query = $this->db->get();

		$cnt = 0;

		$custom_form_array = array();
		$column_arr = array();
		foreach ($query->result() as $row)
		{
			$cnt++;
			array_push($column_arr,"_".$row->pc_seq);
			$custom_form_array[] = $row;
		}

		$this->db->start_cache();

		$arr = array();



		$search_json = json_decode($data['search_array']);
		$search_num = 0;

		for($i=sizeof($search_json)-1;$i>=0;$i--){
			$sfl = (isset($search_json[$i]->sfl))?$search_json[$i]->sfl:null;
			$stx = (isset($search_json[$i]->stx))?$search_json[$i]->stx:null;
			$sop = (isset($search_json[$i]->sop))?$search_json[$i]->sop:null;
			$start_date = (isset($search_json[$i]->search_start_date))?$search_json[$i]->search_start_date:null;
			$end_date = (isset($search_json[$i]->search_end_date))?$search_json[$i]->search_end_date:null;
			$status = (isset($search_json[$i]->search_status))?$search_json[$i]->search_status:null;
			$userform_combo_value = (isset($search_json[$i]->search_userform_combo))?$search_json[$i]->search_userform_combo:null;

			$start_date = str_replace(" ","",substr($start_date,0,10));
			$end_date = str_replace(" ","",substr($end_date,0,10));

			if($sfl && ($stx || $start_date || $end_date || $status || $userform_combo_value)){
				$search_num++;

				switch($sfl){
					case "subject":
						if($search_num >1 && $sop == 'OR'){
							$this->db->or_like('df_subject',$stx);
						}else{
							$this->db->like('df_subject',$stx);
						}
					break;
					case "description":
						if($search_num >1 && $sop == 'OR'){
							$this->db->or_like('df_description',$stx);
						}else{
							$this->db->like('df_description',$stx);
						}
					break;
					case "writer":
						if($search_num >1 && $sop == 'OR'){
							$this->db->or_like('mb.mb_name',$stx);
						}else{
							$this->db->like('mb.mb_name',$stx);
						}
					break;
					case "charge":
						if($search_num >1 && $sop == 'OR'){
							$this->db->or_like('asm.mb_name',$stx);
						}else{
							$this->db->like('asm.mb_name',$stx);
						}
					break;
					case "regdate":
						if($search_num >1 && $sop == 'OR'){
							$this->db->where(" or (date_format(a.regdate,'%Y-%m-%d')>='$start_date' and date_format(a.regdate,'%Y-%m-%d')<='$end_date') ");
						}else{
							$this->db->where(" (date_format(a.regdate,'%Y-%m-%d')>='$start_date' and date_format(a.regdate,'%Y-%m-%d')<='$end_date') ");
						}
					break;
					case "status":
						if($search_num >1 && $sop == 'OR'){
							$this->db->or_where(" e.dc_current_status_co_seq='$status'");
							//$this->db->or_like('e.dc_current_status_co_seq',$status);
						}else{
							$this->db->where(" e.dc_current_status_co_seq='$status' ");
							//$this->db->like('e.dc_current_status_co_seq',$status);
						}
					break;
					default:
						$form_type = "";
						for($k=0;$k<sizeof($custom_form_array);$k++){
							if($sfl == $custom_form_array[$k]->pc_seq){
								$form_type = $custom_form_array[$k]->pc_formtype;
								break;
							}
						}
						if($form_type){
							switch($form_type){
								case "radio":
								case "combo":
									$this->db->join("(select cv_custom_value as '_$sfl', otm_defect_df_seq as 'df_$sfl' from otm_defect_custom_value where otm_project_customform_pc_seq='$sfl') as cv_$sfl","a.df_seq=cv_$sfl.df_$sfl",'left');
									if($search_num >1 && $sop == 'OR'){
										$this->db->or_like("_$sfl",$userform_combo_value);
									}else{
										$this->db->like("_$sfl",$userform_combo_value);
									}
								break;
								case "datefield":
									$this->db->join('otm_defect_custom_value as cv',"cv.otm_project_customform_pc_seq='$sfl' and cv.cv_custom_value>='".$start_date."' and cv.cv_custom_value<='".$end_date."'",'left');
									$this->db->where('a.df_seq = cv.otm_defect_df_seq');
								break;
								default:
									$this->db->join("(select cv_custom_value as '_$sfl_$i', otm_defect_df_seq as 'df_$sfl_$i' from otm_defect_custom_value where otm_project_customform_pc_seq='$sfl') as cv_$sfl_$i","a.df_seq=cv_$sfl_$i.df_$sfl_$i",'left');
									if($search_num >1 && $sop == 'OR'){
										$this->db->or_like("_$sfl_$i",$stx);
									}else{
										$this->db->like("_$sfl_$i",$stx);
									}
								break;
							}
						}else{

						}

					break;
				}
			}
		}

		if(isset($data['role']) && $data['role'] !=''){
			if($data['role'] === 'all'){

			}else{
				$writer = $this->session->userdata('mb_email');
				//$where_quy .= " and (writer='$writer' or dc_to='$writer')";
				$this->db->where("(a.writer='$writer' or e.dc_to='$writer')");
			}
		}else{
			return "{success:true,totalCount: 0, data:[]}";
		}

		$this->db->select('a.df_seq,a.otm_project_pr_seq,a.otm_testcase_result_tr_seq,a.df_id,a.df_subject,
										a.df_severity,a.df_priority,a.df_frequency,a.writer,a.regdate,a.last_writer,a.last_update,
										e.dc_to,e.dc_current_status_co_seq as df_status,
										asm.mb_name as df_assign_member,
										mb.mb_name as writer_name
										');

		$this->db->from('otm_defect as a');
		$this->db->where('a.otm_project_pr_seq',$pr_seq);
		$this->db->join("otm_defect_assign as e",'a.df_seq=e.otm_defect_df_seq','');
		$this->db->join('otm_member as asm','e.dc_to=asm.mb_email and a.otm_project_pr_seq="'.$pr_seq.'"','left');
		$this->db->join('otm_member as mb','a.writer=mb.mb_email and a.otm_project_pr_seq="'.$pr_seq.'"','left');

		$this->db->stop_cache();

		$cnt_result = $this->db->count_all_results();

		$this->db->select('a.df_seq,a.otm_project_pr_seq,a.otm_testcase_result_tr_seq,a.df_id,a.df_subject,
										a.df_severity,a.df_priority,a.df_frequency,a.writer,a.regdate,a.last_writer,a.last_update,
										asm.mb_name as df_assign_member,
										mb.mb_name as writer_name
										');
		//$order_by_sql = " order by a.df_seq desc ";
		$sort = $data['sort'][0];

		if($sort){
			//$order_by_sql = " order by ";
			foreach($sort as $row => $v){
				//$order_by_sql .= $v.' ';
				$this->db->order_by($v,'');
			}
		}else{
			$this->db->order_by('a.df_seq','desc');
		}
		if($limitSql != ""){
			$this->db->limit($limit,$start);
		}
		$query = $this->db->get();

		foreach ($query->result() as $row)
		{
			$row->tp_subject = implode(',',array_unique(explode(",",$row->tp_subject)));

			for($k=0;$k<sizeof($column_arr);$k++){
				$row->$column_arr[$k] = $custom_arr[$row->df_seq][$column_arr[$k]]->cv_custom_value;
			}

			$row->df_assign_member = $row->df_assign_member;
			$row->writer_name = $row->writer_name;

			$row->df_status_name = $p_code[$row->df_status];
			$row->df_severity_name = $p_code[$row->df_severity];
			$row->df_priority_name = $p_code[$row->df_priority];
			$row->df_frequency_name = $p_code[$row->df_frequency];

			$row->regdate = substr($row->regdate, 0, 10);

			$arr[] = $row;
		}
		//return $this->db->last_query();

		return "{success:true,totalCount: ".$cnt_result.", data:".json_encode($arr)."}";
	}

	/**
	* Function create_defect
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_defect($data)
	{
		$pr_seq = $data['pr_seq'];
		$tr_seq = $data['tr_seq'];
		$d_subject = $data['d_subject'];
		$d_description = $data['d_description'];
		$d_severity = $data['d_severity'];
		$d_priority = $data['d_priority'];
		$d_frequency = $data['d_frequency'];

		$d_status = $data['d_status'];
		$d_assign_member = $data['d_assign_member'];
		$d_start_date = $data['d_start_date'];
		$d_end_date = $data['d_end_date'];
		$custom_form = $data['custom_form'];

		$date=date("Y-m-d H:i:s");
		$writer = $this->session->userdata('mb_email');

		if($data['df_id']){
			$this->db->set('df_id', $data['df_id']);
		}else{
			$df_id = $this->get_id($data);

			if($df_id !== 'over_num' && $df_id !== 'empty'){
				$this->db->set('df_id',	$df_id);
			}else{
				$data['msg'] = $df_id;
				return $data;
			}
		}

		$this->db->set('otm_project_pr_seq',	$pr_seq);
		$this->db->set('otm_testcase_result_tr_seq',	$tr_seq);
		$this->db->set('df_subject',			$d_subject);
		$this->db->set('df_description',		$d_description);
		$this->db->set('df_severity',			$d_severity);
		$this->db->set('df_priority',			$d_priority);
		$this->db->set('df_frequency',			$d_frequency);

		if(isset($data['writer'])){
			$this->db->set('writer',			$data['writer']);
			$this->db->set('last_writer',		$data['writer']);
		}else{
			$this->db->set('writer',			$writer);
			$this->db->set('last_writer',		$writer);
		}

		if(isset($data['regdate'])){
			$this->db->set('regdate',			$data['regdate']);
		}else{
			$this->db->set('regdate',			$date);
		}

		if(isset($data['last_update'])){
			$this->db->set('last_update',		$data['last_update']);
		}else{
			$this->db->set('last_writer',		$writer);
			$this->db->set('last_update',		$date);
		}

		$this->db->insert('otm_defect');
		$df_seq = $this->db->insert_id();

		if(isset($tr_seq)){
			$modify_array_tr = array('otm_defect_df_seq'=>$df_seq);
			$where_tr = array('tr_seq'=>$tr_seq);
			$this->db->update('otm_testcase_result',$modify_array_tr,$where_tr);


			$history = array();
			$history['pr_seq'] = $data['pr_seq'];
			$cnt = 0;

			$this->db->select('tl.tl_seq,tc.tc_seq,tc.tc_out_id ');
			$this->db->from('otm_testcase_result as tr');
			$this->db->join('otm_testcase_link as tl','tr.otm_testcase_link_tl_seq=tl.tl_seq', 'left');
			$this->db->join('otm_testcase as tc','tl.otm_testcase_tc_seq=tc.tc_seq', 'left');
			$this->db->where('tr_seq',$tr_seq);

			$query = $this->db->get();
			$cnt = 0;
			foreach ($query->result() as $row)
			{
				$history['category']	= 'testcase';
				$history['tc_seq']		= $row->tl_seq;
				$history['details']	= array();
				$history['details'][$cnt]['action_type']= 'tracking_tc';
				$history['details'][$cnt]['old_value']	= $df_id;
				$history['details'][$cnt]['value']		= 'set_link';
				$this->history->history($history);

				$history['category']	= 'defect';
				$history['df_seq']		= $df_seq;
				$history['details']	= array();
				$history['details'][$cnt]['action_type']= 'tracking_df';
				$history['details'][$cnt]['old_value']	= $row->tc_out_id;
				$history['details'][$cnt]['value']		= 'set_link';
				$this->history->history($history);
			}
		}

		if(isset($d_status)){
			$this->db->set('otm_defect_df_seq',			$df_seq);
			$this->db->set('dc_from',					$writer);
			$this->db->set('dc_to',						$d_assign_member);
			$this->db->set('dc_start_date',				$d_start_date);
			$this->db->set('dc_end_date',				$d_end_date);
			$this->db->set('dc_regdate',				$date);
			$this->db->set('dc_current_status_co_seq',	$d_status);

			$this->db->insert('otm_defect_assign');
			$this->db->insert_id();
		}

		$custom_arr = json_decode($custom_form);

		if(sizeof($custom_arr) >= 1){
			for($i=0;$i<sizeof($custom_arr);$i++){
				$form_seq = $custom_arr[$i]->seq;
				$form_type = $custom_arr[$i]->type;
				$form_value = $custom_arr[$i]->value;

				$this->create_custom_value($df_seq,$form_seq,$form_type,$form_value);
			}
		}

		if(isset($data['return_key'])){
			return $df_seq;
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

					$file_data['category'] = 'ID_DEFECT';
					$file_data['pr_seq'] = $pr_seq;
					$file_data['target_seq'] = $df_seq;
					$file_data['of_no'] = $i;


					$this->File_Form->file_upload($file_data);
				}
			}
		}

        return $df_seq;
	}

	/**
	* Function update_defect
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_defect($data){
		$pr_seq = $data['pr_seq'];
		$df_seq = $data['d_seq'];
		$df_subject = $data['d_subject'];
		$df_description = $data['d_description'];
		$df_severity = $data['d_severity'];
		$df_priority = $data['d_priority'];
		$df_frequency = $data['d_frequency'];
		$df_status = $data['d_status'];
		$df_assign_member = $data['d_assign_member'];
		$df_start_date = $data['d_start_date'];
		$df_end_date = $data['d_end_date'];
		$custom_form = $data['custom_form'];

		$history = array();
		$history['pr_seq'] = $pr_seq;
		$history['df_seq'] = $df_seq;
		$history['details'] = array();

		$date=date("Y-m-d H:i:s");
		$writer = $this->session->userdata('mb_email');

		$this->db->from('otm_defect');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('df_seq',$df_seq);

		$query = $this->db->get();
		$cnt = 0;
		foreach ($query->result() as $row)
		{
			if($df_subject !== $row->df_subject){
				$history['details'][$cnt]['action_type'] = 'df_subject';
				$history['details'][$cnt]['old_value'] = $row->df_subject;
				$history['details'][$cnt]['value'] = $df_subject;
				$cnt++;
			}
			if($df_description !== $row->df_description){
				$history['details'][$cnt]['action_type'] = 'df_description';
				$history['details'][$cnt]['old_value'] = $row->df_description;
				$history['details'][$cnt]['value'] = $df_description;
				$cnt++;
			}
			if($df_severity !== $row->df_severity){
				$history['details'][$cnt]['action_type'] = 'df_severity';
				$history['details'][$cnt]['old_value'] = $row->df_severity;
				$history['details'][$cnt]['value'] = $df_severity;
				$cnt++;
			}
			if($df_priority !== $row->df_priority){
				$history['details'][$cnt]['action_type'] = 'df_priority';
				$history['details'][$cnt]['old_value'] = $row->df_priority;
				$history['details'][$cnt]['value'] = $df_priority;
				$cnt++;
			}
			if($df_frequency !== $row->df_frequency){
				$history['details'][$cnt]['action_type'] = 'df_frequency';
				$history['details'][$cnt]['old_value'] = $row->df_frequency;
				$history['details'][$cnt]['value'] = $df_frequency;
				$cnt++;
			}
		}

		$modify_array = array(
			'df_subject'		=> $df_subject,
			'df_description'	=> $df_description,
			'df_severity'		=> $df_severity,
			'df_priority'		=> $df_priority,
			'df_frequency'		=> $df_frequency,
			'last_writer'		=> $writer,
			'last_update'		=> $date
		);
		$where = array(	'otm_project_pr_seq'=>$pr_seq,'df_seq'=>$df_seq);
		$this->db->update('otm_defect',$modify_array,$where);

		$this->db->from('otm_defect_assign');
		$this->db->where('otm_defect_df_seq',$df_seq);
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$tmp_arr = $row;
		}

		if(isset($df_status)){
			if($df_status !== $tmp_arr->dc_current_status_co_seq){
				$history['details'][$cnt]['action_type'] = 'df_status';
				$history['details'][$cnt]['old_value'] = $tmp_arr->dc_current_status_co_seq;
				$history['details'][$cnt]['value'] = $df_status;
				$cnt++;

				$modify_array = array('dc_current_status_co_seq' => $df_status);
				$where = array(	'otm_defect_df_seq'=>$df_seq);
				$this->db->update('otm_defect_assign',$modify_array,$where);
			}
		}

		if(isset($df_assign_member)){
			if($df_assign_member !== $tmp_arr->dc_to){
				$history['details'][$cnt]['action_type'] = 'df_assign';
				$history['details'][$cnt]['old_value'] = $tmp_arr->dc_to;
				$history['details'][$cnt]['value'] = $df_assign_member;
				$cnt++;

				$modify_array = array(
					'dc_from'					=> $writer,
					'dc_to'						=> $df_assign_member,
					'dc_regdate'				=> $date
				);
				$where = array(	'otm_defect_df_seq'=>$df_seq);
				$this->db->update('otm_defect_assign',$modify_array,$where);
			}
		}

		if(isset($df_start_date) && $df_start_date){
			$tmp_arr->dc_start_date = substr($tmp_arr->dc_start_date, 0, 10);
			if($df_start_date !== $tmp_arr->dc_start_date){
				$history['details'][$cnt]['action_type'] = 'df_start_date';
				$history['details'][$cnt]['old_value'] = $tmp_arr->dc_start_date;
				$history['details'][$cnt]['value'] = $df_start_date;
				$cnt++;
			}
			$modify_array = array(
				'dc_start_date'				=> $df_start_date,
				'dc_regdate'				=> $date
			);
			$where = array(	'otm_defect_df_seq'=>$df_seq);
			$this->db->update('otm_defect_assign',$modify_array,$where);
		}
		if(isset($df_end_date) && $df_end_date){
			$tmp_arr->dc_end_date = substr($tmp_arr->dc_end_date, 0, 10);
			if($df_end_date !== $tmp_arr->dc_end_date){
				$history['details'][$cnt]['action_type'] = 'df_end_date';
				$history['details'][$cnt]['old_value'] = $tmp_arr->dc_end_date;
				$history['details'][$cnt]['value'] = $df_start_date;
				$cnt++;
			}

			$modify_array = array(
				'dc_end_date'				=> $df_end_date,
				'dc_regdate'				=> $date
			);
			$where = array(	'otm_defect_df_seq'=>$df_seq);
			$this->db->update('otm_defect_assign',$modify_array,$where);
		}

		$custom_arr = json_decode($custom_form);
		if(sizeof($custom_arr) >= 1){
			for($i=0;$i<sizeof($custom_arr);$i++){
				$form_name = $custom_arr[$i]->name;
				$form_seq = $custom_arr[$i]->seq;
				$form_type = $custom_arr[$i]->type;
				$form_value = $custom_arr[$i]->value;

				$is_item = $this->is_custom_value($df_seq,$form_seq);
				if($is_item >= 1){

					$this->db->from('otm_defect_custom_value');
					$this->db->where('otm_defect_df_seq',$df_seq);
					$this->db->where('otm_project_customform_pc_seq',$form_seq);
					$query = $this->db->get();
					foreach ($query->result() as $row)
					{
						$tmp_fomarr = $row;
					}

					if($form_value !== $tmp_fomarr->cv_custom_value){
						$history['details'][$cnt]['action_type'] = $form_name;
						$history['details'][$cnt]['old_value'] = $tmp_fomarr->cv_custom_value;
						$history['details'][$cnt]['value'] = $form_value;
						$cnt++;
					}

					$this->update_custom_value($df_seq,$form_seq,$form_type,$form_value);
				}else{

					$history['details'][$cnt]['action_type'] = $form_name;
					$history['details'][$cnt]['old_value'] = '';
					$history['details'][$cnt]['value'] = $form_value;
					$cnt++;

					$this->create_custom_value($df_seq,$form_seq,$form_type,$form_value);
				}
			}
		}

		if(isset($data['return_key'])){
			return $df_seq;
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

					$file_data['category'] = 'ID_DEFECT';
					$file_data['pr_seq'] = $pr_seq;
					$file_data['target_seq'] = $df_seq;
					$file_data['of_no'] = 2;

					$history['details'][$cnt]['action_type'] = 'df_file';
					$history['details'][$cnt]['old_value'] = '';
					$history['details'][$cnt]['value'] = $file_data['source']['name'];
					$cnt++;

					$this->File_Form->file_upload($file_data);
				}
			}
		}

		$history['category']= 'defect';
		$this->history->history($history);

		return $df_seq;
	}

	/**
	* Function is_custom_value
	*
	* @param array $data Post Data.
	*
	* @return integer
	*/
	function is_custom_value($df_seq,$form_seq){
		/*
		$str_sql = "select count(*) as cnt from otm_defect_custom_value
					where
						otm_defect_df_seq='$df_seq' and
						otm_project_customform_pc_seq='$form_seq'
					";
		$query = $this->db->query($str_sql);
		*/
		$this->db->select('count(*) as cnt');
		$this->db->where('otm_defect_df_seq',$df_seq);
		$this->db->where('otm_project_customform_pc_seq',$form_seq);
		$query = $this->db->get('otm_defect_custom_value');

		$tmp_arr="";
		foreach ($query->result() as $row)
		{
			$tmp_arr = $row;
		}
		return $tmp_arr->cnt;
	}

	/**
	* Function create_custom_value
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_custom_value($df_seq,$form_seq,$form_type,$form_value){
		$this->db->set('otm_defect_df_seq',				$df_seq);
		$this->db->set('otm_project_customform_pc_seq',	$form_seq);
		$this->db->set('cv_custom_type',				$form_type);
		$this->db->set('cv_custom_value',				$form_value);

		$this->db->insert('otm_defect_custom_value');
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
			'cv_custom_type'		=> $form_type,
			'cv_custom_value'		=> $form_value
		);
		$where = array(	'otm_defect_df_seq'=>$df_seq,'otm_project_customform_pc_seq'=>$form_seq);
		$this->db->update('otm_defect_custom_value',$modify_array,$where);
	}

	/**
	* Function delete_defect
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_defect($data){
		$project_seq = $data['project_seq'];

		for($i=0; $i<count($data['df_list']); $i++)
		{
			$df_seq = $data['df_list'][$i];

			$delete_array = array(
				'otm_project_pr_seq' => $project_seq,
				'df_seq'=>$df_seq
			);

			$result = $this->db->delete('otm_defect',$delete_array);

			$delete_array = array('otm_defect_df_seq' => $df_seq);
			$result = $this->db->delete('otm_defect_assign',$delete_array);

			$this->db->delete('otm_defect_custom_value',$delete_array);
			$this->db->delete('otm_defect_comment',$delete_array);

			$history['category']= 'defect';
			$history['pr_seq']	= $project_seq;
			$history['df_seq']	= $df_seq;
			$this->history->delete_history($history);

			$modify_array = array(
				'otm_defect_df_seq'		=> null
			);
			$where = array(	'otm_defect_df_seq'=>$df_seq);
			$this->db->update('otm_testcase_result',$modify_array,$where);
		}

		return $result;
	}

	/**
	* Function view_defect
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function view_defect($data)
	{
		$df_seq = $data['df_seq'];
		$pr_seq = $data['pr_seq'];

		$arr = "";

		$str_sql = "
			select
				a.*,
				date_format(b.dc_start_date,'%Y-%m-%d')as dc_start_date,
				date_format(b.dc_end_date,'%Y-%m-%d')as dc_end_date,
				b.dc_to as dc_to_seq,
				(select mb_name from otm_member where b.dc_to=otm_member.mb_email)as dc_to,
				(select pco_name from otm_project_code as c where b.dc_current_status_co_seq=c.pco_seq) as status_name,
				(select pco_seq from otm_project_code as c where b.dc_current_status_co_seq=c.pco_seq) as status_seq
			from
			(
				select
					df_seq,df_id,df_subject,df_description,regdate,a.df_severity,a.df_priority,a.df_frequency,
					(select mb_name from otm_member where a.writer=otm_member.mb_email)as writer,
					(select mb_name from otm_member where a.last_writer=otm_member.mb_email)as last_writer,
					(select pco_name from otm_project_code as b where a.df_severity=b.pco_seq) as severity_name,
					(select pco_name from otm_project_code as b where a.df_priority=b.pco_seq) as priority_name,
					(select pco_name from otm_project_code as b where a.df_frequency=b.pco_seq) as frequency_name
				from
				otm_defect as a
				where df_seq='$df_seq'
			) as a
			left outer join
			(
				select * from otm_defect_assign where dc_seq = (select max(dc_seq) from otm_defect_assign where otm_defect_df_seq='$df_seq')
			) as b
			on a.df_seq=b.otm_defect_df_seq
		";

		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr = $row;
		}

		$tmp_arr = "";
		/*
		$str_sql = "select
						otm_project_customform_pc_seq as seq,
						cv_custom_type as formtype,
						cv_custom_value as value
					from
					otm_defect_custom_value
					where otm_defect_df_seq='$df_seq'
		";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$tmp_arr[] = $row;
		}
		*/
		
		$this->db->select('otm_project_customform_pc_seq as seq,
						cv_custom_type as formtype,
						cv_custom_value as value');
		$this->db->where('otm_defect_df_seq',$df_seq);		
		$query = $this->db->get('otm_defect_custom_value');
		$tmp_arr = $query->result_array();
		$arr->df_customform = json_encode($tmp_arr);


		/* attached file */
		$file_arr = "";
		//$str_sql = "select * from otm_file where otm_project_pr_seq='$pr_seq' and otm_category='ID_DEFECT' and target_seq='$df_seq' order by of_no asc";
		//$query = $this->db->query($str_sql);
		//foreach ($query->result() as $row)
		//{
		//	$file_arr[] = $row;
		//}

		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('otm_category','ID_DEFECT');
		$this->db->where('target_seq',$df_seq);
		$this->db->order_by('of_no asc');
		$query = $this->db->get('otm_file');
		$file_arr = $query->result_array();
		$arr->fileform = json_encode($file_arr);

		$arr->next_status = json_encode('{permission=>1,permission=>2}');

		$arr->defect_history = json_encode($this->view_defect_history($data));


		$result_arr = array();
		$this->db->select('tr.*,pco.*,tc.tc_out_id,tc.tc_subject,tc.tc_seq');
		$this->db->from('otm_testcase_result as tr');
		$this->db->join('otm_project_code as pco','tr.otm_project_code_pco_seq=pco.pco_seq','left');
		$this->db->join('otm_testcase_link as tl','tl.tl_seq=tr.otm_testcase_link_tl_seq','left');
		$this->db->join('otm_testcase as tc','tl.otm_testcase_tc_seq=tc.tc_seq','left');
		$this->db->where('tr.otm_defect_df_seq', $df_seq);
		$this->db->group_by('tr.tr_seq');
		$this->db->order_by('tr.tr_seq desc');
		$result_query = $this->db->get();

		foreach ($result_query->result() as $result_temp_row)
		{
			$result_arr[] = $result_temp_row;
		}

		$arr->tc_result = $result_arr;

		return $arr;
	}

	/**
	* Function view_defect_history
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function view_defect_history($data)
	{
		$df_seq = $data['df_seq'];
		//$pr_seq = $data['pr_seq'];

		$history = array();

		$this->db->select('dh.*, m.mb_name');
		$this->db->from('otm_defect_historys as dh');
		$this->db->where('otm_defect_df_seq',$df_seq);
		$this->db->join('otm_member as m','dh.writer = m.mb_email','left');
		$this->db->order_by('dh_seq asc');

		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$history_detail = array();
			$this->db->select('dhd.*,m.mb_name');
			$this->db->from('otm_defect_history_details as dhd');
			$this->db->where('otm_defect_historys_dh_seq',$row->dh_seq);
			$this->db->join('otm_member as m',"dhd.action_type='df_assign' and dhd.value=m.mb_email",'left');
			$this->db->order_by('dhd_seq asc');

			$query2 = $this->db->get();
			foreach ($query2->result() as $row2)
			{
				if($row2->action_type === 'df_description'){
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
	* Function code_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function code_list($data){
		$arr = array();

		$this->db->select('*');
		$this->db->from('otm_project_code');
		$this->db->where('otm_project_pr_seq', $data['project_seq']);
		$this->db->order_by('pco_position,pco_seq asc');

		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}
		return $arr;
	}

	/**
	* Function defect_list_export
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function defect_list_export($data)
	{
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
		/**
			End : Get OTM Mamber Data
		*/


		/**
			Get Project Code Data
		*/
		$p_code = array();

		$this->db->from('otm_project_code as a');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$p_code[$row->pco_seq] = $row->pco_name;
		}
		/**
			End : Get Project Code Data
		*/


		/**
			Get UserForm Data
		*/
		//$p_customform = array();

		$custom_arr = array();
		$this->db->select('pc_seq,otm_project_pr_seq,pc_name,b.otm_defect_df_seq,b.cv_custom_value as cv_custom_value');
		$this->db->from('otm_project_customform as a');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('pc_category','ID_DEFECT');
		$this->db->where('pc_is_use','Y');
		$this->db->join('otm_defect_custom_value as b','a.pc_seq=b.otm_project_customform_pc_seq', 'left');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$custom_arr[$row->otm_defect_df_seq][$row->pc_seq] = $row;
		}

		$this->db->select('pc_seq,pc_name,pc_formtype');
		$this->db->from('otm_project_customform');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('pc_category','ID_DEFECT');
		$this->db->where('pc_is_use','Y');
		$this->db->order_by('ABS(pc_1)', 'asc');
		$this->db->order_by('pc_seq', 'asc');

		$query = $this->db->get();
		$custom_form_array = array();
		$column_arr = array();
		foreach ($query->result() as $row)
		{
			array_push($column_arr,$row->pc_seq);
			$custom_form_array[] = $row;
		}
		/**
			End : Get UserForm Data
		*/

		/**
			Get Image File Data
		*/
		$defect_images = array();
		$this->db->select('otm_project_pr_seq as pr_seq, otm_category as category, of_no,target_seq,of_source,of_file,of_width,of_height');
		$this->db->from('otm_file');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('otm_category','ID_DEFECT');
		$files_query = $this->db->get();
		foreach ($files_query->result() as $row)
		{
			$row->path = '/uploads/files/'.$pr_seq.'/'.$row->of_file;
			$defect_images[$row->target_seq][$row->of_no] = $row;
		}
		/**
			End : Get Image File Data
		*/

		$arr = array();

		$this->db->select('a.df_seq, a.otm_project_pr_seq, a.otm_testcase_result_tr_seq, a.df_id, a.df_subject, a.df_description, a.df_severity, a.df_priority, a.df_frequency, a.writer, a.regdate,
						e.dc_current_status_co_seq as df_status,
						e.dc_start_date, e.dc_end_date,
						e.dc_to as df_assign');
		$this->db->from('otm_defect as a');
		$this->db->where('a.otm_project_pr_seq',$pr_seq);
		$this->db->join("otm_defect_assign as e",'a.df_seq=e.otm_defect_df_seq','');

		$this->db->order_by('a.df_seq','desc');

		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$tmp_arr = array();

			$row->df_status = $p_code[$row->df_status];
			$row->df_severity = $p_code[$row->df_severity];
			$row->df_priority = $p_code[$row->df_priority];
			$row->df_frequency = $p_code[$row->df_frequency];

			$row->df_assign = $member_list[$row->df_assign];

			if(!$row->dc_start_date) $row->dc_start_date = '0000-00-00';
			if(!$row->dc_end_date) $row->dc_end_date = '0000-00-00';

			$row->regdate = substr($row->regdate, 0, 10);
			$row->writer = $member_list[$row->writer];

			if($defect_images[$row->df_seq]){
				$row->df_file = $defect_images[$row->df_seq];
			}else{
				$row->df_file = array();
			}

			$tmp_arr['ID(*)'] = $row->df_id;

			$tmp_arr[lang('subject').'(*)'] = $row->df_subject;
			$tmp_arr[lang('description').'(*)'] = $row->df_description;
			$tmp_arr[lang('severity').'(*)'] = $row->df_severity;
			$tmp_arr[lang('priority').'(*)'] = $row->df_priority;
			$tmp_arr[lang('frequency').'(*)'] = $row->df_frequency;
			$tmp_arr[lang('status').'(*)'] = $row->df_status;
			$tmp_arr[lang('responsible_person').'(*)'] = $row->df_assign;
			$tmp_arr[lang('start_date').'(*)'] = (substr($row->dc_start_date,0,10) == '0000-00-00')?'':substr($row->dc_start_date,0,10);
			$tmp_arr[lang('end_date').'(*)'] = (substr($row->dc_end_date,0,10) == '0000-00-00')?'':substr($row->dc_end_date,0,10);

			for($i=0; $i<count($column_arr); $i++){
				$tmp_arr[$custom_form_array[$i]->pc_name."(*)"] = $custom_arr[$row->df_seq][$column_arr[$i]]->cv_custom_value;
			}

			$tmp_arr[lang('writer')] = $row->writer;
			$tmp_arr[lang('regdate')] = $row->regdate;
			$tmp_arr['otm_export_images'] = $row->df_file;

			$arr[] = $tmp_arr;
		}
		return $arr;
		exit;
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
		$pr_seq = $data['project_seq'];
		$str = "<script> top.myUpdateProgress(0,'Step 1 : Data Loading...');</script>";
		echo $str;

		$worksheet	= $data['import_data'];
		unset($data['import_data']);

		$highestRow	= $worksheet->getHighestRow();
		$highestColumn      = $worksheet->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		$str = "<script> top.myUpdateProgress(100,'Step 1 : Data Loading...');</script>";
		echo $str;

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
		$str = "<script> top.myUpdateProgress(0,'Step 2 : Data Checking...');</script>";
		echo $str;

		/*
			ID 중복 확인
		*/
		$df_id_arry = array();
		for ($row = 2; $row <= $highestRow; ++ $row) {
			if($data['import_check_id']){
				$df_id_cell = $worksheet->getCellByColumnAndRow(0, $row);
				$df_id = $df_id_cell->getValue();
				array_push($df_id_arry,trim($df_id));
			}
			$tmp_per = (round(($row/$highestRow)*100) > 20)?(round(($row/$highestRow)*100)-20):0;
			$str = "<script> top.myUpdateProgress(".$tmp_per.",'Step 2 : Data Checking...');</script>";
			echo $str;
		}

		if($data['import_check_id']){
			$duplicate_id_array = array();
			$duplicate_seq_array = array();
			$this->db->select('df_seq, df_id');
			$this->db->from('otm_defect');
			$this->db->where('otm_project_pr_seq',$pr_seq);
			$this->db->where_in('df_id',$df_id_arry);
			$query = $this->db->get();
			if($query->result()){
				foreach ($query->result() as $row)
				{
					array_push($duplicate_id_array,$row->df_id);
					$duplicate_seq_array[$row->df_id] = $row->df_seq;
				}
			}
		}

		if(count($duplicate_id_array) > 0 && $data['update'] == false){
			$result_data['result'] = FALSE;
			$msg['duplicate_id'] = $duplicate_id_array;
			$result_data['msg'] = json_encode($msg);

			return $result_data;
		}
		/*
			End : ID 중복 확인
		*/


		/**
			Get OTM Mamber Data
		*/
		$member_list = array();
		$this->db->from('otm_member');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$member_list[$row->mb_name] = $row->mb_email;
		}
		/**
			End : Get OTM Mamber Data
		*/


		/**
			Get Project Code Data
		*/
		$p_code = array();

		$this->db->from('otm_project_code as a');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$p_code[$row->pco_name] = $row->pco_seq;
		}
		/**
			End : Get Project Code Data
		*/


		/**
			Get UserForm Data
		*/
		$this->db->select('pc_seq,pc_name,pc_formtype');
		$this->db->from('otm_project_customform');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('pc_category','ID_DEFECT');
		$this->db->where('pc_is_use','Y');
		$this->db->order_by('ABS(pc_1)', 'asc');
		$this->db->order_by('pc_seq', 'asc');

		$query = $this->db->get();
		$userform_list = array();
		foreach ($query->result() as $row)
		{
			$userform_list[] = $row;
		}

		/**
			End : Get UserForm Data
		*/


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
					case 0:	$col_id = 'df_id';
							$col_array[$col_id] = trim($val);
						break;
					case 1:	$col_id = 'd_subject';
							$col_array[$col_id] = trim($val);
						break;
					case 2:	$col_id = 'd_description';
							$col_array[$col_id] = $val;
						break;
					case 3:	$col_id = 'd_severity';
							$col_array[$col_id] = ($p_code[$val])?$p_code[$val]:'';
						break;
					case 4:	$col_id = 'd_priority';
							$col_array[$col_id] = ($p_code[$val])?$p_code[$val]:'';
						break;
					case 5:	$col_id = 'd_frequency';
							$col_array[$col_id] = ($p_code[$val])?$p_code[$val]:'';
						break;
					case 6:	$col_id = 'd_status';
							$col_array[$col_id] = ($p_code[$val])?$p_code[$val]:'';
						break;
					case 7:	$col_id = 'd_assign_member';
							$col_array[$col_id] = $member_list[$val];
						break;
					case 8:	$col_id = 'd_start_date';
							$col_array[$col_id] = $val;
						break;
					case 9:	$col_id = 'd_end_date';
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

			if (count($duplicate_id_array) > 0 && in_array($col_array['df_id'], $duplicate_id_array)) {
				/*
					Defect Update
				*/
				$import_excel_data = array(
					'pr_seq' => $pr_seq,
					'd_seq' => $duplicate_seq_array[$col_array['df_id']],
					'df_id' => $col_array['df_id'],
					'd_subject' => $col_array['d_subject'],
					'd_description' => $col_array['d_description'],
					'd_severity' => $col_array['d_severity'],
					'd_priority' => $col_array['d_priority'],
					'd_frequency' => $col_array['d_frequency'],
					'd_status' => $col_array['d_status'],
					'd_assign_member' => $col_array['d_assign_member'],
					'd_start_date' => $col_array['d_start_date'],
					'd_end_date' => $col_array['d_end_date'],
					'custom_form' => json_encode($custom_form_data),
					'role' => '',
					'return_key' => 'seq'
				);
				$df_id = $this->update_defect($import_excel_data);
			}else{
				/*
					Defect Insert
				*/
				$import_excel_data = array(
					'pr_seq' => $pr_seq,
					'tr_seq' => '',
					'df_id' => $col_array['df_id'],
					'd_subject' => $col_array['d_subject'],
					'd_description' => $col_array['d_description'],
					'd_severity' => $col_array['d_severity'],
					'd_priority' => $col_array['d_priority'],
					'd_frequency' => $col_array['d_frequency'],
					'd_status' => $col_array['d_status'],
					'd_assign_member' => $col_array['d_assign_member'],
					'd_start_date' => $col_array['d_start_date'],
					'd_end_date' => $col_array['d_end_date'],
					'custom_form' => json_encode($custom_form_data),
					'return_key' => 'seq'
				);
				$seq = $this->create_defect($import_excel_data);
				if($seq['msg'] && $seq['msg'] == 'empty'){
					$result_data['result'] = FALSE;

					$site_lang = $this->session->userdata('mb_lang');
					switch($site_lang)
					{
						case "ko":
							$msg['msg'] = '결함 ID 체계를 등록해주세요.';
							break;
						default:
							$msg['msg'] = 'Please, set defect id rule.';
							break;
					}

					$result_data['msg'] = json_encode($msg);
					return $result_data;
				}
			}
			$str = "<script> top.myUpdateProgress(".round(($row/$highestRow)*100).",'Step 3 : Data Importing...(".$col_array['df_id'].":".$row."/".$highestRow.")');</script>";
			echo $str;
		}

		$result_data['result'] = TRUE;
		$result_data['msg'] = $highestRow;

		return $result_data;
	}


	/**
	* Function defect_dashboard_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function defect_dashboard_list($data)
	{
		$writer = $this->session->userdata('mb_email');

		$start = $data['start'];
		$limit = $data['limit'];
		if($start != null && $limit != null){
			$limitSql = " limit $limit OFFSET $start ";
		}else{
			$limitSql = "";
		}

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
			Get Project Code Data
		*/
		$p_code = array();
		//$p_code_name = array();
		$this->db->from('otm_project_code');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			if($row->pco_is_required == 'Y'){
				$row->pco_name = '<font color=blue>'.$row->pco_name.'</font>';
			}
			$p_code[$row->pco_seq] = $row->pco_name;
		}
		/**
			End : OTM Mamber Data
		*/

		/**
			Defect List
		*/
		$this->db->start_cache();
		$this->db->select('a.df_seq,
						a.otm_project_pr_seq,
						pr.pr_name,
						a.otm_testcase_result_tr_seq,
						a.df_id,a.df_subject,
						a.df_severity,
						a.df_priority,
						a.df_frequency,
						a.writer,
						a.regdate,
						e.dc_seq,
						e.dc_to,
						e.dc_current_status_co_seq as df_status

			');
		$this->db->from('otm_defect as a');
		$this->db->join('(select dc_seq,otm_defect_df_seq,dc_to,dc_current_status_co_seq from otm_defect_assign) as e','a.df_seq=e.otm_defect_df_seq and dc_seq=e.dc_seq','');
		$this->db->join('(select pr_seq,pr_name from otm_project) as pr','pr.pr_seq=a.otm_project_pr_seq','left');

		if($data['defect_list_option']){
			if($data['defect_list_option'] == 'all'){
				$this->db->where("(a.writer = '".$writer."' or e.dc_to = '".$writer."')");
			}else if($data['defect_list_option'] == 'writer'){
				$this->db->where('a.writer',$writer);
			}else if($data['defect_list_option'] == 'assign'){
				$this->db->where('e.dc_to', $writer);
			}
		}else{
			$this->db->where("(a.writer = '".$writer."' or e.dc_to = '".$writer."')");
		}

		/**
			Search
		*/
		$search_json = json_decode($data['search_array']);

		$sfl = (isset($search_json[0]->sfl))?$search_json[0]->sfl:null;
		$stx = (isset($search_json[0]->stx))?$search_json[0]->stx:null;
		$start_date = (isset($search_json[0]->search_start_date))?$search_json[0]->search_start_date:null;
		$end_date = (isset($search_json[0]->search_end_date))?$search_json[0]->search_end_date:null;

		$start_date = str_replace(" ","",substr($start_date,0,10));
		$end_date = str_replace(" ","",substr($end_date,0,10));

		if($sfl && ($stx || $start_date || $end_date)){
			switch($sfl){
				case "regdate":
					if($start_date){
						$this->db->where(" date_format(a.regdate,'%Y-%m-%d') >= ", $start_date);
					}
					if($end_date){
						$this->db->where(" date_format(a.regdate,'%Y-%m-%d') <= ", $end_date);
					}
				break;
				case "status":
					$ss = array();
					foreach($p_code as $k=>$v){
						if(preg_match('/'.$stx.'/', $v)){
							array_push($ss,$k);
						}
					}

					$this->db->where_in('e.dc_current_status_co_seq',$ss);
				break;
			}
		}
		/**
			End : Search
		*/

		$this->db->stop_cache();
		$cnt_result = $this->db->count_all_results();

		$this->db->order_by('a.otm_project_pr_seq','desc');
		$this->db->order_by('a.df_seq','desc');

		if($limitSql != ""){
			$this->db->limit($limit,$start);
		}

		$arr = array();

		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$row->df_assign_member = $member_list[$row->dc_to];
			$row->writer_name = $member_list[$row->writer];

			$row->df_status_name = $p_code[$row->df_status];
			$row->df_severity_name = $p_code[$row->df_severity];
			$row->df_priority_name = $p_code[$row->df_priority];
			$row->df_frequency_name = $p_code[$row->df_frequency];

			$row->regdate = substr($row->regdate, 0, 10);

			//if($row->pr_name){
				$arr[] = $row;
			//}


		}
		return "{success:true,totalCount: ".$cnt_result.", data:".json_encode($arr)."}";
	}


	/**
	* Function send_mail
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function send_mail($data)
	{
		$pr_seq = $data['project_seq'];
		$user_list = $data['user_list'];
		$df_list = $data['defect_list'];

		$subject = $data['subject'];
		//$content = iconv('utf-8','euc-kr',nl2br($data['content']));
		$content = nl2br($data['content']);

		/*
		 *	Project Info
		 */
		//$project_info = array();
		$pr_name = '';

		$this->db->select('pr_seq, pr_name');
		$this->db->from('otm_project');
		$this->db->where('pr_seq', $pr_seq);
		$result_query = $this->db->get();

		foreach ($result_query->result() as $result_temp_row)
		{
			//$pr_name = iconv('utf-8','euc-kr',$result_temp_row->pr_name);
			$pr_name = $result_temp_row->pr_name;
		}

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
			Get Project Code Data
		*/
		$p_code = array();

		$this->db->from('otm_project_code as a');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$p_code[$row->pco_seq] = $row->pco_name;
		}
		/**
			End : Get Project Code Data
		*/


		/*
		 *	Defect Info
		 */
		$defect_info_list = array();
		$defect_id_arr	= array();

		//$this->db->query('set names euc-kr');
		$this->db->select('a.df_seq, a.otm_project_pr_seq, a.otm_testcase_result_tr_seq, a.df_id, a.df_subject, a.df_description, a.df_severity, a.df_priority, a.df_frequency, a.writer, a.regdate,
						e.dc_current_status_co_seq as df_status,
						e.dc_start_date, e.dc_end_date,
						e.dc_to as df_assign');
		$this->db->from('otm_defect as a');
		$this->db->where('a.otm_project_pr_seq',$pr_seq);
		$this->db->where_in('df_seq',$df_list);
		$this->db->join("otm_defect_assign as e",'a.df_seq=e.otm_defect_df_seq','');
		$this->db->order_by('a.df_seq','desc');
		$result_query = $this->db->get();


		/*
		 *	Mail Message
		 */
		$defect_list_msg = "<table style='padding:3px;width:100%;border: 1px solid #2DA5DA;'><tr style='background-color:#2DA5DA;font-weight: bold;color:#ffffff;text-align:center;'><td style='width:120px;'>ID</td><td style='width:430px;'>Subject</td><td style='width:60px;'>Status</td><td style='width:60px;'>Responsible Person</td><td style='width:60px;'>Writer</td><td style='width:60px;'>Write Date</td></tr>";
		$i=0;
		foreach ($result_query->result() as $result_temp_row)
		{
			$defect_info_list[] = $result_temp_row;
			array_push($defect_id_arr, $result_temp_row->df_id);

			if($i % 2 == 1){
				$row_style = "style='background-color:#dddddd;'";
			}else{
				$row_style = "style=''";
			}

			$result_id = $result_temp_row->df_id;
			$result_subject = $result_temp_row->df_subject;
			$result_status = $p_code[$result_temp_row->df_status];
			$result_assign = $member_list[$result_temp_row->df_assign];
			$result_writer = $member_list[$result_temp_row->writer];

			$defect_list_msg .= "<tr ".$row_style."><td>".$result_id."</td><td>".$result_subject."</td><td>".$result_status."</td><td>".$result_assign."</td><td>".$result_writer."</td><td>".substr($result_temp_row->regdate,0,10)."</td></tr>";
			$i++;
		}
		$defect_list_msg .= "</table>";

		//$yoil_ko = array("일","월","화","수","목","금","토");
		//if($mb_lang === "ko"){
		//	$yoil = '('.$yoil_ko[date('w')].')';
		//}
		//$datetime = date('Y-m-d').$yoil.date('H:i:s');

		/*
		 * Send Mail
		 */
		$this->load->library('sendmail');

		for($i=0; $i<count($user_list); $i++){

			$templet = "
				<table width='500px' style='padding:1px;border: 1px solid #2DA5DA;background-color:#2DA5DA;'>
					<tr><td style='color:#ffffff;font-weight: bold;'>OTestManager Mail - Defect </td></tr>
					<tr><td>
						<table width='500px' style='padding:3px; border: 1px solid #bcbcbc; background-color:#ffffff;'>
							<tr>
								<td style='width:60px; background-color:#dddddd; font-weight: bold;'>Project Name</td><td style='width:10px;'> : </td><td style='width:430px;'>".$pr_name."</td>
							</tr>
							<tr>
								<td colspan=3>".$defect_list_msg."</td>
							</tr>
						</table>
					</td></tr>
				</table>
			";

			$send_data = array();
			$send_data['from'] = $this->session->userdata('mb_email');
			$send_data['to'] = $user_list[$i];
			$send_data['subject'] = $subject;
			$send_data['message'] = $content.'<br><br>'.$templet;
			$msg = $this->sendmail->send($send_data);
		}

		return $msg;
	}
}
//End of file defect_m.php
//Location: ./models/defect_m.php
?>