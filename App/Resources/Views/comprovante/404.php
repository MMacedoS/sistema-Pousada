<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante não encontrado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .error-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .error-icon {
            font-size: 64px;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .error-title {
            color: #dc3545;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .error-message {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .btn-back {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h1 class="error-title">Comprovante não encontrado</h1>
        <p class="error-message">
            <?php echo htmlspecialchars($message ?? 'O comprovante solicitado não foi encontrado ou não existe.'); ?>
        </p>
        <a href="javascript:history.back()" class="btn-back">Voltar</a>
    </div>
</body>

</html>