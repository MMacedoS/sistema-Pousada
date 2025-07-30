
<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Nome do Produto</label>
        <input type="text" class="form-control" name="name" placeholder="" value="<?=$product->name ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-8 col-sm-8 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Descrição</label>
        <input type="text" class="form-control" name="description" placeholder="" value="<?=$product->description ?? ''?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-6 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Preço</label>
        <input type="number" class="form-control" step="0.01" min="0" name="amount" placeholder="" value="<?=$product->price ?? '0.00'?>" />
      </div>
    </div>
  </div>
</div>

<div class="col-lg-4 col-sm-4 col-12">
  <div class="card mb-3">
    <div class="card-body">
      <div class="m-0">
        <label class="form-label">Categoria</label>
        <select class="form-select" name="category">
          <option value="Brasileiro" 
            <?php if (isset($product->category) && $product->category === 'Bebidas') { echo 'selected';} ?>>
              Bebidas
          </option>          
          <option value="Brasileiro" 
            <?php if (isset($product->category) && $product->category === 'Almoço') { echo 'selected';} ?>>
              Almoço
          </option>
          <option value="Brasileiro" 
            <?php if (isset($product->category) && $product->category === 'Petiscos') { echo 'selected';} ?>>
              Petiscos
          </option>
          <option value="Salgados" 
            <?php if (isset($product->category) && $product->category === 'Salgados') { echo 'selected';} ?>>
            Salgados
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
        <label class="form-label">Estoque</label>
        <input type="number" class="form-control" name="stock" step="0" min="0" id="stock" placeholder="" value="<?=$product->stock ?? '0.00'?>" />
        <div class="invalid-feedback" id="doc_error"></div>
      </div>
    </div>
  </div>
</div>

<div class="col-xxl-12">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="\produto\" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>
    </div>
</div>

