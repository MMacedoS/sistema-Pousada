<?php require_once __DIR__ . '/../layout/top.php'; ?>

<style>
  @media screen and (max-width:760px){
      .d-none-sm {
        display: none !important;
      }
  }
</style>

    <!-- Row start -->
<div class="row gx-3">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row gx-3">
                    <?php 
                        foreach ($data['reservas'] as $key => $value) {
                    ?>                    
                    <div class="col-lg-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <img src="https://placehold.co/600x400/EEE/31343C?font=lato&text=APT <?=$value->apartament?>" alt="Bootstrap Dashboards"
                                        class="img-fluid img-5x rounded-3 border" />
                                    <div class="ms-3">
                                        <p>
                                           Clientes: <?=getCustomers(json_decode($value->customers))?> <br>
                                           Período de <?=brDate($value->dt_checkin)?> á  <?=brDate($value->dt_checkout)?> <br>
                                        </p>
                                        <span class="small badge rounded-pill bg-warning m-1 d-none-sm"> Mensalidade <?=brCurrency($value->total_diaries)?></span>
                                        <span class="small badge rounded-pill bg-info m-1 d-none-sm"> Consumo <?=brCurrency($value->total_consumptions)?></span>                                        
                                        <span class="small badge rounded-pill bg-success m-1 d-none-sm"> Pagamento <?=brCurrency($value->total_payments)?></span>
                                    </div>
                                    
                                    <div class="ms-3 w-auto display-6 fs-4 d-none-sm">
                                      <span class="btn btn-primary m-1">Total <?=brCurrency(($value->total_diaries + $value->total_consumptions))?></span>  
                                      <br>                                    
                                      <span class="btn btn-danger m-1 text-light">Resta <?=brCurrency(($value->total_diaries + $value->total_consumptions) - $value->total_payments)?></span>
                                    </div>
                                </div>

                                <div class="d-sm-none">
                                  <span class="small badge rounded-pill bg-warning m-1"> Mensalidade <?=brCurrency($value->total_diaries)?></span>
                                  <span class="small badge rounded-pill bg-info m-1"> Consumo <?=brCurrency($value->total_consumptions)?></span>                                        
                                  <span class="small badge rounded-pill bg-success m-1"> Pagamento <?=brCurrency($value->total_payments)?></span>
                                </div>
                                                               
                                <div class="ms-3 d-none-sm">
                                      <button type="button" class="btn btn-warning m-1" onclick="openModelDiaries('<?=$value->uuid?>')">
                                        <span class="fs-6 icon-plus-circle"> Mensalidade</span>
                                      </button>
                                      <button type="button" class="btn btn-info m-1" onclick="openModelProducts('<?=$value->uuid?>')">
                                        <span class="fs-6 icon-plus-circle"> Produtos</span>
                                      </button>
                                      <a class="btn btn-light m-1" href="\reserva\<?=$value->uuid?>\editar">
                                        <span class="fs-6 icon-edit"> Editar</span>
                                      </a>                                      
                                      <button type="button" class="btn btn-success m-1" onclick="openModelPayments('<?=$value->uuid?>')">
                                        <span class="fs-6 icon-dollar-sign"> Pagamento</span>
                                      </button>
                                      <!-- <button type="button" class="btn btn-danger m-1" onclick="openModelDiaries('<?=$value->uuid?>')">
                                        <span class="fs-6 icon-login"> Check-out</span>
                                      </button> -->
                                </div>

                                <div class="ms-3 display-6 fs-4 d-sm-none">
                                  <span class="btn btn-primary m-1 w-100">Total R$ <?=brCurrency(($value->total_diaries + $value->total_consumptions))?></span>                                 
                                  <span class="btn btn-danger m-1 text-light w-100">Resta R$ <?=brCurrency(($value->total_diaries + $value->total_consumptions) - $value->total_payments)?></span>
                                </div> 

                                <div class="ms-3 d-sm-none me-2 w-100">
                                    <button type="button" class="btn btn-outline-info w-100 dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                    <div class="dropdown-menu">
                                    <button type="button" class="btn btn-warning m-1" onclick="openModelDiaries('<?=$value->uuid?>')">
                                        <span class="fs-6 icon-plus-circle"> Mensalidade</span>
                                      </button>
                                      <button type="button" class="btn btn-info m-1" onclick="openModelProducts('<?=$value->uuid?>')">
                                        <span class="fs-6 icon-plus-circle"> Produtos</span>
                                      </button>
                                      <a class="btn btn-light m-1" href="\reserva\<?=$value->uuid?>\editar">
                                        <span class="fs-6 icon-edit"> Editar</span>
                                      </a>                                      
                                      <button type="button" class="btn btn-success m-1" onclick="openModelPayments('<?=$value->uuid?>')">
                                        <span class="fs-6 icon-dollar-sign"> Pagamento</span>
                                      </button>
                                      <!-- <button type="button" class="btn btn-danger m-1" onclick="openModelDiaries('<?=$value->uuid?>')">
                                        <span class="fs-6 icon-login"> Check-out</span>
                                      </button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="float-end">
        <?=$data['links']?>
    </div>
</div>

<div class="modal fade" id="modalDiaries" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="modalDiariesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDiariesLabel">
                   Cadastro de Mensalidade
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2 d-flex align-items-end justify-content-between">
                    <h5>Mensalidades Adicionadas</h5>
                    <button class="btn btn-danger" id="get-checked-values" disabled>Delete Selecionados</button>
                </div>
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>Valor</th>
                              <th>Data</th>                              
                              <th>Ações</th>
                              <th>
                                <input type="checkbox" id="checkAll"/>
                              </th>
                            </tr>
                          </thead>
                          <tbody id="list">
                            
                          </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
            <div class="container mt-2">
                <form action="" id="form_inserir" method="post">
                    <input type="hidden" name="id" id="id" disabled>
                    <div class="row gx-3">
                        <div class="col-6">
                            <input type="date" name="dt_daily" id="dt_daily" class="form-control" value="<?=date('Y-m-d')?>">
                        </div>
                        <div class="col-6">
                            <input type="number" name="amount" id="amount" step="0.01" value="0" min="0" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="refreshPage()">
                    Fechar
                </button>
                <button type="button" disabled id="btn_inserir" class="btn btn-primary">
                    Inserir Mensalidade
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalProduct" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="modalDiariesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDiariesLabel">
                   Adicionar Produtos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2 d-flex align-items-end justify-content-between">
                    <h5>Produtos Adicionados</h5>
                    <button class="btn btn-danger" id="get-checked-values-products" disabled>Delete Selecionados</button>
                </div>
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                          <thead>
                            <tr>                           
                              <th>Produto</th>                              
                              <th class="d-none-sm">Data</th> 
                              <th>Valor</th>
                              <th>Qtde.</th>                              
                              <th>Ações</th>
                              <th>
                                <input type="checkbox" id="checkAllProducts"/>
                              </th>
                            </tr>
                          </thead>
                          <tbody id="listProducts">
                            
                          </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
            <div class="container mt-2">
                <form action="" id="form_inserir_produtos" method="post">
                    <input type="hidden" name="id" id="product_id" disabled>
                    <div class="row gx-3">
                        <div class="col-6">
                            <select name="product_id" id="product" class="form-control">
                              <?php foreach($data['products'] as $product){ ?>
                                <option value="<?=$product->id?>"><?=$product->name?></option>
                               <? } ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <input type="number" name="quantity" id="quantity" step="0" value="1" min="1" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="refreshPage()">
                    Fechar
                </button>
                <button type="button" id="btn_inserir_consumo" class="btn btn-primary">
                    Inserir Produto
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPayments" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="modalDiariesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPaymentsLabel">
                   Adicionar Pagamentos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="refreshPage()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2 d-flex align-items-end justify-content-between">
                    <h5>Pagamento Adicionados</h5>
                    <button class="btn btn-danger" id="get-checked-values-payments" disabled>Delete Selecionados</button>
                </div>
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                          <thead>
                            <tr>                           
                              <th>Tipo</th>                              
                              <th>Data</th> 
                              <th>Valor</th>                             
                              <th>Ações</th>
                              <th>
                                <input type="checkbox" id="checkAllPayments"/>
                              </th>
                            </tr>
                          </thead>
                          <tbody id="listPayments">
                            
                          </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
            <div class="container mt-2">
                <form action="" id="form_inserir_payments" method="post">
                    <input type="hidden" name="id" id="payment_id" disabled>
                    <div class="row gx-3">
                        <div class="col-5">
                            <select name="type_payment" id="type_payment" class="form-control">
                              <option value="Dinheiro">Dinheiro</option>
                              <option value="Cartão Crédito">Cartão Crédito</option>
                              <option value="Cartão Débito">Cartão Débito</option>
                              <option value="Pix">Pix</option>
                              <option value="Cortesia">Cortesia</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <input type="number" name="payment_amount" id="payment_amount" step="0.01" value="0.00" min="0" class="form-control">
                        </div>
                        <div class="col-4">
                            <input type="date" name="dt_payment" id="dt_payment" class="form-control" value="<?=date('Y-m-d')?>">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="refreshPage()">
                    Fechar
                </button>
                <button type="button" id="btn_inserir_payments" class="btn btn-primary">
                    Inserir Pagamentos
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
