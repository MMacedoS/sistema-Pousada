<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?=$_ENV['APP_NAME']?></title>

    <link rel="shortcut icon" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/site/sind/logo_sindsmut.png" />

    <!-- *************
			************ CSS Files *************
		************* -->
    <!-- Icomoon Font Icons css -->
    <link rel="stylesheet" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/assets/fonts/icomoon/style.css" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/assets/css/main.min.css" />

    <!-- *************
			************ Vendor Css Files *************
		************ -->
  </head>

  <body>
    <!-- Container start -->
    <div class="container">
      <div class="row justify-content-center align-items-center vh-100">
        <div class="col-sm-6 col-12">
          <div class="text-warning">
            <h1 class="display-1 fw-bold">Ops!</h1>
            <h6 class="lh-2">Esta pagina não foi encontrada!.</h6>
            <img src="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/assets/images/error.svg" class="img-fluid" alt="Dashboards" />
            <div class="text-end">
              <a href="<?=$_ENV['URL_PREFIX_APP'] . $page ?>" class="btn btn-light rounded-5 px-4 py-3 shadow">Ir para principal</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Container end -->
  </body>

</html>