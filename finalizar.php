<?php

mysqli_report(MYSQLI_REPORT_OFF);
//OBS: Caso não funcionar, altere a linha abaixo de "localhost:3307" para "localhost".
$conn = new mysqli("localhost:3307", "root", "", "agendaCar");

$veiculo_id = $_POST['veiculo_id'];
$data = $_POST['data'];
$hora = $_POST['hora'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];

$check = $conn->query("
    SELECT id FROM agendamentos 
    WHERE veiculo_id = $veiculo_id 
      AND data = '$data' 
      AND hora = '$hora'
");

//checa se já tem agendamento no horário selecionado
if ($check->num_rows > 0) {
    $erro = true;
} else {
    $erro = false;
}

//se ja tiver o agendamento no horario então manda este html de erro:
if ($erro) {
?>
<html>
<head>
<meta charset="UTF-8">
<title>Horário Indisponível</title>

<style>
    body { font-family: Arial; background:#f0f0f0; margin:0; }
    .navbar { background:#111; padding:15px 25px; color:white; font-size:22px; font-weight:bold; }
    .container {
        max-width:700px; margin:50px auto; padding:30px;
        background:white; border-radius:12px;
        text-align:center; box-shadow:0 4px 15px rgba(0,0,0,0.15);
    }
    h2 { color:#d9534f; font-size:26px; }
    .btn-voltar {
        margin-top:20px; display:inline-block; padding:12px 22px;
        background:#007bff; color:white; text-decoration:none;
        border-radius:8px; font-size:18px;
    }
    .btn-voltar:hover { background:#005fcc; }
</style>

</head>
<body>

<div class="navbar">🚗 Kevão Veículos</div>

<div class="container">
    <h2>⚠️ Este horário já está agendado!</h2>
    <p style="font-size:18px">Por favor, selecione outro horário disponível.</p>
    <a class="btn-voltar" href="main.php">Escolher outro horário</a>
</div>

</body>
</html>
<?php
exit;
}

$conn->query("INSERT INTO clientes (nome, email, telefone) VALUES ('$nome', '$email', '$telefone')");
$cliente_id = $conn->insert_id;

$conn->query("INSERT INTO agendamentos (veiculo_id, cliente_id, data, hora)
              VALUES ($veiculo_id, $cliente_id, '$data', '$hora')");

$veiculo = $conn->query("SELECT * FROM veiculos WHERE id = $veiculo_id")->fetch_assoc();
?>

<html>
<head>
<meta charset="UTF-8">
<title>Agendamento Confirmado</title>

<style>
    body { font-family: Arial; margin:0; background:#f0f0f0; }
    .navbar { background:#111; padding:15px 25px; color:white; font-size:22px; font-weight:bold; }
    .container {
        max-width:800px; margin:40px auto; padding:30px;
        background:white; border-radius:12px;
        box-shadow:0 4px 15px rgba(0,0,0,0.15); text-align:center;
    }
    h2 { color:#28a745; font-size:28px; margin-bottom:20px; }
    .info { font-size:18px; margin:12px 0; }
    .btn-voltar {
        display:inline-block; margin-top:25px; padding:12px 22px;
        background:#007bff; color:white; text-decoration:none;
        border-radius:8px; font-size:18px;
    }
    .btn-voltar:hover { background:#005fcc; }
    .veiculo { margin-top:25px; font-size:17px; color:#444; }
    img {
        width:300px; height:180px; object-fit:cover;
        border-radius:10px; margin-bottom:20px;
    }
</style>

</head>
<body>

<div class="navbar">🚗 Kevão Veículos</div>

<div class="container">

    <h2>Agendamento realizado com sucesso! ✅</h2>

    <img src="<?= $veiculo['imagem'] ?>">

    <div class="veiculo">
        <strong><?= $veiculo['marca'] ?> - <?= $veiculo['modelo'] ?></strong><br>
        Versão: <?= $veiculo['versao'] ?><br>
        Local: <?= $veiculo['local_venda'] ?>
    </div>

    <hr style="margin:25px 0;">

    <p class="info"><strong>Data:</strong> <?= $data ?></p>
    <p class="info"><strong>Hora:</strong> <?= $hora ?></p>
    <p class="info"><strong>Cliente:</strong> <?= $nome ?></p>

    <a class="btn-voltar" href="main.php">Voltar</a>
</div>

</body>
</html>
