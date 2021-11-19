<?php

/*
  $_SERVER['REQUEST_URI'] вернет "/dmgnPs/php/xlsx.php" , то есть путь откуда запускается скрипт из корневой папки, саму корневую папку не вернет!!!
 а вот $_SERVER['DOCUMENT_ROOT'], вернет саму коневую паку - "C:\inetpub\wwwroot"
 */

include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/lib/PHPExcel.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/lib/PHPExcel/Writer/Excel2007.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/dmgnPsAdm/php/db_depo.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_dbbasemy.php';    
include_once $_SERVER['DOCUMENT_ROOT'] . '/orgcomm/php/_dbcomm.php';

include_once 'assist.php';

class xlsx{
       
    public function __construct() {
        if (strlen(trim(session_id())) == 0)
            session_start();
    }
    
    public static
        $borderB = [ 'borders' => [ 'bottom'  => [ 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000') ] ] ],
        $borderR = [ 'borders' => [ 'right'   => [ 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000') ] ] ],
        
        $borderTB = [ 'borders' => [ 'top'    => [ 'style' => PHPExcel_Style_Border::BORDER_MEDIUM, 'color' => array('rgb' => '000000') ] ] ],
        $borderLB = [ 'borders' => [ 'left'   => [ 'style' => PHPExcel_Style_Border::BORDER_MEDIUM, 'color' => array('rgb' => '000000') ] ] ],
        $borderBB = [ 'borders' => [ 'bottom' => [ 'style' => PHPExcel_Style_Border::BORDER_MEDIUM, 'color' => array('rgb' => '000000') ] ] ],
        $borderRB = [ 'borders' => [ 'right'  => [ 'style' => PHPExcel_Style_Border::BORDER_MEDIUM, 'color' => array('rgb' => '000000') ] ] ],
            
        $borderBLight = [ 'borders' => [ 'bottom'  => [ 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'DADADA') ] ] ],
        $borderRLight = [ 'borders' => [ 'right'  => [ 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'DADADA') ] ] ],    

        $titleFont = [ 'font' => [ 'name' => 'Times New Roman', 'size' => 14, 'bold' => true ] ],
            
        $bgYellow  = [ 'fill' => [ 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'ffffcc'] ] ];    
    
    public function setCellW($sheet, string $col, float $val) { $sheet->getColumnDimension($col)->setWidth($val + 0.71); }
    
    private static $ttl_cell_fnt = [ 'font' => [ 'name' => 'Calibri', 'size' => 11, 'bold' => true ] ];
    
    // used
    private function rdp_stdTtlCell($sheet, string $cell, string $text, bool $vert = false) {
        $style = $sheet->getStyle($cell);
        $style->applyFromArray(self::$ttl_cell_fnt);
        
        $align = $style->getAlignment();
        
        $align->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
              ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
              ->setWrapText(true);
        
        if ($vert)
            $align->setTextRotation(90); // Rotation - поворот
        
        $sheet->setCellValue($cell, $text);
    }
    
      private static $cell_bg_clr   = [ 'fill' => [ 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF'] ] ];
      
                       // unused
    private function rdp_stdGroupCell($sheet, string $cell, string $text) { // used only in rdp_makeGroupTitle (rdp_makeGroupTitle - unused)
        $fnt = [ 'font' => [ 'name' => 'Calibri', 'size' => 12, 'bold' => true ] ];
        
        $style = $sheet->getStyle($cell);
        $style->applyFromArray($fnt);

        $style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                              ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                              //->setWrapText(true);
        
        self::$cell_bg_clr['fill']['color']['rgb'] = 'FFFFCC';
        $style->applyFromArray(self::$cell_bg_clr);
        
        $sheet->setCellValue($cell, $text);
    }
    
        private static $std_cell_fnt = [ 'font' => [ 'name' => 'Calibri', 'size' => 11 ] ];
    
        // создает ячейки, пользуюсь ей
    private function rdp_stdDataCell($sheet, string $cell, string $text, string $bcolor = "FFFFFF") {
        /*
        if ($bcolor != "FFFFFF") {
            self::$cell_bg_clr['fill']['color']['rgb'] = $bcolor;
            $sheet->getStyle($cell)->applyFromArray(self::$cell_bg_clr);
        }
        */
        
        $sheet->setCellValue($cell, $text);
    }
    
    private static $foot_cell_fnt = [ 'font' => [ 'name' => 'Calibri', 'size' => 12, 'bold' => true ] ];
        
    private function rdp_makeFooterCell($sheet, string $cell, string $text, string $halign, string $tcolor, string $bcolor) {
        switch (strtoupper($halign)) {
            case 'LEFT':   $halign_ = PHPExcel_Style_Alignment::HORIZONTAL_LEFT; break;
            case 'CENTER': $halign_ = PHPExcel_Style_Alignment::HORIZONTAL_CENTER; break;
            default:       $halign_ = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
        }
        
        $style = $sheet->getStyle($cell);
        $style->applyFromArray(self::$foot_cell_fnt);

        $style->getAlignment()->setHorizontal($halign_)
                              ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        
        $style->getFont()->getColor()->setRGB($tcolor);
        
        self::$cell_bg_clr['fill']['color']['rgb'] = $bcolor;
        $style->applyFromArray(self::$cell_bg_clr);
        
        $sheet->setCellValue($cell, $text);
    }
    
    private function reestr_grpFooterCell($sheet, string $cell, string $text, string $halign = 'RIGHT', string $tcolor = 'C00000') {
        $this->rdp_makeFooterCell($sheet, $cell, $text, $halign, $tcolor, 'FFFFFF');
    }
    
     private function reestr_totalFooterCell($sheet, string $cell, string $text, string $halign = 'RIGHT', string $tcolor = 'C00000') {
        $this->rdp_makeFooterCell($sheet, $cell, $text, $halign, $tcolor, 'FFFFFF');
    }
    
    private function rdp_repFooterCell($sheet, string $cell, string $text, string $halign = 'RIGHT', string $tcolor = 'C00000') {
        $this->rdp_makeFooterCell($sheet, $cell, $text, $halign, $tcolor, 'FFFFCC');
    }
    
    private function rdp_stdCommentCell($sheet, string $cell, string $text) {
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                               ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                                               ->setWrapText(false);
        
        $sheet->setCellValue($cell, $text);
    }
    
     public function make_yellow_Subtitle($sheet, $lastrow, $road_ttl, $lastcol){      
        $row = strval(++$lastrow);
        
        foreach($sheet->getColumnIterator('A', $lastcol) as $col){
            $sheet->getStyle($col->getColumnIndex() . $row)->applyFromArray(self::$borderBB);
            $sheet->getStyle($col->getColumnIndex() . $row)->applyFromArray(self::$bgYellow);
        }
                                                                                        // раньше вместо $road_ttl искался obj_nm в carr_pasport 
                                                                                        // Для досс не будет работать
         $sheet->mergeCells("B".$row.":".$lastcol.$row); $this->rdp_stdTtlCell($sheet, 'B'.$row, $road_ttl);
        
        $sheet->getStyle('A'.$row)->applyFromArray(self::$borderRB);
       
        $sheet->getStyle($lastcol.$row)->applyFromArray(self::$borderRB);

        return $row;
    }
    
   public function make_yellow_Subtitle_by_name($sheet, $lastrow, $ttl, $lastcol){

        $row = strval(++$lastrow);
        
        foreach($sheet->getColumnIterator('A', $lastcol) as $col){
            $sheet->getStyle($col->getColumnIndex() . $row)->applyFromArray(self::$borderBB);
            $sheet->getStyle($col->getColumnIndex() . $row)->applyFromArray(self::$bgYellow);
        }
        
         $sheet->mergeCells("B".$row.":".$lastcol.$row); $this->rdp_stdTtlCell($sheet, 'B'.$row, $ttl);
        
        $sheet->getStyle('A'.$row)->applyFromArray(self::$borderRB);
       
        $sheet->getStyle($lastcol.$row)->applyFromArray(self::$borderRB);

        return $row;
    }
    
    public function subTitle_by_nm_direction_4_mdl($sheet, $lastrow ,$nm){
        foreach($sheet->getColumnIterator('A', 'AE') as $col){ 
             $sheet->getStyle($col->getColumnIndex() . $lastrow)->applyFromArray(self::$borderBB); 
        }
        
        $sheet->getStyle('A'. $lastrow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('AE'. $lastrow)->applyFromArray(self::$borderRB);
        
        $sheet->mergeCells("B".$lastrow.":AE".$lastrow); $this->rdp_stdTtlCell($sheet, "B".$lastrow, $nm);
        
        return $lastrow;
    }
    
    
        private function mdl_makeReportTitle_DSS($sheet) : int {    // result: last data row number
        
        $sheet->getStyle('B13')->applyFromArray(self::$titleFont);       
        $sheet->mergeCells("B13:O13");
        $sheet->setCellValue("B13", 'РЕЕСТР ДОСТУПНОСТИ ДЛЯ ПАССАЖИРОВ ИЗ ЧИСЛА ИНВАЛИДОВ МОДЕЛИ ВАГОНА ДИРЕКЦИИ СКОРОСТНОГО СООБЩЕНИЯ-ФИЛИАЛА ОАО "РЖД"');

        // col whidts
        $this->setCellW($sheet, 'A',  3.57);
        $this->setCellW($sheet, 'B',  30.14);
        $this->setCellW($sheet, 'C',  26.57);
        $this->setCellW($sheet, 'D',  13.43);
        $this->setCellW($sheet, 'E',  30.57);
        $this->setCellW($sheet, 'F',  31.29);
        $this->setCellW($sheet, 'G',  3.29);
        $this->setCellW($sheet, 'H',  71.14);
        $this->setCellW($sheet, 'I',  15.14);
        $this->setCellW($sheet, 'J',  3.00);
        $this->setCellW($sheet, 'K',  3.00);
        $this->setCellW($sheet, 'L',  40.57);
        $this->setCellW($sheet, 'M',  21.86);
        $this->setCellW($sheet, 'N',  26.86);
        $this->setCellW($sheet, 'O',  13.00);
        $this->setCellW($sheet, 'P',  8.43);
        $this->setCellW($sheet, 'Q',  6.14);
        $this->setCellW($sheet, 'R',  8.43);
        $this->setCellW($sheet, 'S',  8.71);
                
        for($i = 1; $i < 15 ; $i++){
            $sheet->getRowDimension(strval($i))->setRowHeight(15);
        }
        
        $sheet->getRowDimension("15")->setRowHeight(15.75); 
        $sheet->getRowDimension("16")->setRowHeight(18);    
        $sheet->getRowDimension("17")->setRowHeight(42);
        $sheet->getRowDimension("18")->setRowHeight(54.75);

        // TITLE
        
        // Horizontal cell borders
        foreach ($sheet->getColumnIterator('A',  'S') as $col) {
            $sheet->getStyle($col->getColumnIndex() . '15')->applyFromArray(self::$borderTB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderBB); 
        }
        
        foreach($sheet->getColumnIterator('C',  'I') as $col){
             $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderB);
        }
        
        $sheet->getStyle('M17')->applyFromArray(self::$borderB);
        
        foreach($sheet->getColumnIterator('A', 'B') as $col){
            $sheet->getStyle($col->getColumnIndex() . '15')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
        
        foreach($sheet->getColumnIterator('C') as $col){
                $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderR);
                $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderR);
        }
        
        foreach($sheet->getColumnIterator('D') as $col){
            $sheet->getStyle($col->getColumnIndex() . '15')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
        foreach($sheet->getColumnIterator('E') as $col){
                $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderR);
                $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderR);
        }
        
        foreach($sheet->getColumnIterator('F') as $col){
            $sheet->getStyle($col->getColumnIndex() . '15')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
          foreach($sheet->getColumnIterator('H') as $col){
                $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderR);
                $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderR);
        }
        
         foreach($sheet->getColumnIterator('I') as $col){
            $sheet->getStyle($col->getColumnIndex() . '15')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
          foreach($sheet->getColumnIterator('K', 'O') as $col){
            $sheet->getStyle($col->getColumnIndex() . '15')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
         foreach($sheet->getColumnIterator('Q') as $col){
            $sheet->getStyle($col->getColumnIndex() . '15')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
        foreach($sheet->getColumnIterator('S') as $col){
            $sheet->getStyle($col->getColumnIndex() . '15')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }

        $sheet->mergeCells("A15:A18"); $this->rdp_stdTtlCell($sheet, 'A15', '№ п/п');
        $sheet->mergeCells("B15:B18"); $this->rdp_stdTtlCell($sheet, 'B15', 'Наименование балансодержателя');
        
        $sheet->mergeCells("C15:D16"); $this->rdp_stdTtlCell($sheet, 'C15', "1. Общие сведения об объекте");
          
        $sheet->mergeCells("C17:C18"); $this->rdp_stdTtlCell($sheet, 'C17', "Модель вагона");
        $sheet->mergeCells("D17:D18"); $this->rdp_stdTtlCell($sheet, 'D17', "Дата регистрации и № паспорта");   
        
        $sheet->mergeCells("E15:F16"); $this->rdp_stdTtlCell($sheet, 'E15', "2. Оценка доступности*");
        
        $sheet->mergeCells("E17:E18"); $this->rdp_stdTtlCell($sheet, 'E17', "Требования действующей нормативной документации, регламентирующей технические требования для перевозки инвалидов, достигнуты при постройке");
        $sheet->mergeCells("F17:F18"); $this->rdp_stdTtlCell($sheet, 'F17', "Требования действующей нормативной документации,регламентирующей технические  требования для перевозки инвалидов, достигнуты при модернизации ");
        
        $sheet->mergeCells("G15:I16"); $this->rdp_stdTtlCell($sheet, 'G15', "3. Требуемые мероприятия по адаптации и рекомендованный период их проведения");
        $sheet->mergeCells("G17:H18"); $this->rdp_stdTtlCell($sheet, 'G17', "Виды работ по адаптации");
        $sheet->mergeCells("I17:I18"); $this->rdp_stdTtlCell($sheet, 'I17', "Плановый период (срок) исполнения");
        
         // Vertical cell borders
        for ($i = 15; $i <= 18; ++$i) {
            $sheet->getStyle('J' . strval($i))->applyFromArray(self::$borderLB)
                                              ->applyFromArray(self::$borderRB);
            
             $sheet->getStyle('K' . strval($i))->applyFromArray(self::$borderRB);
        }
        
        $sheet->mergeCells("J15:K18"); $this->rdp_stdTtlCell($sheet, 'J15', "Отметка о выполнении работ по адаптации", true);
        
        $sheet->mergeCells("L15:L18"); $this->rdp_stdTtlCell($sheet, 'L15', "Причины невыполнения");
        
        $sheet->mergeCells("M15:M17"); $this->rdp_stdTtlCell($sheet, 'M15', "4. Ожидаемый результат состояния доступности после выполнения работ по адаптации");
        $this->rdp_stdTtlCell($sheet, 'M18', "Ожидаемый результат по (состоянию доступности)");
        
        $sheet->mergeCells("N15:N18"); $this->rdp_stdTtlCell($sheet, 'N15', "5. Рекомендации по использованию объекта транспортной инфраструктуры для обслуживания инвалидов");
        $sheet->mergeCells("O15:O18"); $this->rdp_stdTtlCell($sheet, 'O15', "Дата актуализации информации");
        $sheet->mergeCells("P15:Q18"); $this->rdp_stdTtlCell($sheet, 'P15', "**Отметка об участии общественных объединений инвалидов в проведении обследовании и паспортизации");
        $sheet->mergeCells("R15:S18"); $this->rdp_stdTtlCell($sheet, 'R15', "Примечание");  
        
        return 18;  // last data row number
    }
    
    private function mdl_makeReportTitle_FPK($sheet, $ttl = '') : int {    // result: last data row number
        
        $sheet->getStyle('B13')->applyFromArray(self::$titleFont);       
        $sheet->mergeCells("B13:AA13");
        
        if(strlen($ttl) > 0){
           $sheet->setCellValue("B13", 'РЕЕСТР ДОСТУПНОСТИ ДЛЯ ПАССАЖИРОВ ИЗ ЧИСЛА ИНВАЛИДОВ МОДЕЛИ ВАГОНА '.mb_strtoupper($ttl));
        }else{
           $sheet->setCellValue("B13", 'РЕЕСТР ДОСТУПНОСТИ ДЛЯ ПАССАЖИРОВ ИЗ ЧИСЛА ИНВАЛИДОВ МОДЕЛЕЙ ВАГОНОВ');
        } 

        // col whidts
        $this->setCellW($sheet, 'A',  3.57);
        $this->setCellW($sheet, 'B',  31.57);
        $this->setCellW($sheet, 'C',  26.57);
        $this->setCellW($sheet, 'D',  13.43);
        $this->setCellW($sheet, 'E',  20.71);
        $this->setCellW($sheet, 'F',  20.14);
        $this->setCellW($sheet, 'G',  3.14);
        $this->setCellW($sheet, 'H',  3.14);
        $this->setCellW($sheet, 'I',  3.14);
        $this->setCellW($sheet, 'J',  3.29);
        $this->setCellW($sheet, 'K',  3.00);
        $this->setCellW($sheet, 'L',  3.29);
        $this->setCellW($sheet, 'M',  3.29);
        $this->setCellW($sheet, 'N',  3.57);
        $this->setCellW($sheet, 'O',  3.29);
        $this->setCellW($sheet, 'P',  3.29);
        $this->setCellW($sheet, 'Q',  3.14);
        $this->setCellW($sheet, 'R',  3.14);
        $this->setCellW($sheet, 'S',  3);
        $this->setCellW($sheet, 'T',  71);
        $this->setCellW($sheet, 'U',  15.14);
        $this->setCellW($sheet, 'V',  3);
        $this->setCellW($sheet, 'W', 3);
        $this->setCellW($sheet, 'X', 40.57);
        $this->setCellW($sheet, 'Y', 20.00);
        $this->setCellW($sheet, 'Z', 26.86);
        $this->setCellW($sheet, 'AA', 13);
        $this->setCellW($sheet, 'AB', 8.43);
        $this->setCellW($sheet, 'AC', 6.14);
        $this->setCellW($sheet, 'AD', 8.43);
        $this->setCellW($sheet, 'AE', 8.71);
                
        for($i = 1; $i < 15 ; $i++){
            $sheet->getRowDimension(strval($i))->setRowHeight(15);
        }
        
        $sheet->getRowDimension("15")->setRowHeight(15.75); 
        $sheet->getRowDimension("16")->setRowHeight(18);    
        $sheet->getRowDimension("17")->setRowHeight(39);
        $sheet->getRowDimension("18")->setRowHeight(122.5);

        // TITLE
        
        // Horizontal cell borders
        foreach ($sheet->getColumnIterator('A',  'AE') as $col) {
            $sheet->getStyle($col->getColumnIndex() . '15')->applyFromArray(self::$borderTB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderBB); 
        }
        
        foreach($sheet->getColumnIterator('A', 'B') as $col){
            $sheet->getStyle($col->getColumnIndex() . '15')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
        
        foreach($sheet->getColumnIterator('C') as $col){
                $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderR);
                $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderR);
        }
        
        foreach($sheet->getColumnIterator('D') as $col){
            $sheet->getStyle($col->getColumnIndex() . '15')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
        foreach($sheet->getColumnIterator('E') as $col){
                $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderR);
                $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderR);
        }
        
        foreach($sheet->getColumnIterator('F') as $col){
            $sheet->getStyle($col->getColumnIndex() . '15')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
          foreach($sheet->getColumnIterator('G', 'H') as $col){
                $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderR);
        }
        
         foreach($sheet->getColumnIterator('I') as $col){
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
          foreach($sheet->getColumnIterator('J', 'K') as $col){
                $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderR);
        }
        
         foreach($sheet->getColumnIterator('L') as $col){
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
          foreach($sheet->getColumnIterator('M', 'N') as $col){
                $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderR);
        }
        
        
         foreach($sheet->getColumnIterator('O') as $col){
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
          foreach($sheet->getColumnIterator('P', 'Q') as $col){
                $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderR);
        }
        
        foreach($sheet->getColumnIterator('R') as $col){
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
          foreach($sheet->getColumnIterator('T') as $col){
                $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderR);
                $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderR);
        }
        
         foreach($sheet->getColumnIterator('U', 'AE') as $col){
            $sheet->getStyle($col->getColumnIndex() . '15')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
        }
        
        foreach($sheet->getColumnIterator('C', 'Y') as $col){
                $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderB);
                $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderB);
        }

        $sheet->mergeCells("A15:A18"); $this->rdp_stdTtlCell($sheet, 'A15', '№ п/п');
        $sheet->mergeCells("B15:B18"); $this->rdp_stdTtlCell($sheet, 'B15', 'Наименование балансодержателя');
        
        $sheet->mergeCells("C15:D16"); $this->rdp_stdTtlCell($sheet, 'C15', "1. Общие сведения об объекте");
          
        $sheet->mergeCells("C17:C18"); $this->rdp_stdTtlCell($sheet, 'C17', "Модель вагона");
        $sheet->mergeCells("D17:D18"); $this->rdp_stdTtlCell($sheet, 'D17', "Дата регистрации и № паспорта");   
        
        $sheet->mergeCells("E15:F16"); $this->rdp_stdTtlCell($sheet, 'E15', "2. Оценка доступности*");
        
        $sheet->mergeCells("E17:E18"); $this->rdp_stdTtlCell($sheet, 'E17', "Требования действующей нормативной документации, регламентирующей технические требования для перевозки инвалидов, достигнуты при постройке");
        $sheet->mergeCells("F17:F18"); $this->rdp_stdTtlCell($sheet, 'F17', "Требования действующей нормативной документации,регламентирующей технические  требования для перевозки инвалидов, достигнуты при модернизации ");
        
        $sheet->mergeCells("G15:R16"); $this->rdp_stdTtlCell($sheet, 'G15', "Оценка доступности моделей вагонов по категориям маломобильных пассажиров");
        $sheet->mergeCells("G17:I17"); $this->rdp_stdTtlCell($sheet, 'G17', "K");
        $sheet->mergeCells("J17:L17"); $this->rdp_stdTtlCell($sheet, 'J17', "О");
        $sheet->mergeCells("M17:O17"); $this->rdp_stdTtlCell($sheet, 'M17', "С");
        $sheet->mergeCells("P17:R17"); $this->rdp_stdTtlCell($sheet, 'P17', "Г");
        
        $arrCol = ['G' => 'ДП', 'H' => 'НД', 'I' => 'ДЧ', 'J' => 'ДП', 'K' => 'НД', 'L' => 'ДЧ', 'M' => 'ДП', 'N' => 'НД', 'O' => 'ДЧ', 'P' => 'ДП', 'Q' => 'НД', 'R' => 'ДЧ'];
        
      foreach($arrCol as $key => $val)
               $this->rdp_stdTtlCell($sheet, $key.'18', $val);

        $sheet->mergeCells("S15:U16"); $this->rdp_stdTtlCell($sheet, 'S15', "3. Требуемые мероприятия по адаптации и рекомендованный период их проведения");
        $sheet->mergeCells("S17:T18"); $this->rdp_stdTtlCell($sheet, 'S17', "Виды работ по адаптации");
        $sheet->mergeCells("U17:U18"); $this->rdp_stdTtlCell($sheet, 'U17', "Плановый период (срок) исполнения");
        
        // Vertical cell borders
        for ($i = 15; $i <= 18; ++$i) {
            $sheet->getStyle('V' . strval($i))->applyFromArray(self::$borderLB)
                                              ->applyFromArray(self::$borderRB);
            
             $sheet->getStyle('W' . strval($i))->applyFromArray(self::$borderRB);
        }
        
        $sheet->mergeCells("V15:W18"); $this->rdp_stdTtlCell($sheet, 'V15', "Отметка о выполнении работ по \nадаптации", true);
        
        $sheet->mergeCells("X15:X18"); $this->rdp_stdTtlCell($sheet, 'X15', "Причины не выполнения");
        $sheet->mergeCells("Y15:Y17"); $this->rdp_stdTtlCell($sheet, 'Y15', "4. Ожидаемый результат состояния доступности после выполнения работ по адаптации");
        $this->rdp_stdTtlCell($sheet, 'Y18', "Ожидаемый результат по (состоянию доступности)");
        
        $sheet->mergeCells("Z15:Z18"); $this->rdp_stdTtlCell($sheet, 'Z15', "5. Рекомендации по использованию объекта транспортной инфраструктуры для обслуживания инвалидов");
        
        $sheet->mergeCells("AA15:AA18"); $this->rdp_stdTtlCell($sheet, 'AA15', "Дата актуализации информации");
        $sheet->mergeCells("AB15:AC18"); $this->rdp_stdTtlCell($sheet, 'AB15', "**Отметка об участии общественных объединений инвалидов в проведении обследовании и паспортизации");
        $sheet->mergeCells("AD15:AE18"); $this->rdp_stdTtlCell($sheet, 'AD15', "Примечание");
  
        return 18;  // last data row number
    }
    
    
    private function rdp_makeGroupTitle($sheet, int $from_row, int $grp_num, array $rws_rec) : int {    // result: last data row number
        $result = $from_row;
        
        if (count($rws_rec) > 0) {
            $srow = strval($from_row);
                
            foreach ($sheet->getColumnIterator('A', 'AX') as $col) $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$borderBB);
            
            $style_ = $sheet->getStyle('A' . $srow);
            
            $style_->applyFromArray(self::$borderLB) 
                   ->applyFromArray(self::$borderRB);
            
            $sheet->getStyle('AX' . $srow)->applyFromArray(self::$borderRB);
            
            $sheet->mergeCells("B" . $srow . ":AX" . $srow);
            
            $this->rdp_stdGroupCell($sheet, 'A' . $srow, $grp_num);
            $this->rdp_stdGroupCell($sheet, 'B' . $srow, $rws_rec['obj_nm']);
        }
        
        return $result;
    }
    
    
        private function reestr_makeItemRow_DSS($db, $sheet, int $from_row, int $row_num, array $record) : int {    // result: last data row number
        $result = $from_row;
        
        if (count($record) > 0) {
            //$db = new _dbpoints();
            $action = $db->get_actionsList($record['rid']);
            //unset($db);
            
            $action_cnt = count($action);
            
         
            
            if ($action_cnt > 0) {
                
                if($action_cnt > 1){
                    for ($i = $from_row; $i < $from_row + $action_cnt; ++$i)  // Set rows heights to auto
                        $sheet->getRowDimension($i)->setRowHeight(-1);
                }else $sheet->getRowDimension($from_row)->setRowHeight(45);
                
                
                $clr = 'FFFFCC';  // temporarily moved above for
                
                for ($i = 0; $i < $action_cnt; ++$i) {
                    $srow = strval($from_row + $i);
  
                    $done = ($action[$i]['flg'] >> 24) & 0x3;

                    $pp = $action[$i]['flg'] & 0xFFFFFF; // $pp - plaining period
                    $year = $action[$i]['flg'] & 0xFFF;

                    $str = '';
            
                    if($year > 0){             
                        $quarter = ($action[$i]['flg'] >> 12) & 0xF;
                        if($quarter> 0){
                               $str = $quarter .' кв. '. $year;                       
                        }
                    }else{
                         $select = ($action[$i]['flg']  >> 16) & 0xFF;
                         $str = assist::$plaining_period[$select];
                    }

                
                    $this->rdp_stdDataCell($sheet, 'H' . $srow, $action[$i]['twa']);
                                                
                    $this->rdp_stdDataCell($sheet, 'J' . $srow, ($done == 1 ? 1 : ""), $clr);
                    $this->rdp_stdDataCell($sheet, 'K' . $srow, ($done == 2 ? 1 : ""), $clr);
                    $this->rdp_stdDataCell($sheet, 'L' . $srow, $action[$i]['pnv']);
                    
                    
                    
                    if(mb_strlen($str) > 0){
                        $this->rdp_stdDataCell($sheet, 'I' . $srow, $str);
                    }  
                    
                    if ($i < ($action_cnt - 1)) {                       
                        $sheet->getStyle('G' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('H' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('I' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('J' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('K' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('L' . $srow)->applyFromArray(self::$borderB);
                    }
                }
                
                $result += $action_cnt - 1;
                  // file_put_contents('c:/TEMP/php_debug1.txt', $result);
            }
            
            
            else $sheet->getRowDimension($from_row)->setRowHeight(45);

            $sresult = strval($result);
            
            foreach ($sheet->getColumnIterator('A', 'S') as $col) $sheet->getStyle($col->getColumnIndex() . $sresult)->applyFromArray(self::$borderBB);

            for ($i = $from_row; $i <= $result; ++$i) {
                $srow = strval($i);
                $sheet->getStyle('A' . $srow)->applyFromArray(self::$borderRB);
                
                $sheet->getStyle('B' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('D' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('F' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('G' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('Q' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('S' . $srow)->applyFromArray(self::$borderRB);
                
                foreach ($sheet->getColumnIterator('I', 'O') as $col)
                        $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$borderRB);
                
                $sheet->getStyle('C' . $srow)->applyFromArray(self::$borderR);
                $sheet->getStyle('E' . $srow)->applyFromArray(self::$borderR);
                $sheet->getStyle('H' . $srow)->applyFromArray(self::$borderR);
                
            }

            $stop = strval($from_row);

            $flg = ($record['flg'] & 0x3FF);
            $m_ooi = ($flg >> 8) & 0x3;
            
            if ($result >= $from_row) {
                $sbot = strval($result);
                
                $sheet->mergeCells("A" . $stop . ":A" . $sbot);
                $sheet->mergeCells("B" . $stop . ":B" . $sbot);
                $sheet->mergeCells("C" . $stop . ":C" . $sbot);
                $sheet->mergeCells("D" . $stop . ":D" . $sbot);
                $sheet->mergeCells("E" . $stop . ":E" . $sbot);
                $sheet->mergeCells("F" . $stop . ":F" . $sbot);
                $sheet->mergeCells("M" . $stop . ":M" . $sbot);
                $sheet->mergeCells("N" . $stop . ":N" . $sbot);
                $sheet->mergeCells("O" . $stop . ":O" . $sbot);
                $sheet->mergeCells("P" . $stop . ":Q" . $sbot);
                $sheet->mergeCells("R" . $stop . ":S" . $sbot);
            }

            $this->rdp_stdDataCell($sheet, 'A' . $stop, $row_num + 1);  // $row_num + 1: №пп

            $nm = $record['obj_nm'];
            if (mb_strlen($record['obj_nm']) > 0) $nm  = $record['obj_nm'] . " " . $nm;
            
            $this->rdp_stdDataCell($sheet, 'B' . $stop, $nm);
            $this->rdp_stdDataCell($sheet, 'C' . $stop, $record['mdl']);
            

            $this->rdp_stdDataCell($sheet, 'D' . $stop, $record['date_num_registr']);
            $this->rdp_stdDataCell($sheet, 'E' . $stop, $record['docs_at_constr']);
            $this->rdp_stdDataCell($sheet, 'F' . $stop, $record['docs_at_modern']); 

            $this->rdp_stdDataCell($sheet, 'M' . $stop, $record['exp_res']); 
            $this->rdp_stdDataCell($sheet, 'N' . $stop, $record['recoms_using']);
            $this->rdp_stdDataCell($sheet, 'O' . $stop, _dbbasemy::dates_YYYYMMDD2RuD($record['date_act']));
            $this->rdp_stdDataCell($sheet, 'P' . $stop, ($m_ooi == 1) ? '1' : ' '); 
            $this->rdp_stdDataCell($sheet, 'R' . $stop, $record['note'] ); 
        }
        
        return $result;
    }
  // reestr_makeItemRow_FPK($db, $sheet, $lastrow + 1, $row_num, $elem)
    private function reestr_makeItemRow_FPK($db, $sheet, int $from_row, int $row_num, array $record, $ttl_current_road = '') : int {    // result: last data row number
        $result = $from_row;
        
        if (count($record) > 0) {
            //$db = new _dbpoints();
            $action = $db->get_actionsList($record['rid']);
            //unset($db);
            
            $action_cnt = count($action);
            
            if ($action_cnt > 0) {
                
                if($action_cnt > 1){
                   for ($i = $from_row; $i < $from_row + $action_cnt; ++$i)  // Set rows heights to auto                
                        $sheet->getRowDimension($i)->setRowHeight(-1);
                }else $sheet->getRowDimension($from_row)->setRowHeight(45);
            
                $clr = 'FFFFCC';  // temporarily moved above for
                
                for ($i = 0; $i < $action_cnt; ++$i) {
                    $srow = strval($from_row + $i);
  
                    $done = ($action[$i]['flg'] >> 24) & 0x3;

                    $pp = $action[$i]['flg'] & 0xFFFFFF; // $pp - plaining period
                    $year = $action[$i]['flg'] & 0xFFF;

                    $str = '';
            
                    if($year > 0){             
                        $quarter = ($action[$i]['flg'] >> 12) & 0xF;
                        if($quarter> 0){
                               $str = $quarter .' кв. '. $year;                       
                        }
                    }else{
                         $select = ($action[$i]['flg']  >> 16) & 0xFF;
                         $str = assist::$plaining_period[$select];
                    }

                    if(mb_strlen($str) > 0){
                        $this->rdp_stdDataCell($sheet, 'U' . $srow, $str);
                    } 
                
                    $this->rdp_stdDataCell($sheet, 'T' . $srow, $action[$i]['twa']);
                                                
                    $this->rdp_stdDataCell($sheet, 'V' . $srow, ($done == 1 ? 1 : ""), $clr);
                    $this->rdp_stdDataCell($sheet, 'W' . $srow, ($done == 2 ? 1 : ""), $clr);
                    $this->rdp_stdDataCell($sheet, 'X' . $srow, $action[$i]['pnv']);
                    
                    $sheet->getStyle('AS' . $srow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    
                    if ($i < ($action_cnt - 1)) {                       
                        $sheet->getStyle('S' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('T' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('U' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('V' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('W' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('X' . $srow)->applyFromArray(self::$borderB);
                    }                   
                }
                
                $result += $action_cnt - 1;
            }
            else $sheet->getRowDimension($from_row)->setRowHeight(45);

            $sresult = strval($result);
            
            foreach ($sheet->getColumnIterator('A', 'AE') as $col) $sheet->getStyle($col->getColumnIndex() . $sresult)->applyFromArray(self::$borderBB);

            for ($i = $from_row; $i <= $result; ++$i) {
                $srow = strval($i);
                $sheet->getStyle('A' . $srow)->applyFromArray(self::$borderLB)
                                             ->applyFromArray(self::$borderRB);
                
                $sheet->getStyle('B' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('D' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('F' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('I' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('L' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('O' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('R' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('S' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('U' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('V' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('W' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('X' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('Y' . $srow)->applyFromArray(self::$borderRB);
                
                foreach ($sheet->getColumnIterator('X', 'AA') as $col)
                        $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$borderRB);
                
                $sheet->getStyle('AC' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('AE' . $srow)->applyFromArray(self::$borderRB);

                $sheet->getStyle('C' . $srow)->applyFromArray(self::$borderR);
                $sheet->getStyle('E' . $srow)->applyFromArray(self::$borderR);
            
                
                foreach ($sheet->getColumnIterator('G', 'H') as $col)
                        $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$borderR);

                foreach ($sheet->getColumnIterator('J', 'K') as $col)
                        $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$borderR);
                
                  foreach ($sheet->getColumnIterator('M', 'N') as $col)
                        $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$borderR);
                  
                  foreach ($sheet->getColumnIterator('P', 'Q') as $col)
                        $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$borderR);

                $sheet->getStyle('T' . $srow)->applyFromArray(self::$borderR);
            }

            $stop = strval($from_row);

            $flg = ($record['flg'] & 0x3FF);
            $m_ooi = ($flg >> 8) & 0x3;
            
            if ($result >= $from_row) {
                $sbot = strval($result);
                
                $sheet->mergeCells("A" . $stop . ":A" . $sbot);
                $sheet->mergeCells("B" . $stop . ":B" . $sbot);
                $sheet->mergeCells("C" . $stop . ":C" . $sbot);
                $sheet->mergeCells("D" . $stop . ":D" . $sbot);
                $sheet->mergeCells("E" . $stop . ":E" . $sbot);
                $sheet->mergeCells("F" . $stop . ":F" . $sbot);
                $sheet->mergeCells("Y" . $stop . ":Y" . $sbot);
                $sheet->mergeCells("Z" . $stop . ":Z" . $sbot);
                $sheet->mergeCells("AA" . $stop . ":AA" . $sbot);
                $sheet->mergeCells("AB" . $stop . ":AC" . $sbot); // $this->rdp_stdDataCell($sheet, 'AB' . $stop, ($m_ooi == 1) ? '1' : ' '); 
                $sheet->mergeCells("AD" . $stop . ":AE" . $sbot);
            }

            $this->rdp_stdDataCell($sheet, 'A' . $stop, $row_num + 1);  // $row_num + 1: №пп

            //$nm = $record['obj_nm'];
            
            $nm = (strlen($ttl_current_road) > 0) ? $ttl_current_road :  $record['ttl'];
            $this->rdp_stdDataCell($sheet, 'B' . $stop, $nm); 
            /*
            if(strlen($ttl_current_road) > 0){
                $this->rdp_stdDataCell($sheet, 'B' . $stop, $ttl_current_road);
            }else{
                $this->rdp_stdDataCell($sheet, 'B' . $stop, $record['ttl']); 
            }
            */
            $this->rdp_stdDataCell($sheet, 'C' . $stop, $record['mdl']);
            

            $this->rdp_stdDataCell($sheet, 'D' . $stop, $record['date_num_registr']);
            $this->rdp_stdDataCell($sheet, 'E' . $stop, $record['docs_at_constr']);
            $this->rdp_stdDataCell($sheet, 'F' . $stop, $record['docs_at_modern']); 

            $this->rdp_stdDataCell($sheet, 'Y' . $stop, $record['exp_res']); 
            $this->rdp_stdDataCell($sheet, 'Z' . $stop, $record['recoms_using']);
            $this->rdp_stdDataCell($sheet, 'AA' . $stop, _dbbasemy::dates_YYYYMMDD2RuD($record['date_act']));
            $this->rdp_stdDataCell($sheet, 'AB' . $stop, ($m_ooi == 1) ? '1' : ' '); 
            $this->rdp_stdDataCell($sheet, 'AD' . $stop, $record['note'] ); 
 
            
            $col_1 = ''; 
            $col_2 = '';
            $col_3 = '';
            $col_4 = '';
            
            $k = $flg & 0x3;
            $o = ($flg >> 2) & 0x3;
            $c = ($flg >> 4) & 0x3;
            $g = ($flg >> 6) & 0x3;
            
            if($k == 1){
                $col_1 = 'G';
            }else if($k == 2){
                $col_1 = 'H';
            }else if($k == 3){
                $col_1 = 'I';
            }
            
            if($o == 1){
               $col_2 = 'J';
            }else if($o == 2){
               $col_2 = 'K';
            }else if($o == 3){
               $col_2 = 'L';  
            }
            
            if($c == 1){
               $col_3 = 'M';
            }else if($c == 2){
               $col_3 = 'N';
            }else if($c == 3){
               $col_3 = 'O';  
            }
            
            if($g == 1){
               $col_4 = 'P';
            }else if($g == 2){
               $col_4 = 'Q';
            }else if($g == 3){
               $col_4 = 'R';  
            }
            
           if(strlen($col_1) != 0) $this->rdp_stdDataCell($sheet, $col_1 . $stop, '1');
           if(strlen($col_2) != 0) $this->rdp_stdDataCell($sheet, $col_2 . $stop, '1');
           if(strlen($col_3) != 0) $this->rdp_stdDataCell($sheet, $col_3 . $stop, '1');
           if(strlen($col_4) != 0) $this->rdp_stdDataCell($sheet, $col_4 . $stop, '1');

        }
        
        return $result;
    }
    
    private function reestr_makeFooterRow_DSS($sheet, int $row) {
        foreach ($sheet->getColumnIterator('A', 'S') as $col) $sheet->getStyle($col->getColumnIndex() . strval($row))->applyFromArray(self::$borderBB);
        
        $srow = strval($row);

        $sheet->getStyle('A' . $srow)->applyFromArray(self::$borderRLight);
        
        $sheet->getStyle('B' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('D' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('F' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('G' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('I' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('J' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('K' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('L' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('M' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('N' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('O' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('Q' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('S' . $srow)->applyFromArray(self::$borderRB);
        

        $sheet->getStyle('C' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('E' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('H' . $srow)->applyFromArray(self::$borderR);

    }
    
     private function reestr_makeFooterRow_FPK($sheet, int $row) {
        foreach ($sheet->getColumnIterator('A', 'AE') as $col) $sheet->getStyle($col->getColumnIndex() . strval($row))->applyFromArray(self::$borderBB);
        
        $srow = strval($row);

        $sheet->getStyle('A' . $srow)->applyFromArray(self::$borderRLight);
        
        $sheet->getStyle('B' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('D' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('F' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('I' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('L' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('O' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('R' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('S' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('X' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('Z' . $srow)->applyFromArray(self::$borderRB);
        
        foreach ($sheet->getColumnIterator('U', 'AA') as $col)        
                $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$borderRB);
         $sheet->getStyle('AC' . $srow)->applyFromArray(self::$borderRB);
         $sheet->getStyle('AE' . $srow)->applyFromArray(self::$borderRB);

        $sheet->getStyle('C' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('E' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('G' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('H' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('J' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('K' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('M' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('N' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('P' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('Q' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('T' . $srow)->applyFromArray(self::$borderR);

    }
    
    private function f_sum(string $col, int $from, int $to) : string { return "=SUM(" . $col . strval($from) . ":" . $col . strval($to) . ")"; }
    
    private function f_divisor(string $col, int $from, int $to, $div) : string { return "=ROUND(SUM(" . $col . strval($from) . ":" . $col . strval($to) . ")/A". strval($to + 1).",2)" ; } //.""
    
    private function f_sum_div(string $col, int $from, int $to, string $col_div, int $row_div) : string { return "=ROUND(SUM(" . $col . strval($from) . ":" . $col . strval($to) . ")/" . $col_div . strval($row_div) . ",2)"; }
    
    
                 ///reestr_makeGroupFooter_DSS($sheet, $lastrow + 1, $data_top, $lastrow, $grp_row_num, 'по ' . $org_record['abb']);
    private function reestr_makeGroupFooter_DSS($sheet, int $row, int $calc_fr, int $calc_to, int $item_cnt, string $obj_nm) {
        
        $this->reestr_makeFooterRow_DSS($sheet, $row);
        
        $srow = strval($row);

        if (mb_strlen($obj_nm) > 0) $obj_nm = 'по дирекции';
        
        $this->reestr_grpFooterCell($sheet, 'A' . $srow, $item_cnt);
        $this->reestr_grpFooterCell($sheet, 'B' . $srow, $obj_nm, "CENTER");  
        
        foreach ($sheet->getColumnIterator('C', 'I') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");
        
        foreach ($sheet->getColumnIterator('J', 'K') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, $this->f_sum($col->getColumnIndex(), $calc_fr, $calc_to));
        
            $this->reestr_grpFooterCell($sheet, 'P' . $srow, $this->f_sum('P', $calc_fr, $calc_to));

        foreach ($sheet->getColumnIterator('R', 'S') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");
        
    }
     // reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $data_top, $lastrow, $grp_row_num, 'по ' . $org_record['abb'])
      private function reestr_makeGroupFooter_FPK($sheet, int $row, int $calc_fr, int $calc_to, int $item_cnt, string $text) {

        $this->reestr_makeFooterRow_FPK($sheet, $row);
        
        $srow = strval($row);

        // if (mb_strlen($obj_nm) > 0) $obj_nm = 'по филиалу';
        
        $this->reestr_grpFooterCell($sheet, 'A' . $srow, $item_cnt);
        $this->reestr_grpFooterCell($sheet, 'B' . $srow, $text, "CENTER");  
        
        foreach ($sheet->getColumnIterator('C', 'U') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");
        
        foreach ($sheet->getColumnIterator('V', 'W') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, $this->f_sum($col->getColumnIndex(), $calc_fr, $calc_to));
        
            $this->reestr_grpFooterCell($sheet, 'AB' . $srow, $this->f_sum('AB', $calc_fr, $calc_to));

        foreach ($sheet->getColumnIterator('AD', 'AE') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");
        
        return $row;
        
    }
                                     // $count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $v_w_ab, 'V', 'W', 'AB'
    public function mdl_totalFooter_FPK($count_iteration, $sheet, $lastrow, $calc_fr, $calc_to, $arr, $column_v, $column_w, $column_ab, $text){ //$column_arr $V, $W, $AB
        $this->reestr_makeFooterRow_FPK($sheet, $lastrow);
        
        $srow = strval($lastrow);

        $this->reestr_grpFooterCell($sheet, 'A' . $srow, $count_iteration);
        $this->reestr_grpFooterCell($sheet, 'B' . $srow, $text, "CENTER");  
        
        $sheet->getStyle('V' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        $sheet->getStyle('W' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        $sheet->getStyle('AB' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        
         foreach ($sheet->getColumnIterator('C', 'U') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");

         $str_v = $this->num_row_4_excel_sum($column_v, $arr);                           
         $this->rdp_stdDataCell($sheet, $column_v.$lastrow, '=SUM('.$str_v . ')');         
         
         $str_w = $this->num_row_4_excel_sum($column_w, $arr);                    
         $this->rdp_stdDataCell($sheet, $column_w.$lastrow, '=SUM('.$str_w . ')'); 

          $str_ab = $this->num_row_4_excel_sum($column_ab, $arr);
          $this->rdp_makeFooterCell($sheet, $column_ab.$lastrow, '=SUM('.$str_ab . ')', 'RIGHT', '#00010a', 'FFFFFF');

         
        foreach ($sheet->getColumnIterator('X', 'AA') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");     
        
        foreach ($sheet->getColumnIterator('AC', 'AE') as $col)
                 $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, ""); 
        
        foreach($sheet->getColumnIterator('A', 'AE') as $col){           
            $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$bgYellow);
        }
        
        return $lastrow;
    }
    
    
    public function mdl_totalFooter_DSS($count_iteration, $sheet, $lastrow, $calc_fr, $calc_to, $arr, $J, $K, $P){
        $this->reestr_makeFooterRow_DSS($sheet, $lastrow);
        
        $srow = strval($lastrow);
        
        $this->reestr_grpFooterCell($sheet, 'A' . $srow, $count_iteration);
        $this->reestr_grpFooterCell($sheet, 'B' . $srow, 'ВСЕГО по ДОСС', "CENTER");  
        
        foreach ($sheet->getColumnIterator('C', 'I') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");
        
        $str_j = $this->num_row_4_excel_sum($J, $arr);
        $this->rdp_stdDataCell($sheet, $J.$lastrow, '=SUM('.$str_j . ')'); 
        
        $str_k = $this->num_row_4_excel_sum($K, $arr);
        $this->rdp_stdDataCell($sheet, $K.$lastrow, '=SUM('.$str_k . ')'); 
        
        $str_p = $this->num_row_4_excel_sum($P, $arr);
        $this->rdp_makeFooterCell($sheet, $P.$lastrow, '=SUM('.$str_p . ')', 'RIGHT', '#00010a', 'FFFFFF');

        foreach ($sheet->getColumnIterator('Q', 'S') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");
        
        foreach($sheet->getColumnIterator('A', 'S') as $col){           
            $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$bgYellow);
        }
    }
                         //$count_iteration
    private function mdl_total_footer($column_a, $sheet, $lastrow, $calc_fr, $calc_to, $arr, $column_v, $column_w, $column_ab, $text){
        $this->reestr_makeFooterRow_FPK($sheet, $lastrow);
        
        $srow = strval($lastrow);

     //   $this->reestr_grpFooterCell($sheet, 'A' . $srow, $count_iteration);
        
        $str_a = $this->num_row_4_excel_sum($column_a, $arr);                           
        $this->reestr_grpFooterCell($sheet, $column_a.$lastrow, '=SUM('.$str_a . ')', "CENTER");  
        
        $this->reestr_grpFooterCell($sheet, 'B' . $srow, $text, "CENTER");  
        
        $sheet->getStyle('V' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        $sheet->getStyle('W' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        $sheet->getStyle('AB' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        
         foreach ($sheet->getColumnIterator('C', 'U') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");

         $str_v = $this->num_row_4_excel_sum($column_v, $arr);                           
         $this->rdp_stdDataCell($sheet, $column_v.$lastrow, '=SUM('.$str_v . ')');         
         
         $str_w = $this->num_row_4_excel_sum($column_w, $arr);                    
         $this->rdp_stdDataCell($sheet, $column_w.$lastrow, '=SUM('.$str_w . ')'); 

          $str_ab = $this->num_row_4_excel_sum($column_ab, $arr);
          $this->rdp_makeFooterCell($sheet, $column_ab.$lastrow, '=SUM('.$str_ab . ')', 'RIGHT', '#00010a', 'FFFFFF');

         
        foreach ($sheet->getColumnIterator('X', 'AA') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");     
        
        foreach ($sheet->getColumnIterator('AC', 'AE') as $col)
                 $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, ""); 
        
        foreach($sheet->getColumnIterator('A', 'AE') as $col){           
            $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$bgYellow);
        }
        
        return $lastrow;
    }
   
    
    private function num_row_4_excel_sum($col, $arr){
        $str = ''; 
         
        foreach($arr as $row){
              if(strlen($str) > 0)
                $str.= '+'; 
                $str .=  $col.$row;              
        }
        
        return $str;
    }

    private function rdp_footer_colSum(string $col, array $grp_rows) : string {
        $result = '';
        
        foreach ($grp_rows as $row) {
            if (strlen($result) > 0)
                $result .= '+';
            $result .= $col . $row;
        }
        
        return '=SUM(' . $result . ')';
    }

    
    private function bottomComment($sheet, int $lastrow) {
        // Comment
        $this->rdp_stdCommentCell($sheet, 'B' . strval($lastrow), "ПРИМЕЧАНИЕ");
        
        $this->rdp_stdCommentCell($sheet, 'B' . strval($lastrow + 2), "*указывается: ДП, ДП-И, ДЧ-И, ДУ, ВНД, ДЧ-В (см. п. 4.20 Методики)");
        $this->rdp_stdCommentCell($sheet, 'B' . strval($lastrow + 3), "** указывается: да-1, нет-0");
        
       
    }

    public function mdl_reestr(string $rid, $org_rid) : string {   // $mdl_rid
        $result = "";
        
        $db = new db_depo();
      //  $record = $db->carriage_getList_by_carr_mdl($mdl_rid);
        $record = $db->carriage_getList_by_carriage_rid($rid);
        
        if (count($record) > 0) {
          //  $rid_habb = $record['org'];

            $org_record = $db->org_getRec($org_rid);
            $habb = $org_record['habb'];
            $httl = $org_record['httl'];
            $road_ttl = $org_record['ttl'];
            
            // _assbase::siteRootDirX() uses path from $_SERVER['REQUEST_URI'], i.e. jgate.php. result - project dir, ex: '/dmgnI'
            $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
            $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
            
            if (_assbase::makeDir($report_dir)) {
               
                $report_file = substr(md5('report_by_mdl'), 0, 8). '.xlsx';  
                $xls = new PHPExcel();
                $xls->setActiveSheetIndex(0);
                
                $def_style = $xls->getDefaultStyle();
                $def_style->applyFromArray(self::$std_cell_fnt);
                $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                              ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                              ->setWrapText(true);
                
                // теперь одна функция для досс и фпк
                $this->mdl_reestr_FPK($db, $xls, $rid, $record, $httl, $road_ttl);
                
                /* НЕ УДАЛЯЙ ПОКА !!!
                для раздельных реестров ФПК и ДОСС

                if(preg_match('#ФПК#i' , $habb) == 1){ 
                             $this->mdl_reestr_FPK($db, $xls, $mdl_rid, $record);
                }else if(preg_match('#ДОСС#i' , $habb) == 1){
                    $this->mdl_reestr_DSS($db,$xls, $mdl_rid, $record);
                } 
                */
                
                $objWriter = new PHPExcel_Writer_Excel2007($xls);
                $objWriter->setPreCalculateFormulas(true);
                $objWriter->save($report_dir . '/' . $report_file);
                
                $result = $report_relpath . '/' . $report_file;
            }
        }
        unset($db);
        
        return $result;
    }   
    
    
    public function mdl_reestr_FPK($db, $xls, $rid, $record, $httl, $road_ttl){
        
        $sheet = $xls->getActiveSheet();
                
        $sheet->getSheetView()->setZoomScale(80); 

        $grp_num = 1;

        $lastrow = $this->mdl_makeReportTitle_FPK($sheet, $httl);
        $lastrow = $this->subTitle_by_nm_direction_4_mdl($sheet, $lastrow + 1, $httl);

       // $lastrow = $this->make_yellow_Subtitle($sheet, $lastrow, $rid, 'AE');
        $lastrow = $this->make_yellow_Subtitle($sheet, $lastrow, $road_ttl, 'AE');
         ++$grp_num;
         
        $data_top = ++$lastrow; // это $lastrow после желтого заголовка ( $lastrow = 20)
        $row_num = $group_item_cnt = 0; // $row_num - single excel row; $group_item_cnt - count of group elements (Data Rows)
         
          $lastrow = $this->reestr_makeItemRow_FPK($db, $sheet, $lastrow, $row_num, $record, $road_ttl);
          $data_bottom = $lastrow;   // здесь $lastrow = последнему ряду rdp_makeItemRow
          ++$group_item_cnt;

           // in multigroup reports should be group footers and summary (Data) footer
         $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $data_top, $data_bottom, $group_item_cnt, 'Итого');
          ++$lastrow;

        $this->bottomComment($sheet, $lastrow + 5);
    }
   
 //    mdl_reestr_DSS ПОКА НЕ ИСПОЛЬЗУЕТСЯ, ОНА ДЛЯ ОТДЕЛЬНОГО РЕЕСТРА ДОСС
    public function mdl_reestr_DSS($db, $xls, $mdl_rid, $record){
        $sheet = $xls->getActiveSheet();
                
        $sheet->getSheetView()->setZoomScale(80); 
        $grp_num = 1;
        
        $lastrow = $this->mdl_makeReportTitle_DSS($sheet);
        
       // теперь в нее надо передать ttl (именно заголовок), а не $mdl_rid                                        
        $lastrow = $this->make_yellow_Subtitle($sheet, $lastrow, $mdl_rid, 'S');
         ++$grp_num;
         
        $data_top = ++$lastrow; // это $lastrow после желтого заголовка ( $lastrow = 20)
        $row_num = $group_item_cnt = 0; // $row_num - single excel row; $group_item_cnt - count of group elements (Data Rows)
  
        $lastrow = $this->reestr_makeItemRow_DSS($db, $sheet, $lastrow, $row_num, $record); 
       
         $data_bottom = $lastrow;   // здесь $lastrow = последнему ряду rdp_makeItemRow
          ++$group_item_cnt;
          
          $this->reestr_makeGroupFooter_DSS($sheet, $lastrow + 1, $data_top, $data_bottom, $group_item_cnt, $record['obj_nm']);
          $this->bottomComment($sheet, $lastrow + 5);
    }
    
    public function road_reestr_group($org_rid){
       $db = new db_depo();
       $records = $db->carriage_getList($org_rid);          
       $result= '';
       
       if(count($records) > 0){
           $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
           $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
           
           if (_assbase::makeDir($report_dir)) {
               
                //$report_file = preg_replace('#"#' , "", $road_nm['abb']). '.xlsx';
               $report_file = substr(md5('report'), 0, 8). '.xlsx';  
                
                $xls = new PHPExcel();
                $xls->setActiveSheetIndex(0);
                
                
                $def_style = $xls->getDefaultStyle();
                $def_style->applyFromArray(self::$std_cell_fnt);
                $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                              ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                              ->setWrapText(true);
                
                $sheet = $xls->getActiveSheet();
                $sheet->getSheetView()->setZoomScale(80);
                
                $row_num = $grp_row_num  = 0;

                $org_record = $db->org_getRec($org_rid);
                $main_org_ttl = $org_record['httl'];
                $org_record_nm = $org_record['ttl'];
                               
               // if(preg_match('#ФПК#i', $road_nm['habb']) == 1){
                
                    $lastrow = $this->mdl_makeReportTitle_FPK($sheet, $main_org_ttl);
                    $lastrow = $this->subTitle_by_nm_direction_4_mdl($sheet, $lastrow + 1, $main_org_ttl);

                    $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $org_record_nm, 'AE');

                    $data_top = $lastrow + 1;

                    for($i = 0; $i < count($records); ++$i){
                            $row = $records[$i];

                            $passport_list = $db->carriage_getList_by_carriage_rid($row['rid']);

                            $lastrow = $this->reestr_makeItemRow_FPK($db, $sheet, $lastrow + 1, $row_num, $passport_list, $org_record_nm);
                            $row_num++;
                            $grp_row_num++;
                    }

                    $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $data_top, $lastrow, $grp_row_num, 'Всего по филиалу');
                     $grp_row_num = 0;
                
              // } ДЛЯ ОТДЕЛЬНОГО РЕЕСТРА ДОСС
               /*else if(preg_match('#ДОСС#i', $road_nm['habb']) == 1){
                   
                    $lastrow = $this->mdl_makeReportTitle_DSS($sheet);

                    $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $org_record_nm, 'S');

                     $data_top = $lastrow + 1;

                    for($i = 0; $i < count($records); ++$i){
                            $row = $records[$i];

                            $passport_list = $db->carriage_getList_by_carriage_rid($row['rid']);

                            $lastrow = $this->reestr_makeItemRow_DSS($db, $sheet, $lastrow + 1, $row_num, $passport_list);
                            $row_num++;
                            $grp_row_num++;
                    }

                    $this->reestr_makeGroupFooter_DSS($sheet, $lastrow + 1, $data_top, $lastrow, $grp_row_num, 'по ' . $org_record['abb']);
               }
               */ 
                
                $objWriter = new PHPExcel_Writer_Excel2007($xls);
                $objWriter->setPreCalculateFormulas(true);
                $objWriter->save($report_dir . '/' . $report_file);
                
                $result = $report_relpath . '/' . $report_file;
             
           }
       }     
       unset($db);        
       return $result; 
     }
     
    public function mdl_reestr_org(){ // $main_org_rid
       $db = new db_depo();
       $result= '';
       $main_org_list = $db->org_getList('');
       $_FPK = '';
       $_DSS = '';
       $_CDMV = '';
       
        foreach($main_org_list as $row){          
           if(preg_match('#ФПК#i', $row['abb']) == 1){
               $_FPK = strval($row['rid']);
           }else if(preg_match('#ДОСС#i' , $row['abb']) ==  1){
               $_DSS = strval($row['rid']);
           }else if(preg_match('#ЦДМВ#i' , $row['abb']) ==  1){
               $_CDMV = strval($row['rid']);
           }
       }
       
       $road_list_FPK = $db->org_getList($_FPK);
       $road_list_DSS = $db->org_getList($_DSS);
       $road_list_CDMV = $db->org_getList($_CDMV);
       
       if(count($road_list_DSS) > 0 || count($road_list_FPK) > 0 ){
           
            $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
            $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
            
            $report_file = substr(md5('reportall'), 0, 8). '.xlsx'; 
            
            $xls = new PHPExcel();
            $xls->setActiveSheetIndex(0);

            $def_style = $xls->getDefaultStyle();
            $def_style->applyFromArray(self::$std_cell_fnt);
            $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                          ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                          ->setWrapText(true);
                         
            $sheet = $xls->getActiveSheet();
            $sheet->getSheetView()->setZoomScale(80);            
            
            $lastrow = $this->mdl_makeReportTitle_FPK($sheet);
            
            $count_iteration = 0;                         
            $data_top = $data_bottom = 0;
            
            $total_arr = [];             
            $total_top = $lastrow + 1;
            $total_bottom = 0;
            
             if (count($road_list_DSS) > 0) {
                 
                 $text = 'по дирекции';
                 $org_text = 'ВСЕГО по дирекциям';
                                 
                 $data_top = $lastrow + 1;
                 $arr_cells_DSS = [];
                 
                 $lastrow = $this->subTitle_by_nm_direction_4_mdl($sheet, $lastrow + 1, 'ДИРЕКЦИЯ СКОРОСТНОГО СООБЩЕНИЯ ФИЛИАЛ ОАО "РЖД"');
                 
                 foreach($road_list_DSS as $row){
                     $ttl_current_road = $row['ttl'];
                     $road_mdl_list = $db->carriage_getList($row['rid']);
                     
                     if(count($road_mdl_list) > 0){
                         
                         $row_num = $grp_row_num  = 0; 
                         $road_record = $db->org_getRec($row['rid']);
                         $road_nm = $road_record['ttl'];
                         
                         $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $road_nm, 'AE');
                         $grp_start_row = $lastrow;
                         
                         // $road_mdl_list - массив carr_pasport, где org это rid дороги, дорог м.б. несколько дорожек
                         for($i = 0; $i < count($road_mdl_list); $i++){
                             $row = $road_mdl_list[$i];
                             
                             $carr_pasport_list = $db->carriage_getList_by_carriage_rid($row['rid']);
                             
                            $lastrow = $this->reestr_makeItemRow_FPK($db, $sheet, $lastrow + 1, $row_num, $carr_pasport_list, $ttl_current_road);
                            $row_num++;
                            $grp_row_num++;
                            $count_iteration++;
                         }
                         
                        $data_bottom = $lastrow; //                  $sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по2 ' . $current_org['abb'], $text
                        $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                     
                        $grp_row_num = 0; 
                        array_push($arr_cells_DSS, $lastrow);
                    }
                
                 }

                 $lastrow = $this->mdl_totalFooter_FPK($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $arr_cells_DSS, 'V', 'W', 'AB', $org_text); 
                 
                  array_push($total_arr, $lastrow);   
                 
             } if(count($road_list_FPK) > 0){
                  $count_iteration = 0;
                  $text = 'по филиалу';
                  $org_text = 'ВСЕГО по филиалам';
                  
                   $arr_cells_FPK = [];
                   
                   $lastrow = $this->subTitle_by_nm_direction_4_mdl($sheet, $lastrow + 1, '"АО" ФЕДЕРАЛЬНАЯ ПАССАЖИРСКАЯ КОМПАНИЯ');

                   foreach($road_list_FPK as $row){
                       $ttl_current_road = $row['ttl'];
                       $road_mdl_list = $db->carriage_getList($row['rid']);
                       
                       if(count($road_mdl_list) > 0){
                           $row_num = $grp_row_num  = 0; 
                           $road_record = $db->org_getRec($row['rid']);
                           $road_nm = $road_record['ttl'];
                            
                           $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $road_nm, 'AE');
                           $grp_start_row = $lastrow;
                           
                           for($i = 0; $i < count($road_mdl_list); ++$i){
                               $row = $road_mdl_list[$i];
                               
                               $carr_pasport_list = $db->carriage_getList_by_carriage_rid($row['rid']);
                               
                               $lastrow = $this->reestr_makeItemRow_FPK($db, $sheet, $lastrow + 1, $row_num, $carr_pasport_list, $ttl_current_road);
                               $row_num++;
                               $grp_row_num++;
                               $count_iteration++;
                           }
                           
                          $data_bottom = $lastrow; 
                          $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                     
                          $grp_row_num = 0; 
                          array_push($arr_cells_FPK, $lastrow);
                       }
                    
                   }
                   
                  $lastrow = $this->mdl_totalFooter_FPK($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $arr_cells_FPK, 'V', 'W', 'AB', $org_text); 
                 
                  array_push($total_arr, $lastrow);   
             }
             
             if(count($road_list_CDMV) > 0){
                  $count_iteration = 0;
                  $text = 'по филиалу';
                  $org_text = 'ВСЕГО по филиалам';
                  
                   $arr_cells_CDMV = [];
                   
                   $lastrow = $this->subTitle_by_nm_direction_4_mdl($sheet, $lastrow + 1, 'Центральная дирекция моторвагонного подвижного состава');

                   foreach($road_list_CDMV as $row){
                       $ttl_current_road = $row['ttl'];
                       $road_mdl_list = $db->carriage_getList($row['rid']);
                       
                       if(count($road_mdl_list) > 0){
                           $row_num = $grp_row_num  = 0; 
                           $road_record = $db->org_getRec($row['rid']);
                           $road_nm = $road_record['ttl'];
                            
                           $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $road_nm, 'AE');
                           $grp_start_row = $lastrow;
                           
                           for($i = 0; $i < count($road_mdl_list); ++$i){
                               $row = $road_mdl_list[$i];
                               
                               $carr_pasport_list = $db->carriage_getList_by_carriage_rid($row['rid']);
                               
                               $lastrow = $this->reestr_makeItemRow_FPK($db, $sheet, $lastrow + 1, $row_num, $carr_pasport_list, $ttl_current_road);
                               $row_num++;
                               $grp_row_num++;
                               $count_iteration++;
                           }
                           
                          $data_bottom = $lastrow; 
                          $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                     
                          $grp_row_num = 0; 
                          array_push($arr_cells_CDMV, $lastrow);
                       }
                    
                   }
                   
                  $lastrow = $this->mdl_totalFooter_FPK($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $arr_cells_CDMV, 'V', 'W', 'AB', $org_text); 
                 
                  array_push($total_arr, $lastrow);   
             }
             
             $this->mdl_total_footer('A', $sheet, $lastrow + 1, $data_top, $data_bottom, $total_arr, 'V', 'W', 'AB', 'Общий итог');
        }
        $objWriter = new PHPExcel_Writer_Excel2007($xls);
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save($report_dir . '/' . $report_file);

        $result = $report_relpath . '/' . $report_file;
      
       unset($db);        
       return $result; 
   }
   
   
   public function category_carriage_reestr($ini_str, $k, $o, $s, $g, $dp, $dc, $nd){ 
       $db = new db_depo();
       $records = $db->category_carriage_reestr($ini_str, $k, $o, $s, $g, $dp, $dc, $nd);     // переделай запрос с двумя лефт джоинами
       
        $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
        $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
        $report_file = substr(md5('report'), 0, 8). '.xlsx';              

        // $report_file = '1.xlsx';   // md5('report').

         $xls = new PHPExcel();
         $xls->setActiveSheetIndex(0);

         $def_style = $xls->getDefaultStyle();
         $def_style->applyFromArray(self::$std_cell_fnt);
         $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                       ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                       ->setWrapText(true);

         $sheet = $xls->getActiveSheet();
         $sheet->getSheetView()->setZoomScale(80);

         $lastrow = $this->mdl_makeReportTitle_FPK($sheet);

         $count_iteration = 0;            
         $data_top = $data_bottom = 0;

         $total_arr = []; 

         $total_top = $lastrow + 1;
         $total_bottom = 0;

         $text = '';
         
         if(count($records) > 0){
            $org_rid = $rid_road = '';
            $current_org = [];

            $row_num = $grp_row_num = $grp_start_row  = 0;                 
            $arr_cells = [];
            $total_arr = [];
            $org_text = '';
            $option_orgs = '';
            
            foreach($records as $elem){
                // $elem['high'] не равно '' 
              //  $currentroad_ttl = $elem['ttl'];
                if($elem['high'] != $org_rid){
                    
                    if(mb_strlen($option_orgs) > 0)
                               $option_orgs .= ', ';
                        
                        $option_orgs .= $elem['habb'];
                    
                    if($org_rid != ''){  
                         
  /*5)*/                $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'ДОСС' : 'АО ФПК');
                                        
                        $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                        array_push($arr_cells, $lastrow);
          
                        $lastrow = $this->mdl_totalFooter_FPK($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $arr_cells, 'V', 'W', 'AB', $org_text);                        
                        $arr_cells = [];
                        array_push($total_arr, $lastrow);
                        $grp_row_num = $count_iteration = 0; 
                        
                    }                   
                     
            /*1)*/  $lastrow = $this->subTitle_by_nm_direction_4_mdl($sheet, $lastrow + 1, $elem['httl']);
                   
                    $org_rid = $elem['high'];
                    $current_org = $elem;

                    $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'AE');
                    $grp_start_row = $lastrow;
                    $rid_road = $elem['org'];
                    
                }
                else {
                    if($rid_road != $elem['org']){

                       if($rid_road != ''){
                           
     /*3)*/                  $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                            
                                       //reestr_makeGroupFooter_FPK($sheet, int $row, int $calc_fr, int $calc_to, int $item_cnt, string $text)
                             $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                             array_push($arr_cells, $lastrow);
                             
                    }  

  /*4)*/                   $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'AE');
                           $grp_start_row = $lastrow;
                           $rid_road = $elem['org'];
                           
                           $grp_row_num = 0; 
                     }
                }
               //reestr_makeItemRow_FPK($db, $sheet, int $from_row, int $row_num, array $record)                
     /*2)*/     $lastrow = $this->reestr_makeItemRow_FPK($db, $sheet, $lastrow + 1, $row_num, $elem);
                $row_num++;
                $grp_row_num++;
                $count_iteration++;        
            }
            
            if(count($current_org) > 0){
                
                        $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'АО ФПК');
  
                        $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                        array_push($arr_cells, $lastrow);
                                             
                        $lastrow = $this->mdl_totalFooter_FPK($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $arr_cells, 'V', 'W', 'AB', $org_text); 
                        array_push($total_arr, $lastrow);
                        
                        $this->mdl_total_footer('A', $sheet, $lastrow + 1, $data_top, $data_bottom, $total_arr, 'V', 'W', 'AB', 'Общий итог'); 
            }
            
         }
        $objWriter = new PHPExcel_Writer_Excel2007($xls);
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save($report_dir . '/' . $report_file);

        $result = $report_relpath . '/' . $report_file;

        unset($db);        
        return $result; 
   }
   
   public function cat_params_reestr_mdl ($k, $o, $s, $g, $dp, $dc, $nd){
       $db = new db_depo();
        $records = $db->cat_params_carriage_reestr($k, $o, $s, $g, $dp, $dc, $nd);
       
        $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
        $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
        $report_file = substr(md5('report_p'), 0, 8). '.xlsx';              

         $xls = new PHPExcel();
         $xls->setActiveSheetIndex(0);

         $def_style = $xls->getDefaultStyle();
         $def_style->applyFromArray(self::$std_cell_fnt);
         $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                       ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                       ->setWrapText(true);

         $sheet = $xls->getActiveSheet();
         $sheet->getSheetView()->setZoomScale(80);

         $lastrow = $this->mdl_makeReportTitle_FPK($sheet);

         $count_iteration = 0;            
         $data_top = $data_bottom = 0;

         $total_arr = []; 

         $total_top = $lastrow + 1;
         $total_bottom = 0;

         $text = '';
         
         if(count($records) > 0){
            $org_rid = $rid_road = '';
            $current_org = [];

            $row_num = $grp_row_num = $grp_start_row  = 0;                 
            $arr_cells = [];
            $total_arr = [];
            $org_text = '';
            $option_orgs = '';
            
            foreach($records as $elem){
                // $elem['high'] не равно '' 
                if($elem['high'] != $org_rid){
                    
                    if(mb_strlen($option_orgs) > 0)
                               $option_orgs .= ', ';
                        
                        $option_orgs .= $elem['habb'];
                    
                    if($org_rid != ''){  
                         
  /*5)*/                $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'ДОСС' : 'АО ФПК');
                                        
                        $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                        array_push($arr_cells, $lastrow);
          
                        $lastrow = $this->mdl_totalFooter_FPK($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $arr_cells, 'V', 'W', 'AB', $org_text);                        
                        $arr_cells = [];
                        array_push($total_arr, $lastrow);
                        $grp_row_num = $count_iteration = 0; 
                        
                    }                   
                     
            /*1)*/  $lastrow = $this->subTitle_by_nm_direction_4_mdl($sheet, $lastrow + 1, $elem['httl']);
                   
                    $org_rid = $elem['high'];
                    $current_org = $elem;

                    $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'AE');
                    $grp_start_row = $lastrow;
                    $rid_road = $elem['org'];
                    
                }
                else {
                    if($rid_road != $elem['org']){

                       if($rid_road != ''){
                           
     /*3)*/                  $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                            
                                       //reestr_makeGroupFooter_FPK($sheet, int $row, int $calc_fr, int $calc_to, int $item_cnt, string $text)
                             $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                             array_push($arr_cells, $lastrow);
                             
                    }  

  /*4)*/                   $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'AE');
                           $grp_start_row = $lastrow;
                           $rid_road = $elem['org'];
                           
                           $grp_row_num = 0; 
                     }
                }
               //reestr_makeItemRow_FPK($db, $sheet, int $from_row, int $row_num, array $record)                
     /*2)*/     $lastrow = $this->reestr_makeItemRow_FPK($db, $sheet, $lastrow + 1, $row_num, $elem);
                $row_num++;
                $grp_row_num++;
                $count_iteration++;        
            }
            
            if(count($current_org) > 0){
                
                        $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'АО ФПК');
  
                        $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                        array_push($arr_cells, $lastrow);
                                             
                        $lastrow = $this->mdl_totalFooter_FPK($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $arr_cells, 'V', 'W', 'AB', $org_text); 
                        array_push($total_arr, $lastrow);
                        
                        $this->mdl_total_footer('A', $sheet, $lastrow + 1, $data_top, $data_bottom, $total_arr, 'V', 'W', 'AB', 'Общий итог'); 
            }
            
         }
         
         
    /*    if(count($records) > 0){
            $org_rid = $rid_road = '';
            $current_org = [];

            $row_num = $grp_row_num = $grp_start_row  = 0;                 
            $arr_cells = [];
            $total_arr = [];
            $org_text = '';
            $option_orgs = '';
            
            foreach($records as $elem){
                // $elem['high'] не равно '' 
                if($elem['high'] != $org_rid){
                    
                    if(mb_strlen($option_orgs) > 0)
                               $option_orgs .= ', ';
                        
                        $option_orgs .= $elem['habb'];
                    
                    if($org_rid != ''){  

                        $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'ДОСС' : 'АО ФПК');
                                        
                        $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                        array_push($arr_cells, $lastrow);
          
                        $lastrow = $this->mdl_totalFooter_FPK($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $arr_cells, 'V', 'W', 'AB', $org_text);                        
                        $arr_cells = [];
                        array_push($total_arr, $lastrow);
                        
                        $row_num = $grp_row_num = 0;
                    }                   
                     
                    $lastrow = $this->subTitle_by_nm_direction_4_mdl($sheet, $lastrow + 1, $elem['httl']);
                   
                    $org_rid = $elem['high'];
                    $current_org = $elem;

                    $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'AE');
                    $grp_start_row = $lastrow;
                    $rid_road = $elem['org'];
                    
                }
                else {
                    if($rid_road != $elem['org']){

                       if($rid_road != ''){
                           
                             $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');

                             $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                             array_push($arr_cells, $lastrow);
                             
                    }  

                           $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'AE');
                           $grp_start_row = $lastrow;
                           $rid_road = $elem['org'];
                           
                           $grp_row_num = 0;
                     }
                }
                
                $lastrow = $this->reestr_makeItemRow_FPK($db, $sheet, $lastrow + 1, $row_num, $elem);
                $row_num++;
                $grp_row_num++;
                $count_iteration++;           
            }
            
            if(count($current_org) > 0){
                
                        $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'АО ФПК');
  
                        $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                        array_push($arr_cells, $lastrow);
                                             
                        $lastrow = $this->mdl_totalFooter_FPK($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $arr_cells, 'V', 'W', 'AB', $org_text); 
                        array_push($total_arr, $lastrow);
                        
                        $this->mdl_total_footer('A', $sheet, $lastrow + 1, $data_top, $data_bottom, $total_arr, 'V', 'W', 'AB', 'Общий итог'); 
            }
            
         } */
         
        $objWriter = new PHPExcel_Writer_Excel2007($xls);
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save($report_dir . '/' . $report_file);

        $result = $report_relpath . '/' . $report_file;

        unset($db);        
        return $result; 
   }
   
   
   public function multi_params_carriage_reestr($k, $o, $s, $g, $dp, $dc, $nd, $ini_str){
        $db = new db_depo();
        $records = $db->multi_params_carriage_reestr($k, $o, $s, $g, $dp, $dc, $nd, $ini_str);
       
         // отчет будет сохранять в проекте dmgnPs в папке '/tmp/rep' (конечная rep)
        $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
        $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
        $report_file = substr(md5('mul'), 0, 8). '.xlsx';              

         $xls = new PHPExcel();
         $xls->setActiveSheetIndex(0);

         $def_style = $xls->getDefaultStyle();
         $def_style->applyFromArray(self::$std_cell_fnt);
         $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                       ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                       ->setWrapText(true);

         $sheet = $xls->getActiveSheet();
         $sheet->getSheetView()->setZoomScale(80);

         $lastrow = $this->mdl_makeReportTitle_FPK($sheet);

         $count_iteration = 0;            
         $data_top = $data_bottom = 0;

         $total_arr = []; 

         $total_top = $lastrow + 1;
         $total_bottom = 0;

         $text = '';
         
        if(count($records) > 0){
            $org_rid = $rid_road = '';
            $current_org = [];

            $row_num = $grp_row_num = $grp_start_row  = 0;                 
            $arr_cells = [];
            $total_arr = [];
            $org_text = '';
            $option_orgs = '';
            /*
                'rid' => $sel_rid,
                         'obj_nm' => $sel_obj_nm,
                         'mdl' => $sel_mdl,
                         'date_num_registr'  => $sel_date_num_registr,
                         'docs_at_constr' => $sel_docs_at_constr,
                         'docs_at_modern' => $sel_docs_at_modern,

                         'flg' => $sel_flg,
                         'exp_res'  => $sel_exp_res,
                         'recoms_using'  => $sel_recoms_using,
                         'date_act' => $sel_date_act, 
                         'note'  => $sel_note,
                         'carr_mdl' => $sel_carr_mdl,
                         'carr_pasport' => $sel_carr_pasport,  
                         'org' => $sel_org,   
                         'high' => $sel_high,
                         'habb' => $sel_habb,
                         'httl' => $sel_httl,
                         'abb' =>  $sel_abb,
                         'ttl' => $sel_ttl   
            
            
                         */
            
            foreach($records as $elem){
                // $elem['high'] не равно '' 
                if($elem['high'] != $org_rid){
                    
                    if(mb_strlen($option_orgs) > 0)
                               $option_orgs .= ', ';
                        
                        $option_orgs .= $elem['habb'];
                    
                    if($org_rid != ''){  

                        $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'ДОСС' : 'АО ФПК');
                                        
                        $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                        array_push($arr_cells, $lastrow);
          
                        $lastrow = $this->mdl_totalFooter_FPK($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $arr_cells, 'V', 'W', 'AB', $org_text);                        
                        $arr_cells = [];
                        array_push($total_arr, $lastrow);
                        $grp_row_num = $count_iteration = 0; 
                    }                   
                     
                    $lastrow = $this->subTitle_by_nm_direction_4_mdl($sheet, $lastrow + 1, $elem['httl']);
                   
                    $org_rid = $elem['high'];
                    $current_org = $elem;

                    $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'AE');
                    $grp_start_row = $lastrow;
                    $rid_road = $elem['org'];
                    
                }
                else {
                    if($rid_road != $elem['org']){

                       if($rid_road != ''){
                           
                             $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');

                             $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                             array_push($arr_cells, $lastrow);
                             
                    }  

                           $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'AE');
                           $grp_start_row = $lastrow;
                           $rid_road = $elem['org'];
                           $grp_row_num = 0;
                     }
                }
                
                $lastrow = $this->reestr_makeItemRow_FPK($db, $sheet, $lastrow + 1, $row_num, $elem);
                $row_num++;
                $grp_row_num++;
                $count_iteration++;           
            }
            
            if(count($current_org) > 0){
                
                        $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'АО ФПК');
  
                        $lastrow = $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, $text);
                        array_push($arr_cells, $lastrow);
                                             
                        $lastrow = $this->mdl_totalFooter_FPK($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $arr_cells, 'V', 'W', 'AB', $org_text); 
                        array_push($total_arr, $lastrow);
                        
                        $this->mdl_total_footer('A', $sheet, $lastrow + 1, $data_top, $data_bottom, $total_arr, 'V', 'W', 'AB', 'Общий итог'); 
            }
            
         }
         
        $objWriter = new PHPExcel_Writer_Excel2007($xls);
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save($report_dir . '/' . $report_file);

        $result = $report_relpath . '/' . $report_file;

       unset($db);
       return $result;
   }
   /*************************************** train part ************************************************/
   
   public function train_makeReportTitle($sheet){
       $sheet->getStyle('A14')->applyFromArray(self::$titleFont);       
        $sheet->mergeCells("A14:W14");
        $sheet->setCellValue("A14", 'РЕЕСТР ДОСТУПНОСТИ ДЛЯ ПАССАЖИРОВ ИЗ ЧИСЛА ИНВАЛИДОВ ПАССАЖИРСКИХ ПОЕЗДОВ');

        // col whidts
        $this->setCellW($sheet, 'A',  3.71);
        $this->setCellW($sheet, 'B',  14.57);
        $this->setCellW($sheet, 'C',  25.86);
        $this->setCellW($sheet, 'D',  28.43);
        $this->setCellW($sheet, 'E',  13.57);
        $this->setCellW($sheet, 'F',  4.14);
        $this->setCellW($sheet, 'G',  4.29);
        $this->setCellW($sheet, 'H',  4.14);
        $this->setCellW($sheet, 'I',  19.57);
        $this->setCellW($sheet, 'J',  35.00);
        $this->setCellW($sheet, 'K',  21.43);
        $this->setCellW($sheet, 'L',  72.71);
        $this->setCellW($sheet, 'M',  11.86);
        $this->setCellW($sheet, 'N',  2.57);
        $this->setCellW($sheet, 'O',  2.43);
        $this->setCellW($sheet, 'P',  22.57);
        $this->setCellW($sheet, 'Q',  16);
        $this->setCellW($sheet, 'R',  30.86);
        $this->setCellW($sheet, 'S',  12.14);
        $this->setCellW($sheet, 'T',  14.29);
        $this->setCellW($sheet, 'U',  4.57);
        $this->setCellW($sheet, 'V',  8.43);
        $this->setCellW($sheet, 'W',  6.14);
                
        for($i = 1; $i < 16 ; $i++){
            $sheet->getRowDimension(strval($i))->setRowHeight(16);
        }
        
        $sheet->getRowDimension("16")->setRowHeight(15.75); 
        $sheet->getRowDimension("17")->setRowHeight(14.25); // 18    
        $sheet->getRowDimension("18")->setRowHeight(15); // 19
        $sheet->getRowDimension("19")->setRowHeight(17.25);  // 20
        $sheet->getRowDimension("20")->setRowHeight(19.50);
        $sheet->getRowDimension("21")->setRowHeight(17.25);
        $sheet->getRowDimension("22")->setRowHeight(18.00);
        $sheet->getRowDimension("23")->setRowHeight(19.50);
        $sheet->getRowDimension("24")->setRowHeight(17.25);
        $sheet->getRowDimension("25")->setRowHeight(132.75);
        $sheet->getRowDimension("26")->setRowHeight(15.00); 

        // TITLE
        
    
        foreach ($sheet->getColumnIterator('A',  'W') as $col) {
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderTB);
            $sheet->getStyle($col->getColumnIndex() . '25')->applyFromArray(self::$borderBB); 
            $sheet->getStyle($col->getColumnIndex() . '26')->applyFromArray(self::$borderBB); 
        }
        
        foreach($sheet->getColumnIterator('B',  'K') as $col){
             $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderB);
        }
        
        foreach($sheet->getColumnIterator('L',  'P') as $col){ // 'W'
             $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderBB);
        }
        
        foreach($sheet->getColumnIterator('A') as $col){
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '19')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '20')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '21')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '22')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '23')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '24')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '25')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '26')->applyFromArray(self::$borderRB);
        }
        
         foreach($sheet->getColumnIterator('H') as $col){
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '19')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '20')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '21')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '22')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '23')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '24')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '25')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '26')->applyFromArray(self::$borderRB);
        }
        
        foreach($sheet->getColumnIterator('K') as $col){
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '19')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '20')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '21')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '22')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '23')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '24')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '25')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '26')->applyFromArray(self::$borderRB);
        }
        
        foreach($sheet->getColumnIterator('L') as $col){
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '19')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '20')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '21')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '22')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '23')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '24')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '25')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '26')->applyFromArray(self::$borderR);
        }
        
                
        foreach($sheet->getColumnIterator('M', 'W') as $col){
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '19')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '20')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '21')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '22')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '23')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '24')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '25')->applyFromArray(self::$borderRB);
            $sheet->getStyle($col->getColumnIndex() . '26')->applyFromArray(self::$borderRB);
        }
        
        foreach($sheet->getColumnIterator('B', 'D') as $col){
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '19')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '20')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '21')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '22')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '23')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '24')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '25')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '26')->applyFromArray(self::$borderR);
        }
        
                
        foreach($sheet->getColumnIterator('I', 'J') as $col){
            $sheet->getStyle($col->getColumnIndex() . '16')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '17')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '18')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '19')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '20')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '21')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '22')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '23')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '24')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '25')->applyFromArray(self::$borderR);
            $sheet->getStyle($col->getColumnIndex() . '26')->applyFromArray(self::$borderR);
        }
        
        foreach($sheet->getColumnIterator('F', 'G') as $col){
             $sheet->getStyle($col->getColumnIndex() . '25')->applyFromArray(self::$borderR);
        }
        
        foreach($sheet->getColumnIterator('F', 'H') as $col){
             $sheet->getStyle($col->getColumnIndex() . '24')->applyFromArray(self::$borderB);
        }
        
        $sheet->getStyle('R24')->applyFromArray(self::$borderB);     

        $sheet->mergeCells("A16:A25"); $this->rdp_stdTtlCell($sheet, 'A16', '№ п/п');
        $sheet->mergeCells("B16:E16"); $this->rdp_stdTtlCell($sheet, 'B16', 'Общие сведения об объекте');
        $sheet->mergeCells("F16:K16"); $this->rdp_stdTtlCell($sheet, 'F16', '1.Оценка доступности пассажирского поезда*');
        $sheet->mergeCells("L16:P16"); $this->rdp_stdTtlCell($sheet, 'L16', '2.Требуемые мероприятия по адаптациии и рекомендованный период их проведения');
        
        $sheet->mergeCells("B17:B25"); $this->rdp_stdTtlCell($sheet, 'B17', "Номер поезда");
        $sheet->mergeCells("C17:C25"); $this->rdp_stdTtlCell($sheet, 'C17', "Маршрут поезда");
        $sheet->mergeCells("D17:D25"); $this->rdp_stdTtlCell($sheet, 'D17', "Адрес предприятия формирования поезда");
        $sheet->mergeCells("E17:E25"); $this->rdp_stdTtlCell($sheet, 'E17', "Дата и номер регистрации паспорта"); 
        
        
        $sheet->mergeCells("F17:H24"); $this->rdp_stdTtlCell($sheet, 'F17', "Итоговая оценка доступности пассажирского поезда");
        $this->rdp_stdTtlCell($sheet, 'F25', "ДП");
        $this->rdp_stdTtlCell($sheet, 'G25', "ДЧ");
        $this->rdp_stdTtlCell($sheet, 'H25', "НД");
 
       $sheet->mergeCells("I17:I25"); $this->rdp_stdTtlCell($sheet, 'I17', "a)наличие в составе поезда не менее одного вагона для перевозки инвалидов, полностью соответсвующего требованиеям".
                                                                             "доступности для них, от общего количества пассажирских поездов дальнего следования, предусмотренных расписанием");
        $sheet->mergeCells("J17:J25"); $this->rdp_stdTtlCell($sheet, 'J17', "б) количество работников перевозчика, профессии которых связанны с обслуживанием пассажиров из числа инвалидов и прошедших инструктирование ". 
                                                                                "или обучение для  работы с указанной категорией пассажиров, по вопросам, связанным с обеспечением доступности для них объектов и услуг в сфере ". 
                                                                                "пассажирских  перевозок   железнодорожным транспортом в соответствии с законодательством Российской Федерации и законодательством субъектов ".
                                                                                "Российской Федерации, от общего количества таких \n%");

        $sheet->mergeCells("K17:K25"); $this->rdp_stdTtlCell($sheet, 'K17', "в) удельный вес услуг, предоставляемых пассажирам из числа ".
                                                                                "инвалидов  с  сопровождением  персонала   перевозчика, ".
                                                                                "от общего количества предоставляемых услуг (подъемники, купе, туалеты), измеряемый в процентах %");   
        
        $sheet->mergeCells("L17:L25"); $this->rdp_stdTtlCell($sheet, 'L17', "Виды работ по адаптации");   
        $sheet->mergeCells("M17:M25"); $this->rdp_stdTtlCell($sheet, 'M17', "Плановый период (срок) исполнения");   
        
            
         // Vertical cell borders
        for ($i = 17; $i <= 25; ++$i) {
            $sheet->getStyle('N' . strval($i))->applyFromArray(self::$borderLB)
                                              ->applyFromArray(self::$borderRB);
            
             $sheet->getStyle('O' . strval($i))->applyFromArray(self::$borderRB);
        }
              
        $sheet->mergeCells("N17:N25"); $this->rdp_stdTtlCell($sheet, 'N17', "Отметка о выполнении работ по адаптации", true);   
        $sheet->mergeCells("O17:O25"); $this->rdp_stdTtlCell($sheet, 'O17', "Отметка о невыполнении работ по адаптации", true); 
        
        
        
        $sheet->mergeCells("P17:P25"); $this->rdp_stdTtlCell($sheet, 'P17', "Причины не выпонения");
        
        $sheet->mergeCells("Q16:Q25"); $this->rdp_stdTtlCell($sheet, 'Q16', "Ожидаемый результат состояния доступности после выполнения работ по адаптации");
        
        $sheet->mergeCells("R16:R24"); $this->rdp_stdTtlCell($sheet, 'R16', "Рекомендацмм по использованимю объекта транспортной инфраструктуры для обслуживания инвалидов \n Ожидаемый результат по (состоянию доступности)");
        $this->rdp_stdTtlCell($sheet, 'R25', "Ожидаемый результат по (состоянию доступности)");
        
        $sheet->mergeCells("S16:S25"); $this->rdp_stdTtlCell($sheet, 'S16', "Дата актуализации информации");
        
        $sheet->mergeCells("T16:U25"); $this->rdp_stdTtlCell($sheet, 'T16', "Отметка об участии общественных объединений инвалидов в проведении в обследовании и в паспортизации");
        
        $sheet->mergeCells("V16:W25"); $this->rdp_stdTtlCell($sheet, 'V16', "Примечание");
        
        $i=1;
        foreach($sheet->getColumnIterator('A', 'M') as $col){ 
            $this->rdp_stdTtlCell($sheet, $col->getColumnIndex() . '26', $i);
            $i++;
        }
      //  $i = 13;
        
        foreach($sheet->getColumnIterator('Q', 'S') as $col){ 
            $this->rdp_stdTtlCell($sheet, $col->getColumnIndex() . '26', $i);
            $i++;
        }
        
        $sheet->mergeCells("T26:U26"); $this->rdp_stdTtlCell($sheet, 'T26', $i++);
        $sheet->mergeCells("V26:W26"); $this->rdp_stdTtlCell($sheet, 'V26', $i);
        
        return 26;  
   }
   
   
   public function subTitle_by_nm_direction($sheet, $lastrow ,$nm){
       
        foreach($sheet->getColumnIterator('A', 'W') as $col){ 
             $sheet->getStyle($col->getColumnIndex() . $lastrow)->applyFromArray(self::$borderBB); 
        }
        
        $sheet->getStyle('A'. $lastrow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('W'. $lastrow)->applyFromArray(self::$borderRB);
        
        $sheet->mergeCells("B".$lastrow.":W".$lastrow); $this->rdp_stdTtlCell($sheet, "B".$lastrow, $nm);
        
        return $lastrow;
   }
   
   // reestr_makeItemRow_FPK($db, $sheet, int $from_row, int $row_num, array $record) 
   public function train_makeItemRow($db, $sheet, $from_row, $row_num, $record){
        $result = $from_row;
        
        if (count($record) > 0) {
            //$db = new _dbpoints();
            $action = $db->get_train_actionsList($record['rid']);
            //unset($db);
            
            $action_cnt = count($action);
            
            if ($action_cnt > 0) {
                if($action_cnt > 1){
                for ($i = $from_row; $i < $from_row + $action_cnt; ++$i)  // Set rows heights to auto
                    $sheet->getRowDimension($i)->setRowHeight(-1); // setRowHeight(-1); со значением -1 считает высоту по умолчанию
                } else $sheet->getRowDimension($from_row)->setRowHeight(45);
                
                $clr = 'FFFFCC';  // temporarily moved above for
                
                for ($i = 0; $i < $action_cnt; ++$i) {
                    $srow = strval($from_row + $i);
  
                    $done = ($action[$i]['flg'] >> 24) & 0x3;

                    $pp = $action[$i]['flg'] & 0xFFFFFF; // $pp - plaining period
                    $year = $action[$i]['flg'] & 0xFFF;

                    $str = '';
            
                    if($year > 0){             
                        $quarter = ($action[$i]['flg'] >> 12) & 0xF;
                        if($quarter> 0){
                               $str = $quarter .' кв. '. $year;                       
                        }
                    }else{
                         $select = ($action[$i]['flg']  >> 16) & 0xFF;
                         $str = assist::$plaining_period[$select];
                    }

                    if(mb_strlen($str) > 0){
                        $this->rdp_stdDataCell($sheet, 'M' . $srow, $str);
                    } 
                
                    $this->rdp_stdDataCell($sheet, 'L' . $srow, $action[$i]['twa']);
                                                
                    $this->rdp_stdDataCell($sheet, 'N' . $srow, ($done == 1 ? 1 : ""), $clr);
                    $this->rdp_stdDataCell($sheet, 'O' . $srow, ($done == 2 ? 1 : ""), $clr);
                    $this->rdp_stdDataCell($sheet, 'P' . $srow, $action[$i]['pnv']);
                    
                    $sheet->getStyle('AW' . $srow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    
                    if ($i < ($action_cnt - 1)) {                       
                        $sheet->getStyle('I' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('J' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('K' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('L' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('M' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('N' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('O' . $srow)->applyFromArray(self::$borderB);
                        $sheet->getStyle('P' . $srow)->applyFromArray(self::$borderB);
                    }                   
                }
                
                $result += $action_cnt - 1;
            }
            else $sheet->getRowDimension($from_row)->setRowHeight(45);

            $sresult = strval($result);
            
            foreach ($sheet->getColumnIterator('A', 'W') as $col) $sheet->getStyle($col->getColumnIndex() . $sresult)->applyFromArray(self::$borderBB);

            for ($i = $from_row; $i <= $result; ++$i) {
                $srow = strval($i);
                $sheet->getStyle('A' . $srow)->applyFromArray(self::$borderLB)
                                             ->applyFromArray(self::$borderRB);
                
                $sheet->getStyle('E' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('H' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('K' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('M' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('N' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('O' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('P' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('Q' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('R' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('S' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('U' . $srow)->applyFromArray(self::$borderRB);
                $sheet->getStyle('W' . $srow)->applyFromArray(self::$borderRB);
                
                foreach ($sheet->getColumnIterator('B', 'D') as $col)
                        $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$borderR);
                        
                foreach ($sheet->getColumnIterator('F', 'J') as $col)
                        $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$borderR);

                foreach ($sheet->getColumnIterator('I', 'J') as $col)
                        $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$borderR);
                
                $sheet->getStyle('L' . $srow)->applyFromArray(self::$borderR);
                
            }

       $stop = strval($from_row);
       
      //  $flg = ($record['flg'] & 0x3FF);
           // $m_ooi = ($record['flg']  >> 8) & 0x3;
       
           // $flg = ($record['flg'] & 0x3FFFFFF);
            $m_ooi = ($record['flg'] >> 4) & 0x3;
            $presence_spc_carr = ($record['flg'] & 0x3);
            $quantity_workers_prc = ($record['flg']  >> 8) & 0x3FF;
            $weight_service_prc = ($record['flg']  >> 16) & 0x3FF ;

         //   $m_ooi = ($flg >> 8) & 0x3;
            
          if ($result >= $from_row) {
                $sbot = strval($result);
                
                $sheet->mergeCells("A" . $stop . ":A" . $sbot);
                $sheet->mergeCells("B" . $stop . ":B" . $sbot);
                $sheet->mergeCells("C" . $stop . ":C" . $sbot);
                $sheet->mergeCells("D" . $stop . ":D" . $sbot);
                $sheet->mergeCells("E" . $stop . ":E" . $sbot);
                $sheet->mergeCells("F" . $stop . ":F" . $sbot);
                $sheet->mergeCells("G" . $stop . ":G" . $sbot);
                $sheet->mergeCells("H" . $stop . ":H" . $sbot);
                $sheet->mergeCells("I" . $stop . ":I" . $sbot);
                $sheet->mergeCells("J" . $stop . ":J" . $sbot);
                $sheet->mergeCells("K" . $stop . ":K" . $sbot);
                $sheet->mergeCells("Q" . $stop . ":Q" . $sbot);
                $sheet->mergeCells("R" . $stop . ":R" . $sbot); // $this->rdp_stdDataCell($sheet, 'AB' . $stop, ($m_ooi == 1) ? '1' : ' '); 
                $sheet->mergeCells("S" . $stop . ":S" . $sbot);
                $sheet->mergeCells("T" . $stop . ":U" . $sbot);
                $sheet->mergeCells("V" . $stop . ":W" . $sbot);
            }

            $this->rdp_stdDataCell($sheet, 'A' . $stop, $row_num + 1);  // $row_num + 1: №пп
          
            $this->rdp_stdDataCell($sheet, 'B' . $stop, $record['num_train']);
            $this->rdp_stdDataCell($sheet, 'C' . $stop, $record['route']);
            $this->rdp_stdDataCell($sheet, 'I' . $stop, ($presence_spc_carr == 1) ? '1' : ' ');
            $this->rdp_stdDataCell($sheet, 'J' . $stop, $quantity_workers_prc);
            $this->rdp_stdDataCell($sheet, 'K' . $stop, $weight_service_prc);

            $this->rdp_stdDataCell($sheet, 'D' . $stop, $record['adr_formation']);
            $this->rdp_stdDataCell($sheet, 'E' . $stop, $record['date_num_registr']);

            $this->rdp_stdDataCell($sheet, 'Q' . $stop, $record['exp_res']); 
            $this->rdp_stdDataCell($sheet, 'R' . $stop, $record['recoms_using']);
            $this->rdp_stdDataCell($sheet, 'S' . $stop, _dbbasemy::dates_YYYYMMDD2RuD($record['date_act']));
            $this->rdp_stdDataCell($sheet, 'T' . $stop, ($m_ooi == 1) ? '1' : ' '); 
            $this->rdp_stdDataCell($sheet, 'V' . $stop, $record['note'] ); 
 
            
          $col_1 = ''; 
                       
          $total_estimation = ($record['flg']  >> 2) & 0x3;
            
            if($total_estimation == 1){
                $col_1 = 'F';
            }else if($total_estimation == 2){
                $col_1 = 'G';
            }else if($total_estimation == 3){
                $col_1 = 'H';
            }           
            
           if(strlen($col_1) != 0) $this->rdp_stdDataCell($sheet, $col_1 . $stop, '1');
           

        }       
        return $result;       
   }
   
    private function train_makeGroupFooter($sheet, int $row, int $calc_fr, int $calc_to, int $item_cnt, string $nm, $text) {
        
        $this->train_makeFooterRow($sheet, $row);
        
        $srow = strval($row);

        if (mb_strlen($nm) > 0) $nm = $text; //'Итого по дирекции'
        
        $this->reestr_grpFooterCell($sheet, 'A' . $srow, $item_cnt);
        
        $sheet->mergeCells("B".$srow.":C".$srow); 
        $this->reestr_grpFooterCell($sheet, 'B' . $srow, $nm, "CENTER");  
        
        foreach ($sheet->getColumnIterator('D', 'E') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");
        
        foreach ($sheet->getColumnIterator('F', 'I') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, $this->f_sum($col->getColumnIndex(), $calc_fr, $calc_to));
        
        foreach ($sheet->getColumnIterator('J', 'K') as $col) //СУММ(K39:K41)/A42
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, $this->f_divisor($col->getColumnIndex(), $calc_fr, $calc_to, $item_cnt)); //$this->f_sum($col->getColumnIndex(), $calc_fr, $calc_to)
        
        foreach ($sheet->getColumnIterator('L', 'S') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");
        
        
        $sheet->mergeCells("T".$srow.":U".$srow); 
            $this->reestr_grpFooterCell($sheet, 'T' . $srow, $this->f_sum('T', $calc_fr, $calc_to));

       $sheet->mergeCells("V".$srow.":W".$srow); 
            $this->reestr_grpFooterCell($sheet, 'V' . $srow, "");      
            
        return $row;    
    }
    
   public function train_makeFooterRow($sheet, $row, $total = false){
        foreach ($sheet->getColumnIterator('A', 'W') as $col) $sheet->getStyle($col->getColumnIndex() . strval($row))->applyFromArray(self::$borderBB);
        
        $srow = strval($row);

        (!$total) ? $sheet->getStyle('A' . $srow)->applyFromArray(self::$borderRLight) : $sheet->getStyle('A' . $srow)->applyFromArray(self::$borderR);
        
        $sheet->getStyle('E' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('H' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('K' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('M' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('N' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('O' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('P' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('Q' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('R' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('S' . $srow)->applyFromArray(self::$borderRB);         
        $sheet->getStyle('U' . $srow)->applyFromArray(self::$borderRB);
        $sheet->getStyle('W' . $srow)->applyFromArray(self::$borderRB);
        
      /*  foreach ($sheet->getColumnIterator('U', 'AA') as $col)        
                $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$borderRB);
         $sheet->getStyle('AC' . $srow)->applyFromArray(self::$borderRB);
         $sheet->getStyle('AE' . $srow)->applyFromArray(self::$borderRB);
    */
        
        $sheet->getStyle('C' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('D' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('F' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('G' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('I' . $srow)->applyFromArray(self::$borderR);
        $sheet->getStyle('J' . $srow)->applyFromArray(self::$borderR);

   }
  
                                  //$count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $arr_cells, $_F, $_G, $_H, $_I, $_J, $_K, $_T
    public function train_totalFooter($count_iteration, $sheet, $lastrow, $calc_fr, $calc_to, $text, $arr, $_F, $_G, $_H, $_I, $_J, $_K, $_T){ //$column_arr $V, $W, $AB
        $this->train_makeFooterRow($sheet, $lastrow, true);
        
        $srow = strval($lastrow);

        $this->reestr_grpFooterCell($sheet, 'A' . $srow, $count_iteration, 'RIGHT', '000000');
        
        $sheet->mergeCells("B" . $srow . ":C" . $srow);       
        $this->reestr_grpFooterCell($sheet, 'B' . $srow, $text, "CENTER", '000000');  //'ВСЕГО по дирекции'
        
        $sheet->getStyle('F' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        $sheet->getStyle('G' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        $sheet->getStyle('H' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        
        $sheet->getStyle('I' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        $sheet->getStyle('J' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        $sheet->getStyle('K' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        
        $sheet->getStyle('T' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        
         foreach ($sheet->getColumnIterator('D', 'E') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");

         $str_F = $this->num_row_4_excel_sum($_F, $arr);                           
         $this->reestr_grpFooterCell($sheet, $_F.$lastrow, '=SUM('.$str_F . ')', 'RIGHT', '000000');         
          
         $str_G = $this->num_row_4_excel_sum($_G, $arr);                    
         $this->reestr_grpFooterCell($sheet, $_G.$lastrow, '=SUM('.$str_G . ')', 'RIGHT', '000000'); 
         
         $str_H = $this->num_row_4_excel_sum($_H, $arr);                    
         $this->reestr_grpFooterCell($sheet, $_H.$lastrow, '=SUM('.$str_H . ')', 'RIGHT', '000000'); 
         
         $str_I = $this->num_row_4_excel_sum($_I, $arr);                    
         $this->reestr_grpFooterCell($sheet, $_I.$lastrow, '=SUM('.$str_I . ')', 'RIGHT', '000000'); 
         
         $str_J = $this->num_row_4_excel_sum($_J, $arr);                    
         $this->reestr_grpFooterCell($sheet, $_J.$lastrow, '=SUM('.$str_J . ')', 'RIGHT', '000000'); 
         
         $str_K = $this->num_row_4_excel_sum($_K, $arr);                    
         $this->reestr_grpFooterCell($sheet, $_K.$lastrow, '=SUM('.$str_K . ')', 'RIGHT', '000000');
         
         $sheet->mergeCells("T" . $srow . ":U" . $srow); 
         
         $str_T = $this->num_row_4_excel_sum($_T, $arr);                    
         $this->reestr_grpFooterCell($sheet, $_T.$lastrow, '=SUM('.$str_T . ')', 'RIGHT', '000000');
         
        foreach ($sheet->getColumnIterator('L', 'S') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");   
        
        $sheet->mergeCells("V" . $srow . ":W" . $srow);       
        $this->reestr_grpFooterCell($sheet, 'V' . $srow, '');        
        
        foreach($sheet->getColumnIterator('A', 'W') as $col){           
            $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$bgYellow);
        }
        
        return $lastrow;
    }
    
    public function train_total_result($_A, $sheet, $lastrow, $arr, $_F, $_G, $_H, $_I, $_J, $_K, $_T, $option_orgs = ''){
         $this->train_makeFooterRow($sheet, $lastrow, true);
        
        $srow = strval($lastrow);

     //   $this->reestr_grpFooterCell($sheet, 'A' . $srow, $count_iteration, 'RIGHT', '000000');
        
        $str_A = $this->num_row_4_excel_sum($_A, $arr);                           
        $this->reestr_grpFooterCell($sheet, $_A.$lastrow, '=SUM('.$str_A . ')', 'RIGHT', '000000');  
        
        $sheet->mergeCells("B" . $srow . ":C" . $srow);       
        $this->reestr_grpFooterCell($sheet, 'B' . $srow, $option_orgs, "CENTER", '000000');  //'ВСЕГО по дирекции'   
        
        $sheet->getStyle('F' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        $sheet->getStyle('G' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        $sheet->getStyle('H' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        
        $sheet->getStyle('I' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        $sheet->getStyle('J' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        $sheet->getStyle('K' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        
        $sheet->getStyle('T' . $srow)->applyFromArray(self::$ttl_cell_fnt);
        
         foreach ($sheet->getColumnIterator('D', 'E') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");

         $str_F = $this->num_row_4_excel_sum($_F, $arr);                           
         $this->reestr_grpFooterCell($sheet, $_F.$lastrow, '=SUM('.$str_F . ')', 'RIGHT', '000000');         
          
         $str_G = $this->num_row_4_excel_sum($_G, $arr);                    
         $this->reestr_grpFooterCell($sheet, $_G.$lastrow, '=SUM('.$str_G . ')', 'RIGHT', '000000'); 
         
         $str_H = $this->num_row_4_excel_sum($_H, $arr);                    
         $this->reestr_grpFooterCell($sheet, $_H.$lastrow, '=SUM('.$str_H . ')', 'RIGHT', '000000'); 
         
         $str_I = $this->num_row_4_excel_sum($_I, $arr);                    
         $this->reestr_grpFooterCell($sheet, $_I.$lastrow, '=SUM('.$str_I . ')', 'RIGHT', '000000'); 
         
         $str_J = $this->num_row_4_excel_sum($_J, $arr);     //   "=ROUND(SUM(" . $col . strval($from) . ":" . $col . strval($to) . ")/A". strval($to + 1).",2)"              
         $this->reestr_grpFooterCell($sheet, $_J.$lastrow, '=ROUND(SUM('.$str_J . ')/2 ,2)', 'RIGHT', '000000'); 
         
         $str_K = $this->num_row_4_excel_sum($_K, $arr);                    
         $this->reestr_grpFooterCell($sheet, $_K.$lastrow, '=ROUND(SUM('.$str_K . ')/2 ,2)', 'RIGHT', '000000');
         
         $sheet->mergeCells("T" . $srow . ":U" . $srow); 
         
         $str_T = $this->num_row_4_excel_sum($_T, $arr);                    
         $this->reestr_grpFooterCell($sheet, $_T.$lastrow, '=SUM('.$str_T . ')', 'RIGHT', '000000');
         
        foreach ($sheet->getColumnIterator('L', 'S') as $col)
            $this->reestr_grpFooterCell($sheet, $col->getColumnIndex() . $srow, "");   
        
        $sheet->mergeCells("V" . $srow . ":W" . $srow);       
        $this->reestr_grpFooterCell($sheet, 'V' . $srow, '');        
        
        foreach($sheet->getColumnIterator('A', 'W') as $col){           
            $sheet->getStyle($col->getColumnIndex() . $srow)->applyFromArray(self::$bgYellow);
        }
        
        return $lastrow;
    }
   
   
   public function one_train_reestr($rid_train, $ttl){
        $result = "";
        
        $db = new db_depo();
        $record = $db->train_getList_by_train_rid($rid_train);
        $org_records = $db->org_getRec($ttl); // $ttl - rid дороги
        $ttl_ = $org_records['ttl'];
        $habb = $org_records['habb'];
        
        // название подразделения надо передать
      //  $org_record = $db->org_getRec($rid_habb);
      //  $habb = $org_record['habb'];
        
        if (count($record) > 0) {
            // _assbase::siteRootDirX() uses path from $_SERVER['REQUEST_URI'], i.e. jgate.php. result - project dir, ex: '/dmgnI'
            $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
            $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
            
            if (_assbase::makeDir($report_dir)) {

                $report_file = preg_replace('#"#' , "", $org_records['abb']). '_' . preg_replace('#["\./]#' , "", $record['num_train']) . '.xlsx';
              //  $report_file = 'report_one_train.xlsx';
                $xls = new PHPExcel();
                $xls->setActiveSheetIndex(0);
                
                $def_style = $xls->getDefaultStyle();
                $def_style->applyFromArray(self::$std_cell_fnt);
                $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                              ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                              ->setWrapText(true);
                
                
                $sheet = $xls->getActiveSheet();
                $sheet->getSheetView()->setZoomScale(80); 

                $grp_num = 1;

                $lastrow = $this->train_makeReportTitle($sheet);
                $text = '';
                
                if(preg_match('#ДОСС#i' , $habb) == 1){
                     $lastrow = $this->subTitle_by_nm_direction($sheet, $lastrow + 1, 'ДИРЕКЦИЯ СКОРОСТНОГО СООБЩЕНИЯ ФИЛИАЛ ОАО "РЖД"');
                     $text = 'Итого по дирекции';
                }
                else if(preg_match('#ФПК#i' , $habb) ==  1){
                     $lastrow = $this->subTitle_by_nm_direction($sheet, $lastrow + 1, '"АО" ФЕДЕРАЛЬНАЯ ПАССАЖИРСКАЯ КОМПАНИЯ');
                      $text = 'Итого по филиалу';
                }

                $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $ttl_, 'W'); // сама увеличит $lastrow
                ++$grp_num;
                
                $data_top = ++$lastrow; // это $lastrow после желтого заголовка 
                $row_num = $group_item_cnt = 0; // $row_num - single excel row; $group_item_cnt - count of group elements (Data Rows)
                
                
                $lastrow = $this->train_makeItemRow($db, $sheet, $lastrow, $row_num, $record);
                $data_bottom = $lastrow;   // здесь $lastrow = последнему ряду rdp_makeItemRow
                ++$group_item_cnt;
          
                //   $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $data_top, $data_bottom, $group_item_cnt, $record['obj_nm']);
                $lastrow = $this->train_makeGroupFooter($sheet, $lastrow + 1, $data_top, $data_bottom, $group_item_cnt, $record['num_train'], $text);                             

                $objWriter = new PHPExcel_Writer_Excel2007($xls);
                $objWriter->setPreCalculateFormulas(true);
                $objWriter->save($report_dir . '/' . $report_file);
                
                $result = $report_relpath . '/' . $report_file;
            }
        }
        unset($db);
        
        return $result;
    }

   public function train_reestr_group($org_rid){ // $org_rid - rid 
       $db = new db_depo();
       $records = $db->train_getList_by_NUM_PREFIX($org_rid);      
       $road_nm = $db->org_getRec($org_rid);    
       $habb = $road_nm['habb'];
       $result= '';
       
       if(count($records) > 0){
           $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
           $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
           
           if (_assbase::makeDir($report_dir)) {
               
                //$report_file = preg_replace('#"#' , "", $road_nm['abb']). '.xlsx';
              $report_file = substr(md5('report'), 0, 8). '.xlsx';  
                
                $xls = new PHPExcel();
                $xls->setActiveSheetIndex(0);
                               
                $def_style = $xls->getDefaultStyle();
                $def_style->applyFromArray(self::$std_cell_fnt);
                $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                              ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                              ->setWrapText(true);
                
                $sheet = $xls->getActiveSheet();
                $sheet->getSheetView()->setZoomScale(80);
                
                $row_num = $grp_row_num  = 0;
                 $count_iteration = 0;

                $org_record = $db->org_getRec($org_rid);
                $org_record_nm = $org_record['ttl'];
                
                $lastrow = $this->train_makeReportTitle($sheet);
                $text = '';  
                $org_text = '';
                
                if(preg_match('#ДОСС#i' , $habb) == 1){
                     $lastrow = $this->subTitle_by_nm_direction($sheet, $lastrow + 1, 'ДИРЕКЦИЯ СКОРОСТНОГО СООБЩЕНИЯ ФИЛИАЛ ОАО "РЖД"');
                     $text = 'Итого по дирекции';
                     $org_text = 'ВСЕГО по дирекции';
                }
                else if(preg_match('#ФПК#i' , $habb) ==  1){
                     $lastrow = $this->subTitle_by_nm_direction($sheet, $lastrow + 1, '"АО" ФЕДЕРАЛЬНАЯ ПАССАЖИРСКАЯ КОМПАНИЯ');
                     $text = 'Итого по филиалу';
                     $org_text = 'Всего по АО"ФПК"';
                }
                
                    $arr_cells = [];                 

                    $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $org_record_nm, 'W');

                     $data_top = $lastrow + 1;

                    for($i = 0; $i < count($records); ++$i){
                            $row = $records[$i];

                            $passport_list = $db->train_getList_by_train_rid($row['rid']);
                            $lastrow = $this->train_makeItemRow($db, $sheet, $lastrow + 1, $row_num, $passport_list);
                            $row_num++;
                            $grp_row_num++;
                            $count_iteration++;
                            
                    }
                    
                    $data_bottom = $lastrow;  
                            // train_makeGroupFooter($sheet, $lastrow + 1, $data_top, $lastrow, $group_item_cnt, $record['num_train']); 
                   $lastrow = $this->train_makeGroupFooter($sheet, $lastrow + 1, $data_top, $lastrow, $grp_row_num, 'по ' . $org_record['abb'], $text);
                    array_push($arr_cells, $lastrow); // !!!!!!
                   
               //  $this->mdl_totalFooter_DSS($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $j_k_p, 'J', 'K', 'P');
                   $this->train_totalFooter($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $org_text, $arr_cells, 'F', 'G', 'H', 'I', 'J', 'K', 'T');               
                
                $objWriter = new PHPExcel_Writer_Excel2007($xls);
                $objWriter->setPreCalculateFormulas(true);
                $objWriter->save($report_dir . '/' . $report_file);
                
                $result = $report_relpath . '/' . $report_file;
             
           }
       }     
       unset($db);        
       return $result; 
   }
   
      public function train_reestr_Total(){ //$rid_org  
       $db = new db_depo();
       
       $main_org_list = $db->org_getList('');
       $_FPK = '';
       $_DSS = '';
       $result = '';
       
        foreach($main_org_list as $org){   

                if(preg_match('#ФПК#i' , $org['abb']) ==  1){
                     $_FPK = strval($org['rid']);
                }else if(preg_match('#ДОСС#i' , $org['abb']) ==  1){
                    $_DSS = strval($org['rid']);
                }
        }      
              
       
       $road_list_FPK = $db->org_getList($_FPK);
       $road_list_DSS = $db->org_getList($_DSS);

       if(count($road_list_DSS) > 0 || count($road_list_FPK) > 0){
           
           $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
           $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
           
           
            $report_file = md5($_DSS).'.xlsx';   //md5();

            $xls = new PHPExcel();
            $xls->setActiveSheetIndex(0);

            $def_style = $xls->getDefaultStyle();
            $def_style->applyFromArray(self::$std_cell_fnt);
            $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                          ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                          ->setWrapText(true);

            $sheet = $xls->getActiveSheet();
            $sheet->getSheetView()->setZoomScale(80);

            $lastrow = $this->train_makeReportTitle($sheet);
            
            $count_iteration = 0;            
            $data_top = $data_bottom = 0;
            
            $total_arr = []; 
            
            $total_top = $lastrow + 1;
            $total_bottom = 0;
            
          if(count($road_list_DSS) > 0 ){  
             if (_assbase::makeDir($report_dir)) {    
                 
                     $text = 'Итого по дирекции';
                     $org_text = 'ВСЕГО по дирекции';
                                 
                         $data_top = $lastrow + 1;
                         $arr_cells_DSS = [];

                          $lastrow = $this->subTitle_by_nm_direction($sheet, $lastrow + 1, 'ДИРЕКЦИЯ СКОРОСТНОГО СООБЩЕНИЯ ФИЛИАЛ ОАО "РЖД"');

                           foreach($road_list_DSS as $road_rec){                              
                               
                             $road_train_list = $db->train_getList_by_NUM_PREFIX($road_rec['rid']);

                              if(count($road_train_list) > 0){

                                     $row_num = $grp_row_num  = 0;                                    
                                     
                                     $org_record = $db->org_getRec($road_rec['rid']); 
                                     $org_record_nm = $org_record['ttl'];

                                     $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $org_record_nm, 'W');
                                      $grp_start_row = $lastrow;
                                      
                                       for($i = 0; $i < count($road_train_list); ++$i){

                                               $row = $road_train_list[$i];
                                                // взять один поезд
                                               $passport_list = $db->train_getList_by_train_rid($row['rid']);

                                               $lastrow = $this->train_makeItemRow($db, $sheet, $lastrow + 1, $row_num, $passport_list);
                                               $row_num++;
                                               $grp_row_num++;
                                               $count_iteration++;
                                       }                       
                                      
                                     $data_bottom = $lastrow;  

                                    $lastrow = $this->train_makeGroupFooter($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по ' . $org_record['abb'], $text);
                                     $grp_row_num = 0; 
                                     
                                 //     ++$lastrow;
                                       array_push($arr_cells_DSS, $lastrow);
                                 
                          }
                   }
                   
                  $lastrow = $this->train_totalFooter($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $org_text, $arr_cells_DSS, 'F', 'G', 'H', 'I', 'J', 'K', 'T'); 
                  
                  array_push($total_arr, $lastrow);       
                  $count_iteration = 0;
             }    
                
        }
        if(count($road_list_FPK) > 0 ){ 
            
            $text = 'Итого по филиалу';
            $org_text = 'Всего по АО"ФПК"';
            
            $arr_cells_FPK = [];
            
             $lastrow = $this->subTitle_by_nm_direction($sheet, $lastrow + 1, '"АО" ФЕДЕРАЛЬНАЯ ПАССАЖИРСКАЯ КОМПАНИЯ');
             
                    foreach($road_list_FPK as $road_rec){                              
                               
                             $road_train_list = $db->train_getList_by_NUM_PREFIX($road_rec['rid']);

                              if(count($road_train_list) > 0){

                                     $row_num = $grp_row_num  = 0;                                    
                                     
                                     $org_record = $db->org_getRec($road_rec['rid']); 
                                     $org_record_nm = $org_record['ttl'];

                                     $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $org_record_nm, 'W');
                                      $grp_start_row = $lastrow;
                                      
                                       for($i = 0; $i < count($road_train_list); ++$i){

                                               $row = $road_train_list[$i];
                                                // взять один поезд
                                               $passport_list = $db->train_getList_by_train_rid($row['rid']);

                                               $lastrow = $this->train_makeItemRow($db, $sheet, $lastrow + 1, $row_num, $passport_list);
                                               $row_num++;
                                               $grp_row_num++;
                                               $count_iteration++;
                                       }                       
                                      
                                     $data_bottom = $lastrow;  

                                    $lastrow = $this->train_makeGroupFooter($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по ' . $org_record['abb'], $text);
                                     $grp_row_num = 0; 
                                     
                                 //     ++$lastrow;
                                       array_push($arr_cells_FPK, $lastrow);
                                 
                          }
                   }
                   
                  $lastrow = $this->train_totalFooter($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $org_text, $arr_cells_FPK, 'F', 'G', 'H', 'I', 'J', 'K', 'T'); 
                  
                  array_push($total_arr, $lastrow);  
                                             //  $count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $arr_cells_FPK, 'F', 'G', 'H', 'I', 'J', 'K', 'T'
                  $this->train_total_result('A', $sheet, $lastrow + 1, $total_arr, 'F', 'G', 'H', 'I', 'J', 'K', 'T', 'ИТОГО по ДОСС, АО"ФПК"');
                  
             
        } 
        
        
        $objWriter = new PHPExcel_Writer_Excel2007($xls);
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save($report_dir . '/' . $report_file);

        $result = $report_relpath . '/' . $report_file;
     } 
       unset($db);        
       return $result; 
    }
/***********************************************categoryes ************************************************************************/
    
    public function category_train_reestr($ini_str, $dp, $dc, $nd){ //category_train_reestr($ini_str, $dp, $dc, $nd)
       $db = new db_depo();
       $records = $db->train_getList_by_categories($ini_str, $dp, $dc, $nd);     
 
      /* 
        $rwc_list = $db->req_getList_report($from, $to);         
                       
           $csv_file = $tmp_dir . '/report_list.xlsx';

            // удалить существующий файл rwc_list.csv, если существует
           if (file_exists($csv_file))
               unlink($csv_file);
       */
           $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
          //  $report_relpath = _assbase::siteRootDirX() . '/temptation/report';
           $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
          $report_file = substr(md5('report'), 0, 8). '.xlsx'; 
          
          if(file_exists($report_file)) unlink($report_file);
			 
           // $report_file = '1.xlsx';   // md5('report').

            $xls = new PHPExcel();
            $xls->setActiveSheetIndex(0);

            $def_style = $xls->getDefaultStyle();
            $def_style->applyFromArray(self::$std_cell_fnt);
            $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                          ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                          ->setWrapText(true);

            $sheet = $xls->getActiveSheet();
            $sheet->getSheetView()->setZoomScale(80);

            $lastrow = $this->train_makeReportTitle($sheet);
            
            $count_iteration = 0;            
            $data_top = $data_bottom = 0;
            
            $total_arr = []; 
            
            $total_top = $lastrow + 1;
            $total_bottom = 0;
            
            $text = '';
            

        if(count($records) > 0){
            $org_rid = $rid_road = '';
            $current_org = [];

             $row_num = $grp_row_num = $grp_start_row  = 0;                 
             $arr_cells = [];
             $total_arr = [];
             $org_text = '';
             $option_orgs = '';
             
            foreach($records as $elem){
                if($elem['high'] != $org_rid){
                    
                    if(mb_strlen($option_orgs) > 0)
                               $option_orgs .= ', ';
                        
                        $option_orgs .= $elem['habb'];
                    
                    if($org_rid != ''){  

                        $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'АО ФПК');
                      
                        
                        $lastrow = $this->train_makeGroupFooter($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по ' . $current_org['abb'], $text);
                        array_push($arr_cells, $lastrow);
                        
                        
                        
                        $lastrow = $this->train_totalFooter($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $org_text, $arr_cells, 'F', 'G', 'H', 'I', 'J', 'K', 'T'); 
                        $arr_cells = [];
                        array_push($total_arr, $lastrow);
                        
                        $grp_row_num = $count_iteration = 0; 
                    }                   
                     
                    $lastrow = $this->subTitle_by_nm_direction($sheet, $lastrow + 1, $elem['httl']);
                   
                    $org_rid = $elem['high'];
                    $current_org = $elem;
                   
               
                    $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'W');
                    $grp_start_row = $lastrow;
                    $rid_road = $elem['rid_road'];
                    
                }
                else {
                    if($rid_road != $elem['rid_road']){

                       if($rid_road != ''){
                           
                             $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');

                             $lastrow = $this->train_makeGroupFooter($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по ' . $elem['abb'], $text);
                             array_push($arr_cells, $lastrow);
                             
                    }  

                           $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'W');
                           $grp_start_row = $lastrow;
                           $rid_road = $elem['rid_road'];

                           $grp_row_num = 0;
                     }
                }
                
                $lastrow = $this->train_makeItemRow($db, $sheet, $lastrow + 1, $row_num, $elem);
                $row_num++;
                $grp_row_num++;
                $count_iteration++;           
                
            }
            
            if(count($current_org) > 0){
                
                        $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'АО ФПК');
                      
                        
                        $lastrow = $this->train_makeGroupFooter($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по ' . $current_org['abb'], $text);
                        array_push($arr_cells, $lastrow);
                        
                        
                        $lastrow = $this->train_totalFooter($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $org_text, $arr_cells, 'F', 'G', 'H', 'I', 'J', 'K', 'T'); 
                        array_push($total_arr, $lastrow);
                        
                        $this->train_total_result('A', $sheet, $lastrow + 1, $total_arr, 'F', 'G', 'H', 'I', 'J', 'K', 'T', 'ИТОГО по ' . $option_orgs); //$total_top, $total_bottom,
            }
            
 
        }
        
        
        $objWriter = new PHPExcel_Writer_Excel2007($xls);
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save($report_dir . '/' . $report_file);

        $result = $report_relpath . '/' . $report_file;

        unset($db);        
        return $result; 
    }
    
   public function cat_params_reestr_train($dp, $dc, $nd){
       $db = new db_depo();
       $records = $db->cat_params_train_reestr($dp, $dc, $nd);
       
       $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
        $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
        $report_file = substr(md5('catrep'), 0, 8). '.xlsx';    

         $xls = new PHPExcel();
         $xls->setActiveSheetIndex(0);

         $def_style = $xls->getDefaultStyle();
         $def_style->applyFromArray(self::$std_cell_fnt);
         $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                       ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                       ->setWrapText(true);

         $sheet = $xls->getActiveSheet();
         $sheet->getSheetView()->setZoomScale(80);

         $lastrow = $this->train_makeReportTitle($sheet);

         $count_iteration = 0;            
         $data_top = $data_bottom = 0;

         $total_arr = []; 

         $total_top = $lastrow + 1;
         $total_bottom = 0;

         $text = '';
         
         
         if(count($records) > 0){
            $org_rid = $rid_road = '';
            $current_org = [];

             $row_num = $grp_row_num = $grp_start_row  = 0;                 
             $arr_cells = [];
             $total_arr = [];
             $org_text = '';
             $option_orgs = '';
             
            foreach($records as $elem){
                if($elem['high'] != $org_rid){
                    
                    if(mb_strlen($option_orgs) > 0)
                               $option_orgs .= ', ';
                        
                        $option_orgs .= $elem['habb'];
                    
                    if($org_rid != ''){  

                        $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'АО ФПК');
                      
                        
                        $lastrow = $this->train_makeGroupFooter($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по ' . $current_org['abb'], $text);
                        array_push($arr_cells, $lastrow);
                        
                        
                        
                        $lastrow = $this->train_totalFooter($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $org_text, $arr_cells, 'F', 'G', 'H', 'I', 'J', 'K', 'T'); 
                        $arr_cells = [];
                        array_push($total_arr, $lastrow);
                        
                        $grp_row_num = $count_iteration = 0; 
                    }                   
                     
                    $lastrow = $this->subTitle_by_nm_direction($sheet, $lastrow + 1, $elem['httl']);
                   
                    $org_rid = $elem['high'];
                    $current_org = $elem;
                   
               
                    $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'W');
                    $grp_start_row = $lastrow;
                    $rid_road = $elem['rid_road'];
                    
                }
                else {
                    if($rid_road != $elem['rid_road']){

                       if($rid_road != ''){
                           
                             $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');

                             $lastrow = $this->train_makeGroupFooter($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по ' . $elem['abb'], $text);
                             array_push($arr_cells, $lastrow);
                             
                    }  

                           $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'W');
                           $grp_start_row = $lastrow;
                           $rid_road = $elem['rid_road'];

                           $grp_row_num = 0;
                     }
                }
                
                $lastrow = $this->train_makeItemRow($db, $sheet, $lastrow + 1, $row_num, $elem);
                $row_num++;
                $grp_row_num++;
                $count_iteration++;           
                
            }
            
            if(count($current_org) > 0){
                
                        $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'АО ФПК');
                      
                        
                        $lastrow = $this->train_makeGroupFooter($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по ' . $current_org['abb'], $text);
                        array_push($arr_cells, $lastrow);
                        
                        
                        $lastrow = $this->train_totalFooter($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $org_text, $arr_cells, 'F', 'G', 'H', 'I', 'J', 'K', 'T'); 
                        array_push($total_arr, $lastrow);
                        
                        $this->train_total_result('A', $sheet, $lastrow + 1, $total_arr, 'F', 'G', 'H', 'I', 'J', 'K', 'T', 'ИТОГО по ' . $option_orgs); //$total_top, $total_bottom,
            }
            
 
        }
        
       $objWriter = new PHPExcel_Writer_Excel2007($xls);
       $objWriter->setPreCalculateFormulas(true);
       $objWriter->save($report_dir . '/' . $report_file);

       $result = $report_relpath . '/' . $report_file;
       unset($db);
       return $result; 
   }
   
   public function multi_train_reestr($dp, $dc, $nd, $ini_str){
       $db = new db_depo();
       $records = $db->multi_train_reestr_list($dp, $dc, $nd, $ini_str);
       
       $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
        $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
        $report_file = substr(md5('catrep'), 0, 8). '.xlsx';    

         $xls = new PHPExcel();
         $xls->setActiveSheetIndex(0);

         $def_style = $xls->getDefaultStyle();
         $def_style->applyFromArray(self::$std_cell_fnt);
         $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                       ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                       ->setWrapText(true);

         $sheet = $xls->getActiveSheet();
         $sheet->getSheetView()->setZoomScale(80);

         $lastrow = $this->train_makeReportTitle($sheet);

         $count_iteration = 0;            
         $data_top = $data_bottom = 0;

         $total_arr = []; 

         $total_top = $lastrow + 1;
         $total_bottom = 0;

         $text = '';
            

        if(count($records) > 0){
            $org_rid = $rid_road = '';
            $current_org = [];

             $row_num = $grp_row_num = $grp_start_row  = 0;                 
             $arr_cells = [];
             $total_arr = [];
             $org_text = '';
             $option_orgs = '';
             
            foreach($records as $elem){
                if($elem['high'] != $org_rid){
                    
                    if(mb_strlen($option_orgs) > 0)
                               $option_orgs .= ', ';
                        
                        $option_orgs .= $elem['habb'];
                    
                    if($org_rid != ''){  

                        $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'АО ФПК');
                      
                        
                        $lastrow = $this->train_makeGroupFooter($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по ' . $current_org['abb'], $text);
                        array_push($arr_cells, $lastrow);
                        
                        
                        
                        $lastrow = $this->train_totalFooter($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $org_text, $arr_cells, 'F', 'G', 'H', 'I', 'J', 'K', 'T'); 
                        $arr_cells = [];
                        array_push($total_arr, $lastrow);
                        
                        $grp_row_num = $count_iteration = 0; 
                    }                   
                     
                    $lastrow = $this->subTitle_by_nm_direction($sheet, $lastrow + 1, $elem['httl']);
                   
                    $org_rid = $elem['high'];
                    $current_org = $elem;
                   
               
                    $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'W');
                    $grp_start_row = $lastrow;
                    $rid_road = $elem['rid_road'];
                    
                }
                else {
                    if($rid_road != $elem['rid_road']){

                       if($rid_road != ''){
                           
                             $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');

                             $lastrow = $this->train_makeGroupFooter($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по ' . $elem['abb'], $text);
                             array_push($arr_cells, $lastrow);
                             
                    }  

                           $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $elem['ttl'], 'W');
                           $grp_start_row = $lastrow;
                           $rid_road = $elem['rid_road'];

                           $grp_row_num = 0;   
                     }
                }
                
                $lastrow = $this->train_makeItemRow($db, $sheet, $lastrow + 1, $row_num, $elem);
                $row_num++;
                $grp_row_num++;
                $count_iteration++;           
                
            }
            
            if(count($current_org) > 0){
                
                        $text = 'Итого по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'филиалу');
                        $org_text = 'ВСЕГО по ' . (preg_match('#ДОСС#i', $current_org['habb']) == 1 ? 'дирекции' : 'АО ФПК');
                      
                        
                        $lastrow = $this->train_makeGroupFooter($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по ' . $current_org['abb'], $text);
                        array_push($arr_cells, $lastrow);
                        
                        
                        $lastrow = $this->train_totalFooter($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $org_text, $arr_cells, 'F', 'G', 'H', 'I', 'J', 'K', 'T'); 
                        array_push($total_arr, $lastrow);
                        
                        $this->train_total_result('A', $sheet, $lastrow + 1, $total_arr, 'F', 'G', 'H', 'I', 'J', 'K', 'T', 'ИТОГО по ' . $option_orgs); //$total_top, $total_bottom,
            }
            
 
        }

       $objWriter = new PHPExcel_Writer_Excel2007($xls);
       $objWriter->setPreCalculateFormulas(true);
       $objWriter->save($report_dir . '/' . $report_file);

       $result = $report_relpath . '/' . $report_file;
       unset($db);
       return $result; 
   }
}

/*
          $db = new db_depo();
        $records = $db->org_getList('38400d1f-dc47-4915-b44d-45ed3ae494a0');
        var_dump($records[0]['ttl']);
       
var_dump($records);

        $_FPK_ABB = '';
        $_DSS_ABB = '';
        $text = '';
        
        foreach($main_org_list as $row){
            if(preg_match('#ФПК#i', $high) == 1){
                $_FPK_ABB = $row['abb'];
            }else if (preg_match('#ДОСС#i', $high) == 1){
                $_DSS_ABB = $row['abb'];
            }
        }

 var_dump($_DSS_ABB, $_FPK_ABB);
*/
/* ПРЕДЫДУЩАЯ ФУНКЦИЯ ДЛЯ РАЗНЫХ РЕЕСТРОВ ПО ДОРОГЕ(ПОДРАЗДЕЛЕНИЕ) ДОСС И ФПК

public function road_reestr_group($org_rid){
       $db = new db_depo();
       $records = $db->carriage_getList($org_rid);      
       $road_nm = $db->org_getRec($org_rid);    
	   $result= '';
       
       if(count($records) > 0){
           $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
           $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
           
           if (_assbase::makeDir($report_dir)) {
               
                //$report_file = preg_replace('#"#' , "", $road_nm['abb']). '.xlsx';
               $report_file = substr(md5('report'), 0, 8). '.xlsx';  
                
                $xls = new PHPExcel();
                $xls->setActiveSheetIndex(0);
                
                
                $def_style = $xls->getDefaultStyle();
                $def_style->applyFromArray(self::$std_cell_fnt);
                $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                              ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                              ->setWrapText(true);
                
                $sheet = $xls->getActiveSheet();
                $sheet->getSheetView()->setZoomScale(80);
                
                $row_num = $grp_row_num  = 0;

                $org_record = $db->org_getRec($org_rid);
                $org_record_nm = $org_record['ttl'];
                
                if(preg_match('#ФПК#i', $road_nm['habb']) == 1){
                
                    $lastrow = $this->mdl_makeReportTitle_FPK($sheet);

                    $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $org_record_nm, 'AE');

                     $data_top = $lastrow + 1;

                    for($i = 0; $i < count($records); ++$i){
                            $row = $records[$i];

                            $passport_list = $db->carriage_getList_by_carriage_rid($row['rid']);

                            $lastrow = $this->reestr_makeItemRow_FPK($db, $sheet, $lastrow + 1, $row_num, $passport_list);
                            $row_num++;
                            $grp_row_num++;
                    }

                    $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $data_top, $lastrow, $grp_row_num, 'по ' . $org_record['abb']);
                     $grp_row_num = 0;
                
               }else if(preg_match('#ДОСС#i', $road_nm['habb']) == 1){
                   
                    $lastrow = $this->mdl_makeReportTitle_DSS($sheet);

                    $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $org_record_nm, 'S');

                     $data_top = $lastrow + 1;

                    for($i = 0; $i < count($records); ++$i){
                            $row = $records[$i];

                            $passport_list = $db->carriage_getList_by_carriage_rid($row['rid']);

                            $lastrow = $this->reestr_makeItemRow_DSS($db, $sheet, $lastrow + 1, $row_num, $passport_list);
                            $row_num++;
                            $grp_row_num++;
                    }

                    $this->reestr_makeGroupFooter_DSS($sheet, $lastrow + 1, $data_top, $lastrow, $grp_row_num, 'по ' . $org_record['abb']);
               }
                
                
                $objWriter = new PHPExcel_Writer_Excel2007($xls);
                $objWriter->setPreCalculateFormulas(true);
                $objWriter->save($report_dir . '/' . $report_file);
                
                $result = $report_relpath . '/' . $report_file;
             
           }
       }     
       unset($db);        
       return $result; 
     } 

ДЛЯ ДОСС И ФПК ОТДЕЛЬНОБ РЕССТР ПО ВСЕЙ ДИРЕКЦИИ
public function mdl_reestr_org($main_org_rid){ 
       $db = new db_depo();
       
       $road_list = $db->org_getList($main_org_rid);
       $org_list = $db->org_getRec($main_org_rid);
       $main_org_abb = $org_list['abb'];
       $main_org_ttl = $org_list['ttl'];
       
       if(count($road_list) > 0){
           
            $report_relpath = _assbase::siteRootDirX() . '/tmp/rep';
             $report_dir = $_SERVER['DOCUMENT_ROOT'] . $report_relpath;
             
             
             if (_assbase::makeDir($report_dir)) {
                
                $report_file = preg_replace('#"#' , "", $main_org_abb). '.xlsx';   

                $xls = new PHPExcel();
                $xls->setActiveSheetIndex(0);

                $def_style = $xls->getDefaultStyle();
                $def_style->applyFromArray(self::$std_cell_fnt);
                $def_style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                              ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                              ->setWrapText(true);

                $sheet = $xls->getActiveSheet();
                $sheet->getSheetView()->setZoomScale(80);
                 
                
                if(preg_match('#ФПК#i' , $main_org_abb) ==  1){
                              
                         $lastrow = $this->mdl_makeReportTitle_FPK($sheet);
                         $count_iteration = 0;

                         $data_top = $lastrow + 1;
                         $v_w_ab = [];
                         
                           foreach($road_list as $road_rec){
                             $road_carriage_list = $db->carriage_getList($road_rec['rid']);

                              if(count($road_carriage_list) > 0){

                                     $row_num = $grp_row_num  = 0;
                                    
                                     
                                     $org_record = $db->org_getRec($road_rec['rid']); 
                                     $org_record_nm = $org_record['ttl'];

                                     $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $org_record_nm, 'AE');
                                      $grp_start_row = $lastrow;
                                      
                                       for($i = 0; $i < count($road_carriage_list); ++$i){

                                               $row = $road_carriage_list[$i];

                                               $passport_list = $db->carriage_getList_by_carriage_rid($row['rid']);

                                               $lastrow = $this->reestr_makeItemRow_FPK($db, $sheet, $lastrow + 1, $row_num, $passport_list);
                                               $row_num++;
                                               $grp_row_num++;
                                               $count_iteration++;
                                       }                       
                                      
                                     $data_bottom = $lastrow;  

                                     $this->reestr_makeGroupFooter_FPK($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по ' . $org_record['abb']);
                                     $grp_row_num  = 0; 

                                     ++$lastrow;
                                       array_push($v_w_ab, $lastrow);
                          }
                   } 
                   //                     ($count_iteration, $sheet, $lastrow, $calc_fr, $calc_to, $arr, $column_v, $column_w, $column_ab)
                $this->mdl_totalFooter_FPK($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $v_w_ab, 'V', 'W', 'AB'); //['V', 'W', 'AB']
                   
                }else if(preg_match('#ДОСС#i' , $main_org_abb) == 1){
                         
                         $lastrow = $this->mdl_makeReportTitle_DSS($sheet);
                         $count_iteration = 0;
                         
                         $data_top = $lastrow + 1;
                         $j_k_p = []; 
                         
                           foreach($road_list as $road_rec){
                              $road_carriage_list = $db->carriage_getList($road_rec['rid']);

                               if(count($road_carriage_list) > 0){

                                     $row_num = $grp_row_num  = 0;

                                     $org_record = $db->org_getRec($road_rec['rid']); //!!!
                                     $org_record_nm = $org_record['ttl'];

                                     $lastrow = $this->make_yellow_Subtitle_by_name($sheet, $lastrow, $org_record_nm, 'S');                                     
                                     $grp_start_row = $lastrow;
                                     
                                       for($i = 0; $i < count($road_carriage_list); ++$i){

                                               $row = $road_carriage_list[$i];

                                               $passport_list = $db->carriage_getList_by_carriage_rid($row['rid']);
                                                
                                               $lastrow = $this->reestr_makeItemRow_DSS($db, $sheet, $lastrow + 1, $row_num, $passport_list);
                                               $row_num++;
                                               $grp_row_num++;
                                               $count_iteration++;
                                       }  
                                       
                                    $data_bottom = $lastrow;  

                                     $this->reestr_makeGroupFooter_DSS($sheet, $lastrow + 1, $grp_start_row, $lastrow, $grp_row_num, 'по ' . $org_record['abb']);
                                     $grp_row_num = 0; 
                                     ++$lastrow;
                                     
                                     array_push($j_k_p, $lastrow);
                             }
                   } 
                   
                   $this->mdl_totalFooter_DSS($count_iteration, $sheet, $lastrow + 1, $data_top, $data_bottom, $j_k_p, 'J', 'K', 'P');
               }
        }
        $objWriter = new PHPExcel_Writer_Excel2007($xls);
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save($report_dir . '/' . $report_file);

        $result = $report_relpath . '/' . $report_file;
     } 
       unset($db);        
       return $result; 
   }

 */


?>

