
<div class="col-lg-4 col-sm-12 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Permissão</label>
        <input type="text" step="0" min="1" class="form-control" name="name" placeholder="digite aqui" value="<?=$permissao->name ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-8 col-sm-12 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Descrição</label>
        <input type="text" step="0" min="1" class="form-control" name="description" placeholder="digite aqui" value="<?=$permissao->description ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="\permissao\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

