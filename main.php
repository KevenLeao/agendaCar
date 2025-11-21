<?php
//OBS: Caso não funcionar, altere a linha abaixo de "localhost:3307" para "localhost".
$conn = new mysqli("localhost", "root", "", "agendaCar");
//select dos veículos disponiveis no banco
$sql = "SELECT * FROM veiculos";
$result = $conn->query($sql);
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lista de Veículos</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .navbar {
            width: 100%;
            background: #1a1a1a;
            padding: 15px 25px;
            color: white;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .veiculo {
            width: 300px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: transform .2s;
        }

        .veiculo:hover {
            transform: scale(1.03);
        }

        .veiculo img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .info {
            padding: 15px;
        }

        h3 {
            margin: 10px 0;
            font-size: 20px;
            color: #222;
        }

        p {
            margin: 6px 0;
            color: #444;
            font-size: 14px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 15px;
            border-radius: 0 0 12px 12px;
            cursor: pointer;
            transition: background .2s;
        }

        .btn:hover {
            background: #005fcc;
        }

    </style>
</head>

<body>

<div class="navbar">
    🚗 Kevão Veículos 
</div>

<div class="container">

<?php while ($v = $result->fetch_assoc()): ?>
    <div class="veiculo">

        <img src="<?= $v['imagem'] ?>">

        <div class="info">
            <h3><?= $v['marca'] ?> <?= $v['modelo'] ?></h3>

            <p><strong>Versão:</strong> <?= $v['versao'] ?></p>
            <p><strong>Preço:</strong> R$ <?= number_format($v['preco'], 2, ',', '.') ?></p>
            <p><strong>Local de Venda:</strong> <?= $v['local_venda'] ?></p>
        </div>

        <a class="btn" href="agendamento.php?id=<?= $v['id'] ?>">
            Agendar Visita
        </a>

    </div>
<?php endwhile; ?>

</div>

</body>
</html>
