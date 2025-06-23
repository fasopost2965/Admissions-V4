<?php
// Mock TCPDF class pour démonstration
// En production, utilisez la vraie bibliothèque TCPDF : https://tcpdf.org

class TCPDF {
    protected $currentY = 20;
    protected $pageWidth = 210;
    protected $pageHeight = 297;
    protected $margins = array('left' => 15, 'top' => 20, 'right' => 15);
    protected $headerFunction = null;
    protected $footerFunction = null;
    protected $pageNumber = 1;
    protected $title = '';
    protected $author = '';
    protected $creator = '';
    
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
        // Constructeur de base
    }
    
    public function SetCreator($creator) {
        $this->creator = $creator;
    }
    
    public function SetAuthor($author) {
        $this->author = $author;
    }
    
    public function SetTitle($title) {
        $this->title = $title;
    }
    
    public function SetMargins($left, $top, $right) {
        $this->margins = array('left' => $left, 'top' => $top, 'right' => $right);
    }
    
    public function SetAutoPageBreak($auto, $margin = 0) {
        // Auto page break
    }
    
    public function AddPage() {
        $this->pageNumber++;
        $this->currentY = $this->margins['top'];
        if ($this->headerFunction) {
            call_user_func($this->headerFunction);
        }
    }
    
    public function SetFont($family, $style = '', $size = 10) {
        // Set font
    }
    
    public function SetTextColor($r, $g = -1, $b = -1) {
        // Set text color
    }
    
    public function SetFillColor($r, $g = -1, $b = -1) {
        // Set fill color
    }
    
    public function SetDrawColor($r, $g = -1, $b = -1) {
        // Set draw color
    }
    
    public function SetLineWidth($width) {
        // Set line width
    }
    
    public function SetLineDash($dash = array()) {
        // Set line dash
    }
    
    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {
        if ($ln == 1) {
            $this->currentY += $h;
        }
    }
    
    public function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0) {
        $lines = ceil(strlen($txt) / 80); // Estimation
        $this->currentY += ($h * $lines);
    }
    
    public function Ln($h = '') {
        $this->currentY += ($h ?: 5);
    }
    
    public function GetY() {
        return $this->currentY;
    }
    
    public function SetY($y) {
        $this->currentY = $y;
    }
    
    public function SetX($x) {
        // Set X position
    }
    
    public function SetXY($x, $y) {
        $this->currentY = $y;
    }
    
    public function Text($x, $y, $txt) {
        // Add text at position
    }
    
    public function Line($x1, $y1, $x2, $y2) {
        // Draw line
    }
    
    public function Rect($x, $y, $w, $h, $style = '') {
        // Draw rectangle
    }
    
    public function Image($file, $x = '', $y = '', $w = 0, $h = 0, $type = '', $link = '') {
        // Add image
    }
    
    public function Header() {
        // Override in subclass
    }
    
    public function Footer() {
        // Override in subclass
    }
    
    public function Output($dest = 'I', $name = 'doc.pdf') {
        if ($dest == 'F') {
            // Simuler la sauvegarde en créant un fichier texte
            $content = $this->generateMockPDFContent();
            file_put_contents($name, $content);
            return true;
        } else {
            // Pour la démonstration, générer un HTML à la place
            $html = $this->generateHTMLPreview();
            echo $html;
        }
    }
    
    protected function generateMockPDFContent() {
        return "MOCK PDF CONTENT - École La Victoire\n" .
               "================================\n" .
               "Title: " . $this->title . "\n" .
               "Author: " . $this->author . "\n" .
               "Creator: " . $this->creator . "\n" .
               "Generated: " . date('Y-m-d H:i:s') . "\n" .
               "Pages: " . $this->pageNumber . "\n\n" .
               "This is a mock PDF file for demonstration.\n" .
               "In production, use the real TCPDF library.";
    }
    
    protected function generateHTMLPreview() {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . $this->title . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .pdf-preview { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #003C71; padding-bottom: 20px; margin-bottom: 30px; }
        .mock-notice { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="pdf-preview">
        <div class="mock-notice">
            <strong>Mode Démonstration:</strong> Ceci est un aperçu HTML du PDF. 
            En production, utilisez la vraie bibliothèque TCPDF pour générer de vrais PDFs.
        </div>
        <div class="header">
            <h1>' . $this->title . '</h1>
            <p>École La Victoire - Document généré le ' . date('d/m/Y à H:i') . '</p>
        </div>
        <div class="content">
            <p><strong>Créateur:</strong> ' . $this->creator . '</p>
            <p><strong>Auteur:</strong> ' . $this->author . '</p>
            <p><strong>Pages:</strong> ' . $this->pageNumber . '</p>
            <p>Ce document PDF professionnel contiendrait normalement toutes les informations formatées avec le design École La Victoire.</p>
        </div>
    </div>
</body>
</html>';
    }
}

// Classe ZipArchive mock si nécessaire
if (!class_exists('ZipArchive')) {
    class ZipArchive {
        const CREATE = 1;
        
        public function open($filename, $flags = null) {
            return true;
        }
        
        public function addFile($filename, $entryname = null) {
            return true;
        }
        
        public function addFromString($entryname, $contents) {
            return true;
        }
        
        public function close() {
            return true;
        }
    }
}
?> 