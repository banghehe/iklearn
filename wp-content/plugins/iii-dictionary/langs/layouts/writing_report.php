<?php
	$page_title_tag = __('Writing Report', 'iii-dictionary');
	$mode = get_query_var('mode', 'practice');
	$select_grade_sheets = array();
	$sid = isset($_GET['sid']) ? $_GET['sid'] : 0;
	$uhid = 0;
	$view = $_GET["hid"] ;
        if($view > 0) {
            MWDB::update_user_is_view_homework($view);
        }
	$homework_sheets = MWDB::get_homework_sheets(ASSIGNMENT_REPORT, true);
	$sheet_list = $homework_sheets;
	// check homework id
	if(!empty($_GET['hid'])) {
		$homework_assignment = MWDB::get_homework_assignment_by_id($_GET['hid']);
		$sid = $homework_assignment->sheet_id;
		$uhid = $homework_assignment->id;
	}

	foreach($sheet_list as $item)
	{
		$select_grade_sheets[$item->grade] .= '<option data-sheet-id="' . $item->sheet_id . '" value="' . $item->sheet_name . '">' . $item->sheet_name . '</option>';
		if($sid && $sid == $item->sheet_id) {
			$sheet = $item;
			break;
		}
	}

	// user want to upload report
	if(isset($_POST['upload-report']))
	{
		$hwa = MWDB::get_homework_assignment_by_id($_POST['uhid']);
		$report_file = $_FILES['input-file'];
		if($report_file['error'] == UPLOAD_ERR_OK) {
			$user = wp_get_current_user();
			$upload_time = date('Y-m-d', time());
			$report_file_name = '[' . date('ymdHis', time()) . '][' . $hwa->group_name . '][' . $hwa->sheet_name . '][' . $user->display_name . ']_' . $report_file['name'];
			$report_file_name = str_replace(' ', '_', $report_file_name);
			if(move_uploaded_file($report_file['tmp_name'], IK_STUDENT_REPORT_DIR . '/' . $report_file_name)) {
				$result = $wpdb->insert(
					$wpdb->prefix . 'dict_homework_results',
					array(
						'userid' => get_current_user_id(),
						'homework_id' => $_POST['uhid'],
						'finished' => 1,
						'report_file' => $report_file_name,
						'attempted_on' => $upload_time,
						'submitted_on' => $upload_time,
						'graded' => 0
					)
				);

				if($result) {
					ik_enqueue_messages(__('You successfully uploaded your assignment.', 'iii-dictionary'), 'success');
					wp_redirect(locale_home_url() . '/?r=homework-status&gid=' . $hwa->gid);
					exit;
				}
				else {
					ik_enqueue_messages(__('You cannot upload your assignment.', 'iii-dictionary'), 'error');
				}
			}
		}
	}

	$disable_select = ' disabled';
?>
<?php get_dict_header($page_title_tag, 'green') ?>
<?php get_dict_page_title($page_title_tag) ?>

<form method="post" action="" enctype="multipart/form-data">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="med-font-size" style="font-size: 20px;"><?php _e('Worksheet Projects', 'iii-dictionary') ?></h3>
        </div>
        <div class='txt-ws'>Please select a group from the dropdown menu below and upload project file.</div>
    </div>
    <div class="line-ws"></div>
    <div class="row">
        <div class="col-sm-3" style="width: 50%; padding-left: 0px !important;">
            <div style="background:none !important" class="form-group box small<?php echo $box_bg ?>">
                <select class="select-box-it select-green" id="grade"<?php echo $disable_select ?>>
                    <?php foreach($select_grade_sheets as $g => $s) : ?>
                            <option value="<?php echo $g ?>"<?php echo $sid && $g == $sheet->grade ? ' selected' : '' ?>><?php echo $g ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group with-ws" id="writing-box">
                <label class="italic-2">Teacher:</label>
                <div id ="name-teacher">Linda Anderson</div>
                <div class="line-ws1"></div>
                <label class="italic-1">Assignment:</label>
                <div id="vocab-question" class="txt-vc"><?php echo $sheet->questions ?></div>
            </div>
        </div>
    </div>
    <div class="row">
            <?php if($mode == 'homework') : ?>
        <div class="col-sm-9" style="width: 49%;float: left;">
                    <div class="form-group">
                        <label for="report-file" style="color: #fff"><?php _e('Select report file', 'iii-dictionary') ?></label>
                        <input type="text" class="form-control" style="color: #27572A" id="report-file" name="report-file" value="" readonly>
                    </div>					
                </div>
        <div class="col-sm-3" style="width: 49%;float: right">
                    <div class="form-group">
                        <label >&nbsp;</label>
                        <span class="btn btn-default btn-block grey btn-file form-control tx-browse">
                            <?php _e('Browse', 'iii-dictionary') ?> <input name="input-file" id="input-file" type="file">
                        </span>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-4 form-group" style="float: right; width: 49%;">
                    <label style="display: none">&nbsp;</label>
                    <button type="submit" name="upload-report" class="btn btn-block form-control" style="background: #BCB3B4;color: #000"><?php _e('Upload Now', 'iii-dictionary') ?></button>
                </div>
            <?php endif ?>
    </div>
    <input type="hidden" name="uhid" value="<?php echo $uhid ?>">
</form>

<script>
	(function($){
		$(function(){
			$("#input-file").change(function(e){
				$("#report-file").val($(this).val());
			});
		});
	})(jQuery);
</script>
<script>
if ((window.matchMedia('screen and (min-width: 1200px)').matches)) {
            jQuery('#main').removeClass('container');
            jQuery('#writing-report .article-header .row').attr('style', 'width:1050px; margin:auto !important');
            jQuery('#writing-report .entry-content .row:first').attr('style', 'width:1050px; margin:auto !important');
        }
</script>
<?php get_dict_footer() ?>