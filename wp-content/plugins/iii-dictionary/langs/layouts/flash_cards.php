<?php
	$current_user_id = get_current_user_id();
        $check_user_sub_any_dic = check_user_subscrible_any_dictionary($current_user_id);
	$flashcard_folders = MWDB::get_flashcard_folders($current_user_id, true);
        $dictionary_price = mw_get_option('dictionary-price');
	$flashcards = MWDB::get_flashcards($current_user_id);
	$teacher_sets = MWDB::get_flashcard_teacher_sets($current_user_id);
	$teacher_flashcards = MWDB::get_teacher_flashcards($current_user_id);
        $folderid = isset($_REQUEST['id'])?(int)$_REQUEST['id']:0;
	$fc_sets =array();
        $arr_memory=array();
	foreach($teacher_sets as $set)
	{
            $a = array();
            foreach($teacher_flashcards as $flashcard) {
                    if($flashcard->teacher_set_id == $set->id) {
                        $memorized = is_null($flashcard->memorized) ? 0 : $flashcard->memorized;
                        if(empty($flashcard->notes)) {
                            if(!is_null($flashcard->teacher_sentence)) {
                                $notes = $flashcard->teacher_sentence;
                            }
                            else {
                                $notes = '';
                            }
                        }
                        else {
                            $notes = $flashcard->notes;
                        }

                        $a[] = 'w' . $flashcard->id . ': {' .
                                                'word_id: "' . $flashcard->id . '",' .
                                                'word: ' . json_encode($flashcard->word) . ',' .
                                                'memorized: ' . $memorized . ',' .
                                                'notes: ' . json_encode($notes) .
                                        '}';
                    }
            }

            $fc_sets .= 'fc_sets[' . $set->id . '] = {header: ' . json_encode($set->header_name) . ', comment: ' . json_encode($set->comments) . ', teacher: ' . json_encode($set->display_name) . ', group: ' . json_encode($set->group_name) . ', date: ' . json_encode($set->created_on) . ', words: {' . implode(',', $a) . '}};';
	}

	$ta = array();
	foreach($teacher_flashcards as $flashcard)
	{
            $memorized = is_null($flashcard->memorized) ? 0 : $flashcard->memorized;
            $notes = is_null($flashcard->notes) ? '' : $flashcard->notes;
            $ta[] = '{word_id: "' . $flashcard->id . '", word: ' . json_encode($flashcard->word) . ', memorized: ' . $memorized . ', notes: ' . json_encode($notes) . '}';
	}
	$tfc_js = 'var tfc_folders = [' . implode(',', $ta) . '];';

	$fc_js = 'var fc_folders = [];';
        //$array_memory = array();
	foreach($flashcard_folders as $folder)
	{
            $a = array();
            foreach($flashcards as $flashcard)
            {
                if($flashcard->folder_id == $folder->id) {
                    $a[] = 'w' . $flashcard->id . ': {word_id: "' . $flashcard->id . '", word: ' . json_encode($flashcard->word) . ', memorized: ' . $flashcard->memorized . ', notes: ' . json_encode($flashcard->notes) . '}';
                    //array_push($array_memory, $flashcard->memorized);
                }
            }
            $fc_js .= 'fc_folders[' . $folder->id . '] = {' . implode(',', $a) . '};';
	}     
        //var_dump(in_array('0', $array_memory));
?>
<?php get_dict_header(__('Flash Cards', 'iii-dictionary'), 'green') ?>
<?php get_dict_page_title(__('Vocabulary Builder', 'iii-dictionary'), '', __('Flash Cards', 'iii-dictionary'), array(), get_info_tab_cloud_url('Popup_info_16.jpg')) ?>
<script>
    if ((window.matchMedia('screen and (min-width: 1200px)').matches)) {
        jQuery('#main').removeClass('container');
        jQuery('#flash-cards .article-header .row').attr('style', 'width:1050px; margin:auto !important');
        jQuery('#flash-cards .entry-content .row:first').attr('style', 'width:1050px; margin:auto !important');            
    }
    if ((window.matchMedia('screen and (max-width: 480px)').matches)) {
        //jQuery('.col-sm-offset-1 h1:first-child').attr('style', 'color: #599180;margin-bottom: 4%;padding-top: 4%;');
        jQuery('#sub-title').attr('style', 'margin-top: 6%;');
    }
</script>
<div class="row">
		<div class="col-sm-6">
                    <label class="color-yellow"><?php _e('Flashcard type', 'iii-dictionary') ?></label>
			<select class="select-box-it select-green form-control" id="sel-fc-type">
				<option value="my-own">My Own</option>
				<option value="teacher-sets">Teacher</option>
			</select>
		</div>
		
		<div class="col-sm-12"><hr style="border-top: 1px solid #062206"></div>
		<div class="col-sm-6" id="my-own-block" style="">
			<div class="form-group">
                            <label class="color-yellow"><?php _e('Select a folder', 'iii-dictionary') ?></label>
				<select class="select-box-it select-green form-control" id="sel-fc-folders">
                                    <?php 
                                    foreach($flashcard_folders as $folder) :
                                            if($folder->id == $folderid)
                                                $sel = 'selected="selected"';
                                            else
                                                $sel = '';
                                            if($folder->id != TEACHER_FLASHCARD_FOLDER) : ?>
                                                <option value="<?php echo $folder->id ?>" test="<?php echo $_GET['id']?>" <?php echo $sel ?>><?php echo $folder->name ?></option>
                                        <?php 
                                            endif; 
                                    endforeach ?>
				</select>
			</div>
		</div>
                <div class="col-sm-6 css-btn-create">
			<label>&nbsp;</label>
                        <button type="button" id="create-folder" class="btn btn-default btn-block grey form-control btn-fl"><?php _e('Create Folder', 'iii-dictionary') ?></button>
		</div>
		<div class="col-sm-6" id="teacher-sets-block" style="display: none">
			<div class="form-group">
				<label><?php _e('Select a set sent by your teacher', 'iii-dictionary') ?></label>
				<select class="select-box-it select-green form-control" id="sel-teacher-sets">
					<?php foreach($teacher_sets as $set) : ?>
						<option value="<?php echo $set->id ?>"><?php echo $set->header_name ?></option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
                <div>
                <div class="col-sm-6 css-btn-delete-fl" >
			<label>&nbsp;</label>
                        <button type="button" id="flash-card-delete-folder"  class="btn btn-default btn-block grey form-control btn-fl bnt-fl-margin-bottom"><?php _e('Delete Folder', 'iii-dictionary') ?></button>
		</div>
                <div class="col-sm-6 css-btn-save"> 
                    <label>&nbsp;</label> 
                    <button type="button" id="save-change" class="btn btn-default btn-block grey form-control btn-fl bnt-fl-margin-bottom" style="margin-top: -17px"><?php _e('Save Changes', 'iii-dictionary') ?></button>
		</div>
                <div class="col-sm-6 css-btn-fl-card">
			<label>&nbsp;</label>
                        <button type="button" id="flash-card-mode" class="btn btn-default btn-block grey form-control btn-fl btn-fl-card-mode"><?php _e('Flash card mode', 'iii-dictionary') ?></button>
		</div>
                </div>
		<div class="col-sm-12">
			<div class="row" id="flashcard-set-header" style="display: none">
				<div class="col-sm-4 col-md-3" id="set-header"><?php _e('Set:', 'iii-dictionary') ?> <span></span></div>
				<div class="col-sm-4 col-md-3" id="set-teacher"><?php _e('Teacher:', 'iii-dictionary') ?> <span></span></div>
				<div class="col-sm-4 col-md-3" id="set-group"><?php _e('Group:', 'iii-dictionary') ?> <span></span></div>
				<div class="col-sm-4 col-md-3" id="set-date"><?php _e('Date:', 'iii-dictionary') ?> <span></span></div>
				<div class="col-sm-12" id="set-comment"><?php _e('Teacher\'s comment:', 'iii-dictionary') ?> <span></span></div>
			</div>
			<div class="flashcard-table">
				<div class="flashcard-table-header">
                                    <div class="color-yellow"><?php _e('Words', 'iii-dictionary') ?></div>
                                    <div class="color-yellow"><?php _e('Create my sentence', 'iii-dictionary') ?></div>
                                    <div class="color-yellow"><?php _e('Memorized?', 'iii-dictionary') ?></div>
				</div>
				<div class="flashcard-table-content box " style="max-height: 375px">
					<table class="table table-striped table-condensed1 ik-table2 ik-table-noborder ik-table-green" id="fc-table">
						<tbody>
							<?php if(empty($teacher_flashcards)) : ?>
									<tr><td><?php _e('There\'s no flashcard in this folder', 'iii-dictionary') ?></td></tr>
							<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-sm-12" id="dictionary-block">
			<div class="box box-white flashcard-des scroll-list2 dictionary" style="max-height: 200px">
				<div id="fc-meaning"></div>
			</div>
		</div>
	</div>

<div id="flashcard-modal" class="modal fade" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header css-head-flash">
            <span style="right: 3%;padding-top: 4%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
             <h3><?php _e('Flash Card Mode', 'iii-dictionary') ?></h3>
        </div>
        <div class="modal-body">
            <div id="answer-block" >
            </div>
            <div id="hint-block" style="padding: 0px !important;">
                <div id="hints"><span style="color:#B6B6B6">My sentence:</span><span style="color:#708B22"> My grand father's history was gone long times ago now.</span></div>
            </div>
            <div class="line-flash"></div>
        </div>
        <div class="modal-footer">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
                                                <button type="button" id="as-memory" class="btn-custom confirm"> <?php _e('Mark as Memorized', 'iii-dictionary') ?></button>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<button type="button" id="next-flashcard" class="btn btn-default btn-block orange btn-next-word"> <?php _e('Next Word', 'iii-dictionary') ?></button>
					</div>
				</div>
			</div>
        </div>
      </div>
    </div>
</div>

<div id="require-modal" class="modal fade modal-white ik-modal1 ik-modal-transparent" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
            <div class="modal-header">
                    <a href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog magin-top-1"></a>
                    <h3><?php _e('Messages', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body">
                    <?php _e('You have memorized all flash cards or there\'s no flash card in this folder', 'iii-dictionary') ?>
            </div>
            <div class="modal-footer">
            <div class="row">
                <div class="col-sm-6" style="width: 100% !important">
                    <div class="form-group">
                        <button type="button" id="btn-ok-full-memory" class="btn-custom btn-leave-group"><?php _e('OK', 'iii-dictionary') ?></button>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>

<!--modal create folder-->
<div id="fl-create-folder-modal" class="modal fade modal-white ik-modal1 ik-modal-transparent" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog magin-top-1"></a>
                <h3 style="color: #FBD582"><?php _e('Create a New Folder', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body">
                <label for="fc-folder-name1" class="lb-create-folder1"><?php _e('Create folder name below:', 'iii-dictionary') ?></label>
                <input type="text" class="form-control" id="fc-folder-name1" style="height:42px">
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <button type="button" class="btn btn-block orange bt-create-fl" id="btn-create" data-loading-text="<?php _e('Saving ...', 'iii-dictionary') ?>"><?php _e('Create', 'iii-dictionary') ?></button>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <button type="button" id="fc-folder-form" class="btn-custom btn-leave-group bt-create-folder"><?php _e('Cancel', 'iii-dictionary') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--modal message create fodder -->
<div class="modal fade " id="create-folder-success" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content modal-content-custom">
                <div class="modal-header custom-header">
                    <a href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog magin-top-1"></a>
                    <h3 style="padding-left: 2%"><?php _e('Message', 'iii-dictionary') ?></h3>
                </div>
                <div class="modal-body body-custom">
                    <span>Folder created successfully.</span>
                </div>
                <div class="modal-footer footer-custom create-success">
                    <div class="col-sm-6" style="width: 100%">
                        <div class="form-group">
                            <button type="button" id="create-ok" class="btn-custom confirm"><?php _e('OK', 'iii-dictionary') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--modal message create fodder -->
<div class="modal fade " id="message-full-memorized" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content modal-content-custom">
                <div class="modal-header custom-header">
                    <a href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog magin-top-1"></a>
                    <h3 style="padding-left: 2%"><?php _e('Message', 'iii-dictionary') ?></h3>
                </div>
                <div class="modal-body body-custom">
                    </b>You have memorized all flash cards.</span>
                </div>
                <div class="modal-footer footer-custom create-success">
                    <div class="col-sm-6" style="width: 100%">
                        <div class="form-group">
                            <button type="button" id="memorized-ok" class="btn-custom confirm"><?php _e('OK', 'iii-dictionary') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--modal delete folder-->
<div id="fl-delete-folder-modal" class="modal fade modal-white ik-modal1 ik-modal-transparent" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog magin-top-1"></a>
                <h3 style="color: #FBD582"><?php _e('Delete a Folder', 'iii-dictionary') ?></h3>
            </div>
            <div class="modal-body" style="padding-bottom: 0px !important;">
                <div class='lb-create-folder'><?php _e('Are you sure you want to permanently delete folder?', 'iii-dictionary') ?></div>            
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <button type="button" class="btn btn-block orange bt-create-fl" id="delete-folder-ok" data-loading-text="<?php _e('Deleting ...', 'iii-dictionary') ?>"><?php _e('Delete the Folder', 'iii-dictionary') ?></button>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <button type="button" id="delete-folder-cancel" class="btn-custom btn-leave-group bt-create-folder"><?php _e('Cancel', 'iii-dictionary') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--modal mesage not delete default folder-->
<div id="modal-message-not-delete" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content modal-content-custom">
                <div class="modal-header custom-header">
                    <span style="right: 3%;padding-top: 4% !important;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                    <h3 style="padding-left: 0%"><?php _e('Error', 'iii-dictionary') ?></h3>
                </div>
                <div class="modal-body body-custom">
                    <span>You can’t delete default folder.</span>
                    <div class="form-group" style="margin-top:15px;">
                        <button type="button" id="btn-ok-close" class="btn-custom btn-leave-group"><?php _e('OK', 'iii-dictionary') ?></button>
                    </div>
                </div>
        </div>
    </div>
</div>
<!--modal-required subscribe-->
            <div id="require-modal1" class="modal fade modal-white ik-modal1 ik-modal-transparent" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog magin-top-1"></a>
                                <h3 style="color: #FBD582"><?php _e('Save to Folder', 'iii-dictionary') ?></h3>
                            </div>
                            <div class="modal-body padding-0">
                                <div class='lb-create-folder'>Please subscribe dictionary to use vocabulary builder.
                                    <a class="sub-dictionary-now" style="color: #ff8283;" href="#modal-sub-dictionary"> Subscribe Dictionary Now.</a></div>
                                
                            </div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-sm-6" style="width: 100% !important">
                                        <div class="form-group">
                                            <button type="button" id="ok-modal-req-sub" class="btn-custom btn-leave-group"><?php _e('OK', 'iii-dictionary') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
</div>
<!--modal-subscrible-dictionary-now  -->
<div id="modal-sub-dictionary" class="modal fade">
    <div class="modal-dialog modal-custom-first">
        <div class="modal-content boder-black">
            <div class="modal-header custom-header">
                <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                <h3><?php _e('Check Out - Dictionary', 'iii-dictionary') ?></h3>
            </div>
            <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                <input type="hidden" id="addi-sub-type" name="sub-type" value="">
                <input type="hidden" id="addi-gid" name="assoc-group" value="">
                <input type="hidden" id="addi-gname" name="group-name" value="">
                <input type="hidden" id="addi-gpass" name="group-pass" value="">
                <input type="hidden" id="sub-id" name="sub-id" value="0">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group<?php echo $is_math_panel ? ' hidden' : '' ?>">
                                <label class="font-dialog"><?php _e('Select the type of dictionary', 'iii-dictionary') ?></label>
                                <?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary', 'form-control', true) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label id="num-of-student-lbl" class="font-dialog"><?php _e('Number of Students', 'iii-dictionary') ?></label>
                                <?php $min_no_of_student = mw_get_option('min-students-subscription') ?>
                                <input type="number" name="no-students" id="student_num" class="form-control" data-min="<?php echo $min_no_of_student ?>" value="<?php echo $min_no_of_student ?>">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label id="num-of-months-lbl" class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                <select class="select-box-it form-control" name="teacher-tool-months" id="sel-teacher-tool">
                                    <?php for ($i = 3; $i <= 24; $i++) : ?>
                                        <option value="<?php echo $i ?>"><?php printf(__('%s months', 'iii-dictionary'), $i) ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row padding-top15">
                        <div class="col-sm-12">
                            <div class="box-gray-dialog" style="text-align: right">
                                <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color-green708b23">$</span> <span id="total-amount" class="color-green708b23">0</span>
                            </div>
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" id="add-to-cart" name="add-to-cart" class="btn-custom"><?php _e('Check out', 'iii-dictionary') ?></button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1"><?php _e('Cancel', 'iii-dictionary') ?></a>
                            </div>
                        </div>
                    </div>
                </div>			
            </form>
        </div>
    </div>
</div>
<!--modal-message-saving-changes-->
<div id="message-modal-save-changes" class="modal fade modal-white ik-modal1 ik-modal-transparent" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog magin-top-1"></a>
                <h3 style="color: #FBD582"><?php _e('Save sentence', 'iii-dictionary') ?></h3>
            </div>
            <div id ="message-save-changes" class="modal-body">
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-6" style="width: 100% !important">
                        <div class="form-group">
                            <button type="button" id="btn-ok-save-changes" class="btn-custom btn-leave-group"><?php _e('OK', 'iii-dictionary') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
        var dp = <?php echo (int)$dictionary_price ?>;
        var adp = <?php echo mw_get_option('all-dictionary-price') ?>;
	var fc_sets = <?php echo $fc_sets ?>;
	<?php echo $tfc_js ?>
	<?php echo $fc_js ?>
        var check_sub_any_dic = <?php echo $check_user_sub_any_dic ? 'true':'false'?>;
        var folderid = <?php echo $folderid ?>;
        
</script>
<?php get_dict_footer() ?>

