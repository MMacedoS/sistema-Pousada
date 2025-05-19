<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?=APP_NAME?></title>

    <link rel="shortcut icon" href="<?=URL_PREFIX_APP?>/Public/assets/images/ico-geeduc.png"/>

    <link rel="stylesheet" href="<?=URL_PREFIX_APP?>/Public/assets/fonts/icomoon/style.css" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?=URL_PREFIX_APP?>/Public/assets/css/main.min.css" />
  </head>

  <body class="login-bg">
    <!-- Container start -->
    <div class="container p-0">
      <!-- Row start -->
      <div class="row g-0">
        <div class="col-xl-6 col-lg-12"></div>
        <div class="col-xl-6 col-lg-12">
          <!-- Row start -->
          <div class="row align-items-center justify-content-center">
            <div class="col-xl-8 col-sm-4 col-12">
              <form action="\recuperar\<?=$solicitation->uuid?>" method="POST" class="my-5">
                <div class="bg-white p-5 rounded-4">
                  <div class="login-form">
                    <a href="\" class="mb-4 d-flex">
                      <img src="<?=URL_PREFIX_APP?>/Public/assets/images/ico-geeduc.png" class="img-fluid login-logo" alt="Admin Dashboards" />
                    </a>
                    <h5 class="fw-light mb-4 lh-2">
                    Para acessar sua conta, digite a nova senha
                    durante o processo de alteração.
                    </h5>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                              <label for="passwordNew" class="form-label">Nova senha</label>
                              <input type="password" class="form-control" id="passwordNew" name="password" required/>
                            </div>
                          </div>              
                          <div class="col-sm-6">
                            <div class="mb-3">
                              <label for="passwordConfirm" class="form-label">Confirme nova senha</label>
                              <input type="password" class="form-control" id="passwordConfirm" name="password_confirm" required/>
                              <small id="passwordError" class="text-danger" style="display: none;">As senhas não coincidem.</small>
                            </div>
                          </div>        
                    </div>
                    <div class="d-grid py-2">
                      <button type="submit" class="btn btn-lg btn-primary">
                        Alterar
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- Row end -->
        </div>
      </div>
    </div>
    <!-- Container end -->
  </body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const passwordNew = document.getElementById('passwordNew');
        const passwordConfirm = document.getElementById('passwordConfirm');
        const passwordError = document.getElementById('passwordError');

        const validatePasswords = () => {
        if (passwordNew.value && passwordConfirm.value) {
            if (passwordNew.value === passwordConfirm.value) {
            passwordError.style.display = 'none';
            passwordNew.classList.remove('is-invalid');
            passwordConfirm.classList.remove('is-invalid');
            passwordNew.classList.add('is-valid');
            passwordConfirm.classList.add('is-valid');
            } else {
            passwordError.style.display = 'block';
            passwordNew.classList.add('is-invalid');
            passwordConfirm.classList.add('is-invalid');
            passwordNew.classList.remove('is-valid');
            passwordConfirm.classList.remove('is-valid');
            }
        } else {
            passwordError.style.display = 'none';
            passwordNew.classList.remove('is-valid', 'is-invalid');
            passwordConfirm.classList.remove('is-valid', 'is-invalid');
        }
        };

        passwordNew.addEventListener('input', validatePasswords);
        passwordConfirm.addEventListener('input', validatePasswords);

        passwordNew.addEventListener('blur', validatePasswords);
        passwordConfirm.addEventListener('blur', validatePasswords);
    });
</script>