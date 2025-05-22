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
    <div class="card mt-2 p-2 rounded-3">
        <div class="card-title">
            <h3 class="display-8 bg-opacity-25 text-secondary">Parâmetros do Sistema</h3>
        </div>
        <div class="card-body">
            <form action="/user" method="post">
                <div class="row gx-3 mb-2">
                    <div class="col-lg-4 col-sm-12 col-12 mt-sm-3">
                        <div class="m-0">
                            <label class="form-label">Nome da Empresa</label>
                            <input type="text" class="form-control rounded-3" name="name" id="name" placeholder="digite aqui" value="<?=$usuario->name ?? ''?>" />
                            <div class="invalid-feedback" id="name_error"></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-12 col-12 mt-sm-3">
                        <div class="m-0">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control rounded-3" required name="email" id="email" placeholder="digite aqui" value="<?=$usuario->name ?? ''?>" />
                            <div class="invalid-feedback" id="email_error"></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-12 col-12 mt-sm-3">
                        <div class="m-0">
                            <label class="form-label">Telefone</label>
                            <input type="phone" class="form-control rounded-3" name="phone" id="phone" maxlength="16" maxlength="15" placeholder="(99) 99999-9999" value="<?=$pessoa_fisica->telefone ?? ''?>" 
                             required/>
                            <div class="invalid-feedback">Telefone inválido</div>
                        </div>
                    </div>
                </div>
                <div class="row gx-3 mb-2">
                    <div class="col-lg-4 col-sm-12 col-12 mt-sm-3">
                        <div class="m-0">
                            <label class="form-label">CNPJ</label>
                            <input type="text" class="form-control rounded-3" name="cnpj" id="cnpj_company" maxlength="18" maxlength="18" placeholder="xx.xxx.xxx/0001-xx" value="<?=$pessoa_fisica->telefone ?? ''?>" 
                                required/>
                            <div class="invalid-feedback">CNPJ inválido</div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-sm-12 col-12 mt-sm-3">
                        <div class="m-0">
                            <label class="form-label">Endereço</label>
                            <input type="text" name="address" id="address" class="form-control rounded-3" value="<?=$pessoa_fisica->address ?? ''?>" >
                            <div class="invalid-feedback">Endereço não pode ser vazio</div>
                        </div>
                    </div>
                </div>
                <div class="row gx-3 mb-2">
                    <div class="col-lg-4 col-sm-6 col-12 mt-sm-3">
                        <label class="form-label">Hora Check-in</label>
                        <input type="time" name="checkin" id="checkin" class="form-control rounded-3">
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12 mt-sm-3">
                        <label class="form-label">Hora Check-out</label>
                        <input type="time" name="checkout" id="checkout" class="form-control rounded-3">
                    </div>

                    <div class="col-lg-2 col-sm-6 col-12 mt-sm-3">
                        <label class="form-label">Taxa de Porcentagem</label>
                        <input type="number" step="0.01" min="0" name="porcentage_service_fee" id="porcentage_service_fee" class="form-control rounded-3">
                    </div>

                    <div class="col-lg-2 col-sm-6 col-12 mt-sm-3">
                        <label class="form-label">Taxa de Limpeza</label>
                        <input type="number" step="0.5" min="0" name="cleaning_rate" id="cleaning_rate" class="form-control rounded-3">
                    </div>
                </div>
                <div class="row gx-3 mb-2 mt-3">                   
                   <div class="col-lg-4 col-sm-6 col-12 mt-sm-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="display_values_on_dashboard" id="display_values_on_dashboard" />
                            <label class="form-label" for="display_values_on_dashboard">Exibir valores em dashboard</label>
                        </div>
                   </div>

                   <div class="col-lg-4 col-sm-6 col-12 mt-sm-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="allow_booking_online" id="allow_booking_online" />
                            <label class="form-label" for="allow_booking_online">Permitir reserva online</label>
                        </div>
                   </div>
                </div>
                <div class="row mb-2 mt-4">
                   <div class="col-lg-4 col-sm-6 col-12 mt-sm-3">
                        <label class="form-label">Moeda</label>
                        <select name="currency" id="currency" class="form-control rounded-3">
                            <option value="BRL">BRL</option>
                        </select>
                   </div>

                   <div class="col-lg-4 col-sm-6 col-12 mt-sm-3">
                        <label class="form-label">Fuso Horário</label>
                        <select name="currency" id="currency" class="form-control rounded-3">
                            <option value="America/Bahia">America/Bahia</option>
                        </select>
                   </div>

                    <div class="col-lg-4 col-sm-6 col-12 mt-sm-3">
                        <label class="form-label">Dias maximos reserva antecipada</label>
                        <input type="number" min="0" name="advance_booking_days" id="advance_booking_days" class="form-control rounded-3">
                    </div>
                </div>
                <div class="row mb-2 mt-4">
                    <div class="col-lg-12 col-sm-12 col-12">
                        <label class="form-label">Políticas de Cancelamento</label>
                        <textarea name="cancellation_policies" id="cancellation_policies" class="form-control rounded-3"></textarea>
                    </div>
                </div>
                <div class="row mb-2 mt-4 ">
                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                        <a href="\settings\" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
