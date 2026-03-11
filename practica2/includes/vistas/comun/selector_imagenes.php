<!-- SE BEDE ESTABLECER $ruta_avatares ANTES DEL INCLUDE -->
<label>Foto de perfil:</label>
<br>
<div class="seleccion-avatares">
    <?php foreach ($ruta_avatares as $indice => $archivo): ?>
        <label class="opcion-avatar">
            <img class="opcion-imagen-avatar" src="<?php echo RUTA_IMG ?>/perfiles/<?= $archivo; ?>" alt="Avatar <?= $indice; ?>">
            <input type="radio" name="avatar" value="<?= $archivo; ?>">
        </label>
    <?php endforeach; ?>

    <label class="opcion-avatar">
        <div class="cuadro-subir-archivo">
            <p>Elegir<br>Archivo</p>
        </div>
        <input type="radio" name="avatar" value="custom" id="radio-custom">
    </label>
</div>

<div id="archivo-avatar">
    <br>
    <input type="file" name="avatar" accept="image/*">
    <br>
    <br>
    <br>
</div>