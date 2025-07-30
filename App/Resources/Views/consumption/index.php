<?php require_once __DIR__ . '/../layout/top.php'; ?>

<!-- Row start -->
<div class="row gx-3">
    <div class="col-8 col-xl-6">
        <!-- Breadcrumb start -->
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item">
                <i class="icon-house_siding lh-1"></i>
                <a href="\" class="text-decoration-none">Início</a>
            </li>
            <li class="breadcrumb-item">
                <a href="/reserva/" class="text-decoration-none">Reservas</a>
            </li>         
            <li class="breadcrumb-item">Hospedadas</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <?php if (hasPermission('cadastrar clientes')) { ?>
        <div class="col-2 col-xl-6">
            <div class="float-end">
            <a href="\cliente\criar" class="btn btn-outline-primary" > + </a>
            </div>
        </div>
    <? } ?>
</div>
    <!-- Row end -->
<? if(isset($success)){?>
    <div class="alert border border-success alert-dismissible fade show text-success" role="alert">
      <b>Success!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? }?>
<? if(isset($danger)){?>
    <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
       <b>Danger!</b>.
       <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? }?>
    <!-- Row start -->
<div class="row gx-3">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row gx-3">
                    <?php 
                        foreach ($data['reservas'] as $key => $value) {
                    ?>                    
                    <div class="col-lg-4">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div onclick="openModelProducts('<?=$value->uuid?>')" class="d-flex align-items-start">
                                    <img src="https://placehold.co/600x400/EEE/31343C?font=lato&text=APT <?=$value->apartament?>" alt="Bootstrap Dashboards"
                                        class="img-fluid img-5x rounded-3" />
                                    <div class="ms-3">
                                        <p>
                                           Hospedes: <?=getCustomers(json_decode($value->customers))?> <br>
                                           Período de <?=brDate($value->dt_checkin)?> á  <?=brDate($value->dt_checkout)?> <br>
                                        </p>
                                        <span class="small badge rounded-pill bg-success m-0"> Consumo R$ <?=number_format($value->total_consumptions, 2 ,',' ,'.')?></span>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Fechar
                </button>
                <button type="button" id="btn_inserir_consumo" class="btn btn-primary">
                    Inserir Produto
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>