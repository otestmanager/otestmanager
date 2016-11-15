<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Id_rule_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Id_rule_m extends CI_Model
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
			$temp_arr = array(array('co_name'=>'No Selected','co_seq'=>''));
		}

		$this->db->where('co_type', $data['type']);
		$this->db->order_by('co_position,co_seq asc');
		$query = $this->db->get('otm_code');
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
		return $data;

		$temp_arr = array();
		$co_seq = $data['co_seq'];
		switch($data['co_type'])
		{
			case "tc_id_rule":
				$pattern = "/,/";

				$this->db->where('co_type', 'df_id_rule');
				$query = $this->db->get('otm_code');
				foreach ($query->result() as $temp_row)
				{
					$temp_arr[] = $temp_row;

					$temp_default_value = $temp_row->co_default_value;
					$rule_arr = preg_split($pattern,$temp_default_value);

					if(isset($rule_arr[0]) || $tmp_arr[0])
					{
						if($rule_arr[0] == $co_seq){
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
		//$result = $this->db->insert_id();
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
		if($data['check_type']){
			switch($data['check_type'])
			{
				case "use_df_id":
					//해당 TC ID 체계를 사용중인 결함 ID 체계(co_name)를 변경해준다.
					$pattern = "/,/";
					$this->db->where('co_type', 'df_id_rule');
					$query = $this->db->get('otm_code');
					foreach ($query->result() as $temp_row)
					{
						$temp_arr[] = $temp_row;

						$temp_default_value = $temp_row->co_default_value;
						$rule_arr = preg_split($pattern,$temp_default_value);

						if(isset($rule_arr[0]) || $tmp_arr[0])
						{
							if($rule_arr[0] == $data['co_seq']){
								$string = $temp_row->co_name;
								$new_df_id_rule_name = str_replace($data['before_name'], $data['co_name'], $string );

								$new_df_data = array(
									'co_name' => $new_df_id_rule_name
								);

								$this->db->where('co_seq', $temp_row->co_seq);
								$this->db->update('otm_code', $new_df_data);

							}
						}
					}
					break;
			}
		}

		if($data['co_is_required'] === 'Y'){
			$str_sql = "update otm_code set co_is_required='N' where co_type='".$data['co_type']."'";
			$query = $this->db->query($str_sql);
		}
		if($data['co_is_default'] === 'Y'){
			$str_sql = "update otm_code set co_is_default='N' where co_type='".$data['co_type']."'";
			$query = $this->db->query($str_sql);
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
	* Function delete_code
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function delete_code($data)
	{
		for($i=0; $i<count($data['co_list']); $i++){

			$data['action_type']= 'delete';
			$data['co_seq']		= $data['co_list'][$i];
			$data['check']		= true;
			$check_rule = $this->check_id_rule($data);

			if($check_rule['check_msg'] !== '' && $check_rule['check'] == false){
				return json_encode($check_rule);
			}else{
				$this->db->where('co_seq', $data['co_list'][$i]);
				$this->db->delete('otm_code');
			}
		}
		return 'ok';
	}
}
//End of file Id_rule_m.php
//Location: ./models/Id_rule_m.php
