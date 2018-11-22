<!-- Google Analytics -->
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','/'+'/www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-156767-9', 'auto');
    ga('require', 'linkid', 'linkid.js');
    ga('set', 'dimension1', '<?=$user ? 'Member' : 'Guest'?>');
    <?php if ($user->id):?>ga('set', '&uid', '<?=$user->id?>');<?php endif?>
</script>
<!-- End Google Analytics -->

<!-- Cnzz Analytics -->
<script>
    var _czc = _czc || [];
    _czc.push(["_setAccount", "3454557"]);
    _czc.push(["_setCustomVar", "UserType", "<?=$user ? 'Member' : 'Guest'?>"]);
    _czc.push(["_setCustomVar", "Source", "<?=Cookie::get('source')?>"]);
</script>
<!-- End Cnzz Analytics -->