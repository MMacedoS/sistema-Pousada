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
            <li class="breadcrumb-item">Permissão</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <div class="col-2 col-xl-6">
        <div class="float-end">
         <a href="\permissao\criar" class="btn btn-outline-primary" > + </a>
        </div>
    </div>
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
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                           <thead>
                                <tr>
                                    <th></th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>                            
                            <tbody>
                            <? foreach ($data['permissao'] as $permissao) { ?>
                                    <tr>
                                        <td><?=$permissao->id?></td>
                                        <td class="fw-bold"> 
                                            <?=$permissao->name?>
                                        </td>
                                        <td>
                                            <?=$permissao->description?>
                                        </td>                                        
                                    </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        Total <b><?=count($data['permissao'])?></b> registros
                    </div>
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

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
