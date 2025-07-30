<?php require_once __DIR__ . '/../layout/top.php'; ?>


<!-- Row start -->
<div class="row gx-3">
        <div class="col-8 col-xl-6">
            <!-- Breadcrumb start -->
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item">
                    <i class="icon-house_siding lh-1"></i>
                    <a href="/" class="text-decoration-none">Início</a>
                </li>
                <li class="breadcrumb-item">
                    <i class="fs-3 icon-archive lh-1"></i>
                    <a href="/apartamento/" class="text-decoration-none">Serviço</a>
                </li>
                <li class="breadcrumb-item">Atualizar</li>
            </ol>
            <!-- Breadcrumb end -->
        </div>
        <div class="col-2 col-xl-6">
            <div class="float-end">
                <a href="/apartamento/" class="btn btn-outline-primary" > Voltar </a>
            </div>
        </div>
    </div>
    <!-- Row end -->
    <form action="/apartamento/<?=$apartamento->uuid?>/upt" method="post" enctype="multipart/form-data">   
        <div class="row gx-3">
            <? include_once('_forms.php');?>
        </div>
    </form>


<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
