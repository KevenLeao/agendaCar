<?php

//OBS: Caso não funcionar, altere a linha abaixo de "localhost:3307" para "localhost".
$conn = new mysqli("localhost:3307", "root", "", "agendaCar");

$veiculo_id = $_GET['id'];

// gera os horarios para a lista dos proximos 5 dias
$horariosDisponiveis = [];
$horariosPadrao = ["09:00","10:00","11:00","13:00","14:00","15:00"];

for ($i = 1; $i <= 5; $i++) {
    $dia = date("Y-m-d", strtotime("+$i day"));
    $horariosDisponiveis[$dia] = $horariosPadrao;
}

// select do veiculo selecionado 
$veiculo = $conn->query("SELECT * FROM veiculos WHERE id = $veiculo_id")->fetch_assoc();

// filtro dos horarios ocupados
$sql = "SELECT data, hora FROM agendamentos WHERE veiculo_id = $veiculo_id";
$result = $conn->query($sql);

$horariosOcupados = [];

while ($row = $result->fetch_assoc()) {
    $dia = $row['data'];
    $hora = $row['hora'];

    if (!isset($horariosOcupados[$dia])) {
        $horariosOcupados[$dia] = [];
    }

    $horariosOcupados[$dia][] = $hora;
}

// remove dias sem horários disponiveis
foreach ($horariosDisponiveis as $dia => $horas) {
    if (isset($horariosOcupados[$dia])) {
        $horariosDisponiveis[$dia] = array_values(array_diff($horas, $horariosOcupados[$dia]));
    }
}
$horariosDisponiveis = array_filter($horariosDisponiveis, fn($h) => count($h) > 0);
?>

<html>
<head>
<meta charset="UTF-8">
<title>Agendar Visita</title>

<style>
    body { font-family: Arial; margin:0; background:#f0f0f0; }

    .navbar {
        background:#111; padding:15px 25px;
        color:white; font-size:22px; font-weight:bold;
    }

    .container {
        max-width:900px; margin:30px auto; padding:25px;
        background:white; border-radius:12px;
        box-shadow:0 4px 12px rgba(0,0,0,0.15);
    }

    .veiculo-card { display:flex; gap:20px; align-items:center; }
    .veiculo-card img {
        width:260px; height:160px; object-fit:cover; border-radius:10px;
    }

    hr { margin:25px 0; border:none; border-top:1px solid #ddd; }

    .calendar-btn {
        display:block; width:100%; padding:12px;
        border-radius:10px; border:1px solid #ccc;
        background:white; cursor:pointer; margin-bottom:10px;
        font-size:16px; transition:.2s;
    }
    .calendar-btn:hover { background:#f2f2f2; }

    .calendar-btn.selecionado {
        background:#007bff;
        color:white;
        border-color:#005fcc;
        font-weight:bold;
    }

    .time-btn {
        padding:10px 18px;
        border-radius:8px;
        border:2px solid #007bff;
        margin:8px;
        cursor:pointer;
        background:white;
        color:#007bff;
        font-weight:bold;
        transition:.2s;
    }
    .time-btn:hover { background:#e6f0ff; }

    .time-btn.selecionado {
        background:#007bff;
        color:white;
        border-color:#005fcc;
    }

    .hidden { display:none; }

    input {
        width:100%; padding:12px; margin-bottom:12px;
        border-radius:8px; border:1px solid #ccc; font-size:16px;
    }

    .btn-finalizar {
        width:100%; padding:14px; background:#28a745;
        border:none; color:white; font-size:18px; border-radius:10px;
    }
    .btn-finalizar:hover { background:#1f8c3a; }
</style>

</head>
<body>

<div class="navbar">
    🚗 Kevão Veículos
</div>

<div class="container">

    <h2>Veículo selecionado</h2>

    <div class="veiculo-card">
        <img src="<?= $veiculo['imagem'] ?>">
        <div>
            <p><strong><?= $veiculo['marca'] ?> - <?= $veiculo['modelo'] ?></strong></p>
            <p>Versão: <?= $veiculo['versao'] ?></p>
            <p>Preço: R$ <?= number_format($veiculo['preco'], 2, ',', '.') ?></p>
            <p>Local de venda: <?= $veiculo['local_venda'] ?></p>
        </div>
    </div>

    <hr>

    <h3>1️⃣ Escolha a data</h3>

    <?php foreach ($horariosDisponiveis as $dia => $horas): ?>
        <button class="calendar-btn" onclick="selecionarData(this, '<?= $dia ?>')">
            <?= date("d/m/Y", strtotime($dia)) ?>
        </button>
    <?php endforeach; ?>

    <div id="box-horarios" class="hidden">
        <hr>
        <h3>2️⃣ Escolha o horário</h3>
        <div id="horarios"></div>
    </div>

    <form id="form-dados" class="hidden" method="POST" action="finalizar.php">
        <hr>
        <h3>3️⃣ Seus dados</h3>

        <input type="hidden" name="veiculo_id" value="<?= $veiculo_id ?>">
        <input type="hidden" name="data" id="data-escolhida">
        <input type="hidden" name="hora" id="hora-escolhida">

        <input type="text" name="nome" placeholder="Nome completo" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="telefone" placeholder="Telefone" required>

        <button class="btn-finalizar" type="submit">Finalizar Agendamento</button>
    </form>

</div>

<script>
    const horarios = <?= json_encode($horariosDisponiveis) ?>;

        //marca o dia selecionado
    function selecionarData(botao, dia) {
        document.getElementById("data-escolhida").value = dia;
        document.getElementById("box-horarios").classList.remove("hidden");

        // limpar seleção anterior
        document.querySelectorAll(".calendar-btn").forEach(b => b.classList.remove("selecionado"));

        // marcar selecionado
        botao.classList.add("selecionado");

        // carregar horários
        let lista = "";
        horarios[dia].forEach(h => {
            lista += `<button type='button' class='time-btn' onclick="selecionarHora(this, '${h}')">${h}</button>`;
        });
        document.getElementById("horarios").innerHTML = lista;
    }

        //,marca o horario selecionado
    function selecionarHora(botao, hora) {
        document.getElementById("hora-escolhida").value = hora;
        document.getElementById("form-dados").classList.remove("hidden");

        document.querySelectorAll(".time-btn").forEach(b => b.classList.remove("selecionado"));
        botao.classList.add("selecionado");
    }
</script>

</body>
</html>
