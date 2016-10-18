<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Requirement_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/

class Requirement_m extends CI_Model {

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
	* Function is_custom_value
	*
	* @param array $data Post Data.
	*
	* @return integer
	*/
	function is_custom_value($req_seq,$form_seq){
		$str_sql = "select count(*) as cnt from otm_requirement_custom_value
					where
						otm_requirement_req_seq='$req_seq' and
						otm_project_customform_pc_seq='$form_seq'
					";
		$query = $this->db->query($str_sql);
		$tmp_arr="";
		foreach ($query->result() as $temp_row)
		{
			$tmp_arr = $temp_row;
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
	function create_custom_value($req_seq,$form_seq,$form_type,$form_value){
		$this->db->set('otm_requirement_req_seq',				$req_seq);
		$this->db->set('otm_project_customform_pc_seq',	$form_seq);
		$this->db->set('reqcv_custom_type',				$form_type);
		$this->db->set('reqcv_custom_value',				$form_value);

		$this->db->insert('otm_requirement_custom_value');
		$this->db->insert_id();
	}

	/**
	* Function update_custom_value
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_custom_value($req_seq,$form_seq,$form_type,$form_value){
		$modify_array = array(
			'reqcv_custom_type'		=> $form_type,
			'reqcv_custom_value'		=> $form_value
		);
		$where = array(	'otm_requirement_req_seq'=>$req_seq,'otm_project_customform_pc_seq'=>$form_seq);
		$this->db->update('otm_requirement_custom_value',$modify_array,$where);
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
	*******************************
	*	Requirement
	*******************************
	*/

	/**
	* Function requirement_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function requirement_list($data)
	{
		$member_name = $this->get_member_name();
		$return_array = array();

		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->order_by('req_seq desc');
		$query = $this->db->get('otm_requirement');
		foreach ($query->result() as $temp_row)
		{
			$temp_row->req_assign = $member_name[$temp_row->req_assign];
			$temp_row->writer = $member_name[$temp_row->writer];
			$return_array[] = $temp_row;
		}
		return $return_array;
	}


	/**
	* Function create_requirement
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_requirement($data)
	{
		$pr_seq = $data['pr_seq'];
		$req_seq = $data['req_seq'];
		$req_subject = $data['req_subject'];
		$req_description = $data['req_description'];
		$req_priority = $data['req_priority'];
		$req_difficulty = $data['req_difficulty'];
		$req_accept = $data['req_accept'];
		$custom_form = $data['custom_form'];

		$date=date("Y-m-d H:i:s");
		$writer = $this->session->userdata('mb_email');

		$this->db->set('otm_project_pr_seq',	$pr_seq);
		$this->db->set('req_subject',			$req_subject);
		$this->db->set('req_description',		$req_description);
		$this->db->set('req_priority',			$req_priority);
		$this->db->set('req_difficulty',		$req_difficulty);
		$this->db->set('req_accept',			$req_accept);

		$this->db->set('writer',			$writer);
		$this->db->set('regdate',			$date);

		$this->db->set('last_writer',		$writer);
		$this->db->set('last_update',		$date);

		$this->db->insert('otm_requirement');
		$req_seq = $this->db->insert_id();

		$custom_arr = json_decode($custom_form);
		if(sizeof($custom_arr) >= 1){
			for($i=0;$i<sizeof($custom_arr);$i++){
				$form_seq = $custom_arr[$i]->seq;
				$form_type = $custom_arr[$i]->type;
				$form_value = $custom_arr[$i]->value;

				$this->create_custom_value($req_seq,$form_seq,$form_type,$form_value);
			}
		}

		if(isset($data['return_key'])){
			return $req_seq;
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

					$file_data['category'] = 'ID_REQ';
					$file_data['pr_seq'] = $pr_seq;
					$file_data['target_seq'] = $req_seq;
					$file_data['of_no'] = $i;


					$this->File_Form->file_upload($file_data);
				}
			}
		}

        return $req_seq;
	}


	/**
	* Function update_requirement
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_requirement($data)
	{
		$mb_lang = $this->session->userdata('mb_lang');
		//($mb_lang === "ko")

		$pr_seq = $data['pr_seq'];
		$req_seq = $data['req_seq'];
		$req_subject = $data['req_subject'];
		$req_description = $data['req_description'];
		$req_priority = $data['req_priority'];
		$req_difficulty = $data['req_difficulty'];
		$req_accept = $data['req_accept'];
		$custom_form = $data['custom_form'];


		$history = array();
		$history['pr_seq'] = $pr_seq;
		$history['req_seq'] = $req_seq;
		$history['details'] = array();

		$date=date("Y-m-d H:i:s");
		$writer = $this->session->userdata('mb_email');

		$this->db->from('otm_requirement');
		$this->db->where('otm_project_pr_seq',$pr_seq);
		$this->db->where('req_seq',$req_seq);

		$query = $this->db->get();
		$cnt = 0;
		foreach ($query->result() as $temp_row)
		{
			if($req_subject !== $temp_row->req_subject){
				$history['details'][$cnt]['action_type'] = ($mb_lang === "ko")?'요구사항명':'subject';//'req_subject';
				$history['details'][$cnt]['old_value'] = $temp_row->req_subject;
				$history['details'][$cnt]['value'] = $req_subject;
				$cnt++;
			}
			if($req_description !== $temp_row->req_description){
				$history['details'][$cnt]['action_type'] = ($mb_lang === "ko")?'설명':'description';//'req_description';
				$history['details'][$cnt]['old_value'] = $temp_row->req_description;
				$history['details'][$cnt]['value'] = $req_description;
				$cnt++;
			}
			if($req_priority !== $temp_row->req_priority){
				$history['details'][$cnt]['action_type'] = ($mb_lang === "ko")?'중요도':'Priority';//'req_priority';
				$history['details'][$cnt]['old_value'] = $temp_row->req_priority;
				$history['details'][$cnt]['value'] = $req_priority;
				$cnt++;
			}
			if($req_difficulty !== $temp_row->req_difficulty){
				$history['details'][$cnt]['action_type'] = ($mb_lang === "ko")?'난이도':'Difficulty';//'req_difficulty';
				$history['details'][$cnt]['old_value'] = $temp_row->req_difficulty;
				$history['details'][$cnt]['value'] = $req_difficulty;
				$cnt++;
			}
			if($req_accept !== $temp_row->req_accept){
				$history['details'][$cnt]['action_type'] = ($mb_lang === "ko")?'수용여부':'Accept';//'req_accept';
				$history['details'][$cnt]['old_value'] = $temp_row->req_accept;
				$history['details'][$cnt]['value'] = $req_accept;
				$cnt++;
			}
		}

		$modify_array = array(
			'req_subject'		=> $req_subject,
			'req_description'	=> $req_description,
			'req_priority'		=> $req_priority,
			'req_difficulty'	=> $req_difficulty,
			'req_accept'		=> $req_accept,
			'last_writer'		=> $writer,
			'last_update'		=> $date
		);
		$where = array(	'otm_project_pr_seq'=>$pr_seq,'req_seq'=>$req_seq);
		$this->db->update('otm_requirement',$modify_array,$where);


		$custom_arr = json_decode($custom_form);
		if(sizeof($custom_arr) >= 1){
			for($i=0;$i<sizeof($custom_arr);$i++){
				$form_name = $custom_arr[$i]->name;
				$form_seq = $custom_arr[$i]->seq;
				$form_type = $custom_arr[$i]->type;
				$form_value = $custom_arr[$i]->value;

				$is_item = $this->is_custom_value($req_seq,$form_seq);
				if($is_item >= 1){

					$this->db->from('otm_requirement_custom_value');
					$this->db->where('otm_requirement_req_seq',$req_seq);
					$this->db->where('otm_project_customform_pc_seq',$form_seq);
					$query = $this->db->get();
					foreach ($query->result() as $temp_row)
					{
						$tmp_fomarr = $temp_row;
					}

					if($form_value !== $tmp_fomarr->reqcv_custom_value){
						$history['details'][$cnt]['action_type'] = $form_name;
						$history['details'][$cnt]['old_value'] = $tmp_fomarr->reqcv_custom_value;
						$history['details'][$cnt]['value'] = $form_value;
						$cnt++;
					}

					$this->update_custom_value($req_seq,$form_seq,$form_type,$form_value);
				}else{

					$history['details'][$cnt]['action_type'] = $form_name;
					$history['details'][$cnt]['old_value'] = '';
					$history['details'][$cnt]['value'] = $form_value;
					$cnt++;

					$this->create_custom_value($req_seq,$form_seq,$form_type,$form_value);
				}
			}
		}

		if(isset($data['return_key'])){
			return $req_seq;
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

					$file_data['category'] = 'ID_REQ';
					$file_data['pr_seq'] = $pr_seq;
					$file_data['target_seq'] = $req_seq;
					$file_data['of_no'] = 2;

					$history['details'][$cnt]['action_type'] = 'req_file';
					$history['details'][$cnt]['old_value'] = '';
					$history['details'][$cnt]['value'] = $file_data['source']['name'];
					$cnt++;

					$this->File_Form->file_upload($file_data);
				}
			}
		}

		$history['category']= 'requirement';		
		$history['category_key'] = 'req_seq';
		$history['history_key'] = 'reqh_seq';

		$this->history->history($history);

		return $req_seq;
	}


	/**
	* Function delete_requirement
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_requirement($data)
	{
		$pr_seq = $data['pr_seq'];

		for($i=0; $i<count($data['req_list']); $i++)
		{
			$req_seq = $data['req_list'][$i];

			$delete_array = array(
				'otm_project_pr_seq' => $pr_seq,
				'req_seq'=>$req_seq
			);

			$result = $this->db->delete('otm_requirement',$delete_array);

			$delete_array = array('otm_requirement_req_seq' => $req_seq);
			$this->db->delete('otm_requirement_custom_value',$delete_array);

			$history['category']= 'requirement';
			$history['pr_seq']	= $pr_seq;
			$history['req_seq']	= $req_seq;
			$this->history->delete_history($history);


			$delete_file_array = array(
				'otm_project_pr_seq' => $pr_seq,
				'otm_category'	=> 'ID_REQ',
				'target_seq'=>$req_seq
			);
			$this->db->delete('otm_file',$delete_file_array);
		}
		return true;
	}


	/**
	* Function assign_requirement
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function assign_requirement($data)
	{
		$modify_array['req_assign'] = $data['assign_to'];

		for($i=0; $i<count($data['req_list']); $i++){
			$where = array('req_seq'=>$data['req_list'][$i]);
			$this->db->update('otm_requirement', $modify_array, $where);
		}

		return true;
	}


	/**
	* Function requirement_info
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function requirement_info($data)
	{
		$member_name = $this->get_member_name();

		$pr_seq	= $data['pr_seq'];
		$req_seq = $data['req_seq'];
		
		$return_array = array();

		$this->db->where('req_seq', $req_seq);
		$query = $this->db->get('otm_requirement');
		foreach ($query->result() as $temp_row)
		{
			$temp_row->writer = $member_name[$temp_row->writer];
			$return_array = $temp_row;
		}

		/* customform */
		$this->db->select('otm_project_customform_pc_seq as seq,
						reqcv_custom_type as formtype,
						reqcv_custom_value as value');
		$this->db->where('otm_requirement_req_seq', $req_seq);
		$query = $this->db->get('otm_requirement_custom_value');
		$return_array->df_customform = json_encode($query->result_array());

		/* attached file */
		$this->db->where('otm_project_pr_seq', $pr_seq);
		$this->db->where('otm_category', 'ID_REQ');		
		$this->db->where('target_seq', $req_seq);
		$this->db->order_by('of_no asc');
		$query = $this->db->get('otm_file');
		$return_array->fileform = json_encode($query->result_array());

		/* history */
		$return_array->req_history = json_encode($this->view_requirement_history($data));

		return $return_array;
	}


	/**
	* Function view_requirement_history
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function view_requirement_history($data)
	{
		$member_name = $this->get_member_name();

		$req_seq = $data['req_seq'];
		$pr_seq = $data['pr_seq'];

		$history = array();

		$this->db->from('otm_requirement_historys as reqh');
		$this->db->where('otm_requirement_req_seq',$req_seq);
		$this->db->order_by('reqh_seq asc');

		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$temp_row->mb_name = $member_name[$temp_row->writer];

			$history_detail = array();
			$this->db->from('otm_requirement_history_details as reqhd');
			$this->db->where('otm_requirement_historys_reqh_seq',$temp_row->reqh_seq);
			$this->db->order_by('reqhd_seq asc');

			$query2 = $this->db->get();
			foreach ($query2->result() as $temp_row2)
			{
				//if($temp_row2->action_type === 'req_description'){
				//	$temp_row2->value = $this->common->diffline($temp_row2->old_value, $temp_row2->value);
				//	$temp_row2->value = nl2br($temp_row2->value);
				//}
				$history_detail[] = $temp_row2;
			}

			$temp_row->detail = $history_detail;
			$history[] = $temp_row;
		}

		return $history;
	}
}
//End of file requirement_m.php
//Location: ./models/requirement_m.php
?>