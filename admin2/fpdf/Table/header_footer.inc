<?php

/**
 * Class extention for Header and Footer Definitions
 *
 */
class pdf_usage extends fpdf_table
{
	
	public function Header()
	{
    global $titre_page;
    global $lib_groupe;
    
    $titre_page = utf8_decode($titre_page);
		$this->SetStyle("head1","arial","",10,"160,160,160");
    $this->SetY(10);
    $this->MultiCellTag(100, 4, "<head1>$titre_page\nGroupe : $lib_groupe</head1>");
    $this->SetY($this->tMargin);
	}	
	
	public function Footer()
	{
	    $this->SetY(-10);
	    $this->SetFont('Arial','I',7);
	    $this->SetTextColor(170, 170, 170);
	    $this->MultiCell(0, 4, "Page {$this->PageNo()} / {nb}", 0, 'C');
	}
} 

?>