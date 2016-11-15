<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Otm_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Otm_m extends CI_Model
{
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
	}

	/**
	* Function project_list
	*
	* @return array
	*/
	function project_list($data)
	{
		//$temp_arr = array();
		$writer = $this->session->userdata('mb_email');
		$mb_is_admin = $this->session->userdata('mb_is_admin');

		$start = (isset($data['start']))?$data['start']:null;
		$limit = (isset($data['limit']))?$data['limit']:null;
		/*if($start != null && $limit != null){
			$limitSql = " limit $limit OFFSET $start ";
		}else{
			$limitSql = "";
		}*/

		/**
			Get Project Defect Data
		*/
		$p_df = array();
		$this->db->select('a.otm_project_pr_seq, otm_defect_df_seq, dc_current_status_co_seq,pco_name,pco_seq, count(*) as defect_cnt_close');
		$this->db->from('otm_defect as a');
		$this->db->join('otm_defect_assign as b','a.df_seq=b.otm_defect_df_seq','');
		$this->db->join("(select * from otm_project_code where pco_type='status' and pco_is_required='Y') as b",'b.dc_current_status_co_seq=b.pco_seq','');
		$this->db->group_by('a.otm_project_pr_seq');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$p_df[$row->otm_project_pr_seq] = $row->defect_cnt_close;
		}
		/**
			End : Get Project Defect Data
		*/

		$this->db->select('a.pr_seq,a.pr_name,a.pr_startdate,a.pr_enddate,
					a.writer,
					(select mb_name from otm_member where a.writer=otm_member.mb_email) as writer_name,
					(select count(*) from otm_project_member where otm_project_pr_seq=a.pr_seq) as user_cnt,
					(select count(*) from otm_defect where otm_project_pr_seq=a.pr_seq) as defect_cnt');
		$this->db->from('otm_project as a');

		if($mb_is_admin !== 'Y'){
			$this->db->join('otm_project_member as b','a.pr_seq=b.otm_project_pr_seq','left');
			$this->db->where('b.otm_member_mb_email',$writer);
		}
		$this->db->order_by('pr_seq desc');

		$this->db->limit($limit,$start);

		$query = $this->db->get();
		$project_list = array();
		foreach ($query->result() as $row)
		{
			$row->defect_cnt_close = $p_df[$row->pr_seq];
			$project_list[] = $row;
		}
		return $project_list;
	}

	/**
	* Function project_totalCnt
	*
	* @return array
	*/
	function project_totalCnt(){
		//$temp_arr = array();
		$writer = $this->session->userdata('mb_email');
		$mb_is_admin = $this->session->userdata('mb_is_admin');

		if($mb_is_admin == 'Y'){
			$str_sql = "
				select
					count(*) as cnt
				from
					otm_project
			";
		}else{
			$str_sql = "
				select
					count(*) as cnt
				from
					otm_project as a,otm_project_member b
				where
					a.pr_seq=b.otm_project_pr_seq and
					b.otm_member_mb_email='$writer'
			";
		}

		$query = $this->db->query($str_sql);
		return $query->result();
	}


	/**
	* Function project_view
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function project_view($data)
	{
		$project_seq = $data['project_seq'];

		$this->db->select('pr_seq,pr_name,pr_description,pr_startdate,pr_enddate,regdate,last_update,
			(select mb_name from otm_member where a.writer=otm_member.mb_email)as writer,
			(select mb_name from otm_member where a.last_writer=otm_member.mb_email)as last_writer
			');
		$this->db->from('otm_project as a');
		$this->db->where('a.pr_seq',$project_seq);
		$query = $this->db->get();

		foreach ($query->result() as $temp_row)
		{
			$temp_arr = $temp_row;
		}
		return $temp_arr;
	}

	/**
	* Function copy_default_option
	*
	* @param array $project_seq Post Data.
	*
	* @return array
	*/
	function copy_default_option($project_seq)
	{
		$data = array();

		$id_rule_arr = array();

		$this->db->select('*');
		$this->db->from('otm_code');
		$this->db->order_by('co_seq asc');
		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$data['otm_project_pr_seq'] = $project_seq;
			$data['pco_type']			= $temp_row->co_type;
			$data['pco_name']			= $temp_row->co_name;
			$data['pco_is_required']	= $temp_row->co_is_required;
			$data['pco_is_default']		= $temp_row->co_is_default;
			$data['pco_position']		= $temp_row->co_position;
			$data['pco_default_value']	= $temp_row->co_default_value;
			$data['pco_color']			= $temp_row->co_color;

			$this->db->insert('otm_project_code', $data);
			$role_seq = $this->db->insert_id();

			$temp_row->pco_seq = $role_seq;
			$id_rule_arr[] = $temp_row;
		}

		$pattern = "/,/";
		for($i=0; $i<count($id_rule_arr); $i++)
		{
			$temp_tc_co_seq = '';
			$temp_tc_pco_seq = '';

			//df id rule 찾기
			if($id_rule_arr[$i]->co_type === 'df_id_rule'){
				$temp_default_value = $id_rule_arr[$i]->co_default_value;
				$rule_arr = preg_split($pattern,$temp_default_value);

				//tc id rule 사용중인지 확인
				if(isset($rule_arr[0]) || $tmp_arr[0])
				{
					//사용중인 tc id rule 찾기
					for($j=0; $j<count($id_rule_arr); $j++)
					{
						if($id_rule_arr[$j]->co_seq == $rule_arr[0])
						{
							//tc id rule seq 변경 사항 확인
							$temp_tc_co_seq = $id_rule_arr[$j]->co_seq;
							$temp_tc_pco_seq = $id_rule_arr[$j]->pco_seq;
						}
					}
				}

				//변경 사항 확인되면 df id rule 정보 업데이트
				if($temp_tc_pco_seq !== '')
				{
					//변경된 tc id rule seq를 df id rule default_value에 업데이트
					$new_pco_default_value = str_replace($temp_tc_co_seq, $temp_tc_pco_seq, $id_rule_arr[$i]->co_default_value );

					$new_df_data = array(
						'pco_default_value' => $new_pco_default_value
					);

					$this->db->where('pco_seq', $id_rule_arr[$i]->pco_seq);
					$this->db->update('otm_project_code', $new_df_data);
				}
			}
		}

		$str_sql = "select
				a.co_seq,b.pco_seq
			from
				otm_code as a, otm_project_code as b
			where
				a.co_type='status' and
				a.co_type=b.pco_type and
				a.co_name=b.pco_name and
				b.otm_project_pr_seq='$project_seq'
				order by a.co_seq asc";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $temp_row)
		{
			$project_code_arr[$temp_row->co_seq] = $temp_row->pco_seq;
		}

		if ($this->db->table_exists('otm_project_defect_workflow') && $this->db->table_exists('otm_defect_workflow')) {
			$this->db->select('*');
			$this->db->from('otm_defect_workflow');
			$this->db->order_by('dw_seq asc');
			$query = $this->db->get();
			foreach ($query->result() as $temp_row)
			{
				$from = $project_code_arr[$temp_row->otm_code_co_seq_from];
				$to = $project_code_arr[$temp_row->otm_code_co_seq_to];
				if(isset($from) && isset($to)){
					$data = array();
					$data['otm_project_pr_seq']				= $project_seq;
					$data['otm_role_rp_seq']				= $temp_row->otm_role_rp_seq;
					$data['otm_project_code_pco_seq_from']	= $project_code_arr[$temp_row->otm_code_co_seq_from];
					$data['otm_project_code_pco_seq_to']	= $project_code_arr[$temp_row->otm_code_co_seq_to];
					$data['pdw_value']						= $temp_row->dw_value;

					$this->db->insert('otm_project_defect_workflow', $data);
				}
			}
		}

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$data = array();
		$this->db->select('*');
		$this->db->from('otm_customform');
		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$data['otm_project_pr_seq'] = $project_seq;
			$data['pc_name']			= $temp_row->cf_name;
			$data['pc_category']		= $temp_row->cf_category;
			$data['pc_is_required']		= $temp_row->cf_is_required;
			$data['pc_is_display']		= $temp_row->cf_is_display;
			$data['pc_formtype']		= $temp_row->cf_formtype;
			$data['pc_default_value']	= $temp_row->cf_default_value;
			$data['pc_content']			= $temp_row->cf_content;
			$data['writer']				= $temp_row->writer;
			$data['regdate']			= $temp_row->regdate;
			$data['last_writer']		= $writer;
			$data['last_update']		= $date;
			$data['pc_1']				= $temp_row->cf_1;

			$this->db->insert('otm_project_customform', $data);
		}
	}

	/**
	* Function copy_project_option
	*
	* @param array $project_seq Post Data.
	*
	* @return array
	*/
	function copy_project_option($target_pr_seq, $project_seq,$project_data)
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');


		$new_code = array();

		$data = array();
		$id_rule_arr = array();

		$this->db->select('*');
		$this->db->from('otm_project_code');
		$this->db->where('otm_project_pr_seq',$target_pr_seq);
		$this->db->order_by('pco_seq asc');
		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$old_pco_seq				= $temp_row->pco_seq;
			$data['otm_project_pr_seq'] = $project_seq;
			$data['pco_type']			= $temp_row->pco_type;
			$data['pco_name']			= $temp_row->pco_name;
			$data['pco_is_required']	= $temp_row->pco_is_required;
			$data['pco_is_default']		= $temp_row->pco_is_default;
			$data['pco_position']		= $temp_row->pco_position;
			$data['pco_default_value']	= $temp_row->pco_default_value;
			$data['pco_color']			= $temp_row->pco_color;
			$data['pco_is_use']			= $temp_row->pco_is_use;
			$data['pco_1']				= $temp_row->pco_1;
			$data['pco_2']				= $temp_row->pco_2;
			$data['pco_3']				= $temp_row->pco_3;
			$data['pco_4']				= $temp_row->pco_4;
			$data['pco_5']				= $temp_row->pco_5;

			$this->db->insert('otm_project_code', $data);
			$role_seq = $this->db->insert_id();

			$new_code[$old_pco_seq] = $role_seq;

			$temp_row->pco_seq = $role_seq;
			$id_rule_arr[] = $temp_row;
		}

		$pattern = "/,/";
		for($i=0; $i<count($id_rule_arr); $i++)
		{
			$temp_tc_co_seq = '';
			$temp_tc_pco_seq = '';

			//df id rule
			if($id_rule_arr[$i]->co_type === 'df_id_rule'){
				$temp_default_value = $id_rule_arr[$i]->co_default_value;
				$rule_arr = preg_split($pattern,$temp_default_value);

				//tc id rule
				if(isset($rule_arr[0]) || $tmp_arr[0])
				{
					for($j=0; $j<count($id_rule_arr); $j++)
					{
						if($id_rule_arr[$j]->co_seq == $rule_arr[0])
						{
							$temp_tc_co_seq = $id_rule_arr[$j]->co_seq;
							$temp_tc_pco_seq = $id_rule_arr[$j]->pco_seq;
						}
					}
				}

				if($temp_tc_pco_seq !== '')
				{
					$new_pco_default_value = str_replace($temp_tc_co_seq, $temp_tc_pco_seq, $id_rule_arr[$i]->co_default_value );

					$new_df_data = array(
						'pco_default_value' => $new_pco_default_value
					);

					$this->db->where('pco_seq', $id_rule_arr[$i]->pco_seq);
					$this->db->update('otm_project_code', $new_df_data);
				}
			}
		}

		if($project_data['copy_pjtmem_chk'] == 'true'){
			$data = array();
			$str_sql = "select
							a.otm_member_mb_email,b.otm_role_rp_seq
							from
							otm_project_member as a, otm_project_member_role as b
							where
							a.pm_seq=b.otm_project_member_pm_seq and
							a.otm_project_pr_seq='$target_pr_seq' and
							a.otm_member_mb_email!='$writer'
			";
			$query = $this->db->query($str_sql);
			foreach ($query->result() as $temp_row)
			{
				$data['otm_project_pr_seq']		= $project_seq;
				$data['otm_member_mb_email']	= $temp_row->otm_member_mb_email;

				$role_seq						= $temp_row->otm_role_rp_seq;

				$this->db->insert('otm_project_member', $data);
				$pm_seq = $this->db->insert_id();

				$data2 = array(
					'otm_project_member_pm_seq' => $pm_seq,
					'otm_role_rp_seq' => $role_seq
				);
				$this->db->insert('otm_project_member_role', $data2);
			}
		}

		$data = array();
		$str_sql = "
			select
				a.pco_seq as co_seq,b.pco_seq as pco_seq
			from
				otm_project_code as a,otm_project_code as b
			where
				a.pco_type='status' and
				b.pco_type='status' and
				a.pco_type=b.pco_type and
				a.pco_name=b.pco_name and
				a.otm_project_pr_seq='$target_pr_seq' and
				b.otm_project_pr_seq='$project_seq'
			order by a.pco_seq asc
		";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $temp_row)
		{
			$project_code_arr[$temp_row->co_seq] = $temp_row->pco_seq;
		}

		if ($this->db->table_exists('otm_project_defect_workflow') && $this->db->table_exists('otm_defect_workflow')) {
			$this->db->select('*');
			$this->db->from('otm_defect_workflow');
			$this->db->order_by('dw_seq asc');
			$query = $this->db->get();
			foreach ($query->result() as $temp_row)
			{
				$from = $project_code_arr[$temp_row->otm_code_co_seq_from];
				$to = $project_code_arr[$temp_row->otm_code_co_seq_to];
				if(isset($from) && isset($to)){
					$data = array();
					$data['otm_project_pr_seq']				= $project_seq;
					$data['otm_role_rp_seq']				= $temp_row->otm_role_rp_seq;
					$data['otm_project_code_pco_seq_from']	= $project_code_arr[$temp_row->otm_code_co_seq_from];
					$data['otm_project_code_pco_seq_to']	= $project_code_arr[$temp_row->otm_code_co_seq_to];
					$data['pdw_value']						= $temp_row->dw_value;

					$this->db->insert('otm_project_defect_workflow', $data);
				}
			}
		}

		$customform_arr = array();
		$data = array();
		$this->db->select('*');
		$this->db->from('otm_project_customform');
		$this->db->where('otm_project_pr_seq',$target_pr_seq);
		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$data['otm_project_pr_seq'] = $project_seq;
			$data['pc_name']			= $temp_row->pc_name;
			$data['pc_category']		= $temp_row->pc_category;
			$data['pc_is_required']		= $temp_row->pc_is_required;
			$data['pc_is_display']		= $temp_row->pc_is_display;
			$data['pc_formtype']		= $temp_row->pc_formtype;
			$data['pc_default_value']	= $temp_row->pc_default_value;
			$data['pc_content']			= $temp_row->pc_content;
			$data['pc_is_use']			= $temp_row->pc_is_use;
			$data['writer']				= $writer;
			$data['regdate']			= $date;
			$data['last_writer']		= $writer;
			$data['last_update']		= $date;
			$data['pc_1']				= $temp_row->pc_1;
			$data['pc_2']				= $temp_row->pc_2;
			$data['pc_3']				= $temp_row->pc_3;
			$data['pc_4']				= $temp_row->pc_4;
			$data['pc_5']				= $temp_row->pc_5;

			$this->db->insert('otm_project_customform', $data);
			$new_pc_seq = $this->db->insert_id();

			$customform_arr[$temp_row->pc_seq] = $new_pc_seq;
		}

		if($project_data['copy_def_chk'] == 'true'){
			$old_df_seq_arr = array();
			$new_df_seq_arr = array();
			$data = array();
			$this->db->select('*');
			$this->db->from('otm_defect as a');
			$this->db->where('otm_project_pr_seq',$target_pr_seq);
			$this->db->join("otm_defect_assign as e",'a.df_seq=e.otm_defect_df_seq','');

			$query = $this->db->get();

			foreach ($query->result() as $temp_row)
			{
				//$df_seq							= $temp_row->df_seq;
				$data['otm_project_pr_seq']		= $project_seq;
				$data['df_id']					= $temp_row->df_id;
				$data['df_subject']				= $temp_row->df_subject;
				$data['df_description']			= $temp_row->df_description;
				$data['df_severity']			= $new_code[$temp_row->df_severity];
				$data['df_priority']			= $new_code[$temp_row->df_priority];
				$data['df_frequency']			= $new_code[$temp_row->df_frequency];
				$data['writer']					= $writer;
				$data['regdate']				= $date;
				$data['last_writer']			= $writer;
				$data['last_update']			= $date;
				$data['df_1']					= $temp_row->df_1;
				$data['df_2']					= $temp_row->df_2;
				$data['df_3']					= $temp_row->df_3;
				$data['df_4']					= $temp_row->df_4;
				$data['df_5']					= $temp_row->df_5;

				$this->db->insert('otm_defect', $data);
				$new_df_seq = $this->db->insert_id();

				array_push($old_df_seq_arr,$temp_row->df_seq);
				$new_df_seq_arr[$temp_row->df_seq] = $new_df_seq;

				$history = array();
				$history['pr_seq'] = $project_seq;
				$history['df_seq'] = $new_df_seq;
				$history['details'] = array();

				$history['details'][0]['action_type'] = 'df_project_copy';
				$history['details'][0]['old_value'] = $target_pr_seq;
				$history['details'][0]['value'] = $project_seq;

				$history['category']= 'defect';
				$this->history->history($history);

				$this->db->set('otm_defect_df_seq',			$new_df_seq);
				$this->db->set('dc_from',					$temp_row->dc_from);
				$this->db->set('dc_to',						$temp_row->dc_to);
				$this->db->set('dc_start_date',				$temp_row->dc_start_date);
				$this->db->set('dc_end_date',				$temp_row->dc_end_date);
				$this->db->set('dc_regdate',				$temp_row->dc_regdate);
				$this->db->set('dc_previous_status_co_seq',	$new_code[$temp_row->dc_previous_status_co_seq]);
				$this->db->set('dc_current_status_co_seq',	$new_code[$temp_row->dc_current_status_co_seq]);

				$this->db->insert('otm_defect_assign');
			}

			$old_df_seq = implode(',', $old_df_seq_arr);

			if(sizeof($old_df_seq_arr) >= 1){
				$data = array();
				$str_sql = "select * from otm_defect_custom_value where otm_defect_df_seq in ($old_df_seq)";
				$query = $this->db->query($str_sql);
				foreach ($query->result() as $temp_row)
				{
					$data['otm_defect_df_seq']				= $new_df_seq_arr[$temp_row->otm_defect_df_seq];
					$data['otm_project_customform_pc_seq']	= $customform_arr[$temp_row->otm_project_customform_pc_seq];
					$data['cv_custom_type']					= $temp_row->cv_custom_type;
					$data['cv_custom_value']				= $temp_row->cv_custom_value;
					$data['cv_1'] 							= $temp_row->cv_1;
					$data['cv_2'] 							= $temp_row->cv_2;
					$data['cv_3'] 							= $temp_row->cv_3;
					$data['cv_4'] 							= $temp_row->cv_4;
					$data['cv_5'] 							= $temp_row->cv_5;

					$this->db->insert('otm_defect_custom_value', $data);
				}

				$data = array();
				$str_sql = "select * from otm_file where otm_category='ID_DEFECT' and otm_project_pr_seq='$target_pr_seq' and target_seq in ($old_df_seq)";
				$query = $this->db->query($str_sql);
				foreach ($query->result() as $temp_row)
				{
					$data['otm_category']					= $temp_row->otm_category;
					$data['otm_project_pr_seq']				= $project_seq;
					$data['target_seq']						= $new_df_seq_arr[$temp_row->target_seq];
					$data['of_no']							= $temp_row->of_no;
					$data['of_source'] 						= $temp_row->of_source;
					$data['of_file'] 						= $temp_row->of_file;
					$data['of_filesize'] 					= $temp_row->of_filesize;
					$data['of_width'] 						= $temp_row->of_width;
					$data['of_height'] 						= $temp_row->of_height;
					$data['writer']							= $writer;
					$data['regdate']						= $date;
					$data['of_1']							= $temp_row->of_1;
					$data['of_2']							= $temp_row->of_2;
					$data['of_3']							= $temp_row->of_3;
					$data['of_4']							= $temp_row->of_4;
					$data['of_5']							= $temp_row->of_5;
					$data['otm_project_storage_ops_seq']	= $temp_row->otm_project_storage_ops_seq;

					$this->db->insert('otm_file', $data);

					//$default_directory = 'uploads/files/'.$project_seq."/";
					if(is_dir('uploads/files/'.$project_seq) != true){

						@mkdir("uploads/files/".$project_seq, 0707);
						@chmod("uploads/files/".$project_seq, 0707);
					}
					$oldfile = 'uploads/files/'.$target_pr_seq.'/'.$temp_row->of_file;
					$newfile = 'uploads/files/'.$project_seq.'/'.$temp_row->of_file;
					copy($oldfile, $newfile);
				}
			}
		}

		if($project_data['copy_tc_chk'] == 'true'){
			$old_tp_seq_arr = array();
			$new_tp_seq_arr = array();

			$data = array();
			$this->db->select('*');
			$this->db->from('otm_testcase_plan');
			$this->db->where('otm_project_pr_seq',$target_pr_seq);
			$this->db->order_by('tp_seq asc');
			$query = $this->db->get();
			foreach ($query->result() as $temp_row)
			{
				$data['otm_project_pr_seq']		= $project_seq;
				$data['tp_subject']				= $temp_row->tp_subject;
				$data['tp_description']			= $temp_row->tp_description;
				$data['tp_startdate']			= $temp_row->tp_startdate;
				$data['tp_enddate']				= $temp_row->tp_enddate;
				$data['tp_status']				= $temp_row->tp_status;
				$data['writer']					= $writer;
				$data['regdate']				= $date;
				$data['last_writer']			= $writer;
				$data['last_update']			= $date;
				$data['tp_1']					= $temp_row->tp_1;
				$data['tp_2']					= $temp_row->tp_2;
				$data['tp_3']					= $temp_row->tp_3;
				$data['tp_4']					= $temp_row->tp_4;
				$data['tp_5']					= $temp_row->tp_5;

				$this->db->insert('otm_testcase_plan', $data);
				$new_tp_seq = $this->db->insert_id();

				array_push($old_tp_seq_arr,$temp_row->tp_seq);
				$new_tp_seq_arr[$temp_row->tp_seq] = $new_tp_seq;
			}

			$old_tc_seq_arr = array();
			$new_tc_seq_arr = array();

			$data = array();
			$this->db->select('*');
			$this->db->from('otm_testcase');
			$this->db->where('otm_project_pr_seq',$target_pr_seq);
			$query = $this->db->get();
			foreach ($query->result() as $temp_row)
			{
				$data['otm_project_pr_seq']		= $project_seq;
				$data['tc_subject']				= $temp_row->tc_subject;
				$data['tc_precondition']		= $temp_row->tc_precondition;
				$data['tc_testdata']			= $temp_row->tc_testdata;
				$data['tc_procedure']			= $temp_row->tc_procedure;
				$data['tc_expected_result']		= $temp_row->tc_expected_result;
				$data['tc_description']			= $temp_row->tc_description;
				$data['tc_inp_id']				= $temp_row->tc_inp_id;
				$data['tc_inp_pid']				= $temp_row->tc_inp_pid;
				$data['tc_out_id']				= $temp_row->tc_out_id;
				$data['tc_is_task']				= $temp_row->tc_is_task;
				$data['tc_ord']					= $temp_row->tc_ord;
				$data['writer']					= $writer;
				$data['regdate']				= $date;
				$data['last_writer']			= $writer;
				$data['last_update']			= $date;
				$data['tc_1']					= $temp_row->tc_1;
				$data['tc_2']					= $temp_row->tc_2;
				$data['tc_3']					= $temp_row->tc_3;
				$data['tc_4']					= $temp_row->tc_4;
				$data['tc_5']					= $temp_row->tc_5;
				$data['tc_6']					= $temp_row->tc_6;
				$data['tc_7']					= $temp_row->tc_7;
				$data['tc_8']					= $temp_row->tc_8;
				$data['tc_9']					= $temp_row->tc_9;
				$data['tc_10']					= $temp_row->tc_10;

				$this->db->insert('otm_testcase', $data);
				$new_tc_seq = $this->db->insert_id();

				array_push($old_tc_seq_arr,$temp_row->tc_seq);
				$new_tc_seq_arr[$temp_row->tc_seq] = $new_tc_seq;

				$history = array();
				$history['pr_seq'] = $project_seq;
				$history['tc_seq'] = $new_tc_seq;
				$history['details'] = array();

				$history['details'][0]['action_type'] = 'tc_project_copy';
				$history['details'][0]['old_value'] = $target_pr_seq;
				$history['details'][0]['value'] = $project_seq;

				$history['category']= 'testcase';
				$this->history->history($history);
			}

			$old_tc_seq = implode(',', $old_tc_seq_arr);

			if(sizeof($old_tc_seq_arr) >= 1){

				$data = array();
				$str_sql = "select * from otm_testcase_custom_value where otm_testcase_tc_seq in ($old_tc_seq)";
				$query = $this->db->query($str_sql);
				foreach ($query->result() as $temp_row)
				{
					$data['otm_testcase_tc_seq']				= $new_tc_seq_arr[$temp_row->otm_testcase_tc_seq];
					$data['otm_project_customform_pc_seq']		= $customform_arr[$temp_row->otm_project_customform_pc_seq];
					$data['tcv_custom_type']					= $temp_row->tcv_custom_type;
					$data['tcv_custom_value']					= $temp_row->tcv_custom_value;

					$this->db->insert('otm_testcase_custom_value', $data);
				}


				$data = array();
				$str_sql = "select * from otm_testcase_link where otm_testcase_tc_seq in ($old_tc_seq)";
				$query = $this->db->query($str_sql);
				foreach ($query->result() as $temp_row)
				{
					$data['otm_testcase_plan_tp_seq']		=	$new_tp_seq_arr[$temp_row->otm_testcase_plan_tp_seq];
					$data['otm_testcase_tc_seq']			=	$new_tc_seq_arr[$temp_row->otm_testcase_tc_seq];
					$data['tl_inp_pid']						=	$temp_row->tl_inp_pid;
					$data['tl_ord']							=	$temp_row->tl_ord;
					$data['tl_assign_from']					=	$temp_row->tl_assign_from;
					$data['tl_assign_to']					=	$temp_row->tl_assign_to;
					$data['tl_assign_date']					=	$temp_row->tl_assign_date;
					$data['tl_assign_deadline_date']		=	$temp_row->tl_assign_deadline_date;
					$data['tl_assign_enddate']				=	$temp_row->tl_assign_enddate;
					$data['tl_1']							=	$temp_row->tl_1;
					$data['tl_2']							=	$temp_row->tl_2;
					$data['tl_3']							=	$temp_row->tl_3;
					$data['tl_4']							=	$temp_row->tl_4;
					$data['tl_5']							=	$temp_row->tl_5;

					$this->db->insert('otm_testcase_link', $data);
				}

				$data = array();
				$str_sql = "select * from otm_file where otm_category='ID_TC' and otm_project_pr_seq='$target_pr_seq' and target_seq in ($old_tc_seq)";
				$query = $this->db->query($str_sql);
				foreach ($query->result() as $temp_row)
				{
					$data['otm_category']					= $temp_row->otm_category;
					$data['otm_project_pr_seq']				= $project_seq;
					$data['target_seq']						= $new_tc_seq_arr[$temp_row->target_seq];
					$data['of_no']							= $temp_row->of_no;
					$data['of_source'] 						= $temp_row->of_source;
					$data['of_file'] 						= $temp_row->of_file;
					$data['of_filesize'] 					= $temp_row->of_filesize;
					$data['of_width'] 						= $temp_row->of_width;
					$data['of_height'] 						= $temp_row->of_height;
					$data['writer']							= $writer;
					$data['regdate']						= $date;
					$data['of_1']							= $temp_row->of_1;
					$data['of_2']							= $temp_row->of_2;
					$data['of_3']							= $temp_row->of_3;
					$data['of_4']							= $temp_row->of_4;
					$data['of_5']							= $temp_row->of_5;
					$data['otm_project_storage_ops_seq']	= $temp_row->otm_project_storage_ops_seq;

					$this->db->insert('otm_file', $data);

					//$default_directory = 'uploads/files/'.$project_seq."/";
					if(is_dir('uploads/files/'.$project_seq) != true){

						@mkdir("uploads/files/".$project_seq, 0707);
						@chmod("uploads/files/".$project_seq, 0707);
					}
					$oldfile = 'uploads/files/'.$target_pr_seq.'/'.$temp_row->of_file;
					$newfile = 'uploads/files/'.$project_seq.'/'.$temp_row->of_file;
					copy($oldfile, $newfile);
				}
			}
		}


		if($project_data['copy_filedoc_chk'] == 'true'){//$target_pr_seq, $project_seq
			$old_storage_seq_arr = array();
			$new_storage_seq_arr = array();

			$data = array();
			$str_sql = "select * from otm_project_storage where otm_project_pr_seq='$target_pr_seq'";
			$query = $this->db->query($str_sql);
			foreach ($query->result() as $temp_row)
			{
				$data['otm_project_pr_seq']				= $project_seq;
				$data['ops_subject']					= $temp_row->ops_subject;
				$data['ops_pid']						= $temp_row->ops_pid;
				$data['ops_ord']						= $temp_row->ops_ord;
				$data['ops_data_trach']					= $temp_row->ops_data_trach;
				$data['writer']							= $writer;
				$data['regdate']						= $date;
				$data['last_writer']					= $temp_row->last_writer;
				$data['last_update']					= $temp_row->last_update;

				$this->db->insert('otm_project_storage', $data);
				$new_storage_seq = $this->db->insert_id();

				array_push($old_storage_seq_arr,$temp_row->ops_seq);
				$new_storage_seq_arr[$temp_row->ops_seq] = $new_storage_seq;
			}

			$old_storage_seq = implode(',', $old_storage_seq_arr);

			if(sizeof($old_storage_seq_arr) >= 1){

				$data = array();
				$str_sql = "select * from otm_project_storage_permission where otm_project_pr_seq='$target_pr_seq' and otm_project_storage_ops_seq in ($old_storage_seq)";
				$query = $this->db->query($str_sql);
				foreach ($query->result() as $temp_row)
				{
					$data['otm_project_pr_seq']				= $project_seq;
					$data['otm_project_storage_ops_seq']	= $new_storage_seq_arr[$temp_row->otm_project_storage_ops_seq];
					$data['otm_role_rp_seq']				= $temp_row->otm_role_rp_seq;
					$data['psp_read']						= $temp_row->psp_read;
					$data['psp_write']						= $temp_row->psp_write;
					$data['psp_delete']						= $temp_row->psp_delete;

					$this->db->insert('otm_project_storage_permission', $data);
				}

				$data = array();
				$str_sql = "select * from otm_file where otm_category='ID_STORAGE' and otm_project_pr_seq='$target_pr_seq' and target_seq in ($old_storage_seq)";
				$query = $this->db->query($str_sql);
				foreach ($query->result() as $temp_row)
				{
					$data['otm_category']					= $temp_row->otm_category;
					$data['otm_project_pr_seq']				= $project_seq;
					$data['target_seq']						= $new_storage_seq_arr[$temp_row->target_seq];
					$data['of_no']							= $temp_row->of_no;
					$data['of_source'] 						= $temp_row->of_source;
					$data['of_file'] 						= $temp_row->of_file;
					$data['of_filesize'] 					= $temp_row->of_filesize;
					$data['of_width'] 						= $temp_row->of_width;
					$data['of_height'] 						= $temp_row->of_height;
					$data['writer']							= $writer;
					$data['regdate']						= $date;
					$data['of_1']							= $temp_row->of_1;
					$data['of_2']							= $temp_row->of_2;
					$data['of_3']							= $temp_row->of_3;
					$data['of_4']							= $temp_row->of_4;
					$data['of_5']							= $temp_row->of_5;
					$data['otm_project_storage_ops_seq']	= $temp_row->otm_project_storage_ops_seq;

					$this->db->insert('otm_file', $data);

					//$default_directory = 'uploads/files/'.$project_seq."/";
					if(is_dir('uploads/files/'.$project_seq) != true){

						@mkdir("uploads/files/".$project_seq, 0707);
						@chmod("uploads/files/".$project_seq, 0707);
					}
					$oldfile = 'uploads/files/'.$target_pr_seq.'/'.$temp_row->of_file;
					$newfile = 'uploads/files/'.$project_seq.'/'.$temp_row->of_file;
					copy($oldfile, $newfile);
				}
			}
		}
	}

	/**
	* Function duplicate_project
	*
	* @param array $project_data Post Data.
	*
	* @return array
	*/
	function duplicate_project($data)
	{
		$project_name = $data['project_name'];

		$str_sql = "select count(*) as cnt from otm_project where pr_name='$project_name'";
		$query = $this->db->query($str_sql);
		$result  = $query->result();
		return $result[0]->cnt;
	}

	/**
	* Function create_project
	*
	* @param array $project_data Post Data.
	*
	* @return array
	*/
	function create_project($project_data)
	{
		$pg_seq = $project_data['pg_seq'];
		$project_name = $project_data['project_name'];
		$project_startdate = $project_data['project_startdate'];
		$project_enddate = $project_data['project_enddate'];
		$project_description = $project_data['project_description'];

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$this->db->set('pr_name', $project_name);
		$this->db->set('pr_startdate', $project_startdate);
		$this->db->set('pr_enddate', $project_enddate);
		$this->db->set('pr_description', $project_description);
		$this->db->set('writer', $writer);
		$this->db->set('regdate', $date);
		$this->db->set('otm_project_group_pg_seq', $pg_seq);

		$pr_ord = $this->get_ord_maxval('otm_project','otm_project_group_pg_seq',$pg_seq,'pr_ord');
		$this->db->set('pr_ord', $pr_ord);

		$this->db->insert('otm_project');
		$result = $this->db->insert_id();

		$data2 = array(
			'otm_project_pr_seq' => $result,
			'otm_member_mb_email' => $writer
		);
		$this->db->insert('otm_project_member', $data2);
		$pm_seq = $this->db->insert_id();

		$data2 = array(
			'otm_project_member_pm_seq' => $pm_seq,
			'otm_role_rp_seq' => '1'
		);
		$this->db->insert('otm_project_member_role', $data2);

		$this->copy_default_option($result);
		return $result;
	}

	/**
	* Function update_project
	*
	* @param array $project_data Post Data.
	*
	* @return array
	*/
	function update_project($project_data)
	{
		$project_seq = $project_data['project_seq'];
		$project_name = $project_data['project_name'];
		$project_startdate = $project_data['project_startdate'];
		$project_enddate = $project_data['project_enddate'];
		$project_description = $project_data['project_description'];

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$modify_array = array(
			'pr_name'			=> $project_name,
			'pr_startdate'		=> $project_startdate,
			'pr_enddate'		=> $project_enddate,
			'pr_description'	=> $project_description,
			'last_writer'		=> $writer,
			'last_update'		=> $date
		);
		$where = array(	'pr_seq'=>$project_seq);
		$this->db->update('otm_project', $modify_array, $where);
		return $project_seq;
	}

	/**
	* Function delete_project
	*
	* @param array $project_data Post Data.
	*
	* @return array
	*/
	function delete_project($project_data)
	{
		$project_seq = $project_data['project_seq'];
		$delete_array = array(
			'pr_seq' => $project_seq
		);
		$result = $this->db->delete('otm_project', $delete_array);

		$database_name = $this->db->database;
		$str_sql = "
				SELECT
				TABLE_NAME, COLUMN_NAME
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE TABLE_SCHEMA = '$database_name' and COLUMN_NAME='otm_project_pr_seq'
			";

		$query = $this->db->query($str_sql);
		foreach ($query->result() as $temp_row)
		{
			$this->db->where('otm_project_pr_seq', $project_seq);
			$this->db->delete($temp_row->TABLE_NAME);
		}

		return $result;
	}

	/**
	* Function copy_project
	*
	* @param array $project_data Post Data.
	*
	* @return array
	*/
	function copy_project($project_data)
	{
		$target_pr_seq = $project_data['target_pr_seq'];
		$pg_seq = $project_data['select_group_seq'];

		$project_name = $project_data['copy_subject'];
		$project_startdate = $project_data['copy_startdate'];
		$project_enddate = $project_data['copy_enddate'];
		$project_description = $project_data['copy_description'];
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$this->db->set('pr_name', $project_name);
		$this->db->set('pr_startdate', $project_startdate);
		$this->db->set('pr_enddate', $project_enddate);
		$this->db->set('pr_description', $project_description);
		$this->db->set('writer', $writer);
		$this->db->set('regdate', $date);
		$this->db->set('otm_project_group_pg_seq', $pg_seq);

		$pr_ord = $this->get_ord_maxval('otm_project','otm_project_group_pg_seq',$pg_seq,'pr_ord');
		$this->db->set('pr_ord', $pr_ord);

		$this->db->insert('otm_project');
		$result = $this->db->insert_id();

		$data2 = array(
			'otm_project_pr_seq' => $result,
			'otm_member_mb_email' => $writer
		);
		$this->db->insert('otm_project_member', $data2);
		$pm_seq = $this->db->insert_id();

		$data2 = array(
			'otm_project_member_pm_seq' => $pm_seq,
			'otm_role_rp_seq' => '1'
		);
		$this->db->insert('otm_project_member_role', $data2);

		$this->copy_project_option($target_pr_seq,$result,$project_data);//copy_default_option
		return $result;
	}

	/**
	* Function project_tree_list
	*
	* @param array $node Post Data.
	*
	* @return array
	*/
	function project_tree_list($node)
	{
		$node_info = $node['node'];
		$node_info = explode('_', $node_info);

		$writer = $this->session->userdata('mb_email');
		$mb_is_admin = $this->session->userdata('mb_is_admin');
		//$mb_lang = $this->session->userdata('mb_lang');

		$pid = ($node_info[1])?$node_info[1]:0;
		$temp_arr = array();
		$i=0;
		/*
			Get Project Group List
		*/
		$this->db->from('otm_project_group');
		$this->db->where('pg_pid',$pid);
		$this->db->order_by('pg_ord asc');

		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$temp_arr[$i]['id'] = 'PG_'.$temp_row->pg_seq;
			$temp_arr[$i]['text'] = $temp_row->pg_name;
			$temp_arr[$i]['seq'] = $temp_row->pg_seq;

			$temp_arr[$i]['type'] = 'group';
			$temp_arr[$i]['leaf'] = FALSE;
			$temp_arr[$i]['expanded'] = TRUE;

			$i++;
		}

		/*
			Get Project List
		*/
		$this->db->select('a.pr_seq,a.pr_name');
		$this->db->from('otm_project as a');

		if($mb_is_admin !== 'Y'){
			$this->db->join('otm_project_member as b','a.pr_seq=b.otm_project_pr_seq','left');
			$this->db->where('b.otm_member_mb_email',$writer);
		}
		$this->db->where('otm_project_group_pg_seq',$pid);

		$this->db->order_by('pr_ord asc');
		$this->db->order_by('pr_seq desc');
		//$this->db->order_by('pr_ord asc');

		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$temp_arr[$i]['id'] = $temp_row->pr_seq;
			$temp_arr[$i]['text'] = $temp_row->pr_name;
			$temp_arr[$i]['seq'] = $temp_row->pr_seq;

			$temp_arr[$i]['type'] = 'project';
			$temp_arr[$i]['iconCls'] = 'ico-project';
			$temp_arr[$i]['leaf'] = FALSE;
			//$temp_arr[$i]['expanded'] = TRUE;

			$i++;
		}

		if($node_info[0] !== 'root' && $node_info[0] !== 'PG' && $node_info[0] !== 'setup'){
			//그룹이 아닐 경우 프로젝트 또는 프로젝트 설정
			$data = array(
				'pr_seq' => $node_info[0],
				'pc_category' => ''
			);
			$this->load->model('project_setup_m');
			$testcase_role = $this->project_setup_m->user_project_role($data);
			for($i=0; $i<count($testcase_role); $i++){
				if($testcase_role[$i]->pmi_value === '1'){
					$role[$testcase_role[$i]->pmi_name] = true;
				}
			}

			$this->load->model('plugin_m');
			$plugin_list = $this->plugin_m->plugin_list();
			$plusins = $plugin_list['migrations'];



			$temp_arr1 = array();
			for($i=0; $i<count($plusins); $i++)
			{

				if($plusins[$i]['ishidden'] == 'true'){
					continue;
				}

				$plusins[$i]['qtip'] = '';
				if($plusins[$i]['migration_ver'] > 0 && !$plusins[$i]['current_ver']){
					//설치되지 않은 플러그인은 나오지 않도록 함.
					continue;
				}else if($plusins[$i]['migration_ver'] > 0 && $plusins[$i]['current_ver'] > 0 && $plusins[$i]['migration_ver'] != $plusins[$i]['current_ver']){
					//새버전이 있을 경우
					//'qtip', 'The tip text'
					//$plusins[$i]['qtip'] = $plusins[$i]['version_info'];
				}


				$leaf = ($plusins[$i]['subpage'] == 'true' || $plusins[$i]['subpage'] == 'TRUE')?FALSE:TRUE;

				$check_role = true;
				switch($plusins[$i]['service_id'])
				{
					case 'report':
						if(isset($role['report_view']) || $this->session->userdata('mb_is_admin') === 'Y'){
						}else{
							$check_role = false;
						}
						break;
					case 'tracking':
						if((isset($role['defect_view_all']) && isset($role['tc_view_all'])) || $this->session->userdata('mb_is_admin') === 'Y'){
						}else{
							$check_role = false;
						}
						break;
					default:
						break;
				}

				if($check_role){
					array_push($temp_arr1,array('text'=>$plusins[$i]['name'], 'pr_seq'=>$node_info[0], 'id'=>$plusins[$i]['service_id'].'_'.$node_info[0], 'type'=>$plusins[$i]['service_id'], 'iconCls'=>$plusins[$i]['iconcls'],'leaf'=>$leaf,'qtip'=>$plusins[$i]['qtip']));
				}
			}

			if(isset($role['project_edit']) || $this->session->userdata('mb_is_admin') === 'Y'){
				array_push($temp_arr1,array('text'=>lang('project').lang('setup'), 'pr_seq'=>$node_info[0], 'id'=>'setup_'.$node_info[0], 'type'=>'project_setup_main', 'iconCls'=>'ico-setup','leaf'=>FALSE));
			}
			return $temp_arr1;

		}else if($node_info[0] === 'setup'){
			//프로젝트 설정
			$temp_arr1 = array(
				//'프로젝트 사용자'
				array('text'=>lang('project').' '.lang('user'), 'id'=>'setup_user_'.$node_info[1], 'pr_seq'=>$node_info[1], 'type'=>'project_setup_user', 'iconCls'=>'ico-group', 'leaf'=>TRUE),
				//'프로젝트 아이디 체계 관리'
				array('text'=>lang('project').' '.lang('id_structure'), 'id'=>'setup_id_rule_'.$node_info[1], 'pr_seq'=>$node_info[1], 'type'=>'project_setup_id_rule', 'iconCls'=>'', 'leaf'=>TRUE),
				//'프로젝트 코드'
				array('text'=>lang('project').' '.lang('code'), 'id'=>'setup_code_'.$node_info[1], 'pr_seq'=>$node_info[1], 'type'=>'project_setup_code', 'iconCls'=>'ico-code', 'leaf'=>TRUE),
				//'프로젝트 사용자 정의 양식'
				array('text'=>lang('project').' '.lang('user_defined_form'), 'id'=>'setup_userform_'.$node_info[1], 'pr_seq'=>$node_info[1], 'type'=>'project_setup_userform', 'iconCls'=>'ico-userform', 'leaf'=>TRUE)
			);

			if ($this->db->table_exists('otm_defect_workflow')) {
				//'프로젝트 결함 수명 주기'
				array_push($temp_arr1,array('text'=>lang('project').' '.lang('defect').' '.lang('lifecycle'), 'id'=>'setup_workflow_'.$node_info[1], 'pr_seq'=>$node_info[1], 'type'=>'project_setup_workflow', 'iconCls'=>'ico-workflow', 'leaf'=>TRUE));
			}
			//Project Notification
			//array_push($temp_arr1,array('text'=>'프로젝트 알림', 'id'=>'setup_notification_'.$node_info[1], 'pr_seq'=>$node_info[1], 'type'=>'project_setup_notification', 'iconCls'=>'ico-notification', 'leaf'=>TRUE));

			return $temp_arr1;
		}

		return $temp_arr;
	}

	/**
	* Function permission_list
	*
	* @return array
	*/
	function permission_list()
	{
		//$temp_arr = array();

		$this->db->select('pmi_category,pmi_name,pmi_value');
		$this->db->from('otm_role_permission');
		$this->db->order_by('pmi_seq asc');

		$query = $this->db->get();
		return $query->result();
	}

	/**
	* Function export
	*
	* @return array
	*/
	function export($data)
	{
		if(isset($data['function'])){
			switch($data['function'])
			{
				case 'project_list_export':
					return $this->project_list_export($data);
					break;
			}
		}
	}

	/**
	* Function project_list_export
	*
	* @return array
	*/
	function project_list_export($data)
	{

		//$temp_arr = array();
		$writer = $this->session->userdata('mb_email');
		$mb_is_admin = $this->session->userdata('mb_is_admin');

		$start = (isset($data['start']))?$data['start']:null;
		$limit = (isset($data['limit']))?$data['limit']:null;
		/*if($start != null && $limit != null){
			$limitSql = " limit $limit OFFSET $start ";
		}else{
			$limitSql = "";
		}*/

		/**
			Get Project Defect Data
		*/
		$p_df = array();
		$this->db->select('a.otm_project_pr_seq, otm_defect_df_seq, dc_current_status_co_seq,pco_name,pco_seq, count(*) as defect_cnt_close');
		$this->db->from('otm_defect as a');
		$this->db->join('otm_defect_assign as b','a.df_seq=b.otm_defect_df_seq','');
		$this->db->join("(select * from otm_project_code where pco_type='status' and pco_is_required='Y') as b",'b.dc_current_status_co_seq=b.pco_seq','');
		$this->db->group_by('a.otm_project_pr_seq');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$p_df[$row->otm_project_pr_seq] = $row->defect_cnt_close;
		}
		/**
			End : Get Project Defect Data
		*/


		$this->db->select('a.pr_seq, a.pr_name,a.pr_description,a.pr_startdate,a.pr_enddate,
					(select mb_name from otm_member where a.writer=otm_member.mb_email) as writer_name,
					(select count(*) from otm_project_member where otm_project_pr_seq=a.pr_seq) as user_cnt,
					(select count(*) from otm_defect where otm_project_pr_seq=a.pr_seq) as defect_cnt,
					a.regdate');
		$this->db->from('otm_project as a');

		if($mb_is_admin !== 'Y'){
			$this->db->join('otm_project_member as b','a.pr_seq=b.otm_project_pr_seq','left');
			$this->db->where('b.otm_member_mb_email',$writer);
		}

		//$this->db->join('otm_project_group as g','g.pg_seq=a.otm_project_group_pg_seq','left');
		//$this->db->order_by('pg_ord asc');

		$this->db->order_by('otm_project_group_pg_seq asc');
		$this->db->order_by('pr_ord asc');
		$this->db->order_by('pr_seq desc');

		$this->db->limit($limit,$start); // limit $start, $limit

		$query = $this->db->get();
		$project_list = array();
		foreach ($query->result() as $row)
		{
			$tmp_arr = array();

			$row->pr_startdate = (substr($row->pr_startdate, 0, 10)!=='0000-00-00')?substr($row->pr_startdate, 0, 10):'';
			$row->pr_enddate = (substr($row->pr_enddate, 0, 10)!=='0000-00-00')?substr($row->pr_enddate, 0, 10):'';
			$row->regdate = (substr($row->regdate, 0, 10)!=='0000-00-00')?substr($row->regdate, 0, 10):'';
			$row->defect_cnt_close = $p_df[$row->pr_seq];

			$defect_rate = ($row->defect_cnt > 0)?round(($row->defect_cnt_close/$row->defect_cnt) * 100): 0;

			$tmp_arr[lang('project').lang('name')] = $row->pr_name;
			$tmp_arr[lang('description')] = $row->pr_description;
			$tmp_arr[lang('defect').lang('count')] = $row->defect_cnt;
			$tmp_arr[lang('close_defect').lang('count')] = $row->defect_cnt_close;

			$tmp_arr[lang('close_defect').'/'.lang('defect')] = $defect_rate.'%';

			$tmp_arr[lang('member')] = $row->user_cnt;
			$tmp_arr[lang('start_date')] = $row->pr_startdate;
			$tmp_arr[lang('end_date')] = $row->pr_enddate;
			$tmp_arr[lang('project').lang('writer')] = $row->writer_name;
			$tmp_arr[lang('regdate')] = $row->regdate;

			$project_list[] = $tmp_arr;
		}
		return $project_list;
	}

	/**
	* Function otm_database_backup
	*
	* @return array
	*/
	function otm_database_backup()
	{
		$host		= $this->db->hostname;
		$user		= $this->db->username;
		$pw			= $this->db->password;
		$dbname		= $this->db->database;

		$backup_url = './uploads/db_backup/';
		$backup_file_name = $dbname .'_'. date("YmdHis").'.dmp';

		$backup_file = $backup_url.$backup_file_name;
		$command = "mysqldump --opt -h $host -u $user -p$pw" . " $dbname > $backup_file  2>&1";

		system($command,$retval);
		if(is_file($backup_file)){
			$msg = "Success Database Backup!<br><br>- URL : ".$backup_url."<br>- File Name: ".$backup_file_name;
			return '{success:true, data:"'.$msg.'"}';
		}else{
			return '{success:false, data:"Database Backup Fail."}';
		}
	}


	/*********************************
	*
	*	OTestManager Project
	*
	***********************************/
	/**
	* Function get_ord_maxval
	*
	* @return double
	*/
	private function get_ord_maxval($table,$where_key,$where_vlaue, $ord_key)
	{
		if($where_vlaue && $where_vlaue != ''){
			$str_sql = "select max($ord_key) as max_ord from $table where $where_key = '$where_vlaue'";
		}else{
			$str_sql = "select max($ord_key) as max_ord from $table ";
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
	* Function project_dashboard_list
	*
	* @param array $node Post Data.
	*
	* @return array
	*/
	function project_dashboard_list($node)
	{
		if($node['node'] === 'root'){
			$root = array();
			$root[0]['id'] = 'root_0';
			$root[0]['text'] = 'Group';
			$root[0]['seq'] = 0;
			$root[0]['type'] = 'group';
			$root[0]['leaf'] = FALSE;
			$root[0]['expanded'] = TRUE;
			$root[0]['draggable'] = FALSE;

			$root[0]['pr_startdate'] = '';
			$root[0]['pr_enddate'] = '';
			$root[0]['writer_name'] = '';
			$root[0]['user_cnt'] = '';
			$root[0]['defect_cnt'] = '';
			$root[0]['defect_cnt_close'] = '';

			$root[0]['writer_name'] = '';
			$root[0]['writer'] = '';
			$root[0]['regdate'] = '';
			$root[0]['last_writer'] = '';
			$root[0]['last_update'] = '';

			//return $root;
		}
		//if($node['node'] === 'Group') $node['node'] = 'root';

		$node_info = $node['node'];
		$node_info = explode('_', $node_info);
		$pid = ($node_info[1])?$node_info[1]:0;

		$writer = $this->session->userdata('mb_email');
		$mb_is_admin = $this->session->userdata('mb_is_admin');
		//$mb_lang = $this->session->userdata('mb_lang');

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
			Get Project Defect Data
		*/
		$p_df = array();
		$this->db->select('a.otm_project_pr_seq, otm_defect_df_seq, dc_current_status_co_seq,pco_name,pco_seq, count(*) as defect_cnt_close');
		$this->db->from('otm_defect as a');
		$this->db->join('otm_defect_assign as b','a.df_seq=b.otm_defect_df_seq','');
		$this->db->join("(select * from otm_project_code where pco_type='status' and pco_is_required='Y') as b",'b.dc_current_status_co_seq=b.pco_seq','');
		$this->db->group_by('a.otm_project_pr_seq');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$p_df[$row->otm_project_pr_seq] = $row->defect_cnt_close;
		}
		/**
			End : Get Project Defect Data
		*/


		$temp_arr = array();
		$i=0;

		//if($node_info[0] === 'root'){
			/*
				Get Project Group List
			*/
			$this->db->from('otm_project_group');
			$this->db->where('pg_pid',$pid);
			$this->db->order_by('pg_ord asc');

			$query = $this->db->get();
			foreach ($query->result() as $temp_row)
			{
				$temp_arr[$i]['id'] = 'PG_'.$temp_row->pg_seq;
				$temp_arr[$i]['text'] = $temp_row->pg_name;
				$temp_arr[$i]['seq'] = $temp_row->pg_seq;

				$temp_arr[$i]['pr_startdate'] = '';
				$temp_arr[$i]['pr_enddate'] = '';
				$temp_arr[$i]['writer_name'] = '';
				$temp_arr[$i]['user_cnt'] = '';
				$temp_arr[$i]['defect_cnt'] = '';
				$temp_arr[$i]['defect_cnt_close'] = '';

				$temp_arr[$i]['writer_name'] = $member_list[$temp_row->writer];
				$temp_arr[$i]['writer'] = $temp_row->writer;
				$temp_arr[$i]['regdate'] = $temp_row->regdate;
				$temp_arr[$i]['last_writer'] = $temp_row->last_writer;
				$temp_arr[$i]['last_update'] = $temp_row->last_update;

				$temp_arr[$i]['type'] = 'group';
				//$temp_arr[$i]['iconCls'] = 'ico-project';
				$temp_arr[$i]['leaf'] = FALSE;
				$temp_arr[$i]['expanded'] = FALSE;

				$i++;
			}

			if($node['return_type'] === 'group'){
				return $temp_arr;
			}

			/*
				Get Project List
			*/
			$this->db->select('a.pr_seq,a.pr_name,a.pr_startdate,a.pr_enddate,
				a.writer,
				(select count(*) from otm_project_member where otm_project_pr_seq=a.pr_seq) as user_cnt,
				(select count(*) from otm_defect where otm_project_pr_seq=a.pr_seq) as defect_cnt');
			$this->db->from('otm_project as a');

			if($mb_is_admin !== 'Y'){
				$this->db->join('otm_project_member as b','a.pr_seq=b.otm_project_pr_seq','left');
				$this->db->where('b.otm_member_mb_email',$writer);
			}
			$this->db->where('otm_project_group_pg_seq',$pid);

			$this->db->order_by('pr_ord asc');
			$this->db->order_by('pr_seq desc');

			$query = $this->db->get();
			foreach ($query->result() as $temp_row)
			{
				$temp_arr[$i]['id'] = $temp_row->pr_seq;
				$temp_arr[$i]['text'] = $temp_row->pr_name;
				$temp_arr[$i]['seq'] = $temp_row->pr_seq;

				$temp_arr[$i]['pr_startdate'] = $temp_row->pr_startdate;
				$temp_arr[$i]['pr_enddate'] = $temp_row->pr_enddate;
				$temp_arr[$i]['user_cnt'] = $temp_row->user_cnt;
				$temp_arr[$i]['defect_cnt'] = $temp_row->defect_cnt;
				$temp_arr[$i]['defect_cnt_close'] = ($p_df[$temp_row->pr_seq])?$p_df[$temp_row->pr_seq]:0;

				$temp_arr[$i]['writer_name'] = $member_list[$temp_row->writer];
				$temp_arr[$i]['writer'] = $temp_row->writer;
				$temp_arr[$i]['regdate'] = $temp_row->regdate;
				$temp_arr[$i]['last_writer'] = $temp_row->last_writer;
				$temp_arr[$i]['last_update'] = $temp_row->last_update;

				$temp_arr[$i]['type'] = 'project';
				$temp_arr[$i]['iconCls'] = 'ico-project';
				$temp_arr[$i]['leaf'] = TRUE;
				//$temp_arr[$i]['expanded'] = TRUE;

				$i++;
			}
			return $temp_arr;

	}

	/**
	* Function create_project_group
	*
	* @param array $node Post Data.
	*
	* @return array
	*/
	function create_project_group($data)
	{
		$pid	= 0;

		$node = explode('_', $data['node']);
		if($node[0] === 'root'){
		}else{
			$pid	= $node[1];
		}

		$pg_name	= $data['pg_name'];

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$pg_ord = $this->get_ord_maxval("otm_project_group","pg_pid",$pid,'pg_ord');

		$this->db->set('pg_name', $pg_name);
		$this->db->set('pg_pid', $pid);
		$this->db->set('pg_ord', $pg_ord);

		$this->db->set('writer', $writer);
		$this->db->set('regdate', $date);

		$this->db->insert('otm_project_group');
		$result = $this->db->insert_id();
		return "{success:true, pg_seq:'PG_".$result."'}";
	}

	/**
	* Function update_project_group
	*
	* @param array $node Post Data.
	*
	* @return array
	*/
	function update_project_group($data)
	{
		$node = explode('_', $data['node']);
		$pg_seq	= $node[1];

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$modify_array['pg_name'] = $data['pg_name'];
		$modify_array['last_writer'] = $writer;
		$modify_array['last_update'] = $date;

		$where = array('pg_seq'=>$pg_seq);
		$this->db->update('otm_project_group', $modify_array, $where);

		return "{success:true, pg_seq:'".$data['node']."'}";
	}

	/**
	* Function delete_project_group
	*
	* @param array $node Post Data.
	*
	* @return array
	*/
	function delete_project_group($data)
	{
		$node = explode('_', $data['node']);
		$pg_seq	= $node[1];


		$this->db->where('pg_seq', $pg_seq);
		$this->db->delete('otm_project_group');
		return "{success:true}";
	}

	/**
	* Function move_project_group
	*
	* @param array $node Post Data.
	*
	* @return array
	*/
	function move_project_group($data)
	{
		if(isset($data['target_id']) && $data['target_id'] === 'root'){
			$data['target_id'] = 0;
		}else{
			$target_node = explode('_', $data['target_id']);
			if(isset($target_node[1])){
				$data['target_id'] = $target_node[1];
			}else{
				$data['target_id'] = $target_node[0];
			}
		}

		$target_id = $data['target_id'];
		$position = $data['position'];

		$select_item = explode('_',$data['select_id'][0]);
		$select_item_type = $select_item[0];

		if($select_item_type == 'PG'){
			//Group
			if($position == "before" || $position == "after"){
				$this->db->from('otm_project_group');
				$this->db->where('pg_seq',$target_id);

				$query = $this->db->get();
				$result = $query->result();
				$pg_pid = $result[0]->pg_pid;
				$pg_ord = $result[0]->pg_ord;

				$modify_array['pg_pid'] = $pg_pid;

				if($position == "before"){
					$str_sql = "update otm_project_group set pg_ord=pg_ord+1 where pg_pid='$pg_pid' and pg_ord>='$pg_ord'";
				}else if($position=="after"){
					$str_sql = "update otm_project_group set pg_ord=pg_ord+1 where pg_pid='$pg_pid' and pg_ord>'$pg_ord'";
					$pg_ord++;
				}
				$query = $this->db->query($str_sql);

			}else{
				$modify_array['pg_pid'] = $target_id;
			}

			if($position == "before" || $position == "after"){
				$modify_array['pg_ord'] = $pg_ord;
			}else{
				$modify_array['pg_ord'] = $this->get_ord_maxval("otm_project_group","pg_pid",$target_id,'pg_ord');
			}

			$where = array('pg_seq'=>$select_item[1]);
			$this->db->update('otm_project_group', $modify_array, $where);

		}else{
			if($position == "before" || $position == "after"){
				if(isset($target_node[1])){
					//target is group.
				}else{
					//target is project.
					$this->db->from('otm_project');
					$this->db->where('pr_seq',$target_id);

					$query = $this->db->get();
					$result = $query->result();
					$otm_project_group_pg_seq = $result[0]->otm_project_group_pg_seq;
					$pr_ord = $result[0]->pr_ord;

					$modify_array['otm_project_group_pg_seq'] = $otm_project_group_pg_seq;

					if($position == "before"){
						$str_sql = "update otm_project set pr_ord=pr_ord+1 where otm_project_group_pg_seq='$otm_project_group_pg_seq' and pr_ord>='$pr_ord'";
					}else if($position=="after"){
						$str_sql = "update otm_project set pr_ord=pr_ord+1 where otm_project_group_pg_seq='$otm_project_group_pg_seq' and pr_ord>'$pr_ord'";
						$pr_ord++;
					}
					$query = $this->db->query($str_sql);
				}

			}else{
				$modify_array['otm_project_group_pg_seq'] = $target_id;
			}

			if($position == "before" || $position == "after"){
				$modify_array['pr_ord'] = $pr_ord;
			}else{
				$modify_array['pr_ord'] = $this->get_ord_maxval("otm_project","otm_project_group_pg_seq",$target_id,'pr_ord');
			}

			$where = array('pr_seq'=>$select_item[0]);
			$this->db->update('otm_project', $modify_array, $where);


		}

		return 'ok';
	}



	/**
	* Function move_project_group
	*
	* @param array $node Post Data.
	*
	* @return array
	*/
	function user_project_include_info($node)
	{
		$mb_email = $node['mb_email'];
		if($node['node'] === 'root'){
			$root = array();
			$root[0]['id'] = 'root_0';
			$root[0]['text'] = 'Group';
			$root[0]['seq'] = 0;
			$root[0]['type'] = 'group';
			$root[0]['leaf'] = FALSE;
			$root[0]['expanded'] = TRUE;
			$root[0]['draggable'] = FALSE;
		}

		$node_info = $node['node'];
		$node_info = explode('_', $node_info);
		$pid = ($node_info[1])?$node_info[1]:0;

		//$writer = $this->session->userdata('mb_email');
		//$mb_is_admin = $this->session->userdata('mb_is_admin');
		//$mb_lang = $this->session->userdata('mb_lang');

		$temp_arr = array();
		$i=0;

		$this->db->from('otm_project_group');
		$this->db->where('pg_pid',$pid);
		$this->db->order_by('pg_ord asc');

		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$temp_arr[$i]['id'] = 'PG_'.$temp_row->pg_seq;
			$temp_arr[$i]['text'] = $temp_row->pg_name;
			$temp_arr[$i]['seq'] = $temp_row->pg_seq;

			$temp_arr[$i]['type'] = 'group';
			$temp_arr[$i]['leaf'] = FALSE;
			$temp_arr[$i]['expanded'] = TRUE;

			$i++;
		}

		if($node['return_type'] === 'group'){
			return $temp_arr;
		}

		$str_sql = "
			select
				a.pr_seq,a.pr_name,b.rp_name,b.pm_seq,b.rp_seq
			from
			(
				select
					pr_seq,pr_name
				from
					otm_project as a
				where
					otm_project_group_pg_seq='$pid'
				order by pr_ord asc,pr_seq desc
			)as a
			left outer join
			(
				select
					a.pr_seq,
					group_concat(orole.rp_seq) as rp_seq,
					group_concat(orole.rp_name) as rp_name,opm.pm_seq
				from
					otm_project as a,otm_project_member as opm, otm_role as orole, otm_project_member_role opmr
				where
				a.otm_project_group_pg_seq='$pid' and
				opm.otm_project_pr_seq=a.pr_seq and
				opm.otm_member_mb_email='$mb_email' and
				opm.pm_seq=opmr.otm_project_member_pm_seq and
				opmr.otm_role_rp_seq = orole.rp_seq
				group by a.pr_seq,opm.otm_member_mb_email
			)as b
			on
			a.pr_seq=b.pr_seq
		";

		$query = $this->db->query($str_sql);
		foreach ($query->result() as $temp_row)
		{
			$temp_arr[$i]['id'] = $temp_row->pr_seq;
			$temp_arr[$i]['text'] = $temp_row->pr_name;
			$temp_arr[$i]['seq'] = $temp_row->pr_seq;
			$temp_arr[$i]['rp_name'] = $temp_row->rp_name;
			$temp_arr[$i]['pm_seq'] = $temp_row->pm_seq;
			$temp_arr[$i]['rp_seq'] = $temp_row->rp_seq;

			$temp_arr[$i]['mb_email'] = $mb_email?true:false;//$temp_row->rp_name;

			$temp_arr[$i]['type'] = 'project';
			$temp_arr[$i]['iconCls'] = 'ico-project';
			//$temp_arr[$i]['checked'] = TRUE;
			$temp_arr[$i]['leaf'] = TRUE;

			$i++;
		}
		return $temp_arr;
	}

}
//End of file Otm_m.php
//Location: ./models/Otm_m.php
