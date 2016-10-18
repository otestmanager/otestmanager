<?php
class Session_login_check {
	var $CI;

    function __construct() {
        $this->CI = & get_instance(); // Create instance of CI
		//if(!isset($this->CI->session))  //Check if session lib is loaded or not
        //  $this->CI->load->library('session');  //If not loaded, then load it here

		//$this->CI->load->helper(array('url'));
    }

    function checkPermission() {
		//echo '<br>';
		//echo $this->CI->router->class;
		//echo '<br>';
		//echo $this->CI->router->method;

		//if($this->CI->router->class !== 'otm' AND $this->CI->router->class !== 'Login' ){
		//	if(!$this->CI->session->userdata('mb_email')){


				//echo $this->CI->router->class;
				//echo '<br>';
				//echo $this->CI->router->method;
				//echo '<br>';
				//echo site_url();
				//redirect('');
				//echo "Not login";
				//exit;
				//$this->CI->load->view('login_v');
		//	}

		//}
		//if (isset($CI->allow) && (is_array($CI->allow) === false OR in_array($CI->router->method, $CI->allow) === false)) {
        //	if($this->session->userdata('logged_in') !== TRUE){
				//redirect('', 'location');
			//}
		//}
    }
}
?>

