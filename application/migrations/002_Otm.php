<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_otm extends CI_Migration {
	
	public function up()
	{
		/**	otm_customform Table
		*	Modify Column
		*/
		$fields = array(
			'cf_default_value' => array('name' => 'cf_default_value', 'type' => 'TEXT'),
			'cf_content' => array('name' => 'cf_content', 'type' => 'TEXT')
		);
		$this->dbforge->modify_column('otm_customform', $fields);

		$fields = array(
			'pc_default_value' => array('name' => 'pc_default_value', 'type' => 'TEXT'),
			'pc_content' => array('name' => 'pc_content', 'type' => 'TEXT')
		);
		$this->dbforge->modify_column('otm_project_customform', $fields);
		

		$fields = array(
			'mb_1' => array(
					 'name' => 'mb_1',
					 'type' => 'TEXT',
			),
			'mb_2' => array(
					 'name' => 'mb_2',
					 'type' => 'TEXT',
			),
			'mb_3' => array(
					 'name' => 'mb_3',
					 'type' => 'TEXT',
			),
			'mb_4' => array(
					 'name' => 'mb_4',
					 'type' => 'TEXT',
			),
			'mb_5' => array(
					 'name' => 'mb_5',
					 'type' => 'TEXT',
			),
			'mb_6' => array(
					 'name' => 'mb_6',
					 'type' => 'TEXT',
			),
			'mb_7' => array(
					 'name' => 'mb_7',
					 'type' => 'TEXT',
			),
			'mb_8' => array(
					 'name' => 'mb_8',
					 'type' => 'TEXT',
			),
			'mb_9' => array(
					 'name' => 'mb_9',
					 'type' => 'TEXT',
			),
			'mb_10' => array(
					 'name' => 'mb_10',
					 'type' => 'TEXT',
			)
		);
		$this->dbforge->modify_column('otm_member', $fields);



		/**
		* OTM New Table
		*/

		/*
		* ID Generator Table(Saved last increment number.)
		*/
		$str_sql = 'create table if not exists otm_id_generator (
			  otm_project_pr_seq int(11) not null,
			  id_type varchar(255) not null,
			  id_seq   int(11) unsigned not null,
			  primary key (otm_project_pr_seq,id_type)
			)';
		$this->migration->check_table('otm_id_generator', $str_sql);

		$this->insert_default_data();
	}

	public function down()
	{
		//$this->dbforge->drop_table('members');
	}

	function insert_default_data()
	{
		$str_sql = "INSERT INTO otm_code (co_type,co_name,co_is_required,co_is_default,co_default_value) VALUES ('tc_id_rule','tc_###','N','Y','tc_,,,,###')";
		$this->db->query($str_sql);

		$str_sql = "INSERT INTO otm_code (co_type,co_name,co_is_required,co_is_default,co_default_value) VALUES ('df_id_rule','df_###','N','Y','df_,,,,###')";
		$this->db->query($str_sql);
	}
}

/* End of file 002_Otm.php */
/* Location: ./application/migratioins/002_Otm.php */