        <div class="modal fade" id="form_for_railwayCarriage" tabindex="-1" role="dialog" aria-labelledby="railwayCarriage_ttl" aria-hidden="true" ondrop="return false;" ondragover="return false;" data-rid="" data-carr_mdl=""
                  data-backdrop="static" data-keyboard="false">   <!-- Prevent close by click outside or by ESC press (else mouseup close form when outside) --> 
            <div class="modal-dialog modal-lg">
                <div class="modal-content y-modal-shadow">
                    <div class="modal-header">
                        <div class="modal-title w-100 p-0">
                            <div class="y-flex-row-nowrap p-0 align-items-center">
                                <h4 id="railwayCarriage_ttl" class="y-dgray-text modal-header-text">ПАСПОРТ ДОСТУПНОСТИ ДЛЯ ПАССАЖИРОВ ИЗ ЧИСЛА ИНВАЛИДОВ МОДЕЛИ ВАГОНА<small><i id="fm_emp_ttl_add" class="y-steel-blue-text"></i></small></h4>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <form onsubmit="return false">
                            <div id="verify_block">
                                <div class="form-row">   
                                    <div class="col-12 m-bottom-5" id="label_for_check_mdl_exist">Заполните поле <q>Модель вагона</q> и нажмите кнопку для дальнейшей работы </div>  
                                    <div class="col-7">
                                        <small><label for="mdl">Модель вагона</label></small>
                                        <input id="mdl" class="form-control" type="text" ondrop="return false;" ondragover="return false;"> <!--onblur="mdl_name_verify(this);" будет вылазтьб везде и не давать редактировать-->                                    
                                    </div>                                
                                    <div class="col-5">
                                        <div class="custom-control">
                                            <p class="m-bottom-5">Нажмите кнопку</p>  
                                            <button id="check_mdl_exist" class="btn btn-info y-shad m-bottom-5" onclick="mdl_name_verify(this);">Проверить модель на балансе в других филиалах</button>
                                        </div>
                                    </div>

                                </div> 
<!--
                                <div id="hiden_div" class="form-group d-none">
                                    <div class='custom-control custom-checkbox'>
                                        <small>
                                            <input id='include_mdl' type='checkbox' class='custom-control-input' onclick="including_mdl(this);">
                                            <label for="include_mdl" class='custom-control-label'>Включить модель в филиал</label>                                           
                                        </small>    
                                    </div>
                                </div> -->
                            </div>
                            <hr>
                               
                            <div class="form-row">                                  
                              <!--   <div class="col">
                                    <small><label for="name_object">Наименование объекта</label></small>
                                    <input id="name_object" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div> -->
                            
                                <div class="col">                             
                                   <small><label for="date_num_passport">Дата / номер регистрации паспорта</label></small>
                                    <input id="date_num_passport" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                            
                          
                            <div class="form-row">                                                                  
                                <div class="col">
                                    <small><label for="docs_constr">Документация, регламентирующая технические требования для перевозки инвалидов, достигнутые при постройке</label></small>
                                    <input id="docs_constr" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col">
                                    <small><label for="docs_modern">Документация, регламентирующая технические требования для перевозки инвалидов, достигнутые при модернизации</label></small>
                                    <input id="docs_modern" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="form-group my_row_group"> 
                                 <small> Оценка доступности моделей вагонов по категориям маломобильных пассажиров <i>по категории <b> "К":</b> </i> </small>
                            </div>   
           
                            
                           <div class="form-row">   
                                    <div class="col" id="row-k">
                                        
                                             <div class='custom-control custom-radio custom-control-inline'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='k-0' type='radio' class='custom-control-input' name="k">
                                                      <label class='custom-control-label' for='k-0'>не определена</label>
                                                  </small>
                                              </div> 

                                              <div class='custom-control custom-radio custom-control-inline'>
                                                  <small><!-- The only way to align box and label vertically -->
                                                      <input id='k-1' type='radio' class='custom-control-input' name="k">
                                                       <label class='custom-control-label' for='k-1'>ДП</label>
                                                  </small>
                                              </div> 

                                             <div class="between"></div>

                                              <div class='custom-control custom-radio custom-control-inline'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='k-2' type='radio' class='custom-control-input' name="k">
                                                      <label class='custom-control-label' for='k-2'>НД</label>
                                                  </small>
                                              </div> 

                                             <div class="between"></div>

                                              <div class='custom-control custom-radio custom-control-inline'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='k-3' type='radio' class='custom-control-input' name="k">
                                                      <label class='custom-control-label' for='k-3'>ДЧ</label>
                                                  </small>
                                              </div> 
                                             
                                             <div class="between"></div>
                                             
                                             
                                    </div>
                            </div> 
                                                       
                            <div class="form-group my_row_group"> 
                                 <small>Оценка доступности моделей вагонов по категориям маломобильных пассажиров <i>по категории <b> "О":</b> </i></small>
                            </div> 
                                    <div class="form-row">    
                                           <div class="col" id="row-k">
                                               
                                              <div class='custom-control custom-radio custom-control-inline'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='o-0' type='radio' class='custom-control-input' name="o">
                                                      <label class='custom-control-label' for='o-0'>не определена</label>
                                                  </small>
                                              </div> 
                                               
                                              <div class='custom-control custom-radio'>
                                                  <small><!-- The only way to align box and label vertically -->
                                                      <input id='o-1' type='radio' class='custom-control-input' name="o">
                                                       <label class='custom-control-label' for='o-1'>ДП</label>
                                                  </small>
                                              </div> 
                                           
                                             <div class="between"></div> 
                                     
                                              <div class='custom-control custom-radio'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='o-2' type='radio' class='custom-control-input' name="o">
                                                      <label class='custom-control-label' for='o-2'>НД</label>
                                                  </small>
                                              </div> 
                                           
                                            <div class="between"></div> 
                                         
                                              <div class='custom-control custom-radio'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='o-3' type='radio' class='custom-control-input' name="o">
                                                      <label class='custom-control-label' for='o-3'>ДЧ</label>
                                                  </small>
                                              </div> 
                                          </div> 
                                    </div> 

                              <div class="form-group my_row_group"> 
                                 <small>Оценка доступности моделей вагонов по категориям маломобильных пассажиров <i>по категории <b> "С":</b> </i></small>
                              </div> 
                                    <div class="form-row">    
                                        <div class="col" id="row-k">
                                            
                                           <div class='custom-control custom-radio custom-control-inline'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='c-0' type='radio' class='custom-control-input' name="c">
                                                      <label class='custom-control-label' for='c-0'>не определена</label>
                                                  </small>
                                            </div>  
                                            
                                          <div class='custom-control custom-radio custom-control-inline'>
                                              <small><!-- The only way to align box and label vertically -->
                                                  <input id='c-1' type='radio' class='custom-control-input' name="c">
                                                   <label class='custom-control-label' for='c-1'>ДП</label>
                                              </small>
                                          </div> 
                                     
                                         <div class="between"></div>
                                        
                                          <div class='custom-control custom-radio custom-control-inline'>
                                              <small> <!-- The only way to align box and label vertically -->
                                                  <input id='c-2' type='radio' class='custom-control-input' name="c">
                                                  <label class='custom-control-label' for='c-2'>НД</label>
                                              </small>
                                          </div> 
                                       
                                         <div class="between"></div>
                                        
                                          <div class='custom-control custom-radio custom-control-inline'>
                                              <small> <!-- The only way to align box and label vertically -->
                                                  <input id='c-3' type='radio' class='custom-control-input' name="c">
                                                  <label class='custom-control-label' for='c-3'>ДЧ</label>
                                              </small>
                                          </div> 
                                        </div>
                                    </div>

                                
                                 <div class="form-group my_row_group"> 
                                      <small>Оценка доступности моделей вагонов по категориям маломобильных пассажиров <i>по категории <b> "Г":</b> </i></small>
                                 </div>
                            
                                    <div class="form-row">    
                                        <div class="col" id="row-k">
                                            
                                             <div class='custom-control custom-radio custom-control-inline'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='g-0' type='radio' class='custom-control-input' name="g">
                                                      <label class='custom-control-label' for='g-0'>не определена</label>
                                                  </small>
                                            </div>  
                                            
                                          <div class='custom-control custom-radio custom-control-inline'>
                                              <small><!-- The only way to align box and label vertically -->
                                                  <input id='g-1' type='radio' class='custom-control-input' name="g">
                                                   <label class='custom-control-label' for='g-1'>ДП</label>
                                              </small>
                                          </div> 
                                       
                                         <div class="between"></div>
                                        
                                          <div class='custom-control custom-radio custom-control-inline'>
                                              <small> <!-- The only way to align box and label vertically -->
                                                  <input id='g-2' type='radio' class='custom-control-input' name="g">
                                                  <label class='custom-control-label' for='g-2'>НД</label>
                                              </small>
                                          </div> 
                                       
                                         <div class="between"></div>
                                       
                                          <div class='custom-control custom-radio custom-control-inline'>
                                              <small> <!-- The only way to align box and label vertically -->
                                                  <input id='g-3' type='radio' class='custom-control-input' name="g">
                                                  <label class='custom-control-label' for='g-3'>ДЧ</label>
                                              </small>
                                          </div> 
                                         
                                       </div> 
                                    </div>
        
                            <hr>
  
                            <div class="form-row">  
                                <div class="col">
                                    <small><label for="expected_result">Ожидаемый результат по состоянию доступности</label></small>
                                    <input id="expected_result" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div> 
                                
                                <div class="col">
                                    <small> <label for="recomendations_using">Рекомендации по использованию объекта</label></small>
                                    <input id="recomendations_using" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div>
                          
                            <hr>
                            
                            
                            <div class="form-row">                              
                                <div class="col">
                                    <small><label for="actual_dt_inf">Дата актуализации информации</label></small>
                                    <input id="actual_dt_inf" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                
                                <div class="col">
                                    <small><label for="note">Примечание</label></small>
                                    <input id="note" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                                                              
                            </div>
                            
                                <div class="form-group">
                                    <div class='custom-control custom-checkbox'>
                                        <small>
                                            <input id='mark' type='checkbox' class='custom-control-input'>
                                            <label for="mark" class='custom-control-label'>**Отметка об участии общественных объединений инвалидов в проведении обследовании и паспортизации</label>                                           
                                        </small>    
                                    </div>
                                </div> 
                        </form>    
                    </div>
                    <div class="modal-footer y-modal-footer-bk">
                        <p id="dlg_err" class="y-modal-err y-err-text y-info-label"></p>
                        <button id="send_form_click" class="btn btn-info y-shad">Отправить</button>
                        <button id="carriage_exit" data-dismiss="modal" class="btn btn-light y-shad">Отменa</button>
                    </div>
                </div>
            </div>
        </div>
