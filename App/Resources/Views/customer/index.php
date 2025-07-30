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
            <li class="breadcrumb-item">Clientes</li>
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
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                           <thead>
                                <tr>
                                    <th></th>
                                    <th>Cliente</th>
                                    <th>Representante</th>
                                    <th>Situação</th>
                                    <?php if (hasPermission('editar clientes') || hasPermission('deletar clientes')) { ?>
                                        <th>Actions</th>
                                    <? }?>
                                </tr>
                            </thead>
                            
                            <tbody>
                            <? foreach ($data['clientes'] as $cliente) { ?>
                                    <tr>
                                        <td><?=$cliente->id?></td>
                                        <td class="fw-bold"><?=$cliente->name?>
                                        </td>
                                        <td>
                                        <?=$cliente->representative ? 'Sim': 'Não'?>
                                        </td>
                                        <td>    
                                            <div class="d-flex align-items-center">
                                                <? if($cliente->status == 0) { ?>
                                                    <i class="icon-circle1 me-2 text-danger fs-5"></i>
                                                    Impedido
                                                <? } ?>
                                                <? if($cliente->status == 1) { ?>
                                                    <i class="icon-circle1 me-2 text-success fs-5"></i>
                                                    Disponivel
                                                <? } ?>
                                            </div>
                                        </td>
                                        <?php if (hasPermission('editar clientes') || hasPermission('deletar clientes')) { ?>
                                            <td class="d-flex">
                                            <?php if (hasPermission('editar clientes')) { ?>
                                                <a class="mb-1 me-2 mt-1" href="/cliente/<?=$cliente->uuid?>/editar">
                                                   <div class="border p-2 rounded-3">
                                                        <span class="fs-5 icon-edit"></span>
                                                    </div>
                                                </a>
                                            <? } ?>

                                            <?php if (hasPermission('deletar clientes')) { ?>
                                                <form action="/cliente/<?=$cliente->uuid?>/deletar" method="post">                                            
                                                    <button class="btn btn-outline btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_<?=$cliente->uuid?>"> 
                                                        <div class="border p-2 rounded-3">
                                                            <span class="fs-5 text-danger icon-trash"></span>
                                                        </div>
                                                    </button>
                                                    <div class="modal fade" id="exampleModal_<?=$cliente->uuid?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Tem certeza que deseja excluir este registro? 
                                                                    <p>cliente <?=$cliente->name?></p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                <? }?>
                                            </td>
                                        <? } ?>
                                    </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        Total <b><?=count($data['clientes'])?></b> registros
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
