<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Riskanalysis_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Riskanalysis_m extends CI_Model {

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
		$pr_seq = $node[1];

		//example : testcase model / plan_tree_list
		if($node[0] === 'riskanalysis'){
			$i=0;
			$temp_arr[$i]['pr_seq'] = $node[1];
			$temp_arr[$i]['pid'] = $data['node'];
			$temp_arr[$i]['id'] = 'riskanalysis_riskitem_'.$pr_seq;
			$temp_arr[$i]['text'] = '리스크 아이템 관리';
			$temp_arr[$i]['type'] = 'riskanalysis_riskitem';
			//$temp_arr[$i]['iconCls'] = 'ico-backlog';
			$temp_arr[$i]['leaf'] = TRUE;
			$i++;

			$temp_arr[$i]['pr_seq'] = $node[1];
			$temp_arr[$i]['pid'] = $data['node'];
			$temp_arr[$i]['id'] = 'riskanalysis_discussion_'.$pr_seq;
			$temp_arr[$i]['text'] = '분석협의';
			$temp_arr[$i]['type'] = 'riskanalysis_discussion';
			//$temp_arr[$i]['iconCls'] = 'ico-backlog';
			$temp_arr[$i]['leaf'] = TRUE;
			$i++;

			$temp_arr[$i]['pr_seq'] = $node[1];
			$temp_arr[$i]['pid'] = $data['node'];
			$temp_arr[$i]['id'] = 'riskanalysis_discussion_result_'.$pr_seq;
			$temp_arr[$i]['text'] = '협의결과';
			$temp_arr[$i]['type'] = 'riskanalysis_discussion_result';
			//$temp_arr[$i]['iconCls'] = 'ico-backlog';
			$temp_arr[$i]['leaf'] = TRUE;
			$i++;

			$temp_arr[$i]['pr_seq'] = $node[1];
			$temp_arr[$i]['pid'] = $data['node'];
			$temp_arr[$i]['id'] = 'riskanalysis_strategy_'.$pr_seq;
			$temp_arr[$i]['text'] = '전략수립';
			$temp_arr[$i]['type'] = 'riskanalysis_strategy';
			//$temp_arr[$i]['iconCls'] = 'ico-backlog';
			$temp_arr[$i]['leaf'] = TRUE;
			$i++;

			$temp_arr[$i]['pr_seq'] = $node[1];
			$temp_arr[$i]['pid'] = $data['node'];
			$temp_arr[$i]['id'] = 'riskanalysis_setup_'.$pr_seq;
			$temp_arr[$i]['text'] = '설정';
			$temp_arr[$i]['type'] = 'riskanalysis_setup';
			//$temp_arr[$i]['iconCls'] = 'ico-backlog';
			$temp_arr[$i]['leaf'] = TRUE;
			$i++;
		}
		return $temp_arr;
	}


	/**
	* Function get_member_name
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function get_member_name()
	{
		$member_arr = array();
		$this->db->from('otm_member');
		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$member_arr[$temp_row->mb_email] = $temp_row->mb_name;
		}

		return $member_arr;
	}


	/**
	* Function is_custom_value
	*
	* @param array $data Post Data.
	*
	* @return integer
	*/
	function is_custom_value($ri_seq,$form_seq){
		/*
		$str_sql = "select count(*) as cnt from otm_riskitem_custom_value
					where
						otm_riskitem_ri_seq='$ri_seq' and
						otm_project_customform_pc_seq='$form_seq'
					";
		$query = $this->db->query($str_sql);
		*/

		$this->db->select('count(*) as cnt');
		$this->db->where('otm_riskitem_ri_seq',$ri_seq);
		$this->db->where('otm_project_customform_pc_seq',$form_seq);		
		$query = $this->db->get('otm_riskitem_custom_value');

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
	function create_custom_value($ri_seq,$form_seq,$form_type,$form_value){
		$this->db->set('otm_riskitem_ri_seq',				$ri_seq);
		$this->db->set('otm_project_customform_pc_seq',	$form_seq);
		$this->db->set('rcv_custom_type',				$form_type);
		$this->db->set('rcv_custom_value',				$form_value);

		$this->db->insert('otm_riskitem_custom_value');
		$this->db->insert_id();
	}

	/**
	* Function update_custom_value
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_custom_value($ri_seq,$form_seq,$form_type,$form_value){
		$modify_array = array(
			'rcv_custom_type'		=> $form_type,
			'rcv_custom_value'		=> $form_value
		);
		$where = array(	'otm_riskitem_ri_seq'=>$ri_seq,'otm_project_customform_pc_seq'=>$form_seq);
		$this->db->update('otm_riskitem_custom_value',$modify_array,$where);
	}


	/*
	*******************************
	* Riskanalysis Chart
	*******************************
	*/

	/**
	* Function riskanalysis_riskarea_riskitem_chart
	*
	* @return string
	*/
	function riskanalysis_riskarea_riskitem_chart($data)
	{
		$pr_seq = $data['pr_seq'];

		$riskarea = array();
		$riskarea = $this->code_list(array('pr_seq'=>$pr_seq, 'type'=>'riskarea'));

		$riskpoint = array();
		$riskpoint_arr = $this->code_list(array('pr_seq'=>$pr_seq, 'type'=>'riskpoint'));
		for($i=0; $i<count($riskpoint_arr); $i++){
			$riskpoint[$riskpoint_arr[$i]->pco_seq] = $riskpoint_arr[$i]->pco_default_value;
		}

		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->order_by('rf_type desc, rf_ord asc');
		$query = $this->db->get('otm_riskfactor');
		$riskfactor = $query->result();

		$riskitem_factor_vlaue = array();
		$query = $this->db->get('otm_riskitem_factor_value');
		foreach ($query->result() as $temp_row)
		{
			$riskitem_factor_vlaue[$temp_row->otm_riskitem_ri_seq][$temp_row->otm_riskfactor_rf_seq] = $temp_row;
		}
		
		// riskitem discussion data
		$return_arr = array();
		$this->db->select('ri_seq,ri_subject');
		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		//$this->db->order_by('ri_seq asc');
		$query = $this->db->get('otm_riskitem');
		foreach ($query->result() as $temp_row)
		{		
			$likelihood_point = 0;
			$impat_point = 0;

			$temp_arr = array();

			foreach ($riskfactor as $riskfactor_row)
			{
				if($riskitem_factor_vlaue[$temp_row->ri_seq]){
					if($riskitem_factor_vlaue[$temp_row->ri_seq][$riskfactor_row->rf_seq]){
						//input value
						if($riskitem_factor_vlaue[$temp_row->ri_seq][$riskfactor_row->rf_seq]->rifv_value > 0){

							//point sum
							if($riskfactor_row->rf_type == 'likelihood'){
								$likelihood_point += $riskpoint[$riskitem_factor_vlaue[$temp_row->ri_seq][$riskfactor_row->rf_seq]->rifv_value] * 1;
							}else if($riskfactor_row->rf_type == 'impact'){
								$impat_point += ($riskpoint[$riskitem_factor_vlaue[$temp_row->ri_seq][$riskfactor_row->rf_seq]->rifv_value]) * 1;
							}
						}
					}
				}
			}

			$temp_arr['final_point'] = $likelihood_point * $impat_point;
			$temp_arr['riskarea'] = '';
			foreach ($riskarea as $riskarea_row)
			{
				$riskarea_row->name = $riskarea_row->pco_name;

				if($temp_arr['riskarea'] == ''){
					$check_point = $riskarea_row->pco_default_value * 1;

					if($check_point <= $temp_arr['final_point']){
						$temp_arr['riskarea'] = $riskarea_row->pco_name;
						if(!$riskarea_row->cnt){
							$riskarea_row->cnt = 1;
						}else{
							$riskarea_row->cnt++;
						}
					}				
				}
			}		
			//$return_arr[] = $temp_arr;
		}

		return $riskarea;
		//return $return_arr;
	}


	/**
	* Function riskanalysis_riskitem_requirement_chart
	*
	* @return string
	*/
	function riskanalysis_riskitem_requirement_chart($data)
	{
		$pr_seq = $data['pr_seq'];

		$req_list = array();
		$this->db->select('otm_riskitem_ri_seq as ri_seq, count(*) as cnt');
		$this->db->join('otm_requirement as req','rrm.otm_requirement_req_seq=req.req_seq','left');
		$this->db->where('req.req_seq !=','');
		$this->db->group_by('otm_riskitem_ri_seq');
		$query = $this->db->get('otm_risk_req_mapping as rrm');
		foreach ($query->result() as $temp_row)
		{
			$req_list[$temp_row->ri_seq] = $temp_row->cnt;
		}

		$temp_arr = array();
		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->order_by('ri_seq asc');
		$query = $this->db->get('otm_riskitem');
		foreach ($query->result() as $temp_row)
		{
			$temp_row->name = $temp_row->ri_subject;
			$temp_row->cnt = $req_list[$temp_row->ri_seq];
			$temp_arr[] = $temp_row;
		}
		return $temp_arr;
	}
	/*
	*******************************
	* END : Riskanalysis Chart
	*******************************
	*/


	/**
	*******************************
	*	Riskitem 
	*******************************
	*/


	/**
	* Function riskitem_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function riskitem_list($data)
	{
		$req_list = array();
		$this->db->select('otm_riskitem_ri_seq as ri_seq, count(*) as cnt');
		$this->db->join('otm_requirement as req','rrm.otm_requirement_req_seq=req.req_seq','left');
		$this->db->where('req.req_seq !=','');
		$this->db->group_by('otm_riskitem_ri_seq');
		$query = $this->db->get('otm_risk_req_mapping as rrm');
		foreach ($query->result() as $temp_row)
		{
			$req_list[$temp_row->ri_seq] = $temp_row->cnt;
		}

		$tc_list = array();
		$this->db->select('otm_riskitem_ri_seq as ri_seq, count(*) as cnt');
		$this->db->join('otm_testcase as tc','rtm.otm_testcase_tc_seq=tc.tc_seq','left');
		$this->db->where('tc.tc_seq !=','');
		$this->db->group_by('otm_riskitem_ri_seq');
		$query = $this->db->get('otm_risk_tc_mapping as rtm');
		foreach ($query->result() as $temp_row)
		{
			$tc_list[$temp_row->ri_seq] = $temp_row->cnt;
		}


		$temp_arr = array();
		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->order_by('ri_seq asc');
		$query = $this->db->get('otm_riskitem');
		foreach ($query->result() as $temp_row)
		{
			$temp_row->link_req_cnt = $req_list[$temp_row->ri_seq];
			$temp_row->link_tc_cnt = $tc_list[$temp_row->ri_seq];
			$temp_arr[] = $temp_row;
		}
		return $temp_arr;
	}


	/**
	* Function view_riskitem
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function view_riskitem($data)
	{
		$pr_seq	= $data['pr_seq'];
		$ri_seq	= $data['ri_seq'];

		$member_name = $this->get_member_name();

		//$date=date('Y-m-d H:i:s');
		//$writer = $this->session->userdata('mb_email');

		//return "{success:true}";

		$return_array = "";
		$this->db->where('ri_seq',$ri_seq);
		$query = $this->db->get('otm_riskitem');
		foreach ($query->result() as $row)
		{
			$row->writer = $member_name[$row->writer];
			$return_array = $row;
		}

		/*
		$tmp_arr = "";
		$str_sql = "select
						otm_project_customform_pc_seq as seq,
						rcv_custom_type as formtype,
						rcv_custom_value as value
					from
					otm_riskitem_custom_value
					where otm_riskitem_ri_seq='$ri_seq'
		";		
		$query = $this->db->query($str_sql);
		
		$this->db->select('otm_project_customform_pc_seq as seq,
						rcv_custom_type as formtype,
						rcv_custom_value as value');
		$this->db->where('otm_riskitem_ri_seq',$ri_seq);
		$query = $this->db->get('otm_riskitem_custom_value');
		foreach ($query->result() as $row)
		{
			$tmp_arr[] = $row;
		}

		$arr->df_customform = json_encode($tmp_arr);
		*/
		$this->db->select('otm_project_customform_pc_seq as seq,
						rcv_custom_type as formtype,
						rcv_custom_value as value');
		$this->db->where('otm_riskitem_ri_seq',$ri_seq);
		$query = $this->db->get('otm_riskitem_custom_value');
		$return_array->df_customform = json_encode($query->result_array());


		/* attached file */
		$file_arr = "";
		//$str_sql = "select * from otm_file where otm_project_pr_seq='$pr_seq' and otm_category='ID_RISK' and target_seq='$ri_seq' order by of_no asc";
		//$query = $this->db->query($str_sql);

		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('otm_category','ID_RISK');
		$this->db->where('target_seq',$ri_seq);
		$this->db->order_by('of_no asc');
		$query = $this->db->get('otm_file');
		foreach ($query->result() as $row)
		{
			$file_arr[] = $row;
		}
		$return_array->fileform = json_encode($file_arr);

		return $return_array;
	}


	/**
	* Function create_riskitem
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_riskitem($data)
	{
		$pr_seq = $data['pr_seq'];
		$ri_subject = $data['ri_subject'];
		$ri_description = $data['ri_description'];
		$custom_form = $data['custom_form'];

		$date=date("Y-m-d H:i:s");
		$writer = $this->session->userdata('mb_email');

		$this->db->set('otm_project_pr_seq',	$pr_seq);
		$this->db->set('ri_subject',			$ri_subject);
		$this->db->set('ri_description',		$ri_description);

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

		$this->db->insert('otm_riskitem');
		$ri_seq = $this->db->insert_id();

		$custom_arr = json_decode($custom_form);

		if(sizeof($custom_arr) >= 1){
			for($i=0;$i<sizeof($custom_arr);$i++){
				$form_seq = $custom_arr[$i]->seq;
				$form_type = $custom_arr[$i]->type;
				$form_value = $custom_arr[$i]->value;

				$this->create_custom_value($ri_seq,$form_seq,$form_type,$form_value);
			}
		}

		if(isset($data['return_key'])){
			return $ri_seq;
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

					$file_data['category'] = 'ID_RISK';
					$file_data['pr_seq'] = $pr_seq;
					$file_data['target_seq'] = $ri_seq;
					$file_data['of_no'] = $i;


					$this->File_Form->file_upload($file_data);
				}
			}
		}
		$return_data['ri_seq'] = $ri_seq;
        return $return_data;
	}


	/**
	* Function update_riskitem
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_riskitem($data)
	{
		$pr_seq = $data['pr_seq'];
		$ri_seq = $data['ri_seq'];
		$ri_subject = $data['ri_subject'];
		$ri_description = $data['ri_description'];
		$custom_form = $data['custom_form'];

		$date=date("Y-m-d H:i:s");
		$writer = $this->session->userdata('mb_email');

		$modify_array = array(
			'ri_subject'		=> $ri_subject,
			'ri_description'	=> $ri_description,
			'last_writer'		=> $writer,
			'last_update'		=> $date
		);
		$where = array('otm_project_pr_seq'=>$pr_seq,'ri_seq'=>$ri_seq);
		$this->db->update('otm_riskitem',$modify_array,$where);

		$custom_arr = json_decode($custom_form);
		if(sizeof($custom_arr) >= 1){
			for($i=0;$i<sizeof($custom_arr);$i++){
				$form_name = $custom_arr[$i]->name;
				$form_seq = $custom_arr[$i]->seq;
				$form_type = $custom_arr[$i]->type;
				$form_value = $custom_arr[$i]->value;

				$is_item = $this->is_custom_value($ri_seq,$form_seq);
				if($is_item >= 1){
					$this->db->from('otm_riskitem_custom_value');
					$this->db->where('otm_riskitem_ri_seq',$ri_seq);
					$this->db->where('otm_project_customform_pc_seq',$form_seq);
					$query = $this->db->get();
					foreach ($query->result() as $row)
					{
						$tmp_fomarr = $row;
					}

					$this->update_custom_value($ri_seq,$form_seq,$form_type,$form_value);
				}else{
					$this->create_custom_value($ri_seq,$form_seq,$form_type,$form_value);
				}
			}
		}

		if(isset($data['return_key'])){
			return $ri_seq;
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

					$file_data['category'] = 'ID_RISK';
					$file_data['pr_seq'] = $pr_seq;
					$file_data['target_seq'] = $ri_seq;
					$file_data['of_no'] = 2;

					$this->File_Form->file_upload($file_data);
				}
			}
		}

		$return_data['ri_seq'] = $ri_seq;
        return $return_data;
	}


	/**
	* Function delete_riskitem
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_riskitem($data)
	{
		$pr_seq	= $data['pr_seq'];

		for($i=0; $i<count($data['ri_list']); $i++)
		{
			$ri_seq = $data['ri_list'][$i];

			$delete_array = array(
				'otm_project_pr_seq' => $pr_seq,
				'ri_seq'=>$ri_seq
			);

			$result = $this->db->delete('otm_riskitem',$delete_array);

			$delete_array = array('otm_riskitem_ri_seq' => $ri_seq);
			$this->db->delete('otm_riskitem_custom_value',$delete_array);

			//mapping data delete
			$this->db->delete('otm_risk_req_mapping',$delete_array);
			$this->db->delete('otm_risk_tc_mapping',$delete_array);
		}

		return $result;
	}


	/**
	*******************************
	*	Riskitem Discussion
	*******************************
	*/


	/**
	* Function riskitem_discussion
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function riskitem_discussion($data)
	{
		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->order_by('rf_type desc, rf_ord asc');
		$query = $this->db->get('otm_riskfactor');
		$riskfactor = $query->result();
		//rf_seq, otm_project_pr_seq, rf_subject, rf_description, rf_type, rf_ord
	
		$riskitem_factor_vlaue = array();
		$query = $this->db->get('otm_riskitem_factor_value');
		//rifv_seq, otm_riskitem_ri_seq, otm_riskfactor_rf_seq, rifv_value
		foreach ($query->result() as $temp_row)
		{
			$riskitem_factor_vlaue[$temp_row->otm_riskitem_ri_seq][$temp_row->otm_riskfactor_rf_seq] = $temp_row;
		}
		

		// riskitem discussion data
		$return_arr = array();
		$this->db->select('ri_seq,ri_subject');
		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->order_by('ri_seq asc');
		$query = $this->db->get('otm_riskitem');
		foreach ($query->result() as $temp_row)
		{		
			$temp_arr = array();
			$temp_arr['ri_seq'] = $temp_row->ri_seq;
			$temp_arr['ri_subject'] = $temp_row->ri_subject;

			foreach ($riskfactor as $riskfactor_row)
			{
				$temp_arr[$riskfactor_row->rf_type.'_'.$riskfactor_row->rf_seq] = '';

				if($riskitem_factor_vlaue[$temp_row->ri_seq]){
					if($riskitem_factor_vlaue[$temp_row->ri_seq][$riskfactor_row->rf_seq]){
						//input value
						if($riskitem_factor_vlaue[$temp_row->ri_seq][$riskfactor_row->rf_seq]->rifv_value > 0){
							$temp_arr[$riskfactor_row->rf_type.'_'.$riskfactor_row->rf_seq] = $riskitem_factor_vlaue[$temp_row->ri_seq][$riskfactor_row->rf_seq]->rifv_value; 
						}
					}
				}
			}
			
			$return_arr[] = $temp_arr;
		}
		return $return_arr;
	}


	/**
	* Function riskitem_discussion_save
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function riskitem_discussion_save($data)
	{
		$pr_seq = $data['pr_seq'];
		$save_list = $data['save_list'];
		for($i=0; $i<count($save_list); $i++)
		{

			$temp_factor_col = array();
			$temp_factor_update_list = array();

			foreach ($save_list[$i] as $k=>$v)
			{	
				$temp_key = preg_split('/_/',$k);
				if($temp_key && ($temp_key[0] == 'likelihood' || $temp_key[0] == 'impact') && $v != ''){
					$temp_factor_col[$temp_key[1]] = $v;		
				}
			}

			$this->db->where('otm_riskitem_ri_seq', $save_list[$i]->ri_seq);
			$query = $this->db->get('otm_riskitem_factor_value');
			foreach ($query->result() as $temp_row)
			{
				if($temp_factor_col[$temp_row->otm_riskfactor_rf_seq]){
					$temp_factor_update_list[$temp_row->otm_riskfactor_rf_seq] = $temp_factor_col[$temp_row->otm_riskfactor_rf_seq];	
				}
			}

			foreach ($temp_factor_col as $k=>$v)
			{
				if($temp_factor_update_list[$k]){
					//update
					$modify_array = array('rifv_value' => $temp_factor_col[$k]);
					$where = array('otm_riskitem_ri_seq' => $save_list[$i]->ri_seq, 'otm_riskfactor_rf_seq' => $k);
					$this->db->update('otm_riskitem_factor_value',$modify_array,$where);
				}else{
					//insert
					$this->db->set('otm_riskitem_ri_seq',	$save_list[$i]->ri_seq);
					$this->db->set('otm_riskfactor_rf_seq',	$k);
					$this->db->set('rifv_value',			$temp_factor_col[$k]);
					$this->db->insert('otm_riskitem_factor_value');
				}
			}			
		}

		return json_encode($temp_factor_col);
		return json_encode($save_list);
		return 'ok';

	}


	/**
	*******************************
	*	Riskitem Discussion Result
	*******************************
	*/


	/**
	* Function riskitem_discussion_result
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function riskitem_discussion_result($data)
	{
		$pr_seq = $data['pr_seq'];

		$req_list = array();
		$this->db->select('otm_riskitem_ri_seq as ri_seq, count(*) as cnt');
		$this->db->join('otm_requirement as req','rrm.otm_requirement_req_seq=req.req_seq','left');
		$this->db->where('req.req_seq !=','');
		$this->db->group_by('otm_riskitem_ri_seq');
		$query = $this->db->get('otm_risk_req_mapping as rrm');
		foreach ($query->result() as $temp_row)
		{
			$req_list[$temp_row->ri_seq] = $temp_row->cnt;
		}

		$tc_list = array();
		$this->db->select('otm_riskitem_ri_seq as ri_seq, count(*) as cnt');
		$this->db->join('otm_testcase as tc','rtm.otm_testcase_tc_seq=tc.tc_seq','left');
		$this->db->where('tc.tc_seq !=','');
		$this->db->group_by('otm_riskitem_ri_seq');
		$query = $this->db->get('otm_risk_tc_mapping as rtm');
		foreach ($query->result() as $temp_row)
		{
			$tc_list[$temp_row->ri_seq] = $temp_row->cnt;
		}



		//$temp_arr = array();
		//return $temp_arr;
		
		//$data['type'] = 'riskpoint';
		//$riskpoint_arr = $this->code_list($data);

		$riskarea = array();
		$riskarea = $this->code_list(array('pr_seq'=>$pr_seq, 'type'=>'riskarea'));

		$riskpoint = array();
		$riskpoint_arr = $this->code_list(array('pr_seq'=>$pr_seq, 'type'=>'riskpoint'));
		for($i=0; $i<count($riskpoint_arr); $i++){
			$riskpoint[$riskpoint_arr[$i]->pco_seq] = $riskpoint_arr[$i]->pco_default_value;
		}


		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->order_by('rf_type desc, rf_ord asc');
		$query = $this->db->get('otm_riskfactor');
		$riskfactor = $query->result();
		//rf_seq, otm_project_pr_seq, rf_subject, rf_description, rf_type, rf_ord
	
		$riskitem_factor_vlaue = array();
		$query = $this->db->get('otm_riskitem_factor_value');
		//rifv_seq, otm_riskitem_ri_seq, otm_riskfactor_rf_seq, rifv_value
		foreach ($query->result() as $temp_row)
		{
			$riskitem_factor_vlaue[$temp_row->otm_riskitem_ri_seq][$temp_row->otm_riskfactor_rf_seq] = $temp_row;
		}
		
		// riskitem discussion data
		$return_arr = array();
		$this->db->select('ri_seq,ri_subject');
		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->order_by('ri_seq asc');
		$query = $this->db->get('otm_riskitem');
		foreach ($query->result() as $temp_row)
		{		

			//riskitem analysis final point
			// final point = (likelihood point sum) * (impact point sum)
			$likelihood_point = 0;
			$impat_point = 0;

			$temp_arr = array();
			$temp_arr['ri_seq'] = $temp_row->ri_seq;
			$temp_arr['ri_subject'] = $temp_row->ri_subject;

			foreach ($riskfactor as $riskfactor_row)
			{
				if($riskitem_factor_vlaue[$temp_row->ri_seq]){
					if($riskitem_factor_vlaue[$temp_row->ri_seq][$riskfactor_row->rf_seq]){
						//input value
						if($riskitem_factor_vlaue[$temp_row->ri_seq][$riskfactor_row->rf_seq]->rifv_value > 0){

							//point sum
							if($riskfactor_row->rf_type == 'likelihood'){
								$likelihood_point += $riskpoint[$riskitem_factor_vlaue[$temp_row->ri_seq][$riskfactor_row->rf_seq]->rifv_value] * 1;
							}else if($riskfactor_row->rf_type == 'impact'){
								$impat_point += ($riskpoint[$riskitem_factor_vlaue[$temp_row->ri_seq][$riskfactor_row->rf_seq]->rifv_value]) * 1;
							}
						}
					}
				}
			}

			$temp_arr['check'] = count($riskfactor).' : '.$likelihood_point .' : '. $impat_point;

			$temp_arr['final_point'] = $likelihood_point * $impat_point;

			$temp_arr['riskarea'] = '';
			foreach ($riskarea as $riskarea_row)
			{
				if($temp_arr['riskarea'] == ''){
					$check_point = $riskarea_row->pco_default_value * 1;

					if($check_point <= $temp_arr['final_point']){
						$temp_arr['riskarea'] = $riskarea_row->pco_name;
					}				
				}
			}

			$temp_arr['risk_req_cnt'] = $req_list[$temp_row->ri_seq];
			$temp_arr['risk_tc_cnt'] = $tc_list[$temp_row->ri_seq];
			
			$return_arr[] = $temp_arr;
		}
		return $return_arr;
	}


	/**
	*******************************
	*	riskanalysis_requirement
	*******************************
	*/

	/**
	* Function riskanalysis_requirement
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function riskanalysis_requirement($data)
	{
		//otm_risk_req_mapping

		$pr_seq = $data['pr_seq'];
		$ri_seq = $data['ri_seq'];
		$type = $data['type'];
		$return_array = array();

		$this->db->select('rrm.*, req.req_subject');
		$this->db->from('otm_risk_req_mapping as rrm');
		$this->db->join('otm_requirement as req','req.req_seq = rrm.otm_requirement_req_seq','left');
		$this->db->where('req.otm_project_pr_seq', $pr_seq);
		$this->db->where('rrm.otm_riskitem_ri_seq', $ri_seq);
		$this->db->order_by('rrl_seq asc');
		$query = $this->db->get();		
		$link_array = $query->result_array();
		//return $this->db->last_query();
		switch($type)
		{
			case "link":
				//연결된 요구사항
				return $link_array;
				break;
			case "unlink":
				//연결되지 않은 요구사항
				//중복 연결 허용? 여부 확인

				$this->db->select('req_seq, req_subject');
				$this->db->where('otm_project_pr_seq', $pr_seq);
				$this->db->order_by('req_seq asc');
				$query = $this->db->get('otm_requirement');
				
				$requirement_array = $query->result_array();

				for($i=0; $i<count($requirement_array); $i++)
				{
					$check_data = true;
					for($j=0; $j<count($link_array); $j++)
					{
						if($requirement_array[$i]['req_seq'] == $link_array[$j]['otm_requirement_req_seq']){
							$check_data = false;
						}
					}		
					
					if($check_data){
						array_push($return_array, $requirement_array[$i]);
					}
				}

				
				return $return_array;
				break;
			default:
				return array('msg'=>'type error');
				break;
		}
	}


	/**
	* Function riskitem_requirement_link
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function riskitem_requirement_link($data)
	{
		switch($data['type'])
		{
			case "link":
				for($i=0; $i<count($data['req_list']); $i++){
					$this->db->set('otm_riskitem_ri_seq',	$data['ri_seq']);
					$this->db->set('otm_requirement_req_seq', $data['req_list'][$i]);
					$this->db->insert('otm_risk_req_mapping');
				}
				break;
			case "unlink":
				for($i=0; $i<count($data['req_list']); $i++){
					$this->db->where('otm_riskitem_ri_seq',	$data['ri_seq']);
					$this->db->where('otm_requirement_req_seq', $data['req_list'][$i]);
					$this->db->delete('otm_risk_req_mapping');
				}
				break;
			default:
				return false;
				break;
		}

		return true;
	}	


	/**
	*******************************
	*	riskanalysis_testcase
	*******************************
	*/

	/**
	* Function riskanalysis_testcase_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function riskanalysis_testcase_list($data)
	{
		/*
		$this->load->model('testcase/testcase_m');
		$data['tcplan'] = 'backlog_'.$pr_seq;
		$data['project_seq'] = $pr_seq;
		$data['role'] = 'all';
		return $this->testcase_m->testcase_tree_list($data);
		*/

		//$mb_email = $this->session->userdata('mb_email');

		$temp_arr = array();
		$node = $data['node'];
		$node = explode('_', $node);
		$pr_seq = $data['pr_seq'];


		//Member Info
		$member_name = $this->get_member_name();

		$this->db->join('otm_risk_tc_mapping as rtm','rtm.otm_testcase_tc_seq = tc.tc_seq', 'left');

		if($node[0] !== 'root'){
			//$this->db->select('tc.*,tc.writer as mb_name');
			$this->db->from('otm_testcase as tc');
			$this->db->where('tc.otm_project_pr_seq',$pr_seq);
			$this->db->where('tc.tc_inp_pid',$data['node']);
			$this->db->where('tc.tc_is_task','folder');
			$this->db->order_by('tc_ord asc');


			$query = $this->db->get();
			$i=0;
			foreach ($query->result() as $temp_row)
			{
				$temp_arr[$i]['pr_seq'] = $temp_row->otm_project_pr_seq;
				$temp_arr[$i]['tc_seq'] = $temp_row->tc_seq;
				$temp_arr[$i]['pid'] = $temp_row->tc_inp_pid;
				$temp_arr[$i]['id'] = $temp_row->tc_inp_id;
				$temp_arr[$i]['out_id'] = $temp_row->tc_out_id;
				$temp_arr[$i]['text'] = $temp_row->tc_subject;
				$temp_arr[$i]['type'] = $temp_row->tc_is_task;
				
				$temp_link = $temp_row->otm_riskitem_ri_seq;
				if($temp_link){
					if($temp_link == $data['ri_seq'])
					{
						//$temp_link = true;
						$temp_arr[$i]['disabled'] = true;
					}else{
						//$temp_link = 
						$temp_arr[$i]['disabled'] = false;
					}
				}
				$temp_arr[$i]['link_seq'] = $temp_link;


				//$temp_arr[$i]['writer_name'] = $member_name[$temp_row->mb_name];
				//$temp_arr[$i]['writer'] = $temp_row->writer;
				//$temp_arr[$i]['regdate'] = $temp_row->regdate;
				//$temp_arr[$i]['last_writer'] = $temp_row->last_writer;
				//$temp_arr[$i]['last_update'] = $temp_row->last_update;
				//$temp_arr[$i]['leaf'] = ($temp_row->tc_is_task === 'folder')?FALSE:TRUE;
				$temp_arr[$i]['leaf'] = FALSE;
				$i++;
			}
		}else{
			

			

			//$this->db->select("tc.*,tc.writer as mb_name");
			$this->db->from('otm_testcase tc');
			$this->db->where('tc.otm_project_pr_seq',$pr_seq);
			$this->db->where('tc.tc_inp_pid','tc_0');
			$this->db->where('tc.tc_is_task','folder');				
			$this->db->order_by('tc.tc_ord asc');
			$query = $this->db->get();

			$i=0;
			foreach ($query->result() as $temp_row)
			{
				$temp_arr[$i]['pr_seq'] = $temp_row->otm_project_pr_seq;
				$temp_arr[$i]['tc_seq'] = $temp_row->tc_seq;
				$temp_arr[$i]['pid'] = $temp_row->tc_inp_pid;
				$temp_arr[$i]['id'] = $temp_row->tc_inp_id;
				$temp_arr[$i]['out_id'] = $temp_row->tc_out_id;
				$temp_arr[$i]['text'] = $temp_row->tc_subject;
				$temp_arr[$i]['type'] = $temp_row->tc_is_task;

				$temp_link = $temp_row->otm_riskitem_ri_seq;
				if($temp_link){
					if($temp_link == $data['ri_seq'])
					{
						//$temp_link = true;
						$temp_arr[$i]['disabled'] = true;
					}else{
						//$temp_link = 
						$temp_arr[$i]['disabled'] = false;
					}
				}else{
					$temp_link = '';
				}
				$temp_arr[$i]['link_seq'] = $temp_link;

				//$temp_arr[$i]['writer_name'] = $member_name[$temp_row->mb_name];
				//$temp_arr[$i]['writer'] = $temp_row->writer;
				//$temp_arr[$i]['regdate'] = $temp_row->regdate;
				//$temp_arr[$i]['last_writer'] = $temp_row->last_writer;
				//$temp_arr[$i]['last_update'] = $temp_row->last_update;
				$temp_arr[$i]['leaf'] = FALSE;

				$i++;
			}

			/*
			$return_array[0]['pr_seq'] = $data['pr_seq'];
			//$return_array[0]['tc_seq'] = '';
			//$return_array[0]['pid'] = 0;
			$return_array[0]['id'] = 'tc_0';
			//$return_array[0]['out_id'] = '';
			$return_array[0]['text'] = 'Root';
			$return_array[0]['type'] = 'folder';
			$return_array[0]['leaf'] = FALSE;
			$return_array[0]['children'] = $temp_arr;

			return $return_array;
			*/
		}

		return $temp_arr;
	}


	/**
	* Function riskitem_testcase_link
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function riskitem_testcase_link($data)
	{
		/*
		$data = array(
			'type' => $this->input->post_get('type', TRUE),
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'ri_seq' => $this->input->post_get('ri_seq', TRUE),
			'tc_seq' => $this->input->post_get('tc_seq', TRUE),
			'pid' => $this->input->post_get('pid', TRUE)
		);
		*/

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');
		$writer_name = $this->session->userdata('mb_name');

		switch($data['type'])
		{
			case "add_link":
				$this->db->where('ri_seq',$data['ri_seq']);
				$query = $this->db->get('otm_riskitem');
				$riskitem_info = $query->result();
				//return $riskitem_info->ri_subject;
				


				$insert_array['otm_project_pr_seq'] = $data['pr_seq'];
				$insert_array['tc_subject'] = $riskitem_info[0]->ri_subject;//'link test';//$data['tc_subject'];
				$insert_array['tc_description'] = $riskitem_info[0]->ri_description;//'';//$data['tc_description'];
				if(isset($data['pid']) && $data['pid'] === 'root') $data['pid'] = 'tc_0';
				$insert_array['tc_inp_pid'] = ($data['pid'])?$data['pid']:'tc_0';

				$insert_array['writer'] = $writer;
				$insert_array['regdate'] = $date;
				$insert_array['last_writer'] = '';
				$insert_array['last_update'] = '';

				$insert_array['tc_is_task'] = 'folder';

				$this->db->insert('otm_testcase', $insert_array);
				$tc_seq = $this->db->insert_id();

				$tc_out_id = 'ts_'.$tc_seq;
				$modify_array['tc_out_id'] = 'ts_'.$tc_seq;
				$modify_array['tc_inp_id'] = 'ts_'.$tc_seq;
				$modify_array['tc_ord'] = $tc_seq;
				$where = array('tc_seq'=>$tc_seq);
				$this->db->update('otm_testcase', $modify_array, $where);

				$return_data = array();
				$return_data['pr_seq']		= $data['pr_seq'];
				$return_data['tc_seq']		= $tc_seq;
				$return_data['pid']			= $insert_array['tc_inp_pid'];
				$return_data['id']			= $modify_array['tc_inp_id'];
				$return_data['out_id']		= $tc_out_id;
				$return_data['text']		= $insert_array['tc_subject'];
				$return_data['type']		= 'folder';

				$return_data['link_seq']		= $data['ri_seq'];

				$return_data['leaf']		= false;

				$this->db->set('otm_riskitem_ri_seq',	$data['ri_seq']);
				$this->db->set('otm_testcase_tc_seq', $tc_seq);
				$this->db->insert('otm_risk_tc_mapping');

				return "{success:true, data:'".json_encode($return_data)."'}";

				break;
			case "link":

				$this->db->where('otm_testcase_tc_seq',$data['tc_seq']);
				$query = $this->db->get('otm_risk_tc_mapping');
				$check = $query->result_array();
				
				if($check){
					return "{success:true, data:{msg:'연결되어있는 테스트 케이스 입니다.'}}";
				}

				$this->db->where('tc_seq',$data['tc_seq']);
				$query = $this->db->get('otm_testcase');
				$testcase_info = $query->result();

				$return_data = array();
				$return_data['pr_seq']		= $data['pr_seq'];
				$return_data['tc_seq']		= $data['tc_seq'];
				$return_data['pid']			= $testcase_info[0]->tc_inp_pid;
				$return_data['id']			= $testcase_info[0]->tc_inp_id;
				$return_data['out_id']		= $testcase_info[0]->tc_out_id;
				$return_data['text']		= $testcase_info[0]->tc_subject;
				$return_data['type']		= 'folder';

				$return_data['link_seq']		= $data['ri_seq'];

				$return_data['leaf']		= false;

				$this->db->set('otm_riskitem_ri_seq',	$data['ri_seq']);
				$this->db->set('otm_testcase_tc_seq', $data['tc_seq']);
				$this->db->insert('otm_risk_tc_mapping');

				return "{success:true, data:'".json_encode($return_data)."'}";
				break;
			case "unlink":

				$this->db->where('otm_testcase_tc_seq',$data['tc_seq']);
				$query = $this->db->get('otm_risk_tc_mapping');
				$check = $query->result_array();
				
				if($check){
					$this->db->where('tc_seq',$data['tc_seq']);
					$query = $this->db->get('otm_testcase');
					$testcase_info = $query->result();

					$return_data = array();
					$return_data['pr_seq']		= $data['pr_seq'];
					$return_data['tc_seq']		= $data['tc_seq'];
					$return_data['pid']			= $testcase_info[0]->tc_inp_pid;
					$return_data['id']			= $testcase_info[0]->tc_inp_id;
					$return_data['out_id']		= $testcase_info[0]->tc_out_id;
					$return_data['text']		= $testcase_info[0]->tc_subject;
					$return_data['type']		= 'folder';

					$return_data['link_seq']		= '';

					$return_data['leaf']		= false;

					$delete_array = array(
						'otm_riskitem_ri_seq' => $data['ri_seq'],
						'otm_testcase_tc_seq' => $data['tc_seq']
					);
					$this->db->delete('otm_risk_tc_mapping',$delete_array);

					return "{success:true, data:'".json_encode($return_data)."'}";
					
				}else{
					return "{success:true, data:{msg:'연결해제 할 수 없습니다.'}}";
				}				
				break;
			default:
				return false;
				break;
		}

	}


	/**
	*******************************
	*	Strategy
	*******************************
	*/


	/**
	* Function strategy
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function strategy($data)
	{
		$pr_seq = $data['pr_seq'];
		$cnt_result = 4;
		$temp_array = array();

		$data['type'] = 'riskarea';
		$riskarea = $this->code_list($data);
		$temp_array['riskarea'] = json_encode($riskarea);

		$data['type'] = 'testlevel';
		$testlevel = $this->code_list($data);
		$temp_array['testlevel'] = json_encode($testlevel);

		$temp_strategy = array();
		$this->db->where('otm_project_pr_seq', $data['pr_seq']);		
		$query = $this->db->get('otm_strategy');
		foreach ($query->result() as $temp_row)
		{
			$temp_strategy[$temp_row->riskarea_pco_seq][$temp_row->testlevel_pco_seq] = $temp_row->str_description;
		}
		
		$temp_array['strategy_data'] = '';
		if(count($temp_strategy) > 0){
			$temp_array['strategy_data'] = json_encode($temp_strategy);
		}

		return "{success:true,totalCount: ".$cnt_result.", data:".json_encode($temp_array)."}";
	}


	/**
	* Function strategy_save
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function strategy_save($data)
	{		
		$this->db->where('otm_project_pr_seq',$data['pr_seq']);
		$this->db->where('riskarea_pco_seq',$data['riskarea_seq']);
		$this->db->where('testlevel_pco_seq',$data['testlevel_seq']);
		$this->db->from('otm_strategy');

		if($this->db->count_all_results() > 0){
			//update
			$update_data = array();
			$update_data['str_description'] = $data['value'];

			$this->db->where('otm_project_pr_seq',$data['pr_seq']);
			$this->db->where('riskarea_pco_seq',$data['riskarea_seq']);
			$this->db->where('testlevel_pco_seq',$data['testlevel_seq']);
			$this->db->update('otm_strategy', $update_data);
			return 'update';
		}else{
			//insert
			$insert_data = array();
			$insert_data['otm_project_pr_seq'] = $data['pr_seq'];
			$insert_data['riskarea_pco_seq'] = $data['riskarea_seq'];
			$insert_data['testlevel_pco_seq'] = $data['testlevel_seq'];
			$insert_data['str_description'] = $data['value'];
			$this->db->insert('otm_strategy', $insert_data);
			return 'insert';
		}

		return 'ok';
	}


	/**
	*******************************
	*	Setup : Code
	*******************************
	*/


	/**
	* Function is_duplicate
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function is_code_duplicate($pr_seq,$type,$name,$seq="")
	{
		/*
		$seq_try_quy="";
		if($seq){
			$seq_try_quy = " and pco_seq!='$seq'";
		}

		$str_sql = "select count(*) as cnt from otm_project_code
					where
						otm_project_pr_seq='$pr_seq' and
						pco_type='$type' and
						pco_name='$name' $seq_try_quy
					";
		$query = $this->db->query($str_sql);
		*/

		$this->db->select('count(*) as cnt');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('pco_type',$type);
		$this->db->where('pco_name',$name);		

		if($seq){
			$this->db->where('pco_seq!=',$seq);		
		}

		$query = $this->db->get('otm_project_code');

		$tmp_arr="";
		foreach ($query->result() as $row)
		{
			$tmp_arr = $row;
		}
		return $tmp_arr->cnt;
	}


	/**
	* Function code_list
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function code_list($data)
	{
		$temp_arr = array();

		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->where('pco_type', $data['type']);
		$this->db->order_by('ABS(pco_position), pco_seq asc');
		$query = $this->db->get('otm_project_code');
		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}
		return $temp_arr;
	}


	/**
	* Function create_code
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function create_code($data)
	{
		$pr_seq = $data['pr_seq'];
		$pco_type = $data['pco_type'];
		$pco_name = $data['pco_name'];

		$duplicate = $this->is_code_duplicate($pr_seq,$pco_type,$pco_name);

		if($duplicate){
			return 'Duplication Code';
		}else{
			if($data['pco_is_required'] === 'Y'){
				//$str_sql = "update otm_project_code set pco_is_required='N' where otm_project_pr_seq='".$data['pr_seq']."' and pco_type='".$data['pco_type']."'";
				//$query = $this->db->query($str_sql);

				$update_data = array('pco_is_required' => 'N');
				$this->db->where('otm_project_pr_seq', $data['pr_seq']);
				$this->db->where('pco_type', $data['pco_type']);
				$this->db->update('otm_project_code', $update_data);
			}
			if($data['pco_is_default'] === 'Y'){
				//$str_sql = "update otm_project_code set pco_is_default='N' where otm_project_pr_seq='".$data['pr_seq']."' and pco_type='".$data['pco_type']."'";
				//$query = $this->db->query($str_sql);

				$update_data = array('pco_is_default' => 'N');
				$this->db->where('otm_project_pr_seq', $data['pr_seq']);
				$this->db->where('pco_type', $data['pco_type']);
				$this->db->update('otm_project_code', $update_data);
			}

			$insert_data = array();
			$insert_data['otm_project_pr_seq'] = $data['pr_seq'];
			$insert_data['pco_type'] = $data['pco_type'];
			$insert_data['pco_name'] = $data['pco_name'];
			$insert_data['pco_is_required'] = $data['pco_is_required'];
			$insert_data['pco_is_default'] = $data['pco_is_default'];
			$insert_data['pco_default_value'] = $data['pco_default_value'];
			$insert_data['pco_1'] = $data['pco_1'];
			$insert_data['pco_2'] = $data['pco_2'];
			

			$this->db->insert('otm_project_code', $insert_data);
			$result = $this->db->insert_id();
			return 'ok';
		}
	}


	/**
	* Function update_code
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function update_code($data)
	{
		$pr_seq = $data['pr_seq'];
		$pco_type = $data['pco_type'];
		$pco_name = $data['pco_name'];
		$pco_seq = $data['pco_seq'];

		$duplicate = $this->is_code_duplicate($pr_seq,$pco_type,$pco_name,$pco_seq);

		if($duplicate){
			return 'Duplication Code';
		}else{
			if($data['pco_is_required'] === 'Y'){			
				$update_data = array('pco_is_required' => 'N');
				$this->db->where('otm_project_pr_seq', $data['pr_seq']);
				$this->db->where('pco_type', $data['pco_type']);
				$this->db->update('otm_project_code', $update_data);
			}
			if($data['pco_is_default'] === 'Y'){
				$update_data = array('pco_is_default' => 'N');
				$this->db->where('otm_project_pr_seq', $data['pr_seq']);
				$this->db->where('pco_type', $data['pco_type']);
				$this->db->update('otm_project_code', $update_data);
			}

			$data2 = array(
				'pco_name' => $data['pco_name'],
				'pco_is_required' => $data['pco_is_required'],
				'pco_is_default' => $data['pco_is_default'],
				'pco_default_value' => $data['pco_default_value'],
				'pco_1' => $data['pco_1'],
				'pco_2' => $data['pco_2']
			);

			$this->db->where('pco_seq', $data['pco_seq']);
			$this->db->update('otm_project_code', $data2);
			return 'ok';
		}
	}


	/**
	* Function delete_code
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function delete_code($data)
	{
		for($i=0; $i<count($data['pco_list']); $i++){
			$this->db->where('pco_seq', $data['pco_list'][$i]);
			$this->db->delete('otm_project_code');
		}
		return 'ok';
	}


	/**
	* Function update_sort_code
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function update_sort_code($data)
	{
		//$pr_seq = $data['pr_seq'];
		//$type = $data['pco_type'];
		$list = json_decode($data['pco_list']);

		for($i=0;$i<sizeof($list);$i++){
			$pco_seq = $list[$i];
			//$str_query = "update otm_project_code set pco_position='$i' where otm_project_pr_seq='$pr_seq' and pco_type='{$type}' and pco_seq='{$pco_seq}'";
			//$this->db->query($str_query);

			$update_data = array('pco_position' => $i);
			$this->db->where('otm_project_pr_seq', $data['pr_seq']);
			$this->db->where('pco_type', $data['pco_type']);
			$this->db->where('pco_seq', $pco_seq);
			$this->db->update('otm_project_code', $update_data);

		}
		return '{success:true}';
	}


	/**
	* Function factor_list
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function factor_list($data)
	{
		$temp_arr = array();

		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->where('rf_type', $data['type']);
		$this->db->order_by('ABS(rf_ord), rf_seq asc');
		$query = $this->db->get('otm_riskfactor');
		foreach ($query->result() as $temp_row)
		{
			//['pco_seq','pco_type','pco_name','pco_is_required','pco_is_default','pco_position','pco_default_value','pco_color','pco_is_use']
			$new_row['pco_seq'] = $temp_row->rf_seq;
			$new_row['pco_type'] = $temp_row->rf_type;
			$new_row['pco_name'] = $temp_row->rf_subject;
			$new_row['pco_position'] = $temp_row->rf_ord;

			array_push($temp_arr, $new_row);
		}
		return $temp_arr;
	}


	/**
	* Function create_factor
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function create_factor($data)
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$insert_data = array();
		$insert_data['otm_project_pr_seq'] = $data['pr_seq'];
		$insert_data['rf_subject'] = $data['pco_name'];
		$insert_data['rf_description'] = '';
		$insert_data['rf_type'] = $data['pco_type'];
		$insert_data['rf_ord'] = 1;
		$insert_data['writer'] = $writer;
		$insert_data['regdate'] = $date;

		$this->db->insert('otm_riskfactor', $insert_data);
		$result = $this->db->insert_id();
		return 'ok';
	}

	/**
	* Function update_factor
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function update_factor($data)
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$data2 = array(
			'rf_subject' => $data['pco_name'],
			//'rf_ord' => $data['pco_position'],
			'last_writer' => $writer,
			'last_regdate' => $date
		);

		$this->db->where('rf_seq', $data['pco_seq']);
		$this->db->update('otm_riskfactor', $data2);
		return 'ok';
	}


	/**
	* Function delete_factor
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function delete_factor($data)
	{
		for($i=0; $i<count($data['pco_list']); $i++){
			$this->db->where('rf_seq', $data['pco_list'][$i]);
			$this->db->delete('otm_riskfactor');
		}
		return 'ok';
	}


	/**
	* Function update_sort_factor
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function update_sort_factor($data)
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$pr_seq = $data['pr_seq'];
		$type = $data['pco_type'];
		$list = json_decode($data['pco_list']);

		for($i=0;$i<sizeof($list);$i++){
			$pco_seq = $list[$i];
			//$str_query = "update otm_riskfactor set rf_ord='$i', last_writer='{$writer}', last_regdate='{$date}' where otm_project_pr_seq='$pr_seq' and rf_type='{$type}' and rf_seq='{$pco_seq}'";
			//$this->db->query($str_query);

			$update_data = array('rf_ord'=>$i, 'last_writer'=>$writer, 'last_regdate'=>$date);
			$this->db->where('otm_project_pr_seq', $data['pr_seq']);
			$this->db->where('rf_type', $data['pco_type']);
			$this->db->where('rf_seq', $pco_seq);
			$this->db->update('otm_riskfactor', $update_data);
		}
		return '{success:true}';
	}

	
	/**
	* Function export_riskitem
	*
	* @return array
	*/
	function export_riskitem($data)
	{
		$return_array = array();
		
		//$data['pr_seq'] = $data['project_seq'];
		//$return_array = $this->requirement_list($data);
		
		$pr_seq = $data['project_seq'];

		//Member Info
		$member_name = $this->get_member_name();


		/**
			Get UserForm Data
		*/
		$p_customform = array();
		$custom_arr = array();

		$this->db->select('pc_seq,otm_project_pr_seq,pc_name,b.otm_riskitem_ri_seq,b.rcv_custom_value as cv_custom_value');
		$this->db->from('otm_project_customform as a');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('pc_category','ID_RISK');
		$this->db->where('pc_is_use','Y');
		$this->db->join('otm_riskitem_custom_value as b','a.pc_seq=b.otm_project_customform_pc_seq', 'left');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$custom_arr[$row->otm_riskitem_ri_seq][$row->pc_seq] = $row;
		}

		$this->db->select('pc_seq,pc_name,pc_formtype');
		$this->db->from('otm_project_customform');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('pc_category','ID_RISK');
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
			Get Attach File Data
		*/
		$attach_files = array();
		$this->db->select('otm_project_pr_seq as pr_seq, otm_category as category, of_no,target_seq,of_source,of_file,of_width,of_height');
		$this->db->from('otm_file');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('otm_category','ID_RISK');
		$files_query = $this->db->get();
		foreach ($files_query->result() as $row)
		{
			$row->path = '/uploads/files/'.$pr_seq.'/'.$row->of_file;
			$attach_files[$row->target_seq][$row->of_no] = $row;
		}
		/**
			End : Get Image File Data
		*/


		$this->db->where('otm_project_pr_seq', $pr_seq);
		$this->db->order_by('ri_seq asc');
		$query = $this->db->get('otm_riskitem');
		foreach ($query->result() as $temp_row)
		{
			//ri_seq, otm_project_pr_seq, ri_subject, ri_description, writer, regdate, last_writer, last_update

			$export_row[lang('subject')] = $temp_row->ri_subject;
			$export_row[lang('description')] = $temp_row->ri_description;

			for($i=0; $i<count($column_arr); $i++){
				$export_row[$custom_form_array[$i]->pc_name."(*)"] = $custom_arr[$temp_row->ri_seq][$column_arr[$i]]->cv_custom_value;
			}			

			$export_row[lang('writer')] = $member_name[$temp_row->writer];
			$export_row[lang('regdate')] = $temp_row->regdate;

			
			if($attach_files[$temp_row->ri_seq]){
				$temp_row->ri_file = $attach_files[$temp_row->ri_seq];
			}else{
				$temp_row->ri_file = array();
			}
			$export_row['otm_export_images'] = $temp_row->ri_file;

			$return_array[] = $export_row;
		}
		
		return $return_array;
	}


	/**
	* Function import_riskitem
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	public function import($data)
	{
		if(isset($data['function'])){
			switch($data['function'])
			{
				case 'import_riskitem':
					return $this->import_riskitem($data);
					break;
			}
		}else{
			return;	
		}
	}
	public function import_riskitem($data)
	{
		$pr_seq = $data['project_seq'];

		echo "<script> top.myUpdateProgress(0,'Step 1 : Data Loading...');</script>";

		$worksheet	= $data['import_data'];
		unset($data['import_data']);

		$highestRow	= $worksheet->getHighestRow();
		$highestColumn      = $worksheet->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
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

		/*
			ID 중복 확인
		* /
		$df_id_arry = array();
		for ($row = 2; $row <= $highestRow; ++ $row) {
			if($data['import_check_id']){
				$df_id_cell = $worksheet->getCellByColumnAndRow(0, $row);
				$df_id = $df_id_cell->getValue();
				array_push($df_id_arry,trim($df_id));
			}
			$tmp_per = (round(($row/$highestRow)*100) > 20)?(round(($row/$highestRow)*100)-20):0;

			echo "<script> top.myUpdateProgress(".$tmp_per.",'Step 2 : Data Checking...');</script>";
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
		/ *
			End : ID 중복 확인
		*/


		/**
			Get OTM Mamber Data
		*/
		$member_name = array();
		$this->db->from('otm_member');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$member_name[$row->mb_name] = $row->mb_email;
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
		$this->db->where('pc_category','ID_RISK');
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
					case 0:	$col_id = 'ri_subject';
							$col_array[$col_id] = trim($val);
						break;
					case 1:	$col_id = 'ri_description';
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

		
			/*
				Riskitem Insert
			*/
			$import_excel_data = array(
				'pr_seq' => $pr_seq,
				'ri_subject' => $col_array['ri_subject'],
				'ri_description' => $col_array['ri_description'],	
				'custom_form' => json_encode($custom_form_data),
				'return_key' => 'seq'
			);
			$seq = $this->create_riskitem($import_excel_data);
			
			echo "<script> top.myUpdateProgress(".round(($row/$highestRow)*100).",'Step 3 : Data Importing...(".$col_array['ri_subject'].":".$row."/".$highestRow.")');</script>";
		}

		$result_data['result'] = TRUE;
		$result_data['msg'] = $highestRow;

		return $result_data;
	}
}
//End of file riskanalysis_m.php
//Location: ./models/riskanalysis_m.php
