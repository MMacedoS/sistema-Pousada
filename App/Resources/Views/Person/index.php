<?php require_once __DIR__ . '/../layout/top.php'; ?>

<style>
    .table .dropdown {
    position: relative;
}

.table .dropdown-menu {
    top: 100%;
    left: auto;
    right: 0;
    transform: translateY(0);
    z-index: 1050;
}

</style>

<!-- Row start -->
<div class="row gx-3">
    <div class="col-8 col-xl-6">
        <!-- Breadcrumb start -->
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item">
                <i class="icon-house_siding lh-1"></i>
                <a href="/dashboard" class="text-decoration-none">Início</a>
            </li>
            <li class="breadcrumb-item">Usuarios</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <div class="col-4 col-xl-6">
        <div class="float-end">
         <a href="/user" class="btn btn-outline-primary" > + </a>
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
        <form id="users-form" action="/users" method="GET">            
            <div class="accordion mt-2" id="accordionSpecialTitle">
                <div class="accordion-item bg-transparent">
                    <h2 class="accordion-header" id="headingSpecialTitleTwo">
                        <button 
                            class=" bg-transparent accordion-button <?= isset($situation) || isset($searchFilter) ? '' : 'collapsed'?>" 
                            type="button" data-bs-toggle="collapse"
                            data-bs-target="#filters-users" 
                            aria-expanded="false"
                            aria-controls="collapseSpecialTitleTwo">
                            <h5 class="m-0">Filtros</h5>
                        </button>
                    </h2>
                    <div id="filters-users" 
                        class="accordion-collapse <?= isset($situation) || isset($searchFilter) ? '' : 'collapse'?>"
                        aria-labelledby="headingSpecialTitleTwo" 
                        data-bs-parent="#accordionSpecialTitle">
                      <div class="accordion-body">
                        <div class="row justify-content-start">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <label class="form-label">Busca por nome ou email</label>
                                            <input 
                                                class="form-input form-control" 
                                                type="text" 
                                                name="name_email" 
                                                id="name_email" 
                                                value="<?= isset($searchFilter) ? $searchFilter : null ?>" 
                                                placeholder="Digite o nome ou o email">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <label class="form-label">Acesso</label>
                                            <select class="form-select form-control" name="access" id="access">
                                                <option <?= (isset($access) && $access == '') ? 'selected' : ''?> value="">Ambos</option>
                                                <option value="administrador" <?= (isset($access) && $access == "administrador") ? 'selected' : ''?>>Administrador</option>
                                                <option value="gerente" <?= (isset($access) && $access == "gerente") ? 'selected' : ''?>>Gerente</option>
                                                <option value="recepcionista" <?= (isset($access) && $access == "recepcionista") ? 'selected' : ''?>>Recepcionista</option>
                                                <option value="recepcionista_bar" <?= (isset($access) && $access == "recepcionista_bar") ? 'selected' : ''?>>Recepcionista_bar</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mb-2">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <label class="form-label">Situação</label>
                                            <select class="form-select form-control" name="situation" id="situation">
                                                <option <?= (isset($situation) && $situation == '') ? 'selected' : ''?> value="">Ambas</option>
                                                <option value="1" <?= (isset($situation) && $situation == 1) ? 'selected' : ''?>>Disponível</option>
                                                <option value="0" <?= (isset($situation) && $situation == 0) ? 'selected' : ''?>>Impedido</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-12">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                                            <a href="\users" class="btn btn-secondary <?= isset($situation) || isset($searchFilter) || isset($shift) || isset($coordinator) ? 'd-block' : 'd-none'?>">Limpar</a>
                                            <button type="submit" class="btn btn-primary">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>