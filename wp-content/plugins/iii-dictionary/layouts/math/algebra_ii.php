<?php
	$levels = MWDB::get_grades(array('type' => 'MATH', 'level' => 1, 'parent_id' => MATH_ALGEBRA_II, 'orderby' => 'ordering', 'order-dir' => 'asc'));
update_user_subscription();
?>
<?php get_math_header(__('Algebra II', 'iii-dictionary')) ?>

<?php MWHtml::select_math_level_page($levels) ?>

<?php get_math_footer() ?>
<script>
    jQuery('.container .row div #purchase-dialog').html("Select the subject and click on a worksheet to get started");
    jQuery('.container .row div #purchase-dialog').attr("style","color:#408A73;font-size:16px");
    jQuery('.container .row div h1:nth-child(1)').attr("style","float:left;padding-right: 4%;margin-top: 9px;font-family: kenyan_coffee;font-size: 30px;");
    jQuery('.container .row div h1:nth-child(1)').html("Algebra 2");
    jQuery('#container #content .entry-content').attr("style","background: #fff !important");
    if ((window.matchMedia('screen and (max-width: 768px)').matches)) {    
        jQuery('#purchase-dialog').attr("style", "display: none;");
        jQuery('.css-padd-des-10').attr("style", "padding-left: 20px !important;");
    }
</script>