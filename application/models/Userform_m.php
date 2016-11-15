<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Userform_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Userform_m extends CI_Model
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
	* Function userform_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function userform_list($data)
	{
		$temp_arr = array();

		//$pr_seq = $data['pr_seq'];

		$this->db->select('cf_seq,cf_name,cf_category,cf_is_required,cf_is_display,cf_formtype,cf_default_value,cf_content,otm_customform.regdate,otm_member.mb_name as writer');
		$this->db->from('otm_customform');
		$this->db->join('otm_member', 'otm_member.mb_email=otm_customform.writer');

		if(isset($data['cf_category']))
		{
			$this->db->where('cf_category', $data['cf_category']);
		}else{
			$this->db->where('cf_category !=', 'TC_ITEM');
		}

		$this->db->order_by('cf_category', 'desc');
		$this->db->order_by('ABS(cf_1)', 'asc');
		$this->db->order_by('cf_seq', 'asc');

		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$formtype = $temp_row->cf_formtype;
			if($formtype === 'combo' || $formtype === 'checkbox' || $formtype === 'radio'){
				$option_arr = json_decode($temp_row->cf_content);
				for($i=0; $i<count($option_arr); $i++){
					if($option_arr[$i]->is_required === 'Y'){
						$temp_row->cf_default_value = $option_arr[$i]->name;
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
		$this->db->insert('otm_customform', $data);
		$result = $this->db->insert_id();

		$data2 = array('cf_1' => $result*10000);
		$this->db->where('cf_seq', $result);
		$this->db->update('otm_customform', $data2);
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
			'cf_category' => $data['cf_category'],
			'cf_formtype' => $data['cf_formtype'],
			'cf_name' => $data['cf_name'],
			'cf_is_required' => $data['cf_is_required'],
			'cf_is_display' => $data['cf_is_display'],
			'cf_default_value' => $data['cf_default_value'],
			'cf_content' => $data['cf_content'],
			'last_writer' => $data['last_writer'],
			'last_update' => $data['last_update']
		);

		$this->db->where('cf_seq', $data['cf_seq']);
		$this->db->update('otm_customform', $data2);
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
		for($i=0; $i<count($data['cf_list']); $i++){
			$this->db->where('cf_seq', $data['cf_list'][$i]);
			$this->db->delete('otm_customform');
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

		$this->db->select('cf_content');
		$this->db->from('otm_customform');
		$this->db->where('cf_seq', $data['cf_seq']);

		$query = $this->db->get();

		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}
		$cf_content = $temp_arr[0]->cf_content;
		$cf_content = str_replace('"N"', 'false', $cf_content);
		$cf_content = str_replace('"Y"', 'true', $cf_content);
		return $cf_content;
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
			$cf_seq = $list[$i];
			$str_query = "update otm_customform set cf_1='$i' where cf_seq='{$cf_seq}'";

			$this->db->query($str_query);
		}
		return '{success:true}';
	}
}
//End of file Userform_m.php
//Location: ./models/Userform_m.php