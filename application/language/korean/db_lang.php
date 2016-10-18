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

$lang['db_invalid_connection_str'] = '제출된 연결 문자열에 기반한 데이터베이스 설정을 결정할 수 없습니다.';
$lang['db_unable_to_connect'] = '제공된 설정으로는 데이터베이스 서버에 접속할수 없습니다.';
$lang['db_unable_to_select'] = '명시된 데이터베이스 %s 을(를) 선택할 수 없습니다.';
$lang['db_unable_to_create'] = '명시된 데이터베이스 %s 을(를) 생성할 수 없습니다.';
$lang['db_invalid_query'] = '제출된 쿼리는 유효하지 않습니다.';
$lang['db_must_set_table'] = '쿼리를 사용할 데이터베이스 테이블을 설정해야 합니다.';
$lang['db_must_set_database'] = '데이터베이스 설정 파일에 데이터베이스 이름을 설정해야 합니다.';
$lang['db_must_use_set'] = '엔트리를 업데이트 하려면 "set" 메소드를 사용해야 합니다.';
$lang['db_must_use_index'] = 'You must specify an index to match on for batch updates.';
$lang['db_batch_missing_index'] = 'One or more rows submitted for batch updating is missing the specified index.';
$lang['db_must_use_where'] = '"where" 문이 포함되어있지 않으면 업데이트는 불가능 합니다.';
$lang['db_del_must_use_where'] = '"where" 또는 "like" 문이 포함되어있지 않으면 삭제는 불가능합니다.';
$lang['db_field_param_missing'] = '필드들을 가져오려면 테이블 이름이 인자로 필요합니다.';
$lang['db_unsupported_function'] = '이 기능은 당신이 사용하고 있는 데이터베이스에서는 불가능합니다.';
$lang['db_transaction_failure'] = '트랙잭션 실패 : 롤백이 실행 되었습니다.';
$lang['db_unable_to_drop'] = '명시된 데이터베이스를 드롭할 수 없습니다.';
$lang['db_unsuported_feature'] = '당신이 사용하고 있는 데이터베이스에서는 지원되지 않는 기능입니다.';
$lang['db_unsuported_compression'] = '선택된 파일 압축 방식은 현재 서버에서 지원되지 않습니다.';
$lang['db_filepath_error'] = '제출된 파일 경로로 데이터를 저장할 수 없습니다.';
$lang['db_invalid_cache_path'] = '제출된 캐시 경로는 유효하지 않거나 저장할 수 없습니다.';
$lang['db_table_name_required'] = '이 기능은 테이블 이름이 필요합니다.';
$lang['db_column_name_required'] = '이 기능은 컬럼 정의가 필요합니다.';
$lang['db_column_definition_required'] = '이 기능은 컬럼 설명이 필요합니다.';
$lang['db_unable_to_set_charset'] = '클라이언트 접속 문자셋: %s 을(를) 설정할 수 없습니다.';
$lang['db_error_heading'] = '데이터베이스 오류가 발생하였습니다.';


/* End of file db_lang.php */
/* Location: ./system/language/korean/db_lang.php */