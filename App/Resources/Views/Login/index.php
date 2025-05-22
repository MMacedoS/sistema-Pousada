<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title><?=$_ENV['APP_NAME']?></title>
        <link rel="shortcut icon" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/assets/images/logo-dark-rm-bg.png"/>
            <!-- *************
                ************ CSS Files *************
            ************* -->
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/animate/animate.css">
    <!--===============================================================================================-->	
        <link rel="stylesheet" type="text/css" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/select2/select2.min.css">
    <!--===============================================================================================-->	
        <link rel="stylesheet" type="text/css" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/css/util.css">
        <link rel="stylesheet" type="text/css" href="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/css/main.css">
    </head>
    <body style="background-color: #666666;">
    <div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form" method="POST" action="/login">
                    <span class="login100-form-title p-b-43">
                        <a class="brand" href="/">
                            <img class="brand-logo-dark" src="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/assets/images/logo-dark-rm-bg.png" alt="" width="100"/>
                        </a>
					</span>	
                    <span class="login100-form-title p-b-43">
						Acessar Sistema
					</span>					
					
					<div class="wrap-input100 validate-input" data-validate = "email é obrigatório e deve ser valido: ex@abc.xyz">
						<input class="input100" type="text" name="email">
						<span class="focus-input100"></span>
						<span class="label-input100">Email</span>
					</div>
					
					
					<div class="wrap-input100 validate-input" data-validate="senha é obrigatória">
						<input class="input100" type="password" name="password">
						<span class="focus-input100"></span>
						<span class="label-input100">Senha</span>
					</div>

					<div class="flex-sb-m w-full p-t-3 p-b-32">
						<div class="contact100-form-checkbox">
							<input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
							<label class="label-checkbox100" for="ckb1">
								Lembrar senha
							</label>
						</div>

						<div>
							<a href="\recuperar" class="txt1">
								Esqueceu a senha?
							</a>
						</div>
					</div>
			

					<div class="container-login100-form-btn">
						<button class="login100-form-btn" type="submit">
							Login
						</button>
					</div>
				</form>

				<div class="login100-more" style="background-image: url('<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/assets/images/products/product3.jpg');">
				</div>
			</div>
		</div>
	</div>
	
	
    <!--===============================================================================================-->
        <script src="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/jquery/jquery-3.2.1.min.js"></script>
    <!--===============================================================================================-->
        <script src="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/animsition/js/animsition.min.js"></script>
    <!--===============================================================================================-->
        <script src="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/bootstrap/js/popper.js"></script>
        <script src="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
        <script src="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/select2/select2.min.js"></script>
    <!--===============================================================================================-->
        <script src="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/daterangepicker/moment.min.js"></script>
        <script src="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/daterangepicker/daterangepicker.js"></script>
    <!--===============================================================================================-->
        <script src="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/vendor/countdowntime/countdowntime.js"></script>
    <!--===============================================================================================-->
        <script src="<?=$_ENV['URL_PREFIX_APP']?>/Public/admin/js/main.js"></script>
    </body>
</html>