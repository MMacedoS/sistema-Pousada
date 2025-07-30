<?php

// $router->create('GET', '/customers', [$clienteController, 'index'], $auth);
// $router->create('DELETE', '/customer/{id}', [$clienteController, 'delete'], $auth);
// $router->create('GET', '/customer/{id}', [$clienteController, 'edit'], $auth);
// $router->create('POST', '/customer/{id}', [$clienteController, 'update'], $auth);
// $router->create('GET', '/customer', [$clienteController, 'create'], $auth);
// $router->create('POST', '/customer', [$clienteController, 'store'], $auth);


// $router->create('POST', '/reserva/{id}/deletar', [$reservaController, 'delete'], $auth);
// $router->create('GET', '/reserva/{id}/editar', [$reservaController, 'edit'], $auth);
// $router->create('POST', '/reserva/{id}/upt', [$reservaController, 'update'], $auth);
// $router->create('POST', '/reserva/add/', [$reservaController, 'store'], $auth);
// $router->create('GET', '/reserva/criar', [$reservaController, 'create'], $auth);
// $router->create('GET', '/reservas', [$reservaController, 'index'], $auth);
// $router->create('GET', '/reserva/', [$reservaController, 'index'], $auth);
// $router->create('POST', '/reserva/apartamentos', [$reservaController, 'findAvailableApartments'], $auth);
// $router->create('GET', '/checkin/reserva', [$reservaController, 'checkin'], $auth);
// $router->create('POST', '/checkin/{id}/reserva', [$reservaController, 'executeCkeckin'], $auth);
// $router->create('GET', '/reserva/checkout', [$reservaController, 'checkout'], $auth);
// $router->create('GET', '/maps', [$reservaController, 'maps'], $auth);
// $router->create('POST', '/maps', [$reservaController, 'reserve_by_maps'], $auth);

// $router->create('GET', '/reservas/{token}/diarias/atualizar',[$diariaController, 'generateDaily']);

// $router->create('GET', '/consumos/diaria', [$diariaController, 'index'], $auth);
// $router->create('GET', '/consumos/reserva/{id}/diarias', [$diariaController, 'indexJsonByReservaUuid'], $auth);
// $router->create('POST', '/consumos/reserva/{id}/diarias', [$diariaController, 'storeByJson'], $auth);
// $router->create('GET', '/consumos/reserva/{id}/diarias/{diaria_id}', [$diariaController, 'showByJson'], $auth);
// $router->create('POST', '/consumos/reserva/{id}/diarias/{diaria_id}', [$diariaController, 'updateByJson'], $auth);
// $router->create('DELETE', '/consumos/reserva/{id}/diarias/{diaria_id}', [$diariaController, 'destroy'], $auth);
// $router->create('DELETE', '/consumos/reserva/{id}/diarias', [$diariaController, 'destroyAll'], $auth);

// $router->create('GET', '/consumos/produto', [$consumoController, 'index'], $auth);
// $router->create('GET', '/consumos/reserva/{id}/produto', [$consumoController, 'indexJsonByReservaUuid'], $auth);
// $router->create('POST', '/consumos/reserva/{id}/produto', [$consumoController, 'storeByJson'], $auth);
// $router->create('GET', '/consumos/reserva/{id}/produto/{consumo_id}', [$consumoController, 'showByJson'], $auth);
// $router->create('POST', '/consumos/reserva/{id}/produto/{consumo_id}', [$consumoController, 'updateByJson'], $auth);
// $router->create('DELETE', '/consumos/reserva/{id}/produto/{consumo_id}', [$consumoController, 'destroy'], $auth);
// $router->create('DELETE', '/consumos/reserva/{id}/produto', [$consumoController, 'destroyAll'], $auth);

// $router->create('GET', '/consumos/pagamento', [$pagamentoController, 'index'], $auth);
// $router->create('GET', '/consumos/reserva/{id}/pagamento', [$pagamentoController, 'indexJsonByReservaUuid'], $auth);
// $router->create('POST', '/consumos/reserva/{id}/pagamento', [$pagamentoController, 'storeByJson'], $auth);
// $router->create('GET', '/consumos/reserva/{id}/pagamento/{pagamento_id}', [$pagamentoController, 'showByJson'], $auth);
// $router->create('POST', '/consumos/reserva/{id}/pagamento/{pagamento_id}', [$pagamentoController, 'updateByJson'], $auth);
// $router->create('DELETE', '/consumos/reserva/{id}/pagamento/{pagamento_id}', [$pagamentoController, 'destroy'], $auth);
// $router->create('DELETE', '/consumos/reserva/{id}/pagamento', [$pagamentoController, 'destroyAll'], $auth);

// $router->create('GET', '/vendas', [$vendaController, 'index'], $auth);
// // $router->create('GET', '/consumos/venda/list', [$produtoController, 'indexJsonWithoutPaginate'], $auth);
// $router->create('GET', '/vendas/criar', [$vendaController, 'create'], $auth);
// $router->create('POST', '/vendas', [$vendaController, 'store'], $auth);
// $router->create('GET', '/vendas/{id}', [$vendaController, 'edit'], $auth);
// $router->create('POST', '/vendas/{id}', [$vendaController, 'update'], $auth);
// // $router->create('POST', '/produtos/{produto_id}/remove', [$produtoController, 'destroy'], $auth);

// $router->create('GET', '/vendas/{id}/items', [$itemVendaController, 'indexJsonByReservaUuid'], $auth);
// $router->create('POST', '/vendas/{id}/items', [$itemVendaController, 'storeByJson'], $auth);
// $router->create('GET', '/vendas/{id}/items/{consumo_id}', [$itemVendaController, 'showByJson'], $auth);
// $router->create('POST', '/vendas/{id}/items/{consumo_id}', [$itemVendaController, 'updateByJson'], $auth);
// $router->create('DELETE', '/vendas/{id}/items/{consumo_id}', [$itemVendaController, 'destroy'], $auth);
// $router->create('DELETE', '/vendas/{id}/items', [$itemVendaController, 'destroyAll'], $auth);

// $router->create('GET', '/vendas/{id}/pagamento', [$pagamentoController, 'indexJsonWithSaleByUuid'], $auth);
// $router->create('POST', '/vendas/{id}/pagamento', [$pagamentoController, 'storeWithSaleByJson'], $auth);
// $router->create('GET', '/vendas/{id}/pagamento/{pagamento_id}', [$pagamentoController, 'showWithSaleByJson'], $auth);
// $router->create('POST', '/vendas/{id}/pagamento/{pagamento_id}', [$pagamentoController, 'updateWithSaleByJson'], $auth);
// $router->create('DELETE', '/vendas/{id}/pagamento/{pagamento_id}', [$pagamentoController, 'destroyWithSale'], $auth);
// $router->create('DELETE', '/vendas/{id}/pagamento', [$pagamentoController, 'destroyWithSaleAll'], $auth);

// $router->create('GET', '/produtos', [$produtoController, 'index'], $auth);
// $router->create('GET', '/produtos/list', [$produtoController, 'indexJsonWithoutPaginate'], $auth);
// $router->create('GET', '/produtos/criar', [$produtoController, 'create'], $auth);
// $router->create('POST', '/produtos', [$produtoController, 'store'], $auth);
// $router->create('GET', '/produtos/{produto_id}', [$produtoController, 'edit'], $auth);
// $router->create('POST', '/produtos/{produto_id}', [$produtoController, 'update'], $auth);
// $router->create('POST', '/produtos/{produto_id}/remove', [$produtoController, 'destroy'], $auth);
// // $router->create('DELETE', '/produtos', [$produtoController, 'destroyAll'], $auth);

// $router->create('POST', '/caixa/create', [$caixaController, 'storeByJson'], $auth);
