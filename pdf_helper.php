<?php
// ============================================
// PDF HELPER FILE
// This is a small, simple PDF generator made with
// only normal PHP (no extra libraries).
//
// It lets us build a PDF step by step:
//   $pdf = new SimplePdf('L', 'pt', 'A4');   // make a PDF
//   $pdf->setTitle('...');                   // set the title
//   $pdf->addTitle('...');                   // add a heading
//   $pdf->addText('...');                    // add a line of text
//   $pdf->setTableColumns([...]);            // set table headers
//   $pdf->addRow([...]);                     // add a table row
//   $pdf->output('file.pdf');                // download the PDF
// ============================================

class SimplePdf
{
    // --- Things the PDF builder remembers ---
    private $pages = [];          // all the pages
    private $buffer = '';         // the current page being written
    private $pageNo = 0;          // which page we are on
    private $pageW = 0;           // page width (points)
    private $pageH = 0;           // page height (points)
    private $marginL = 0;         // left margin
    private $marginR = 0;         // right margin
    private $marginT = 0;         // top margin
    private $marginB = 0;         // bottom margin
    private $y = 0;               // where we are on the page (up/down)
    private $landscape = false;   // is the page wide (landscape)?
    private $title = 'PDF Document';   // document title
    private $fontSize = 10;       // text size
    private $fontFamily = 'Helvetica';   // font name
    private $colWidths = [];      // column widths for tables
    private $headerCols = [];     // column headers for tables
    private $objects = [];        // internal PDF objects
    private $objCount = 0;        // how many PDF objects
    private $pageObjs = [];       // page objects
    private $pagesObjId = 0;      // pages object id
    private $finalized = false;   // is the PDF finished?

    /**
     * Make a new PDF.
     *
     * @param string $orientation 'P' (tall) or 'L' (wide)
     * @param string $unit        'pt' (points) — default
     * @param string $format      'A4' or 'Letter'
     */
    public function __construct($orientation = 'P', $unit = 'pt', $format = 'A4')
    {
        // Page sizes in points.
        $sizes = [
            'A4' => [595.28, 841.89],
            'A5' => [419.53, 595.28],
            'Letter' => [612, 792],
            'Legal' => [612, 1008],
        ];
        $size = isset($sizes[$format]) ? $sizes[$format] : $sizes['A4'];

        // Check if we want a wide page (landscape).
        $this->landscape = (strtoupper($orientation) === 'L');

        if ($this->landscape) {
            $this->pageW = $size[1];
            $this->pageH = $size[0];
        } else {
            $this->pageW = $size[0];
            $this->pageH = $size[1];
        }

        // Default margins (40 points = about half an inch).
        $this->marginL = 40;
        $this->marginR = 40;
        $this->marginT = 40;
        $this->marginB = 40;

        // Start the first page.
        $this->addPage();
    }

    /**
     * Set the document title (shown in PDF info).
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Start a new page.
     */
    private function addPage()
    {
        // Save the current page before starting a new one.
        if ($this->pageNo > 0) {
            $this->pages[$this->pageNo] = $this->buffer;
        }
        $this->pageNo++;
        $this->buffer = '';
        $this->y = $this->marginT;   // start near the top
    }

    /**
     * Add a big title line to the PDF.
     *
     * @param string $text
     * @param int    $size
     */
    public function addTitle($text, $size = 16)
    {
        $this->setFontSize($size);
        $this->setFontBold();
        $this->text($text);
        $this->ln(4);
        $this->setFontNormal();
        $this->setFontSize(10);
    }

    /**
     * Add a normal text line.
     *
     * @param string $text
     * @param int    $size
     */
    public function addText($text, $size = 10)
    {
        $this->setFontSize($size);
        $this->text($text);
        $this->ln(4);
    }

    /**
     * Set the table columns (the header row).
     * The columns are spread evenly across the page.
     *
     * @param array $cols List of column header names.
     */
    public function setTableColumns($cols)
    {
        $this->headerCols = $cols;
        $count = count($cols);
        $usable = $this->pageW - $this->marginL - $this->marginR;
        $this->colWidths = array_fill(0, $count, $usable / $count);
    }

    /**
     * Add a row to the table.
     *
     * @param array $cells List of cell values for the row.
     */
    public function addRow($cells)
    {
        if (empty($this->headerCols)) {
            return;
        }

        // If the row would go past the bottom of the page,
        // start a new page and draw the header again.
        $rowHeight = 22;
        if ($this->y + $rowHeight > $this->pageH - $this->marginB) {
            $this->addPage();
            $this->drawTableHeader();
        }

        $x = $this->marginL;
        $this->drawCell($x, $this->y, $this->colWidths, $rowHeight, $cells, false);
        $this->y += $rowHeight;
    }

    /**
     * Draw the table header row (bold, shaded).
     */
    private function drawTableHeader()
    {
        $rowHeight = 24;
        $x = $this->marginL;
        $this->drawCell($x, $this->y, $this->colWidths, $rowHeight, $this->headerCols, true);
        $this->y += $rowHeight;
    }

    /**
     * Draw a single row of cells.
     *
     * @param float $x
     * @param float $y
     * @param array $widths
     * @param float $height
     * @param array $cells
     * @param bool  $isHeader
     */
    private function drawCell($x, $y, $widths, $height, $cells, $isHeader)
    {
        $this->setFontSize($isHeader ? 9 : 9);

        // Shorten long text so it fits in its column.
        $cellText = [];
        foreach ($cells as $i => $val) {
            $maxChars = max(3, (int)floor($widths[$i] / 5.5));
            $text = (string)$val;
            if (strlen($text) > $maxChars) {
                $text = substr($text, 0, $maxChars - 1) . '…';
            }
            $cellText[] = $text;
        }

        // Shade the header row (a light gray box).
        if ($isHeader) {
            $this->fillRect($x, $y, $this->pageW - $this->marginL - $this->marginR, $height, '0.9');
        }

        // Draw the box outline around the whole row.
        $this->drawRect($x, $y, $this->pageW - $this->marginL - $this->marginR, $height);

        // Write each cell's text inside its column.
        $cx = $x;
        foreach ($cellText as $i => $text) {
            if ($isHeader) {
                $this->setFontBold();
            } else {
                $this->setFontNormal();
            }
            $this->textAt($cx + 4, $y + $height / 2 - 3, $text);
            $cx += $widths[$i];
        }
    }

    /**
     * Send the PDF to the browser for download.
     *
     * @param string $filename
     */
    public function output($filename)
    {
        // Save the last page.
        $this->pages[$this->pageNo] = $this->buffer;

        $this->finalize();

        // Tell the browser to download a PDF file.
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        echo $this->buildPdf();
        exit;
    }

    // ================= INTERNAL HELPERS =================

    private function setFontSize($size)
    {
        $this->fontSize = $size;
    }

    private function setFontBold()
    {
        $this->fontFamily = 'Helvetica-Bold';
    }

    private function setFontNormal()
    {
        $this->fontFamily = 'Helvetica';
    }

    /**
     * Add a text line at the current Y position.
     */
    private function text($txt)
    {
        $this->textAt($this->marginL, $this->y, $txt);
        $this->y += $this->fontSize + 2;
    }

    /**
     * Add text at an exact position.
     */
    private function textAt($x, $y, $txt)
    {
        $esc = $this->escape($txt);
        $this->buffer .= sprintf(
            "BT /F1 %d Tf %.2f %.2f Td (%s) Tj ET\n",
            $this->fontSize,
            $x,
            $y,
            $esc
        );
    }

    /**
     * Move down by a given amount.
     */
    private function ln($h = 4)
    {
        $this->y += $h;
    }

    /**
     * Draw a rectangle outline.
     */
    private function drawRect($x, $y, $w, $h)
    {
        $this->buffer .= sprintf(
            "%.2f %.2f %.2f %.2f re S\n",
            $x,
            $y,
            $w,
            $h
        );
    }

    /**
     * Draw a filled rectangle (for the shaded header).
     */
    private function fillRect($x, $y, $w, $h, $gray = '0.9')
    {
        $this->buffer .= sprintf(
            "%.2f g %.2f %.2f %.2f %.2f re f\n",
            $gray,
            $x,
            $y,
            $w,
            $h
        );
    }

    /**
     * Escape special characters in PDF text.
     */
    private function escape($txt)
    {
        $txt = str_replace('\\', '\\\\', $txt);
        $txt = str_replace('(', '\\(', $txt);
        $txt = str_replace(')', '\\)', $txt);
        // Change UTF-8 to a simpler format and remove weird characters.
        $txt = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $txt);
        if ($txt === false) {
            $txt = '';
        }
        return $txt;
    }

    /**
     * Finish building the PDF structure.
     */
    private function finalize()
    {
        if ($this->finalized) {
            return;
        }
        $this->finalized = true;

        $this->objects = [];

        // Object 1: Catalog (the main PDF entry point).
        $this->objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";

        // Object 2: Pages (lists all pages in the document).
        $kids = [];
        $offset = 3; // page objects start at 3
        for ($i = 0; $i < $this->pageNo; $i++) {
            $kids[] = ($offset + $i * 2) . " 0 R";
        }
        $this->objects[2] = sprintf(
            "<< /Type /Pages /Kids [%s] /Count %d >>",
            implode(' ', $kids),
            $this->pageNo
        );

        // Page objects and their content streams.
        $idx = 3;
        foreach ($this->pages as $pageData) {
            // Content stream object (the actual drawing commands).
            $this->objects[$idx] = "<< /Length " . strlen($pageData) . " >>\nstream\n" . $pageData . "endstream";
            $contentId = $idx;
            $idx++;
            // Page object (points to its content and the font).
            $this->objects[$idx] = sprintf(
                "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2f %.2f] /Contents %d 0 R /Resources << /Font << /F1 %d 0 R >> >> >>",
                $this->pageW,
                $this->pageH,
                $contentId,
                $idx + 1
            );
            $idx++;
        }

        // Font object (the text style used everywhere).
        $fontId = $idx;
        $this->objects[$fontId] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
    }

    /**
     * Build the final PDF file as a string.
     */
    private function buildPdf()
    {
        $pdf = "%PDF-1.4\n";

        $offsets = [];
        $objCount = count($this->objects);

        // Write each PDF object.
        foreach ($this->objects as $id => $content) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $content . "\nendobj\n";
        }

        // Write the xref table (tells the PDF reader where things are).
        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . ($objCount + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $objCount; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        // Write the trailer and the end marker.
        $pdf .= "trailer\n";
        $pdf .= "<< /Size " . ($objCount + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefPos . "\n%%EOF\n";

        return $pdf;
    }
}
