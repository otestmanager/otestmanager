<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_defect extends CI_Migration {
	
	public function up()
	{
		/** 
		* otm_project_defect_workflow
		*/
		$str_sql = 'create table if not exists otm_project_defect_workflow (
					pdw_seq int(11) not null auto_increment,
					otm_project_pr_seq int(11) not null,
					otm_role_rp_seq int(11) not null,
					otm_project_code_pco_seq_from int(11) not null,
					otm_project_code_pco_seq_to int(11) not null,
					pdw_value char(1) default "N",
					primary key(pdw_seq)
				)';
		$this->migration->check_table('otm_project_defect_workflow', $str_sql);
		

		/**
		* OTM Defect Workflow
		*/
		$str_sql = 'create table if not exists otm_defect_workflow (
					dw_seq int(11) not null auto_increment,
					otm_role_rp_seq int(11) not null,
					otm_code_co_seq_from int(11) not null,
					otm_code_co_seq_to int(11) not null,
					dw_value char(1) default "N",
					primary key(dw_seq)
				)';
		$this->migration->check_table('otm_defect_workflow', $str_sql);

		/**
		* OTM Defect
		*/
		$str_sql = 'create table if not exists otm_defect (
					df_seq int(11) not null auto_increment,
					otm_project_pr_seq int(11) not null,
					otm_testcase_result_tr_seq int(11),
					df_id varchar(255),
					df_subject varchar(255) not null,
					df_description text,					
					df_severity varchar(255) not null,
					df_priority varchar(255) not null,
					df_frequency varchar(255) not null,
					writer varchar(255) not null,
					regdate datetime not null,
					last_writer varchar(255),
					last_update datetime,
					df_1 text default "",
					df_2 text default "",
					df_3 text default "",
					df_4 text default "",
					df_5 text default "",
					primary key (df_seq)
			)';
		$this->migration->check_table('otm_defect', $str_sql);
		
		/**
		* OTM Defect Comment
		*/
		$str_sql = 'create table if not exists otm_defect_comment (
					dc_seq int(11) not null auto_increment,
					otm_defect_df_seq int(11) not null,
					dc_description text,
					writer varchar(255) not null,
					regdate datetime not null,
					last_writer varchar(255),
					last_update datetime,
					primary key (dc_seq)
			)';
		$this->migration->check_table('otm_defect_comment', $str_sql);
		
		/**
		* OTM Defect Assign
		*/
		$str_sql = 'create table if not exists otm_defect_assign (
					dc_seq int(11) not null auto_increment,
					otm_defect_df_seq int(11) not null,
					dc_from varchar(60),
					dc_to varchar(60),
					dc_start_date datetime,
					dc_end_date datetime,
					dc_regdate datetime,
					dc_description text,
					dc_previous_status_co_seq int(11),
					dc_current_status_co_seq int(11),
					dc_1 text default "",
					dc_2 text default "",
					dc_3 text default "",
					dc_4 text default "",
					dc_5 text default "",
					primary key (dc_seq)
				)';
		$this->migration->check_table('otm_defect_assign', $str_sql);

		/**
		* OTM Defect Custom Value
		*/
		$str_sql = 'create table if not exists otm_defect_custom_value (
					cv_seq int(11) not null auto_increment,
					otm_defect_df_seq int(11) not null,
					otm_project_customform_pc_seq int(11) not null,
					cv_custom_type varchar(255) not null,
					cv_custom_value text,
					cv_1 text default "",
					cv_2 text default "",
					cv_3 text default "",
					cv_4 text default "",
					cv_5 text default "",
					primary key (cv_seq)
			)';
		$this->migration->check_table('otm_defect_custom_value', $str_sql);


		/**
		* OTM Defect History
		* - action_type varchar(30),
		*/
		$str_sql = 'create table if not exists otm_defect_historys (
					dh_seq int(11) not null auto_increment,
					otm_project_pr_seq int(11) not null default 0,
					otm_defect_df_seq int(11) not null,
					writer varchar(255) not null,
					regdate datetime not null,
					dh_1 text default "",
					dh_2 text default "",
					dh_3 text default "",
					dh_4 text default "",
					dh_5 text default "",
					primary key (dh_seq)
			)';
		$this->migration->check_table('otm_defect_historys', $str_sql);


		/**
		* OTM Defect History Details
		* action_type : status,priority, ... , assign, file, ...
		*/
		$str_sql = 'create table if not exists otm_defect_history_details (
					dhd_seq int(11) not null auto_increment,
					otm_defect_historys_dh_seq int(11) not null,
					action_type varchar(30) not null,
					old_value	text,
					value		text,
					dhd_1 text default "",
					dhd_2 text default "",
					dhd_3 text default "",
					dhd_4 text default "",
					dhd_5 text default "",
					primary key (dhd_seq)
			)';
		$this->migration->check_table('otm_defect_history_details', $str_sql);


		/**
		* OTM New Table
		*/

		$this->insert_default_data();
		$this->insert_sample_data();
	}

	public function down()
	{
		//$this->dbforge->drop_table('table_name');
	}

	function insert_default_data()
	{
		//otm_admin
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '1', '2', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '1', '3', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '1', '4', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '1', '5', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '1', '6', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '1', '7', '1')");

		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '2', '1', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '2', '3', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '2', '4', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '2', '5', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '2', '6', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '2', '7', '1')");

		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '3', '1', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '3', '2', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '3', '4', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '3', '5', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '3', '6', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '3', '7', '1')");

		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '4', '1', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '4', '2', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '4', '3', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '4', '5', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '4', '6', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '4', '7', '1')");

		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '5', '1', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '5', '2', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '5', '3', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '5', '4', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '5', '6', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '5', '7', '1')");

		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '6', '1', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '6', '2', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '6', '3', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '6', '4', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '6', '5', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '6', '7', '1')");

		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '7', '1', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '7', '2', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '7', '3', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '7', '4', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '7', '5', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('1', '7', '6', '1')");


		//test_leader
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('4', '1', '2', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('4', '1', '3', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('4', '4', '5', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('4', '4', '6', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('4', '5', '4', '1')");

		//tester
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('5', '1', '2', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('5', '1', '3', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('5', '4', '5', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('5', '4', '6', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('5', '5', '4', '1')");

		//defect_manager
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('6', '2', '3', '1')");
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('6', '2', '4', '1')");	
		
		//developer
		$this->db->query("insert into otm_defect_workflow (otm_role_rp_seq,otm_code_co_seq_from,otm_code_co_seq_to,dw_value) values('7', '2', '4', '1')");	
	}
	function insert_sample_data()
	{
		$default_writer = "admin@sta.co.kr";
		$str_sql = "INSERT INTO otm_project_defect_workflow VALUES (1,1,1,1,2,'1'),(2,1,1,1,3,'1'),(3,1,1,1,4,'1'),(4,1,1,1,5,'1'),(5,1,1,1,6,'1'),(6,1,1,2,1,'1'),(7,1,1,2,3,'1'),(8,1,1,2,4,'1'),(9,1,1,2,5,'1'),(10,1,1,2,6,'1'),(11,1,1,3,1,'1'),(12,1,1,3,2,'1'),(13,1,1,3,4,'1'),(14,1,1,3,5,'1'),(15,1,1,3,6,'1'),(16,1,1,4,1,'1'),(17,1,1,4,2,'1'),(18,1,1,4,3,'1'),(19,1,1,4,5,'1'),(20,1,1,4,6,'1'),(21,1,1,5,1,'1'),(22,1,1,5,2,'1'),(23,1,1,5,3,'1'),(24,1,1,5,4,'1'),(25,1,1,5,6,'1'),(26,1,1,6,1,'1'),(27,1,1,6,2,'1'),(28,1,1,6,3,'1'),(29,1,1,6,4,'1'),(30,1,1,6,5,'1'),(31,1,4,1,2,'1'),(32,1,4,1,3,'1'),(33,1,4,4,5,'1'),(34,1,4,4,6,'1'),(35,1,4,5,4,'1'),(36,1,5,1,2,'1'),(37,1,5,1,3,'1'),(38,1,5,4,5,'1'),(39,1,5,4,6,'1'),(40,1,5,5,4,'1'),(41,1,6,2,3,'1'),(42,1,6,2,4,'1'),(43,1,7,2,4,'1')";
		$this->db->query($str_sql);

		$str_sql = "INSERT INTO otm_defect VALUES (1,1,27,'df_1','[share] 아이폰 보호자 추가시 404 결함 발생','1. 사전조건 : \r\n\r\n2. 재현절차 : \r\n1) 보호자 초대로 이동 \r\n2) 연락처에서 선택 \r\n3) 아이폰 유저에게 연락처 선택하여 메시지 전송 \r\n4) 아아폰 유저 전송받은 내용으로 링크 클릭 \r\n\r\n\r\n3. 실제결과 : \r\n\r\n메시지가 아이폰용과 안드로이드용 메시지 두가지가 한꺼번에 전송되며 \r\n안드로이드 링크 클릭시 404화면 노출 \r\n아이폰용 링크 클릭시에도 드롭박스 인덱스 화면이 나타나서 이용자가 사용이 매우 어려움 \r\n4. 기대결과 : \r\n라이너블 설치 또는 보호자 등록화면이 노출됨','7','12','16','jypark@conkrit.com','2015-04-17 16:38:25','jypark@conkrit.com','2015-04-17 16:38:25','','','','',''),(2,1,29,'df_2','[HELP] 자주 묻는 질문은 웹과 연동하지 않고 바로 볼 수 있도록 개선','1. 사전조건 : 리니어블 앱 실행 \r\n\r\n2. 재현절차 : \r\n1) 메뉴에 도움말 선택 \r\n2) 자주묻는 질문 선택 \r\n\r\n3. 실제결과 : 웹과 연동하여 나옴 \r\n\r\n4. 기대결과 : 웹과 연동하지 않고 바로 볼 수 있도록','10','13','16','jypark@conkrit.com','2015-04-17 16:39:07','jypark@conkrit.com','2015-04-17 16:39:07','','','','',''),(3,1,50,'df_3','[위치정보] 내 아이 상태 표시 오류 발생됨','1. 사전조건 : 제품 등록 \r\n\r\n2. 재현절차 : 어플 실행 > 실제 내아이 사정거리서 떨어짐 > 경고음 발생 > Lineable 내 아이 상태에서는 \"떨어져 있음\"으로 표기 되나 상세보기(Info)에서는 \"나와 함께 있음\" 다르게 표기됨 \r\n\r\n3. 실제결과 : 내 아이 상태 동일하게 표기되도록 수정바람 \r\n\r\n4. 기대결과 : 메인 내아이 표기와 Info 표기가 같아야함 \r\n\r\n5. 발생빈도(n회/10회) : 항상 \r\n\r\n6. 추가정보(Device 종류, 버전) : VEGA IRON1 (IM-A870S, KK 4.4.2) \r\n\r\n7. 파일첨부(Y/N) :','10','12','16','nylee@conkrit.com','2015-04-17 16:42:13','nylee@conkrit.com','2015-04-17 16:42:13','','','','',''),(4,1,55,'df_4','[미아감지]A 단말기로 미아신고시 B단말기 옆 리니어블 있었으나 인식하지 못함','1. 사전조건 : 리니어블 앱 실행 \r\n\r\n2. 재현절차 : \r\n1) A 핸드폰으로 신고하기 \r\n2)B 핸드폰 옆에 리니어블있음 \r\n\r\n3. 실제결과 : 리니어블을 인식하지 못함. 서버상 문제인지 시간대의 문제 인지 알 수 없으나 동일 조건으로 몇시간 전에는 인식했음 \r\n\r\n4. 기대결과 : 시간과 공간에 상관없이 인식해야함 \r\n\r\n5. 발생빈도(n회/10회) : \r\n\r\n6. 추가정보(Device 종류, 버전) : VEGA LTE A/4.4.2 \r\n\r\n7. 파일첨부(Y/N) :N ','10','12','16','nylee@conkrit.com','2015-04-17 16:43:01','nylee@conkrit.com','2015-04-17 16:43:01','','','','',''),(5,1,56,'df_5','[미아감지] 신고된 미아 감지 후 보호자에게 연락시 연결 불가능','1. 사전조건 : A,B두 핸드폰에 리니어블 앱 실행 \r\n\r\n2. 재현절차 : \r\n1) A 핸드폰에서 내 아이 신고하기 \r\n2) B 핸드폰에서 아이와 가까이있다고 메시지 표출 \r\n3)보호자에게 전화걸기 \r\n\r\n3. 실제결과 : 미아아동의 보호자 정보를 가져오지 못하였다는 메시지 표출 \r\n\r\n4. 기대결과 : 전화가 걸려야함 \r\n\r\n5. 발생빈도(n회/10회) : 10/10 \r\n\r\n6. 추가정보(Device 종류, 버전) : VEGA LTE A/4.4.2 \r\n\r\n7. 파일첨부(Y/N) :N','10','12','16','nylee@conkrit.com','2015-04-17 16:43:22','nylee@conkrit.com','2015-04-17 16:43:22','','','','',''),(6,1,75,'df_6','[Sign] 회원가입과 비밀번호 변경의 PW 자릿수 제한이 다릅니다.','[TC_번호] : \r\n\r\n\r\n1. 사전조건 : \r\n\r\n2. 재현절차 : \r\n1. 회원가입에서 ID 입력 후 PW에 1111 와 같이 입력 \r\n2. 비밀번호 변경에서 aaaa로 변경 \r\n3. 실제결과 : \r\n1. 회원가입 성공 \r\n2. 비밀번호 변경 불가 (6자리 이상 얼럿) \r\n4. 기대결과 : \r\n회원가입/비밀번호 변경 동일한 자릿수로 통일 필요 \r\n추가적으로 보완을 위해 연속된 글자수를 비밀번호로 할 경우 막는게 좋을 듯 싶습니다. \r\n5. 발생빈도(n회/10회) : \r\n\r\n6. 추가정보(Device 종류, 버전) : \r\n\r\n7. 파일첨부(Y/N) :','11','15','16','dwkang@conkrit.com','2015-04-17 16:45:55','dwkang@conkrit.com','2015-04-17 16:45:55','','','','',''),(7,1,78,'df_7','[Product] 프로필 이미지 등록 선택 화면의 레이어 불일치','[TC_번호] : \r\n\r\n\r\n1. 사전조건 : \r\n\r\n2. 재현절차 : \r\n1) 제품등록화면 이동 \r\n2) 남자 이미지 선택 \r\n\r\n3. 실제결과 : \r\n전체로 원이 오버랩이 되어야하는데 1/3만 오버랩 됨 \r\n\r\n4. 기대결과 : \r\n전체로 원이 오버랩이 정확히 됨 \r\n\r\n5. 발생빈도(n회/10회) : \r\n항상 \r\n6. 추가정보(Device 종류, 버전) : \r\n갤럭시S5, 4.4.2 \r\n7. 파일첨부(Y/N) : \r\nN','11','12','16','dwkang@conkrit.com','2015-04-17 16:46:45','dwkang@conkrit.com','2015-04-17 16:46:45','','','','',''),(8,1,81,'df_8','[Protector] 보호자 드래그 혹은 삭제 시도 시 앱 종료','[TC_번호] : \r\n\r\n\r\n1. 사전조건 : 리니어블 등록 및 보호자 추가 \r\n\r\n2. 재현절차 : \r\n1. 리니어블에서 내아이 클릭 \r\n2. 설정>보호자 선택 \r\n3. 보호자 리스트에서 좌우로 드래그 \r\n4. 보호자 리스트에서 길게 선택하여 삭제 시도 \r\n3. 실제결과 : (앱 종료) \r\ncom.reverth.lineable 프로세스가 중지되었습니다. \r\nLineable이 중지되었습니다. \r\n4. 기대결과 : \r\n정상 삭제 \r\n5. 발생빈도(n회/10회) : \r\n50% 정도 \r\n6. 추가정보(Device 종류, 버전) : \r\n\r\n7. 파일첨부(Y/N) :','7','12','17','dwkang@conkrit.com','2015-04-17 16:47:23','dwkang@conkrit.com','2015-04-17 16:47:23','','','','',''),(9,1,82,'df_9','[Protector] \'2차 보호자 추가\' 항목 반복 실행 시 비정상 종료','[TC_번호] : 150112-03 \r\n\r\n1. 사전조건 : 어플 로그인 완료 후 보호자, 아이 모두 등록되어야 함 \r\n\r\n2. 재현절차 : \r\n1. 아이를 선택 후 오른쪽 상단에 SEtting 아이콘을 선택 \r\n2. \'보호자\' 항목을 반복 선택(약 10회 -15회) ( \'보호자 선택\' 다시 \'뒤로\' 반복 입력) \r\n- 또는 리니어블, 위험경보, 다른 항목들을 선택 후 보호자를 선택 \r\n\r\n3. 실제결과 : 어플 비정상 종료되며 1회 자동으로 재실행 되나 비정상 종료 되며 경고 팝업 창 출력됨 \r\n\r\n4. 기대결과 : 비정상 종료되지 않아야 함 \r\n\r\n5. 발생빈도(n회/10회) : 3회/5회 \r\n\r\n6. 추가정보(Device 종류, 버전) : 소니 Z2 / 안드로이드 4.4.2 \r\n\r\n7. 파일첨부(Y/N) : N','7','12','17','dwkang@conkrit.com','2015-04-17 16:47:54','dwkang@conkrit.com','2015-04-17 16:47:54','','','','',''),(10,1,85,'df_10','[알람] 알람 소리가 통화 중일때 수신기를 통해 들림','[TC_번호] : \r\n\r\n1. 사전조건 : 리니어블 팔찌가 설정된 감지 거리 이상으로 멀리 떨어져 있음. 경보음 사운드 2선택(북소리) \r\n\r\n2. 재현절차 : \r\n1) 위험경보 알람이 울리고 있음 \r\n2) 내 폰에 전화가 걸려옴. 전화를 수신하여 통화함 \r\n3) 전화를 끊음 \r\n4) 위험경보 알람이 계속 울리는 것을 확인함 \r\n5) 내가 전화를 다른 사람에게 건다 \r\n6) 통화를 하고 전화를 끊는다 \r\n\r\n\r\n3. 실제결과 : 재현절차 2번에서 귀를 대고 있는 수신기를 통해 상대방의 말소리와 함께 경보음이 같이 들림. \r\n휴대폰의 외부 스피커로는 들리지 않음(통화도중 휴대폰을 멀리하면 경보음이 안 들리고, 귀에 폰을 대면 들림) \r\n재현절차 3번에서 전화를 끊으면 다시 휴대폰 외부 스피커로 경보음이 들림 \r\n6번에서 다른 사람이 전화를 받음과 동시에 경보음은 다시 수신기 내의 스피커로 상대방 목소리와 함께 들림. \r\n상대방은 경보음이 들리지는 않는다고 함 \r\n\r\n4. 기대결과 : 경보음은 외부 스피커로만 들려야 함. 휴대폰 시계 알람처럼.. 테스트 몇 번 했더니 귀가 아픔... ㅜㅜ \r\n\r\n5. 발생빈도(n회/10회) : 항상 재연 \r\n\r\n6. 추가정보(Device 종류, 버전) :갤럭시 노트2, Android 4.4.2 \r\n\r\n7. 파일첨부(Y/N) : N','8','12','16','dwkang@conkrit.com','2015-04-17 16:48:27','dwkang@conkrit.com','2015-04-17 16:48:27','','','','',''),(11,1,142,'df_11','[Help] 도움말 일부 글자 깨짐 발생','1. 사전조건 : 제품 등록, English 설정상태 \r\n\r\n2. 재현절차 : 어플 진입 > 메뉴창 > Help & About > about > Terms & Conditions, Proivacy Policy \r\n\r\n3. 실제결과 : 영문으로 진입시 이용 약관, 개인정보 보호정책 내용 일부 깨짐 \r\n\r\n4. 기대결과 : 깨지는 글자 없이 디스플레이 요함','10','12','16','tjsoe@conkrit.com','2015-04-17 16:54:27','tjsoe@conkrit.com','2015-04-17 16:54:27','','','','',''),(12,1,143,'df_12','[HELP] 자주 묻는 질문 한글화 필요함','1. 사전조건 : 없음 \r\n\r\n2. 재현절차 : 도움말 > 자주묻는 질문 클릭 \r\n\r\n3. 실제결과 : 영문으로 되어 있음 \r\n\r\n4. 개선 사항 : 한글화 작업이 필요합니다.','11','14','16','tjsoe@conkrit.com','2015-04-17 16:54:51','tjsoe@conkrit.com','2015-04-17 16:54:51','','','','',''),(13,1,145,'df_13','[shop] 구매화면에서 상하 좌우 스크롤의 제약으로 전체 화면을 볼 수 없는 현상','1. 사전조건 : \r\n\r\n2. 재현절차 : \r\n1) 리니어블 메뉴 클릭 \r\n2) 구매 메뉴 클릭 \r\n3) 구매 화면에서 상하좌우 스크롤 시도 \r\n\r\n3. 실제결과 : \r\n최상위로 스크롤이 끝까지 되지 않고 양 옆으로 스크롤이 되지 않아 이미지도 잘려보입니다. \r\n문장당 각 왼쪽 마진도 0라서 가독성에도 문제 발생 \r\n\r\n4. 기대결과 : \r\n상하좌우 스크롤 또는 화면 스크롤 이동으로 전체 화면을 볼 수 있다. \r\n또는 모바일 화면에 맞게 화면이 반응형웹형태로 구현 필요','10','13','16','tjsoe@conkrit.com','2015-04-17 16:55:18','tjsoe@conkrit.com','2015-04-17 16:55:18','','','','',''),(14,1,250,'df_14','[shop] 배송료 정책 적용 안됨','[TC_번호] : \r\n1. 사전조건 : 리니어블 앱 설치 및 로그인 > 제품등록 + 메뉴 선택 \r\n\r\n2. 재현절차 : \r\n1) 제품 등록 후 구매 메뉴 선택 \r\n2) 영어 쇼핑몰 화면에서 \r\n3) 50개 패키지 한개 선택 후 카트 보기 \r\n4) 50개 2개 더 선택 후 카트 보기 \r\n5) 리니어블 1개 선택 후 카트 보기 \r\n6) 20개 패키지 1개 더 선택 후 카트 보기 \r\n\r\n3. 실제결과 : 50개 패키지 3개 선택할 때까지 $750에 배송료 free, 1개를 더 추가하니 $755 가 되면서 배송료가 $3이 추가됨 \r\n20개 추가 선택 후 카트 보면 $855 총액에 배송료는 $10 이 됨(20개 선택 시 배송료 $7가 그대로 추가된 것임) \r\n\r\n4. 기대결과 : 50개 패키지가 추가되면 그 이후 어떠한 구매 구성이 되더라도 배송지가 하나라면 배송료는 그대로 free여야 함. \r\n또한 20개, 1개를 한 배송지로 주문하더라도 10$가 아닌 한 쪽의 배송료가 적용되어야 함','8','12','16','shsong@conkrit.com','2015-04-19 11:57:14','shsong@conkrit.com','2015-04-19 11:57:14','','','','',''),(15,1,256,'df_15','[Sign] ID, PWD 입력창 자리수 제한필요','[TC_번호] : \r\n\r\n\r\n1. 사전조건 : \r\n\r\n2. 재현절차 : \r\n1) 로그인 화면 이동 \r\n2) ID, PWD입력창에 무작위로 입력 \r\n\r\n3. 실제결과 : \r\n확인 버튼 누르기 전에 입력값이 무제한으로 입력됨 \r\n\r\n4. 기대결과 : \r\n최초 자리수 제한만큼만 글자가 입력되게 개선 필요 \r\n\r\n5. 발생빈도(n회/10회) : \r\n항상 \r\n6. 추가정보(Device 종류, 버전) : \r\n갤럭시S5, 4.4.2 \r\n7. 파일첨부(Y/N) :N','10','15','16','jygo@conkrit.com','2015-04-19 11:59:28','jygo@conkrit.com','2015-04-19 11:59:28','','','','',''),(16,1,257,'df_16','[Sign] Login 창 관련 문제','[TC_번호] : \r\n\r\n1. 사전조건 : \r\n2. 재현절차 : App 실행 \r\n3. 실제결과 : 자동로그인 체크박스가 존재 하지 않음 \r\n4. 기대결과 : 체크박스로 자동로그인이 되어야 할 것으로 판단됨 \r\n5. 발생빈도(n회/10회) : Always \r\n6. 추가정보(Device 종류, 버전) : Galuxy Note4, 4.4.4 \r\n7. 파일첨부(Y/N) : N','11','15','16','jygo@conkrit.com','2015-04-19 11:59:53','jygo@conkrit.com','2015-04-19 11:59:53','','','','',''),(17,1,258,'df_17','[Sign] 로그아웃 관련','[TC_번호] : \r\n1. 사전조건 : 사용자가 App Login 된 상태 > 왼쪽 상단 아이콘 터치 \r\n2. 재현절차 : 로그아웃 버튼 선택 \r\n3. 실제결과 : 취소 버튼이 존재 하지 않음(확인 버튼만 존재함) \r\n4. 기대결과 : Back Key 인가시 취소가 되지만 취소 버튼이 있어야 함 \r\n5. 발생빈도(n회/10회) : Always \r\n6. 추가정보(Device 종류, 버전) : Galuxy Note4, 4.4.4 \r\n7. 파일첨부(Y/N) : N','10','15','16','jygo@conkrit.com','2015-04-19 12:00:20','jygo@conkrit.com','2015-04-19 12:00:20','','','','',''),(18,1,259,'df_18','[Product] 두번째 제품 등록시 화면상에 HTML코드가 노출되는 현상','[TC_번호] : \r\n\r\n\r\n1. 사전조건 : \r\n리니어블 1개 선 등록 \r\n2. 재현절차 : \r\n1) 리니어블 두번째 검색 후 등록 \r\n2) 등록 시 이름을 첫번째와 동일하게 입력 \r\n3) 등록확인 버튼 클릭 \r\n\r\n3. 실제결과 : \r\n실제로 등록은 되나 화면상에 HTML코드가 나타나며 등록확인을 클릭 시 해당 화면이 노출되어 실제 등록 여부를 당시에는 알 수 가 없음 \r\n같은 이름으로 중복 등록이 됨 \r\n\r\n4. 기대결과 : \r\n동일한 이름이 있다라고 경고메세지와 함께 등로기 되지 않음 \r\n\r\n5. 발생빈도(n회/10회) : \r\n항상 \r\n6. 추가정보(Device 종류, 버전) : \r\n갤럭시S5, 4.4.2 \r\n7. 파일첨부(Y/N) :','8','12','16','jygo@conkrit.com','2015-04-19 12:00:45','jygo@conkrit.com','2015-04-19 12:00:45','','','','',''),(19,1,260,'df_19','[Product] 제품 등록 시 문제','[TC_번호] : \r\n\r\n1. 사전조건 : 사용자가 App Login 된 상태 > 왼쪽 상단 아이콘 터치 \r\n2. 재현절차 : 최초실행 > 리니어블 > 등록 \r\n3. 실제결과 : 사진 아이콘에 UI가 정확하게 겹쳐지지 않음 \r\n4. 기대결과 : 사진 아이콘에 UI가 정확하게 겹쳐져야 함 \r\n5. 발생빈도(n회/10회) : Always \r\n6. 추가정보(Device 종류, 버전) : Galuxy Note4, 4.4.4 \r\n7. 파일첨부(Y/N) : N','10','15','16','jygo@conkrit.com','2015-04-19 12:01:07','jygo@conkrit.com','2015-04-19 12:01:07','','','','',''),(20,1,261,'df_20','[Product] 제품등록(아이) 해제 시 실패했다는 팝업 창이 출력됨','[TC_번호] : 150116-04 \r\n\r\n1. 사전조건 : 어플 로그인 완료 후 보호자, 아이 모두 등록되어야 함 \r\n\r\n2. 재현절차 : \r\n1. 아이를 선택 후 길게 눌름 \r\n2. 목록에서 삭제하시겠습니까 라는 팝업 창 출력됨 \r\n3. 확인 버튼 입력 \r\n\r\n3. 실제결과 : \'아이를 해제하는데 실패하였습니다\' 팝업창이 출력됨 \r\n\r\n4. 기대결과 : 정상적으로 해제되었다는 팝업 창이 출력되고 기능도 정상적으로 동작해야함 \r\n\r\n5. 발생빈도(n회/10회) : 항상 \r\n\r\n6. 추가정보(Device 종류, 버전) : 소니 Z2 / 안드로이드 4.4.2 \r\n\r\n7. 파일첨부(Y/N) : N','8','13','16','jygo@conkrit.com','2015-04-19 12:01:31','jygo@conkrit.com','2015-04-19 12:01:31','','','','',''),(21,1,262,'df_21','[Protector] 보호자 초대 메시지 순서 바뀜 및 링크깨짐','[TC_번호] : 1. 사전조건 : 로그인 > 리니어블 등록 \r\n\r\n2. 재현절차 : \r\n1) 리니어블 1이 등록되어 있음 \r\n2) 리니어블 1 선택 \r\n3) 설정 선택 \r\n4) 보호자 선택 \r\n5) 김희선(주 보호자) 확인 \r\n6) + 선택 \r\n7) \"연락처에서 선택\"에서 보호자로 추가할 연락처를 선택 \r\n8) 보호자 초대를 완료하였습니다. 팝업 창 표시됨 \r\n9) 확인 클릭 \r\n10) 초대한 보호자의 폰에서 메시지 확인 \r\n\r\n\r\n\r\n3. 실제결과 : \r\n1) 금요일 이전, 토요일, 일요일 메시지 전송 순서 및 유형이 좀 바뀜(전송 메시지를 수정하신건가요??) \r\n=> 첨부 파일 참조(첨부파일 이름 날짜(사양).xxx). 같은 날 노트2가 받은 메시지와 노트3가 받은 메시지가 약간 다름(\'web발신\' 글자 추가) \r\n2) 링크가 먼저 오고 초대 메시지가 나중에 오기도 함 \r\n3) 링크 메시지가 2개의 메시지로 끊어져서 전송됨 \r\n\r\n4. 기대결과 : \r\n1) 메시지 전송 순서는 초대 메시지 다음에 링크가 오는 순서가 맞음 \r\n2) 링크 메시지는 끊어져서 전송되어서는 안 됨 \r\n\r\n5. 발생빈도(n회/10회) : 2015.1.17(토) 부터 항상 \r\n\r\n6. 추가정보(Device 종류, 버전) :갤럭시 노트2, Android 4.4.2 \r\n갤럭시 노트3, Android 4.3 \r\n7. 파일첨부(Y/N) : N','8','12','16','jygo@conkrit.com','2015-04-19 12:01:54','jygo@conkrit.com','2015-04-19 12:01:54','','','','',''),(22,1,263,'df_22','[Protector] 보호자 추가 요청 시 주소창 연결 오류','[TC_번호] : \r\n1. 사전조건 : 앱 로그인 후 리니어블 1 등록 \r\n\r\n2. 재현절차 : \r\n1) 리니어블 1 선택 \r\n2) 설정 선택 \r\n3) 보호자 선택 \r\n4) + 선택 \r\n5) 연락처에서 검색 \r\n6) 내 이름 검색하여 선택 \r\n\r\n3. 실제결과 : \r\n1) 보호자 초대 및 앱 다운로드 URL 주소 메시지를 수신함 \r\niPhone 사용자 다운로드 주소 클릭 \r\nAndroid 사용자 다운로드 주소 클릭 \r\n\r\n4. 기대결과 : \r\n1) iPhone 사용자 다운로드 주소 연결 정상 \r\n2) Android 사용자 다운로드 주소 연결 실패 (404 오류 메시지 보임) \r\n\r\n\r\n5. 발생빈도(n회/10회) : \r\n\r\n6. 추가정보(Device 종류, 버전) :갤럭시 노트2, Android 4.4.2 \r\n\r\n7. 파일첨부(Y/N) : N','10','14','16','jygo@conkrit.com','2015-04-19 12:02:18','jygo@conkrit.com','2015-04-19 12:02:18','','','','',''),(23,1,264,'df_23','[Protector] 보호자 드래그 혹은 삭제 시도 시 앱 종료','[TC_번호] : \r\n\r\n\r\n1. 사전조건 : 리니어블 등록 및 보호자 추가 \r\n\r\n2. 재현절차 : \r\n1. 리니어블에서 내아이 클릭 \r\n2. 설정>보호자 선택 \r\n3. 보호자 리스트에서 좌우로 드래그 \r\n4. 보호자 리스트에서 길게 선택하여 삭제 시도 \r\n3. 실제결과 : (앱 종료) \r\ncom.reverth.lineable 프로세스가 중지되었습니다. \r\nLineable이 중지되었습니다. \r\n4. 기대결과 : \r\n정상 삭제 \r\n5. 발생빈도(n회/10회) : \r\n50% 정도 \r\n6. 추가정보(Device 종류, 버전) : \r\n\r\n7. 파일첨부(Y/N) :','7','12','17','jygo@conkrit.com','2015-04-19 12:02:46','jygo@conkrit.com','2015-04-19 12:02:46','','','','',''),(24,1,266,'df_24','[알람] 알람 모드 선택 기능','[TC_번호] : \r\n\r\n1. 사전조건 : 리니어블 등록 > 아이선택 > 설정 > 위험경보 > 소리설정 선택 \r\n\r\n2. 재현절차 : \r\n1) 위험경보에서 진동과 사운드 선택이 가능함 \r\n\r\n3. 실제결과 : 진동과 사운드 중 한가지 씩만 선택이 가능 \r\n\r\n4. 개선사항 : 진동+사운드 모드가 함께 선택 가능한 설정이 있으면 좋겠음 \r\n\r\n5. 발생빈도(n회/10회) : 항상 재연 \r\n\r\n6. 추가정보(Device 종류, 버전) :갤럭시 노트2, Android 4.4.2 \r\n\r\n7. 파일첨부(Y/N) : N','10','15','16','jygo@conkrit.com','2015-04-19 12:03:13','jygo@conkrit.com','2015-04-19 12:03:13','','','','',''),(25,1,268,'df_25','[알람] 거리 인식에 따른 경보음과 상태 표시가 수시로 바뀜','[TC_번호] : \r\n\r\n1. 사전조건 : 거리감도 1/10로 설정 \r\n\r\n2. 재현절차 : \r\n1) 휴대폰으로 부터 350cm 떨어진 위치에 리니어블을 두고 경보음이 울리는지 확인 \r\n2) 530cm 떨어진 위치에 리니어블을 두고 경보음이 울리는지 확인 \r\n3) 30분 이상 두면서 상태 변화를 확인함 \r\n\r\n\r\n3. 실제결과 : 1번에서 경보음이 울리는 것을 확인 \r\n2번으로 위치를 옮기니 \"나와 함께 있음\"으로 상태가 바뀜. 30초에서 1분 후에 경보음이 울리며 \"떨어져 있음\"으로 표시됨 \r\n2번 위치에 계속 두고 상태 변화가 있는지 확인하니 수시로 \"나와 함께 있음\" \"떨어져 있음\"이 바뀌면서 표시됨 \r\n\r\n4. 기대결과 : 1번에서 2번으로 가면 여전히 경보음이 울리거나 떨어져 있다고 상태표시가 되어야 함. \r\n일정한 위치에 대한 상태 변화가 수시로 발생함. 심지어 signal을 보면 변화가 없는데도 상태표시는 바뀌기도 함. \r\n\r\n\r\n5. 발생빈도(n회/10회) : 일정하지 않게 수시로 결과가 바뀜 \r\n\r\n6. 추가정보(Device 종류, 버전) :갤럭시 노트2, Android 4.4.2 \r\n\r\n7. 파일첨부(Y/N) : N','7','12','18','jygo@conkrit.com','2015-04-19 12:03:40','jygo@conkrit.com','2015-04-19 12:03:40','','','','',''),(26,1,373,'df_26','[sign] 공란으로 회원가입의 경우 우선순위 문제','[TC_번호] : \r\n\r\n1. 사전조건 : \r\n2. 재현절차 : App 실행 > 신규가입 > 아무런 입력을 하지 않고 회원가입 버튼 선택 \r\n\r\n3. 실제결과 : \"아이디를 입력해주세요.\" 팝업이 생성됨 \r\n4. 기대결과 : \"이름을 입력해주세요.\" 팝업이 생성됨 (가장 최상위부터 팝업이 생성되어야 함) \r\n5. 발생빈도(n회/10회) : Always \r\n6. 추가정보(Device 종류, 버전) : Galuxy Note4, 4.4.4 \r\n7. 파일첨부(Y/N) : N','10','12','16','khkim@conkrit.com','2015-04-19 12:13:24','khkim@conkrit.com','2015-04-19 12:13:24','','','','',''),(27,1,374,'df_27','[Sign] Login 창 관련 문제','[TC_번호] : \r\n\r\n1. 사전조건 : \r\n2. 재현절차 : App 실행 \r\n3. 실제결과 : 자동로그인 체크박스가 존재 하지 않음 \r\n4. 기대결과 : 체크박스로 자동로그인이 되어야 할 것으로 판단됨 \r\n5. 발생빈도(n회/10회) : Always \r\n6. 추가정보(Device 종류, 버전) : Galuxy Note4, 4.4.4 \r\n7. 파일첨부(Y/N) : N','10','15','16','khkim@conkrit.com','2015-04-19 12:13:45','khkim@conkrit.com','2015-04-19 12:13:45','','','','',''),(28,1,375,'df_28','[SIGN] LOG OUT 후에도 Tab the Add button to add your first Lineable 메시지가 우측 상단에 계속 출력되어 있음','[TC_번호] : \r\n\r\n\r\n1. 사전조건 : Lineable SIGN UP 완료 후 Tab the Add button to add your first Lineable 메시지 출력 상태 \r\n\r\n2. 재현절차 : 네비게이션 메뉴 터치 -> LOG OUT 터치 \r\n\r\n3. 실제결과 : Tab the Add button to add your first Lineable 메시지가 우측 상단에 계속 출력되어 있음 \r\n\r\n4. 기대결과 : Tab the Add button to add your first Lineable 메시지 사라짐 \r\n\r\n5. 발생빈도(n회/10회) : always \r\n\r\n6. 추가정보(Device 종류, 버전) : iPhone 6+, iOS 8.1.2 \r\n\r\n7. 파일첨부(Y/N) : N','10','14','16','khkim@conkrit.com','2015-04-19 12:14:05','khkim@conkrit.com','2015-04-19 12:14:05','','','','',''),(29,1,376,'df_29','[Product]제품등록시 이름과 설명후 등록하면 에러문구 표출 후 등록되거나 강제종료됨 확인하면 등록 되어있음','[TC_번호] : \r\n\r\n\r\n1. 사전조건 :리니어블 앱 실행 \r\n\r\n2. 재현절차 : \r\n1)리니어블 제품 등록 \r\n2)설명 후 등록 \r\n\r\n3. 실제결과 : 1) 에러 메시지 표출 후 강제 종료됨 확인시 등록되어있음 \r\n2)다시 제거 후 등록 에러메시지는 표출되나 강제종료 되지 않고 뒤로바가 버튼시 등록되어있음 \r\n3)같은 기종의 핸드폰 중 한개만 증상 나타남 \r\n\r\n4. 기대결과 : 에러 메시지 표출 안되어야함 \r\n\r\n5. 발생빈도(n회/10회) : 항상/10 \r\n\r\n6. 추가정보(Device 종류, 버전) : VEGA LTE A/4.4.2 \r\n\r\n7. 파일첨부(Y/N) :','10','15','16','khkim@conkrit.com','2015-04-19 12:14:28','khkim@conkrit.com','2015-04-19 12:14:28','','','','',''),(30,1,378,'df_30','[Product] 제품등록(아이) 해제 시 실패했다는 팝업 창이 출력됨','[TC_번호] : 150116-04 \r\n\r\n1. 사전조건 : 어플 로그인 완료 후 보호자, 아이 모두 등록되어야 함 \r\n\r\n2. 재현절차 : \r\n1. 아이를 선택 후 길게 눌름 \r\n2. 목록에서 삭제하시겠습니까 라는 팝업 창 출력됨 \r\n3. 확인 버튼 입력 \r\n\r\n3. 실제결과 : \'아이를 해제하는데 실패하였습니다\' 팝업창이 출력됨 \r\n\r\n4. 기대결과 : 정상적으로 해제되었다는 팝업 창이 출력되고 기능도 정상적으로 동작해야함 \r\n\r\n5. 발생빈도(n회/10회) : 항상 \r\n\r\n6. 추가정보(Device 종류, 버전) : 소니 Z2 / 안드로이드 4.4.2 \r\n\r\n7. 파일첨부(Y/N) : N','8','12','16','khkim@conkrit.com','2015-04-19 12:14:58','khkim@conkrit.com','2015-04-19 12:14:58','','','','',''),(31,1,379,'df_31','[Protector] 자기자신에게도 보호자 초대 메시지 전송함','[TC_번호] : \r\n\r\n1. 사전조건 : 로그인 후 리니어블 1 등록 \r\n\r\n2. 재현절차 : \r\n1) 리니어블 1이 등록되어 있음 \r\n2) 리니어블 1 선택 \r\n3) 설정 선택 \r\n4) 보호자 선택 \r\n5) 김희선(주 보호자) 확인 \r\n6) + 선택 \r\n7) ID 검색에서 김희선의 아이디 sezsez 입력 \r\n8) 검색결과에서 김희선 sezsez 검색되고, 아래 초대 버튼 나옴 \r\n9) 초대버튼 클릭 \r\n10) 알림 메뉴 클릭 \r\n\r\n3. 실제결과 : \r\n1) 보호자 초대를 완료하였습니다 팝업 발생 \r\n2) 알림 메뉴에서 김희선이 당신을 OO의 보호자로 초대하였습니다.라는 메시지 확인됨 \r\n\r\n4. 기대결과 : \r\n1) 내 자신의 ID 검색 시 검색이 안 되어야 함 \r\n2) 검색이 되더라도 \"초대\" 버튼은 활성화되지 말아야 함 \r\n\r\n\r\n5. 발생빈도(n회/10회) : \r\n\r\n6. 추가정보(Device 종류, 버전) :갤럭시 노트2, Android 4.4.2 \r\n\r\n7. 파일첨부(Y/N) : N','11','12','16','khkim@conkrit.com','2015-04-19 12:15:24','khkim@conkrit.com','2015-04-19 12:15:24','','','','',''),(32,1,381,'df_32','[Protector] 2차 보호자 제거시 해체 실패 메시지 노출 후 리니어블은 삭제됨','[TC_번호] : \r\n\r\n\r\n1. 사전조건 : 리니어블 앱 실행 \r\n\r\n2. 재현절차 : \r\n1)2차 보호자가 설정에서 \r\n2) 리니어블삭제 \r\n\r\n3. 실제결과 : 아이를 해체하는데 실패하였다는 메시지 표출 후 리니어블 삭제됨 \r\n\r\n4. 기대결과 : 실패메시지 노출 안되야함 \r\n\r\n5. 발생빈도(n회/10회) : 항상/10 \r\n\r\n6. 추가정보(Device 종류, 버전) : VEGA LTE A/ 4.4.2 \r\n\r\n7. 파일첨부(Y/N) :N','10','12','16','khkim@conkrit.com','2015-04-19 12:15:47','khkim@conkrit.com','2015-04-19 12:15:47','','','','',''),(33,1,383,'df_33','[알람]알림이 진동으로 되어있을 시 통화중 리니어블과 멀어지는데 알람이 오지 않는 현상','[TC_번호] : \r\n\r\n\r\n1. 사전조건 : 리니어블 앱실행 \r\n\r\n2. 재현절차 : \r\n1) 알람은 진동 \r\n2) 보호자 통화중 \r\n3) 리니어블과 멀어짐 \r\n\r\n3. 실제결과 : 진동울리지 않음 \r\n\r\n4. 기대결과 : 진동으로 아이와 멀어졌다는 것을 알려줘야함 \r\n\r\n5. 발생빈도(n회/10회) : 1/10 \r\n\r\n6. 추가정보(Device 종류, 버전) : VEGA LTE A /4.4.2 \r\n\r\n7. 파일첨부(Y/N) :N','8','12','18','khkim@conkrit.com','2015-04-19 12:16:17','khkim@conkrit.com','2015-04-19 12:16:17','','','','',''),(34,1,385,'df_34','[알람] 리니어블이 바로 옆에 있을 시에도 알람 발생','[TC_번호] : \r\n\r\n\r\n1. 사전조건 : 리니어블 앱 실행 \r\n\r\n2. 재현절차 : 아이와 동행하며 바로 옆 리니어블이 있음 \r\n\r\n3. 실제결과 : 불규칙적으로 아이와 떨어졌다는 알람 발생 \r\n\r\n4. 기대결과 : 설정한 근거리에 리니어블이 있을 시 알람이 울리지 않아야함 \r\n\r\n5. 발생빈도(n회/10회) : 불규칙적으로 다수 \r\n\r\n6. 추가정보(Device 종류, 버전) : VEGA LTE A/ 4.4.2 \r\n\r\n7. 파일첨부(Y/N) :N','10','12','18','khkim@conkrit.com','2015-04-19 12:16:44','khkim@conkrit.com','2015-04-19 12:16:44','','','','','')";
		$this->db->query($str_sql);

		$str_sql = "INSERT INTO otm_defect_assign VALUES (1,1,'jypark@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-17 16:38:25',NULL,NULL,1,'','','','',''),(2,2,'jypark@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-17 16:39:07',NULL,NULL,1,'','','','',''),(3,3,'nylee@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-17 16:42:13',NULL,NULL,1,'','','','',''),(4,4,'nylee@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-17 16:43:01',NULL,NULL,1,'','','','',''),(5,5,'nylee@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-17 16:43:22',NULL,NULL,1,'','','','',''),(6,6,'dwkang@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-17 16:45:55',NULL,NULL,1,'','','','',''),(7,7,'dwkang@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-17 16:46:45',NULL,NULL,1,'','','','',''),(8,8,'dwkang@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-17 16:47:23',NULL,NULL,1,'','','','',''),(9,9,'dwkang@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-17 16:47:54',NULL,NULL,1,'','','','',''),(10,10,'dwkang@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-17 16:48:27',NULL,NULL,1,'','','','',''),(11,11,'tjsoe@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-17 16:54:27',NULL,NULL,1,'','','','',''),(12,12,'tjsoe@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-17 16:54:51',NULL,NULL,1,'','','','',''),(13,13,'tjsoe@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-17 16:55:18',NULL,NULL,1,'','','','',''),(14,14,'shsong@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 11:57:14',NULL,NULL,1,'','','','',''),(15,15,'jygo@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 11:59:28',NULL,NULL,1,'','','','',''),(16,16,'jygo@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 11:59:53',NULL,NULL,1,'','','','',''),(17,17,'jygo@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:00:20',NULL,NULL,1,'','','','',''),(18,18,'jygo@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:00:45',NULL,NULL,1,'','','','',''),(19,19,'jygo@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:01:07',NULL,NULL,1,'','','','',''),(20,20,'jygo@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:01:31',NULL,NULL,1,'','','','',''),(21,21,'jygo@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:01:54',NULL,NULL,1,'','','','',''),(22,22,'jygo@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:02:18',NULL,NULL,1,'','','','',''),(23,23,'jygo@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:02:46',NULL,NULL,1,'','','','',''),(24,24,'jygo@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:03:13',NULL,NULL,1,'','','','',''),(25,25,'jygo@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:03:40',NULL,NULL,1,'','','','',''),(26,26,'khkim@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:13:24',NULL,NULL,1,'','','','',''),(27,27,'khkim@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:13:45',NULL,NULL,1,'','','','',''),(28,28,'khkim@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:14:05',NULL,NULL,1,'','','','',''),(29,29,'khkim@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:14:28',NULL,NULL,1,'','','','',''),(30,30,'khkim@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:14:58',NULL,NULL,1,'','','','',''),(31,31,'khkim@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:15:24',NULL,NULL,1,'','','','',''),(32,32,'khkim@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:15:47',NULL,NULL,1,'','','','',''),(33,33,'khkim@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:16:17',NULL,NULL,1,'','','','',''),(34,34,'khkim@conkrit.com','','0000-00-00 00:00:00','0000-00-00 00:00:00','2015-04-19 12:16:44',NULL,NULL,1,'','','','','')";
		$this->db->query($str_sql);
	}
}