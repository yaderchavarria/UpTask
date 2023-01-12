<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <a href="/cambiar-password" class="enlace">Cambiar password</a>

    <form method="POST" action="/perfil" class="formulario">
        <div class="campo">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="" value="<?php echo $usuario->nombre; ?>" placeholder="Tu nombre">
        </div>
        <div class="campo">
            <label for="email">Email</label>
            <input type="email" name="email" id="" value="<?php echo $usuario->email; ?>" placeholder="Tu email">
        </div>
        <input type="submit" value="Guardar Cambios">
    </form>
</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>