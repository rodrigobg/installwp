<?php
###################### INSTALL WP RODRIGO PORTO #############################
# Este script pode parecer idiota, mas é apenas pra quem tem                 #
# preguiça de copiar o link do wordpress, subir no FTP e extrair e instalar. #
# Se achou útil faça uma doação kkkk                                         #
# https://api.dicasbrasil.com.br/doar/                                       #
# Linkedin: https://www.linkedin.com/in/rodrigo-porto-1a8b23161/             #
###################### INSTALL WP RODRIGO PORTO #############################
$api_url = 'https://api.wordpress.org/core/version-check/1.7/';
$response = file_get_contents($api_url);
$data = json_decode($response, true);

$latest_version = $data['offers'][0]['current'];
$versions = array_reverse(array_unique(array_column($data['offers'], 'current')));

if (isset($_POST['version'])) {
    $selected_version = $_POST['version'];
    $download_url = "https://wordpress.org/wordpress-$selected_version.zip";
    
    $zip_file = "wordpress-$selected_version.zip";
    file_put_contents($zip_file, fopen($download_url, 'r'));
    
    $zip = new ZipArchive;
    if ($zip->open($zip_file) === TRUE) {
        $zip->extractTo(__DIR__);
        $zip->close();
        unlink($zip_file);

        // Mover arquivos da pasta /wordpress/ para o diretório atual
        $source = __DIR__ . '/wordpress/';
        $files = scandir($source);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                rename($source . $file, __DIR__ . '/' . $file);
            }
        }
        rmdir($source);

        // Capturar a URL de acesso
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $site_url = "$protocol://$host$path";
        
        $install_link = "<a href='$site_url/wp-admin/install.php' class='btn btn-success mt-3'>Iniciar Instalação</a>";
    } else {
        $error = "Erro ao extrair o arquivo.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador do WordPress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
body {
    background: linear-gradient(-45deg, #007bff, #6c757d, #28a745, #ffc107);
    background-size: 400% 400%;
    animation: gradientBG 10s ease infinite;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

@keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

        .card {
            max-width: 500px;
            width: 100%;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px; 
        }
        .logo {
            width: 100px;
            margin-bottom: 15px;
        }
        .form-select, .btn {
            height: 50px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="card text-center">
        <center><img src="https://s.w.org/style/images/about/WordPress-logotype-wmark.png" alt="WordPress" class="logo"></center>
        <h2 class="mb-3">Instalador do WordPress</h2>
        <form method="post">
            <label class="form-label">Escolha a versão:</label>
            <select name="version" class="form-select mb-3">
                <?php foreach ($versions as $version): ?>
                    <option value="<?= $version ?>" <?= $version == $latest_version ? 'selected' : '' ?>>
                        <?= $version ?> <?= $version == $latest_version ? '(Última versão)' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary w-100">Baixar e Instalar</button>
        </form>
        <?php if (isset($install_link)) echo "<div class='mt-3'>$install_link</div>"; ?>
        <?php if (isset($error)) echo "<p class='text-danger mt-3'>$error</p>"; ?>
    </div>
    <div class="card text-center">
      <small><center>Version 1.0.1</center></small>
  </div>
</body>
</html>
