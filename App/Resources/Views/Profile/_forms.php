
<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nome Completo</label>
        <input type="text" step="0" min="1" class="form-control" name="name" placeholder="digite aqui" value="<?=$usuario->name ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Email</label>
        <input type="email" step="0" min="1" class="form-control" name="email" placeholder="digite aqui" value="<?=$usuario->email ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-3 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Senha de primeiro acesso</label>
        <input type="password" step="0" min="1" class="form-control" name="password" placeholder="digite aqui" value="" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Situação</label>
        <select name="active" class="form-control" id="">
            <option value="0" <?php if(isset($usuario->active) && $usuario->active == '0') { echo 'selected'; } ?>>Impedido</option>
            <option value="1" selected <?php if(isset($usuario->active) && $usuario->active == '1') { echo 'selected'; } ?>>Disponivel</option>
        </select>
      </div>
   </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Acesso</label>
        <select name="access" class="form-control" id="">
          <option value="administrador" <?= (isset($usuario->access) && $usuario->access == "administrador") ? 'selected' : ''?>>Administrador</option>
          <option value="gerente" <?= (isset($usuario->access) && $usuario->access == "gerente") ? 'selected' : ''?>>Gerente</option>
          <option value="recepcionista" <?= (isset($usuario->access) && $usuario->access == "recepcionista") ? 'selected' : ''?>>Recepcionista</option>
          <option value="recepcionista_bar" <?= (isset($usuario->access) && $usuario->access == "recepcionista_bar") ? 'selected' : ''?>>Recepcionista_bar</option>
        </select>
      </div>
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

