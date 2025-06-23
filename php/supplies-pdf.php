<?php
require_once('tcpdf/tcpdf.php');

class SuppliesList extends TCPDF {
    private $schoolType;
    
    public function __construct() {
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->SetCreator('École La Victoire');
        $this->SetTitle('Liste des fournitures scolaires');
        $this->SetMargins(20, 20, 20);
        $this->SetAutoPageBreak(true, 20);
    }
    
    public function Header() {
        // Logo
        if (file_exists('../assets/logo.png')) {
            $this->Image('../assets/logo.png', 20, 10, 30, 0, 'PNG');
        }
        
        // Titre école (aligné à droite du logo)
        $this->SetXY(55, 15);
        $this->SetFont('helvetica', 'B', 14);
        $this->SetTextColor(0, 60, 113);
        $this->Cell(0, 8, 'LA VICTOIRE DE L\'ENSEIGNEMENT PRIVÉ', 0, 1, 'L');
        
        $this->SetX(55);
        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 6, 'École ' . ($this->schoolType ?? 'Maternelle et Primaire'), 0, 1, 'L');
        
        // Ligne de séparation
        $this->Ln(5);
        $this->SetDrawColor(255, 215, 0);
        $this->SetLineWidth(0.5);
        $this->Line(20, 40, 190, 40);
        
        $this->Ln(15);
    }
    
    public function Footer() {
        $this->SetY(-25);
        $this->SetFont('helvetica', 'I', 9);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 5, 'École La Victoire - Avenue Mohammed Daoud, Tétouan', 0, 1, 'C');
        $this->Cell(0, 5, 'Tél: +212 5 39 96 XX XX | www.groupelavictoire.com', 0, 1, 'C');
    }
    
    public function generateSuppliesList($data) {
        // Déterminer le type d'école
        $level = $data['school_level'] ?? $data['niveau'] ?? '';
        $this->schoolType = in_array($level, ['PS', 'MS', 'GS']) ? 'Maternelle' : 'Primaire';
        
        $this->AddPage();
        
        // Titre principal
        $this->SetFont('helvetica', 'B', 18);
        $this->SetTextColor(0, 60, 113);
        $this->Cell(0, 12, 'LISTE DES FOURNITURES SCOLAIRES', 0, 1, 'C');
        
        // Année scolaire
        $this->SetFont('helvetica', '', 14);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, 'Année scolaire 2025-2026', 0, 1, 'C');
        
        $this->Ln(10);
        
        // Cadre informations élève
        $this->SetFillColor(245, 245, 245);
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.3);
        
        // Box info élève
        $this->Rect(20, 75, 170, 25, 'DF');
        
        $this->SetY(78);
        $this->SetFont('helvetica', 'B', 12);
        $this->SetTextColor(0, 0, 0);
        
        // Infos sur 2 colonnes
        $this->SetX(25);
        $studentName = ($data['student_firstname'] ?? $data['prenom'] ?? '') . ' ' . ($data['student_lastname'] ?? $data['nom'] ?? '');
        $this->Cell(80, 6, 'Élève : ' . trim($studentName), 0, 0, 'L');
        $this->Cell(80, 6, 'N° Dossier : ' . ($data['dossier_number'] ?? 'INS-' . date('Y') . '-' . rand(1000, 9999)), 0, 1, 'L');
        
        $this->SetX(25);
        $this->Cell(80, 6, 'Classe : ' . $this->formatLevel($level), 0, 0, 'L');
        $this->Cell(80, 6, 'Date : ' . date('d/m/Y'), 0, 1, 'L');
        
        $this->Ln(15);
        
        // Liste des fournitures
        $this->SetFont('helvetica', 'B', 14);
        $this->SetTextColor(0, 60, 113);
        $this->Cell(0, 10, 'Fournitures à préparer :', 0, 1, 'L');
        
        // Générer la liste avec cases à cocher
        $supplies = $this->getSuppliesForLevel($level);
        $checked = isset($data['checked_supplies']) ? $data['checked_supplies'] : [];
        
        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(0, 0, 0);
        
        foreach ($supplies as $index => $item) {
            // Vérifier si on dépasse la page
            if ($this->GetY() > 250) {
                $this->AddPage();
            }
            
            $isChecked = in_array($item, $checked);
            
            // Case à cocher avec bordure
            $boxX = 30;
            $boxY = $this->GetY() + 2;
            $this->SetDrawColor(128, 128, 128);
            $this->Rect($boxX, $boxY, 6, 6);
            
            // Marque si coché
            if ($isChecked) {
                $this->SetTextColor(0, 150, 0);
                $this->SetFont('helvetica', 'B', 12);
                $this->Text($boxX + 1, $boxY + 5, '✓');
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('helvetica', '', 11);
            }
            
            // Texte de l'item
            $this->SetX(40);
            $this->Cell(150, 8, $item, 0, 1, 'L');
            
            // Ligne légère entre items
            if ($index < count($supplies) - 1) {
                $this->SetDrawColor(230, 230, 230);
                $this->Line(30, $this->GetY(), 180, $this->GetY());
            }
        }
        
        $this->Ln(10);
        
        // Statistiques de préparation
        $totalItems = count($supplies);
        $checkedItems = count($checked);
        $percentage = $totalItems > 0 ? ($checkedItems / $totalItems) * 100 : 0;
        
        // Barre de progression
        $this->SetFont('helvetica', 'B', 12);
        $this->SetTextColor(0, 60, 113);
        $this->Cell(0, 8, sprintf('Progression de préparation : %d/%d items (%.0f%%)', $checkedItems, $totalItems, $percentage), 0, 1, 'L');
        
        // Barre visuelle
        $barWidth = 170;
        $fillWidth = ($percentage / 100) * $barWidth;
        
        $this->SetFillColor(240, 240, 240);
        $this->Rect(20, $this->GetY() + 2, $barWidth, 8, 'F');
        
        if ($fillWidth > 0) {
            $this->SetFillColor(76, 175, 80);
            $this->Rect(20, $this->GetY() + 2, $fillWidth, 8, 'F');
        }
        
        $this->Ln(15);
        
        // Note importante avec fond coloré
        $noteY = $this->GetY();
        $this->SetFillColor(255, 243, 224);
        $this->SetDrawColor(255, 193, 7);
        $this->Rect(20, $noteY, 170, 35, 'DF');
        
        $this->SetY($noteY + 5);
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(133, 100, 4);
        $this->SetX(25);
        $this->Cell(0, 6, '⚠ IMPORTANT :', 0, 1, 'L');
        
        $this->SetFont('helvetica', '', 10);
        $this->SetX(25);
        $this->MultiCell(160, 5, 
            "• Tous les articles doivent être marqués au nom de l'enfant\n" .
            "• Les fournitures doivent être apportées le jour de la rentrée\n" .
            "• Cette liste doit être signée et remise à l'école\n" .
            "• Prévoir des articles de rechange pour l'année", 
            0, 'L');
        
        // Signatures
        $this->SetY(-50);
        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(0, 0, 0);
        
        // Tableau signatures
        $this->Cell(85, 8, 'Signature des Parents', 'T', 0, 'C');
        $this->Cell(20, 8, '', 0, 0, 'C'); // Espace
        $this->Cell(65, 8, 'Cachet de l\'École', 'T', 1, 'C');
        
        $this->Ln(10);
        $this->SetFont('helvetica', 'I', 9);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(85, 5, 'Lu et approuvé', 0, 0, 'C');
        $this->Cell(20, 5, '', 0, 0, 'C');
        $this->Cell(65, 5, 'École La Victoire', 0, 1, 'C');
    }
    
    private function formatLevel($level) {
        $levels = [
            'PS' => 'Petite Section',
            'MS' => 'Moyenne Section', 
            'GS' => 'Grande Section',
            'CP' => 'CP (1ère année primaire)',
            'CE1' => 'CE1 (2ème année primaire)',
            'CE2' => 'CE2 (3ème année primaire)',
            'CM1' => 'CM1 (4ème année primaire)',
            'CM2' => 'CM2 (5ème année primaire)',
            '6EME' => '6ème année primaire'
        ];
        return $levels[$level] ?? $level;
    }
    
    private function getSuppliesForLevel($level) {
        $supplies = [
            'PS' => [
                '2 boîtes de mouchoirs en papier',
                '1 paquet de lingettes humides',
                '1 tablier en plastique pour les activités',
                '1 gobelet en plastique marqué au nom',
                '1 petit coussin pour la sieste (30x30cm)',
                '1 change complet dans un sac marqué',
                '2 photos d\'identité récentes'
            ],
            'MS' => [
                '2 boîtes de mouchoirs en papier',
                '1 paquet de lingettes humides',
                '1 tablier pour la peinture',
                '1 ardoise blanche (format A4) + 3 feutres effaçables',
                '1 boîte de 12 crayons de couleur',
                '1 paire de ciseaux à bouts ronds',
                '2 bâtons de colle',
                '2 photos d\'identité récentes'
            ],
            'GS' => [
                '2 boîtes de mouchoirs en papier',
                '1 tablier pour les activités manuelles',
                '1 ardoise blanche + 4 feutres effaçables + 1 chiffon',
                '1 boîte de 12 crayons de couleur + 1 boîte de feutres',
                '1 paire de ciseaux à bouts ronds',
                '3 bâtons de colle',
                '1 règle plate de 20 cm',
                '1 cahier de dessin (format A4)',
                '2 photos d\'identité récentes'
            ],
            'CP' => [
                '2 boîtes de mouchoirs en papier',
                '1 ardoise blanche + 4 feutres effaçables + 1 chiffon',
                '1 boîte de 12 crayons de couleur',
                '1 boîte de 12 feutres',
                '4 crayons à papier HB',
                '1 gomme blanche',
                '1 taille-crayon avec réservoir',
                '1 règle plate de 20 cm',
                '3 bâtons de colle',
                '1 paire de ciseaux à bouts ronds',
                '2 cahiers 96 pages (grands carreaux)',
                '1 cahier de brouillon',
                '2 photos d\'identité récentes'
            ],
            'CE1' => [
                '1 trousse complète (crayons, gomme, règle)',
                '1 boîte de 12 crayons de couleur',
                '1 boîte de 12 feutres',
                '4 crayons à papier HB + 2 crayons 2H',
                '2 gommes blanches',
                '1 taille-crayon avec réservoir',
                '1 règle graduée de 30 cm',
                '4 bâtons de colle',
                '1 paire de ciseaux',
                '3 cahiers 96 pages (grands carreaux)',
                '2 cahiers 96 pages (petits carreaux)',
                '1 cahier de brouillon',
                '1 agenda scolaire',
                '2 photos d\'identité récentes'
            ],
            'CE2' => [
                '1 trousse complète bien équipée',
                '1 boîte de 12 crayons de couleur',
                '1 boîte de 12 feutres',
                '6 crayons à papier (HB et 2H)',
                '2 gommes blanches',
                '1 taille-crayon avec réservoir',
                '1 règle graduée de 30 cm',
                '1 équerre',
                '1 compas à mine',
                '4 bâtons de colle',
                '1 paire de ciseaux',
                '4 cahiers 96 pages (grands carreaux)',
                '2 cahiers 96 pages (petits carreaux)',
                '1 cahier de brouillon',
                '1 agenda scolaire',
                '1 dictionnaire français adapté au niveau',
                '2 photos d\'identité récentes'
            ],
            'CM1' => [
                '1 trousse complète (double compartiment recommandé)',
                '1 boîte de 12 crayons de couleur',
                '1 boîte de 12 feutres',
                '6 crayons à papier (HB, 2H, 4H)',
                '2 gommes blanches',
                '1 taille-crayon métallique avec réservoir',
                '1 règle graduée de 30 cm',
                '1 équerre',
                '1 compas à mine',
                '4 bâtons de colle',
                '1 paire de ciseaux',
                '4 cahiers 96 pages (grands carreaux)',
                '3 cahiers 96 pages (petits carreaux)',
                '1 cahier de brouillon',
                '1 agenda scolaire',
                '1 dictionnaire français complet',
                '1 calculatrice simple',
                '2 photos d\'identité récentes'
            ],
            'CM2' => [
                '1 trousse complète professionnelle',
                '1 boîte de 12 crayons de couleur',
                '1 boîte de 12 feutres',
                '8 crayons à papier (HB, 2H, 4H)',
                '3 gommes blanches',
                '1 taille-crayon métallique avec réservoir',
                '1 règle graduée de 30 cm',
                '1 équerre',
                '1 compas à mine professionnel',
                '1 rapporteur',
                '4 bâtons de colle',
                '1 paire de ciseaux',
                '5 cahiers 96 pages (grands carreaux)',
                '3 cahiers 96 pages (petits carreaux)',
                '2 cahiers de brouillon',
                '1 agenda scolaire',
                '1 dictionnaire français complet',
                '1 calculatrice scientifique simple',
                '2 photos d\'identité récentes'
            ],
            '6EME' => [
                '1 trousse complète professionnelle',
                '1 boîte de 12 crayons de couleur',
                '1 boîte de 12 feutres',
                '8 crayons à papier (HB, 2H, 4H)',
                '3 gommes blanches',
                '1 taille-crayon métallique avec réservoir',
                '1 règle graduée de 30 cm',
                '1 équerre',
                '1 compas à mine professionnel',
                '1 rapporteur',
                '5 bâtons de colle',
                '1 paire de ciseaux',
                '6 cahiers 96 pages (grands carreaux)',
                '4 cahiers 96 pages (petits carreaux)',
                '2 cahiers de brouillon',
                '1 agenda scolaire',
                '1 dictionnaire français complet',
                '1 dictionnaire arabe-français',
                '1 calculatrice scientifique',
                '2 photos d\'identité récentes'
            ]
        ];
        
        return $supplies[$level] ?? [
            'Fournitures de base',
            'Cahiers et carnets',
            'Matériel d\'écriture',
            'Outils de géométrie',
            'Photos d\'identité'
        ];
    }
}
?> 