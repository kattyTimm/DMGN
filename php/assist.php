<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pointsbase/php/_dbpoints.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pointsbase/php/_asspoints.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/dmgnPsAdm/php/db_depo.php';

class assist {

    public static $copyright_str = '&copy;ПКТБ Л, 2020', 
                  $plaining_period = [0 => "", 1 =>  "по достижению срока проведение кап. ремонта", 2 => "по достижению срока/пробега проведение кап. ремонта"];
    
    public function __construct() {
        if (strlen(trim(session_id())) == 0)
            session_start();
    }
    
    public static function siteRootDir() : string {    // site root must have index.php. directory will return with starting slash, ex: /IcmrM
        return _assbase::siteRootDir_($_SERVER['PHP_SELF']);
    }

    public function get_fm(string $fm_name, string $sparam = ''){
        $result = '';
        
        $fm_path = 'forms/'. $fm_name .'.php';
        
        if(file_exists($fm_path)){
            $form = file_get_contents($fm_path);
            
            if($fm_name == 'form_works_adaptation'){
               
                $options = "";
                
                foreach(self::$plaining_period as $key => $val)
                       $options .= "<option value='".$key."'>".$val."</option>";
                
                $form = str_replace('{ROWS}', $options, $form);
            }
         
                $result = $form;
        }else{
                    $assbase = new _assbase();
                    $result = $assbase->get_fm($fm_name, $sparam);
                    unset($ass);
                }

         return $result;
    }
   
    public function get_btns_for_user_FPK(){
        $result = [];
        
        $result['btn_for_curriage'] = '<button class="card-header btn modal-header" id="show_carriage">Паспорт моделей вагонов</button>';
        $result['btn_for_train'] = '<button class="card-header btn modal-header" id="show_train">Паспорт пассажирских поездов</button>';
        
        return $result;
    }
    
    public function get_detail_body($org_rid, $section){
        $result = [];
        $db = new db_depo();
        $org_rec = $db->org_getRec($org_rid);
        unset($db);
        
        $content = '<div id="tmp_for_form" class="y-flex-row-nowrap h-100">'.                
                        '<div class="card detail-card detail-dmgn-card y-shad">' .

                              '<div class="card-header y-flex-row-nowrap y-align-items-center" id="center_part_header">'.
                                    '<div id="filter_tmp" class="dropdown">'. //style="visibility:'.($section == 'carriage' ? 'visible ' : 'hidden').'"
                                        '<img src="img/dots_mnu_20.png" id="show_filter" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.                

                                        '<div id="filter" class="dropdown-menu" aria-labelledby="dropdownMenuButton">'.										
                                              '<a id="show_reestr" class="dropdown-item" onclick="get_oneRecord_reestr();">'. // id="show_form_4_reestr" onclick="get_form_for_reestr();"
                                                    "<i class='fab fa-medium y-lgray-text'></i> &nbsp; " . 'Реестр по '.($section == 'carriage' ? 'модели' : 'поезду').
                                                '</a>'.

                                                '<a id="show_group_reestr" class="dropdown-item" onclick="show_group_excel()">'.
                                                    "<i class='fas fa-road y-lgray-text'></i> &nbsp; " . 'Реестр по подразделению'.
                                                '</a>'.
                                        '</div>'.

                                    '</div>'.

                                    '<div id="main_org-'.$org_rec['high'].'" class="p-0"><span id="org_abb-'.$org_rec['rid'].'">'.$org_rec['habb'].' . '. $org_rec['ttl'] .'. </span>Реестры доступности '.($section == 'carriage' ? 'вагонов' : 'поездов').'</div>'.
                                    '<img id="show_form" class="d-block ml-auto" src="img/add_48.png" data-toggle=\'tooltip\' title=\'Добавить паспорт\'> ' .
                               '</div>' .
                
                             '<div class="y-flex-column-nowrap" id="registr_div">'.
                                          '{registers_table}'.

/*                               '<div class="card-header y-flex-row-nowrap y-align-items-center">' .
                                    ' <button id="show_reestr" class="btn btn-info" style=\'margin-right:10px;\'>'.
                                           'Показать реестр'.
                                    '</button>'.
                                    ' <button id="show_gruop_reestr" class="btn btn-info">'.
                                           'Показать реестр по подразделению'.
                                    '</button>'.


                                '</div>'. 
*/
                                '<div id="common_div_docs" class="y-flex-row-nowrap mt-auto" style=\'height:25%;\'>'.

                                    ($section == 'carriage' ? 
                                        '<div id="passport_div_tmp" class="y-mrg-r10 y-flex-column-nowrap" style=\'align-items:stretch;\'>'. // див с документами

                                                '<div class="card-header y-flex-row-nowrap y-align-items-center">'. 
                                                   '<div class="p-0">Паспорт </div>'.
                                                    '<img id="add_new_passport_pdf" class="d-block ml-auto" src="img/add_48.png" data-toggle=\'tooltip\' title=\'Добавить новый паспорт\'> ' .
                                                '</div>' .

                                                '<div id="passport_tmp">'.   
                                                       // '{passport_cards}'.
                                                '</div>' .

                                        '</div>' // passport_div_tmp  
                                    : '').

                                        '<div id="docs_div_tmp" class="y-flex-column-nowrap">'. //                                                                 

                                                '<div class="card-header y-flex-row-nowrap y-align-items-center">'. 
                                                    '<div class="p-0">Документация </div>'.
                                                    '<img id="add_new_doc" class="d-block ml-auto" src="img/add_48.png" data-toggle=\'tooltip\' title=\'Добавить новый документ\'> ' .
                                                '</div>' .

                                                '<div id="docs_tmp">'.   
                                                    // '{docs_cards}'.
                                                '</div>' .

                                        '</div>'. // docs_div_tmp

                                    '</div>'. //end of common_div_docs

                                '</div>' . //end of registr_div

                        '</div>' . // end of detail-dmgn-card

                         '<div class="card detail-card detail-comm-card y-shad">' . 

                             '<div class="card-header y-flex-row-nowrap y-align-items-center">' .                                                       
                                '<div class="p-0">Работы по адаптации</div>'.
                                '<img id="new_actions" class="d-block ml-auto" src="img/add_48.png" data-toggle=\'tooltip\' title=\'Добавить паспорт\'> ' . 
                            '</div>'. 

                            '<div id="actions_div">'.  // class="y-flex-column-nowrap" 
                              //'{actions_table}'.
                           '</div>'.

                        '</div>'.


                   '</div>'; // end of tmp_for_form
        
        $content = str_replace("{registers_table}", $this->get_registers_table($org_rid, $section) ,$content); // что заменяем, чем заменяемб где заменяем 

        $result['content'] = $content;
                
        return $result;
    }
    
    
    public function get_document_part($pid){
        
        $part = "<div class=\"mt-auto docs_div\">". //    data_registr-rid=''  id=\"docs_div\"
                       "{ROWS}".
                "</div>";
        
        $db = new db_depo();
        $arr = $db->docs_getDocsList4Pid($pid);
        $rowset = '';
        
        foreach($arr as $row){
            $ftype = _assbase::getFtypeByFname($row['fnm']); 
            
            $img = $ftype == "unk" ? "" :
                            "<img class='y-cur-point' src='/pktbbase/img/file/" . $ftype . "_32.png' onclick='doc_view_click(this);' data-doc='" .$row['rid']."'>";
            
             $fnm_ttip = mb_strlen($row['fnm']) > 30 ? "data-toggle='tooltip' title='" . $row['fnm'] . "' data-delay='100'" : "";
             
             $fnm = "<span class='y-cur-point y-gray-text' onclick='doc_view_click(this);' data-doc='" . $row['rid'] . "' " . $fnm_ttip . ">" . 
                                _dbbase::shrink_filename($row['fnm'], 30) . "</span>";            
       
            
            $rowset .= "<span class='badge badge-light y-fw-normal y-shad badge-dmgn-doc' style='margin:8px 4px;'>" .
                                    $img . $fnm . "&nbsp; <img src='img/delete_15.png' onclick='delete_badge(this);' id='docs-".$row['rid']."'>"  .
                        "</span>";
        }
        
       $part = str_replace("{ROWS}", $rowset, $part);
        
        return $part;
    }
    
    public function get_passportPdf_part($pid){
        $part = "<div class=\"mt-auto docs_div\">".
                       "{ROWS}".
                "</div>";
         
        $db = new db_depo();
        $arr = $db->passport_pdf_record_by_roadRid($pid);
        $rowset = '';
        
        foreach($arr as $row){
            $ftype = _assbase::getFtypeByFname($row['fnm']); 
             
            $img = $ftype == "unk" ? "" :                                                       //  // doc_view_click ??????   
               "<img class='y-cur-point' src='/pktbbase/img/file/" . $ftype . "_32.png' onclick='pass_pdf_view_click(this);' data-doc='" .$row['rid']."'>";
            
            $fnm_ttip = mb_strlen($row['fnm']) > 30 ? "data-toggle='tooltip' title='" . $row['fnm'] . "' data-delay='100'" : "";
             
                                                                 // doc_view_click ??????
            $fnm = "<span class='y-cur-point y-gray-text' onclick='pass_pdf_view_click(this);' data-doc='" . $row['rid'] . "' " . $fnm_ttip . ">" . 
                                _dbbase::shrink_filename($row['fnm'], 30) . "</span>";            
       
            
            $rowset .= "<span class='badge badge-light y-fw-normal y-shad badge-dmgn-doc' style='margin:8px 4px;'>" .
                                    $img . $fnm . "&nbsp; <img src='img/delete_15.png' onclick='delete_passport_pdf_badge(this);' id='passportPdf-".$row['rid']."'>"  .
                        "</span>";
        }
        
        $part = str_replace("{ROWS}", $rowset, $part);
        
        return $part;    
    }
    
    public function get_common_part($curr_pass, $section){
        $result = '';
        $db = new db_depo();
        
        if($section == 'carriage'){
            $arr = $db->get_actionsList($curr_pass);       
            
        }else if ($section == 'train'){
            $arr = $db->get_train_actionsList($curr_pass);
        }
        
            foreach($arr as $row)
                 $result .= $this->get_common_part_card_rows_carriage($row, $section);
        
        return $result;
    }
    
    public function get_common_part_card_rows_carriage($row, $section){
        
    $flg = intval($row['flg']) & 0x3FFFFFF;   // все 26 битов , во всех F по 4 единицы, 3 в hex тоже две единицы, с операцией & биты остаются такими какими есть(1 остается еденицей, 0 - нулем)
        
    $pp = $flg & 0xFFFFFF; // 24 бита  группы plaining period, F = 4 бита(1111) 
    $ppStr ='';
    
             if($pp > 0){ 
                    $year_quarter = $pp & 0xFFFF; // год и квартал вместе (16 бит)
                    if($year_quarter > 0){
                        $year = $year_quarter & 0xFFF; // (на year 12 бит)
                        $quarter = ($year_quarter >> 12) & 0xF;       // (на quarter 4 бита) 
                        if($year > 2000 && $year < 2070 && $quarter >= 0 && $quarter < 5){
                            $ppStr = ($quarter > 0 ? $quarter. ' кв. ' : ''). $year;
                        }
                    }else{
                              // $flg >> 16 потому что берем его из той же группы($pp), а биты для селекта начинаются с 16!!! Поэтому и сдвинуть надо на 16   
                        $pp1 = ($flg >> 16) & 0xFF;  // (0xFF) 8 бит на селект, они с 16 по 23й бит ($pp1 это селкет)
                        $ppStr = self::$plaining_period[$pp1];
                    }
             }
             
             if(mb_strlen($ppStr) == 0){
                 $ppStr = 'Не определено';
             }
          
             
             
       switch  (($flg >> 24) & 0x3){ //  (($flg >> 24) - берем  с 24 по 25 бит, 3 в хекс это 11
           case 1 : $markStr = 'Выполнено'; break;
           case 2 : $markStr=  'Не выполнено'; break;
           default : $markStr = 'Не определено';
       }     

        
        $l_comm_classes      = "d-table-cell y-lgray-text y-fz08 y-wdt-col-4 align-middle y-border-b";
        $l_last_row_classess = "d-table-cell y-lgray-text y-fz08 y-wdt-col-4 align-middle";
        
        $r_comm_classes      = "d-table-cell y-wdt-col-8 y-fz10 y-pad-lr5 y-steel-blue-text align-middle y-border-b";
        $r_last_row_classess = "d-table-cell y-wdt-col-8 y-fz10 y-pad-lr5 y-steel-blue-text align-middle";
        
        return  "<div class='card y-mrg-a10 y-shad'>".    // d-inline-block
               
                       "<div id='action_card-".$row['rid']."' data-flg='". $row['flg']."' class='card-body y-cur-point d-table y-pad-a10'>".   //self::$plaining_period      y-wdt-col-12 y-fs-i
                            "<div class='d-table-row'><span class='" . $l_comm_classes . "'>Меры по адаптации </span><span id='twa-".$row['rid']."' class='" . $r_comm_classes . "'>" . $row['twa'] . "</span></div>".
                            "<div class='d-table-row'><span class='" . $l_comm_classes . "'>Планируемый период выполнения работ </span> <span class='". $r_comm_classes ."' id='pp-".$row['rid']."'>".$ppStr."</span></div>".
                            "<div class='d-table-row'><span class='" . $l_comm_classes . "'>Отметка о выполнении работ </span><span class='". $r_comm_classes ."' id='mark-".$row['rid']."'>".$markStr."</span></div>".
                            "<div class='d-table-row'><span class='" . $l_last_row_classess ."'>Причины невыполнения </span><span class='". $r_last_row_classess ."' id='pnv-".$row['rid']."'>".$row['pnv']."</span></div>".
                       "</div>".              

                       "<div class='card-footer bg-transparent'>".
                            "<img id='edit-".$row['rid']."' src='img/edit_32_transp.png' onclick='edit_action_record(this);' data-section='".$section."'>" .
                            "<small><img id='del_pp-".$row['rid']."' src='img/deleter_24.png' onclick='delete_action_record(this);' data-section='".$section."'></small>" .
                       "</div>".       
               
                "</div>";
       /*
        $comm_classes = "d-table-cell align-middle y-border-b";
        $last_row_classess = "d-table-cell align-middle";
        
       return  "<div class='card y-mrg-a10 y-shad d-inline-block'>". // style='display:flex;flex-direction:column;'
               
                       "<div id='action_card-".$row['rid']."' data-flg='". $row['flg']."' class='card-body  y-cur-point d-table y-pad-a10'>".   //self::$plaining_period    // y-wdt-col-12
                           "<div class='d-table-row '>".
                                "<span class='" . $comm_classes . " y-lgray-text y-fz08 y-wdt-col-4'>Меры по адаптации</span>".
                                "<span id='twa-".$row['rid']."' class='" . $comm_classes . " y-pad-lr5 y-wdt-col-8 y-fz10 y-steel-blue-text y-fs-i'>" . $row['twa']. "</span>".
                            "</div>".
                             "<div class='d-table-row'><span class='d-table-cell y-wdt-col- y-lgray-text y-fz08 y-wdt-col-4 ". $comm_classes ."'>Планируемый период</span> <span class='d-table-cell ". $comm_classes ." y-fz10 y-steel-blue-text y-fs-i' id='pp-".$row['rid']."'>".$ppStr."</span></div>".
                              "<div class='d-table-row'><span class='d-table-cell y-wdt-col- y-lgray-text y-fz08 y-wdt-col-4 ". $comm_classes ."'>Отметка о выполнении работ</span><span class='d-table-cell ". $comm_classes ." y-fz10 y-steel-blue-text y-fs-i' id='mark-".$row['rid']."'>".$markStr."</span></div>".
                              "<div class='d-table-row'><span class='d-table-cell y-wdt-col- y-lgray-text y-fz08 y-wdt-col-4 ". $last_row_classess ."'>Причины невыполнния</span><span class='d-table-cell ". $last_row_classess ." y-fz10 y-steel-blue-text y-fs-i' id='pnv-".$row['rid']."'>".$row['pnv']."</span></div>".
                       "</div>".              

                       "<div class='card-footer bg-transparent'>".
                                  "<img id='edit-".$row['rid']."' src='img/edit_32_transp.png' onclick='edit_action_record(this);' data-section='".$section."'>" .
                                 "<small><img id='del_pp-".$row['rid']."' src='img/deleter_24.png' onclick='delete_action_record(this);' data-section='".$section."'></small>" .
                       "</div>".       
               
                "</div>";
       */
    }

    public function get_registers_table($org_rid, $section){
        $result = $this->get_registers_table_head($section);
        $db = new db_depo();
        
        $records = '';
        
        if($section == 'carriage'){
            $arr = $db->carriage_getList($org_rid);
            
            foreach($arr as $row)
                $records .= $this->get_registers_table_rows($row);
        }else if($section == 'train'){
             $arr = $db->train_getList_by_NUM_PREFIX($org_rid);
            
             foreach($arr as $row)
                $records .= $this->get_registers_train_rows($row);
        }
        
        $result = str_replace("{ROWSET}", $records, $result);
        
        return $result;
    }
    
    public function get_registers_table_head ($section){
        $comm_classes = "text-center align-middle y-border-no";

        return  "<div id='div_ta_".($section == 'carriage' ? 'registr' : 'train')."' class='table-responsive y-mrg-b20 y-mrg-b10' style='overflow-y:auto;'>" .
                   "<table id='ta_".($section == 'carriage' ? 'registr' : 'train')."' class='table table-hover table-colored table-centered table-inverse table-striped m-0 y-border-b'>" .   //overflow-y:hidden;
                       "<thead>" .
                           "<tr>" .
                              ($section == 'carriage' ? "" :
                               "<th class='y-maxw-col-8 " . $comm_classes . "'>Номер поезда</th>" ).
                                "<th class='y-wdt-col-4 " . $comm_classes . "'>".($section == 'carriage' ? 'Модель' : 'Маршрут')."</th>" .
                           "</tr>" .
                       "</thead>" .
                       "<tbody>" .
                           "{ROWSET}" .
                       "</tbody>" .
                   "</table>" .
               "</div>";
    }
    
    
    /*

    "<tr>" .
                               "<th class='y-maxw-col-8 " . $comm_classes . "'>".($section == 'carriage' ? 'Наименование' : 'Номер поезда')."</th>" .
                                "<th class='y-wdt-col-4 " . $comm_classes . "'>".($section == 'carriage' ? 'Модель' : 'Маршрут')."</th>" .
                           "</tr>" .
    
         */
    
    public function get_registers_table_rows($row){
         $result = '';
        
        if (is_array($row)) {
          $comm_classes = "align-middle y-border-no-t h-100";
        } 
        
       //   if (strcasecmp($row['rid'], $org_current_rid) == 0)
                    //   $comm_classes .= " tbl-act-cell";
        
        $td_style_ttl   = "class='text-left tbl-order " . $comm_classes . "'";
        $td_style_mdl = "class='text-left position-relative overflow-y-hidden " . $comm_classes . "'";               
                       
            $act_edit   = "<a id='a_edit_registr-" . $row['rid'] . "' data-carr_mdl='".$row['carr_mdl']."' href='javascript:;' class='y-mrg-lr5' onclick='registr_edit_click(this);' data-toggle='tooltip' title='Редактировать' data-delay='100'>" .
                              "<img src='/pktbbase/img/edit_32.png'>" . 
                          "</a>";
          
            $act_delete =   
                          "<a id='a_delete_registr-" . $row['rid'] . "' href='javascript:;' class='y-mrg-lr5' onclick='delete_redistrOrTrain_click(this);' data-toggle='tooltip' title='Удалить' data-delay='100'>" .
                              "<img src='/pktbbase/img/delete_32.png'>" . 
                          "</a>";
        
         
        $result =   "<tr id='tr_registr-" . $row['rid'] . "' class='y-cur-def' onclick='passport_click(this);' data-dt_nm_reg='".$row['date_num_registr']."' data-docs_constr='".$row['docs_at_constr']."'".
                                                                                                                    "data-docs_modern='".$row['docs_at_modern']."' data-flg='".$row['flg']."' data-exp_res='".$row['exp_res']."' ". 
                                                                                                                     "data-recoms_using='".$row['recoms_using']."' data-date_act='".$row['date_act']."' data-note='".$row['note']."' data-org='".$row['org']. "'"
                                                                                                                     ."data-carr_mdl='".$row['carr_mdl']."' >".                            
/*
                            "<td id='td_registr_nm-" . $row['rid'] . "' " . $td_style_ttl . ">" .                      
                                $row['obj_nm'] .
                            "</td>" .
                    */
                            "<td id='td_registr_mdl-" . $row['rid'] . "' " . $td_style_mdl . ">" .
                               "<span id='a_registr_mdl-" . $row['rid'] . "' class='y-steel-blue-text d-block y-pad-tb0'>" . 
                                    $row['mdl'] .
                                "</span>" .
                         
                               "<div class='acts-panel position-absolute text-center invisible' style='top:0;right:0;width:auto;'>" .
                                    "<div class='acts-inner' style='height:auto;width:auto;'>". $act_edit  . $act_delete  . "</div>" .  //. $act_edit  . $act_delete  . "
                                "</div>" .
                            "</td>" .
                
                        "</tr>";
                       
      return $result;                 
    }
    
        public function get_registers_train_rows($row){
        $result = '';
        
        if (is_array($row)) {
          $comm_classes = "align-middle y-border-no-t h-100";
        }
                      
        $act_edit   = "<a id='a_edit_train-" . $row['rid'] . "' href='javascript:;' class='y-mrg-lr5' onclick='train_edit_click(this);' data-toggle='tooltip' title='Редактировать' data-delay='100'>" .
                          "<img src='/pktbbase/img/edit_32.png'>" . 
                      "</a>";

        $act_delete =   
                      "<a id='a_delete_train-" . $row['rid'] . "' href='javascript:;' class='y-mrg-lr5' onclick='delete_redistrOrTrain_click(this);' data-toggle='tooltip' title='Удалить' data-delay='100'>" .
                          "<img src='/pktbbase/img/delete_32.png'>" . 
                      "</a>";
        
        
        
        $td_style_ttl   = "class='text-left tbl-order " . $comm_classes . "'";
        $td_style_mdl = "class='text-left position-relative overflow-y-hidden " . $comm_classes . "'";               
        
         if(strlen($row['rid']) > 0){
            $result =   "<tr id='tr_train-" . $row['rid'] . "' class='y-cur-def' onclick='train_click(this);' data-num_train='".$row['num_train']."' data-route='".$row['route']."'".
                                                                                  "data-adr_formation='".$row['adr_formation']."' data-date_num_registr='".$row['date_num_registr']."' data-flg='".$row['flg']."' ". 
                                                                                  "data-recoms_using='".$row['recoms_using']."' data-exp_res='".$row['exp_res']."' data-date_act='".$row['date_act']."' data-note='".$row['note']."'".
                                                                                  "data-rid_road='".$row['rid_road']. "' >".                            

                                "<td id='td_train_num-" . $row['rid'] . "' " . $td_style_ttl . ">" .                      
                                    $row['num_train'] .
                                "</td>" .

                                "<td id='td_train_route-" . $row['rid'] . "' " . $td_style_mdl . ">" .
                                   "<span id='a_registr_route-" . $row['rid'] . "' class='y-steel-blue-text d-block y-pad-tb0'>" . 
                                        $row['route'] .
                                    "</span>" .

                                   "<div class='acts-panel position-absolute text-center invisible' style='top:0;right:0;width:auto;'>" .
                                        "<div class='acts-inner' style='height:auto;width:auto;'>". $act_edit  . $act_delete  . "</div>" .  
                                    "</div>" .
                                "</td>" .

                            "</tr>";
         }else{
              $result = 'таблица базы данных пуста';
         }                
      return $result;                 
    }
    
    
    public function add_carr_pasport($rid,$nm, $mdl, $dt, $docs_constr, $docs_modern, $exp_res, $recoms, $act_dt, $flg, $note, $carr_mdl, $org_param){
        $db = new db_depo();

        $result = $db->add_carr_pasport_availability($rid, $nm, $mdl, $dt, $docs_constr, $docs_modern, $exp_res, $recoms, $act_dt, $flg, $note, $carr_mdl, $org_param);
        unset($db);
        return $result;
    }
    
    public function add_train_data($rid, $num_train, $route, $adr_formation, $dt_regs, $exp_res, $date_act, $recoms_using, $note, $flg, $org){
        $db = new db_depo();
        
        $result = $db->addOrEdit_train_passport_availability($rid, $num_train, $route, $adr_formation, $dt_regs, $exp_res, $date_act, $recoms_using, $note, $flg, $org);
        unset($db);
        return $result;
    }
}

//$ass = new assist();
//var_dump($ass->get_fm('form_for_train'));
//unset($ass);

?>
