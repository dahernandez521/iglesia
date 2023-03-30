<?php

require_once('../../model/conexion/conexion.php');
require_once('../../library/Excel/vendor/autoload.php');
require_once('../../model/query/read/misas.php');
require_once('../../controllers/read/loadTableMisas.php');

if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

if (isset($_SESSION['idMisa'])) {
	$id = $_SESSION['idMisa'];
}


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;


$spreadsheet = new Spreadsheet();

$spreadsheet->setActiveSheetIndex(0);

$spreadsheet->getProperties()
	->setCreator("Duvan Hernandez")
	->setLastModifiedBy("Duvan Hernandez")
	->setTitle("Report Asistentes")
	->setSubject("Asistentes misa")
	->setDescription(
		"Asistentes misa"
	);


$hojaActiva = $spreadsheet->getActiveSheet();
$spreadsheet->getDefaultStyle()->getFont()->setName("Asistentes misa");
$hojaActiva->setTitle("Asistentes misa");
$spreadsheet->getDefaultStyle()->getFont()->setSize(11);
$hojaActiva->getColumnDimension('A')->setWidth(5);
$hojaActiva->getColumnDimension('B')->setWidth(20);
$hojaActiva->getColumnDimension('C')->setWidth(30);
$hojaActiva->getColumnDimension('D')->setWidth(20);
$hojaActiva->getColumnDimension('E')->setWidth(20);
$hojaActiva->getColumnDimension('F')->setWidth(30);
$hojaActiva->getColumnDimension('G')->setWidth(15);
$hojaActiva->getColumnDimension('H')->setWidth(20);

$hojaActiva->setCellValue('A1', '');
$hojaActiva->setCellValue('B1', 'SUPERVISOR');
$hojaActiva->setCellValue('C1', 'NOMBRES');
$hojaActiva->setCellValue('D1', 'TIPO DOCUMENTO');
$hojaActiva->setCellValue('E1', 'N° DOCUMENTO');
$hojaActiva->setCellValue('F1', 'EMAIL');
$hojaActiva->setCellValue('G1', 'CELULAR');
$hojaActiva->setCellValue('H1', 'DIRECCION');



$queries = new queriesMisas();
$result = $queries->showUser($id);


$num = 0;
$i = 1;
if (isset($result)) {
	foreach ($result as $f) {
		$num = $num + 1;
		$i = $i + 1;


		$hojaActiva->setCellValue("A$i", $num);
		$hojaActiva->setCellValue("B$i", "SUPERVISOR");
		$hojaActiva->setCellValue("C$i", $f['name']);
		$hojaActiva->setCellValue("D$i", $f['tipo']);
		$hojaActiva->setCellValue("E$i", $f['document']);
		$hojaActiva->setCellValue("F$i", $f['email']);
		$hojaActiva->setCellValue("G$i", $f['cel']);
		$hojaActiva->setCellValue("H$i", $f['address']);

		$resultTwo = $queries->showUserTwo($f['document'], $id);

		if (isset($resultTwo)) {
			foreach ($resultTwo as $k) {
				$num = $num + 1;
				$i = $i + 1;

					$hojaActiva->setCellValue("A$i", $num);
					$hojaActiva->setCellValue("B$i", $f['document']);
					$hojaActiva->setCellValue("C$i", $k['name']);
					$hojaActiva->setCellValue("D$i", $k['tipo']);
					$hojaActiva->setCellValue("E$i", $k['document']);
					$hojaActiva->setCellValue("F$i", $f['email']);
					$hojaActiva->setCellValue("G$i", $f['cel']);
					$hojaActiva->setCellValue("H$i", $f['address']);

				if ($k['supervisor'] != $f['document']) {
					break;
				}
			}
		} else {
			$num = $num + 1;
			$i = $i + 1;
		}
	}





	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="REPORT ASISTENTS.xlsx"');
	header('Cache-Control: max-age=0');
	
	$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer->save('php://output');
	
} else {

	echo "<script>alert('fallo al descargar reporte');
		location.href='../../';

		</script>";
}
