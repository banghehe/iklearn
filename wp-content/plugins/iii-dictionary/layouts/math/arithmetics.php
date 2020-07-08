<?php
$levels = MWDB::get_grades(array('type' => 'MATH', 'level' => 1, 'parent_id' => MATH_ARITHMETIC, 'orderby' => 'ordering', 'order-dir' => 'asc'));
update_user_subscription();
//print_r($_SESSION['subscription']);die;
?>
<?php get_math_header(__('Arithmetics', 'iii-dictionary')) ?>

<?php MWHtml::select_math_level_page($levels) ?>

<?php get_math_footer() ?>
<script>
    jQuery('.container .row div #purchase-dialog').html("Select the subject and click on a worksheet to get started");
    jQuery('.container .row div #purchase-dialog').attr("style", "color:#408A73;font-size:16px");
    jQuery('.container .row div h1:nth-child(1)').attr("style", "float:left;padding-right: 4%;margin-top: 9px;font-size: 30px;font-family: kenyan_coffee;");
    jQuery('#container #content .entry-content').attr("style", "background: #fff !important");
    if ((window.matchMedia('screen and (max-width: 768px)').matches)) {    
        jQuery('#purchase-dialog').attr("style", "display: none;");
    }
</script>