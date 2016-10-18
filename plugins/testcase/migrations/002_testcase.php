<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_testcase extends CI_Migration {
	
	public function up()
	{
		/**	OTM Testcase Result
		*	Add Column
		*/
		$fields = array(
			'otm_defect_df_seq' => array('type' => 'int(11)','null' => TRUE)
		);
		$this->dbforge->add_column('otm_testcase_result', $fields,'otm_testcase_link_tl_seq');
	
		/**
		* OTM New Table
		*/

		$this->migration_data();
	}

	public function down()
	{
		//$this->dbforge->drop_table('table name');
	}

	function migration_data()
	{		
		$this->db->from('otm_defect');
		$this->db->where('otm_testcase_result_tr_seq !=','');
		$query = $this->db->get();
		if($query->result()){
			foreach ($query->result() as $row)
			{
				$modify_array['otm_defect_df_seq'] = $row->df_seq;
				$where = array('tr_seq'=>$row->otm_testcase_result_tr_seq);
				$this->db->update('otm_testcase_result', $modify_array, $where);
			}
		}
	}
}