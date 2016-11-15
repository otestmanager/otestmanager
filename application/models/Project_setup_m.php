<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Project_setup_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Project_setup_m extends CI_Model
{
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	/**
	* Function Project Info
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function project_info($data)
	{
		$temp_arr = array();

		$this->db->where('pr_seq', $data['pr_seq']);
		$query = $this->db->get('otm_project');
		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}
		return $temp_arr;
	}

	/**
	* Function Project Update
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function project_update($data)
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$project_info_data = array(
			'pr_name' => $data['pr_name'],
			'pr_startdate' => $data['pr_startdate'],
			'pr_enddate' => $data['pr_enddate'],
			'pr_description' => $data['pr_description'],
			'last_writer' => $writer,
			'last_update' => $date
		);

		$this->db->where('pr_seq', $data['pr_seq']);
		$this->db->update('otm_project', $project_info_data);

		return 'ok';
	}

	/**
	* Project Setup : User
	*/

	/**
	* Function project_userlist
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function project_userlist($data)
	{
		$temp_arr = array();
		$project_seq = $data['pr_seq'];

		$str_sql = "
			select a.*,b.rp_name as user_role_name,b.rp_seq from
			(
				select a.*,g.gr_name as user_group_name from
				(
					select
						a.pm_seq,b.mb_email,b.mb_name,b.mb_is_admin
					from
						otm_project_member a,otm_member b
					where
						a.otm_project_pr_seq='$project_seq' and
						a.otm_member_mb_email=b.mb_email
				) as a
				left outer join
				(
					select
						group_concat(a.gr_name) as gr_name,
						b.otm_member_mb_email
					from
					otm_group as a, otm_group_member as b
					where a.gr_seq=b.otm_group_gr_seq
					group by b.otm_member_mb_email
				) as g
				on
				a.mb_email=g.otm_member_mb_email
			) as a
			left outer join
			(
				select
					group_concat(b.rp_name) as rp_name,
					group_concat(b.rp_seq) as rp_seq,
					a.otm_member_mb_email as mb_email
				from
				otm_project_member as a, otm_role as b, otm_project_member_role c
				where
				a.otm_project_pr_seq='$project_seq' and
				a.pm_seq=c.otm_project_member_pm_seq and
				c.otm_role_rp_seq = b.rp_seq
				group by a.otm_member_mb_email
			) as b
			on
			a.mb_email=b.mb_email
		";

		$query = $this->db->query($str_sql);
		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}
		if(sizeof($temp_arr) === 0){
			return '';
		}
		else{
			return $temp_arr;
		}

		exit;
	}

	/**
	* Function create_user
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_user($data)
	{
		for($i=0; $i<count($data['userlist']); $i++){
			$this->db->select('pm_seq');
			$this->db->where('otm_project_pr_seq', $data['pr_seq']);
			$this->db->where('otm_member_mb_email', $data['userlist'][$i]);
			$query = $this->db->get('otm_project_member');
			$pm_result = $query->row();

			if( ! $pm_result){
				$data2 = array(
					'otm_project_pr_seq' => $data['pr_seq'],
					'otm_member_mb_email' => $data['userlist'][$i]
				);
				$this->db->insert('otm_project_member', $data2);
				$result = $this->db->insert_id();

				for($j=0; $j<count($data['rolelist']); $j++){
					$data3 = array(
						'otm_project_member_pm_seq'	 => $result,
						'otm_role_rp_seq'=> $data['rolelist'][$j]
					);
					$this->db->insert('otm_project_member_role', $data3);
				}
			}
			else{
				for($j=0; $j<count($data['rolelist']); $j++){

					$this->db->select('otm_project_member_pm_seq');
					$this->db->where('otm_project_member_pm_seq', $pm_result->pm_seq);
					$this->db->where('otm_role_rp_seq', $data['rolelist'][$j]);
					$query = $this->db->get('otm_project_member_role');

					$result = $query->row();

					if( ! $result){
						$data3 = array(
							'otm_project_member_pm_seq'	 => $pm_result->pm_seq,
							'otm_role_rp_seq'=> $data['rolelist'][$j]
						);
						$this->db->insert('otm_project_member_role', $data3);
					}
				}
			}
		}

		return 'ok';
	}

	/**
	* Function update_user
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_user($data)
	{
		$this->db->where('otm_project_member_pm_seq', $data['pm_seq']);
		$this->db->delete('otm_project_member_role');

		for($j=0; $j<count($data['rolelist']); $j++){
			$data3 = array(
				'otm_project_member_pm_seq' => $data['pm_seq'],
				'otm_role_rp_seq'=> $data['rolelist'][$j]
			);
			$this->db->insert('otm_project_member_role', $data3);
		}

		return 'ok';
	}

	/**
	* Function delete_user
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_user($data)
	{
		$this->db->where('otm_project_member_pm_seq', $data['pm_seq']);
		$this->db->delete('otm_project_member_role');

		$this->db->where('pm_seq', $data['pm_seq']);
		$this->db->delete('otm_project_member');

		return 'ok';
	}

	/**
	* Project Setup : UserForm
	*/
	/**
	* Function userform_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function userform_list($data)
	{
		$temp_arr = array();
		$pr_seq = $data['pr_seq'];
		$pc_category = $data['pc_category'];
		$pc_is_use = $data['pc_is_use'];

		$str_where = "";

		if($data['pc_category'] != ''){
			$str_where .= " and pc_category='$pc_category'";

			if($data['pc_category'] === 'ID_TC'){
				$str_where = " and (pc_category='ID_TC' or pc_category='TC_ITEM') ";
			}
		}else{
			$str_where .= " and pc_category!='TC_ITEM'";
		}

		if($data['pc_is_use'] != ""){
			$str_where .= " and pc_is_use='$pc_is_use'";
		}

		$str_sql = "select
						pc_seq,otm_project_pr_seq,pc_name,pc_category,pc_is_required,pc_is_display,pc_formtype,pc_content,pc_default_value,pc_is_use,regdate,
						(select mb_name from otm_member where a.writer=otm_member.mb_email)as writer
					from
					otm_project_customform as a
					where
						otm_project_pr_seq='$pr_seq' $str_where
					#order by pc_category desc, pc_seq
					order by pc_category desc, ABS(pc_1) asc, pc_seq asc
		";

		$query = $this->db->query($str_sql);

		foreach ($query->result() as $temp_row)
		{
			$formtype = $temp_row->pc_formtype;
			if($formtype === 'combo' || $formtype === 'checkbox' || $formtype === 'radio'){
				$option_arr = json_decode($temp_row->pc_content);
				for($i=0; $i<count($option_arr); $i++){
					if($option_arr[$i]->is_required === 'Y'){
						$temp_row->pc_default_value = $option_arr[$i]->name;
					}
				}
			}
			$temp_arr[] = $temp_row;
		}
		return $temp_arr;
	}

	/**
	* Function create_userform
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function create_userform($data)
	{
		$this->db->insert('otm_project_customform', $data);
		$result = $this->db->insert_id();

		$data2 = array('pc_1' => $result*10000);
		$this->db->where('pc_seq', $result);
		$this->db->update('otm_project_customform', $data2);
		return 'ok';
	}

	/**
	* Function update_userform
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function update_userform($data)
	{
		$data2 = array(
			'pc_category' => $data['pc_category'],
			'pc_formtype' => $data['pc_formtype'],
			'pc_name' => $data['pc_name'],
			'pc_is_required' => $data['pc_is_required'],
			'pc_is_display' => $data['pc_is_display'],
			'pc_is_use' => $data['pc_is_use'],
			'pc_default_value' => $data['pc_default_value'],
			'pc_content' => $data['pc_content'],
			'last_writer' => $data['last_writer'],
			'last_update' => $data['last_update']
		);

		$this->db->where('pc_seq', $data['pc_seq']);
		$this->db->update('otm_project_customform', $data2);
		return 'ok';
	}

	/**
	* Function delete_userform
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function delete_userform($data)
	{
		for($i=0; $i<count($data['pc_list']); $i++){
			$this->db->where('pc_seq', $data['pc_list'][$i]);
			$this->db->delete('otm_project_customform');
		}
		return 'ok';
	}

	/**
	* Function option_list
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function option_list($data)
	{
		$temp_arr = array();

		$this->db->select('pc_content');
		$this->db->from('otm_project_customform');
		$this->db->where('pc_seq', $data['pc_seq']);
		$query = $this->db->get();
		foreach ($query->result() as $temp_row){
			$temp_arr[] = $temp_row;
		}
		$pc_content = $temp_arr[0]->pc_content;
		$pc_content = str_replace('"N"', 'false', $pc_content);
		$pc_content = str_replace('"Y"', 'true', $pc_content);
		return $pc_content;
	}

	/**
	* Function update_sort_list
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function update_sort_list($data)
	{
		$list = json_decode($data['userform_list']);

		for($i=0;$i<sizeof($list);$i++){
			$pc_seq = $list[$i];
			$str_query = "update otm_project_customform set pc_1='$i' where pc_seq='{$pc_seq}'";

			$this->db->query($str_query);
		}
		return '{success:true}';
	}


	/**
		Project Setup : Code
	*/
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
		$pr_seq = $data['otm_project_pr_seq'];
		$pco_type = $data['pco_type'];
		$pco_name = $data['pco_name'];

		$duplicate = $this->is_code_duplicate($pr_seq,$pco_type,$pco_name);

		if($duplicate){
			return 'Duplication Code';
		}else{
			if($data['pco_is_required'] === 'Y'){
				$str_sql = "update otm_project_code set pco_is_required='N' where otm_project_pr_seq='".$data['otm_project_pr_seq']."' and pco_type='".$data['pco_type']."'";
				$this->db->query($str_sql);
			}
			if($data['pco_is_default'] === 'Y'){
				$str_sql = "update otm_project_code set pco_is_default='N' where otm_project_pr_seq='".$data['otm_project_pr_seq']."' and pco_type='".$data['pco_type']."'";
				$this->db->query($str_sql);
			}

			$this->db->insert('otm_project_code', $data);
			//$result = $this->db->insert_id();
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
		$pr_seq = $data['otm_project_pr_seq'];
		$pco_type = $data['pco_type'];
		$pco_name = $data['pco_name'];
		$pco_seq = $data['pco_seq'];

		$duplicate = $this->is_code_duplicate($pr_seq,$pco_type,$pco_name,$pco_seq);

		if($duplicate){
			return 'Duplication Code';
		}else{
			if($data['pco_is_required'] === 'Y'){
				$str_sql = "update otm_project_code set pco_is_required='N' where otm_project_pr_seq='".$data['otm_project_pr_seq']."' and pco_type='".$data['pco_type']."'";
				$this->db->query($str_sql);
			}
			if($data['pco_is_default'] === 'Y'){
				$str_sql = "update otm_project_code set pco_is_default='N' where otm_project_pr_seq='".$data['otm_project_pr_seq']."' and pco_type='".$data['pco_type']."'";
				$this->db->query($str_sql);
			}

			$data2 = array(
				'pco_name' => $data['pco_name'],
				'pco_is_required' => $data['pco_is_required'],
				'pco_is_default' => $data['pco_is_default']
			);

			$this->db->where('pco_seq', $data['pco_seq']);
			$this->db->update('otm_project_code', $data2);
			return 'ok';
		}
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
	* Function code_list_workflow_valuefield
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function code_list_workflow_valuefield($data)
	{
		$project_seq = trim($data['project_seq']);
		//$rp_seq = trim($data['rp_seq']);
		$rp_type = trim($data['type']);


		$value_field = array();

		$str_sql = "select pco_seq from otm_project_code where pco_type='{$rp_type}' and otm_project_pr_seq='{$project_seq}' order by pco_position,pco_seq asc";
		$query = $this->db->query($str_sql);
		$i=0;
		foreach ($query->result() as $query_rows)
		{
			$value_field[$i]->value_field = '_'.$query_rows->pco_seq;
			$i++;
		}
		return $value_field;
	}

	/**
	* Function code_list_workflow
	*
	* @param array $data Post Data.
	* @param array $value_info Info Data.
	*
	* @return array
	*/
	function code_list_workflow($data,$value_info)
	{
		$project_seq = $data['project_seq'];
		$rp_seq = $data['rp_seq'];
		$rp_type = $data['type'];

		if($rp_seq){
			$code_arr = array();
			$workflow_arr = array();
			$str_query = "select pco_seq,pco_type,pco_name,pco_is_required,pco_is_default from otm_project_code where pco_type='{$rp_type}' and otm_project_pr_seq='{$project_seq}' order by pco_position,pco_seq asc";
			$query = $this->db->query($str_query);
			$j=0;
			foreach ($query->result() as $query_rows){
				$code_arr[] = $query_rows;
				for($i=0;$i<sizeof($value_info);$i++){
					$tmp_var = $value_info[$i]->value_field;
					$code_arr[$j]->$tmp_var = "";
				}
				$j++;
			}


			$str_query = "select otm_project_code_pco_seq_from as seq_from,otm_project_code_pco_seq_to as seq_to,pdw_value from otm_project_defect_workflow where otm_role_rp_seq='{$rp_seq}' and otm_project_pr_seq='{$project_seq}'";
			$query = $this->db->query($str_query);
			foreach ($query->result() as $query_rows){
				$workflow_arr[] = $query_rows;
			}


			for($i=0;$i<sizeof($code_arr);$i++){
				for($j=0;$j<sizeof($workflow_arr);$j++){
					if($code_arr[$i]->pco_seq === $workflow_arr[$j]->seq_from){
						$tmp_var = '_'.$workflow_arr[$j]->seq_to;
						$code_arr[$i]->$tmp_var = $workflow_arr[$j]->pdw_value;
					}
				}
			}
			return $code_arr;
		}
		else{
			$quy_field = '';
			for($i=0;$i<sizeof($value_info);$i++){
				$quy_field .= " ,'' as ".$value_info[$i]->value_field;
			}

			$result_arr = array();
			$str_query = "select pco_seq,pco_type,pco_name,pco_is_required,pco_is_default $quy_field from otm_project_code where pco_type='$rp_type' and otm_project_pr_seq='{$project_seq}' order by pco_position,pco_seq asc";
			$query = $this->db->query($str_query);
			$i=0;
			foreach ($query->result() as $query_rows)
			{
				$result_arr[] = $query_rows;
			}
			return $result_arr;
		}
	}

	/**
	* Function update_workflow
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_workflow($data)
	{

		$defect_workflow = json_decode($data['workflow_data']);

		for($i=0;$i<sizeof($defect_workflow);$i++){
			$project_seq = $defect_workflow[$i]->project_seq;
			$rp_seq		= $defect_workflow[$i]->rp_seq;
			$pco_from	= $defect_workflow[$i]->pco_seq_from;
			$pco_to		= $defect_workflow[$i]->pco_seq_to;
			$pdw_value	= $defect_workflow[$i]->pdw_value;

			$str_query = "select count(pdw_seq) as cnt from otm_project_defect_workflow where otm_role_rp_seq='{$rp_seq}' and otm_project_code_pco_seq_from='{$pco_from}' and otm_project_code_pco_seq_to='{$pco_to}' and otm_project_pr_seq='{$project_seq}' ";
			$query = $this->db->query($str_query);
			$result = $query->result();

			if($result[0]->cnt >= 1){
				$str_query = "update otm_project_defect_workflow set pdw_value='{$pdw_value}' where otm_role_rp_seq='{$rp_seq}' and otm_project_code_pco_seq_from='{$pco_from}' and otm_project_code_pco_seq_to='{$pco_to}' and otm_project_pr_seq='{$project_seq}' ";
			}
			else{
				$str_query = "insert into otm_project_defect_workflow(otm_project_pr_seq,otm_role_rp_seq,otm_project_code_pco_seq_from,otm_project_code_pco_seq_to,pdw_value) values ('{$project_seq}','{$rp_seq}','{$pco_from}','{$pco_to}','{$pdw_value}')";
			}
			$this->db->query($str_query);
		}
		return '{success:true}';
	}

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
	* Function user_project_role
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function user_project_role($data)
	{
		$temp_arr = Array();
		$pr_seq = $data['pr_seq'];
		$pc_category = $data['pc_category'];
		$writer = $this->session->userdata('mb_email');

		$this->db->select('pmi.pmi_name,pmi.pmi_value');
		$this->db->from('otm_project_member as pm');
		$this->db->join('otm_project_member_role as pmr','pm.pm_seq=pmr.otm_project_member_pm_seq','left');
		$this->db->join('otm_role as rp','pmr.otm_role_rp_seq=rp.rp_seq','left');
		$this->db->join('otm_role_permission as pmi','rp.rp_seq=pmi.otm_role_rp_seq','left');
		$this->db->where('pm.otm_member_mb_email', $writer);

		if(isset($pc_category)){
			//$this->db->where('pmi.pmi_category', $pc_category);
		}
		if(isset($pr_seq) && $pr_seq != ''){
			$this->db->where('pm.otm_project_pr_seq', $pr_seq);
		}
		$query = $this->db->get();

		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}

		return $temp_arr;
	}

	/**
	* Function project_defect_workflow
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function project_defect_workflow($data)
	{
		$temp_arr = Array();
		if (!$this->db->table_exists('otm_project_defect_workflow')) {
			return $temp_arr;
		}
		$pr_seq = $data['pr_seq'];
		$writer = $this->session->userdata('mb_email');

		$this->db->select('c.otm_project_code_pco_seq_from as from_status,c.otm_project_code_pco_seq_to as to_status');
		$this->db->from('otm_project_member as a');
		$this->db->join('otm_project_member_role as b','a.pm_seq=b.otm_project_member_pm_seq','left');
		$this->db->join('otm_project_defect_workflow as c','b.otm_role_rp_seq=c.otm_role_rp_seq','left');
		$this->db->where('a.otm_member_mb_email', $writer);
		$this->db->where('a.otm_project_pr_seq', $pr_seq);
		$this->db->where('c.otm_project_pr_seq', $pr_seq);
		$this->db->where('c.otm_project_code_pco_seq_from !=',' c.otm_project_code_pco_seq_to');
		$this->db->where('c.pdw_value', '1');

		$this->db->group_by(array('c.otm_project_code_pco_seq_from', 'c.otm_project_code_pco_seq_to'));
		//$this->db->order_by('c.otm_project_code_pco_seq_from', 'asc');

		$query = $this->db->get();

		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}

		return $temp_arr;
	}

	/**
	* Function system_setup_tree_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function system_setup_tree_list($data)
	{
		if($data['node'] !== 'root'){
			return;
		}

		$mb_lang = $this->session->userdata('mb_lang');
		switch($mb_lang){
			case "project-ko":
			case "ko":
				$temp_arr1 = array(
					array('text'=>'사용자',				'id'=>'user',		'type'=>'user',		'leaf'=>TRUE),
					array('text'=>'그룹',				'id'=>'group',		'type'=>'group',	'leaf'=>TRUE),
					array('text'=>'역할 및 권한',		'id'=>'role',		'type'=>'role',		'leaf'=>TRUE),
					array('text'=>'아이디 체계 관리',	'id'=>'id_rule',	'type'=>'id_rule',	'leaf'=>TRUE),
					array('text'=>'코드',				'id'=>'code',		'type'=>'code',		'leaf'=>TRUE),
					array('text'=>'사용자 정의 양식',	'id'=>'userform',	'type'=>'userform',	'leaf'=>TRUE)
				);

				if ($this->db->table_exists('otm_defect_workflow')) {
					array_push($temp_arr1,array('text'=>'결함 수명 주기',	'id'=>'workflow', 'type'=>'workflow', 'leaf'=>TRUE));
				}

				//array_push($temp_arr1,array('text'=>'알림',	'id'=>'notification', 'type'=>'notification', 'leaf'=>TRUE));
				array_push($temp_arr1,array('text'=>'플러그인',	'id'=>'plugin', 'type'=>'plugin', 'leaf'=>TRUE));
			break;
			default:

				$temp_arr1 = array(
					array('text'=>'Member',				'id'=>'user',		'type'=>'user',		'leaf'=>TRUE),
					array('text'=>'Group',				'id'=>'group',		'type'=>'group',	'leaf'=>TRUE),
					array('text'=>'Role and authority', 'id'=>'role',		'type'=>'role',		'leaf'=>TRUE),
					array('text'=>'ID Structure',		'id'=>'id_rule',	'type'=>'id_rule',	'leaf'=>TRUE),
					array('text'=>'Code',				'id'=>'code',		'type'=>'code',		'leaf'=>TRUE),
					array('text'=>'User Defined Form',	'id'=>'userform',	'type'=>'userform',	'leaf'=>TRUE)
				);

				if ($this->db->table_exists('otm_defect_workflow')) {
					array_push($temp_arr1,array('text'=>'Defect Lifecycle',	'id'=>'workflow', 'type'=>'workflow', 'leaf'=>TRUE));
				}

				//array_push($temp_arr1,array('text'=>'Notification',	'id'=>'notification', 'type'=>'notification', 'leaf'=>TRUE));
				array_push($temp_arr1,array('text'=>'Plug-in',	'id'=>'plugin', 'type'=>'plugin', 'leaf'=>TRUE));
			break;
		}

		return $temp_arr1;
	}


	/**
	========================================
		Project ID Rule
	========================================
	*/

	/**
	* Function id_rule_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function id_rule_list($data)
	{
		$temp_arr = array();
		if($data['xtype'] === 'combo'){
			$temp_arr = array(array('pco_name'=>'선택 없음','pco_seq'=>''));
		}

		$this->db->where('pco_type', $data['type']);
		$this->db->where('otm_project_pr_seq', $data['pr_seq']);
		$this->db->order_by('pco_position,pco_seq asc');
		$query = $this->db->get('otm_project_code');
		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}
		return $temp_arr;
	}

	/**
	* Function check_id_rule
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function check_id_rule($data)
	{
		$temp_arr = array();

		$pco_seq = $data['pco_seq'];

		switch($data['pco_type'])
		{
			case "tc_id_rule":
				$pattern = "/,/";

				$this->db->where('pco_type', 'df_id_rule');
				$this->db->where('otm_project_pr_seq', $data['otm_project_pr_seq']);
				$query = $this->db->get('otm_project_code');
				foreach ($query->result() as $temp_row)
				{
					$temp_arr[] = $temp_row;

					$temp_default_value = $temp_row->pco_default_value;
					$rule_arr = preg_split($pattern,$temp_default_value);

					if(isset($rule_arr[0]) || $tmp_arr[0])
					{
						if($rule_arr[0] == $pco_seq){
							switch($data['action_type'])
							{
								case "delete":
									return array('check'=>false,'check_type'=>'use_df_id','check_msg'=>'삭제 하려는 테스트 케이스 ID 체계를 결함 ID에서 사용중이기 때문에 삭제 할 수 없습니다.<br>삭제 하시려면 사용중인 결함 ID 체계를 변경하세요.');
									break;
								case "update":
									return array('check'=>false,'check_type'=>'use_df_id','check_msg'=>'이전에 발급된 테스트 케이스 ID는 수정/변경 이전의 테스트케이스 ID 체계로 유지되며,<br>수정/변경 하려는 테스트 케이스 ID 체계를 결함 ID에서 사용중이기 때문에 결함 ID 체계도 동일하게 적용됩니다.<br>수정/변경된 테스트 케이스 ID 체계로 발급하시겠습니까?');
									break;
								default:
									break;
							}

						}
					}
				}

				return $data;
				break;
			case "df_id_rule":
				return $data;
				break;
		}
		return $temp_arr;
	}

	/**
	* Function create_id_rule
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function create_id_rule($data)
	{
		if($data['pco_is_required'] === 'Y'){
			$str_sql = "update otm_project_code set pco_is_required='N' where pco_type='".$data['pco_type']."'";
			$this->db->query($str_sql);
		}
		if($data['pco_is_default'] === 'Y'){
			$str_sql = "update otm_project_code set pco_is_default='N' where pco_type='".$data['pco_type']."'";
			$this->db->query($str_sql);
		}

		$this->db->insert('otm_project_code', $data);
		//$result = $this->db->insert_id();
		return 'ok';
	}

	/**
	* Function update_id_rule
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function update_id_rule($data)
	{
		if($data['check_type']){
			switch($data['check_type'])
			{
				case "use_df_id":
					//해당 TC ID 체계를 사용중인 결함 ID 체계(pco_name)를 변경해준다.

					$pattern = "/,/";
					$this->db->where('pco_type', 'df_id_rule');
					$this->db->where('otm_project_pr_seq', $data['otm_project_pr_seq']);
					$query = $this->db->get('otm_project_code');
					foreach ($query->result() as $temp_row)
					{
						$temp_arr[] = $temp_row;

						$temp_default_value = $temp_row->pco_default_value;
						$rule_arr = preg_split($pattern,$temp_default_value);

						if(isset($rule_arr[0]) || $tmp_arr[0])
						{
							if($rule_arr[0] == $data['pco_seq']){
								$string = $temp_row->pco_name;
								$new_df_id_rule_name = str_replace($data['before_name'], $data['pco_name'], $string );

								$new_df_data = array(
									'pco_name' => $new_df_id_rule_name
								);

								$this->db->where('pco_seq', $temp_row->pco_seq);
								$this->db->where('otm_project_pr_seq', $data['otm_project_pr_seq']);
								$this->db->update('otm_project_code', $new_df_data);

							}
						}
					}
					break;
			}
		}

		if($data['pco_is_required'] === 'Y'){
			$str_sql = "update otm_project_code set pco_is_required='N'
							where pco_type='".$data['pco_type']."'
							and otm_project_pr_seq = '".$data['otm_project_pr_seq']."'
						";
			$query = $this->db->query($str_sql);
		}
		if($data['pco_is_default'] === 'Y'){
			$str_sql = "update otm_project_code set pco_is_default='N'
							where pco_type='".$data['pco_type']."'
							and otm_project_pr_seq = '".$data['otm_project_pr_seq']."'
						";
			$query = $this->db->query($str_sql);
		}

		$data2 = array(
			'pco_name' => $data['pco_name'],
			'pco_is_required' => $data['pco_is_required'],
			'pco_is_default' => $data['pco_is_default'],
			'pco_position' => $data['pco_position'],
			'pco_default_value' => $data['pco_default_value'],
			'pco_color' => $data['pco_color']
		);

		$this->db->where('pco_seq', $data['pco_seq']);
		$this->db->where('otm_project_pr_seq', $data['otm_project_pr_seq']);
		$this->db->update('otm_project_code', $data2);

		return 'ok';
	}

	/**
	* Function delete_id_rule
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function delete_id_rule($data)
	{
		for($i=0; $i<count($data['pco_list']); $i++){
			$data['action_type']= 'delete';
			$data['pco_seq']	= $data['pco_list'][$i];
			$data['check']		= true;
			$check_rule = $this->check_id_rule($data);

			if($check_rule['check_msg'] !== '' && $check_rule['check'] == false){
				return json_encode($check_rule);
			}else{
				$this->db->where('pco_seq', $data['pco_list'][$i]);
				$this->db->delete('otm_project_code');
			}
		}
		return 'ok';
	}

}
//End of file Project_setup_m.php
//Location: ./models/Project_setup_m.php