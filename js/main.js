    // global constants
var $$_main_rows_ = 10;         // main lists (clnf) rows per page

var _index = {   // interface to index.js
    is_valid_user:        function ()                { return is_valid_user(); },
    app_who:              function ()                { return app_who(); },
    fill_sideout:         function ()                { fill_sideout(); },
    pg_performGo:         function (pg_id)           { pg_performGo(pg_id); },    // required method (_common.js)
    docget_ok_click:      function (data_sec, data_rid, doc_nm, doc_flg) { docget_ok_click(data_sec, data_rid, doc_nm, doc_flg); },
    glob_fm_before_show:  function ()                { glob_fm_before_show(); },
    glob_fm_after_show:   function ()                { glob_fm_after_show(); },
}

$(function() {      // Shorthand for $(document).ready(function() {
    "use strict";
    
    window.addEventListener("popstate", function() { // back or forward button is clicked
        if ($(".modal.show").length > 0)
            $(".modal.show").modal('hide');
    });

    $(window).resize(function() {
        if ($('#list_rwc_choose_items').length > 0)
            $('#list_rwc_choose_items').css("max-height", (window.innerHeight - $('.sticky-footer').outerHeight(true) - 10) + 'px');
        
        measure_list();
        measure_detail();
        
        if ($("#sideout").hasClass("active"))
            _sout.measure_sideout();
        
            _fdb.fdb_measureBody();
            _bbmon.bbmon_measureBody();
    });
    
    $(document).mouseup(function(e) {
        if ($(".popover.outhide").length > 0)
            $(".popover.outhide").each(function () {
                if (!$(this).is(e.target) && $(this).has(e.target).length === 0) $(this).popover("dispose");
            });
            
            var sideout = $("#sideout");
        
        if (sideout.hasClass("active")) {
            // if the target of the click isn't button or menupanel nor a descendant of this elements
            if (!sideout.is(e.target) && sideout.has(e.target).length === 0)
               _sout.hide_sideout();
        }
            
        _common.close_tooltips();
    });
    
     $("#sideout").swipe( {
        swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
          if (direction == "left")
              _sout.hide_sideout();
        }
    });

    $(window).keydown(function(e){
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 70) { // Ctrl-F
            e.preventDefault();
            srch_btnClick();
        }
        else if (e.keyCode === 8) { // Backspace
            // Backspace in browsers used for 'Back' navigate.
            // See here: https://stackoverflow.com/questions/1495219/how-can-i-prevent-the-backspace-key-from-navigating-back
            var $target = $(e.target||e.srcElement);
            if (!$target.is('input,[contenteditable="true"],textarea'))
                e.preventDefault();
        }
        //else check_keyDown(e);
    });
    
    $.when($.getScript("/pktbbase/js/_common.js") ,
           $.getScript("/pktbbase/js/_fdb.js",
           $.getScript("/pktbbase/js/_help.js"),
           $.getScript("/pktbbase/js/_bbmon.js")),
           $.getScript("/pktbbase/js/_viewer.js"),
           $.getScript("/pktbbase/js/_sout.js"),
           $.getScript("/pktbbase/js/_docget.js")
           
           //$.getScript("/pointsbase/js/_pointscomm.js")
        )
        .done(function () {
            set_app_vars();        // recreate_page here
        });
}); // End of use strict

// user validate section
function is_valid_user()   { 
    return _common.getStoredSessionStr('clnf_ip').length > 0;
}
//function is_valid_user()   { return true; } // TEMPORARILY !!!!!!!!!!!!!!!!!!!!!!!!!!!!

function pg_performGo(pg_id) {  // function is called when pagination link is clicked
    switch (pg_id) {
        case 'fm_fdb': _fdb.fm_fdb_reloadBody(); break;
        case 'fm_bbmon': _bbmon.fm_bbmon_reloadBody(); break;
        default: ;
    }
}
/*
function hide_search() {
    $("#global_search").empty();
}

function show_search() {
    $("#global_search").html(
                "<form class='form-inline' style='padding:5px;'>" +
                    "<div class='input-group'>" +
                        "<div class='input-group-prepend'>" +
                            "<button id='srch_btn' class='btn btn-info' type='button' onclick='srch_btnClick();'>" +
                                "<i class='fas fa-search'></i>" +
                            "</button>" +
                        "</div>" +
                        "<input id='srch_box' type='text' class='form-control' placeholder='Поиск <min 2 символа>...' ondrop='return false;' ondragover='return false;'>" +
                    "</div>" +
                "</form>"
            );

    $('#srch_box')
            .autocomplete({
                serviceUrl: '../pointsbase/php/_cdssearch.php', //rwC,rwD,rwS search
                paramName:  'srch_box',
                autoSelectFirst: true,
                //maxHeight: 350,
                triggerSelectOnValidInput: false,   // block onselect firing on browser activate
                showNoSuggestionNotice: true,
                noSuggestionNotice: 'Совпадений не найдено',
                minChars: 2,
                //lookupLimit: 100,
                params: {  //to pass extra parameter in ajax file.
                    //'clnf_rid': _common.getStoredSessionStr('clnf_rid')
                },
                onSelect: function (suggestion) {
                    select_search_item(suggestion.data.trim()); // suggestion.data: rid, suggestion.value: pname

                    $('#srch_box').val('');
                },
                onSearchStart: function () {
                    $('#srch_box').addClass('srch-in-ajax');
                },
                onSearchComplete: function (query, suggestions) {
                    $('#srch_box').removeClass('srch-in-ajax');
                },
                //onInvalidateSelection: function (suggestion) {
                //},
                onHide: function (container) {   // call only when suggestions found or "No results" was visible. So set showNoSuggestionNotice to true
                },
                beforeRender: function (container, suggestions) {
                    container.find('.autocomplete-suggestion').each(function(i, suggestion){
                        $(suggestion).html($(suggestion).html().replace('{отд}', "<small class='y-llgray-text'>&lt;отд&gt;</small>"));
                    });
                }
            });
}

function srch_btnClick() {
    if ($('#srch_box').length > 0) {
        $('#srch_box').autocomplete().clear();
        $('#srch_box').val('').focus();
    }
}
*/

function reset_app_vars() {    // admin version
   // hide_search();
    
   _common.storeSession('clnf_ip', '');
    _common.storeSession('clnf_flg', 0);

    _common.storeSession('clnf_org', '');
    _common.storeSession('clnf_rid', '');
    
    _common.storeSession('app_ip', '');
    _common.storeSession('fdb_blk', 0);
    
    $('#foot_info').html("<small class='y-llgray-text'>Неизвестный ПК</small>");
}

function set_app_vars() {
    reset_app_vars();
    
    var postForm = {
       'part'  : 'clnf_get_rec_by_ip'
    };

    $.ajax({
        type      : 'POST',
        url       : 'php/jgate.php',
        data      : postForm,
        dataType  : 'json',
        error: function (jqXHR, exception) {
            recreate_page();
        },
        success   : function(data) {    // always success
                    var flg = Number(data.flg); 

                        _common.storeSession('clnf_ip', data.ip);
                        _common.storeSession('clnf_flg', flg);
                        
                        _common.storeSession('clnf_org', data.org); //data.org      370dd604-806a-4406-a907-e69f536e8cb3
                        _common.storeSession('clnf_rid', data.rid);
                        
                        _common.storeSession('app_ip', data.ip);
                   //     _common.storeSession('fdb_blk', data.fdb_blk);

                         $('#foot_info').html("<small class='y-llgray-text'>" + data.ip + "</small>");
                        
                        if (((flg >> 1) & 0x1) == 1){ // flg >> 1) & 0x1, >> означает сдвиг вправо на 1 бит, и если он равен 1, то загрузить страницу
                            recreate_page();
                        }         
        }
    });
}


function recreate_page() {
    if (_common.ends_with(window.location.href, '#modal'))    // in single page apps (user, operator)
        window.history.back();                                // just restore url /index.php

    if (is_valid_user()) {
        $(".page-content").html(
                "<div class='content-list'>" +
                    "<div class='list-title y-flex-row-nowrap align-items-center'>" +
                             "<h6 class='m-0'>&nbsp;<span id='list_ttl_curr_rwc' data-rid=''></span></h6>" +
                          /* "<div id='list_rwc_choose_items' class='dropdown-menu dropdown-menu-right overflow-y-auto'>" +*/
                        "</div>" +
                                
                         "<div class='list-body y-flex-column-nowrap'>" +
                              /// btns_for_user_FPK
                         "</div>" +       
                    "</div>" +                    
                "</div>" +
                
                "<div class='content-detail'>" +
                    "<div class='detail-title m-0 y-flex-row-nowrap y-align-items-center'>" +
                        "<div id='detail_ttl_dots_mnu' class='btn-group y-mrg-r5 p-0'>" + // dropdown container should not be overflow-hidden
                        "</div>" +
                        "<h4 class='m-0 y-navy-text'>&nbsp;<span id='detail_ttl_curr_rws' data-rid=''>&nbsp;</span></h4>" +
                    "</div>" +
                    "<div class='detail-body'>" +
  /*                       '<div id="tmp_for_form" class="y-flex-row-nowrap h-100">' +
                            '<div class="card detail-card detail-dmgn-card y-shad">' +
                                    //+ data.html +  
                            '</div>' +   
                             '<div class="card detail-card detail-comm-card y-shad">' + 
                                  '<div class="card-header y-flex row-nowrap y-align-items-center">something info</div>' 
                            + '</div>'
                        +'</div>'+          */
                    "</div>" + /* end of  detail-body */
                "</div>");
    
        measure_list();
        fill_contentList_4_user_FPK();
       
    }
    else $(".page-content").empty();
}

function show_detail_body(){ // вернет разметку для detail-body

    var currentPg = get_url();

    var postForm = {
        'part': 'get_detail_body',  
        'org' : _common.getStoredSessionStr('clnf_org'),
        'section' : currentPg,
    }
    
    $.ajax({
        type: 'POST',
        url: 'php/jgate.php',
        data: postForm,
        dataType: 'json',
        success: function(data){
            if(data.success){
                $('.detail-body').html(data.content);              
              
              
              /********************************************fdb and bbom ********************************************/
              
                $("#foot_bbmon_root").html("<img src='/pktbbase/img/bbmon_28.png' class='y-cur-point y-mrg-r5'" +
                                                                " onclick='_bbmon.bbmon_start(this);' data-toggle='tooltip' title='Оповещения'>");
                                                        
                _bbmon.badge_showActiveCount();                                        
              
                 $("#foot_fdb_root").html("<img src='/pktbbase/img/feedback_28.png' class='y-cur-point y-mrg-r5'" +
                                                                    " onclick='_fdb.fdb_start(this);' data-toggle='tooltip' title='Обратная связь'>");

                 _fdb.badge_showActiveCount();
              
              
                    $('#show_form').click(function(){
                        if(currentPg == 'carriage'){
                            add_new_registr();
                        //   show_form_Carriage('', '');  // вообще здесь следует вызвать отдельную функцию
                       }else{
                          add_new_train(); 
                       }   
                    });
                    
                    setTimeout(function(){
                            check_filter();
                         if(get_url() == 'carriage'){
                              check_active_passport(); 
                              ta_refreshRows('registr');
                         }else{
                             check_active_train();
                             ta_refreshRows('train');
                         }
                     
                      }, 100);

                     _common.refresh_tooltips(); 
                     
                     measure_detail();
                     
                     
                     $('#new_actions').click(function(){
                         show_form_for_actions('');  
                     });
           
            }
        }
    });
}

function ta_refreshRows(table_id) {

    if ($('#ta_' + table_id).length > 0) {     
        $("[id^='tr_" + table_id + "-']")
            .mouseenter(function() {
                _common.swipe_showMenu($(this));
            })
            .mouseleave(function() {
                _common.swipe_hideMenu();
            });  
    }
    
    _common.refresh_tooltips();
}


function fill_contentList_4_user_FPK (){
    
    var currentPg = get_url();
  
    if(currentPg == ''){
        currentPg = 'carriage';
        history.pushState('', $(document).find("title").text(), window.location.protocol + '//' + window.location.host + window.location.pathname + "?pg="+currentPg);
    }
    
      var postForm = {
        'part': 'show_buttons',
        'type': 'FPK'
      }
    
    $.ajax({
        type: 'POST',
        url: 'php/jgate.php',
        data: postForm,
        dataType: 'json',
        success: function(data){
            if(data.success){
                  $('.list-body').append('<div class="card flt-card y-shad">'
                       + data.btn_for_curriage + 
                       data.btn_for_train + 
                   '</div>');
                   
                   remove_active_class_btn($('.link')); 
                   
                    switch (currentPg) {
                                case 'train': {
                                    $('#show_train').addClass('active-btn');
                                    show_detail_body();
                                    break;   
                                } 
                                default: {
                                    $('#show_carriage').addClass('active-btn');   
                                    show_detail_body();
                                }    
                    }

                   $('#show_carriage').click(function(){
                             history.pushState('', $(document).find("title").text(), window.location.protocol + '//' + window.location.host + window.location.pathname + "?pg=carriage");
                              
                             remove_active_class_btn($('.card-header'));
                             $(this).addClass('active-btn');
                             
                             show_detail_body();
                   });
                   
                    $('#show_train').click(function(){
                            history.pushState('', $(document).find("title").text(), window.location.protocol + '//' + window.location.host + window.location.pathname + "?pg=train");

                            remove_active_class_btn($('.card-header'));
                            $(this).addClass('active-btn');
                            
                            show_detail_body();
                   });                        
            }
        }
    });
}


function show_train_form(rid){
 
    var num_train = '', route = '', adr_formation = '',
        date_num_registr = '', flg = 0, recoms_using = '',    
        exp_res = '', date_act = '', note = '',
        rid_road = '', carr_pasport = '';

    if(rid != ''){
            num_train = $('#tr_train-'+rid).attr('data-num_train'); 
            route = $('#tr_train-'+rid).attr('data-route'); 
            adr_formation = $('#tr_train-'+rid).attr('data-adr_formation'); 
            date_num_registr = $('#tr_train-'+rid).attr('data-date_num_registr'); 
            flg = $('#tr_train-'+rid).attr('data-flg'); 
            recoms_using = $('#tr_train-'+rid).attr('data-recoms_using');       
            exp_res = $('#tr_train-'+rid).attr('data-exp_res');       
            date_act = $('#tr_train-'+rid).attr('data-date_act');     
            note = $('#tr_train-'+rid).attr('data-note');     
            rid_road = $('#tr_train-'+rid).attr('data-rid_road');     
            carr_pasport = $('#tr_train-'+rid).attr('data-carr_pasport');     
    }
    
    var postForm = {
        'part': 'get_fm',
        'fm_id' : 'form_for_train'       
    }
    
    $.ajax({
        type: 'POST',
        url: 'php/jgate.php',
        data: postForm,
        dataType: 'json',
        success: function(data){
            if(data.success){                               
                    $('#div_tmp').html(data.html); 
                    
                        $('#actualization_date').datepicker({
                                format: 'dd.mm.yyyy',
                                autoclose: true,
                                keyboardNavigation: false,
                                language: 'ru',
                                startDate: '01.01.1901'
                        });                            
                    
                        $('#form_for_train').attr('data-rid', rid)
                            .on('show.bs.modal', function(){
        
                            $('#train_ok').unbind('click').on('click', function(){
                                send_train_data();
                            });
                            
                        })
                        .on('shown.bs.modal', function () { 
                            
                                                                                            
                            var presence_spc_carr = (flg & 0x3); // 0x3 - это последние два бита(11), 0x2 - втрой бит(10), 0x1 - первый бит (1)
                            (presence_spc_carr == 1) ? $('#spc_carriage').prop('checked', true) : $('#spc_carriage').prop('checked', false);
                            
                            $('[id^="total-'+((flg >> 2) & 0x3)+'"]').prop('checked', true);
                            
                            var mark_OOI = ((flg >> 4) & 0x3);
                            (mark_OOI == 1) ? $('#mark_OOI').prop('checked', true) : $('#mark_OOI').prop('checked', false);

                            var workers_prc =((flg >> 8) & 0xFF);
                            (workers_prc !== 0) ? $('#quan_spc_workers').val(workers_prc) : $('#quan_spc_workers').val('');
                            
                            var service_prc = ((flg >> 16) & 0xFF);
                            (service_prc !== 0) ? $('#weight_service').val(service_prc) : $('#weight_service').val('');
                            
                            
                            $('#number_train').val(num_train);
                            $('#route').val(route);
                            $('#date_num_passport').val(date_num_registr);
                            $('#firm_adress').val(adr_formation);
                            $('#awaiting_result').val(exp_res);
                            $('#recomendations_for_using').val(recoms_using);
                            $('#note').val(note);
                            
                            if(date_act.length > 0) $('#actualization_date').datepicker('update', _common.date_ymd2DDMMYYYY(date_act, '.'));
                        })
                        .modal('show');
            }
        }
    });
}

function send_train_data(){
    
    var rid = $('#form_for_train').attr('data-rid');
    var num_train =  $('#number_train').val();
    var route = $('#route').val();
    
    var presence_spc_carr = $('#spc_carriage').prop('checked') ? 1 : 0;
    var mark_OOI = $('#mark_OOI').prop('checked') ? 1: 0;
    var workers_prc = $('#quan_spc_workers').val();
    var service_prc = $('#weight_service').val();
    
    var flg = presence_spc_carr | (_common.value_fromElementID($('[id^=\'total-\']:checked').attr('id')) << 2) |
              (mark_OOI << 4) | (workers_prc << 8) | (service_prc << 16);
    
     act_dt = $('#actualization_date').val().split('.').reverse().join('.');
    
    if(num_train.length > 0 && route.length > 0){
        
         var postForm = {
             'part' : 'data_train',
             'rid' : rid,
             'num_train' : num_train,
             'route' : route,
             'adr_formation' : $('#firm_adress').val(),
             'date_num_registr' : $('#date_num_passport').val(),
             'exp_res' : $('#awaiting_result').val(),
             'recoms_using' : $('#recomendations_for_using').val(),
             'date_act' : act_dt,
             'note' : $('#note').val(),       
             'flg' : flg,
             'org': _common.value_fromElementID($('[id^="org_abb-"]').attr('id'))
         };

        $.ajax({
            type: 'POST',
            url: 'php/jgate.php',
            data: postForm,
            dataType: 'json',
            success: function(data){
                if(data.success){
                    
                    $('#form_for_train').modal('hide');

                     _common.storeSession('last_train', data.rid);
                     
                    show_detail_body();
                }  
            }
        });       
    } 
}


function show_form_Carriage(rid, carr_mdl){ 

    var nm = '', 
        mdl = '', 
        dt_num_pass = '', // Дата / номер регистрации паспорта
        docs_constr  = '',
        docs_modern = '',
        flg = 0,
        exp_res = '',
        recoms_using = '',
        act_dt = '',
        note = '';
    
    if(rid.length > 0){
        
                // Катя, здесь ты прочитываешь значения
        nm = $('#td_registr_nm-'+rid).text();
        mdl = $('#td_registr_mdl-'+rid).text();
        dt_num_pass = $('#tr_registr-'+rid).attr('data-dt_nm_reg'); 
        docs_constr = $('#tr_registr-'+rid).attr('data-docs_constr');
        docs_modern = $('#tr_registr-'+rid).attr('data-docs_modern');
        exp_res = $('#tr_registr-'+rid).attr('data-exp_res');
        recoms_using = $('#tr_registr-'+rid).attr('data-recoms_using');
        act_dt = $('#tr_registr-'+rid).attr('data-date_act');
        note = $('#tr_registr-'+rid).attr('data-note');
        flg = $('#tr_registr-'+rid).attr('data-flg');  
    }
   
    var postForm = {
        'part': 'get_fm',
        'fm_id' : 'form_for_railwayCarriage'       
    };
           
    $.ajax({
        type: 'POST',
        url: 'php/jgate.php',
        data: postForm,
        dataType: 'json',
        success: function(data){
            if(data.success){                               
                    $('#div_tmp').html(data.html);
                    $('input').not('#mdl').prop('disabled', true);
                    $('#send_form_click').prop('disabled', true);

                                // ПРОЧИТЫВАТЬ инпуты В shown
                               $('#actual_dt_inf').datepicker({
                                                format: 'dd.mm.yyyy',
                                                autoclose: true,
                                                keyboardNavigation: false,
                                                language: 'ru',
                                                startDate: '01.01.1901'
                                });
             
                    $('#form_for_railwayCarriage').attr('data-rid', rid).attr('data-carr_mdl', carr_mdl) // ????????????
                        .on('show.bs.modal', function(){   
                         
                                    
                        if(rid.length > 0){           
                           $('#verify_block').addClass('d-none');  
                           $('input').prop('disabled', false);     
                           $('#send_form_click').prop('disabled', false);
                        }                                
                            $('#send_form_click').unbind('click').on('click',function() {
                                send_form_data();
                            });   
                      
                        })
                        .on('shown.bs.modal', function () {  
                            
                           document.querySelector('#label_for_check_mdl_exist').innerHTML.toUpperCase();
                    
                            var colors = ['red', 'yellow', 'orange'];
                            var i = 0;
                            window.temerId = setInterval(function(){ 
                                                $('#check_mdl_exist').css({backgroundColor : colors[i]});                               
                                                i++;
                                                if(i == colors.length) i = 0;
                                            }, 1000); 
                           
                                              // оператор & умножает биты, | - складывает их
                                              // & 0x3 - обнуляет все биты кроме 2ч последних, (0x3 - и есть два последних бита), оператор & обнуляет все остальные биты кроме 0x3
                            $('[id^="k-'+(flg & 0x3)+'"]').prop('checked', true);
                            
                         //   console.log($('[id^="k-'+(flg & 0x3)+'"]').prop('checked', true))
                            
                                               // flg >> 2 cместить вправо на 2 бита(чтобы прочесть последние два и не считать их)
                             $('[id^="o-'+((flg >> 2) & 0x3)+'"]').prop('checked', true);
                                             // (flg >> 2) & 0x3 получается 1
                             
                                              // flg >> 4 cместить вправо на 4 бита(чтобы прочесть последние два и не считать их)
                             $('[id="c-'+((flg >> 4) & 0x3)+'"]').prop('checked', true);
                                     // (flg >> 4) & 0x3 получается 2
                             
                             $('[id="g-'+((flg >> 6) & 0x3)+'"]').prop('checked', true);
                                          // (flg >> 6) & 0x3 получается 3
                                          
                             var flm = (flg >> 8) & 0x3; 
                             (flm == 1) ? $('#mark').prop('checked', true) : $('#mark').prop('checked', false);
                                           
                            $('#name_object').val(nm);
                            $('#mdl').val(mdl);
                            $('#date_num_passport').val(dt_num_pass);
                            $('#docs_constr').val(docs_constr);
                            $('#docs_modern').val(docs_modern);
                            $('#expected_result').val(exp_res);
                            $('#recomendations_using').val(recoms_using); 
                            $('#note').val(note);
                            
                            if (act_dt.length > 0) $('#actual_dt_inf').datepicker('update', _common.date_ymd2DDMMYYYY(act_dt, '.'));
                        })
                        .modal('show');
            }
        }
    });
}


function mdl_name_verify(e){
   
   window.clearInterval(window.temerId);
   
   var postForm = {
        'part': 'mdl_name_exists_verify',
        'nm' : $('#mdl').val().trim()       
    };
 
    if(postForm.nm.length > 0){
        $.ajax({
            type: 'POST',
            url: 'php/jgate.php',
            data: postForm,
            dataType: 'json',
            success: function(data){  
                if(data.success){  
                  
                    $('input').prop('disabled', false);
                    $('#send_form_click').prop('disabled', false);
                     
                    if(data.mdl.length === 0) {
                      //  $('input').prop('disabled', false);
                        //$('#hiden_div').addClass('d-none');                                             

                        /*  $('#form_for_railwayCarriage').attr('data-org', data.org); //??*/
                    }else {

                        _common.say_noty_warn("Модель " + $(e).val().trim() + " уже существует");
                       // $('input').not('#mdl').prop('disabled', true);
                       // $('#hiden_div').removeClass('d-none');
                       //$('#include_mdl').prop('disabled', false);
                        
                        $('#form_for_railwayCarriage').attr('data-rid', data.rid);
                        $('#form_for_railwayCarriage').attr('data-carr_mdl', data.carr_mdl)
                        
                        $('input').prop('disabled', false);

                        $('[id^="k-'+(data.flg & 0x3)+'"]').prop('checked', true);
                                            // flg >> 2 cместить вправо на 2 бита(чтобы прочесть последние два и не считать их)
                        $('[id^="o-'+((data.flg >> 2) & 0x3)+'"]').prop('checked', true);
                                          // (flg >> 2) & 0x3 получается 1

                                           // flg >> 4 cместить вправо на 4 бита(чтобы прочесть последние два и не считать их)
                        $('[id="c-'+((data.flg >> 4) & 0x3)+'"]').prop('checked', true);
                                  // (flg >> 4) & 0x3 получается 2

                        $('[id="g-'+((data.flg >> 6) & 0x3)+'"]').prop('checked', true);
                                       // (flg >> 6) & 0x3 получается 3

                        var flm = (data.flg >> 8) & 0x3; 
                        (flm == 1) ? $('#mark').prop('checked', true) : $('#mark').prop('checked', false);

                        $('#date_num_passport').val(data.date_num_registr);
                        $('#docs_constr').val(data.docs_at_constr);
                        $('#docs_modern').val(data.docs_at_modern);
                        $('#expected_result').val(data.exp_res);
                        $('#recomendations_using').val(data.recoms_using); 
                        $('#note').val(data.note);

                        if (data.date_act.length > 0) $('#actual_dt_inf').datepicker('update', _common.date_ymd2DDMMYYYY(data.date_act, '.'));
/*
                         $('#send_form_click').unbind('click').on('click',function() {                         
                                    send_form_data();
                         });  
                       */ 
                    }    
                }
            }
        }); 
    }else _common.say_noty_err("Заполните поле \" Модель вагона\"");
}
/* работала по нажатию на чекбокс
function including_mdl(e){
    
    var postForm = {
        part: 'carriage_records_by_self_rid',
        rid: $('#form_for_railwayCarriage').attr('data-rid')
    };
    
      $.ajax({
            type: 'POST',
            url: 'php/jgate.php',
            data: postForm,
            dataType: 'json',
            success: function(data){
                if(data.success){
                 //   console.log(data);  
                    $('input').prop('disabled', false);

                    $('[id^="k-'+(data.flg & 0x3)+'"]').prop('checked', true);
                                        // flg >> 2 cместить вправо на 2 бита(чтобы прочесть последние два и не считать их)
                    $('[id^="o-'+((data.flg >> 2) & 0x3)+'"]').prop('checked', true);
                                      // (flg >> 2) & 0x3 получается 1

                                       // flg >> 4 cместить вправо на 4 бита(чтобы прочесть последние два и не считать их)
                    $('[id="c-'+((data.flg >> 4) & 0x3)+'"]').prop('checked', true);
                              // (flg >> 4) & 0x3 получается 2

                    $('[id="g-'+((data.flg >> 6) & 0x3)+'"]').prop('checked', true);
                                   // (flg >> 6) & 0x3 получается 3

                    var flm = (data.flg >> 8) & 0x3; 
                    (flm == 1) ? $('#mark').prop('checked', true) : $('#mark').prop('checked', false);

                    $('#date_num_passport').val(data.date_num_registr);
                    $('#docs_constr').val(data.docs_at_constr);
                    $('#docs_modern').val(data.docs_at_modern);
                    $('#expected_result').val(data.exp_res);
                    $('#recomendations_using').val(data.recoms_using); 
                    $('#note').val(data.note);

                    if (data.date_act.length > 0) $('#actual_dt_inf').datepicker('update', _common.date_ymd2DDMMYYYY(data.date_act, '.'));
                    
                     $('#send_form_click').unbind('click').on('click',function() {                         
                                send_form_data();
                     });   
                        
                }
            }
        }); 

}
*/
function send_form_data(){   
    
    // проверить есть ли пара rid
   // var name_object = $('#name_object').val();
    var mdl_nm = $('#mdl').val();
    var rid = $('#form_for_railwayCarriage').attr('data-rid');
    var carr_mdl = $('#form_for_railwayCarriage').attr('data-carr_mdl');
   
    if(mdl_nm.length > 0 ){ //name_object.length > 0 && 
        
    var flm = ($('#mark').prop('checked')) ? 1 : 0;  

    var flg =_common.value_fromElementID($('[id^="k-"]:checked').attr('id')) | 
             (_common.value_fromElementID($('[id^="o-"]:checked').attr('id')) << 2) |  // << 2 - записывает левее последних двух бит, чтобы  не затереть грыппу k
             (_common.value_fromElementID($('[id^="c-"]:checked').attr('id')) << 4) | // << 4 - записывает левее последних 4x бит, чтобы  не затереть грыппу с
             (_common.value_fromElementID($('[id^="g-"]:checked').attr('id')) << 6) |
             (flm << 8);  // << 4 - записывает левее последних 6ти бит, чтобы  не затереть грыппу g
               // $('[id^="g-"]:checked' - выбирает из всей группы, 
               // выбрнный радио 
    var act_dt = $('#actual_dt_inf').val().split('.').reverse().join('.');
        
        var postForm = {
            'part': 'carr_data_send',
            'rid' : rid,
            'nm':  '', //$('[id^="org_abb-"]').text().substr($('[id^="org_abb-"]').text().indexOf('.') + 1),  //name_object,
            'mdl': $('#mdl').val(),
            'dt' : $('#date_num_passport').val(),
            'docs_constr' : $('#docs_constr').val(),
            'docs_modern' : $('#docs_modern').val(),
            'exp_res' : $('#expected_result').val(),
            'recomends_using' :  $('#recomendations_using').val(),
            'act_dt' : act_dt,
            'flg' : flg,
            'note' : $('#note').val(),
            'org' : _common.getStoredSessionStr('clnf_org'),
            'carr_mdl' :  carr_mdl //_common.value_fromElementID($('[id^="td_registr_nm-"].tbl-act-cell').attr('id')) 
        };
    console.log(postForm);
        $.ajax({
            type: 'POST',
            url: 'php/jgate.php',
            data: postForm,
            dataType: 'json',
            success: function(data){

                if(data.success){                               
                     $('#form_for_railwayCarriage').modal('hide');

                     _common.storeSession('last_passport', data.rid);
                     
                     show_detail_body();
                }
            }
        }); 
    
    }else {
        _common.say_noty_err('Поле "Модель вагона" обязательно'); //"Наименование объекта" и
      //  $('#send_form_click').prop('disabled', true);
    }    
}

function add_new_registr(){
    show_form_Carriage('', '');
}


function registr_edit_click(e){    
 //   show_form_Carriage(_common.value_fromElementID($(e).attr('id')), $('#td_registr_mdl-'+_common.value_fromElementID($(e).attr('id'))).text());
                                                                  //$('[id^="td_registr_nm-"].tbl-act-cell').attr('id')) 
 show_form_Carriage(_common.value_fromElementID($(e).attr('id')), $(e).attr('data-carr_mdl'));
} 

function add_new_train(){
    show_train_form('');
}


function train_edit_click(e){
     show_train_form(_common.value_fromElementID($(e).attr('id')));
}

function delete_redistrOrTrain_click(e){
    $(e).tooltip('hide');
    start_ynPopoverForDeleteX($(e));
}

/*
function registr_delete_click(e){
        var from = $(e).attr('id').lastIndexOf('_');
    var to =  $(e).attr('id').indexOf('-');
    console.log($(e).attr('id').slice(from + 1, to));
    $(e).tooltip('hide');
    start_ynPopoverForDeleteX($(e));
}

function train_delete_click(e){
    var from = $(e).attr('id').lastIndexOf('_');
    var to =  $(e).attr('id').indexOf('-');
    console.log($(e).attr('id').slice(from + 1, to));
    $(e).tooltip('hide');
    start_ynPopoverForDeleteX($(e));
}
*/
function start_ynPopoverForDeleteX(jq_elem) {
    var el_id = jq_elem.attr('id'),
            
        rid = _common.value_fromElementID(el_id),
        
        pref = el_id.substring(0, el_id.indexOf('-')),
        part = pref.substring(pref.lastIndexOf('_') + 1);
                                  
    var subj_type = '', subj_name = '';

    switch (part) {
        case 'registr':
            subj_type = 'паспорт доступности';
            subj_name = $('#td_registr_nm-' + rid).text();
            break;
        case 'pp':    
            subj_type = 'меру по адаптации';
            break;
        case 'docs':
             subj_type = 'документ';
             break;
        case 'train':
              subj_type = 'паспорт доступности поезда';
              subj_name = $('#td_train_num-'+rid).text();              
              break;
          case 'passportPdf':   
              subj_type = 'паспорт модели';
              break;
    }
        
    var btn_comm_classes = "btn btn-sm y-mrg-t10 y-mrg-r10 y-mrg-b10 y-shad";
    
    var arrow = jq_elem.closest('.acts-inner').length > 0 ? "" : "<div class='arrow'></div>";
    
    jq_elem.popover({
        delay: { "show": 500, "hide": 100 },
        placement : 'left',
        html : true,
        template: '<div class="popover yn-popover outhide" role="tooltip">' + arrow + 
                    '<h3 class="popover-header"></h3><div class="popover-body"></div></div>',
        content : 
                '<span>Действительно удалить ' + subj_type + ' <i class="y-dred-text">' + subj_name + '</i> ?</span>' +
                "<div class='text-right'>" +
                   "<button id='popover_yes' type='button' class='btn-warning " + btn_comm_classes + "'>Да</button>" +
                   "<button id='popover_no' type='button' class='btn-light " + btn_comm_classes + "'>Нет</button>" +
               "</div>"
    }).popover('show');

    $('.popover-header').text('Требуется подтверждение');
    
    $('#popover_yes').unbind('click').on('click',function() {
        $('.yn-popover').popover('dispose');

        switch (part) {
            case 'registr':
                perform_delete_registr(rid);
                break;
            case 'pp' :
                perform_delete_action(rid);
                break;
            case 'docs' :
                perform_delete_doc(rid);
                break;
            case 'train':
                perform_delete_train(rid);
            case 'passportPdf':
                perform_delete_pass_pdf(rid);                
        }
    });

    $('#popover_no').unbind('click').on('click',function() {
        $('.yn-popover').popover('dispose');
    });
}

function perform_delete_registr(rid){
 //  
   var post_form = {
        'part' : 'del_registr',
        'rid' : rid,
        'org' : _common.value_fromElementID($('[id^="org_abb-"]').attr('id'))
    };
    
   // console.log(post_form);
       
    $.ajax({
        url: 'php/jgate.php',
        type: 'POST',
        dataType: 'json',
        data: post_form,
        success: function(data){
            console.log(data);  
            if(data.success){   
             
                _common.say_noty_ok("Запись удалена");                    
                         show_detail_body();              
            }
        }
    });      
}



function perform_delete_train(rid){
    
   var post_form = {
        'part' : 'del_train',
        'rid' : rid
    }

    $.ajax({
        url: 'php/jgate.php',
        type: 'POST',
        dataType: 'json',
        data: post_form,
        success: function(data){
            if(data.success){        
                
                _common.say_noty_ok("Запись удалена");                    
                         show_detail_body();              
            }
        }
    });      
}



function clear_page() {
    $('.list-body').empty();
    
    clear_detail();
}

function clear_detail() {
    $('#detail_ttl_dots_mnu').empty();
    $('#detail_ttl_curr_rws').empty();
    $('.detail-body').empty();
}

function select_search_item(tbl_rid) {  // ex: rws:879876876....
    var rid = tbl_rid.substring(4);

    switch (tbl_rid.substring(0, 3)) {
        case 'rws': select_rws(rid, true); break;
        case 'rwd': select_rwd(rid); break;
        case 'rwc': select_rwc(rid); break;
    }
}

function passport_click(e) {   
    _common.stop_propagation(e);
    
    $(e).tooltip('hide');
    
    var rid = _common.value_fromElementID($(e).attr('id'));
         
    select_active_passport(rid);
    _common.storeSession('last_passport', rid);
}

function select_active_passport(rid){
           
    $("[id^='td_registr_']").removeClass('tbl-act-cell');

    $('#td_registr_nm-' + rid).addClass('tbl-act-cell');
    $('#td_registr_mdl-' + rid).addClass('tbl-act-cell');
    
    show_actions_for_active_registr(rid);
    
     $('#add_new_doc').unbind('click').on('click',function() {
                add_new_document(rid);
     });     
     
   //  var carr_mdl = _common.value_fromElementID($('#tr_registr-'+rid).attr('id')), //$('#tr_registr-'+rid).attr('data-carr_mdl') для поиска по carr_mdl
       //  main_org = _common.value_fromElementID($('[id^=main_org-]').attr('id'));
     
     $('#add_new_passport_pdf').unbind('click').on('click',function() {         
                add_new_passportPDF(rid);
     });
             
    get_docs_part(rid);
    get_pass_pdf_part(rid); //main_org, 
}


function check_active_passport(){ // красит активные организацци, или первую
       
   var cur_passport =  _common.getStoredSessionStr('last_passport');
   
   if(cur_passport.length == 0 || $('#tr_registr-'+cur_passport).length == 0) {
       if($('[id^="tr_registr-"]').length > 0) {
           _common.storeSession('last_passport', _common.value_fromElementID($('[id^="tr_registr-"]').first().attr('id')) );
              select_active_passport(_common.getStoredSessionStr('last_passport'));                    
       }
   }else{
             select_active_passport(cur_passport);
   } 
}

function train_click(e){
    _common.stop_propagation(e);
    
    $(e).tooltip('hide');
    var rid = _common.value_fromElementID($(e).attr('id'));
    select_active_train(rid);
    
    //show_actions_for_active_registr(rid);
    
    _common.storeSession('last_train', rid);
}

function select_active_train(rid){        
    $("[id^='td_train_num-']").removeClass('tbl-act-cell');
    $("[id^='td_train_route-']").removeClass('tbl-act-cell');
    
    $('#td_train_num-' + rid).addClass('tbl-act-cell'); 
    $('#td_train_route-' + rid).addClass('tbl-act-cell');
    
    show_actions_for_active_registr(rid);
    
    $('#add_new_doc').unbind('click').on('click',function() {
        add_new_document(rid);
    });
    
    get_docs_part(rid);
}


function check_active_train(){ 
    
   var cur_train =  _common.getStoredSessionStr('last_train');
   
   if(cur_train.length == 0 || $('#td_train_num-'+cur_train).length == 0) {  
       if($('[id^="td_train_num-"]').length > 0) {
           
           cur_train = _common.value_fromElementID($('[id^="td_train_num-"]').first().attr('id'));
           
           _common.storeSession('last_train', cur_train);  
           select_active_train(cur_train);  
       
       }
   }else{
            
            select_active_train(cur_train);      
   } 
}

function add_new_document(docs_pid){
    
    var tbl = get_url() == 'carriage' ? 'carr_pasport' : 'train';
    var flg = get_url() == 'carriage' ? 1 : 2;
   
    _common.close_dropdowns();
// function (data_sec, data_rid, valid_exts, name_length, flag, div_x)
    _docget.fm_get_doc_startForm(tbl, docs_pid, ".pdf.doc.docx.xls.xlsx.rtf.txt.", 0, flg); // 0 name length, 1 - flag
}

function add_new_passportPDF(carr_mdl_rid){ 
    var tbl = 'carr_pasport'; 
    var flg = 3;
   
    _common.close_dropdowns();

    _docget.fm_get_doc_startForm(tbl, carr_mdl_rid, ".pdf.doc.docx.xls.xlsx.rtf.txt.", 0, flg); // 0 name length, 1 - flag
    // fm_get_doc_startForm вызывается в _docget, и в свою очередь вызывает  docget_ok_click
}


function glob_fm_before_show() {
       $("#div_ta_registr").css({ 'overflow-y': 'hidden' });  // IE can show scrollbar over modal
}

function glob_fm_after_show() {
            $("#div_ta_registr").css({ 'overflow-y': 'auto' });
}

/**********************************actions***************************************************/


// она пока общая для поездов и вагонов
function show_actions_for_active_registr(rid){ // покажет меры по адаптации
    
      var url = get_url();
   
      if(rid.length > 0){
          var postForm = {
                  'part': 'get_actions',
                  'rid' : rid,
                  'section' : url
              };
                
          $.ajax({
               type      : 'POST',
               url       : 'php/jgate.php',
               data      : postForm,
               dataType  : 'json',
               success: function(data){
                   if(data.success){
                      $('#actions_div').empty().html(data.actions);                   
                      measure_detail();
                   }
               }
          });    
      }
}

function show_form_for_actions(rid){

    var twa = '', pnv ='' , flg = 0;

    //! remake
    if(rid.length > 0){
        var elem = $('#action_card-'+rid);
        flg = elem.attr('data-flg');
        twa = $('#twa-'+rid).text();
        pnv = $('#pnv-'+rid).text();
    }
    
     var postForm = {
        'part': 'get_fm',
        'fm_id' : 'form_works_adaptation',
    };
    
              
    $.ajax({
        type: 'POST',
        url: 'php/jgate.php',
        data: postForm,
        dataType: 'json',
        success: function(data){
            if(data.success){               
                
                    $('#div_tmp').html(data.html);                    
                    
                    $('#form_works_adaptation').attr('data-rid', rid).attr('data-flg', flg)
                        .on('show.bs.modal', function(){ // show - подготовка к показу
                            
                            var pp = flg & 0xFFFFFF; //все 24 бита под эту группу, F = 4 бита(1111) 
                    
                            if(pp > 0){
                                var pp2 = flg & 0xFFFF; // год и квартал вместе (16 бит)
                                   if(pp2 > 0){
                                       var year = pp2 & 0xFFF; // (на year 12 бит)
                                       var quarter = (pp2 >> 12) & 0xF;       // (на quarter 4 бита) 
                                       if(year > 2000 && year < 2070 && quarter >= 0 && quarter < 5){
                                           $('#pp-2').prop('checked', true); 
                                           $('#pp_year').val(year);
                                           $('#pp_quarter').val(quarter);
                                       }
                                   }else{
                                       var pp1 = (flg >> 16) & 0xFF;  // (0xFF) 8 бит на селект, они с 16 по 23й бит
                                        $('#pp-1').prop('checked', true); 
                                        $('#pp_select').val(pp1);
                                   }
                            }
                            if($('[id^="pp-"]:checked').length == 0){
                                $('#pp-0').prop('checked', true); 
                            }
                            
                                   var mark = (flg >> 24) & 0x3 //( 0x3 обнуляет левую часть, все что после 25 бита (этот флаг с 24 по 25 бит)) 0x3 это два бита
                                   $('#mark-'+mark).prop('checked', true);
        
                            $('#pnv').val(pnv);
                                          
                            $('#add_action_adaptation').click(function(){ 
                                send_adaptation_data();
                            });
                            
                            console.log(twa, pnv);
                        })
                        .on('shown.bs.modal', function () { // shown - форма уже на экране
                    
                              $('#twa').focus().val(twa);
                        }) 
                        .on('hidden.bs.modal', function () { // shown - форма уже на экране                   
                             $('#div_tmp').empty();        
                        })
                        .modal('show');
            }
        }
    });
}

function edit_action_record(e){
  show_form_for_actions(_common.value_fromElementID($(e).attr('id')));
}

 
function send_adaptation_data(){    
   
    var flg = $('#form_works_adaptation').attr('data-flg') & 0xFC000000; // обнуляет все 26 бит
 
    switch (Number(_common.value_fromElementID($('[id^="pp-"]:checked').attr('id')))){
        case 1 :
            flg |= Number($('#pp_select').val()) << 16; 
              break;
        case 2 : {
             var year = $('#pp_year').val();
             var quarter = $('#pp_quarter').val();   
             
             year = !isNaN(year) && year > 2000 && year < 2070  ? year : 0;
             
             if(year > 0){
                 quarter = !isNaN(quarter) && quarter >= 0 && quarter < 5  ? quarter : 0;
                 flg |= (quarter << 12) | year; 
             }               
        } 
             break;             
    }
     
    flg |= _common.value_fromElementID($('[id^="mark-"]:checked').attr('id')) << 24;
     
   
    if($('#twa').val().length > 0){
        
        var section = get_url();
        var carr_pasport = '';
        
         if(section == 'carriage'){
              carr_pasport = _common.value_fromElementID($('[id^="tr_registr-"] .tbl-act-cell').attr('id'));
         }else{
             carr_pasport = _common.value_fromElementID($('[id^="tr_train-"] .tbl-act-cell').attr('id'));
         }
          
        
        var postForm = {
            part: 'check_action_record',
            section : section,
            rid: $('#form_works_adaptation').attr('data-rid'),
            twa: $('#twa').val(),
            pnv: $('#pnv').val(),
            flg : flg,
            carr_pasport : carr_pasport,
        };
        
        $.ajax({
            url: 'php/jgate.php',
            data: postForm,
            dataType: 'json',
            type: 'POST',
            success: function(data){
                $('#form_works_adaptation').modal('hide');
                
               show_detail_body();
            },
        });
    }
}

function delete_action_record(e){
    $(e).tooltip('hide');
    start_ynPopoverForDeleteX($(e));
}

function perform_delete_action(rid){
    
    var section = get_url();

    var postForm = {
        part : 'del_action',
        rid : rid,
        section: section
                
    };
    
    $.ajax({
        url: 'php/jgate.php',
        type: 'POST',
        dataType: 'json',
        data : postForm,
        success: function(data){
            if(data.success){
                if(section == 'carriage'){
                     show_actions_for_active_registr(_common.value_fromElementID($('[id^="tr_registr-"] .tbl-act-cell').attr('id'))); // запись в sessinStorage в passport_click
                }else if(section == 'train'){
                     show_actions_for_active_registr(_common.value_fromElementID($('[id^="tr_train-"] .tbl-act-cell').attr('id')));
                }
            }
        }
    });
}

//*********************************************************documentation pasport pdf ***********************************/

function get_docs_part(pid){
    
    if(pid.length > 0){
        
        var postForm = {
            part : 'docs_part',
            pid : pid,
        };
        $.ajax({
                url: 'php/jgate.php',
                type: 'POST',
                dataType: 'json',
                data : postForm,
                success: function(data){
                    if(data.success){
                     
                        $('#docs_tmp').empty().append(data.html);
                         _common.refresh_tooltips(); 
                    }
                }
        });
      
    }
}

function get_pass_pdf_part(pid){ //main_org, 
    if(pid.length > 0){
        
        var postForm = {
            'part' : 'pass_pdf_part',
            'carr_mdl_rid' : pid
          //  'main_org' : main_org,
        };
        $.ajax({
                url: 'php/jgate.php',
                type: 'POST',
                dataType: 'json',
                data : postForm,
                success: function(data){
                    if(data.success){  
                       
                        $('#passport_tmp').empty().append(data.html);
                         _common.refresh_tooltips(); 
                    }
                }
        });
      
    } 
}


// docget_ok_click вызывается в _docget.js!!! по клику на кнопке ОК в форме
// _index.docget_ok_click(data_sec, data_rid, $('#get_doc_name_group').hasClass('d-none') ? "" : $.trim($("#doc_name").val()), flag_);

function docget_ok_click(data_tbl, rid_carr_pass, doc_nm, doc_flg){ 

    if (window.docget_file_up != null && rid_carr_pass.length > 0) {
        
     var reader = new FileReader();
        reader.onload = function(e) {              
        
            var postForm = {
                'part' : 'docs_add_rec',
                'tbl'  : data_tbl,
                'pid'  : rid_carr_pass,
                'fnm'  : window.docget_file_up.name,
                'nm'   : doc_nm,
                'doc_flg' : doc_flg,
                'rdat' : reader.result
              //  'main_org' : _common.value_fromElementID($('[id^="main_org-"]').attr('id'))
            };

            $.ajax({
                type      : 'POST',
                url       : 'php/jgate.php',
                data      : postForm,
                dataType  : 'json',
                beforeSend: function() {
                    $(".y-ajax-wait").css('visibility', 'visible');
                },
                complete: function() {
                    $(".y-ajax-wait").css('visibility', 'hidden');
                },
                success   : function(data) {
                console.log(data);
                                if (data.success) {
                                   //_index.ofl_reloadDocSec(rid_carr_pass);
                                   _common.say_noty_ok("Документ загружен");
                                }
                                else _common.say_noty_err("Ошибка загрузки документа");
                                
                                show_detail_body();
                }
            });
        }
        reader.readAsDataURL(window.docget_file_up);
     }
}


function delete_badge(e){
    
    $(e).tooltip('hide');
    start_ynPopoverForDeleteX($(e));
}

function delete_passport_pdf_badge(e){  
    $(e).tooltip('hide');
    start_ynPopoverForDeleteX($(e));
}

function perform_delete_doc (rid){
   
   if(rid.length > 0){
           
       var postForm = {
           'part' : 'delete_doc',
           'rid' : rid,
       };
       
       $.ajax({
            type      : 'POST',
            url       : 'php/jgate.php',
            data      : postForm,
            dataType  : 'json',
            success: function(data){
                if(data.success){
                    console.log(data.rid);
                    get_docs_part(_common.getStoredSessionStr('last_passport'));
                }
            }
       });
   }
}

function perform_delete_pass_pdf (rid){
   
   if(rid.length > 0){
    //   var main_org = _common.value_fromElementID($('[id^="main_org-"]').attr('id'));
       
       var postForm = {
           'part' : 'delete_pass_pdf',
           'rid' : rid,
       };
       
       $.ajax({
            type      : 'POST',
            url       : 'php/jgate.php',
            data      : postForm,
            dataType  : 'json',
            success: function(data){
                if(data.success){
                  //  $('[id^="td_registr_nm-"].tbl-act-cell').parent().attr('data-carr_mdl') - для удаления по carr_mdl (для версии "ВСЕХ моделей")
                    get_pass_pdf_part(_common.value_fromElementID($('[id^="td_registr_nm-"].tbl-act-cell').attr('id'))); //main_org,
                }
            }
       });
   }
}

function doc_view_click(e){

    _common.close_dropdowns();
    _common.stop_propagation(e);
    
    $(e).tooltip('hide');
    
    _viewer.viewer_view("file_put_tmp", "rid", $(e).attr('data-doc'), false);
}

function pass_pdf_view_click(e){
    console.log($(e).attr('data-doc'))
    
    _common.close_dropdowns();
    _common.stop_propagation(e);
    
    $(e).tooltip('hide');
    
    _viewer.viewer_view("file_put_tmp_pdf", "rid", $(e).attr('data-doc'), false);
}


/************************************** excel reestr ********************************************/

/*
function get_form_for_reestr(){
    var rid = _common.value_fromElementID($('.tbl-act-cell').attr('id'));
    var section = get_url();
    
    if(rid.length > 0){
        
        var postForm = {
            'part' : 'rep_carriage',
            'rid_mdl' : rid,
            'section' : section
        };
        
        $.ajax({
            type      : 'POST',
            url       : 'php/jgate.php',
            data      : postForm,
            dataType  : 'json',
            beforeSend: function() {
                  $(".y-ajax-wait").css('visibility', 'visible');
            },
           complete: function() {
                 $(".y-ajax-wait").css('visibility', 'hidden');
            }, 
            success: function(data){
                  if(data.success){
                       _viewer.viewer_viewFile(data.frelname, false);  //false: newwin                     
                  }
            },
        });
    }
}
*/

function get_oneRecord_reestr(){
    var url = get_url();
    var elem = '';
    var road_rid = _common.value_fromElementID($('[id^="org_abb-"]').attr('id'));
    
    (url == 'carriage') ? elem = $('[id^="tr_registr-"]').find('.tbl-act-cell') : elem = $('[id^="tr_train-"]').find('.tbl-act-cell'); 
    var rid = _common.value_fromElementID(elem.attr('id'));

    if(rid.length > 0){
       
        var postForm = {
            'part' : 'rep_carriage',
            'rid_mdl' : rid,
            'section' : url,
            'ttl' : road_rid
        };

        $.ajax({
            type      : 'POST',
            url       : 'php/jgate.php',
            data      : postForm,
            dataType  : 'json',
           beforeSend: function() {
                  $(".y-ajax-wait").css('visibility', 'visible');
            },
           complete: function() {
                 $(".y-ajax-wait").css('visibility', 'hidden');
            }, 
            success: function(data){
                  if(data.success){
                      _viewer.viewer_viewFile(data.frelname, false);
                       
                  }
            },
        });
    } 
}

function show_group_excel(){
    var url = get_url();
    var rid_road = _common.value_fromElementID($('[id^=org_abb-]').attr('id'));
    
    var postForm = {
            'part' : 'road_reestr',
            'rid_road' : rid_road,
            'section' : url
        };
    
      $.ajax({
            type      : 'POST',
            url       : 'php/jgate.php',
            data      : postForm,
            dataType  : 'json',
	beforeSend: function() {
                  $(".y-ajax-wait").css('visibility', 'visible');
            },
           complete: function() {
                 $(".y-ajax-wait").css('visibility', 'hidden');
            }, 
            success: function(data){			
                if(data.success){
                      _viewer.viewer_viewFile(data.frelname, false);
                }
            }
      });
}

function get_reestr (){
     $('#form_for_reestr').modal('hide');
}

function show_sideout() {
    _sout.prepare_sideout();
    
    setTimeout(function(){
        $("#sideout").addClass("active");
    }, 150);
}

function fill_sideout() {
}

function app_who() {
    return  'ДМГН ПС Оператор';
}

function get_url(){
    var currentPg = $.url().param('pg');
    return typeof(currentPg) === 'string' ? currentPg : '';     
}

function remove_active_class_btn(arr){
     arr.each(function(){
            $(this).removeClass('active-btn');      
     });
}

function measure_list() {
    if ($(".list-title").length > 0) {
        var list_title_b = $(".list-title").offset().top + $(".list-title").outerHeight(true),

            list_content_b = $(".content-list").offset().top + $(".content-list").outerHeight(false);    // without margins

        var card_marg_h = $(".flt-card").outerHeight(true) - $(".flt-card").outerHeight(false);

        $('.list-body').css('height', (list_content_b - list_title_b - card_marg_h / 2) + 'px');
    }
}

function measure_detail() {
    if ($(".list-title").length > 0 && $(".detail-title").length > 0)
        $('.detail-title').css('height', $(".list-title").outerHeight(true) + 'px');
    
    var detail_title_b = $(".detail-title").length > 0 ? $(".detail-title").offset().top + $(".list-title").outerHeight(true) : 0,

        detail_content_b = $(".content-detail").offset().top + $(".content-detail").outerHeight(false);    // without margins

    $('.detail-body').css('height', (detail_content_b - detail_title_b) + 'px');
    
    $('.detail-comm-card').css('height', ($('.detail-body').innerHeight() - 20) + 'px');
    
    if ($("#registr_div").length > 0 && $(".detail-dmgn-card").length > 0 && $("#center_part_header").length > 0)
          $('#registr_div').css('height', ($('.detail-dmgn-card').outerHeight() - $('#center_part_header').outerHeight()) + 'px');
      
    //  $('#actions_div').css('height', ($('.detail-comm-card').outerHeight() - $('.detail-comm-card>card-header').outerHeight()) + 'px');
      
      $('#actions_div').css('height', ($('.detail-comm-card').outerHeight() - $('.detail-comm-card>.card-header').outerHeight()) + 'px');
}

function check_filter(){
    // усли есть хоть одна строка для поезда или модедт вагона, то кнопки активны
        if($('[id^="tr_registr"]').length != 0 || $('[id^="tr_train"]').length != 0){
            $('#show_reestr').removeClass('disabled');
            $('#show_group_reestr').removeClass('disabled');
        }else{
            // или же кнопки заблокированы
            $('#show_reestr').addClass('disabled');
            $('#show_group_reestr').addClass('disabled');
        }    
}

