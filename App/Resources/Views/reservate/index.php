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
            <li class="breadcrumb-item">Ordens de Serviços</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <?php if (hasPermission('cadastrar reservas')) { ?>
        <div class="col-2 col-xl-6">
            <div class="float-end">
            <a href="\reserva\criar" class="btn btn-outline-primary" > + </a>
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
                                    <th class="text-center">Clientes</th>                                    
                                    <th class="text-center">Serviço</th>
                                    <th class="text-center">Inicio</th>                                    
                                    <th class="text-center">Fim</th>
                                    <th class="text-center">Situação</th>
                                    <?php if (hasPermission('editar reservas') || hasPermission('deletar reservas')) { ?>
                                        <th>Actions</th>
                                    <? }?>
                                </tr>
                            </thead>
                            
                            <tbody>
                            <? foreach ($data['reservas'] as $reserva) { ?>
                                    <tr>
                                        <td><?=$reserva->id?></td>
                                        <td class="fw-bold text-center">
                                            <?=getCustomers(json_decode($reserva->customers))?>
                                        </td>
                                        <td class="fw-bold text-center"> Apt <?=$reserva->apartament?></td>
                                        <td class="text-center">
                                            <?=brDate($reserva->dt_checkin)?>
                                        </td>
                                        <td class="text-center">
                                            <?=brDate($reserva->dt_checkout)?>
                                        </td>
                                        <td class="text-center">    
                                            <div class="text-center">
                                                <? if($reserva->status === "Cancelada") { ?>
                                                    <i class="icon-circle1 me-2 text-danger fs-5"></i>
                                                    Cancelada
                                                <? } ?>
                                                <? if($reserva->status == "Reservada") { ?>
                                                    <i class="icon-circle1 me-2 text-success fs-5"></i>
                                                    Reservada
                                                <? } ?>
                                                <? if($reserva->status == "Confirmada") { ?>
                                                    <i class="icon-circle1 me-2 text-primary fs-5"></i>
                                                    Confirmada
                                                <? } ?>
                                                <? if($reserva->status == "Hospedada") { ?>
                                                    <i class="icon-circle1 me-2 text-info fs-5"></i>
                                                    Hospedada
                                                <? } ?>
                                                <? if($reserva->status == "Finalizada") { ?>
                                                    <i class="icon-circle1 me-2 text-warning fs-5"></i>
                                                    Finalizada
                                                <? } ?>
                                            </div>
                                        </td>
                                        <?php if (hasPermission('editar reservas') || hasPermission('deletar reservas')) { ?>
                                            <td class="d-flex">
                                            <?php if (hasPermission('editar reservas')) { ?>
                                                <a class="mb-1 me-2 mt-1" href="/reserva/<?=$reserva->uuid?>/editar">
                                                   <div class="border p-2 rounded-3">
                                                        <span class="fs-5 icon-edit"></span>
                                                    </div>
                                                </a>
                                            <? } ?>

                                            <?php if (hasPermission('deletar reservas')) { ?>
                                                <form action="/reserva/<?=$reserva->uuid?>/deletar" method="post">                                            
                                                    <button class="btn btn-outline btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_<?=$reserva->uuid?>"> 
                                                        <div class="border p-2 rounded-3">
                                                            <span class="fs-5 text-danger icon-trash"></span>
                                                        </div>
                                                    </button>
                                                    <div class="modal fade" id="exampleModal_<?=$reserva->uuid?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Tem certeza que deseja excluir este registro? 
                                                                    <p>reserva <?=$reserva->name?></p>
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
                        Total <b><?=count($data['reservas'])?></b> registros
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
