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
                    <a href="/users/" class="text-decoration-none">Usuarios</a>
                </li>
                <li class="breadcrumb-item">Vincular Permissão</li>
            </ol>
            <!-- Breadcrumb end -->
        </div>
        <div class="col-2 col-xl-6">
            <div class="float-end">
                <a href="/users/" class="btn btn-outline-primary" > Voltar </a>
            </div>
        </div>
    </div>
    <!-- Row end -->
    <form action="/user/<?=$usuario->uuid?>/permission" method="post">
        <div class="row gx-3">
            <div class="col-sm-12 col-12">
                <div class="card mb-3">
                  <div class="card-body">
                    <?php 
                        foreach ($permissions as $permission) {
                    ?>
                        <div class="form-check form-check-inline">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="inlineCheckbox<?=$permission->id?>" 
                                <?=isPermissionChecked($permission->id, $permissions_user) ? 'checked' : ''?>
                                name="permissions[]" 
                                value="<?=$permission->id?>" />
                            <label 
                                class="form-check-label" 
                                for="inlineCheckbox<?=$permission->id?>">
                                <?=$permission->name?>
                            </label>
                        </div>                      
                    <?php }?>
                  </div>
                </div>
            </div>
            <div class="col-xxl-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <a href="\users\" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>


<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
