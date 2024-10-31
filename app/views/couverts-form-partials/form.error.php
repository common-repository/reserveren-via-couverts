<div class="row">
    <div class="column column-100">
        <h4>Er is een fout opgetreden</h4>
        <p>Er kon geen verbinding worden gemaakt met Couverts. Zijn de instellingen juist ingevuld?</p>
        <?php if (isset($error['http_code'])): ?>
            <p>Er trad een onverwachte fout op: <?php echo $error['http_code']; ?></p>
        <?php endif; ?>
    </div>

    <hr>
</div>
