<script>
    ga('send', 'pageview');
</script>

<?php
//只统计线上
if(IN_PRODUCTION):?>
<div style="display:none">
    <script src="<?=Request::$protocol?>://s23.cnzz.com/stat.php?id=3454557&amp;web_id=3454557"></script>
</div>
<?php endif?>

<?php if (!IN_PRODUCTION && isset($_GET['debug'])) echo View::factory('profiler/stats');?>

<!-- memory_usage: {memory_usage} -->
<!-- execution_time: {execution_time} -->
