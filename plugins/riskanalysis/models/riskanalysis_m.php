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
	* Function is_custom_value
	*
	* @param array $data Post Data.
	*
	* @return integer
	*/
	function is_custom_value($ri_seq,$form_seq){
		$str_sql = "select count(*) as cnt from otm_riskitem_custom_value
					where
						otm_riskitem_ri_seq='$ri_seq' and
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
		$temp_arr = array();

		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->order_by('ri_seq asc');
		$query = $this->db->get('otm_riskitem');
		foreach ($query->result() as $temp_row)
		{
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

		//$date=date('Y-m-d H:i:s');
		//$writer = $this->session->userdata('mb_email');

		//return "{success:true}";

		$arr = "";
		$str_sql = "
			select
				a.*
			from
				otm_riskitem as a
			where ri_seq='$ri_seq'			
		";

		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr = $row;
		}

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
		foreach ($query->result() as $row)
		{
			$tmp_arr[] = $row;
		}

		$arr->df_customform = json_encode($tmp_arr);


		/* attached file */
		$file_arr = "";
		$str_sql = "select * from otm_file where otm_project_pr_seq='$pr_seq' and otm_category='ID_RISK' and target_seq='$ri_seq' order by of_no asc";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$file_arr[] = $row;
		}
		$arr->fileform = json_encode($file_arr);

		return $arr;
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
				$temp_key = split('_',$k);
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

			$temp_arr['risk_req_cnt'] = 0;
			$temp_arr['risk_tc_cnt'] = 0;

			
			$return_arr[] = $temp_arr;
		}
		return $return_arr;
	}


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
		$this->db->join('otm_requirement as req','req.req_seq = rrm.otm_requirement_req_seq','left');
		$this->db->where('req.otm_project_pr_seq', $pr_seq);
		$this->db->where('rrm.otm_requirement_req_seq', $ri_seq);
		$this->db->order_by('rrl_seq asc');
		$query = $this->db->get('otm_risk_req_mapping as rrm');		
		$link_array = $query->result_array();

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
		for($i=0; $i<count($data['req_list']); $i++){
			$this->db->set('otm_riskitem_ri_seq',	$data['ri_seq']);
			$this->db->set('otm_requirement_req_seq', $data['req_list'][$i]);
			$this->db->insert('otm_risk_req_mapping');
		}

		return true;
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
				$str_sql = "update otm_project_code set pco_is_required='N' where otm_project_pr_seq='".$data['pr_seq']."' and pco_type='".$data['pco_type']."'";
				$query = $this->db->query($str_sql);
			}
			if($data['pco_is_default'] === 'Y'){
				$str_sql = "update otm_project_code set pco_is_default='N' where otm_project_pr_seq='".$data['pr_seq']."' and pco_type='".$data['pco_type']."'";
				$query = $this->db->query($str_sql);
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
				$str_sql = "update otm_project_code set pco_is_required='N' where otm_project_pr_seq='".$data['pr_seq']."' and pco_type='".$data['pco_type']."'";
				$query = $this->db->query($str_sql);
			}
			if($data['pco_is_default'] === 'Y'){
				$str_sql = "update otm_project_code set pco_is_default='N' where otm_project_pr_seq='".$data['pr_seq']."' and pco_type='".$data['pco_type']."'";
				$query = $this->db->query($str_sql);
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
		$pr_seq = $data['pr_seq'];
		$type = $data['pco_type'];
		$list = json_decode($data['pco_list']);

		for($i=0;$i<sizeof($list);$i++){
			$pco_seq = $list[$i];
			$str_query = "update otm_project_code set pco_position='$i' where otm_project_pr_seq='$pr_seq' and pco_type='{$type}' and pco_seq='{$pco_seq}'";

			$this->db->query($str_query);
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
			$str_query = "update otm_riskfactor set rf_ord='$i', last_writer='{$writer}', last_regdate='{$date}' where otm_project_pr_seq='$pr_seq' and rf_type='{$type}' and rf_seq='{$pco_seq}'";

			$this->db->query($str_query);
		}
		return '{success:true}';
	}

}
//End of file riskanalysis_m.php
//Location: ./models/riskanalysis_m.php
