<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_otm extends CI_Migration {

	public function up()
	{
		/**	OTM otm_project
		*	Add Column
		*/
		$fields = array(
			'pr_ord' => array('type' => 'int(11)','null' => FALSE,'default' => 0),
			'otm_project_group_pg_seq' => array('type' => 'int(11)','null' => FALSE,'default' => 0)
		);
		$this->dbforge->add_column('otm_project', $fields,'pr_enddate');

		//$modify_array['otm_project_group_pg_seq'] = 'otm_project.pr_seq';
		//$this->db->update('otm_project', $modify_array, $where);

		//$str_sql = "UPDATE otm_project SET otm_project_group_pg_seq = otm_project.pr_seq";
		//$query = $this->db->query($str_sql);


		/**
		* OTM New Table
		*/

		/*
		* otm_project_group
		*/
		$str_sql = 'create table if not exists otm_project_group (
			pg_seq int(11) not null auto_increment,
			pg_name varchar(255) not null,
			pg_pid int(11) default 0,
			pg_ord int(11) default 0,
			writer varchar(60) not null,
			regdate datetime not null,
			last_writer varchar(60),
			last_update datetime,
			primary key (pg_seq)
		)';
		$this->migration->check_table('otm_project_group', $str_sql);


	}

	public function down()
	{
		//$this->dbforge->drop_table('members');
	}
}

/* End of file 002_Otm.php */
/* Location: ./application/migratioins/002_Otm.php */