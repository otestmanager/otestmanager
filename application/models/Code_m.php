<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Code_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Code_m extends CI_Model
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
	* Function code_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function code_list($data)
	{
		$temp_arr = array();

		$this->db->where('co_type', $data['type']);
		$this->db->order_by('ABS(co_position), co_seq asc');
		$query = $this->db->get('otm_code');
		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}
		return $temp_arr;
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
		//$rp_seq = trim($data['rp_seq']);
		$rp_type = trim($data['type']);

		$value_field	= array();

		$str_sql = "select co_seq from otm_code where co_type='{$rp_type}' order by co_position,co_seq asc";
		$query = $this->db->query($str_sql);
		$i=0;
		foreach ($query->result() as $query_rows)
		{
			$value_field[$i]->value_field = '_'.$query_rows->co_seq;
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
		$rp_seq = $data['rp_seq'];
		$rp_type = $data['type'];

		if($rp_seq){
			$code_arr = array();
			$workflow_arr = array();
			$str_query = "select co_seq,co_type,co_name,co_is_required,co_is_default from otm_code where co_type='{$rp_type}' order by co_position,co_seq asc";
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

			$str_query = "select otm_code_co_seq_from as seq_from,otm_code_co_seq_to as seq_to,dw_value from otm_defect_workflow where otm_role_rp_seq='{$rp_seq}'";
			$query = $this->db->query($str_query);
			foreach ($query->result() as $query_rows){
				$workflow_arr[] = $query_rows;
			}

			for($i=0;$i<sizeof($code_arr);$i++){
				for($j=0;$j<sizeof($workflow_arr);$j++){
					if($code_arr[$i]->co_seq === $workflow_arr[$j]->seq_from){
						$tmp_var = '_'.$workflow_arr[$j]->seq_to;
						$code_arr[$i]->$tmp_var = $workflow_arr[$j]->dw_value;
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
			$str_query = "select co_seq,co_type,co_name,co_is_required,co_is_default $quy_field from otm_code where co_type='$rp_type' order by co_position,co_seq asc";
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
			$rp_seq		= $defect_workflow[$i]->rp_seq;
			$co_from	= $defect_workflow[$i]->co_seq_from;
			$co_to		= $defect_workflow[$i]->co_seq_to;
			$dw_value	= $defect_workflow[$i]->dw_value;

			$str_query = "select count(dw_seq) as cnt from otm_defect_workflow where otm_role_rp_seq='{$rp_seq}' and otm_code_co_seq_from='{$co_from}' and otm_code_co_seq_to='{$co_to}'";
			$query = $this->db->query($str_query);
			$result = $query->result();

			if($result[0]->cnt >= 1){
				$str_query = "update otm_defect_workflow set dw_value='{$dw_value}' where otm_role_rp_seq='{$rp_seq}' and otm_code_co_seq_from='{$co_from}' and otm_code_co_seq_to='{$co_to}'";
			}
			else{
				$str_query = "insert into otm_defect_workflow(otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values ('{$rp_seq}','{$co_from}','{$co_to}','{$dw_value}')";
			}
			$this->db->query($str_query);
		}
		return '{success:true}';
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
		if($data['co_is_required'] === 'Y'){
			$str_sql = "update otm_code set co_is_required='N' where co_type='".$data['co_type']."'";
			$this->db->query($str_sql);
		}
		if($data['co_is_default'] === 'Y'){
			$str_sql = "update otm_code set co_is_default='N' where co_type='".$data['co_type']."'";
			$this->db->query($str_sql);
		}

		$this->db->insert('otm_code', $data);
		
		return 'ok';
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
		if($data['co_is_required'] === 'Y'){
			$str_sql = "update otm_code set co_is_required='N' where co_type='".$data['co_type']."'";
			$this->db->query($str_sql);
		}
		if($data['co_is_default'] === 'Y'){
			$str_sql = "update otm_code set co_is_default='N' where co_type='".$data['co_type']."'";
			$this->db->query($str_sql);
		}

		$data2 = array(
			'co_name' => $data['co_name'],
			'co_is_required' => $data['co_is_required'],
			'co_is_default' => $data['co_is_default'],
			'co_position' => $data['co_position'],
			'co_default_value' => $data['co_default_value'],
			'co_color' => $data['co_color']
		);

		$this->db->where('co_seq', $data['co_seq']);
		$this->db->update('otm_code', $data2);
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
		$type = $data['co_type'];
		$list = json_decode($data['co_list']);

		for($i=0;$i<sizeof($list);$i++){
			$co_seq = $list[$i];
			$str_query = "update otm_code set co_position='$i' where co_type='{$type}' and co_seq='{$co_seq}'";

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
		for($i=0; $i<count($data['co_list']); $i++){
			$this->db->where('co_seq', $data['co_list'][$i]);
			$this->db->delete('otm_code');
		}
		return 'ok';
	}
}
//End of file Code_m.php
//Location: ./models/Code_m.php
