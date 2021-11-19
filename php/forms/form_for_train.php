        <div class="modal fade" id="form_for_train" tabindex="-1" role="dialog" aria-labelledby="railwayCarriage_ttl" aria-hidden="true" ondrop="return false;" ondragover="return false;" data-rid=""
             data-backdrop="static" data-keyboard="false">   <!-- Prevent close by click outside or by ESC press (else mouseup close form when outside) --> 
            
            <div class="modal-dialog modal-lg">
                <div class="modal-content y-modal-shadow">
                    
                    <div class="modal-header">
                        <div class="modal-title w-100 p-0">
                            <div class="y-flex-row-nowrap p-0 align-items-center">
                                <h4 id="fm_train_ttl" class="y-dgray-text modal-header-text">ПАСПОРТ ДОСТУПНОСТИ ПАССАЖИРСКИХ ПОЕЗДОВ<small><i id="fm_emp_ttl_add" class="y-steel-blue-text"></i></small></h4>                      
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-body">
                        <form onsubmit="return false">
                            <div class="form-row">                              
                                <div class="col">
                                    <small><label for="number_train">Номер поезда</label></small>
                                    <input id="number_train" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col">
                                    <small><label for="route">Маршрут поезда</label></small>
                                    <input id="route" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                
                                <div class="col">
                                    <!-- <p id="date_num_passport_downer"></p> -->                              
                                   <small><label for="date_num_passport">Дата и номер регистрации паспорта</label></small>
                                    <input id="date_num_passport" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                            </div> 
                            
                              <div class="form-group">
                                    <!-- <p id="date_num_passport_downer"></p> -->                              
                                   <small><label for="firm_adress">Адрес преприятия формирования поезда</label></small>
                                    <input id="firm_adress" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                              </div>
                            
                                <hr>
                                
                                <div class="form-row">
                                    
                                    <div class='custom-control custom-checkbox'>
                                        <small>
                                            <input id='spc_carriage' type='checkbox' class='custom-control-input'>
                                            <label for="spc_carriage" class='custom-control-label'>Наличие в составе не менее 1 вагона для инвалидов</label>                                           
                                        </small>    
                                   
                                    </div>     
                                </div>  
                                
                            <div class="block-separator"></div>
                                
                            <div class="form-row"> 
                                 <small>Итоговая оценка доступности пассажирского поезда</small>
                            </div>     
                                
                                <div class="form-row">  
                                  <div class="col" id="row-k">
                                      
                                              <div class='custom-control custom-radio custom-control-inline'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='total-0' type='radio' class='custom-control-input' name="total">
                                                      <label class='custom-control-label' for='total-0'>не определена</label>
                                                  </small>
                                              </div> 
                                      
                                              <div class='custom-control custom-radio custom-control-inline'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='total-1' type='radio' class='custom-control-input' name="total">
                                                      <label class='custom-control-label' for='total-1'>ДП</label>
                                                  </small>
                                              </div> 
                                      
                                             <div class='custom-control custom-radio custom-control-inline'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='total-2' type='radio' class='custom-control-input' name="total">
                                                      <label class='custom-control-label' for='total-2'>ДЧ</label>
                                                  </small>
                                              </div> 
                                      
                                      
                                              <div class='custom-control custom-radio custom-control-inline'>
                                                  <small> <!-- The only way to align box and label vertically -->
                                                      <input id='total-3' type='radio' class='custom-control-input' name="total">
                                                      <label class='custom-control-label' for='total-3'>НД</label>
                                                  </small>
                                             </div>                                             
                                  </div>  
                               </div>
                                
                                <hr>
                                
                            <div class="form-row">                              
                                
                                <div class="col">                                                                         
                                   <small><label for="quan_spc_workers">Процент работников, для обслуживания инвалидов</label>
                                   </small>                                 
                                    <input id="quan_spc_workers" class="form-control" type="number" ondrop="return false;" ondragover="return false;">
                                </div>
                                
                                <div class="col">                            
                                   <small><label for="weight_service">Процент услуг, предоставляемых инвалидам</label></small>
                                    <input id="weight_service" class="form-control" type="number" ondrop="return false;" ondragover="return false;">
                                </div>
                           </div>  
                                
                              <div class="block-separator"></div>
                                                               
                            <hr>                          
                            
                            <div class="form-row">                              
                                                                                                                                                 
                                <div class="col">
                                    <small><label for="recomendations_for_using">Рекомендации по использованию объекта транспортной инфраструктуры</label></small>
                                    <input id="recomendations_for_using" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>    
                                
                                <div class="col">
                                    <small><label for="awaiting_result">Ожидаемый результат</label></small>
                                    <input id="awaiting_result" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div> 
                                 
                            </div>  
                          
                            <div class="form-row">                              
                                <div class="col">
                                    <small><label for="actualization_date">Дата актуализации информации</label></small>
                                    <input id="actualization_date" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                
                                <div class="col">
                                    <small><label for="note">Примечание</label></small>
                                    <input id="note" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>                                                                
                            </div>
                            
                            <div class="block-separator"></div>
                            
                             <div class='custom-control custom-checkbox'>
                                    <small> <!-- The only way to align box and label vertically -->
                                        <input id='mark_OOI' type='checkbox' class='custom-control-input'>
                                        <label class='custom-control-label' for='mark_OOI'>**Отметка об участии общественных объединений инвалидов в проведении обследовании и паспортизации</label>
                                    </small>
                             </div>
                            
                        </form>    
                    </div>
                    <div class="modal-footer y-modal-footer-bk">
                        <p id="dlg_err" class="y-modal-err y-err-text y-info-label"></p>
                        <button id="train_ok" class="btn btn-info y-shad">Отправить</button>
                         <button id="train_exit" data-dismiss="modal" class="btn btn-light y-shad">Отмена</button>
                    </div>
                </div>
            </div>
        </div>



<!--
                            <div class="form-row">       

                                <div class="col">
                                    <small><label for="awaiting_result">Ожидаемый результат доспутности после выполнения работ по адаптации</label></small>
                                    <input id="awaiting_result" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                <div class="col">
                                    <small><label for="type_of_adap_work">Виды работ по адаптации</label></small>
                                    <input id="type_of_adap_work" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                 <div class="col">
                                    <small><label for="preriod_execution">Плановый период исполнения</label></small>
                                    <input id="preriod_execution" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                                
                                <div class="col">
                                                        
                                   <small><label for="reasons_non_execution">Причины невыполнения</label></small>
                                    <input id="reasons_non_execution" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                </div>
                             </div> 
                                
                                <div class="form-row">
                                    <div class="col">                                                          
                                         <div class='custom-control custom-checkbox'>
                                                      <small> 
                                                          <input id='execute_mark_adap' type='checkbox' class='custom-control-input'>
                                                          <label class='custom-control-label' for='execute_mark_adap'>Отметка о выполнении работ по адаптации</label>
                                                      </small>
                                          </div>      

                                    </div>     
                                </div>                        

                            <hr>
-->
