<div class="contenedor restablecer">
<?php include_once __DIR__ . '/../templates/nombre-sitio.php' ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca tu nuevo password</p>

        <?php include_once __DIR__ . '/../templates/alertas.php' ?>

        <?php if ($mostrar) { ?>

        <form class="formulario" method="POST">
       
            <div class="campo">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Tu password">
            </div>
            <div class="campo">
                <label for="password2">Confirmar Password</label>
                <input type="password" name="password2" id="password2" placeholder="Confirma tu password">
            </div>
            

            <input class="boton" type="submit" value="Guardar Password">
        </form>

        <?php } ?>

        <div class="acciones">
            <a href="/crear">Aun no tienes una cuenta? Crea una</a>
            <a href="/olvide">Olvidaste tu password?</a>
        </div>
    </div>
</div>