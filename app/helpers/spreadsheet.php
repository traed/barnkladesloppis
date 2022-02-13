<?php

namespace eqhby\bkl;

use PhpOffice\PhpSpreadsheet\Spreadsheet as PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Spreadsheet {

	static public function export_users(array $users, $filename = 'export', $occasion = false) {
		$spreadsheet = new PhpSpreadsheet();

		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Försäljnings-ID');
		$sheet->setCellValue('B1', 'Förnamn');
		$sheet->setCellValue('C1', 'Efternamn');
		$sheet->setCellValue('D1', 'E-post');
		$sheet->setCellValue('E1', 'Telefonnummer');
		$sheet->setCellValue('F1', 'Har swish');

		if($occasion) {
			$sheet->setCellValue('G1', 'Vill ha tillbaka kläder');
		}

		foreach($users as $i => $user) {
			$sheet->setCellValueByColumnAndRow(1, $i + 2, $user->get('seller_id'));
			$sheet->setCellValueByColumnAndRow(2, $i + 2, $user->get('first_name'));
			$sheet->setCellValueByColumnAndRow(3, $i + 2, $user->get('last_name'));
			$sheet->setCellValueByColumnAndRow(4, $i + 2, $user->get('user_email'));
			$sheet->setCellValueByColumnAndRow(5, $i + 2, $user->get('phone'));
			$sheet->setCellValueByColumnAndRow(6, $i + 2, get_user_meta($user->ID, 'has_swish', true) ? 'Ja' : 'Nej');

			if($occasion) {
				$sheet->setCellValueByColumnAndRow(7, $i + 2, $user->get('return_items') ? 'Ja' : 'Nej');
			}
		}

		$writer = new Xlsx($spreadsheet);
		$filename .= '.xlsx';

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		$writer->save('php://output');
		exit;
	}
}