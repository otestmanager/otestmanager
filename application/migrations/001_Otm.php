<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_otm extends CI_Migration {

	public function up()
	{
		/**	Users Table

			mb_email		: email
			mb_name			: name
			mb_pw			: password
			mb_tel			: telephone
			mb_is_admin		: admin permition
			mb_is_approved	: approve
			mb_memo			: content
			writer			: writer user email
			regdate			: regist date
			last_writer		: update wirter
			last_update		: update date
		*/
		$str_sql = 'create table if not exists otm_member (
					mb_email varchar(255) NOT NULL,
					mb_name varchar(255) NOT NULL,
					mb_pw varchar(255) NOT NULL,
					mb_tel varchar(255),
					mb_is_admin char(1) default "N",
					mb_is_approved char(1) default "N",
					mb_memo text,
					mb_lang varchar(255) default "en",
					mb_other text default "",
					writer varchar(255) not null,
					regdate datetime not null,
					last_writer varchar(255),
					last_update datetime,
					mb_1 text default "",
					mb_2 text default "",
					mb_3 text default "",
					mb_4 text default "",
					mb_5 text default "",
					mb_6 text default "",
					mb_7 text default "",
					mb_8 text default "",
					mb_9 text default "",
					mb_10 text default "",
					primary key (mb_email)
			)';
		$this->migration->check_table('otm_member', $str_sql);


		/**	Groups Table

			gr_seq			: key value
			gr_name			: group name
			gr_content		: content
			writer			: writer user email
			regdate			: regist date
			last_writer		: update wirter
			last_update		: update date
		*/
		$str_sql = 'create table if not exists otm_group (
					gr_seq int(11) NOT NULL auto_increment,
					gr_name varchar(255) NOT NULL,
					gr_content text,
					writer varchar(255) not null,
					regdate datetime not null,
					last_writer varchar(255),
					last_update datetime,
					gr_1 text default "",
					gr_2 text default "",
					gr_3 text default "",
					gr_4 text default "",
					gr_5 text default "",
					gr_6 text default "",
					gr_7 text default "",
					gr_8 text default "",
					gr_9 text default "",
					gr_10 text default "",
					primary key (gr_seq)
			)';
		$this->migration->check_table('otm_group', $str_sql);


		/**	Group Members Table

			otm_group_gr_seq			: group key value
			otm_member_mb_email			: user email
		*/
		$str_sql = 'create table if not exists otm_group_member (
				otm_group_gr_seq int(11) NOT NULL,
				otm_member_mb_email varchar(255) NOT NULL,
				primary key (otm_group_gr_seq,otm_member_mb_email)
			)';
		$this->migration->check_table('otm_group_member', $str_sql);


		/**	Projects Table

			pr_seq			: project key value
			pr_name			: user email
			pr_description	: content
			pr_startdate	: project start date
			pr_enddate		: project end date
			writer			: writer user email
			regdate			: regist date
			last_writer		: update wirter
			last_update		: update date
		*/
		$str_sql = 'create table if not exists otm_project (
				pr_seq int(11) not null auto_increment,
				pr_name varchar(255) not null,
				pr_description text,
				#pr_status varchar(255) not null,
				pr_startdate datetime,
				pr_enddate datetime,
				writer varchar(255) not null,
				regdate datetime not null,
				last_writer varchar(255),
				last_update datetime,
				pr_1 text default "",
				pr_2 text default "",
				pr_3 text default "",
				pr_4 text default "",
				pr_5 text default "",
				pr_6 text default "",
				pr_7 text default "",
				pr_8 text default "",
				pr_9 text default "",
				pr_10 text default "",
				primary key (pr_seq)
			)';
		$this->migration->check_table('otm_project', $str_sql);


		/**	Custom Forms Table

			cf_seq				: 번호
			cf_name				: 사용자 정의명
			cf_category			: 분류(결함, 테스트케이스냐) ID_DEFECT, ID_TC
			cf_is_required		: 필수 입력여부
			cf_formtype			: textfied,textarea,combo,....
			cf_default_value    : 기본값
			cf_content			: [{name:'',value:''},{name:'',value:''},{name:'',value:''}]
			writer			: writer user email
			regdate			: regist date
			last_writer		: update wirter
			last_update		: update date
		*/
		$str_sql = 'create table if not exists otm_customform (
				cf_seq int(11) not null auto_increment,
				cf_name varchar(255) not null,
				cf_category varchar(255) not null,
				cf_is_required char(1) default "N",
				cf_is_display char(1) default "N",
				cf_formtype varchar(255) not null,
				cf_default_value text,
				cf_content text,
				writer varchar(255) not null,
				regdate datetime not null,
				last_writer varchar(255),
				last_update datetime,
				cf_1 text default "",
				cf_2 text default "",
				cf_3 text default "",
				cf_4 text default "",
				cf_5 text default "",
				cf_6 text default "",
				cf_7 text default "",
				cf_8 text default "",
				cf_9 text default "",
				cf_10 text default "",
				primary key (cf_seq)
			)';
		$this->migration->check_table('otm_customform', $str_sql);


		/**	Codes Table

			co_seq				: 번호
			co_type				: 상태, 심각도, 우선순위, tc입력항목  (status,severity,priority,tc_inputform,tc_result)
			co_name				: 이름명
			co_is_required		: 필수여부(Y,N)
			co_is_default		: 기본값(Y,N) 처음에 선택되는값.
			co_position			: 순서값
			co_default_value	: 기본값
			co_color			: 색상값
		*/
		$str_sql = 'create table if not exists otm_code (
				co_seq int(11) not null auto_increment,
				co_type varchar(255) not null,
				co_name varchar(255) not null,
				co_is_required char(1) default "N",
				co_is_default char(1) default "N",
				co_position int(11),
				co_default_value text,
				co_color varchar(255),
				co_1 text default "",
				co_2 text default "",
				co_3 text default "",
				co_4 text default "",
				co_5 text default "",
				co_6 text default "",
				co_7 text default "",
				co_8 text default "",
				co_9 text default "",
				co_10 text default "",
				primary key (co_seq)
			)';
		$this->migration->check_table('otm_code', $str_sql);


		/** Role
		*/
		$str_sql = 'create table if not exists otm_role (
				rp_seq int(11) not null auto_increment,
				rp_name varchar(255) not null,
				writer varchar(255) not null,
				regdate datetime not null,
				last_writer varchar(255),
				last_update datetime,
				rp_1 text default "",
				rp_2 text default "",
				rp_3 text default "",
				rp_4 text default "",
				rp_5 text default "",
				primary key (rp_seq)
			)';
		$this->migration->check_table('otm_role', $str_sql);


		/** Role Permission
		*/
		$str_sql = 'create table if not exists otm_role_permission (
				pmi_seq int(11) not null auto_increment,
				otm_role_rp_seq int(11) not null,
				pmi_category varchar(255) not null,
				pmi_name varchar(255) not null,
				pmi_value char(1),
				pmi_1 text default "",
				pmi_2 text default "",
				pmi_3 text default "",
				pmi_4 text default "",
				pmi_5 text default "",
				primary key (pmi_seq)
			)';
		$this->migration->check_table('otm_role_permission', $str_sql);


		/** Project Members

			pm_seq				: key value
			otm_project_pr_seq	: project key value
			otm_member_mb_email : user key value
		*/
		$str_sql = 'create table if not exists otm_project_member (
				pm_seq int(11) not null auto_increment,
				otm_project_pr_seq int(11) not null,
				otm_member_mb_email varchar(255) not null,
				primary key (pm_seq)
			)';
		$this->migration->check_table('otm_project_member', $str_sql);


		/** Project Member Roles

			otm_project_member_pm_seq	: Project Member key value
			otm_role_rp_seq				: System Role Key vale
		*/
		$str_sql = 'create table if not exists otm_project_member_role (
				otm_project_member_pm_seq int(11) not null,
				otm_role_rp_seq int(11) not null,
				primary key (otm_project_member_pm_seq,otm_role_rp_seq)
			)';
		$this->migration->check_table('otm_project_member_role', $str_sql);


		/** Project Codes

			pco_seq				: Project Code key value
			otm_project_pr_seq	: Project Key vale
			pco_type			: 상태, 심각도, 우선순위, tc입력항목  (status,severity,priority,tc_inputform,tc_result)
			pco_name			: 이름명
			pco_is_required		: 필수여부(Y,N)
			pco_is_default		: 기본값(Y,N) 처음에 선택되는값.
			pco_position		: 순서값
			pco_default_value	: 기본값
			pco_color			: 색상값
		*/
		$str_sql = 'create table if not exists otm_project_code (
				pco_seq int(11) not null auto_increment,
				otm_project_pr_seq int(11) not null,
				pco_type varchar(255) not null,
				pco_name varchar(255) not null,
				pco_is_required char(1) default "N",
				pco_is_default char(1) default "N",
				pco_position int(11),
				pco_default_value text,
				pco_color varchar(255),
				pco_is_use char(1) default "Y",
				pco_1 text default "",
				pco_2 text default "",
				pco_3 text default "",
				pco_4 text default "",
				pco_5 text default "",
				primary key (pco_seq)
			)';
		$this->migration->check_table('otm_project_code', $str_sql);


		/**	Project Custom Forms Table

			pc_seq				: 번호
			otm_project_pr_seq	: Project Key vale
			pc_name				: 사용자 정의명
			pc_category			: 분류(결함, 테스트케이스냐) ID_DEFECT, ID_TC
			pc_is_required		: 필수 입력여부
			pc_formtype			: textfied,textarea,combo,....
			pc_default_value    : 기본값
			pc_content			: [{name:'',value:''},{name:'',value:''},{name:'',value:''}]
			writer				: writer user email
			regdate				: regist date
			last_writer			: update wirter
			last_update			: update date
		*/
		$str_sql = 'create table if not exists otm_project_customform (
				pc_seq int(11) not null auto_increment,
				otm_project_pr_seq int(11) not null,
				pc_name varchar(255) not null,
				pc_category varchar(255) not null,
				pc_is_required char(1) default "N",
				pc_is_display char(1) default "N",
				pc_formtype varchar(255) not null,
				pc_default_value text,
				pc_content text,
				pc_is_use char(1) default "Y",
				writer varchar(255) not null,
				regdate datetime not null,
				last_writer varchar(255),
				last_update datetime,
				pc_1 text default "",
				pc_2 text default "",
				pc_3 text default "",
				pc_4 text default "",
				pc_5 text default "",
				primary key (pc_seq)
			)';
		$this->migration->check_table('otm_project_customform', $str_sql);


		/**
		* OTM File
		*/
		$str_sql = 'create table if not exists otm_file (
					otm_category varchar(255) not null default "",
					otm_project_pr_seq int(11) not null,
					target_seq int(11) not null,
					of_no int(11) not null,
					of_source varchar(255) not null,
					of_file varchar(255) not null,
					of_filesize int(11) not null,
					of_width int(11) default 0,
					of_height int(11)  default 0,
					writer varchar(255) not null,
					regdate datetime not null,
					of_1 text default "",
					of_2 text default "",
					of_3 text default "",
					of_4 text default "",
					of_5 text default "",
					primary key (otm_category,otm_project_pr_seq,target_seq,of_no)
				)';
		$this->migration->check_table('otm_file', $str_sql);


		/**
		* OTM Common TestCase product
		*/
		$str_sql = 'create table if not exists otm_com_product (
					p_seq int(11) not null auto_increment,
					p_subject varchar(255) not null,
					p_description text,
					writer varchar(255) not null,
					regdate datetime not null,
					last_writer varchar(255),
					last_update datetime,
					pd_1 text default "",
					pd_2 text default "",
					pd_3 text default "",
					pd_4 text default "",
					pd_5 text default "",
					primary key (p_seq)
			)';
		$this->migration->check_table('otm_com_product', $str_sql);


		/**
		* OTM Common TestCase version
		*/
		$str_sql = 'create table if not exists otm_com_version (
					v_seq int(11) not null auto_increment,
					otm_com_product_p_seq int(11) not null,
					v_version int(11) not null,
					v_version_name varchar(255) not null,
					v_version_description text,
					writer varchar(255) not null,
					regdate datetime not null,
					last_writer varchar(255),
					last_update datetime,
					v_1 text default "",
					v_2 text default "",
					v_3 text default "",
					v_4 text default "",
					v_5 text default "",
					primary key (v_seq)
			)';
		$this->migration->check_table('otm_com_version', $str_sql);


		/**
		* OTM Common TestCase
		*/
		$str_sql = 'create table if not exists otm_com_testcase (
					ct_seq int(11) not null auto_increment,
					otm_com_version_v_seq int(11) not null,
					ct_subject varchar(255) not null,
					ct_precondition text,
					ct_testdata text,
					ct_procedure text,
					ct_expected_result text,
					ct_description text,
					ct_inp_id varchar(255) not null,
					ct_inp_pid varchar(255) default "ctc_0",
					ct_out_id varchar(255),
					ct_is_task varchar(10) default "file",
					ct_ord int(11) default 0,
					writer varchar(255) not null,
					regdate datetime not null,
					last_writer varchar(255),
					last_update datetime,
					ct_1 text default "",
					ct_2 text default "",
					ct_3 text default "",
					ct_4 text default "",
					ct_5 text default "",
					ct_6 text default "",
					ct_7 text default "",
					ct_8 text default "",
					ct_9 text default "",
					ct_10 text default "",
					primary key (ct_seq)
			)';
		$this->migration->check_table('otm_com_testcase', $str_sql);


		/**
		* OTM New Table
		*/

		$this->insert_default_data();
		$this->insert_sample_data();
	}

	public function down()
	{
		//$this->dbforge->drop_table('members');
	}

	function insert_default_data()
	{
		$writer = 'admin@sta.co.kr';
		$regdate = $date=date('Y-m-d H:i:s');

		$this->insert_member();

		$data = array();
		$default_tc_item = array('사전조건','테스트데이터','실행절차','예상결과','비고');
		for($i=0; $i<count($default_tc_item); $i++){
			$data['cf_name']			= $default_tc_item[$i];
			$data['cf_category']		= 'TC_ITEM';
			$data['cf_is_required']		= 'N';
			$data['cf_formtype']		= 'textarea';
			$data['cf_default_value']	= '';
			$data['cf_content']			= 'default';
			$data['writer']				= $writer;
			$data['regdate']			= $regdate;
			$data['last_writer']		= '';
			$data['last_update']		= '';
			$this->db->insert('otm_customform', $data);
		}


		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('status','신규(New)','N','Y')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('status','개설(Open)','N','N')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('status','반려(Rejected)','N','N')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('status','수정완료(Fixed)','N','N')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('status','재개설(Reopened)','N','N')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('status','완료(Closed)','Y','N')");

		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('severity','치명적','N','N')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('severity','매우심각','N','N')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('severity','심각','N','N')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('severity','보통','N','Y')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('severity','경미','N','N')");

		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('priority','즉시해결','N','Y')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('priority','주의요망','N','N')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('priority','대기','N','N')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('priority','낮은순위','N','N')");

		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('frequency','항상','N','Y')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('frequency','빈번한','N','N')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('frequency','불규칙한','N','N')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('frequency','일시적인','N','N')");

		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('tc_result','PASS','N','Y')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('tc_result','FAIL','N','N')");
		$this->db->query("insert into otm_code(co_type,co_name,co_is_required,co_is_default) values('tc_result','BLOCK','N','N')");

		$this->db->query("insert into otm_role (rp_name,writer,regdate) values('OTM_Admin','{$writer}','{$regdate}')");
		$this->db->query("insert into otm_role (rp_name,writer,regdate) values('Test_Monitor','{$writer}','{$regdate}')");
		$this->db->query("insert into otm_role (rp_name,writer,regdate) values('Test_Manager','{$writer}','{$regdate}')");
		$this->db->query("insert into otm_role (rp_name,writer,regdate) values('Test_Leader','{$writer}','{$regdate}')");
		$this->db->query("insert into otm_role (rp_name,writer,regdate) values('Tester','{$writer}','{$regdate}')");
		$this->db->query("insert into otm_role (rp_name,writer,regdate) values('Defect_Manager','{$writer}','{$regdate}')");
		$this->db->query("insert into otm_role (rp_name,writer,regdate) values('Developer','{$writer}','{$regdate}')");

		$otm_role_permission=array(
			array(//OTM_Admin
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_edit","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_delete","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_status","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_add","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_assign","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_TC","pmi_name"=>"tc_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_TC","pmi_name"=>"tc_add","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_TC","pmi_name"=>"tc_view_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_TC","pmi_name"=>"tc_result","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_REPORT","pmi_name"=>"report_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_add","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"1","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete_all","pmi_value"=>"1")
			),
			array(//Test_Monitor
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_status","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_add","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_assign","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_TC","pmi_name"=>"tc_view","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_TC","pmi_name"=>"tc_add","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_TC","pmi_name"=>"tc_view_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_TC","pmi_name"=>"tc_result","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_REPORT","pmi_name"=>"report_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_add","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"2","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete_all","pmi_value"=>"")
			),
			array(//Test_Manager
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_edit","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_status","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_add","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_assign","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_TC","pmi_name"=>"tc_view","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_TC","pmi_name"=>"tc_add","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_TC","pmi_name"=>"tc_view_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_TC","pmi_name"=>"tc_result","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_REPORT","pmi_name"=>"report_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_add","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"3","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete_all","pmi_value"=>"")
			),
			array(//Test_Leader
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_status","pmi_value"=>""),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_add","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_assign","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_TC","pmi_name"=>"tc_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_TC","pmi_name"=>"tc_add","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_TC","pmi_name"=>"tc_view_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_TC","pmi_name"=>"tc_result","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_REPORT","pmi_name"=>"report_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view","pmi_value"=>""),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_add","pmi_value"=>""),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"4","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete_all","pmi_value"=>"")
			),
			array(//Tester
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_status","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_add","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_assign","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_TC","pmi_name"=>"tc_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_TC","pmi_name"=>"tc_add","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_TC","pmi_name"=>"tc_view_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_TC","pmi_name"=>"tc_result","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_REPORT","pmi_name"=>"report_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_add","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"5","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete_all","pmi_value"=>"")
			),
			array(//Defect_Manager
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_status","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_add","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_assign","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_TC","pmi_name"=>"tc_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_TC","pmi_name"=>"tc_add","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_TC","pmi_name"=>"tc_view_all","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_TC","pmi_name"=>"tc_result","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_REPORT","pmi_name"=>"report_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_add","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"6","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete_all","pmi_value"=>"")
			),
			array(//Developer
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_PROJECT","pmi_name"=>"project_status","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_add","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_view_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_delete_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_DEFECT","pmi_name"=>"defect_assign","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_TC","pmi_name"=>"tc_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_TC","pmi_name"=>"tc_add","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_TC","pmi_name"=>"tc_view_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_TC","pmi_name"=>"tc_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_TC","pmi_name"=>"tc_delete_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_TC","pmi_name"=>"tc_result","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_REPORT","pmi_name"=>"report_view","pmi_value"=>"1"),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_add","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_view_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_edit_all","pmi_value"=>""),
				array("otm_role_rp_seq"=>"7","pmi_category"=>"ID_COMTC","pmi_name"=>"com_tc_delete_all","pmi_value"=>"")
			)
		);

		for($i=0;$i<sizeof($otm_role_permission);$i++){
			for($j=0;$j<sizeof($otm_role_permission[$i]);$j++){
				$rp_seq = $otm_role_permission[$i][$j]['otm_role_rp_seq'];
				$pmi_category = $otm_role_permission[$i][$j]['pmi_category'];
				$pmi_name = $otm_role_permission[$i][$j]['pmi_name'];
				$pmi_value = $otm_role_permission[$i][$j]['pmi_value'];

				$this->db->query("insert into otm_role_permission (otm_role_rp_seq,pmi_category,pmi_name,pmi_value) values('$rp_seq', '$pmi_category', '$pmi_name', '$pmi_value')");
			}
		}

	}

	function insert_member()
	{
		$writer = 'admin@sta.co.kr';
		$regdate = $date=date('Y-m-d H:i:s');
		$default_password = 'otestmanager';

		$str_sql = "insert into otm_member(mb_email,mb_name,mb_pw,mb_is_admin,mb_is_approved,writer,regdate) values('admin@sta.co.kr','관리자',password('{$default_password}'),'Y','Y','{$writer}','{$regdate}')";
		$this->db->query($str_sql);

		$member = $this->get_member_list();

		for($i=0; $i<count($member); $i++){
			$mb_email = $member[$i]['mb_email'];
			$mb_name = $member[$i]['mb_name'];
			$str_sql = "insert into otm_member(mb_email,mb_name,mb_pw,mb_is_admin,mb_is_approved,writer,regdate) values('{$mb_email}','{$mb_name}',password('{$default_password}'),'Y','Y','{$writer}','{$regdate}')";
			$this->db->query($str_sql);
		}
	}

	function get_member_list()
	{

		$member = array(
			//array('mb_email'=>'{email}','mb_name'=>'{name}')
		);

		return $member;
	}
	function insert_sample_data()
	{
		$default_writer = "admin@sta.co.kr";

		$member = array(
			array('mb_email'=>'dwkang@conkrit.com','mb_name'=>'강동원'),
			array('mb_email'=>'jygo@conkrit.com','mb_name'=>'고지용'),
			array('mb_email'=>'khkim@conkrit.com','mb_name'=>'김경희'),
			array('mb_email'=>'dskim@conkrit.com','mb_name'=>'김대성'),
			array('mb_email'=>'ybdoung@conkrit.com','mb_name'=>'동영배'),
			array('mb_email'=>'jypark@conkrit.com','mb_name'=>'박진영'),
			array('mb_email'=>'tjsoe@conkrit.com','mb_name'=>'서태지'),
			array('mb_email'=>'shsong@conkrit.com','mb_name'=>'송승헌'),
			array('mb_email'=>'hsyang@conkrit.com','mb_name'=>'양현석'),
			array('mb_email'=>'mjyeon@conkrit.com','mb_name'=>'연민정'),
			array('mb_email'=>'nylee@conkrit.com','mb_name'=>'이나영'),
			array('mb_email'=>'dglee@conkrit.com','mb_name'=>'이동건'),
			array('mb_email'=>'sjlee@conkrit.com','mb_name'=>'이서진'),
			array('mb_email'=>'dgjang@conkrit.com','mb_name'=>'장동건'),
			array('mb_email'=>'jwchoi@conkrit.com','mb_name'=>'최지우')
		);
		for($i=0; $i<count($member); $i++){
			$mb_email = $member[$i]['mb_email'];
			$mb_name = $member[$i]['mb_name'];
			$mb_pw = 'starbugs';
			$str_sql = "insert into otm_member(mb_email,mb_name,mb_pw,mb_is_admin,mb_is_approved,writer,regdate) values('{$mb_email}','{$mb_name}',password('{$mb_pw}'),'N','Y','{$default_writer}',now())";
			$this->db->query($str_sql);
		}

		$str_sql = "INSERT INTO `otm_group` VALUES (1,'리니어블','','admin@sta.co.kr','2015-04-17 15:46:43','','0000-00-00 00:00:00','','','','','','','','','','')";
		$this->db->query($str_sql);

		$str_sql = "INSERT INTO `otm_group_member` VALUES (1,'dgjang@conkrit.com'),(1,'dglee@conkrit.com'),(1,'dskim@conkrit.com'),(1,'dwkang@conkrit.com'),(1,'hsyang@conkrit.com'),(1,'jwchoi@conkrit.com'),(1,'jygo@conkrit.com'),(1,'jypark@conkrit.com'),(1,'khkim@conkrit.com'),(1,'mjyeon@conkrit.com'),(1,'nylee@conkrit.com'),(1,'shsong@conkrit.com'),(1,'sjlee@conkrit.com'),(1,'tjsoe@conkrit.com'),(1,'ybdoung@conkrit.com')";
		$this->db->query($str_sql);

		$str_sql = "INSERT INTO `otm_project` VALUES (1,'리니어블_테스트케이스','','2015-04-10 00:00:00','2015-04-14 00:00:00','admin@sta.co.kr','2015-04-17 15:46:08',NULL,NULL,'','','','','','','','','','')";
		$this->db->query($str_sql);


		$str_sql = "INSERT INTO otm_project_member VALUES (1,1,'admin@sta.co.kr'),(2,1,'ybdoung@conkrit.com'),(3,1,'dwkang@conkrit.com'),(4,1,'jypark@conkrit.com'),(5,1,'jygo@conkrit.com'),(6,1,'khkim@conkrit.com'),(7,1,'dskim@conkrit.com'),(8,1,'dgjang@conkrit.com'),(9,1,'dglee@conkrit.com'),(10,1,'nylee@conkrit.com'),(11,1,'sjlee@conkrit.com'),(12,1,'jwchoi@conkrit.com'),(13,1,'hsyang@conkrit.com'),(14,1,'mjyeon@conkrit.com'),(15,1,'tjsoe@conkrit.com'),(16,1,'shsong@conkrit.com')";
		$this->db->query($str_sql);

		$str_sql = "INSERT INTO otm_project_member_role VALUES (1,1),(2,5),(3,5),(4,5),(5,5),(6,5),(7,5),(8,5),(9,5),(10,5),(11,5),(12,5),(13,5),(14,5),(15,5),(16,5)";
		$this->db->query($str_sql);

		$str_sql = "INSERT INTO otm_project_code VALUES (1,1,'status','신규(New)','N','Y',NULL,NULL,NULL,'Y','','','','',''),(2,1,'status','개설(Open)','N','N',NULL,NULL,NULL,'Y','','','','',''),(3,1,'status','반려(Rejected)','N','N',NULL,NULL,NULL,'Y','','','','',''),(4,1,'status','수정완료(Fixed)','N','N',NULL,NULL,NULL,'Y','','','','',''),(5,1,'status','재개설(Reopened)','N','N',NULL,NULL,NULL,'Y','','','','',''),(6,1,'status','완료(Closed)','Y','N',NULL,NULL,NULL,'Y','','','','',''),(7,1,'severity','치명적','N','N',NULL,NULL,NULL,'Y','','','','',''),(8,1,'severity','매우심각','N','N',NULL,NULL,NULL,'Y','','','','',''),(9,1,'severity','심각','N','N',NULL,NULL,NULL,'Y','','','','',''),(10,1,'severity','보통','N','Y',NULL,NULL,NULL,'Y','','','','',''),(11,1,'severity','경미','N','N',NULL,NULL,NULL,'Y','','','','',''),(12,1,'priority','즉시해결','N','Y',NULL,NULL,NULL,'Y','','','','',''),(13,1,'priority','주의요망','N','N',NULL,NULL,NULL,'Y','','','','',''),(14,1,'priority','대기','N','N',NULL,NULL,NULL,'Y','','','','',''),(15,1,'priority','낮은순위','N','N',NULL,NULL,NULL,'Y','','','','',''),(16,1,'frequency','항상','N','Y',NULL,NULL,NULL,'Y','','','','',''),(17,1,'frequency','빈번한','N','N',NULL,NULL,NULL,'Y','','','','',''),(18,1,'frequency','불규칙한','N','N',NULL,NULL,NULL,'Y','','','','',''),(19,1,'frequency','일시적인','N','N',NULL,NULL,NULL,'Y','','','','',''),(20,1,'tc_result','PASS','N','Y',NULL,NULL,NULL,'Y','','','','',''),(21,1,'tc_result','FAIL','N','N',NULL,NULL,NULL,'Y','','','','',''),(22,1,'tc_result','BLOCK','N','N',NULL,NULL,NULL,'Y','','','','','')";
		$this->db->query($str_sql);

		$str_sql = "INSERT INTO `otm_project_customform` VALUES (1,1,'사전조건','TC_ITEM','N','N','textarea','','default','Y','admin@sta.co.kr','2015-04-17 15:13:50','admin@sta.co.kr','2015-04-17 15:46:08','','','','',''),(2,1,'테스트데이터','TC_ITEM','N','N','textarea','','default','Y','admin@sta.co.kr','2015-04-17 15:13:50','admin@sta.co.kr','2015-04-17 15:46:08','','','','',''),(3,1,'실행절차','TC_ITEM','N','N','textarea','','default','Y','admin@sta.co.kr','2015-04-17 15:13:50','admin@sta.co.kr','2015-04-17 15:46:08','','','','',''),(4,1,'예상결과','TC_ITEM','N','N','textarea','','default','Y','admin@sta.co.kr','2015-04-17 15:13:50','admin@sta.co.kr','2015-04-17 15:46:08','','','','',''),(5,1,'비고','TC_ITEM','N','N','textarea','','default','Y','admin@sta.co.kr','2015-04-17 15:13:50','admin@sta.co.kr','2015-04-17 15:46:08','','','','','')";
		$this->db->query($str_sql);
	}
}

/* End of file 001_Otm.php */
/* Location: ./application/libraries/001_Otm.php */