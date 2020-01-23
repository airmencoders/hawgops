<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $site_key; ?>"></script>
<script>
grecaptcha.ready(function() {
    grecaptcha.execute(<?php echo $site_key; ?>, {action: "login"}).then(function(token) {
       ...
    });
});
</script>