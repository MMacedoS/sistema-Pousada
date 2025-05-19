$(document).ready(function() {
	const phoneInput = $('#phone');
	const typeDocInput = $('#type_doc');
	const docInput = $('#doc');
	const cnpjCompanyInput = $('#cnpj_company');
	const phoneCompanyInput = $('#phone_company');
	const emailInput = $("#email");
	const nameInput = $("#name");
  const nameMotherInput = $("#mother");
  const nameFatherInput = $("#father");

	const clearError = (input) => {
		$(input).removeClass("is-invalid");
		$(input).addClass("is-valid");
		$(`#${input.attr("id")}_error`).text("");
	}

	const validarCPF = (inputCPF) => {
    let cpf = inputCPF.replace(/\D/g, '');

    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
        return false;
    }

    function calcularDigito(base) {
        let soma = 0;
        for (let i = 0; i < base.length; i++) {
            soma += parseInt(base[i]) * (base.length + 1 - i);
        }
        let resto = (soma * 10) % 11;
        return resto === 10 ? 0 : resto;
    }

    let primeiroDigito = calcularDigito(cpf.substring(0, 9));
    let segundoDigito = calcularDigito(cpf.substring(0, 10));

    return primeiroDigito === parseInt(cpf[9]) && segundoDigito === parseInt(cpf[10]);
	}

	const validPhone = () => {
		phoneInput.on('input', function() {
			var telefone = $(this).val().replace(/\D/g, '');
			telefone = telefone.substring(0, 11);

			if (telefone.length > 2) {
					telefone = `(${telefone.substring(0, 2)}) ${telefone.substring(2)}`;
			}
			if (telefone.length > 9) {
					telefone = telefone.replace(/(\(\d{2}\)) (\d{1})(\d{4})/, '$1 $2 $3-');
			}

			$(this).val(telefone);
			var regex = /^\(\d{2}\) \d \d{4}-\d{4}$/;

			if (!regex.test(telefone)) {
					$(this).addClass('is-invalid');
					$(this).siblings('.invalid-feedback').text('Telefone inválido');
					return;
			} 

			clearError($(this));
		});
	}

	const validTypeDoc = () => {
		typeDocInput.on('change', function() {
			var type_doc = $(this).val();
			if (type_doc == '') {
				$(this).addClass('is-invalid');
				$('#type_doc_error').text('Selecione um tipo de documento');
				return;
			} 
			
			clearError($(this));
		});
	}

	const validDoc = () => {
		docInput.on('input', function() {
			var doc = $(this).val().replace(/\D/g, '');
			var type_doc = typeDocInput.val();
			
			if (type_doc == 'CPF') {
				var regex = /^\d{11}$/;
				if (!regex.test(doc)) {
					$(this).addClass('is-invalid');
					$('#doc_error').text('CPF inválido');
					return;
				} 
				
				var formattedDoc = doc.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4');
				let cpf = $(this).val(formattedDoc);

				if (!validarCPF(cpf.val())) {
					$(this).addClass('is-invalid');
					$('#doc_error').text('CPF inválido');
					return;
				} 
				clearError($(this));
				return;
			} 
			
			if (type_doc == 'CNH') {
				var regex = /^\d{11}$/;
				if (!regex.test(doc)) {
					$(this).addClass('is-invalid');
					$(`#${$(this).attr("id")}_error`).text('CNH inválido');
					return;
				} 
			 
				clearError($(this));
	
				var formattedDoc = doc.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1 . $2 .$3-$4');
				$(this).val(formattedDoc);
				return;
			} 
			
			if (type_doc == 'RG') {
				var regex = /^\d{10}$/;
				if (!regex.test(doc)) {
					$(this).addClass('is-invalid');
					$(`#${$(this).attr("id")}_error`).text('RG inválido');
					return;
				} 
				
				clearError($(this));

				var formattedDoc = doc.replace(/^(\d{2})(\d{3})(\d{3})(\d{2})$/, '$1 . $2 .$3-$4');
				$(this).val(formattedDoc);
				return;
			}
			
			if (type_doc == 'PASSAPORTE') {
				var regex = /^[A-Z]{2}\d{6}$/;
				if (!regex.test(doc)) {
					$(this).addClass('is-invalid');
					$(`#${$(this).attr("id")}_error`).text('Passaporte inválido');
					return;
				} 
				
				clearError($(this));
			}
		});
	}

	const validCnpjCompany = () => {
		cnpjCompanyInput.on('input', function() {
			var cnpj = $(this).val();
			var regex = /^\d{14}$/;
			
			if (!regex.test(cnpj)) {
				$(this).addClass('is-invalid');
				$(this).siblings('.invalid-feedback').text('cnpj inválido');
				return;
			} 
			
			clearError($(this));
						
			// Adiciona o formato de cnpj
			var formattedCnpj = cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1 $2 $3/$4-$5');
			$(this).val(formattedCnpj);
		});
	}
	
	const validPhoneCompany = () => {
		phoneCompanyInput.on('input', function() {
			var telefone = $(this).val();
			var regex = /^\(?([0-9]{2})\)?[-. ]?([0-9]{1})[-. ]?([0-9]{4,5})[-. ]?([0-9]{4})$/;
			
			if (!regex.test(telefone)) {
				 $(this).addClass('is-invalid');
				$(this).siblings('.invalid-feedback').text('Telefone inválido');
				return;
			} 
			
			clearError($(this));
						
			// Adiciona o formato de telefone
			var formattedPhone = telefone.replace(/^(\d{2})(\d{1})(\d{4,5})(\d{4})$/, '($1) $2 $3-$4');
			$(this).val(formattedPhone);
		});
	}

	const validEmail = () => {
    emailInput.on("blur", function () {
      let email = $(this).val();
      let regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

      if (!regex.test(email)) {
        $(this).addClass("is-invalid");
        $(`#${$(this).attr("id")}_error`).text("E-mail inválido!");
        return;
      }

      clearError($(this));
    });
  }

  const validName = () => {
    nameInput.on("blur", function () {
      let value = $(this).val();

      if(value.length < 1 || value.length > 100){
        $(this).addClass("is-invalid");
        $(`#${$(this).attr("id")}_error`).text("O nome deve ter entre 1 e 100 caracteres");
        return;
      }

      clearError($(this));
    })
  }

  const validNameMother = () => {
    nameMotherInput.on("blur", function () {
      let value = $(this).val();

      if(value.length < 1 || value.length > 100){
        $(this).addClass("is-invalid");
        $(`#${$(this).attr("id")}_error`).text("O nome da mãe deve ter entre 1 e 100 caracteres");
        return;
      }

      clearError($(this));
    })
  }

  const validNameFather = () => {
    nameFatherInput.on("blur", function () {
      let value = $(this).val();

      if(value.length < 1 || value.length > 100){
        $(this).addClass("is-invalid");
        $(`#${$(this).attr("id")}_error`).text("O nome do pai deve ter entre 1 e 100 caracteres");
        return;
      }

      clearError($(this));
    })
  }

	validPhone();
	validTypeDoc();
  validDoc();
	validCnpjCompany();
	validPhoneCompany();
	validEmail();
	validName();
	validNameMother();
	validNameFather();

	$('#customers').select2();
	  const bankMapping = {
		"001": "Banco do Brasil",
		"104": "Caixa Econômica Federal",
		"237": "Bradesco",
		"341": "Itaú",
		"033": "Santander"
	  };
  
	  $("#bankCode").on("change", function () {
		const selectedCode = $(this).val();
		const correspondingBank = bankMapping[selectedCode] || "";
		$("#bankName").val(correspondingBank);
	  });
  
	  $("#bankName").on("change", function () {
		const selectedBank = $(this).val();
		const correspondingCode = Object.keys(bankMapping).find(key => bankMapping[key] === selectedBank) || "";
		$("#bankCode").val(correspondingCode);
	  });

	  $('#plan_id').on('change', function () {
		var selectedValue = $(this).val();
	
		$('#amount').val(selectedValue || '0.0');
		updateTotal();
	  });

	  function updateExpirationDate() {
		var day = parseInt($('#expiration_day').val());
		if (isNaN(day) || day < 1 || day > 31) {
		  $('#expiration_date').val('');
		  return;
		}
	
		var currentDate = new Date();
		var year = currentDate.getFullYear();
		var month = currentDate.getMonth();
	
		var newDate = new Date(year, month, day);
	
		if (newDate.getDate() !== day) {
		  $('#expiration_date').val('');
		  return;
		}
	
		var formattedDate = newDate.toISOString().split('T')[0];
		$('#expiration_date').val(formattedDate);
	  }

	  updateExpirationDate();
	  
	  $('#expiration_day').on('input focus', function () {
		updateExpirationDate();
	  });

	  function updateTotal() {
		const amount = parseFloat($('#amount').val()) || 0;
		const discount = parseFloat($('#discont').val()) || 0;
  
		const total = Math.max(amount - discount, 0); 
		$('#total').val(total.toFixed(2));
	  }
  
	  updateTotal();
  
	  $('#discont').on('input', function () {
		updateTotal();
	  });


	 	const dateInput = document.getElementById("data-frequencia");

        // Restrict selectable dates
        if (dateInput) {
			dateInput.addEventListener("change", () => {
				const selectedDate = new Date(dateInput.value);
				const today = new Date();
				const day = selectedDate.getDay(); // 0: Sunday, 6: Saturday
	
				// Check if the selected date is in the future or is a weekend
				if (selectedDate > today || day === 5 || day === 6) {
					alert("Selecione uma data válida (dias úteis - Segunda até Sexta).");
					dateInput.value = ""; // Reset to empty if invalid
				}
			});
		}
	  
  });