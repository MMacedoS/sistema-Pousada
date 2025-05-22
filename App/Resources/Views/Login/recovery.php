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
              <form action="\recuperar" method="POST" class="my-5">
                <div class="bg-white p-5 rounded-4">
                  <div class="login-form">
                    <a href="index.html" class="mb-4 d-flex">
                      <img src="<?=URL_PREFIX_APP?>/Public/assets/images/ico-geeduc.png" class="img-fluid login-logo" alt="Admin Dashboards" />
                    </a>
                    <h5 class="fw-light mb-4 lh-2">
                    Para acessar sua conta, digite o ID de e-mail
                    que você forneceu durante o processo de registro.
                    </h5>
                    <div class="mb-3">
                      <label class="form-label">Seu e-mail</label>
                      <input type="email" name="email" class="form-control" placeholder="digite seu email" />
                    </div>
                    <div class="d-grid py-2">
                      <button type="submit" class="btn btn-lg btn-primary">
                        Solicitar
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