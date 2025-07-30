
<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Identificação Venda</label>
        <input type="text" class="form-control" name="name" placeholder="" value="<?=$sale->name ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-8 col-sm-8 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Descrição</label>
        <input type="text" class="form-control" name="description" placeholder="" value="<?=$sale->description ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-8 col-sm-8 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Cliente</label>
        <select name="reserve_id" id="reserve_id" class="form-control">
          <option value="0">Consumidor Livre</option>
          <?php foreach($data['reservas'] as $reserva){ ?>
            <option value="<?=$reserva->id?>" <?php if(isset($sale) && $sale->id_reserva == $reserva->id) { echo 'selected' ; } ?>>Apartamento <?=$reserva->apartament?></option>
         <? } ?>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Adicionar</button>
                <a href="\vendas\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

