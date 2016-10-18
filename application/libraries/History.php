<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class History extends Controller {

	public function __construct() {
		parent::__construct();

	}

	/**
	 * History Insert
	 */
	function history($data)
	{
		$this->load->database();
		$date=date("Y-m-d H:i:s");
		$writer = $this->session->userdata('mb_email');

		$pr_seq	= $data['pr_seq'];
		$category = $data['category'];
		$category_key = (isset($data['category_key']))?$data['category_key']:null;
		$history_key = (isset($data['history_key']))?$data['history_key']:null;

		if($category === 'defect'){
			$category_key = 'df_seq';
			$history_key = 'dh_seq';
		}else if($category === 'testcase'){
			$category_key = 'tc_seq';
			$history_key = 'th_seq';
		}

		if(isset($category_key) && isset($history_key)){
		}else{
			return;
		}

		$details	= $data['details'];

		if(isset($details) && (count($details) > 0)){
			$category_seq	= $data[$category_key];

			$this->db->set('otm_project_pr_seq',	$pr_seq);
			$this->db->set('otm_'.$category.'_'.$category_key,		$category_seq);
			$this->db->set('writer',				$writer);
			$this->db->set('regdate',				$date);

			$this->db->insert('otm_'.$category.'_historys');
			$history_seq = $this->db->insert_id();

			for($i=0; $i<count($details); $i++)
			{
				$detail = $details[$i];

				$this->db->set('otm_'.$category.'_historys_'.$history_key,	$history_seq);
				$this->db->set('action_type',	$detail['action_type']);
				$this->db->set('old_value',		$detail['old_value']);
				$this->db->set('value',			$detail['value']);

				$this->db->insert('otm_'.$category.'_history_details');
			}
		}

		return;

		///////////////////////////////
		/*
		if($category === 'defect'){
			$details	= $data['details'];

			if(isset($details) && (count($details) > 0)){
				$df_seq	= $data['df_seq'];

				$this->db->set('otm_project_pr_seq',	$pr_seq);
				$this->db->set('otm_defect_df_seq',		$df_seq);
				$this->db->set('writer',				$writer);
				$this->db->set('regdate',				$date);

				$this->db->insert('otm_defect_historys');
				$dh_seq = $this->db->insert_id();

				for($i=0; $i<count($details); $i++)
				{
					$detail = $details[$i];

					$this->db->set('otm_defect_historys_dh_seq',	$dh_seq);
					$this->db->set('action_type',	$detail['action_type']);
					$this->db->set('old_value',		$detail['old_value']);
					$this->db->set('value',			$detail['value']);

					$this->db->insert('otm_defect_history_details');
				}
			}
		}else if($category === 'testcase'){
			$details	= $data['details'];

			if(isset($details) && (count($details) > 0)){
				$tc_seq	= $data['tc_seq'];

				$this->db->set('otm_project_pr_seq',	$pr_seq);
				$this->db->set('otm_testcase_tc_seq',	$tc_seq);
				$this->db->set('writer',				$writer);
				$this->db->set('regdate',				$date);

				$this->db->insert('otm_testcase_historys');
				$th_seq = $this->db->insert_id();

				for($i=0; $i<count($details); $i++)
				{
					$detail = $details[$i];

					$this->db->set('otm_testcase_historys_th_seq',	$th_seq);
					$this->db->set('action_type',	$detail['action_type']);
					$this->db->set('old_value',		$detail['old_value']);
					$this->db->set('value',			$detail['value']);

					$this->db->insert('otm_testcase_history_details');
				}
			}
		}
		*/
	}

	/**
	 * History Delete
	 */
	function delete_history($data)
	{
		$date=date("Y-m-d H:i:s");
		$writer = $this->session->userdata('mb_email');
		$category = $data['category'];

		if($category === 'defect'){
			$df_seq	= $data['df_seq'];

			$this->db->from('otm_defect_historys');
			$this->db->where('otm_defect_df_seq',$df_seq);
			$query = $this->db->get();

			foreach ($query->result() as $row)
			{
				$dh_seq = $row->dh_seq;
				$delete_array = array(
					'otm_defect_historys_dh_seq' => $dh_seq
				);
				$this->db->delete('otm_defect_history_details',$delete_array);
			}

			$delete_array = array(
				'otm_defect_df_seq'=>$df_seq
			);

			$this->db->delete('otm_defect_historys',$delete_array);
		}else if($category === 'testcase'){
			$tc_seq	= $data['tc_seq'];

			$this->db->from('otm_testcase_historys');
			$this->db->where('otm_testcase_tc_seq',$tc_seq);
			$query = $this->db->get();

			foreach ($query->result() as $row)
			{
				$th_seq = $row->th_seq;
				$delete_array = array(
					'otm_testcase_historys_th_seq' => $th_seq
				);
				$this->db->delete('otm_testcase_history_details',$delete_array);
			}

			$delete_array = array(
				'otm_testcase_tc_seq'=>$tc_seq
			);

			$this->db->delete('otm_testcase_historys',$delete_array);
		}
	}
}

/* End of file History.php */
/* Location: ./application/libraries/History.php */