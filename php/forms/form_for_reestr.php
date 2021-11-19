        <div class="modal fade" id="form_for_reestr" tabindex="-1" role="dialog" aria-labelledby="fm_emp_ttl" aria-hidden="true" ondrop="return false;" ondragover="return false;" data-rid="" data-flg="">
            <div class="modal-dialog">
                       <div class="modal-content y-modal-shadow">
                           <div class="modal-header">
                               <div class="modal-title w-100 p-0">
                                   <div class="y-flex-row-nowrap p-0 align-items-center">
                                       <h4 id="fm_pos_ttl" class="y-dgray-text">Excel. <small><i id="fm_pos_ttl_add" class="y-steel-blue-text"></i></small></h4>
                                       <a data-dismiss="modal" class="d-inline-block y-modal-close align-self-center">&times;</a>
                                   </div>
                               </div>
                           </div>
                           <div class="modal-body">
                               <form role="form" autocomplete="off" onsubmit="return false">
                                   
                                    <div class="form-group">
                                        <p>Выбрать период</p>
                                    </div>

                                    <div class="form-row">

                                        <div class="col">
                                            <label for="from">От</label> <span class="y-llgray-text">&lt;дд.мм.гггг&gt;</span>
                                            <input id="from" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                        </div>
                                        <div class="col">
                                            <label for="to">До</label> <span class="y-llgray-text">&lt;дд.мм.гггг&gt;**</span>
                                            <input  id="to" class="form-control" type="text" ondrop="return false;" ondragover="return false;">
                                        </div>
                                    </div> 
                                   
                               </form>
                           </div>
                           <div class="modal-footer y-modal-footer-bk">
                                <input type="button" id="show_excel_reestr" class="btn btn-primary y-shad" value="Ok" name="excel_reestr"> <!--id="show_pdf_report"-->           
                                <p id="link_tmp" class="y-modal-err y-err-text y-info-label"></p>
                
                           </div>
                       </div>
             </div>
        </div>
