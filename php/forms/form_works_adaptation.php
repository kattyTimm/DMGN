        <div class="modal fade" id="form_works_adaptation" tabindex="-1" role="dialog" data-rid="" data-flg="" data-sec="" aria-hidden="true" ondrop="return false;" ondragover="return false;"
                    data-backdrop="static" data-keyboard="false">   <!-- Prevent close by click outside or by ESC press (else mouseup close form when outside) -->  
            
              <div class="modal-dialog modal-md">
                <div class="modal-content y-modal-shadow">
                    <div class="modal-header">
                        <div class="modal-title w-100 p-0">
                            <div class="y-flex-row-nowrap p-0 align-items-center">
                                <h4 id="fm_carr_ttl" class="y-dgray-text modal-header-text">Меры по адаптации<small><i id="fm_carr_ttl_add" class="y-steel-blue-text"></i></small></h4>                      
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <form onsubmit="return false">

                            
                            <div class="form-row">
                                <div class="col">
                                    <small><label for="twa">Виды работ по адаптации</label></small>
                                    <textarea id="twa" class="form-control" rows="3"></textarea>
                                </div>                                                                 
                            </div>
                            
                            <hr>
                            
                            <div class="form-group my_row_group"> 
                                 <small>Плановый период (срок) исполнения</small>
                            </div> 
                            
                            
                            <div class="form-group my_row_group"> 
                                       <div class='custom-control custom-radio custom-control-inline'>
                                            <small> <!-- The only way to align box and label vertically -->
                                                <input id='pp-0' type='radio' class='custom-control-input' name="planning_period">
                                                <label class='custom-control-label y-pad-t2' for='pp-0'>Не определен</label>
                                            </small>    
                                       </div> 
                                <p></p>
                            </div> 
                            
                            <div class="form-row">   
                                    <div class="col-md-3" id="row-k" style='padding-top:5px;'>
                                              <div class='custom-control custom-radio custom-control-inline'>
                                                  <small><!-- The only way to align box and label vertically -->
                                                      <input id='pp-1' type='radio' class='custom-control-input' name="planning_period">
                                                       <label class='custom-control-label y-pad-t2' for='pp-1'>По условию</label>
                                                  </small>
                                              </div>   
                                    </div>    
                                        
                                    <div class="col" id="row-k">    
                                                 <select class='custom-select d-inline-block m-0' value='0' id="pp_select">
                                                    {ROWS}
                                                  </select>                                                  
                                    </div> 
                            </div>  
                            
                            <div class="form-row">   
                                    <div class="col-md-3" id="row-k" style='padding-top:5px;'>                                           
                                              <div class='custom-control custom-radio custom-control-inline'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='pp-2' type='radio' class='custom-control-input' name="planning_period">
                                                      <label class='custom-control-label y-pad-t2' for='pp-2'>Год/квартал</label>
                                                  </small>
                                              </div>   
                                    </div>
                                    
                                   <div class="col-md-5" id="row-k">    
                                            <small> <!-- The only way to align box and label vertically -->
                                                    <input id='pp_year' type='text' class="form-control">
                                                    <label class='' for='pp_year'>Год</label>
                                            </small> 
                                    </div>
                                
                                    <div class="col-md-4" id="row-k"> 
                                             <small> <!-- The only way to align box and label vertically -->
                                                    <input id='pp_quarter' type='text' class="form-control">
                                                    <label class='' for='pp_quarter'>Квартал</label>
                                            </small> 
                                    </div>
                                 
                            </div>  
                            
                            <hr>
                            
                             <div class="form-group my_row_group"> 
                                 <small>Отметка о выполнении работ по адаптации</small>
                            </div> 
                            <br>
                            <div class="form-row">   
                                    <div class="col" id="row-k">
                                        
                                             <div class='custom-control custom-radio custom-control-inline'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='mark-0' type='radio' class='custom-control-input' name="exe_mark">
                                                      <label class='custom-control-label' for='mark-0'>Не определены</label>
                                                  </small>
                                              </div> 
                                              
                                              <div class='custom-control custom-radio custom-control-inline'>
                                                  <small><!-- The only way to align box and label vertically -->
                                                      <input id='mark-1' type='radio' class='custom-control-input' name="exe_mark">
                                                      <label class='custom-control-label' for='mark-1'>Выполнены</label>
                                                  </small>
                                              </div> 

                                             <div class="between"></div>

                                              <div class='custom-control custom-radio custom-control-inline'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='mark-2' type='radio' class='custom-control-input' name="exe_mark">
                                                      <label class='custom-control-label' for='mark-2'>Не выполнены</label>
                                                  </small>
                                              </div>   
                                    </div>
                            </div>                             
                            
                            <hr>
                            
                            <div class="form-row">                              
                                 <div class="col">
                                    <small><label for="pnv">Причины невыполнения</label></small>
                                    <textarea id="pnv" class="form-control" rows="2"></textarea>
                                </div>                                   
                            </div> 

                        </form>    
                    </div>
                    <div class="modal-footer y-modal-footer-bk">
                        <p id="dlg_err" class="y-modal-err y-err-text y-info-label"></p>
                        <button id="add_action_adaptation" class="btn btn-info y-shad">Добавить</button>
                        <button id="exit_acts_adap" data-dismiss="modal" class="btn btn-light y-shad">Отмена</button>
                    </div>
                </div>
            </div>
        </div>
