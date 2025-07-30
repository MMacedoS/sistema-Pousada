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
                <a href="/reserva/" class="text-decoration-none">Ordem</a>
            </li>         
            <li class="breadcrumb-item">Serviços</li>
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
                                <div onclick="openModelDiaries('<?=$value->uuid?>')" class="d-flex align-items-start">
                                    <img src="https://placehold.co/600x400/EEE/31343C?font=lato&text=APT <?=$value->apartament?>" alt="Bootstrap Dashboards"
                                        class="img-fluid img-5x rounded-3" />
                                    <div class="ms-3">
                                        <p>
                                           Clientes: <?=getCustomers(json_decode($value->customers))?> <br>
                                           Período de <?=brDate($value->dt_checkin)?> á  <?=brDate($value->dt_checkout)?> <br>
                                        </p>
                                        <span class="small badge rounded-pill bg-success m-0"> Diária R$ <?=number_format($value->total_diaries, 2 ,',' ,'.')?></span>
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
                    <h5>Mensalidade Adicionada</h5>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Fechar
                </button>
                <button type="button" disabled id="btn_inserir" class="btn btn-primary">
                    Inserir Diária
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
