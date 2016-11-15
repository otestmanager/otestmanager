<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Login
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Login extends Controller
{
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->driver('session');
		$this->load->database();
		$this->load->model('login_m');

		switch($this->session->userdata('mb_lang')){
			case "ko":
				$this->config->set_item('language', 'korean');
			break;
			case "en":
			default:
				$this->config->set_item('language', 'english');
			break;
		}
	}

	/**
	* Function index
	*
	* @return string
	*/
	public function index()
	{
		$this->load->view('login_v');
	}

	/**
	* Function login_check
	*
	* @return string
	*/
	public function login_check()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('mb_email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('mb_pw', 'Password', 'required');//alpha_numeric

		if($this->form_validation->run() === TRUE){
			$auth_data = array(
				'mb_email' => $this->input->post_get('mb_email', TRUE),
				'mb_pw' => $this->input->post_get('mb_pw', TRUE)
			);

			$result = $this->login_m->login($auth_data);

			if($result != "" && $result->mb_email){
				$newdata = array(
					'mb_email'		=> $result->mb_email,
					'logged_in'		=> TRUE,
					'mb_name'		=> $result->mb_name,
					'mb_lang'		=> $result->mb_lang,
					'mb_is_admin'	=> $result->mb_is_admin
				);
				$this->session->set_userdata($newdata);
				echo 'Login';
				exit;
			}
			else{
				$to_day = date("Y-m-d H:i:s");
				$str = '<font color=red>Login False ('.$to_day.')</font>';
				echo $str;
				//echo '<font color=red>Login False ('.$to_day.')</font>';
				exit;
			}
		}
		else{
			$str = '<font color=red>'.validation_errors().'</font>';
			echo $str;
			//echo '<font color=red>'.validation_errors().'</font>';
			exit;
		}
	}

	/**
	* Function logout
	*
	* @return string
	*/
	public function logout()
	{
		$this->session->sess_destroy();
		echo 'logout';
		exit;
	}
}
//End of file Login.php
//Location: ./controllers/Login.php