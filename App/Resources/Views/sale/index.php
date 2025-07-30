<?php require_once __DIR__ . '/../layout/top.php'; ?>

<style>
  @media screen and (max-width:760px){
      .d-none-sm {
        display: none !important;
      }
  }
</style>

    <div class="row gx-3">
        <div class="col-8 col-xl-6">
            <!-- Breadcrumb start -->
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item">
                    <i class="icon-house_siding lh-1"></i>
                    <a href="\" class="text-decoration-none">Início</a>
                </li>
                <li class="breadcrumb-item">Vendas</li>
            </ol>
        <!-- Breadcrumb end -->
        </div>
        <?php if (hasPermission('cadastrar vendas')) { ?>
            <div class="col-2 col-xl-6">
                <div class="float-end">
                <a href="\vendas\criar" class="btn btn-outline-primary" > + </a>
                </div>
            </div>
        <? } ?>
    </div>

    <!-- Row start -->
    <div class="row gx-3">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row gx-3">
                    <?php 
                        foreach ($data['sales'] as $key => $value) {
                    ?>                    
                    <div class="col-lg-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <img src="https://placehold.co/600x400/EEE/31343C?font=lato&text=<?=!is_null(reservaFilteredById($value->id_reserva, $data['reservas'])) ? 'APT-' . reservaFilteredById($value->id_reserva, $data['reservas'])->apartament : $value->name?>" alt="Bootstrap Dashboards"
                                        class="img-fluid img-6x rounded-3 border" />
                                    <div class="ms-3">
                                        <span class="small badge rounded-pill bg-info m-1 d-sm-none"> Consumo R$ <?=number_format($value->total_consumptions, 2 ,',' ,'.')?></span>                                        
                                        <span class="small badge rounded-pill bg-success m-1 d-sm-none"> Pagamento R$ <?=number_format($value->total_payments, 2 ,',' ,'.')?></span>
                                        <br>
                                        <button type="button" class="btn btn-info m-1 d-none-sm" onclick="openModelItens('<?=$value->uuid?>')">
                                            <span class="fs-6 icon-plus-circle"> Produtos</span>
                                        </button>
                                        <a class="btn btn-light m-1 d-none-sm" href="\vendas\<?=$value->uuid?>">
                                            <span class="fs-6 icon-edit"> Editar</span>
                                        </a> 
                                        <?php if (is_null(reservaFilteredById($value->id_reserva, $data['reservas']))) {?>                                     
                                            <button type="button" class="btn btn-success m-1 d-none-sm" onclick="openModelPayments('<?=$value->uuid?>')">
                                                <span class="fs-6 icon-dollar-sign"> Pagamento</span>
                                            </button>
                                        <? }?>
                                    </div>
                                    
                                    <div class="ms-3 w-auto display-6 fs-4 d-none-sm">
                                      <span class="btn btn-primary m-1">Total R$ <?=number_format(($value->total_diaries + $value->total_consumptions), 2 ,',' ,'.')?></span>  
                                      <br>                                    
                                      <span class="btn btn-danger m-1 text-light">Resta R$ <?=number_format(($value->total_diaries + $value->total_consumptions) - $value->total_payments, 2 ,',' ,'.')?></span>
                                    </div>
                                </div>

                                <div class="d-none-sm">
                                  <span class="small badge rounded-pill bg-info m-1"> Consumo R$ <?=number_format($value->total_consumptions, 2 ,',' ,'.')?></span>                                        
                                  <span class="small badge rounded-pill bg-success m-1"> Pagamento R$ <?=number_format($value->total_payments, 2 ,',' ,'.')?></span>
                                </div>
                                                               
                                <div class="ms-3 display-6 fs-4 d-sm-none">
                                  <span class="btn btn-primary m-1 w-100">Total R$ <?=number_format(($value->total_diaries + $value->total_consumptions), 2 ,',' ,'.')?></span>                                 
                                  <span class="btn btn-danger m-1 text-light w-100">Resta R$ <?=number_format(($value->total_diaries + $value->total_consumptions) - $value->total_payments, 2 ,',' ,'.')?></span>
                                </div> 

                                <div class="ms-3 d-sm-none me-2 w-100">
                                    <button type="button" class="btn btn-outline-info w-100 dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                    <div class="dropdown-menu">                                    
                                      <button type="button" class="btn btn-info m-1" onclick="openModelItens('<?=$value->uuid?>')">
                                        <span class="fs-6 icon-plus-circle">Produtos</span>
                                      </button>
                                      <a class="btn btn-light m-1" href="\reserva\<?=$value->uuid?>\editar">
                                        <span class="fs-6 icon-edit">Editar</span>
                                      </a> 
                                      <?php if (is_null(reservaFilteredById($value->id_reserva, $data['reservas']))) {?>                                      
                                            <button type="button" class="btn btn-success m-1" onclick="openModelPayments('<?=$value->uuid?>')">
                                                <span class="fs-6 icon-dollar-sign">Pagamento</span>
                                            </button>
                                        <? }?>
                                      <!-- <button type="button" class="btn btn-danger m-1" onclick="openModelDiaries('<?=$value->uuid?>')">
                                        <span class="fs-6 icon-login"> Check-out</span>
                                      </button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalItems" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="modalDiariesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDiariesLabel">
                   Adicionar Produtos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2 d-flex align-items-end justify-content-between">
                    <h5>Produtos Adicionados</h5>
                    <button class="btn btn-danger" id="get-checked-values-items" disabled>Delete Selecionados</button>
                </div>
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                          <thead>
                            <tr>                           
                              <th>Produto</th>                              
                              <th class="d-none-sm">Data</th> 
                              <th>Valor</th>
                              <th>Qtde.</th>                              
                              <th>Ações</th>
                              <th>
                                <input type="checkbox" id="checkAllItems"/>
                              </th>
                            </tr>
                          </thead>
                          <tbody id="listItems">
                            
                          </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
            <div class="container mt-2">
                <form action="" id="form_inserir_items" method="post">
                    <input type="hidden" name="id" id="product_id" disabled>
                    <div class="row gx-3">
                        <div class="col-6">
                            <select name="product_id" id="product" class="form-control">
                              <?php foreach($data['products'] as $product){ ?>
                                <option value="<?=$product->id?>"><?=$product->name?></option>
                               <? } ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <input type="number" name="quantity" id="quantity" step="0" value="1" min="1" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Fechar
                </button>
                <button type="button" id="btn_inserir_itens" class="btn btn-primary">
                    Inserir Produto
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPaymentsItems" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="modalDiariesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPaymentsItemsItensLabel">
                   Adicionar Pagamentos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2 d-flex align-items-end justify-content-between">
                    <h5>Pagamento Adicionados</h5>
                    <button class="btn btn-danger" id="get-checked-values-payments-itens" disabled>Delete Selecionados</button>
                </div>
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                          <thead>
                            <tr>                           
                              <th>Tipo</th>                              
                              <th>Data</th> 
                              <th>Valor</th>                             
                              <th>Ações</th>
                              <th>
                                <input type="checkbox" id="checkAllPaymentsItems"/>
                              </th>
                            </tr>
                          </thead>
                          <tbody id="listPaymentsItems">
                            
                          </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
            <div class="container mt-2">
                <form action="" id="form_inserir_payments_itens" method="post">
                    <input type="hidden" name="id" id="payment_id" disabled>
                    <div class="row gx-3">
                        <div class="col-5">
                            <select name="type_payment" id="type_payment" class="form-control">
                              <option value="Dinheiro">Dinheiro</option>
                              <option value="Cartão Crédito">Cartão Crédito</option>
                              <option value="Cartão Débito">Cartão Débito</option>
                              <option value="Pix">Pix</option>
                              <option value="Cortesia">Cortesia</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <input type="number" name="payment_amount" id="payment_amount" step="0.01" value="0.00" min="0" class="form-control">
                        </div>
                        <div class="col-4">
                            <input type="date" name="dt_payment" id="dt_payment" class="form-control" value="<?=date('Y-m-d')?>">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Fechar
                </button>
                <button type="button" id="btn_inserir_payments_itens" class="btn btn-primary">
                    Inserir Pagamentos
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>

    <script>
          function listConsumptionsItens(data) {  
              if (!data) {
                  return;
              }

              $('#listItems').empty();

              data.forEach((value) => {
                  let created = formatDateWithHour(value.created_at);
                  let tr = "<tr>";
                  tr += `<td>${JSON.parse(value.products).name}</td>`;     
                  tr += `<td class="d-none-sm">${created}</td>`;       
                  tr += `<td>${formatCurrency(value.amount_item)}</td>`;            
                  tr += `<td>${value.quantity}</td>`;
                  tr += `<td>
                      <a class="btn btn-primary m-1" onclick="editarItem('${value.uuid}')"><span class="icon-edit"></span></a>
                      <a class="btn btn-danger m-1" onclick="deletaItem('${value.uuid}')"><span class="icon-trash"></span></a>
                  </td>`;
                  tr += `<td> <input type="checkbox" onclick="checkBox()"; class="form-check m-0" value="${value.uuid}" /></td>`;
                  tr += `</tr>`;

                  $('#listItems').append(tr);
              })
          }

          function openModelItens(params) {
              showData(`/vendas/${params}/items`)
              .then((result) => {
                  $('#checkAllItems').val(params);
                  listConsumptionsItens(result)
                  $('#modalItems').modal('show');
              });
          }   

          $('#checkAllItems').click(function() {
              $('#listItems .form-check').prop('checked', this.checked);
              
              $('#get-checked-values-items').attr('disabled', !this.checked);
          });

          function getCheckedValuesItens() {
            let selectedValues = [];

            $('#listItems .form-check:checked').each(function() {
              selectedValues.push($(this).val());
            });

            return selectedValues;
          }

          $('#get-checked-values-items').click(function() {
            const values = getCheckedValuesItens();
            if (values.length === 0) {
              $('#get-checked-values-items').attr('disabled', true);
            }

            Swal.fire({
                  title: "Tem certeza que deseja deletar estes dados?",
                  text: "A deleção é irrevesivel, não consiguirá reater estes daddos novamente",
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#3085d6",
                  cancelButtonColor: "#d33",
                  confirmButtonText: "Sim, deletar!",
                  cancelButtonText: 'Cancelar',
                  }).then((result) => {
                  if (result.isConfirmed) {
                      let params = $('#checkAllItems').val();
                      deleteData(`/vendas/${params}/items?data=${values}`)
                      .then((result) => {
                          showMessage(result, 'success');
                          showData(`/vendas/${params}/items`)
                          .then((result) => {
                              $('#checkAllItems').val(params);
                              listConsumptionsItens(result)
                              $('#modalItems').modal('show');
                          });
                      });
                  }
              });
          });

          function checkBox() {
              $('#get-checked-values-items').attr('disabled', false);
          }

          $('#btn_inserir_itens').click(function() {
              let params = $('#checkAllItems').val();
              let id = $('#product_id').val();
              if (id === '') {
                  createData(`/vendas/${params}/items`, new FormData(document.getElementById("form_inserir_items"),))
                  .then((result) => {
                      showMessage(result, 'success');
                      showData(`/vendas/${params}/items`)
                      .then((result) => {
                          $('#checkAllItems').val(params);
                          listConsumptionsItens(result)
                          $('#modalItems').modal('show');
                      });
                  });
                  return;
              }
              updateDataWithData(`/vendas/${params}/items/${id}`, new FormData(document.getElementById("form_inserir_items"),))
              .then((result) => {
                  showMessage(result, 'success');
                  showData(`/vendas/${params}/items`)
                  .then((result) => {
                      $('#checkAllItems').val(params);
                      listConsumptionsItens(result)
                      $('#modalItems').modal('show');
                  });
              });
          });

          function editarItem(item) 
          {   
              let params = $('#checkAllItems').val();
              showData(`/vendas/${params}/items/${item}`)
              .then((result) => {
                $('#product_id').val(item);
                $('#product').val(result.id_produto);                
                $('#quantity').val(result.quantity);
                $('#btn_inserir_itens').text('Atualizar Produto');       
              });
          }

          
          function deletaItem(item) {
            Swal.fire({
                  title: "Tem certeza que deseja deletar estes dados?",
                  text: "A deleção é irrevesivel, não consiguirá reater estes daddos novamente",
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#3085d6",
                  cancelButtonColor: "#d33",
                  confirmButtonText: "Sim, deletar!",
                  cancelButtonText: 'Cancelar',
                  }).then((result) => {
                  if (result.isConfirmed) {
                      let params = $('#checkAllItems').val();
                      deleteData(`/vendas/${params}/items/${item}`)
                      .then((result) => {
                          showMessage(result, 'success');
                          showData(`/vendas/${params}/items`)
                          .then((result) => {
                              $('#checkAllItems').val(params);
                              listConsumptionsItens(result)
                              $('#modalItems').modal('show');
                          });
                      });
                  }
              });
          }

    </script>

    <script>
          function listConsumptionsPayments(data) {  
              if (!data) {
                  return;
              }

              $('#listPaymentsItems').empty();

              data.forEach((value) => {
                  let created = formatDate(value.dt_payment);
                  let tr = "<tr>";
                  tr += `<td>${value.type_payment}</td>`;     
                  tr += `<td>${created}</td>`;       
                  tr += `<td>${formatCurrency(value.payment_amount)}</td>`;            
                  tr += `<td>
                      <a class="btn btn-primary m-1" onclick="editar('${value.uuid}')"><span class="icon-edit"></span></a>
                      <a class="btn btn-danger m-1" onclick="deleteItem('${value.uuid}')"><span class="icon-trash"></span></a>
                  </td>`;
                  tr += `<td> <input type="checkbox" onclick="checkBox()"; class="form-check m-0" value="${value.uuid}" /></td>`;
                  tr += `</tr>`;

                  $('#listPaymentsItems').append(tr);
              })
          }

          function openModelPayments(params) {
              showData(`/vendas/${params}/pagamento`)
              .then((result) => {
                  $('#checkAllPaymentsItems').val(params);
                  listConsumptionsPayments(result)
                  $('#modalPaymentsItems').modal('show');
              });
          }   

          $('#checkAllPaymentsItems').click(function() {
              $('#listPaymentsItems .form-check').prop('checked', this.checked);
              
              $('#get-checked-values-payments-itens').attr('disabled', !this.checked);
          });

          function getCheckedValuesPayments() {
            let selectedValues = [];

            $('#listPaymentsItems .form-check:checked').each(function() {
              selectedValues.push($(this).val());
            });

            return selectedValues;
          }

          $('#get-checked-values-payments-itens').click(function() {
            const values = getCheckedValuesPayments();
            if (values.length === 0) {
              $('#get-checked-values-payments-itens').attr('disabled', true);
            }

            Swal.fire({
                  title: "Tem certeza que deseja deletar estes dados?",
                  text: "A deleção é irrevesivel, não consiguirá reater estes daddos novamente",
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#3085d6",
                  cancelButtonColor: "#d33",
                  confirmButtonText: "Sim, deletar!",
                  cancelButtonText: 'Cancelar',
                  }).then((result) => {
                  if (result.isConfirmed) {
                      let params = $('#checkAllPaymentsItems').val();
                      deleteData(`/vendas/${params}/pagamento?data=${values}`)
                      .then((result) => {
                          showMessage(result, 'success');
                          showData(`/vendas/${params}/pagamento`)
                          .then((result) => {
                              $('#checkAllPaymentsItems').val(params);
                              listConsumptionsPayments(result)
                              $('#modalPaymentsItems').modal('show');
                          });
                      });
                  }
              });
          });

          function checkBox() {
              $('#get-checked-values-payments-itens').attr('disabled', false);
          }

          $('#btn_inserir_payments_itens').click(function() {
              let params = $('#checkAllPaymentsItems').val();
              let id = $('#payment_id').val();
              if (id === '') {
                  createData(`/vendas/${params}/pagamento`, new FormData(document.getElementById("form_inserir_payments_itens"),))
                  .then((result) => {
                      showMessage(result, 'success');
                      showData(`/vendas/${params}/pagamento`)
                      .then((result) => {
                          $('#checkAllPaymentsItems').val(params);
                          listConsumptionsPayments(result)
                          $('#modalPaymentsItems').modal('show');
                      });
                  });
                  return;
              }
              updateDataWithData(`/vendas/${params}/pagamento/${id}`, new FormData(document.getElementById("form_inserir_payments_itens"),))
              .then((result) => {
                  showMessage(result, 'success');
                  showData(`/vendas/${params}/pagamento`)
                  .then((result) => {
                      $('#checkAllPaymentsItems').val(params);
                      listConsumptionsPayments(result)
                      $('#modalPaymentsItems').modal('show');
                  });
              });
          });

          function editar(item) {   
              let params = $('#checkAllPaymentsItems').val();
              showData(`/vendas/${params}/pagamento/${item}`)
              .then((result) => {
                $('#payment_id').val(item);     
                $('#payment_amount').val(result.payment_amount);                     
                $('#type_payment').val(result.type_payment);
                $('#btn_inserir_payments_itens').text('Atualizar Pagamentos');       
              });
          }

          
          function deleteItem(item) {
            Swal.fire({
                  title: "Tem certeza que deseja deletar estes dados?",
                  text: "A deleção é irrevesivel, não consiguirá reater estes daddos novamente",
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#3085d6",
                  cancelButtonColor: "#d33",
                  confirmButtonText: "Sim, deletar!",
                  cancelButtonText: 'Cancelar',
                  }).then((result) => {
                  if (result.isConfirmed) {
                      let params = $('#checkAllPaymentsItems').val();
                      deleteData(`/vendas/${params}/pagamento/${item}`)
                      .then((result) => {
                          showMessage(result, 'success');
                          showData(`/vendas/${params}/pagamento`)
                          .then((result) => {
                              $('#checkAllPaymentsItems').val(params);
                              listConsumptionsPayments(result)
                              $('#modalPaymentsItems').modal('show');
                          });
                      });
                  }
              });
          }

    </script>
