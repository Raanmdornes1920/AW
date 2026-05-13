<!-- SE BEDE ESTABLECER $ruta_avatares ANTES DEL INCLUDE -->
<label class="form-label">Foto de perfil</label>
<div class="row row-cols-3 row-cols-sm-4 g-3 mb-3">
    <?php foreach ($ruta_avatares as $indice => $archivo): ?>
        <label class="opcion-avatar col text-center">
            <input class="btn-check" type="radio" name="avatar" value="<?= $archivo; ?>" required>
            <span class="btn btn-outline-secondary w-100 p-2">
                <img class="rounded-circle object-fit-cover" style="width:54px;height:54px;" src="<?php echo RUTA_IMG ?>/perfiles/<?= $archivo; ?>" alt="Avatar <?= $indice; ?>">
            </span>
        </label>
    <?php endforeach; ?>

    <label class="opcion-avatar col text-center">
        <input class="btn-check" type="radio" name="avatar" value="custom" id="radio-custom" required>
        <span class="btn btn-outline-secondary w-100 p-3">Subir archivo</span>
    </label>
</div>

<div id="archivo-avatar" style="display: none;">
    <input class="form-control" type="file" name="avatar-custom" accept="image/*">
</div>
