<?php
if (!defined('BASEPATH'))
     exit('No direct script access allowed');

class Sendmail extends Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('email');
	}

	function send($data)
	{
		$from = $data['from'];
		$to = $data['to'];
		$cc = $data['cc'];
		$bcc = $data['bcc'];
		$subject = $data['subject'];

		$message_head= "<table width='500px'><tr style='padding:3px;width:100%;border: 1px solid #2DA5DA;'><td style='padding:5;background-color:#2DA5DA;'><center><a href='http://".$_SERVER['HTTP_HOST']."'><img src='http://".$_SERVER['HTTP_HOST']."/resource/img/otm_logo1.png'></a></center></td></tr><tr><td>";
		$message = $message_head . $data['message'] . "</td></tr></table>";

		$this->email->initialize($config);
		$this->email->clear();
		$this->email->set_mailtype("html");
		$this->email->set_newline("\r\n");

		$this->email->from($from, $from);
		$this->email->to($to);

		if($cc && isset($cc)){
			$this->email->cc($cc);
		}
		if($bcc && isset($bcc)){
			$this->email->bcc($bcc);
		}

		$this->email->subject($subject);
		$this->email->message($message);

		$this->email->set_alt_message('This is the alternative message');

		if ($this->email->send()){
			return "ok";
			return "Mail Sent!";
		}else{

			$error = explode('User-Agent: CodeIgniter',$this->email->print_debugger());
			$error_msg = $error[0];

			$mb_lang = $this->session->userdata('mb_lang');
			if($mb_lang === "ko"){
				return "발송 실패<br><br>Error message : <br>".$error_msg.".<br><br>매일 설정을 확인해 주세요.<br>*/application/config/email.php";
			}else{
				return "There is error in sending mail!<br><br>Error message : <br>".$error_msg;
			}
		}
	}
}

/* End of file Sendmail.php */
/* Location: ./application/libraries/Sendmail.php */