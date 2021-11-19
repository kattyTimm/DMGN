<?php
// gate for .js ajax queries
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_dbbase.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_assbase.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/pktbbase/php/_jgpktb.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/dmgnPsAdm/php/db_depo.php';

include_once 'assist.php';
include_once 'xlsx.php';

$form_data = [];    //Pass back the data

$part = strval($_POST['part']);
$ass = new assist();    
$form_data['success'] = false;
$db = new db_depo();

/*if ($part == 'get_app_globals') {
    $jg = new _jgpoints();
    $jg->jg_get_app_globals($form_data);     // $form_data pass by reference
    unset($jg);

    $jg = new _jgpktb();
    $jg->jg_get_app_globals($form_data);     // $form_data pass by reference
    unset($jg);

    $form_data['domain'] = _assbase::get_currentClientDomain();
    $form_data['itr']    = _assbase::isAdminIP('dmgnI') ? 1 : 0;                 // 1: Trusted IP
    $form_data['dtr']    = _assbase::isAdminDomain('dmgnI') ? 1 : 0;             // 1: ...

    $form_data['success'] = true;
} 

else*/

if($part == 'clnf_get_rec_by_ip'){
    
        $ip = _dbbase::get_currentClientIP();

        $form_data['ip']  = $ip;
        $result = $db->clnf_getRecByIP($ip);
        
        /*                         'rid' => $sel_rid
                                    'org' => $sel_org,
                                    'flg' => $sel_flg,
                                    'ip'  => $sel_ip,
                                    'rem' => $sel_rem,

                                    'ohigh' => $sel_ohigh,
                                    'ottl'  => $sel_ottl,
                                    'oabb'  => $sel_oabb,
                                    'httl'  => $sel_httl,
                                    'habb'  => $sel_habb,
                                    'hhttl' => $sel_hhttl,
                                    'hhabb' => $sel_hhabb,
        
        */
        
        if (count($result) > 0) {
            $form_data['rid']  = $result['rid'];
            $form_data['org']  = $result['org'];
            $form_data['flg']   = $result['flg'];
            $form_data['ip']   = $result['ip'];
            $form_data['rem']  = $result['rem'];
            $form_data['success'] = true;
        }
}

else if($part == 'get_fm'){
    $fm_id = strval($_POST['fm_id']);
    $sparam = array_key_exists('sparam', $_POST) ? trim(strval($_POST['sparam'])) : "";
    
    $result = $ass->get_fm($fm_id, $sparam);
    
    $form_data['html'] = $result;
    if (strlen($result) > 0)
           $form_data['success'] = true;
    
}

else if($part == 'show_buttons'){
      if(strval($_POST['type']) == 'FPK'){
           $result = $ass->get_btns_for_user_FPK();
    
            if(count($result) > 0){
               $form_data['success'] = true;
               $form_data['btn_for_curriage'] = $result['btn_for_curriage'];
               $form_data['btn_for_train'] = $result['btn_for_train'];
           }
      }   
}

else if($part == 'get_detail_body'){
    $org_rid = strval($_POST['org']);
    $section = strval($_POST['section']);
    
    $result = $ass->get_detail_body($org_rid, $section);
    
       if(count($result) > 0){
           $form_data['success'] = true;
           $form_data['content'] = $result['content'];
       }           
}


else if($part == 'carr_data_send'){
    $rid = strval($_POST['rid']);
    $nm = strval($_POST['nm']);
    $mdl = strval($_POST['mdl']);
    $dt = strval($_POST['dt']); 
    $docs_constr = strval($_POST['docs_constr']);
    $docs_modern = strval($_POST['docs_modern']);        
    $exp_res = strval($_POST['exp_res']);      
    $recoms = strval($_POST['recomends_using']);
    $act_dt = strval($_POST['act_dt']); // !== '') ? strval($_POST['act_dt']) : date('Y.m.d H:i:s');
    $flg = intval($_POST['flg']); 
    $note = strval($_POST['note']); 
    $org_param = strval($_POST['org']); 
    $carr_mdl = strval($_POST['carr_mdl']);
    
    $result = $ass->add_carr_pasport($rid, $nm, $mdl, $dt, $docs_constr, $docs_modern, $exp_res, $recoms, $act_dt, $flg, $note, $carr_mdl, $org_param);
       
    if(strlen($result) > 0){
       $form_data['success'] = true;
       $form_data['rid'] = $result;
    }   
}
/*
else if ($part == 'carriage_records_by_self_rid'){    // !!!
    $rid = strval($_POST['rid']);
            
    $result = $db->carriage_getList_by_carriage_rid($rid);
    
    if(count($result) > 0){               
        $form_data['success'] = true;    
        
        $form_data['rid'] = $result['rid'];
        $form_data['obj_nm'] = $result['obj_nm']; // ?? 
        $form_data['mdl'] = $result['mdl'];
        $form_data['date_num_registr'] = $result['date_num_registr'];
        $form_data['docs_at_constr'] = $result['docs_at_constr'];
        $form_data['docs_at_modern'] = $result['docs_at_modern'];
        $form_data['exp_res'] = $result['exp_res'];
        $form_data['recoms_using'] = $result['recoms_using'];
        $form_data['date_act'] = $result['date_act'];
        $form_data['note'] = $result['note'];
        $form_data['org'] = $result['org'];
        $form_data['carr_mdl'] = $result['carr_mdl'];
        $form_data['flg'] = $result['flg'];
    }
}
*/
else if($part == 'data_train'){
    
    $rid = strval($_POST['rid']);
    $num_train = strval($_POST['num_train']);
    $route = strval($_POST['route']);
    $adr_formation = strval($_POST['adr_formation']);
    $dt_regs = strval($_POST['date_num_registr']);
    $exp_res = strval($_POST['exp_res']);
    $recoms_using = strval($_POST['recoms_using']);
    $date_act = strval($_POST['date_act']);
    $note = strval($_POST['note']);
    $flg = intval($_POST['flg']);
    $org = strval($_POST['org']);
 
    $result = $ass->add_train_data($rid, $num_train, $route, $adr_formation, $dt_regs, $exp_res, $date_act, $recoms_using, $note, $flg, $org);
    
    if(strlen($result) > 0){
       $form_data['success'] = true;
       $form_data['rid'] = $result;
    }
}

else if($part == 'del_registr'){
    $rid = strval($_POST['rid']);
    $org = strval($_POST['org']);
    
    $result = $db->carriage_delRec($rid, $org);    
       if($result){
           $form_data['success'] = true;
       }
           
}

else if($part == 'del_train'){
    $rid = strval($_POST['rid']);
    
    $result = $db->train_delRec($rid);
    
       if($result){
           $form_data['success'] = true;
       }
           
}

else if ($part == 'get_actions'){
     $rid = strval($_POST['rid']);
     $section = strval($_POST['section']);
     
         $result = $ass->get_common_part($rid, $section);   
     
   //  if(strlen($result > 0)){
        $form_data['success'] = true;
        $form_data['actions'] = $result;
  //   }
}

else if($part == 'check_action_record'){
    $rid = strval($_POST['rid']);
    $section = strval($_POST['section']);
    
    $carr_pasport = strval($_POST['carr_pasport']);
    $flg = intval($_POST['flg']); 
    $twa = strval($_POST['twa']); 
    $pnv = strval($_POST['pnv']); 
    
    if($section == 'carriage'){
        $result = $db->check_action_record($rid, $carr_pasport, $flg, $twa, $pnv);
    }else if($section == 'train'){
         $result = $db->check_Train_action_record($rid, $carr_pasport, $flg, $twa, $pnv);
    }   
    
    if(strlen($result) > 0){
       $form_data['success'] = true;
       $form_data['rid'] = $result;
    }   
}

else if($part == 'del_action'){
    $rid = strval($_POST['rid']);
      $section = strval($_POST['section']);
   
     if($section == 'carriage'){
         $result = $db->action_delRec($rid);
    }else if($section == 'train'){
           $result = $db->action_delRec_train($rid);
    }   
    
       if($result){
           $form_data['success'] = true;
       }
           
}
else if($part == 'docs_part'){
     $pid = strval($_POST['pid']);
     
     $result = $ass->get_document_part($pid);
      
     $form_data['html'] = $result;
            $form_data['success'] = true;
}

else if($part == 'pass_pdf_part'){
     $pid = strval($_POST['carr_mdl_rid']);
    // $main_org = strval($_POST['main_org']);
     
     $result = $ass->get_passportPdf_part($pid);
      
     $form_data['html'] = $result;
            $form_data['success'] = true;
}

else if($part == 'docs_add_rec'){
        $tbl = trim(strval($_POST['tbl']));
        $pid = trim(strval($_POST['pid']));
        $fnm = _dbbase::shrink_filename(trim(strval($_POST['fnm'])), 50);
        $nm  = mb_substr(trim(strval($_POST['nm'])), 0, 50);
        $flg = intval($_POST['doc_flg']);
        $rdat = strval($_POST['rdat']);
        
        if($flg == 1 || $flg == 2){
            $result = $db->docs_addFile($tbl, $pid, $fnm, $nm, $flg, $rdat);
        }else if($flg == 3){
             $result = $db->passport_pdf_addFile($tbl, $pid, $flg, $fnm, $nm,  $rdat);
        }
        
        if (strlen($result) > 0) {
            $form_data['pid'] = $pid;
            $form_data['docs_rid'] = $result;
            $form_data['success'] = true;
        }            
}

else if($part == 'file_put_tmp'){
        $rid = trim(strval($_POST['val']));

        $result = $db->docs_getRec($rid);

        if (count($result) > 0) {
            $fname = _assbase::dataUri2tmpFile($_SERVER['DOCUMENT_ROOT'] . assist::siteRootDir() . '/tmp', $result['fnm'], $result['rdat']);

            if (mb_strlen($fname) > 0) {
                $form_data['frelname'] = assist::siteRootDir() . '/tmp/' . $result['fnm'];
                $form_data['success'] = true;
            }
        }
}

else if($part == 'file_put_tmp_pdf'){
        $rid = trim(strval($_POST['val']));

        $result = $db->passport_pdf_record_by_rid($rid);

        if (count($result) > 0) {
            $fname = _assbase::dataUri2tmpFile($_SERVER['DOCUMENT_ROOT'] . assist::siteRootDir() . '/tmp', $result['fnm'], $result['rdat']);

            if (mb_strlen($fname) > 0) {
                $form_data['frelname'] = assist::siteRootDir() . '/tmp/' . $result['fnm'];
                $form_data['success'] = true;
            }
        }
}

else if($part == 'delete_doc'){
    $rid = strval($_POST['rid']);
    
    $result = $db->docs_delRec($rid);
    
   $form_data['rid'] = $result;
   $form_data['success'] = true;
}

else if($part == 'delete_pass_pdf'){
    $rid = strval($_POST['rid']);
    
    $result = $db->passport_pdf_delRec($rid);
    
   $form_data['rid'] = $result;
   $form_data['success'] = true;
}

else if($part == 'rep_carriage'){
    $rid_mdl = strval($_POST['rid_mdl']);
    $section = strval($_POST['section']);
    $ttl = strval($_POST['ttl']);
    
    $xlsx = new xlsx();
     if($section == 'carriage'){
        $result = $xlsx->mdl_reestr($rid_mdl, $ttl);
    }else if($section == 'train'){
        $result = $xlsx->one_train_reestr($rid_mdl, $ttl);
    }
    
    unset($xlsx);
    
     $form_data['frelname'] = $result;
     $form_data['success'] = mb_strlen($result) > 0;
    
}

else if($part == 'road_reestr'){
    $rid_road = strval($_POST['rid_road']);
    $section = strval($_POST['section']);
    
    $xlsx = new xlsx();
    if($section == 'carriage'){
         $result = $xlsx->road_reestr_group($rid_road);
    }else if($section == 'train'){
        $result = $xlsx->train_reestr_group($rid_road);
      //  $result = 'train _road'; в таком случае когда нет пути к файлу вернет html
    }
    
	if (strlen($result) > 0) {
		$form_data['frelname'] = $result;
		$form_data['success'] = true;
	}
     unset($xlsx);    
}

/*
else if ($part == 'carriage_records_by_self_rid'){    // !!!
    $rid = strval($_POST['rid']);
            
    $result = $db->carriage_getList_by_carriage_rid($rid);
    
    if(count($result) > 0){               
        $form_data['success'] = true;    
        
        $form_data['rid'] = $result['rid'];
        $form_data['obj_nm'] = $result['obj_nm']; // ?? 
        $form_data['mdl'] = $result['mdl'];
        $form_data['date_num_registr'] = $result['date_num_registr'];
        $form_data['docs_at_constr'] = $result['docs_at_constr'];
        $form_data['docs_at_modern'] = $result['docs_at_modern'];
        $form_data['exp_res'] = $result['exp_res'];
        $form_data['recoms_using'] = $result['recoms_using'];
        $form_data['date_act'] = $result['date_act'];
        $form_data['note'] = $result['note'];
        $form_data['org'] = $result['org'];
        $form_data['carr_mdl'] = $result['carr_mdl'];
        $form_data['flg'] = $result['flg'];
    }
}
*/

else if ($part == 'mdl_name_exists_verify'){
    $nm = strval($_POST['nm']);
    
    $result = $db->mdl_nm_verify($nm); // return str - nm
    
    if(count($result) > 0){
        $wagon_records = $db->carriage_getList_by_carriage_rid($result['rid']);
        
        $form_data['obj_nm'] = $wagon_records['obj_nm']; // ?? 
       // $form_data['mdl'] = $wagon_records['mdl'];
        $form_data['date_num_registr'] = $wagon_records['date_num_registr'];
        $form_data['docs_at_constr'] = $wagon_records['docs_at_constr'];
        $form_data['docs_at_modern'] = $wagon_records['docs_at_modern'];
        $form_data['exp_res'] = $wagon_records['exp_res'];
        $form_data['recoms_using'] = $wagon_records['recoms_using'];
        $form_data['date_act'] = $wagon_records['date_act'];
        $form_data['note'] = $wagon_records['note'];
        $form_data['org'] = $wagon_records['org'];
        $form_data['carr_mdl'] = $wagon_records['carr_mdl'];
        $form_data['flg'] = $wagon_records['flg'];
    }
    
    if(count($result) > 0){ 
        $form_data['mdl'] = $result['mdl'];
        $form_data['rid'] = $result['rid'];
        $form_data['success'] = true;
    }else{
        $form_data['mdl'] = '';
        $form_data['rid'] = '';
        $form_data['success'] = true;
    }   
}


// help interface --------------------------------------------
else if ($part == 'hlpc_get_rdat_as_html') {
    $jg = new _jgpktb();
    $jg->jhlp_hlpc_get_rdat_as_html($form_data);   // $form_data pass by reference
    unset($jg);
    
    //$form_data['html']
}
// End of: help interface --------------------------------------------
  


// bbmon interface --------------------------------------------
else if ($part == 'bbmon_active_count') {
    $jg = new _jgpktb();
    $jg->jbbmon_active_count($form_data);   // $form_data pass by reference
    unset($jg);
    
    //$form_data['count']  // -1 - error, 0... - active count
}
else if ($part == 'bbmon_bbrs_add') {
    $jg = new _jgpktb();
    $jg->jbbmon_bbrs_add($form_data);   // $form_data pass by reference
    unset($jg);
    
    //success: $form_data['rid']
    //error:   $form_data['errcode']    // errcodes: 0 - sql error, -1 - empty bbrd, -2 - unknown ip, -3 - pair exists
}
else if ($part == 'bbmon_get_detail') {
    $jg = new _jgpktb();
    $jg->jbbmon_get_detail($form_data);   // $form_data pass by reference
    unset($jg);
    
    //$form_data['html']
}
else if ($part == 'bbmon_reload_body') {
    $jg = new _jgpktb();
    $jg->jbbmon_reload_body($form_data);   // $form_data pass by reference
    unset($jg);
    
    //$form_data['totalrows']
    //$form_data['pagination']
    //$form_data['bbmon_list_body']
}
else if ($part == 'bbra_to_tmp') {
    $jg = new _jgpktb();
    $jg->jbbmon_bbra_to_tmp($form_data);   // $form_data pass by reference
    unset($jg);
    
    //$form_data['frelname']
}
// End of: bbmon interface --------------------------------------------



// fdb interface --------------------------------------------
else if ($part == 'fdb_reload_body') {
    $jg = new _jgpktb();
    $jg->jfdb_fdb_reload_body($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['totalrows']
    //$form_data['pagination']
    //$form_data['fdbr_list_body']
    //$form_data['new_fdbr']
}
else if ($part == 'fdba_add_rec') {
    $jg = new _jgpktb();
    $jg->jfdb_fdba_add_rec($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['rid']
}
else if ($part == 'fdba_view_file') {
    $jg = new _jgpktb();
    $jg->jfdb_fdba_view_file($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['frelname']
}
else if ($part == 'fdbm_add_rec') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbm_add_rec($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['rid']
    // or
    //$form_data['errcode']
}
else if ($part == 'fdbm_get_rec') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbm_get_rec($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['rid']
    //$form_data['fdbr']
    //$form_data['flg']
    //$form_data['ips']
    //$form_data['dtcs']
    //$form_data['txt']
}
else if ($part == 'fdbr_active_count') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbr_active_count($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['fdbr_cnt']
    //$form_data['child_cnt']
}
else if ($part == 'fdbr_add_rec') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbr_add_rec($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['rid']
    //$form_data['rownum']
    // or
    //$form_data['errcode']
}
else if ($part == 'fdbr_cancel') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbr_cancel($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['success'] = true;
}
else if ($part == 'fdbr_get_conversation_icnt') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbr_get_conversation_icnt($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['icnt']
}
else if ($part == 'fdbr_get_detail') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbr_get_detail($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['html']
}
else if ($part == 'fdbr_get_detail_body') {
    $jg = new _jgpktb();
    $jg->jfdb_fdbr_get_detail_body($form_data);     // $form_data pass by reference
    unset($jg);

    //$form_data['html']
}
// End of: fdb interface


unset($ass);
unset($db);

echo json_encode($form_data);   //, JSON_UNESCAPED_UNICODE
?>
