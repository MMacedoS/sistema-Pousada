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
            <li class="breadcrumb-item">Checkin</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <?php if (hasPermission('cadastrar reservas')) { ?>
        <div class="col-2 col-xl-6">
            <div class="float-end">
            <a href="\reserva\criar" class="btn btn-outline-primary" > + Reserva </a>
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
    <?php foreach($data['reservas'] as $key => $value) { ?>
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="card mb-3" data-bs-toggle="modal" data-bs-target="#modalReserva<?= $key ?>">
            <div class="card-body">
                <div class="d-flex mb-2">
                    <div class="icons-box md bg-primary rounded-5 me-3">
                        <i class="fs-3 icon-briefcase text-white"></i>
                    </div>
                    <div class="d-flex flex-column">                
                        <h2 class="m-0 lh-1 d-block d-sm-none">Apto: <?= $value->apartament ?></h2>
                        <h2 class="m-0 lh-1 d-none d-xl-block d-lg-block d-md-block">Apto: <?= $value->apartament ?></h2>
                    </div>
                </div>
                <div class="m-0">
                    <p class="m-0 small fw-light">
                        <?= getCustomers(json_decode($value->customers)) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalReserva<?= $key ?>" tabindex="-1" aria-labelledby="modalLabel<?= $key ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel<?= $key ?>">Detalhes da Reserva - Apto: <?= $value->apartament ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Data de Check-in:</strong> <?= brDate($value->dt_checkin) ?></p>
                    <p><strong>Data de Check-out:</strong> <?= brDate($value->dt_checkout) ?></p>
                    <p><strong>Hóspedes:</strong></p>
                    <ul>
                        <?php foreach (json_decode($value->customers) as $customer) { ?>
                            <li><?= $customer->name ?><?= $customer->is_primary ? ' (Titular)' : '' ?></li>
                        <?php } ?>
                    </ul>
                    
                    <p><strong>Valor da Diária(Pacote):</strong> <?= brDate($value->amount) ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-success" onclick="efetivarCheckin('<?= $value->uuid ?>')">Efetivar Check-in</button>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<script>
function efetivarCheckin(reservaId) {
    $.ajax({
        url: '/checkin/' + reservaId + '/reserva',
        method: 'POST',
        dataType: 'JSON',
        contentType: 'application/json',
        processData: true,
        success: function(response){
            if(response.status === 200) {
                Swal.fire({
                    title: "Sucesso!",
                    text: response.message,
                    icon: "success"
                }).then(() => {
                    window.location.reload();
                });
                return;
            }
           
            Swal.fire({
                title: "Atenção!",
                text: response.message,
                icon: "warning"
            }).then(() => {
                window.location.reload();
            });
        },
    }).fail(function(){
        Swal.fire({
            title: "error!",
            text: response.message,
            icon: "warning"
        });
    });    
}
</script>


<div class="row">
    <div class="float-end">
        <?=$data['links']?>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
