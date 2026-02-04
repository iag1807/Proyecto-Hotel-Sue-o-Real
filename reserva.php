<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Reservar</h1>
    <form action="validaciones/validar_reserva.php" method="POST">
        <label for="fecha_ingreso">Fecha y hora de ingreso</label><br>
        <input type="datetime-local" name="fecha_ingreso"><br>
        <label for="fecha_salida">Fecha y hora de salida</label><br>
        <input type="datetime-local" name="fecha_salida"><br>
        <label for="numero_personas">Numero de personas</label><br>
        <input type="number" name="numero_personas"><br>
        <label for="habitacion">Habitacion</label><br>
        <select name="habitacion_numero">
            <option value="">Seleccione una habitacion</option>
            <option value="101">101</option>
            <option value="102">102</option>
            <option value="103">103</option>
            <option value="104">104</option>
            <option value="105">105</option>
            <option value="106">106</option>
            <option value="107">107</option>
            <option value="108">108</option>
            <option value="109">109</option>
            <option value="110">110</option>
            <option value="111">111</option>
            <option value="201">201</option>
            <option value="202">202</option>
            <option value="203">203</option>
            <option value="204">204</option>
            <option value="205">205</option>
            <option value="206">206</option>
            <option value="207">207</option>
            <option value="208">208</option>
            <option value="209">209</option>
            <option value="210">210</option>
            <option value="211">211</option>
            <option value="301">301</option>
            <option value="302">302</option>
            <option value="303">303</option>
            <option value="304">304</option>
            <option value="305">305</option>
            <option value="306">306</option>
            <option value="307">307</option>
            <option value="308">308</option>
            <option value="309">309</option>
            <option value="310">310</option>
            <option value="311">311</option>
            <option value="312">312</option>
            <option value="313">313</option>
            <option value="314">314</option>
            <option value="401">401</option>
            <option value="402">402</option>
            <option value="403">403</option>
            <option value="404">404</option>
            <option value="405">405</option>
            <option value="406">406</option>
            <option value="407">407</option>
            <option value="408">408</option>
            <option value="409">409</option>
            <option value="410">410</option>
            <option value="411">411</option>
            <option value="412">412</option>
            <option value="413">413</option>
            <option value="414">414</option>
            <option value="415">415</option>
            <option value="416">416</option>
        </select><br>
        <input type="submit" value="Reservar"><br>

        <?php if(isset($_SESSION['errores'])) : ?>
            <?php 
            foreach($_SESSION['errores'] as $error){
            echo "<p>".htmlspecialchars($error). "</p>";
            }
            unset($_SESSION['errores']);
            ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])) : ?>
            <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
    </form>
</body>
</html>