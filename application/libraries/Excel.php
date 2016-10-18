<?php if (!defined('BASEPATH'))exit('No direct script access allowed');

class Excel {

	private $excel;

	public function __construct() {
		require_once APPPATH . 'third_party/PHPExcel.php';

		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize' => '256MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
		$this->excel = new PHPExcel();
	}

	public function load($path) {
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		 $objReader->setReadDataOnly(true);
		$this->excel = $objReader->load($path);
	}

	public function save($filename) {

		header('Content-type: application/ms-excel');
		header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
		header("Cache-control: private");
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$objWriter->save("export/$filename");
		header("location: " . base_url() . "export/$filename");
	}

	public function stream($filename, $data = null) {
		if ($data != null) {
			$file_col = '';
			$col = 'A';
			//컬럼명 틀 고정
			$rowNumber = 1;
			$this->excel->getActiveSheet()->freezePane("A2");
			foreach ($data[0] as $key => $val) {
				if($key == "otm_export_images"){
					$key = "Attached files";
					$file_col = $col;
				}

				if(strpos($key,'=') === 0){
					$key = " ".$key;
				}

				$this->excel->getActiveSheet()->getCell($col . $rowNumber)->setValue(str_replace("_", " ", $key));
				$this->excel->getActiveSheet()->getStyle('A'.$rowNumber.':'.$col.$rowNumber)->getFont()->setBold(true);

				$col++;
			}
			$rowNumber = 2;
			foreach ($data as $row) {
				$col = 'A';

				$row_height = 20;
				foreach ($row as $cell) {

					if($file_col != '' && $file_col == $col){
							$tmp_cell = '';

							if(is_array($cell)){
								foreach($cell as $file_cell){
									if($file_cell){
									}else{
										continue;
									}
									$is_file = is_file(FCPATH.$file_cell->path);
									if($is_file){
									}else{
										continue;
									}

									$tmp_cell .= $file_cell->of_source;
									//Add Files

									$objDrawing = new PHPExcel_Worksheet_Drawing();
									$objDrawing->setName($file_cell->of_source);
									$objDrawing->setPath(FCPATH.$file_cell->path);
									$objDrawing->setHeight(50);
									$objDrawing->setCoordinates($col . $rowNumber);
									$objDrawing->setWorksheet($this->excel->getActiveSheet());
									if($row_height < $file_cell->of_height){
										$row_height = 50;
									}

									$link = "http://".$_SERVER['HTTP_HOST']."/index.php/FileDownload/file_download_link/?target_seq=".$file_cell->target_seq."&of_no=".$file_cell->of_no;

									if(strpos($file_cell->of_source,'=') === 0){
										$file_cell->of_source = " ".$file_cell->of_source;
									}

									$this->excel->getActiveSheet()->getCell($col . $rowNumber)->setValue($file_cell->of_source);
									$this->excel->getActiveSheet()->getCell($col . $rowNumber)->getHyperlink()->setUrl($link);
									$col++;
								}
							}
					}else{
						if(strpos($cell,'=') === 0){
							//$cell = "'".$cell;
							$cell = " ".$cell;
						}

						$this->excel->getActiveSheet()->setCellValue($col . $rowNumber, $cell);

						//개별적인 열의 넓이지정
						$this->excel->getActiveSheet()->getColumnDimension($col)->setWidth(25);
						//내용 줄바꿈
						$this->excel->getActiveSheet()->getStyle($col . $rowNumber)->getAlignment()->setWrapText(true);
						$col++;
					}
				}

				$this->excel->getActiveSheet()->getRowDimension($rowNumber)->setRowHeight($row_height);
				$rowNumber++;
			}
		}
		$this->save($filename);
	}

	public function multi_stream($filename, $multi_data = null) {
		if ($multi_data != null) {
			$rowNumber = 1;
			for($i=0; $i<count($multi_data); $i++)
			{
				$data = $multi_data[$i];
				$col = 'A';

				if($data['title']){
					if(strpos($data['title'],'=') === 0){
						$data['title'] = " ".$data['title'];
					}

					$this->excel->getActiveSheet()->getCell($col . $rowNumber)->setValue(str_replace("_", " ", $data['title']));
					$this->excel->getActiveSheet()->getStyle('A'.$rowNumber.':'.$col.$rowNumber)->getFont()->setBold(true);
					$col++;
					$rowNumber++;
					continue;
				}else if($data['image']){

					$objDrawing = new PHPExcel_Worksheet_Drawing();
					//$objDrawing->setName('ZealImg');
					//$objDrawing->setDescription('Image inserted by Zeal');
					$objDrawing->setPath($data['image']);
					$objDrawing->setHeight(200);
					$objDrawing->setCoordinates($col . $rowNumber);
					$objDrawing->setWorksheet($this->excel->getActiveSheet());
					$col++;
					$rowNumber+=10;
					continue;
				}else{
					foreach ($data[0] as $key => $val) {
						if(strpos($key,'=') === 0){
							$key = " ".$key;
						}

						$this->excel->getActiveSheet()->getCell($col . $rowNumber)->setValue(str_replace("_", " ", $key));
						$this->excel->getActiveSheet()->getStyle('A'.$rowNumber.':'.$col.$rowNumber)->getFont()->setBold(true);

						$this->excel->getActiveSheet()->getStyle('A'.$rowNumber.':'.$col.$rowNumber)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$col++;
					}
					$rowNumber++;
				}

				foreach ($data as $row) {
					$col = 'A';
					foreach ($row as $cell) {
						if(strpos($cell,'=') === 0){
							$cell = " ".$cell;
						}

						$this->excel->getActiveSheet()->setCellValue($col . $rowNumber, $cell);
						$this->excel->getActiveSheet()->getStyle('A'.$rowNumber.':'.$col.$rowNumber)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

						//개별적인 열의 넓이지정
						$this->excel->getActiveSheet()->getColumnDimension($col)->setWidth(25);
						$col++;
					}
					$rowNumber++;
				}

				$this->excel->getActiveSheet()->setCellValue('A' . $rowNumber, '');
				$rowNumber++;
			}
		}
		$this->save($filename);
	}

	public function __call($name, $arguments) {
		if (method_exists($this->excel, $name)) {
			return call_user_func_array(array($this->excel, $name), $arguments);
		}
		return null;
	}
}

/* End of file Excel.php */
/* Location: ./application/libraries/Excel.php */