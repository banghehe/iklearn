<?php
	global $wpdb;

	$task = isset($_POST['task']) ? $_POST['task'] : '';
	$gid = empty($_GET['gid']) ? 0 : $_GET['gid'];
	$gname = '';
	$current_user_id = get_current_user_id();
	$uref = rawurlencode(base64_encode(home_url() . $_SERVER['REQUEST_URI']));
	$is_math_panel = is_math_panel();
	$_page_title = __('Homework','iii-dictionary');
	$_averge = 0;

	

	// user click Start button, join user to the group.
	if(!empty($_POST['jid'])) {
		$class_type_id = $_POST['cltid'];
		$is_sat_class_subscribed = is_sat_class_subscribed($class_type_id);
		if($is_sat_class_subscribed){
			$g = MWDB::get_group($_POST['jid'], 'id');

			if(MWDB::join_group($_POST['jid'])) {
				wp_redirect(locale_home_url() . '/?r=homework-status');
				exit;
			}
		}
	}
	// user want to join group
	if(isset($_POST['join']))
	{
		$gname = esc_html($_POST['gname']);
		$gpass = esc_html($_POST['gpass']);
		if(MWDB::join_group($gname, $gpass)) {

			wp_redirect(locale_home_url() . '/?r=online-learning');
			exit;
		}
	}

	// user want to leave group
	if(isset($_POST['leave']))
	{
		$result = MWDB::leave_group($_POST['gid']);

		if($result) {
			ik_enqueue_messages(__('Successfully left group.', 'iii-dictionary'), 'success');

			// updating subscription status
			update_user_subscription();

			wp_redirect(locale_home_url() . '/?r=online-learning');
			exit;
		}
		else {
			ik_enqueue_messages(__('Cannot leave Group.', 'iii-dictionary'), 'error');
		}
	}

	// page content
	$current_page = max( 1, get_query_var('page'));
	$filter['items_per_page'] = 20;
	$filter['offset'] = $filter['items_per_page'] * ($current_page - 1);

	if(!$gid)
	{
		$filter['offset'] = 0;
		$filter['items_per_page'] = 99999999;
		$user_groups = MWDB::get_user_joined_groups($current_user_id, $filter['offset'], $filter['items_per_page'], true);
		$total_pages = ceil($user_groups->total / $filter['items_per_page']);
	}
	else
	{
		// user want to re do the homework
		if(isset($_POST['retry'])) {
			if(MWDB::delete_homework_result($_POST['rid'])) {
				$url = MWHtml::get_practice_page_url($_POST['aid']) . '&mode=homework&sid=' . $_POST['sid'] . '&ref=' . $uref;
				wp_redirect($url);
				exit;
			}
		}

		// user want to request grading from teacher
		if(isset($_POST['request-grading'])) {
			$hrid = $_POST['hrid']; // homework result id
			$hid = $_POST['hid']; // homework id

			// request grading
			if(ik_request_worksheet_grading($hrid, $hid, $current_user_id)) {
				wp_redirect(locale_home_url() . '/?r=homework-status&gid=' . $gid);
				exit;
			}
		}

		$filter['homework_result'] = true;
		$filter['user_id'] = get_current_user_id();
		$filter['is_active'] = 1;
		$group = MWDB::get_group($gid, 'id');
		$homeworks = MWDB::get_group_homeworks($gid, $filter, $filter['offset'], $filter['items_per_page']);
		$total_pages = ceil($homeworks->total / $filter['items_per_page']);
		//calculate the average score
		if(!empty($homeworks->items)) {
			$_averge =  average_test_homework($homeworks->items);
		}
	}
	
	$pagination = paginate_links(array(
		'format' => '?page=%#%',
		'current' =>  $current_page,
		'total' => $total_pages
	));
$sub_msg = __('Please subscribe SAT Preparation to start', 'iii-dictionary');
ik_enqueue_js_messages('login_req_h', __('Login Required', 'iii-dictionary'));
ik_enqueue_js_messages('login_req_err', __('Please login in order to continue to use this function.', 'iii-dictionary'));
ik_enqueue_js_messages('login_req_lbl', __('Login', 'iii-dictionary'));

ik_enqueue_js_messages('sub_req_h', __('Subscription Required', 'iii-dictionary'));
ik_enqueue_js_messages('sub_req_err', $sub_msg);
ik_enqueue_js_messages('sub_req_lbl', __('Subscribe', 'iii-dictionary'));	

?>
<?php if(!$is_math_panel) : ?>
	<?php get_dict_header($_page_title) ?>
<?php else : ?>
	<?php get_math_header($_page_title, 'red-brown') ?>
<?php endif ?>
<?php get_dict_page_title($_page_title, '', '', array(), get_info_tab_cloud_url('Popup_info_11.jpg')) ?>

		<div class="row">

		<?php if(!$gid) : ?>

			<div class="col-sm-12">
				<h2 class="title-border"><?php _e('Select the activity below to restart your worksheet, or check the graded results.', 'iii-dictionary') ?></h2>
			</div>
			<div class="col-sm-12">
				<label><?php _e('Follow the steps to Study online', 'iii-dictionary') ?></label>
			</div>
			<div class="col-sm-12">
				<div class="step-block">
					<!-- List homework assignment -->
					<form method="post" action="<?php echo locale_home_url()?>/?r=online-learning">
						<p><a role="button" style="color:#fff" data-toggle="collapse" href="#step1-collapse" class="step-number" title="" ><?php _e('Homework Assignment from your teachers','iii-dictionary'); ?></a></p>
						<div id="step1-collapse" class="collapse">
							<div class="box box-sapphire">
								<div class="row">
									<div class="col-sm-12">
										<div class="scroll-list2" style="max-height: 600px">
											<table class="table table-striped table-condensed ik-table1 ik-table-break-all text-center">
												<thead>
													<tr>
														<th><?php _e('Group name', 'iii-dictionary') ?></th>
														<th><?php _e('Teacher', 'iii-dictionary') ?></th>
														<th class="hidden-xs"><?php _e('No. of Homework', 'iii-dictionary') ?></th>
														<th class="hidden-xs"><?php _e('No. of Completed', 'iii-dictionary') ?></th>
														<th></th>
														<th></th>
													</tr>
												</thead>
												<tfoot>
													<tr><td colspan="6"><?php echo $pagination ?></td></tr>
												</tfoot>
												<tbody>
													<?php if(empty($user_groups->items)) : ?>
														<tr>
															<td colspan="6"><?php _e('You haven\'t joined any groups yet.', 'iii-dictionary') ?></td>
														</tr>
													<?php else : ?>
														<?php foreach($user_groups->items as $item) : ?>
															<tr>
																<td><?php echo $item->group_name ?></td>
																<td><?php echo $item->group_type_id == GROUP_CLASS ? 'SAT Prep.' : $item->teacher ?></td>
																<td class="hidden-xs"><?php echo $item->no_of_homework ?></td>
																<td><?php echo $item->completed_homework ?></td>
																<td>
																	<a href="<?php echo locale_home_url() . '/?r=online-learning&amp;gid=' . $item->group_id ?>" class="btn btn-default btn-block btn-tiny grey"><?php _e('Homeworks', 'iii-dictionary') ?></a>
																</td>
																<td><?php
																	if(!$item->is_default) : ?>
																		<button type="button" class="btn btn-default btn-block btn-tiny grey leave-grp-btn" data-gid="<?php echo $item->group_id ?>" data-gname="<?php echo $item->group_name ?>"><?php _e('Leave Group', 'iii-dictionary') ?></button>
																	<?php endif ?>
																</td>
															</tr>
															<?php endforeach ?>
													<?php endif ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="leave-group-dialog" class="modal fade modal-red-brown" aria-hidden="true">
						    <div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
						             <h3><?php _e('Leave Group', 'iii-dictionary') ?></h3>
						        </div>
						        <div class="modal-body">
									<p><?php printf(__('Do you want to leave Group: %s', 'iii-dictionary'), '<strong id="lev-group-name"></strong>') ?></p>
						        </div>
						        <div class="modal-footer">
									<div class="row">
										<div class="col-sm-6">
											<button name="leave" class="btn btn-block orange"><span class="icon-check"></span><?php _e('OK', 'iii-dictionary') ?></button>
										</div>
										<div class="col-sm-6">
											<a href="#" data-dismiss="modal" class="btn btn-block grey"><span class="icon-cancel"></span><?php _e('Cancel', 'iii-dictionary') ?></a>
										</div>
									</div>
						        </div>
						      </div>
						    </div>
						</div>
						<input type="hidden" name="gid" id="gid" value="">
					</form>
					<!-- SAT Preparation -->
					<form method="post" action="<?php echo locale_home_url()?>/?r=online-learning" id="main-form" >
						<p><a role="button" style="color:#fff" data-toggle="collapse" href="#step2-collapse" class="step-number" title="" ><?php _e('SAT Preparation and Practice Test','iii-dictionary'); ?></a></p>
						<div id="step2-collapse" class="collapse">
							<?php 
							$user_current_id = get_current_user_id();
							if($is_math_panel): 

								//Math SAT I
								$sat_class_ids = $wpdb->get_results("	SELECT DISTINCT sat_class_id 
																			FROM {$wpdb->prefix}dict_user_subscription 
																			WHERE activated_by = {$user_current_id} AND group_id = 0 AND typeid IN (7) ORDER BY sat_class_id ASC");
							
								if(!empty($sat_class_ids)){ ?>
									<div class="row" id="page-tabs-container" style="background:transparent">
										<ul id="page-tabs">
										<?php foreach ($sat_class_ids as $key => $sat_class_id) {
											$groups_sat_detail = MWDB::get_group_class_type_by('id', $sat_class_id->sat_class_id);
											?>
											<li class="page-tab tab <?php if($key == 0) echo 'active'; ?>" data-tab="box-<?php echo $groups_sat_detail->slug; ?>">
												<a href="javascript:void(0)" >
													<?php echo $groups_sat_detail->html; ?>
												</a>
											</li>
											<?php
										} ?>
										</ul>
									</div>
									<?php 
									foreach ($sat_class_ids as $key => $value1) {
										$is_sat_class_subscribed = is_sat_class_subscribed($value1->sat_class_id);
										$groups_sat_detail = MWDB::get_group_class_type_by('id', $value1->sat_class_id);
										$groups_sat = MWDB::get_groups_by_class_type($groups_sat_detail->slug);
										$is_sat_class_subscribed = is_sat_class_subscribed($value1->sat_class_id);
										?>
										<div class="tab-content" style="display:<?php if($key == 0) echo 'block'; else echo 'none'; ?>" id="box-<?php echo $groups_sat_detail->slug; ?>">
											<div class="box box-purple scroll-list2" style="max-height:500px">
												<table class="table table-striped table-condensed ik-table1 ik-table-break-all text-center">
													<thead>
														<tr>
															<th><?php _e('Content', 'iii-dictionary') ?></th>
															<th class="hidden-xs"><?php _e('No. of Worksheets', 'iii-dictionary') ?></th>
															<th><?php _e('Detail', 'iii-dictionary') ?></th>
															<th><?php _e('Start', 'iii-dictionary') ?></th>
														</tr>
													</thead>
													<tfoot>
														<tr><td colspan="4"><?php echo $pagination ?></td></tr>
													</tfoot>
													<tbody>
														<?php 
														if(!empty($groups_sat) && $is_sat_class_subscribed) :
															foreach($groups_sat as $group_sat) : ?>
																<tr>
																	<td><?php echo $group_sat->content ?></td>
																	<td class="hidden-xs"><?php echo is_null($group_sat->no_homeworks) ? 0 : $group_sat->no_homeworks ?></td>
																	<td><a href="#" class="class-detail-btn">Click</a><div><?php echo $group_sat->detail ?></div></td>
																	<td><?php
																		if(is_student_in_group(get_current_user_id(), $group_sat->id)) :
																			$sat_results = get_sat_class_score($group_sat->id);
																			if(is_sat_class_completed($sat_results)) : ?>
																				<a href="#" role="button" class="view-score" data-jid="<?php echo $group_sat->id ?>"><?php _e('Completed', 'iii-dictionary') ?></a>
																			<?php 
																			else : 
																				if($is_sat_class_subscribed): ?>
																					<a href="<?php echo home_url() . '/?r=online-learning&amp;gid=' . $group_sat->id ?>" class="links-purprle"><?php _e('Working', 'iii-dictionary') ?></a>
																				<?php
																				else: ?>
																					<a href="javascript:void(0);" class="links-purprle working-btn"><?php _e('Working', 'iii-dictionary') ?></a>
																				<?php
																				endif;
																			endif ?>
																			<table class="hidden">
																				<tbody>
																				<?php 
																				foreach($sat_results as $result) : 
																					$sheet = MWDB::get_sheet($result->sid);
																					$words = json_decode($sheet->questions, true);
																					$word_total = count($words['question']);
																					$total_correct = $result->correct_answers_count;
																					if($total_correct){
																						$percent = ($total_correct / $word_total) * 100;
																						$percent = round($percent,2);
																					}else{
																						$percent = 0;
																					}
																					?>
																					<tr>
																						<td><?php echo $result->sheet_name ?></td>
																						<td><?php echo $percent ?> %</td>
																						<td><a href="<?php echo locale_home_url(); ?>?r=homework-result&hid=<?php echo $result->hid; ?>" class="btn btn-default btn-tiny grey" >View</a></td>
																						<td><?php echo $result->submitted_on ?></td>
																					</tr>
																				<?php 
																				endforeach; 
																				if(is_sat_class_completed($sat_results)) : 
																					if(check_admin_by_id($group_sat->uid)&& $is_sat_class_subscribed):
																				?>
																					<tr>
																						<td colspan="3"></td>
																						<td><a href="<?php echo home_url() . '/?r=online-learning&amp;gid=' . $group_sat->id ?>" class="links-purprle"><?php _e('Restart', 'iii-dictionary') ?></a></td>
																					</tr>
																				<?php 
																					endif;
																				endif; ?>
																				</tbody>
																			</table>
																		<?php 
																		else : ?>
																			<a href="#" role="button" class="start-class-btn" data-cltid="<?php echo $group_sat->class_type_id; ?>" data-annoying="<?php echo is_sat_class_subscribed($group_sat->class_type_id) ? 'false' : 'true'; ?>" data-jid="<?php echo $group_sat->id ?>"><?php _e('Start', 'iii-dictionary') ?></a>
																		<?php 
																		endif ?>
																	</td>
																</tr>
															<?php endforeach;
														else : ?>
															<tr>
																<td colspan="4">
																	<?php _e('Subscription Expired', 'iii-dictionary') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																	<?php _e('No Class', 'iii-dictionary') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																	<?php _e('Re-subscribe now?', 'iii-dictionary') ?> <a href="<?php echo locale_home_url(); ?>?r=manage-subscription">(<?php _e('Yes','iii-dictionary'); ?>)</a>
																</td>
															</tr>
														<?php 
														endif ?>
													</tbody>
												</table>
											</div>
										</div>
										<?php
									} 
								}else{
									?>
									<div class="row" style="padding-left:50px;">
										<p><?php _e('You have not subscribed SAT I Preparation and Practice Test.','iii-dictionary'); ?></p>
										<p><?php _e('Subscribe now?','iii-dictionary') ?></p>
										<div class="col-md-4 col-sm-4 col-xs-6">
											<a href="<?php echo locale_home_url() ?>?r=sat-preparation/sat1prep&client=math-sat1" class="btn btn-default btn-block orange form-control"><?php _e('Yes','iii-dictionary') ?></a>
										</div>
									</div>
									<?php
								}  
								
								//Math SAT II
								$math2_sat_class_ids = $wpdb->get_results("SELECT DISTINCT sat_class_id 
																	FROM {$wpdb->prefix}dict_user_subscription 
																	WHERE activated_by = {$user_current_id} AND group_id = 0 AND typeid IN (8) ORDER BY sat_class_id ASC");
								if(!empty($math2_sat_class_ids)){ ?>
									<div class="row" id="page-tabs-container" style="background:transparent; margin-top:10px">
										<ul id="page-tabs">
										<?php foreach ($math2_sat_class_ids as $key => $math2_sat_class_id) {
											$groups_math2_sat_detail = MWDB::get_group_class_type_by('id', $math2_sat_class_id->sat_class_id);
											?>
											<li class="page-tab tab <?php if($key == 0) echo 'active'; ?>" data-tab="box-<?php echo $groups_math2_sat_detail->slug; ?>">
												<a href="javascript:void(0)" >
													<?php echo $groups_math2_sat_detail->html; ?>
												</a>
											</li>
											<?php
										} ?>
										</ul>
									</div>
									<?php 
									foreach ($math2_sat_class_ids as $key => $math2_sat_class_id) {
										$is_sat_class_subscribed = is_sat_class_subscribed($math2_sat_class_id->sat_class_id);
										$groups_math2_sat_detail = MWDB::get_group_class_type_by('id', $math2_sat_class_id->sat_class_id);
										$groups_math2_sat = MWDB::get_groups_by_class_type($groups_math2_sat_detail->slug);
										$is_sat_class_subscribed = is_sat_class_subscribed($math2_sat_class_id->sat_class_id);
										?>
										<div class="tab-content" style="display:<?php if($key == 0) echo 'block'; else echo 'none'; ?>" id="box-<?php echo $groups_math2_sat_detail->slug; ?>">
											<div class="box box-purple scroll-list2" style="max-height:500px">
												<table class="table table-striped table-condensed ik-table1 ik-table-break-all text-center">
													<thead>
														<tr>
															<th><?php _e('Content', 'iii-dictionary') ?></th>
															<th class="hidden-xs"><?php _e('No. of Worksheets', 'iii-dictionary') ?></th>
															<th><?php _e('Detail', 'iii-dictionary') ?></th>
															<th><?php _e('Start', 'iii-dictionary') ?></th>
														</tr>
													</thead>
													<tfoot>
														<tr><td colspan="4"><?php echo $pagination ?></td></tr>
													</tfoot>
													<tbody>
														<?php 
														if(!empty($groups_math2_sat) && $is_sat_class_subscribed) :
															foreach($groups_math2_sat as $group_math2_sat) : ?>
																<tr>
																	<td><?php echo $group_math2_sat->content ?></td>
																	<td class="hidden-xs"><?php echo is_null($group_math2_sat->no_homeworks) ? 0 : $group_math2_sat->no_homeworks ?></td>
																	<td><a href="#" class="class-detail-btn">Click</a><div><?php echo $group_math2_sat->detail ?></div></td>
																	<td><?php
																		if(is_student_in_group(get_current_user_id(), $group_math2_sat->id)) :
																			$math2_sat_results = get_sat_class_score($group_math2_sat->id);
																			if(is_sat_class_completed($math2_sat_results)) : ?>
																				<a href="#" role="button" class="view-score" data-jid="<?php echo $group_math2_sat->id ?>"><?php _e('Completed', 'iii-dictionary') ?></a>
																			<?php 
																			else : 
																				if($is_sat_class_subscribed): ?>
																					<a href="<?php echo home_url() . '/?r=online-learning&amp;gid=' . $group_math2_sat->id ?>" class="links-purprle"><?php _e('Working', 'iii-dictionary') ?></a>
																				<?php 
																				else: ?>
																					<a href="javascript:void(0);" class="links-purprle working-btn"><?php _e('Working', 'iii-dictionary') ?></a>
																				<?php
																				endif;
																			endif ?>
																			<table class="hidden">
																				<tbody>
																				<?php 
																				foreach($math2_sat_results as $result) : 
																					$sheet = MWDB::get_sheet($result->sid);
																					$words = json_decode($sheet->questions, true);
																					$word_total = count($words['question']);
																					$total_correct = $result->correct_answers_count;
																					if($total_correct){
																						$percent = ($total_correct / $word_total) * 100;
																						$percent = round($percent,2);
																					}else{
																						$percent = 0;
																					}
																					?>
																					<tr>
																						<td><?php echo $result->sheet_name ?></td>
																						<td><?php echo $percent ?> %</td>
																						<td><a href="<?php echo locale_home_url(); ?>?r=homework-result&hid=<?php echo $result->hid; ?>" class="btn btn-default btn-tiny grey" >View</a></td>
																						<td><?php echo $result->submitted_on ?></td>
																					</tr>
																				<?php 
																				endforeach; 
																				if(is_sat_class_completed($math2_sat_results)) : 
																					if(check_admin_by_id($group_math2_sat->uid)):
																				?>
																					<tr>
																						<td colspan="3"></td>
																						<td><a href="<?php echo home_url() . '/?r=online-learning&amp;gid=' . $group_math2_sat->id ?>" class="links-purprle"><?php _e('Restart', 'iii-dictionary') ?></a></td>
																					</tr>
																				<?php 
																					endif;
																				endif; ?>
																				</tbody>
																			</table>
																		<?php 
																		else : ?>
																			<a href="#" role="button" class="start-class-btn" data-cltid="<?php echo $group_math2_sat->class_type_id; ?>" data-annoying="<?php echo is_sat_class_subscribed($group_math2_sat->class_type_id) ? 'false' : 'true'; ?>" data-jid="<?php echo $group_math2_sat->id ?>"><?php _e('Start', 'iii-dictionary') ?></a>
																		<?php 
																		endif ?>
																	</td>
																</tr>
															<?php endforeach;
															else : ?>
																<tr><td colspan="4">
																	<?php _e('Subscription Expired', 'iii-dictionary') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																	<?php _e('No Class', 'iii-dictionary') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																	<?php _e('Re-subscribe now?', 'iii-dictionary') ?> <a href="<?php echo locale_home_url(); ?>?r=manage-subscription">(<?php _e('Yes','iii-dictionary'); ?>)</a>
																</td></tr>
														<?php endif ?>
													</tbody>
												</table>
											</div>
										</div>
										<?php
									}
								}else{
									?>
									<div class="row" style="padding-left:50px;margin-top:20px;">
										<p><?php _e('You have not subscribed SAT II Preparation and Practice Test.','iii-dictionary'); ?></p>
										<p><?php _e('Subscribe now?','iii-dictionary') ?></p>
										<div class="col-md-4 col-sm-4 col-xs-6">
											<a href="<?php echo locale_home_url() ?>?r=sat-preparation/sat2prep&client=math-sat2" class="btn btn-default btn-block orange form-control"><?php _e('Yes','iii-dictionary') ?></a>
										</div>
									</div>
									<?php
								} 
								?>

								<?php
							else: 
								
								$sat_class_ids = $wpdb->get_results("SELECT DISTINCT sat_class_id 
																	FROM {$wpdb->prefix}dict_user_subscription 
																	WHERE activated_by = {$user_current_id} AND group_id = 0 AND typeid IN (3) ORDER BY sat_class_id ASC");
								if(!empty($sat_class_ids)){
									?>
									<div class="row" id="page-tabs-container" style="background:transparent">
										<ul id="page-tabs">
										<?php foreach ($sat_class_ids as $key => $sat_class_id) {
											$groups_sat_detail = MWDB::get_group_class_type_by('id', $sat_class_id->sat_class_id);
											?>
											<li class="page-tab tab <?php if($key == 0) echo 'active'; ?>" data-tab="box-<?php echo $groups_sat_detail->slug; ?>">
												<a href="javascript:void(0)" >
													<?php echo $groups_sat_detail->html; ?>
												</a>
											</li>
											<?php
										} ?>
										</ul>
									</div>
									<?php 
									foreach ($sat_class_ids as $key => $value1) {
										$is_sat_class_subscribed = is_sat_class_subscribed($value1->sat_class_id);
										$groups_sat_detail = MWDB::get_group_class_type_by('id', $value1->sat_class_id);
										$groups_sat = MWDB::get_groups_by_class_type($groups_sat_detail->slug);
										$is_sat_class_subscribed = is_sat_class_subscribed($value1->sat_class_id);
										?>
										<div class="tab-content" style="display:<?php if($key == 0) echo 'block'; else echo 'none'; ?>" id="box-<?php echo $groups_sat_detail->slug; ?>">
											<div class="box box-purple scroll-list2" style="max-height:500px">
												<table class="table table-striped table-condensed ik-table1 ik-table-break-all text-center">
													<thead>
														<tr>
															<th><?php _e('Content', 'iii-dictionary') ?></th>
															<th class="hidden-xs"><?php _e('No. of Worksheets', 'iii-dictionary') ?></th>
															<th><?php _e('Detail', 'iii-dictionary') ?></th>
															<th><?php _e('Start', 'iii-dictionary') ?></th>
														</tr>
													</thead>
													<tfoot>
														<tr><td colspan="4"><?php echo $pagination ?></td></tr>
													</tfoot>
													<tbody>
														<?php 
														if(!empty($groups_sat)) :
															foreach($groups_sat as $group_sat) : ?>
																<tr>
																	<td><?php echo $group_sat->content ?></td>
																	<td class="hidden-xs"><?php echo is_null($group_sat->no_homeworks) ? 0 : $group_sat->no_homeworks ?></td>
																	<td><a href="#" class="class-detail-btn">Click</a><div><?php echo $group_sat->detail ?></div></td>
																	<td><?php
																		if(is_student_in_group(get_current_user_id(), $group_sat->id)) :
																			$sat_results = get_sat_class_score($group_sat->id);
																			if(is_sat_class_completed($sat_results)) : ?>
																				<a href="#" role="button" class="view-score" data-jid="<?php echo $group_sat->id ?>"><?php _e('Completed', 'iii-dictionary') ?></a>
																			<?php 
																			else : 
																				if($is_sat_class_subscribed): ?>
																					<a href="<?php echo home_url() . '/?r=online-learning&amp;gid=' . $group_sat->id ?>" class="links-purprle"><?php _e('Working', 'iii-dictionary') ?></a>
																				<?php 
																				else: ?>
																					<a href="javascript:void(0);" class="links-purprle working-btn"><?php _e('Working', 'iii-dictionary') ?></a>
																				<?php 
																				endif;
																			endif ?>
																			<table class="hidden">
																				<tbody>
																				<?php 
																				foreach($sat_results as $result) : 
																					$sheet = MWDB::get_sheet($result->sid);
																					$words = json_decode($sheet->questions, true);
																					$word_total = count($words['question']);
																					$total_correct = $result->correct_answers_count;
																					if($total_correct){
																						$percent = ($total_correct / $word_total) * 100;
																						$percent = round($percent,2);
																					}else{
																						$percent = 0;
																					}
																					?>
																					<tr>
																						<td><?php echo $result->sheet_name ?></td>
																						<td><?php echo $percent; ?> %</td>
																						<td><a href="<?php echo locale_home_url(); ?>?r=homework-result&hid=<?php echo $result->hid; ?>" class="btn btn-default btn-tiny grey" >View</a></td>
																						<td><?php echo $result->submitted_on ?></td>
																					</tr>
																				<?php 
																				endforeach; 
																				if(is_sat_class_completed($sat_results)) : 
																					if(check_admin_by_id($group_sat->uid)&& $is_sat_class_subscribed):
																				?>
																					<tr>
																						<td colspan="3"></td>
																						<td><a href="<?php echo home_url() . '/?r=online-learning&amp;gid=' . $group_sat->id ?>" class="links-purprle"><?php _e('Restart', 'iii-dictionary') ?></a></td>
																					</tr>
																				<?php 
																					endif;
																				endif; ?>
																				</tbody>
																			</table>
																		<?php 
																		else : ?>
																			<a href="#" role="button" class="start-class-btn" data-cltid="<?php echo $group_sat->class_type_id; ?>" data-annoying="<?php echo is_sat_class_subscribed($group_sat->class_type_id) ? 'false' : 'true'; ?>" data-jid="<?php echo $group_sat->id ?>"><?php _e('Start', 'iii-dictionary') ?></a>
																		<?php 
																		endif ?>
																	</td>
																</tr>
															<?php 
															endforeach;
														endif; ?>
													</tbody>
												</table>
											</div>
										</div>
										<?php
									} ?>

								<?php
								}else{
									?>
									<div class="row" style="padding-left:50px;">
										<p><?php _e('You have not subscribed SAT Preparation and Practice Test.','iii-dictionary'); ?></p>
										<p><?php _e('Subscribe now?','iii-dictionary') ?></p>
										<div class="col-md-4 col-sm-4 col-xs-6">
											<a href="<?php echo locale_home_url() ?>?r=sat-preparation" class="btn btn-default btn-block orange form-control"><?php _e('Yes','iii-dictionary') ?></a>
										</div>
									</div>
									<?php
								} 
							endif; 
							?>
						</div>
						<div id="require-modal" class="modal fade modal-purple" aria-hidden="true">
							<div class="modal-dialog">
							  <div class="modal-content">
								<div class="modal-header">
									<a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
									<h3><?php _e('Subscription Required', 'iii-dictionary') ?></h3>
								</div>
								<div class="modal-body"></div>
								<div class="modal-footer">
									<a href="<?php echo locale_home_url() ?>/?r=manage-subscription#3" class="btn btn-block orange"></a>
								</div>
							  </div>
							</div>
						</div>
						<input type="hidden" name="jid" id="jid">
						<input type="hidden" name="cltid" id="cltid">
					</form>
					<!-- Writing Practice -->
					<p><a role="button" style="color:#fff" data-toggle="collapse" href="#step3-collapse" class="step-number" title="" ><?php _e('Start Essay Writing Practice','iii-dictionary'); ?></a></p>
					<div id="step3-collapse" class="collapse" style="padding-left: 60px;">
						<h3 style="margin: 0 0 10px 0;"><?php _e('Writing Practice','iii-dictionary'); ?></h3>
						<div class="row" style="margin-bottom:10px">
							<div class="col-md-4 col-sm-4 col-xs-12">
								<a href="<?php echo locale_home_url(); ?>/?r=sat-preparation/writing" class="btn btn-default btn-block orange form-control"><span class="icon-check"> </span><?php _e('Request Tutoring','iii-dictionary'); ?></a>
							</div>
						</div>
						<p style="margin: 0 0 10px 0;"><label><?php _e('Request a teacher to edit and improve your writing.','iii-dictionary'); ?>..</label></p>
					</div>
					<!-- Math Tutoring -->
					<p><a role="button" style="color:#fff" data-toggle="collapse" href="#step4-collapse" class="step-number" title="" ><?php _e('Request Math Tutoring','iii-dictionary'); ?></a></p>
					<div id="step4-collapse" class="collapse" style="padding-left: 60px;">
						<h3 style="margin: 0 0 10px 0;"><?php _e('Math Tutoring','iii-dictionary'); ?></h3>
						<div class="row" style="margin-bottom:10px">
							<div class="col-md-4 col-sm-4 col-xs-12">
								<button type="button" class="btn btn-default btn-block orange form-control"><span class="icon-check"> </span><?php _e('Request Tutoring','iii-dictionary'); ?></button>
							</div>
						</div>
						<p style="margin: 0 0 10px 0;"><label><?php _e('Request a tutor to help you with math problems, including your math homework.','iii-dictionary'); ?>..</label></p>
					</div>
					<!-- Join Group -->
					<form method="post" action="<?php echo locale_home_url()?>/?r=online-learning">
						<p><a role="button" style="color:#fff" data-toggle="collapse" href="#step5-collapse" class="step-number" title="" ><?php _e('Join the Group (Class)','iii-dictionary'); ?></a></p>
						<div id="step5-collapse" class="collapse" style="padding-left: 60px;">
							<div class="row" style="margin-bottom:10px">
								<div class="col-sm-4">
									<div class="form-group">
										<label for="gname"><?php _e('Group name', 'iii-dictionary') ?></label>
										<input type="text" class="form-control" id="gname" name="gname" value="<?php echo $gname ?>">
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label for="gpass"><?php _e('Group password', 'iii-dictionary') ?></label>
										<input type="password" class="form-control" id="gpass" name="gpass" value="">
									</div>					
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>&nbsp;</label>
										<button type="button" class="btn btn-default btn-block orange form-control" id="join-group"><span class="icon-check"></span><?php _e('Join', 'iii-dictionary') ?></button>
									</div>
								</div>
							</div>
						</div>
						<div id="join-group-dialog" class="modal fade modal-red-brown" aria-hidden="true">
						    <div class="modal-dialog">
						      <div class="modal-content">
						        <div class="modal-header">
						            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
						             <h3><?php _e('Are you sure you are joining to this group?', 'iii-dictionary') ?></h3>
						        </div>
						        <div class="modal-body">
									<p><?php _e('Are you sure you are joining to this group?', 'iii-dictionary') ?></p>
									<p><?php _e('If this group name is provided by your teacher, your name will show up in his/her class member list and it will cost the teacher for class membership. If this is a private group, it is free.', 'iii-dictionary') ?></p>
									<hr>
									<h4 class="text-warning2"><?php _e('Do you want to join group chat (chat board for this group)?', 'iii-dictionary') ?></h4>
									<div class="row">
										<div class="col-xs-3 col-xs-offset-3">
											<div class="radio radio-style1">															
												<input id="rdo-yes" type="radio" name="joinchat" value="1" checked>
												<label for="rdo-yes"><?php _e('Yes', 'iii-dictionary') ?></label>
											</div>
										</div>
										<div class="col-xs-3">
											<div class="radio radio-style1">
												<input id="rdo-no" type="radio" name="joinchat" value="0">  
												<label for="rdo-no"><?php _e('No', 'iii-dictionary') ?></label>
											</div>
										</div>
									</div>
						        </div>
						        <div class="modal-footer">			
									<div class="row">
										<div class="col-sm-6">
											</div>
											<div class="panel-heading">
										      <h4 class="panel-title">
										        <a data-toggle="collapse" href="#collapse1">
										        	<div class="col-sm-6">
														<button type="submit" class="btn btn-block orange confirm"><span class="icon-accept"></span><?php _e('Yes, Join', 'iii-dictionary') ?></button>
													</div>
										        </a>
										      </h4>
										    </div>
										    <!--agree pay-->
										    <div id="collapse1" class="panel-collapse collapse">
												<div class="modal-body-n">
													<p><?php _e('This group is subscription only. Monthly $50. Agree ?')?></p>
													<p><?php _e('If the user does not have enough points ?')?></p>
													<a href="<?php echo get_site_url()?>?r=payments"><?php _e('Click help payments')?></a>

													<hr>
													<h4 class="text-warning2"><?php _e('This group is subscription only. Monthly $50. Agree ?', 'iii-dictionary') ?></h4>
							        			</div>
										        <div class="modal-footer">			
													<div class="row">
														<div class="row">
															<div class="col-sm-6">
																<button type="submit" name="join" class="btn btn-block orange confirm"><span class="icon-accept"></span><?php _e('Agree', 'iii-dictionary') ?></button>
															</div>
															<div class="col-sm-6">
																<a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block grey secondary"><span class="icon-cancel"></span><?php _e('Cancel', 'iii-dictionary') ?></a>
															</div>
														</div>
													</div>
										        </div>
										    </div>
										</div>
									</div>
						        </div>
						      </div>
						    </div>
						</div>
					</form>
				</div>
			</div>




	
		<?php else : ?>
			<?php 
			// check subscribe group
			if($gid){
				$rec = $wpdb->get_row("SELECT class_type_id FROM {$wpdb->prefix}dict_group_details WHERE group_id = {$gid}");
				$class_type_id = $rec->class_type_id;
				$is_sat_class_subscribed = is_sat_class_subscribed($class_type_id);
			}
			?>

			<form method="post" action="<?php echo locale_home_url()?>/?r=online-learning<?php echo $gid ? '&amp;gid=' . $gid : '' ?>">
				<div class="col-sm-12">
					<h2 class="title-border"><?php _e('List of Homeworks', 'iii-dictionary') ?></h2>
				</div>
				<div class="col-sm-12">
					<div class="box">
						<div class="row box-header">
							<div class="col-sm-4">
								<h4><?php _e('Teacher:', 'iii-dictionary') ?> <span id="t-name" style="color: #fff"><?php echo $group->display_name ?></span></h4>
							</div>
							<div class="col-sm-8">
								<h4><?php _e('Group Name:', 'iii-dictionary') ?> <span id="g-name" style="color: #fff"><?php echo $group->name ?></span></h4>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<table class="table table-striped table-condensed ik-table1 text-center vertical-middle">
									<thead>
										<tr>
											<th><?php _e('Homework Name', 'iii-dictionary') ?></th>
											<th><?php _e('Due date', 'iii-dictionary') ?></th>
											<th class="hidden-xs"><?php _e('Status', 'iii-dictionary') ?></th>
											<th><?php _e('Result', 'iii-dictionary') ?></th>
											<th><?php _e('Request Grading', 'iii-dictionary') ?></th>
											<th></th>
										</tr>
									</thead>
									<tfoot>
										<tr><td colspan="6"><?php echo $pagination ?></td></tr>
									</tfoot>
									<tbody>
										<?php if(!empty($homeworks->items)) : foreach($homeworks->items as $hw) : ?>
											<tr>
												<td><?php echo empty($hw->homework_name) ? $hw->sheet_name : $hw->homework_name ?></td>
												<td><?php echo $hw->deadline == '0000-00-00' ? 'No deadline' : ik_date_format($hw->deadline) ?></td>
												<?php if(is_null($hw->finished)) {
														$txt = __('New', 'iii-dictionary');
														$td_class = ' text-primary';
													} 
													else if(!$hw->finished) {
														$txt = __('Unfinished', 'iii-dictionary');
														$td_class = ' text-warning2';
													}
													else {
														if($hw->deadline != '0000-00-00' && $hw->submitted_on > $hw->deadline) {
															$txt = __('Over Due', 'iii-dictionary');
															$td_class = ' text-danger';
														}
														else {
															$txt = __('Finished', 'iii-dictionary');
															$td_class = ' text-success';
														}
													} ?>
												<td class="hidden-xs"><strong class="<?php echo $td_class ?>"><?php echo $txt ?></strong></td>
												<td>
													<?php if(!is_null($hw->attempted_on) && $hw->assignment_id != ASSIGNMENT_REPORT) : ?>
														<a href="<?php echo locale_home_url() . '/?r=homework-result&amp;hid=' . $hw->hid . '&amp;sid=' . $current_user_id ?>" class="btn btn-default btn-tiny grey"><?php _e('View', 'iii-dictionary') ?></a>
													<?php else : ?>
														<?php echo $hw->score ?> %
													<?php endif ?>
												</td>
												<td><?php
														if($hw->homework_type_id == HOMEWORK_CLASS && $hw->finished) :
															$can_retry = $hw->is_retryable;
															if(ik_validate_date($hw->finished_on)) {
																// homework is graded
																$btn_text = __('Graded', 'iii-dictionary');
																$btn_disabled = ' disabled';
															} else if (ik_validate_date($hw->accepted_on)) {
																// grading request is accepted by a teacher
																$btn_text = __('Accepted', 'iii-dictionary');
																$btn_disabled = ' disabled';
																$can_retry = false;
															} else if (ik_validate_date($hw->requested_on)) {
																// grading request is requested
																$btn_text = __('Requested', 'iii-dictionary');
																$btn_disabled = ' disabled';
																$can_retry = false;
															} else if($hw->assignment_id == ASSIGNMENT_WRITING) {
																// request a grading if homework is writing
																$btn_text = __('Request', 'iii-dictionary');
																$btn_disabled = '';
															} else {
																// auto-graded homework
																$btn_text = __('Graded', 'iii-dictionary');
																$btn_disabled = ' disabled';
															} ?>
															<button type="button" data-hid="<?php echo $hw->hid ?>" data-hrid="<?php echo $hw->homework_result_id ?>" data-cost="<?php echo $hw->grading_price ?>" class="btn btn-default btn-block btn-tiny grey request-grading"<?php echo $btn_disabled ?>><?php echo $btn_text ?></button>
													<?php else : ?>
														<?php if(!is_null($hw->finished) && in_array($hw->assignment_id, array(ASSIGNMENT_WRITING,ASSIGNMENT_REPORT)) === true) :
																if(!$hw->graded) :
																	_e('Waiting for grading', 'iii-dictionary');
																else :
																	_e('Graded', 'iii-dictionary');
																endif;
															endif ?>
													<?php endif
												?></td>
												<td>
													<?php 
													if(!$hw->finished || is_null($hw->finished)) :
														$practice_url = MWHtml::get_practice_page_url($hw->assignment_id) . '&amp;hid=' . $hw->hid;
														$homework_url = MWHtml::get_practice_page_url($hw->assignment_id) . '&amp;mode=homework&amp;hid=' . $hw->hid;
															if($hw->assignment_id != ASSIGNMENT_REPORT) : ?>
																<a href="#" data-practice-url="<?php echo $practice_url ?>" data-homework-url="<?php echo $homework_url ?>" data-for-practice="<?php echo $hw->for_practice ?>" data-startnew="<?php echo is_null($hw->finished) ? 1 : 0 ?>" class="btn btn-default btn-block btn-tiny orange goto-homework"><?php _e('Do homework', 'iii-dictionary') ?></a>
															<?php else :
																$rp_url = $hw->for_practice ? $practice_url : $homework_url ?>
																<a href="<?php echo $rp_url ?>" class="btn btn-default btn-block btn-tiny orange"><?php _e('Do homework', 'iii-dictionary') ?></a>
															<?php endif ?>
													<?php 
													elseif($hw->homework_type_id == HOMEWORK_CLASS && $can_retry) : ?>
														<button type="submit" name="retry" class="btn btn-default btn-block btn-tiny orange retry-homework"><?php _e('Retry homework', 'iii-dictionary') ?></button>
														<input type="hidden" name="rid" value="<?php echo $hw->id ?>">
														<input type="hidden" name="sid" value="<?php echo $hw->sheet_id ?>">
														<input type="hidden" name="aid" value="<?php echo $hw->assignment_id ?>">
													<?php 
													elseif (check_admin_by_id($hw->created_by)): ?>
														<button type="submit" name="retry" class="btn btn-default btn-block btn-tiny orange retry-homework"><?php _e('Retry homework', 'iii-dictionary') ?></button>
														<input type="hidden" name="rid" value="<?php echo $hw->id ?>">
														<input type="hidden" name="sid" value="<?php echo $hw->sheet_id ?>">
														<input type="hidden" name="aid" value="<?php echo $hw->assignment_id ?>">
													<?php
													endif; ?>
												</td>
											</tr>
										<?php endforeach; else : ?>
											<tr>
												<td colspan="7"><?php _e('No homework assigned to this Group yet', 'iii-dictionary') ?></td>
											</tr>
										<?php endif ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-4 col-sm-offset-4 omg_average" >
					<?php printf(__('Average : %d %s', 'iii-dictionary'), $_averge, '%') ?>
				</div>
				<div class="col-sm-4 " style="margin-top: 20px">
					<div class="form-group">
						<a href="<?php echo locale_home_url() ?>/?r=online-learning" class="btn btn-default grey form-control"><span class="icon-goback"></span><?php _e('Go back', 'iii-dictionary') ?></a>
					</div>
				</div>
				<input type="hidden" id="uref" value="<?php echo $uref ?>">

			<?php
				ik_enqueue_js_messages('test_inst', __('This is the Test assigned by your teacher. The score will be displayed at Homework Status panel.', 'iii-dictionary'));
				ik_enqueue_js_messages('practice_inst', __('This is Practice Worksheet sent by your teacher', 'iii-dictionary'));
				ik_enqueue_js_messages('unfinished_homework', __('You have more than 2 unfinished homeworks. Please complete it before starting another one.', 'iii-dictionary'));
				ik_enqueue_js_messages('point_err', sprintf(__('Your current points is <strong>%d</strong> pts. You don\'t have enough points to request grading for this homework', 'iii-dictionary'), ik_get_user_points($current_user_id)));
			?>

				<div id="request-grading-dialog" class="modal fade modal-red-brown" aria-hidden="true">
				    <div class="modal-dialog">
				      <div class="modal-content">
				        <div class="modal-header">
				            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
				             <h3><?php _e('Request Grading', 'iii-dictionary') ?></h3>
				        </div>
				        <div class="modal-body">
							<?php printf(__('This grading and editing your writing costs %s points.', 'iii-dictionary'), '<strong id="grading-cost"></strong>') ?>
							<a href="<?php echo locale_home_url() ?>/?r=manage-subscription"><?php _e('Need Points?', 'iii-dictionary') ?></a>
							<p class="text-danger" id="request-grading-err"></p>
				        </div>
				        <div class="modal-footer">
							<div class="row">
								<div class="col-sm-6">
									<button name="request-grading" class="btn btn-block orange"><span class="icon-check"></span><?php _e('OK', 'iii-dictionary') ?></button>
								</div>
								<div class="col-sm-6">
									<a href="#" data-dismiss="modal" class="btn btn-block grey"><span class="icon-cancel"></span><?php _e('Cancel', 'iii-dictionary') ?></a>
								</div>
							</div>
				        </div>
						<input type="hidden" name="hrid" id="hrid">
						<input type="hidden" name="hid" id="hid">
				      </div>
				    </div>
				</div>
				<input type="hidden" name="task" id="task" value="">
				<input type="hidden" name="gid" id="gid" value="">
				<input type="hidden" id="unfinished_homework" value="<?php echo is_homework_unfinished() ? 1 : 0 ?>">
			</form>
		<?php endif ?>

		</div>
		

<div id="switch-mode-dialog" class="modal fade modal-red-brown" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
             <h3><?php _e('Starting Homework', 'iii-dictionary') ?></h3>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">			
			<div class="row">
				<div class="col-sm-6 col-sm-offset-6">
					<a href="#" id="btn-practice" class="btn btn-block orange"><span class="icon-accept"></span><?php _e('OK', 'iii-dictionary') ?></a>
				</div>
			</div>
        </div>
      </div>
    </div>
</div>
<div class="modal fade modal-purple modal-large" id="class-detail-modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
		<h3 class="modal-title" id="myModalLabel"><?php _e('Class Detail', 'iii-dictionary') ?></h3>
	  </div>
	  <div class="modal-body"></div>
	</div>
  </div>
</div>

<div class="modal fade modal-purple modal-large" id="view-score-modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
		<h3 class="modal-title" id="myModalLabel"><?php _e('View Score', 'iii-dictionary') ?></h3>
	  </div>
	  <div class="modal-body">
		<table class="table table-striped table-condensed ik-table1 ik-table-break-all text-center" id="table-score">
			<thead><tr>
					<th><?php _e('Worksheet Name', 'iii-dictionary') ?></th>
					<th><?php _e('Score', 'iii-dictionary') ?></th>
					<th><?php _e('Result', 'iii-dictionary') ?></th>
					<th><?php _e('Completed Date', 'iii-dictionary') ?></th>
			</tr></thead>
			<tbody></tbody>
		</table>
	  </div>
	</div>
  </div>
</div>

<div id="require-modal" class="modal fade modal-purple" aria-hidden="true">
	<div class="modal-dialog">
	  <div class="modal-content">
		<div class="modal-header">
			<a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
			<h3><?php _e('Subscription Required', 'iii-dictionary') ?></h3>
		</div>
		<div class="modal-body"></div>
		<div class="modal-footer">
			<a href="<?php echo locale_home_url() ?>/?r=manage-subscription#3" class="btn btn-block orange"></a>
		</div>
	  </div>
	</div>
</div>

<script>
	var ypoints = <?php echo ik_get_user_points($current_user_id) ?>;
	var annoying = <?php echo $is_sat_class_subscribed ? 'false' : 'true' ?>;
</script>
<?php if(!$is_math_panel) : ?>
	<?php get_dict_footer() ?>
<?php else : ?>
	<?php get_math_footer() ?>
<?php endif ?>