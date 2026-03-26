<?php require_once RUTA_FOOTER_LOGOS; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<?php if (defined('RUTA_URL') && file_exists(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . parse_url(RUTA_URL, PHP_URL_PATH) . '/public/js/main.js')): ?>
<script src="<?php echo RUTA_URL ?>/public/js/main.js"></script>
<?php endif ?>

</body>
</html>
