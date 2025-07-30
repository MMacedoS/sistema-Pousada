
<div class="col-lg-6 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nome Completo</label>
        <input type="text" class="form-control" name="name" placeholder="" value="<?=$cliente->name ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-6 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" placeholder="" value="<?=$cliente->email ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Telefone</label>
        <input type="text" class="form-control" name="phone" id="phone" maxlength="11" value="<?=$cliente->phone ?? ''?>" 
        required pattern="^\(?([0-9]{2})\)?[-. ]?([0-9]{4,5})[-. ]?([0-9]{4})$"/>
        <div class="invalid-feedback">Telefone inválido</div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Profissão</label>
        <input type="text" class="form-control" name="job" placeholder="" value="<?=$cliente->job ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nacionalidade</label>
        <select class="form-select" name="nationality">
          <option value="Brasileiro" 
            <?php if (isset($cliente->nationality) && $cliente->nationality === 'Brasileiro') { echo 'selected';} ?>>
              Brasileiro
          </option>
          <option value="Estrangeiro" 
            <?php if (isset($cliente->nationality) && $cliente->nationality === 'Estrangeiro') { echo 'selected';} ?>>
            Estrangeiro
          </option>
        </select>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Tipo documento</label>
        <select class="form-select" name="type_doc" id="type_doc">
          <option value="CPF" <?php if (isset($cliente->type_doc) && $cliente->type_doc === 'CPF') { echo 'selected';} ?>>CPF</option>
          <option value="CNH" <?php if (isset($cliente->type_doc) && $cliente->type_doc === 'CNH') { echo 'selected';} ?>>CNH</option>
          <option value="RG" <?php if (isset($cliente->type_doc) && $cliente->type_doc === 'RG') { echo 'selected';} ?>>RG</option>
          <option value="PASSAPORTE" <?php if (isset($cliente->type_doc) && $cliente->type_doc === 'PASSAPORTE') { echo 'selected';} ?>>Passaporte</option>
        </select>
        <div class="invalid-feedback" id="type_doc_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Numero documento</label>
        <input type="text" class="form-control" name="doc" id="doc" placeholder="" value="<?=$cliente->doc ?? ''?>" />
        <div class="invalid-feedback" id="doc_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-12 col-sm-12 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Endereço</label>
        <input type="text" class="form-control" name="address" placeholder="" value="<?=$cliente->address ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" name="representative" id="representative"
        <?php if (isset($cliente->representative)) { echo 'checked';} ?> />
        <label class="form-check-label" for="flexSwitchCheckChecked">É um representante?</label>
      </div>
    </div>
  </div>
</div>

<div class="row gx-3  <?=(isset($cliente->representative) && $cliente->representative == 1) ? '' : 'd-none' ?>" id="div_representante">
  <div class="col-lg-6 col-sm-12 col-12">
    <div class="card mb-3">
      <div class="card-body">
        <div class="m-0">
          <label class="form-label">Empresa</label>
          <input type="text" class="form-control" name="company" placeholder="" value="<?=$cliente->company ?? ''?>" />
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-sm-12 col-12">
    <div class="card mb-3">
      <div class="card-body">
        <div class="m-0">
          <label class="form-label">CNPJ</label>
          <input type="text" class="form-control" name="cnpj_company" id="cnpj_company" maxlength="14" value="<?=$cliente->cnpj_company ?? ''?>" />
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 col-sm-12 col-12">
    <div class="card mb-3">
      <div class="card-body">
        <div class="m-0">
          <label class="form-label">Telefone da Empresa</label>
          <input type="text" class="form-control" name="phone_company" maxlength="11" id="phone_company" placeholder="" value="<?=$cliente->phone_company ?? ''?>" />
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-8 col-sm-12 col-12">
    <div class="card mb-3">
      <div class="card-body">
        <div class="m-0">
          <label class="form-label">Email Empresa</label>
          <input type="text" class="form-control" name="email_company" placeholder="" value="<?=$cliente->email_company ?? ''?>" />
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="\cliente\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

