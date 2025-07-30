
<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Serviço</label>
        <input type="text" class="form-control" name="name" placeholder="Serviço" value="<?=$apartamento->name ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Situação</label>
        <select name="status" class="form-control" id="">
            <option value="0" <?php if(isset($apartamento->status) && $apartamento->status == '0') { echo 'selected'; } ?>>Impedido</option>
            <option value="1" selected <?php if(isset($apartamento->status) && $apartamento->status == '1') { echo 'selected'; } ?>>Disponivel</option>
        </select>
      </div>
   </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="m-0">
                <label class="form-label">Categoria</label>
                <select name="category" class="form-control" id="">
                    <option value="Padrão" <?php if(isset($apartamento->category) && $apartamento->category == 'Padrão') { echo 'selected'; } ?>>Padrão</option>
                    <option value="Redes" <?php if(isset($apartamento->category) && $apartamento->category == 'Redes') { echo 'selected'; } ?>>Redes</option>
                    <option value="Manutenção" <?php if(isset($apartamento->category) && $apartamento->category == 'Manutenção') { echo 'selected'; } ?>>Manutenção</option>                       
                    <option value="Software" <?php if(isset($apartamento->category) && $apartamento->category == 'Software') { echo 'selected'; } ?>>Software</option>                      
                    <option value="IPTV" <?php if(isset($apartamento->category) && $apartamento->category == 'IPTV') { echo 'selected'; } ?>>IPTV</option>            
                    <option value="Aluguel" <?php if(isset($apartamento->category) && $apartamento->category == 'Aluguel') { echo 'selected'; } ?>>Aluguel</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12 col-sm-12 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Descrição</label>
        <input type="text" class="form-control" name="description" placeholder="descrição do apartamento" value="<?=$apartamento->description ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="\apartamento\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

