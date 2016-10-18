<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['email_must_be_array'] = "이메일 검증 방법은 반드시 배열이 넘겨져야 합니다.";
$lang['email_invalid_address'] = "다음의 이메일 주소는 유효하지 않습니다: %s";
$lang['email_attachment_missing'] = "다음의 이메일 첨부 파일을 찾을 수 없습니다: %s";
$lang['email_attachment_unreadable'] = "다음의 첨부 파일을 열 수 없습니다: %s";
$lang['email_no_from'] = 'Cannot send mail with no "From" header.';
$lang['email_no_recipients'] = "받는 사람들인 To, Cc, Bcc를 반드시 포함해야 합니다.";
$lang['email_send_failure_phpmail'] = "PHP mail()을 이용하여 이메일을 보낼 수 없습니다. 서버가 이런 방법으로 메일을 보내도록 설정되어 있지 않은 것 같습니다.";
$lang['email_send_failure_sendmail'] = "PHP Sendmail을 이용하여 이메일을 보낼 수 없습니다. 서버가 이런 방법으로 메일을 보내도록 설정되어 있지 않은 것 같습니다.";
$lang['email_send_failure_smtp'] = "PHP SMTP 이용하여 이메일을 보낼 수 없습니다. 서버가 이런 방법으로 메일을 보내도록 설정되어 있지 않은 것 같습니다.";
$lang['email_sent'] = "당신의 메시지가 다음의 프로토콜을 이용하여 성공적으로 보내졌습니다: %s";
$lang['email_no_socket'] = "Sendmail로의 소켓을 열 수 없습니다. 설정을 확인하여 주십시오.";
$lang['email_no_hostname'] = "SMTP 호스트 이름을 명시하지 않았습니다.";
$lang['email_smtp_error'] = "다음의 SMTP 오류가 발생하였습니다: %s";
$lang['email_no_smtp_unpw'] = "오류: SMTP 아이디와 암호를 설정해야 합니다.";
$lang['email_failed_smtp_login'] = "AUTH LOGIN 명령을 보내는 데 실패하였습니다. 오류: %s";
$lang['email_smtp_auth_un'] = "아이디 인증을 실패하였습니다. 오류: %s";
$lang['email_smtp_auth_pw'] = "암호 인증을 실패하였습니다. 오류: %s";
$lang['email_smtp_data_failure'] = "다음의 데이터를 보낼 수 없습니다: %s";
$lang['email_exit_status'] = "종료 상태 코드: %s";
/* End of file email_lang.php */
/* Location: ./system/language/korean/email_lang.php */