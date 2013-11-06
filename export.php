<?php
  require('admin2/fpdf/fpdf.php');
  $pdf=new FPDF('L','mm','A4');
  $pdf->AddPage();
  $pdf->SetFont('Arial', '', 10);
  
  for ($i = 1; $i <= 7; $i++)
  {
    $pdf->Cell(30,7,"Case $i",1);
  }
  $pdf->Cell(30,7,"Case $i",1);
  $pdf->Output();
?>