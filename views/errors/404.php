<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - Erreur 404</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .error-container {
            text-align: center;
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        .error-title {
            font-size: 24px;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        .error-message {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        .btn-home {
            display: inline-block;
            background-color: #3498db;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.30s ease;
        }
        .btn-home:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-title">Page Introuvable</h1>
        <p class="error-message">
            Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
        </p>
        <a href="<?= defined('URL') ? URL : '/' ?>" class="btn-home">Retour à l'accueil</a>
    </div>
</body>
</html>