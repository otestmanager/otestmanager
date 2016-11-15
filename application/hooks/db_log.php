<?php
/*
 * This is our Db_log hook file
 *
 */

class Db_log {

    var $CI;

    function __construct() {
        $this->CI = & get_instance(); // Create instance of CI
    }

    function logQueries() {
		if(ENVIRONMENT === 'development')
		{
			$error_rep = error_reporting();
			error_reporting(0);
			chmod(APPPATH . 'logs', 0707);
			error_reporting($error_rep);

			$filepath = APPPATH . 'logs/QueryLog-' . date('Y-m-d') . '.log'; // Filepath. File is created in logs folder with name QueryLog
			$handle = fopen($filepath, "a+"); // Open the file

			$times = $this->CI->db->query_times; // We get the array of execution time of each query that got executed by our application(controller)

			$sql  = "";
			$check_sql = 0;
			foreach ($this->CI->db->queries as $key => $query) { // Loop over all the queries  that are stored in db->queries array
				$query = trim($query);
				$head = substr($query, 0, 6);

				if(strpos($head,'select') === false && strpos($head,'SELECT') === false && strpos($head,'show') === false  && strpos($head,'SHOW') === false) {
					$check_sql = $times[$key];

					$sql .= "===== Query Start =====\r\n";
					$sql .= "===== Execution Time : " . $times[$key] . " =====\r\n\r\n"; // Write it along with the execution time

					$sql .= $query . "\r\n\r\n";
					$sql .= "----- Query End ------------------------------------------------- \r\n\r\n";

				}
			}
			if($check_sql > 0){
				fwrite($handle, "############################# \r\n");
				fwrite($handle, "##### Call Method Start ##### \r\n");
				fwrite($handle, "############################# \r\n\r\n");

				$sql_head = "===== Time : ".date('Y-m-d H:i:s')." =====\r\n";
				$sql_head .= "===== User : ".$this->CI->session->userdata('mb_name')."(".$this->CI->session->userdata('mb_email').") ===== \r\n\r\n";

				fwrite($handle, $sql_head);
				fwrite($handle, $sql);
				fwrite($handle, "##### Call Method End ##### \r\n\r\n");
			}

			fclose($handle); // Close the file
		}
    }

}

/* End of file db_log.php */
/* Location: ./application/libraries/db_log.php */