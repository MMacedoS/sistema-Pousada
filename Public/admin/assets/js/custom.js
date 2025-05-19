/***********
***********
***********
	Bootstrap JS 
***********
***********
***********/

// Tooltip
var tooltipTriggerList = [].slice.call(
	document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Popover
var popoverTriggerList = [].slice.call(
	document.querySelectorAll('[data-bs-toggle="popover"]')
);
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
	return new bootstrap.Popover(popoverTriggerEl);
});

$('#representative').on('click', function() {
	$('#div_representante').toggleClass('d-none');
});

function createData(url, data) {
	showLoader();
	return $.ajax({
	  url: url,
	  method: 'POST',
	  data: data,
	  dataType: 'JSON',
	  contentType: false,
	  cache: false,
	  processData:false,
	}).catch(function(error){
	   showErrorMessage(error.message);
	  hideLoader();
	});
  }

  // Função para atualizar um registro existente
  function updateData(url, data) {
	showLoader();
	$.ajax({
	  url: url,
	  method: 'POST',
	  data: data,
	  dataType: 'JSON',
	  contentType: false,
	  cache: false,
	  processData:false,
	  success: function(response) {
		showSuccessMessage('Registro atualizado com sucesso!');
		hideLoader();
	  },
	  error: function(error) {
		console.error('Erro ao atualizar registro:', error);
		hideLoader();
	  }
	});
  }

  function sendRequestWithMethod(url, data, method) {
	showLoader();
	return $.ajax({
		url: url,
		method: method,
		data: data,
		contentType: false,
		cache: false,
		processData:false,
		dataType: 'json',
	  }).catch(function(error){
		console.error('Erro ao obter registro:', error);
		hideLoader();
	  });
  }

	// Função para exibir um registro
	function updateDataWithData(url, data) {
	  showLoader();
	  return $.ajax({
		url: url,
		method:'POST',
		data: data,
		contentType: false,
		cache: false,
		processData:false,
		dataType: 'json',
	  }).catch(function(error){
		console.error('Erro ao obter registro:', error);
		hideLoader();
	  });
	}

  // Função para exibir um registro
  function showData(url) {
	showLoader();
	return $.ajax({
	  url: url,
	  method:'GET',
	  processData: false,
	  dataType: 'json',
	}).catch(function(error){
	  console.error('Erro ao obter registro:', error);
	  hideLoader();
	});
  }

  // Função para exibir um registro
  function showDataWithData(url, data) {
	showLoader();
	return $.ajax({
	  url: url,
	  method: 'POST',
	  data: data,
	  dataType: 'JSON',
	  processData: false,
	  contentType: false,
	  cache: false,
	  }).catch(function(error){
		console.error('Erro ao obter registro:', error);
		hideLoader();
	  });
  }

  function redirecionarPagina(url) {
	window.location.assign(url);
  }

  // Função para excluir um registro
  function deleteData(url) {
	showLoader();
	return $.ajax({
	  url: url,
	  method: 'DELETE',
	  contentType: 'application/json',
	  dataType: 'json'
	}).catch(function(error){
		console.error('Erro ao obter registro:', error);
		hideLoader();
	}).then(() => {
		showSuccessMessage('Registro excluído com sucesso!');			
	});
  }

  function deleteDataWithData(url, data) {
	showLoader();
	$.ajax({
	  url: url,
	  method: 'POST',
	  data: data,
	  contentType: 'application/json',
	  dataType: 'json',
	  success: function(response) {
		showSuccessMessage('Registro excluído com sucesso!');
		hideLoader();
	  },
	  error: function(error) {
		console.error('Erro ao excluir registro:', error);
		hideLoader();
	  }
	});
  }

  function showLoader() {
	$('<div class="spinner"></div>').appendTo('body');
  }

  function hideLoader() {
	$('.spinner').remove();
  }

  function showSuccessMessage(message) {
	Swal.fire({
	  icon: 'success',
	  title: 'Sucesso!',
	  text: message,
	  confirmButtonColor: '#3085d6',
	  confirmButtonText: 'Ok'
	}).then(()=>{
		refreshPage();
	});
  }

  function showMessage(result, icon) {
	swal.fire({
		icon: icon,
		title: result.title,
		text: result.message,
		confirmButtonColor: '#3085d6',
		confirmButtonText: 'Ok'
		})
  }
  
  function showWarningMessage(message) {
	Swal.fire({
	  icon: 'warning',
	  title: 'Atenção!',
	  text: message,
	  confirmButtonColor: '#3085d6',
	  confirmButtonText: 'Ok'
	}).then(()=>{
	});
  }
  
  function showErrorMessage(message) {
	Swal.fire({
	  icon: 'error',
	  title: 'Erro!',
	  text: message,
	  confirmButtonColor: '#3085d6',
	  confirmButtonText: 'Ok'
	}).then(()=>{
		hideLoader();
	});
  }
  
  function refreshPage() {
	location.reload(true);
  }
  
  function formatDate(dateString) {
	const date = dateString.split('-');
		return ''+date[2]+ '/' + date[1] + '/' + date[0];
	// const options = { year: 'numeric', month: 'long', day: 'numeric' };
	// const date = new Date(dateString);
	// return date.toLocaleDateString('pt-BR', options);
  }

  function formatCurrency(value) {
	  return  parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  }

  function formatDateTime(value) {
	const date = value.split('-');
		return ''+date[2]+ '/' + date[1] + '/' + date[0];
  }

  function formatDateWithHour(value)
  {
	  const date = value.split(' ');
	  return formatDateTime(date[0]) + ' ' + date[1];
  }

  function prepareTipo(value)
  {
	  var res = "";
	  switch (value) {
		  case '1':
			  res = "Dinheiro";
		  break;
		  case '2':
			  res = "Cartão de Crédito"
		  break;
		  case '3':
			 res =  "Cartão de Débito"
		  break;
		  case '4':
			 res =  "Déposito/PIX"
		  break;
	  }

	  return res;
  }

  function prepareStatus(value)
  {
	  var res = "";
	  switch (value) {
		  case '1':
			  res = "Reservada";
		  break;
		  case '2':
			  res = "Confirmada"
		  break;
		  case '3':
			 res =  "Hospedada"
		  break;
		  case '4':
			 res =  "Finalizada"
		  break;
		  case '5':
			res =  "Cancelada"
		  break;
	  }

	  return res;
  }

  function calculaPagamento(data)
	{
		var valor = 0;
		data.forEach(element => {
			valor += parseFloat(element.valorPagamento);
		});

		return valor;
	}

	function prepareSelect(data, select_id, selected = '')
	{
		console.log(selected);
		$(select_id).selectize()[0].selectize.destroy();
		let $select = $(select_id).selectize({
			maxItems: 1,
			valueField: 'id',
			labelField: 'title',
			searchField: 'title',
			options: 
			   data,
				create: true,
		});

		var control = $select[0].selectize;
		control.setValue(selected);
	}

	function imprimir() {
	  var conteudoDiv = document.getElementById("contents_inputs").innerHTML;
	  var janela = window.open('', '', 'width=1000,height=600');
	  janela.document.write('<html><head><title>Impressão</title><style type="text/css" media="print">' +
	  'body { margin: 0;padding: 0;} table {width: 100% !important; border-collapse: collapse;} table, th, td { border: 1px solid #ccc;}' + 
	  'th, td {padding: 8px;text-align: left;}  tr td:last-child {display: none;} ' + 
	  ' .row {display: flex;flex-wrap: wrap;margin-right: -0.75rem; margin-left: -0.75rem;} .col-12 {flex: 0 0 100%; max-width: 100%;} .col-4 { flex: 0 0 33.33333%;  max-width: 33.33333%;  } .col-8 {flex: 0 0 66.66667%; max-width: 66.66667%; } .text-center {  text-align: center!important;} .col-3 { flex: 0 0 25%; max-width: 25%;} ' + 
	  '.pl-2, .px-2 { padding-left: 0.75rem!important;  } .w-100 {width: 100% !important;}</style></head><body>');
	  janela.document.write(conteudoDiv);
	  janela.document.write('</body></html>');
	  janela.print();
	  janela.close();
  }

  function prepareSelector(data, select_id) {
	
	data.map((item) =>{
	  let opcao = $('<option>', {
		value: item.id,
		text: item.nome
	  });
	  $(select_id).append(opcao);
	});
  }