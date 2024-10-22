<?php
session_start(); // Ensure session is started
require('assets/fpdf/fpdf.php');

// Create new class extending FPDF class and including the custom table methods
class PDF_MC_Table extends FPDF
{
    var $widths;
    var $aligns;
    var $lineHeight;

    function SetWidths($w)
    {
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        $this->aligns = $a;
    }

    function SetLineHeight($h)
    {
        $this->lineHeight = $h;
    }

    function Row($data)
    {
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }
        $h = $this->lineHeight * $nb;
        $this->CheckPageBreak($h);
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }
}

if (isset($_POST['generate_pdf'])) {
    if (!isset($_SESSION['search_results']) || empty($_SESSION['search_results'])) {
        die('No search results available to generate PDF.');
    }

    $results = $_SESSION['search_results'];

    class PDF extends PDF_MC_Table
    {
        function Header()
        {
            $this->Image('images/logo.png', $this->GetX() + ($this->w / 2) - 24, 10, 20);
            $this->Ln(20);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Cagayan Valley Medical Center - IHOMP Troubleshoot Log', 0, 1, 'C');
            $this->Ln(5);
            $this->SetFont('Arial', 'B', 10);
            $this->SetWidths([60, 90, 90, 30]);
            $this->Row(['Name', 'Location', 'Description', 'Status', ]);
            $this->Ln(10);
        }

        function Footer()
        {
            $this->SetY(-15); // Position for footer text
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }

        function LoadData($results)
        {
            $data = [];
            foreach ($results as $row) {
                $data[] = [
                    $row['name'],
                    $row['location'],
                    $row['description'],
                    $row['status'],
                ];
            }
            return $data;
        }

        function FancyTable($header, $data)
        {
            $this->SetFont('Arial', '', 8);
            $this->SetLineHeight(10);
            foreach ($data as $row) {
                $this->Row($row);
            }
        }
        function AddCustomTextBelowTable($leftTexts, $rightTexts)
{
    $this->Ln(30); // Add some space after the table

    // Get page width
    $pageWidth = $this->GetPageWidth();

    // Define margins
    $leftMargin = 10;
    $rightMargin = 10;
    $textWidth = $pageWidth - $leftMargin - $rightMargin; // Width available for text

    // Set font for the left side text
    $this->SetFont('Arial', 'B', 10);
    $this->SetX($leftMargin); // Set X position to 10 units from the left margin
    $this->Cell($textWidth / 2, 10, $leftTexts[0], 0, 0, 'L');
    
    // Set font for the left side text
    $this->SetFont('Arial', '', 10);
    $this->Ln(); // Move to the next line
    $this->SetX($leftMargin); // Reset X position
    $this->Cell($textWidth / 2, 10, $leftTexts[1], 0, 0, 'L');

    // Move Y position up slightly
    $this->SetY($this->GetY() - 10); // Adjust this value as needed

    // Set font for the right side text
    $this->SetFont('Arial', 'B', 10);
    $this->SetX($pageWidth - $textWidth / 2 - $rightMargin); // Position for right side text
    $this->Cell($textWidth / 2, 10, $rightTexts[0], 0, 0, 'R');

    // Set font for the right side text
    $this->SetFont('Arial', '', 10);
    $this->Ln(); // Move to the next line
    $this->SetX($pageWidth - $textWidth / 2 - $rightMargin); // Reset X position
    $this->Cell($textWidth / 2, 10, $rightTexts[1], 0, 0, 'R');
}

        
    }

    $pdf = new PDF();
    $header = ['Name', 'Location', 'Description', 'Status'];
    $data = $pdf->LoadData($results);
    $pdf->SetFont('Arial', '', 12);
    $pdf->AddPage('L');
    $pdf->FancyTable($header, $data);

    // Add custom text below the table
    $leftTexts = [
        'June S. Prodigo',
        'Information System Analyst III',
    ];
    $rightTexts = [
        'Norbert Diaz',
        'Computer Maintenance Technologist III',
    ];
    $pdf->AddCustomTextBelowTable($leftTexts, $rightTexts);

    $pdf->Output('I', 'Search_Results.pdf'); // Change 'D' to 'I' for inline display
    
} else {
    echo 'Generate PDF form not submitted.';
}
?>
