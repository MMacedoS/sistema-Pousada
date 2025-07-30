<input type="hidden" id="reserve" value="<?=$data['reserve']->id ?? ''?>">
<div class="col-lg-6 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Data de Check-in</label>
        <input type="date" class="form-control" name="dt_checkin" id="dt_checkin" placeholder="" value="<?=$data['reserve']->dt_checkin ?? $_GET['dt_reserva'] ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-6 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Data de Check-out</label>
        <input type="date" class="form-control" name="dt_checkout" placeholder="" id="dt_checkout" value="<?=$data['reserve']->dt_checkout ?? $_GET['dt_reserva'] ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Apartamento</label>
        <select class="form-select" name="apartament" id="apartament">
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Situação</label>
        <select class="form-select" name="status">

          <? if(!isset($data['reserve']) || $data['reserve']->status === 'Reservada' || $data['reserve']->status === 'Confirmada') { ?>
            <option value="Reservada" 
              <?php if (isset($data['reserve']->status) && $data['reserve']->status === 'Reservada') { echo 'selected';} ?>>
                Reservada
            </option>
            <option value="Confirmada" 
              <?php if (isset($data['reserve']->status) && $data['reserve']->status === 'Confirmada') { echo 'selected';} ?>>
              Confirmada
            </option>
          <? } ?>

          <? if(isset($data['reserve'])) { ?>
          <option value="Hospedada" 
            <?php if (isset($data['reserve']->status) && $data['reserve']->status === 'Hospedada') { echo 'selected';} ?>>
            Hospedada
          </option>
          <? }?>

          <? if(!isset($data['reserve']) && ($data['reserve']->status === 'Cancelada' || $data['reserve']->status === 'Finalizada')) { ?>
            <option value="Cancelada" 
              <?php if (isset($data['reserve']->status) && $data['reserve']->status === 'Cancelada') { echo 'selected';} ?>>
              Cancelada
            </option>
            <option value="Finalizada" 
              <?php if (isset($data['reserve']->status) && $data['reserve']->status === 'Finalizada') { echo 'selected';} ?>>
              Finalizada
            </option>
          <? } ?>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Valor</label>
        <input type="number" class="form-control" name="amount" step="0.01" min="0.00" id="amount" value="<?=$data['reserve']->amount ?? 150?>">
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-12 col-sm-12 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Hóspedes</label>
        <select class="js-example-basic-multiple form-control form-select" name="customers[]" multiple="multiple">
          <?php 
            $reserve_customers = json_decode($data['reserve']->customers) ?? [];
            
            if (!empty($reserve_customers)) {
              foreach ($reserve_customers as $customer) {
                echo '<option selected value="' . $customer->id_guest . '">' . $customer->name . '</option>';
              }
            }
            
            foreach ($data['customers'] as $customer) {
              $is_selected = false;
              foreach ($reserve_customers as $reserved_customer) {
                if ($reserved_customer->id_guest == $customer->id) {
                  $is_selected = true;
                  break;
                }
              }
              
              if (!$is_selected) {
                echo '<option value="' . $customer->id . '">' . $customer->name . '</option>';
              }
            }
          ?>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="\reserva\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

