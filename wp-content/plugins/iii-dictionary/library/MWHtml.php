<?php

class MWHtml {
    /*
     * generate Language Selectbox
     *
     * @param int   $selected		Selected value
     * @param array $options		select box options. Available options:
     * 					math-number input-box			- $options['first_option']		 first option text. The first option has empty value
     * 								- $options['id']  				 id of the select box
     * 								- $options['name']  			 name of the select box
     * 								- $options['class']  			 additional classes of the select box
     */
   
    public static function select_languages($selected = '', $options = array()) {
        $options['id'] = $options['id'] != '' ? $options['id'] : 'sel-lang';
        $options['name'] = $options['name'] != '' ? $options['name'] : 'sel-lang';
        $options['class'] = $options['class'] != '' ? ' ' . $options['class'] : '';

        $select_options = array(
            'en' => __('English', 'iii-dictionary'),
            'ja' => __('Japanese', 'iii-dictionary'),
            'ko' => __('Korean', 'iii-dictionary'),
            //'vi' => __('Vietnamese', 'iii-dictionary'),
            'zh' => __('Chinese', 'iii-dictionary')
        );
        ?>
        <select class="select-box-it<?php echo $options['class'] ?>" name="<?php echo $options['name'] ?>" id="<?php echo $options['id'] ?>">
            <?php if ($options['first_option'] != '') : ?>
                <option value=""><?php echo $options['first_option'] ?></option>
            <?php endif ?>

            <?php foreach ($select_options as $value => $text) : ?>
                <option value="<?php echo $value ?>"<?php echo $value == $selected ? ' selected' : '' ?>><?php echo $text ?></option>
            <?php endforeach ?>
        </select>
        <?php
    }

    /*
     * generate Grade Selectbox
     *
     * @param string $type			Grade type. Accept: ENGLISH, MATH
     * @param int $selected		Selected value
     * @param array $options
     */

    public static function select_grades($type = 'ENGLISH', $selected = '', $options = array()) {
        global $wpdb;

        $options['id'] = $options['id'] != '' ? ' id="' . $options['id'] . '"' : '';
        $options['name'] = $options['name'] != '' ? ' name="' . $options['name'] . '"' : '';
        $options['class'] = $options['class'] != '' ? ' ' . $options['class'] : '';
        $options['first_option'] = empty($options['first_option']) ? __('-Subject-', 'iii-dictionary') : $options['first_option'];
        $options['level'] = empty($options['level']) ? 2 : $options['level'];

        $admin_only = is_mw_admin() || is_mw_super_admin() ? 1 : 0;

        $query = 'SELECT id, name 
				FROM ' . $wpdb->prefix . 'dict_grades
				WHERE type = \'' . $type . '\' AND level = ' . $options['level'];

        if (!$admin_only) {
            if (!is_sat_special_group()) {
                $query .= ' AND admin_only = 0';
            }
            /*
              if(is_member_SAT()) {
              $query .= ' AND id NOT IN (SELECT id FROM ' . $wpdb->prefix . 'dict_grades WHERE id != '. SAT_GRADE .' AND admin_only = 1)';
              }else {
              $query .= ' AND admin_only = 0';
              }
             */
        }

        $grades = $wpdb->get_results($query);
       // echo $grade;
        ?>
<!--        <select class="select-box-it<?php echo $options['class'] ?>" <?php
        echo $options['name'];
        echo $options['id']
        ?>>
-->            
<!--<option value=""><?php echo $options['first_option'] ?></option>-->
            <?php foreach ($grades as $grade) : ?>
                <option value="<?php echo $grade->id ?>"<?php echo $grade->id == $selected ? ' selected' : '' ?>><?php echo $grade->name ?></option>
            <?php endforeach ?>
        </select>
        <?php
    }

    /*
     * generate Homeworks Assignments Select box
     * English asignment only
     */

    public static function sel_assignments($selected = '', $get_form = false, $questions = array(), $first_option = '', $name = 'assignments', $class = '', $id = 'assignments', $vocab_option = true) {
        global $wpdb;
        $layout = isset($_GET['layout']) ? $_GET['layout'] : '';
        $types = $wpdb->get_results(
                'SELECT a.id, has.name
			 FROM ' . $wpdb->prefix . 'dict_homework_assignments AS a
			 JOIN ' . $wpdb->prefix . 'dict_homework_assignment_langs AS has ON has.assignment_id = a.id
			 WHERE type = \'ENGLISH\'   AND lang = \'' . get_short_lang_code() . '\''
        );

        $class = $class == '' ? '' : ' ' . $class;
        $html = $js = '';
        ?>
        <select class="select-box-it<?php echo $class ?>" name="<?php echo $name ?>" id="<?php echo $id ?>">
            <?php if ($first_option != '') : ?>
                <option value=""><?php echo $first_option ?></option>
            <?php endif ?>
            <?php
            foreach ($types as $type) :
                if ($type->id != ASSIGNMENT_VOCAB_BUILDER && $type->id != ASSIGNMENT_REPORT) :
                    ?>
                    <option value="<?php echo $type->id ?>"<?php echo $selected == $type->id ? ' selected' : '' ?>><?php echo $type->name ?></option>
                    <?php
                else :
                    if (!is_admin_panel() && $vocab_option || ($layout != 'create' && is_admin_panel())) :
                        ?>
                        <option value="<?php echo $type->id ?>"<?php echo $selected == $type->id ? ' selected' : '' ?>><?php echo $type->name ?></option>
                        <?php
                    endif;
                endif
                ?>
            <?php endforeach ?>
    </select>

        <?php
        /* pre-prepared html for sheet input form. TODO: return 1 type if $cid provied */

        if ($get_form) {
            switch ($selected) {
                case ASSIGNMENT_SPELLING:
                    for ($i = 1; $i <= 20; $i++) {
                        $html .= '<tr>';
                        $html .= '<td class="order-number"><span>' . $i . '.</span></td>';
                        $html .= '<td><input type="text" name="words[]" class="input-box-style2" autocomplete="off" value="' . $questions[$i - 1] . '"></td>';
                        $html .= '</tr>';
                    }
                    break;
                case ASSIGNMENT_VOCAB_GRAMMAR:
                    for ($i = 1; $i <= 20; $i++) {
                        $html .= '<tr data-index="' . $i . '">';
                        $html .= '<td class="order-number"><span>' . $i . '.</span></td>';
                        $html .= '<td><a class="btn btn-tiny orange" href="#" onClick="return false">Subject</a><input type="text" name="words[quiz][]" class="quiz_input" value="' . $questions['quiz'][$i - 1] . '"></td>';
                        $html .= '<td><a class="btn btn-tiny orange" href="#" onClick="return false">Question</a><input type="text" name="words[question][]" class="sentence_input" value="' . $questions['question'][$i - 1] . '"></td>';
                        $html .= '<td><input type="text" name="words[c_answer][]" class="input-box-style2" autocomplete="off" value="' . $questions['c_answer'][$i - 1] . '"></td>';
                        $html .= '<td><input type="text" name="words[w_answer1][]" class="input-box-style2" autocomplete="off" value="' . $questions['w_answer1'][$i - 1] . '"></td>';
                        $html .= '<td><input type="text" name="words[w_answer2][]" class="input-box-style2" autocomplete="off" value="' . $questions['w_answer2'][$i - 1] . '"></td>';
                        $html .= '<td><input type="text" name="words[w_answer3][]" class="input-box-style2" autocomplete="off" value="' . $questions['w_answer3'][$i - 1] . '"></td>';
                        $html .= '<td><input type="text" name="words[w_answer4][]" class="input-box-style2" autocomplete="off" value="' . $questions['w_answer4'][$i - 1] . '"></td>';
                        $html .= '</tr>';
                    }
                    break;
                case ASSIGNMENT_READING:
                    for ($i = 1; $i <= 20; $i++) {
                        $html .= '<tr data-index="' . $i . '">';
                        $html .= '<td class="order-number"><span>' . $i . '.</span></td>';
                        $html .= '<td><input type="text" name="words[quiz][]" class="input-box-style2" value="' . esc_html($questions['quiz'][$i - 1]) . '"></td>';
                        $html .= '<td class="hidden"><a class="btn btn-tiny orange" href="#" onClick="return false">Question</a><input type="text" name="words[question][]" class="sentence_input" value="' . esc_html($questions['question'][$i - 1]) . '"></td>';
                        $html .= '<td class="hidden"><a class="btn btn-tiny orange" href="#" onClick="return false">Correct</a><input type="text" name="words[c_answer][]" class="sentence_input" value="' . esc_html($questions['c_answer'][$i - 1]) . '"></td>';
                        $html .= '<td class="hidden"><a class="btn btn-tiny orange" href="#" onClick="return false">Incorrect 1</a><input type="text" name="words[w_answer1][]" class="sentence_input" value="' . esc_html($questions['w_answer1'][$i - 1]) . '"></td>';
                        $html .= '<td class="hidden"><a class="btn btn-tiny orange" href="#" onClick="return false">Incorrect 2</a><input type="text" name="words[w_answer2][]" class="sentence_input" value="' . esc_html($questions['w_answer2'][$i - 1]) . '"></td>';
                        $html .= '<td class="hidden"><a class="btn btn-tiny orange" href="#" onClick="return false">Incorrect 3</a><input type="text" name="words[w_answer3][]" class="sentence_input" value="' . esc_html($questions['w_answer3'][$i - 1]) . '"></td>';
                        $html .= '<td class="hidden"><a class="btn btn-tiny orange" href="#" onClick="return false">Incorrect 4</a><input type="text" name="words[w_answer4][]" class="sentence_input" value="' . esc_html($questions['w_answer4'][$i - 1]) . '"></td>';
                        $html .= '</tr>';
                    }
                    break;
                case ASSIGNMENT_WRITING:
                    for ($i = 1; $i <= 20; $i++) {
                        $html .= '<tr data-index="' . $i . '">';
                        $html .= '<td class="order-number"><span>' . $i . '.</span></td>';
                        $html .= '<td><input type="text" name="words[quiz][]" class="input-box-style2" value="' . $questions['quiz'][$i - 1] . '"></td>';
                        $html .= '<td class="hidden"><textarea name="words[question][]" class="sentence_input">' . $questions['question'][$i - 1] . '</textarea></td>';
                        $html .= '</tr>';
                    }
                    break;
                case ASSIGNMENT_VOCAB_BUILDER:
                    if (!is_admin_panel()) {
                        for ($i = 1; $i <= 20; $i++) {
                            $html .= '<tr>';
                            $html .= '<td class="order-number"><span>' . $i . '.</span></td>';
                            $html .= '<td class="fc"><input type="text" name="words[word][]" class="input-box-style2" autocomplete="off" value="' . $questions['word'][$i - 1] . '" placeholder="Word"></td>';
                            $html .= '<td><input type="text" name="words[sentence][]" class="input-box-style2" autocomplete="off" value="' . $questions['sentence'][$i - 1] . '" placeholder="Sentence"></td>';
                            $html .= '</tr>';
                        }
                    }
                    break;
            }

            $js .= '<script>
					(function($){
						$(function(){';

            if ($selected == ASSIGNMENT_READING) {
                $js .= '$("#reading-passage-block").show();';
            }
            if ($selected != ASSIGNMENT_SPELLING) {
                $js .= '$("#check-word").hide();';
            }
            if ($selected == ASSIGNMENT_REPORT) {
                $js .= 'var _h = $("#sheet-header-block"); _h.removeClass("col-md-5");
										$("#import-block").hide(); $("#questions-sheet").hide();$("#report-assignment-block").show();';
            }
            if ($selected == ASSIGNMENT_VOCAB_BUILDER) {
                $js .= '$("#description-block").hide();';
            }

            // ugly hack
            if (!isset($_POST['words']) && !isset($_GET['cid'])) {
                $js .= 'setup_sheet_layout($("#' . $id . '").val());';
            }

            $js .= '$("#' . $id . '").change(function(){
								setup_sheet_layout($(this).val());
							});
						});
					})(jQuery);
				</script>';

            return array('html' => $html, 'js' => $js);
        }
    }

    /*
     * generate Homeworks Types Select box
     *
     * @param int   $selected	selected value
     * @param array $options	select box options. Available options:
     * 							- $options['first_option']		 first option text. The first option has empty value
     * 							- $options['admin_panel']  		 select box is displayed in admin panel
     * 							- $options['subscribed_option']  display "Subscribed" option
     * 							- $options['id']  				 id of the select box
     * 							- $options['name']  			 name of the select box
     * 							- $options['class']  			 additional classes of the select box
     */

    public static function sel_homework_types($selected = '', $options = array()) {
        // set default value
        if (!isset($options['admin_panel']))
            $options['admin_panel'] = false;
        if (!isset($options['subscribed_option']))
            $options['subscribed_option'] = false;
        $options['id'] = $options['id'] != '' ? $options['id'] : 'homework-types';
        $options['name'] = $options['name'] != '' ? $options['name'] : 'homework-types';
        $options['class'] = $options['class'] != '' ? ' ' . $options['class'] : '';

        // "Public" option is always on
        $select_options = array(HOMEWORK_PUBLIC => __('Public', 'iii-dictionary'));

        if (!$options['admin_panel']) {
            $select_options[HOMEWORK_MY_OWN] = __('My Own', 'iii-dictionary');
            $select_options[HOMEWORK_LICENSED] = __('Licensed', 'iii-dictionary');
        }

        if ($options['subscribed_option']) {
            $select_options[HOMEWORK_SUBSCRIBED] = __('Subscribed', 'iii-dictionary');
        }

        // if current user is admin, print "Class" option
        if (is_mw_admin() || is_mw_super_admin()) {
            $select_options[HOMEWORK_CLASS] = __('Class', 'iii-dictionary');
        }
        ?>
        <select class="select-box-it<?php echo $options['class'] ?>" name="<?php echo $options['name'] ?>" id="<?php echo $options['id'] ?>">
            <?php if ($options['first_option'] != '') : ?>
                <option value=""><?php echo $options['first_option'] ?></option>
            <?php endif ?>

            <?php foreach ($select_options as $value => $text) : ?>
                <option value="<?php echo $value ?>"<?php echo $value == $selected ? ' selected' : '' ?>><?php echo $text ?></option>
            <?php endforeach ?>
        </select>
        <?php
    }

    /*
     * generate Homeworks Types Select box
     */

    public static function sel_sheet_categories($selected = '', $admin = true, $name = 'sheet-categories', $class = '', $id = 'sheet-categories') {
        global $wpdb;

        $categories = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'dict_sheet_categories');

        $class = $class == '' ? '' : ' ' . $class;
        ?>
        <select class="select-box-it<?php echo $class ?>" name="<?php echo $name ?>" id="<?php echo $id ?>">
            <?php foreach ($categories as $category) : ?>
                <?php if ($category->id != 5) : ?>
                    <option value="<?php echo $category->id ?>"<?php echo $selected == $category->id ? ' selected' : '' ?>><?php echo $category->name ?></option>
                <?php else : ?>
                    <?php if ($admin) : ?>
                        <option value="<?php echo $category->id ?>"<?php echo $selected == $category->id ? ' selected' : '' ?>><?php echo $category->name ?></option>
                    <?php endif ?>
                <?php endif ?>
            <?php endforeach ?>
        </select>
        <?php
    }

    /*
     * generate Math assignments selectbox
     *
     * @param mixed $selected		Selected value
     * @param array $options
     */

    public static function sel_math_assignments($selected = 1, $options = array('name' => 'math-assignments', 'id' => 'math-assignments')) {
        global $wpdb;

        if (!empty($options['first-option'])) {
            $options['first-option'] = '<option value="">' . $options['first-option'] . '</option>';
        }

        if (!empty($options['class'])) {
            $options['class'] = ' ' . $options['class'];
        }

        $assignments = $wpdb->get_results(
                'SELECT a.id, has.name
			 FROM ' . $wpdb->prefix . 'dict_homework_assignments AS a
			 JOIN ' . $wpdb->prefix . 'dict_homework_assignment_langs AS has ON has.assignment_id = a.id
			 WHERE type = \'MATH\' AND lang = \'' . get_short_lang_code() . '\''
        );
        ?>
        <select class="select-box-it form-control<?php echo $options['class'] ?>" id="<?php echo $options['id'] ?>" name="<?php echo $options['name'] ?>">
            <?php echo $options['first-option'] ?>
            <?php foreach ($assignments as $item) : ?>
                <option value="<?php echo $item->id ?>"<?php echo $selected == $item->id ? ' selected' : '' ?>><?php echo $item->name ?></option>
            <?php endforeach ?>
        </select>
        <?php
    }

    /*
     * generate create question form for math sheet
     *
     * @param string $sel_assignments_id		id of select tag
     * @param array $data			inputed data
     */

    public static function math_worksheet_form($data = array(), $sel_assignments_id = 'math-assignments') {
        $signs = array('+' => 'Addition', '-' => 'Subtraction', 'x' => 'Multiplication');
        $flashcard_ops = array('' => '(none)', '+' => '+', '-' => '-', 'x' => 'x', '247' => '&divide;');
        ?>
        <div id="math-sheet-form-<?php echo MATH_ASSIGNMENT_SINGLE_DIGIT ?>" class="math-sheet-form">
            <div class="form-group">
                <input type="text" class="form-control" name="questions[question]" placeholder="<?php _e('Question', 'iii-dictionary') ?>" value="<?php echo $data['question'] ?>">
            </div>
            <table class="table table-striped table-condensed ik-table1 vertical-middle text-center">
                <thead>
                    <tr><th><?php _e('Comment', 'iii-dictionary') ?></th><th><?php _e('Sign', 'iii-dictionary') ?></th><th><?php _e('Number', 'iii-dictionary') ?></th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Number 1', 'iii-dictionary') ?></td>
                        <td>
                            <select class="select-box-it" name="questions[sign]" id="sign-sel-single">
                                <?php foreach ($signs as $sign => $name) : ?>
                                    <option value="<?php echo $sign ?>"<?php echo $sign == $data['sign'] ? ' selected' : '' ?>><?php echo $name ?></option>
                                <?php endforeach ?>
                            </select>
                        </td>
                        <td><input type="text" name="questions[op1]" class="input-box-style2 num-box" value="<?php echo $data['op1'] ?>"></td>
                    </tr>
                    <tr>
                        <td><?php _e('Number 2', 'iii-dictionary') ?></td><td></td>
                        <td><input type="text" name="questions[op2]" class="input-box-style2 num-box" value="<?php echo $data['op2'] ?>"></td>
                    </tr>
                    <tr>
                        <td><?php _e('Partial Sum', 'iii-dictionary') ?></td>
                        <td><?php _e('Sum', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[step][s1]" class="input-box-style2 num-box" value="<?php echo $data['step']['s1'] ?>"></td>
                    </tr>
                    <tr>
                        <td id="_carry_lbl"><?php _e('Carry', 'iii-dictionary') ?></td>
                        <td><?php _e('Carry', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[step][s2]" class="input-box-style2 num-box" value="<?php echo $data['step']['s2'] ?>"></td>
                    </tr>
                    <tr>
                        <td><?php _e('Answer', 'iii-dictionary') ?></td><td></td>
                        <td><input type="text" name="questions[step][s3]" class="input-box-style2 num-box" value="<?php echo $data['step']['s3'] ?>"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="math-sheet-form-<?php echo MATH_ASSIGNMENT_TWO_DIGIT_MUL ?>" class="math-sheet-form">
            <div class="form-group">
                <input type="text" class="form-control" name="questions[question]" placeholder="<?php _e('Question', 'iii-dictionary') ?>" value="<?php echo $data['question'] ?>">
            </div>
            <table class="table table-striped table-condensed ik-table1 vertical-middle text-center">
                <thead>
                    <tr><th><?php _e('Comment', 'iii-dictionary') ?></th><th><?php _e('Number', 'iii-dictionary') ?></th><th><?php _e('Note', 'iii-dictionary') ?></th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Multiplicand', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[op1]" class="input-box-style2 num-box" value="<?php echo $data['op1'] ?>">
                            <input type="hidden" name="questions[sign]" value="x"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><?php _e('Multiplier', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[op2]" class="input-box-style2 num-box" value="<?php echo $data['op2'] ?>"></td><td></td>
                    </tr>
                    <tr>
                        <td><?php _e('Partial Sum', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[step][s1]" class="input-box-style2 num-box" value="<?php echo $data['step']['s1'] ?>"></td><td></td>
                    </tr>
                    <tr>
                        <td><?php _e('Carry', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[step][s2]" class="input-box-style2 num-box" value="<?php echo $data['step']['s2'] ?>"></td><td></td>
                    </tr>
                    <tr>
                        <td><?php _e('Partial Sum', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[step][s3]" class="input-box-style2 num-box" value="<?php echo $data['step']['s3'] ?>"></td><td></td>
                    </tr>
                    <tr>
                        <td><?php _e('Carry', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[step][s4]" class="input-box-style2 num-box" value="<?php echo $data['step']['s4'] ?>"></td><td></td>
                    </tr>
                    <tr>
                        <td><?php _e('Partial Sum', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[step][s5]" class="input-box-style2 num-box" value="<?php echo $data['step']['s5'] ?>"></td><td></td>
                    </tr>
                    <tr>
                        <td><?php _e('Carry', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[step][s6]" class="input-box-style2 num-box" value="<?php echo $data['step']['s6'] ?>"></td><td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="text" name="questions[step][s7]" class="input-box-style2 num-box" value="<?php echo $data['step']['s7'] ?>"></td>
                        <td><?php _e('Answer', 'iii-dictionary') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="math-sheet-form-<?php echo MATH_ASSIGNMENT_SINGLE_DIGIT_DIV ?>" class="math-sheet-form">
            <div class="form-group">
                <input type="text" class="form-control" name="questions[question]" placeholder="<?php _e('Question', 'iii-dictionary') ?>" value="<?php echo $data['question'] ?>">
            </div>
            <table class="table table-striped table-condensed ik-table1 vertical-middle text-center">
                <thead>
                    <tr><th><?php _e('Comment', 'iii-dictionary') ?></th><th><?php _e('Number', 'iii-dictionary') ?></th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Answer', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[answer]" class="input-box-style2" value="<?php echo $data['answer'] ?>"></td>
                    </tr>
                    <tr>
                        <td><?php _e('Dividend', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[op1]" class="input-box-style2" value="<?php echo $data['op1'] ?>"></td>
                    </tr>
                    <tr>
                        <td><?php _e('Divisor', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[op2]" class="input-box-style2" value="<?php echo $data['op2'] ?>"></td>
                    </tr>
                    <tr>
                        <td><?php _e('Steps', 'iii-dictionary') ?></td>
                        <td><textarea name="questions[steps]" class="textarea-style2" rows="5"><?php echo $data['steps'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td><?php _e('Remainder', 'iii-dictionary') ?></td>
                        <td><input type="text" name="questions[remainder]" class="input-box-style2" value="<?php echo $data['remainder'] ?>"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="math-sheet-form-<?php echo MATH_ASSIGNMENT_FLASHCARD ?>" class="math-sheet-form">
            <div class="form-group">
                <input type="text" class="form-control" name="questions[question]" placeholder="<?php _e('Question', 'iii-dictionary') ?>" value="<?php echo $data['question'] ?>">
            </div>
            <table class="table table-striped table-condensed ik-table1 vertical-middle text-center">
                <thead>
                    <tr><th></th><th><?php _e('Op1', 'iii-dictionary') ?></th><th><?php _e('Op', 'iii-dictionary') ?></th><th><?php _e('Op2', 'iii-dictionary') ?></th><th><?php _e('Equal', 'iii-dictionary') ?></th><th><?php _e('Answer', 'iii-dictionary') ?></th><th><?php _e('Note', 'iii-dictionary') ?></th></tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                        <tr>
                            <td class="order-number"><?php echo $i ?>.</td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][op1]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['op1'] ?>"></td>
                            <td style="width: 99px">
                                <select class="select-box-it" name="questions[q][q<?php echo $i ?>][op]">
                                    <?php foreach ($flashcard_ops as $v => $op) : ?>
                                        <option value="<?php echo $v ?>"<?php echo $v == $data['q']['q' . $i]['op'] ? ' selected' : '' ?>><?php echo $op ?></option>
                                    <?php endforeach ?>
                                </select>
                            </td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][op2]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['op2'] ?>"></td>
                            <td>=</td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][answer]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['answer'] ?>"></td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][note]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['note'] ?>"></td>
                        </tr>
                    <?php endfor ?>
                </tbody>
            </table>
        </div>
        <div id="math-sheet-form-<?php echo MATH_ASSIGNMENT_FRACTION ?>" class="math-sheet-form">
            <div class="form-group">
                <input type="text" class="form-control" name="questions[question]" placeholder="<?php _e('Question', 'iii-dictionary') ?>" value="<?php echo $data['question'] ?>">
            </div>
            <table class="table table-striped table-condensed ik-table1 vertical-middle text-center">
                <thead>
                    <tr><th></th><th><?php _e('Op1', 'iii-dictionary') ?></th><th><?php _e('Op', 'iii-dictionary') ?></th><th><?php _e('Op2', 'iii-dictionary') ?></th><th><?php _e('Equal', 'iii-dictionary') ?></th><th><?php _e('Answer', 'iii-dictionary') ?></th></tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                        <tr>
                            <td class="order-number"><?php echo $i ?>.</td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][op1]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['op1'] ?>"></td>
                            <td style="width: 99px">
                                <select class="select-box-it" name="questions[q][q<?php echo $i ?>][op]">
                                    <?php foreach ($flashcard_ops as $v => $op) : ?>
                                        <option value="<?php echo $v ?>"<?php echo $v == $data['q']['q' . $i]['op'] ? ' selected' : '' ?>><?php echo $op ?></option>
                                    <?php endforeach ?>
                                </select>
                            </td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][op2]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['op2'] ?>"></td>
                            <td>=</td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][answer]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['answer'] ?>"></td>
                        </tr>
                    <?php endfor ?>
                </tbody>
            </table>
        </div>
        <div id="math-sheet-form-<?php echo MATH_ASSIGNMENT_WORD_PROB ?>" class="math-sheet-form">
            <div class="form-group">
                <input type="text" class="form-control" name="questions[question]" placeholder="<?php _e('Question', 'iii-dictionary') ?>" value="<?php echo $data['question'] ?>">
            </div>
            <table class="table table-striped table-condensed ik-table1 vertical-bottom text-center">
                <thead>
                    <tr><th></th><th style="width: 40%;">PNG</th><th style="width: 40%;">MP3</th><th style="width: 100px"><?php _e('Control Parameter', 'iii-dictionary') ?></th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Answer', 'iii-dictionary') ?></td><td colspan="3"><input type="text" name="questions[answer]" class="input-box-style2" value="<?php echo $data['answer'] ?>"></td>
                    </tr>
                    <tr>
                        <td><?php _e('Image Answer', 'iii-dictionary') ?></td><td colspan="3"><input type="text" name="questions[imganswer]" class="input-box-style2" value="<?php echo $data['imganswer'] ?>"></td>
                    </tr>
                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                        <tr>
                            <td><?php printf(__('Step %d.', 'iii-dictionary'), $i) ?></td>
                            <td>
                                <?php if ($data['q']['q' . $i]['image'] != ''): ?>
                                    <img style="width:200px" src="<?php echo MWHtml::math_image_url($data['q']['q' . $i]['image']) ?>">
                                <?php endif ?>
                                <input type="text" name="questions[q][q<?php echo $i ?>][image]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['image'] ?>">
                            </td>
                            <td>
                                <?php
                                if ($data['q']['q' . $i]['sound'] != ''):
                                    $type_audio = explode('.', $data['q']['q' . $i]['sound']);
                                    if ($type_audio[1] == 'mp3'):
                                        ?>
                                        <audio controls preload="auto" style="width: 100%;">
                                            <source src="<?php echo MWHtml::math_sound_url($data['q']['q' . $i]['sound']) ?>" type="audio/mpeg">
                                        </audio>
                                    <?php elseif ($type_audio[1] == 'mp4' || $type_audio[1] == 'ogg' || $type_audio[1] == 'm4v'):
                                        ?>
                                        <video width="320" height="350" controls>
                                            <source src="<?php echo MWHtml::math_video_url($data['q']['q' . $i]['sound']) ?>" type="video/mp4">
                                        </video>
                                        <?php
                                    endif;
                                endif
                                ?>
                                <input type="text" name="questions[q][q<?php echo $i ?>][sound]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['sound'] ?>">
                            </td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][param]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['param'] ?>"></td>
                        </tr>
                    <?php endfor ?>
                </tbody>
            </table>
        </div>

        <div id="math-sheet-form-<?php echo MATH_ASSIGNMENT_LIST ?>" class="math-sheet-form">
            <div class="form-group">
                <input type="text" class="form-control" name="questions[question]" placeholder="<?php _e('Question', 'iii-dictionary') ?>" value="<?php echo $data['question'] ?>">
            </div>
            <table class="table table-striped table-condensed ik-table1 vertical-bottom text-center">
                <thead>
                    <tr><th></th><th style="width: 25%;">Name</th><th style="width: 40%;">File</th><th style="width: 10%;">Description</th><th style="width: 100px"><?php _e('Control Parameter', 'iii-dictionary') ?></th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e('Answer', 'iii-dictionary') ?></td><td colspan="4"><input type="text" name="questions[answer]" class="input-box-style2" value="<?php echo $data['answer'] ?>"></td>
                    </tr>
                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                        <tr>
                            <td ><?php printf(__('Step %d.', 'iii-dictionary'), $i) ?></td>
                            <td>
                                <input type="text" name="questions[q][q<?php echo $i ?>][name]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['name'] ?>">
                            </td>
                            <td >
                                <?php ?>
                                <input type="text" name="questions[q][q<?php echo $i ?>][file]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['file'] ?>">
                            </td>
            <!--                                                        <td >
                                <input type="text" name="questions[q][q<?php echo $i ?>][description]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['description'] ?>">
                            </td>-->
                            <td>
                                <button type="button" class="btn-link btn-description" id="description-<?php echo $i ?>"><?php printf(__('Step %d', 'iii-dictionary'), $i) ?></button>
                                <input type="hidden" name="questions[q][q<?php echo $i ?>][description]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['description'] ?>">
                            </td>
                            <td ><input type="text" name="questions[q][q<?php echo $i ?>][param]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['param'] ?>"></td>
                        </tr>
                    <?php endfor ?>
                </tbody>
            </table>
        </div>

        <div id="math-sheet-form-<?php echo MATH_ASSIGNMENT_QUESTION_BOX ?>" class="math-sheet-form">
            <div class="form-group">
                <input type="text" class="form-control" name="questions[question]" placeholder="<?php _e('Question', 'iii-dictionary') ?>" value="<?php echo $data['question'] ?>">
            </div>
            <table class="table table-striped table-condensed ik-table1 vertical-middle text-center">
                <thead>
                    <tr><th></th><th><?php _e('X-cord', 'iii-dictionary') ?> <small class="text-muted">(%)</small></th>
                        <th><?php _e('Y-cord', 'iii-dictionary') ?> <small class="text-muted">(%)</small></th>
                        <th><?php _e('Width', 'iii-dictionary') ?> <small class="text-muted">(%)</small></th>
                        <th><?php _e('Height', 'iii-dictionary') ?> <small class="text-muted">(%)</small></th>
                        <th></th><th><?php _e('Value', 'iii-dictionary') ?></th><th><?php _e('Image', 'iii-dictionary') ?></th></tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                        <tr>
                            <td class="order-number"><?php echo $i ?>.</td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][x-cord]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['x-cord'] ?>"></td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][y-cord]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['y-cord'] ?>"></td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][width]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['width'] ?>"></td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][height]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['height'] ?>"></td>
                            <td>=</td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][answer]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['answer'] ?>"></td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][image]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['image'] ?>"></td>
                        </tr>
                    <?php endfor ?>
                </tbody>
            </table>
        </div>
        <div id="math-sheet-form-<?php echo MATH_ASSIGNMENT_EQUATION ?>" class="math-sheet-form">
            <div class="form-group">
                <input type="text" class="form-control" name="questions[question]" placeholder="<?php _e('Question', 'iii-dictionary') ?>" value="<?php echo $data['question'] ?>">
            </div>
            <table class="table table-striped table-condensed ik-table1 vertical-middle text-center">
                <thead>
                    <tr><th></th><th><?php _e('Equation', 'iii-dictionary') ?></th><th><?php _e('Answer', 'iii-dictionary') ?></th><th style="width: 100px"><?php _e('Note', 'iii-dictionary') ?></th></tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                        <tr>
                            <td class="order-number"><?php echo $i ?>.</td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][equation]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['equation'] ?>"></td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][answer]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['answer'] ?>"></td>
                            <td><input type="text" name="questions[q][q<?php echo $i ?>][note]" class="input-box-style2" value="<?php echo $data['q']['q' . $i]['note'] ?>"></td>
                        </tr>
                    <?php endfor ?>
                </tbody>
            </table>
        </div>

        <script>
            (function ($) {
                $(function () {
                    var _CMODE = '<?php echo $curr_mode ?>';
                    var _fid = $("#<?php echo $sel_assignments_id ?>").val();
                    _fid = _fid == <?php echo MATH_ASSIGNMENT_TWO_DIGIT_DIV ?> ? <?php echo MATH_ASSIGNMENT_SINGLE_DIGIT_DIV ?> : _fid;
                    $(".math-sheet-form").hide().find("input, select, textarea").prop("disabled", true);
                    $("#math-sheet-form-" + _fid).show().find("input, select, textarea").prop("disabled", false);
                    if (_fid == <?php echo MATH_ASSIGNMENT_FLASHCARD ?>)
                        $("#time-limit-block").removeClass("hidden");
                    $("#sign-sel-single").change(function () {
                        $(this).val() == "-" ? $("#_carry_lbl").text("<?php _e('Borrow', 'iii-dictionary') ?>") : $("#_carry_lbl").text("<?php _e('Carry', 'iii-dictionary') ?>");
                    });
                    $("#<?php echo $sel_assignments_id ?>").change(function () {
                        var _fid = $(this).val();
                        _fid = _fid == <?php echo MATH_ASSIGNMENT_TWO_DIGIT_DIV ?> ? <?php echo MATH_ASSIGNMENT_SINGLE_DIGIT_DIV ?> : _fid;
                        $(".math-sheet-form").hide().find("input, select, textarea").prop("disabled", true);
                        $("#math-sheet-form-" + _fid).show().find("input, select, textarea").prop("disabled", false);
                        _fid == <?php echo MATH_ASSIGNMENT_FLASHCARD ?> ? $("#time-limit-block").removeClass("hidden") : $("#time-limit-block").addClass("hidden");
                    });
                    $("#sign-sel-single").trigger("change");
                });
            })(jQuery);
        </script>
        <?php
    }

//view danh sách khóa học toán 
    public static function load_ikmaths_course() {
        $array_is_sub = array();
        for ($i = 38; $i <= 50; $i++) {
            if (is_sat_class_subscribed($i)) {
                array_push($array_is_sub, $i);
            }
        }
        ?>
        <div>
            <table class="table table-striped table-condensed ik-table1  vertical-middle scroll-fix-head" id="homeworkcritical">               
                <thead class="homeworkcritical">
                    <tr>
                        <th class="th-title-ikmath padd-left-15 css-th-wid-1"><?php _e('ikMath Course', 'iii-dictionary') ?></th>
                        <th class="th-title-ikmath1 css-th-wid-2">
                            <div id="div-all-select" class="css-select-ikmath1 " style="width: 100% !important">
                                <div id="div-select">
                                    <ul id="test-ikmath" class="image-select-ikmath">
                                        <?php if ($array_is_sub == null) { ?>
                                            <li class="init">Please Subscribe</li>
                                            <?php
                                        } else {
                                            ?>
                                            <?php
                                            $id = $array_is_sub[0];
                                            if ($id == 38) {
                                                ?>
                                                <li class="init">Math Kindergarten</li>
                                            <?php } else if ($id == 39) { ?>
                                                <li class="init">Math Grade 1</li>
                                            <?php } else if ($id == 40) { ?>
                                                <li class="init">Math Grade 2</li>
                                            <?php } else if ($id == 41) { ?>
                                                <li class="init">Math Grade 3</li>
                                            <?php } else if ($id == 42) { ?>
                                                <li class="init">Math Grade 4</li>
                                            <?php } else if ($id == 43) { ?>
                                                <li class="init">Math Grade 5</li>
                                            <?php } else if ($id == 44) { ?>
                                                <li class="init">Math Grade 6</li>
                                            <?php } else if ($id == 45) { ?>
                                                <li class="init">Math Grade 7</li>
                                            <?php } else if ($id == 46) { ?>
                                                <li class="init">Math Grade 8</li>
                                            <?php } else if ($id == 47) { ?>
                                                <li class="init">Math Grade 9</li>
                                            <?php } else if ($id == 48) { ?>
                                                <li class="init">Math Grade 10</li>
                                            <?php } else if ($id == 49) { ?>
                                                <li class="init">Math Grade 11</li>
                                            <?php } else if ($id == 50) { ?>
                                                <li class="init">Math Grade 12</li>
                                            <?php } ?>    

                                            <?php
                                            foreach ($array_is_sub as $id_sub => $value):
                                                ?>
                                                <?php if ($value == 38) { ?>
                                                    <li data-value="38" class="click-test-ikmath border-left-right">Math Kindergarten</li>
                                                <?php } else if ($value == 39) { ?>
                                                    <li data-value="39" class="click-test-ikmath border-left-right">Math Grade 1</li>
                                                <?php } else if ($value == 40) { ?>
                                                    <li data-value="40" class="click-test-ikmath border-left-right">Math Grade 2</li>
                                                <?php } else if ($value == 41) { ?>
                                                    <li data-value="41" class="click-test-ikmath border-left-right">Math Grade 3</li>
                                                <?php } else if ($value == 42) { ?>
                                                    <li data-value="42" class="click-test-ikmath border-left-right">Math Grade 4</li>
                                                <?php } else if ($value == 43) { ?>
                                                    <li data-value="43" class="click-test-ikmath border-left-right">Math Grade 5</li>
                                                <?php } else if ($value == 44) { ?>
                                                    <li data-value="44" class="click-test-ikmath border-left-right">Math Grade 6</li>
                                                <?php } else if ($value == 45) { ?>
                                                    <li data-value="45" class="click-test-ikmath border-left-right">Math Grade 7</li>
                                                <?php } else if ($value == 46) { ?>
                                                    <li data-value="46" class="click-test-ikmath border-left-right">Math Grade 8</li>
                                                <?php } else if ($value == 47) { ?>
                                                    <li data-value="47" class="click-test-ikmath border-left-right">Math Grade 9</li>
                                                <?php } else if ($value == 48) { ?>
                                                    <li data-value="48" class="click-test-ikmath border-left-right">Math Grade 10</li>
                                                <?php } else if ($value == 49) { ?>
                                                    <li data-value="49" class="click-test-ikmath border-left-right">Math Grade 11</li>
                                                <?php } else if ($value == 50) { ?>
                                                    <li data-value="50" class="click-test-ikmath border-left-right">Math Grade 12</li>
                                                    <?php
                                                }
                                            endforeach;
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <thead class="homeworkcritical">
                    <tr>
                        <th style="color: #f5f5f5;width: 58% !important;" class="text-color-custom-1 padd-left-15"><?php _e('Course Name', 'iii-dictionary') ?></th>
                        <th  class="text-color-custom-1" style="width: 18%"><?php _e('No. of Worksheets', 'iii-dictionary') ?></th>
                        <th  class="text-color-custom-1" style="width: 14% !important"><?php _e('Detail', 'iii-dictionary') ?></th>
                        <th  class="text-color-custom-1" style="width:14%;"><?php _e('', 'iii-dictionary') ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr><td colspan="6"><?php echo $pagination ?></td></tr>
                </tfoot>
                <tbody style="height: 380px !important;" id="load-class-math-follow-select">                      
                </tbody>
            </table>
        </div>
        <?php
    }

//view danh sách lịch dạy thêm toán 
    public static function load_tutoringplan() {
        ?>
        <ul class="nav nav-tabs" id="menu-ik">
            <li id="li-math4-private" class="active"><a data-toggle="tab" class="a-custom-color" href="#ikmath-sub"><?php _e('Tutoring Plan', 'iii-dictionary'); ?></a></li>
            <li id="li-math5-private"><a data-toggle="tab" class="a-custom-color" href="#ikmath-tutoring"><?php _e('Math Tutoring Record', 'iii-dictionary'); ?></a></li>
        </ul>
        <div id="tutoring-id-plan">
            <table class="table table-striped table-condensed ik-table1  vertical-middle scroll-fix-head" id="homeworkcritical">               
                <thead class="homeworkcritical" style="height:45px;">
                <th class="th-hidden" style="width: 98%;"></th>
                <th class="css-wid-th3">
                    <div id="div-all-select" class="mb-padd-option css-drop-tutor-plan">
                        <div id="div-select" style="width: 100%;float:right">
                            <ul id="test" class="image-select-tutoring">
                                <li class="init">Show All</li>
                                <li data-value="0" class="click-test border-left-right">Show All</li>
                                <li data-value="1" class="click-test border-left-right">Purchased & Waiting</li>
                                <li data-value="2" class="click-test border-left-right">Confirmed</li>
                                <li data-value="3" class="click-test border-left-right">Canceled</li>
                            </ul>
                        </div>
                    </div>
                </th>
                </thead>
                <thead class="homeworkcritical">
                    <tr>
                        <th  class="text-color-custom-1" style="width: 12%"></th>
                        <th  class="text-color-custom-1" style="padding-left: 8px;width: 31%;"><?php _e('Subject', 'iii-dictionary') ?></th>
                        <th  class="text-color-custom-1" style="width: 15% !important"><?php _e('Assigned Tutor', 'iii-dictionary') ?></th>
                        <th  class="text-color-custom-1" style="width: 12% !important"><?php _e('Date', 'iii-dictionary') ?></th>
                        <th  class="text-color-custom-1" style="width: 21%;"><?php _e('Time', 'iii-dictionary') ?></th>
                        <th  class="text-color-custom-1" style="width: 16%"><?php _e('Status', 'iii-dictionary') ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr><td colspan="6"><?php //echo $pagination ?></td></tr>
                </tfoot>
                <tbody style="height: 365px !important;" id="load-schedule-tutoring-select">                      
                </tbody>
            </table>
        </div>
        <div id="ikmath-tutoring" class="tab-pane hidden">
            <div class="homeworkcritical-online can-scroll" style="height:350px;" >
                <?php MWHtml::load_tutoring_history(); ?>
            </div>
        </div>
        <?php
    }

    public static function load_homework() {
        $is_math_panel = is_math_panel();
        if ($is_math_panel) :
            $_lang = get_short_lang_code();
            $is_math = is_math_panel();
            $current_page = max(1, get_query_var('page'));
            $filter['offset'] = 0;
            $filter['items_per_page'] = 14;
            $filter['group_type'] = GROUP_CLASS;
            $filter['lang'] = is_math_panel() ? get_short_lang_code() . '-math' : get_short_lang_code() . '-en';
            $filter['orderby'] = 'ordering';
            $filter['order-dir'] = 'ASC';
            $homeworks = MWDB::get_groups_home_math($filter);
        else:
            $_lang = get_short_lang_code();
            $is_math = is_math_panel();
            $current_page = max(1, get_query_var('page'));
            $filter['offset'] = 0;
            $filter['items_per_page'] = 19;
            $filter['group_type'] = GROUP_CLASS;
            $filter['lang'] = is_math_panel() ? get_short_lang_code() . '-math' : get_short_lang_code() . '-en';
            $filter['orderby'] = 'ordering';
            $filter['order-dir'] = 'ASC';
            $homeworks = MWDB::get_groups_home_english($filter);
        endif;
        ?>
        <div >
            <table class="table table-striped table-condensed ik-table1 vertical-middle scroll-fix-head1 table-critical" id="homeworkcritical">
                <thead class="homeworkcritical">
                    <tr>
                        <?php
                        $is_math_panel = is_math_panel();
                        if ($is_math_panel) :
                            ?> 
                            <th class="th-homework th-title-list-subject padd-left-15" colspan="5"><?php _e('Critical Math Subjects', 'iii-dictionary') ?></th>
                        <?php else: ?>
                            <th class="th-homework th-title-list-subject padd-left-15" colspan="5"><?php _e('Critical English Subjects', 'iii-dictionary') ?></th>
                        <?php endif;
                        ?>
                    </tr>
                </thead>
                <thead class="homeworkcritical">
                    <tr>
                        <th class="text-color-custom-1 padd-left-15"><?php _e('Course Name', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1"><?php _e('Due date', 'iii-dictionary') ?></th>
                        <th class=" text-color-custom-1"><?php _e('No. of W.S.', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1"><?php _e('Detail', 'iii-dictionary') ?></th>                        
                        <th class="text-color-custom-1"><?php _e('', 'iii-dictionary') ?></th>                        
                </thead>
                <tfoot>
                    <tr><td colspan="6"><?php // echo $pagination ?></td></tr>
                </tfoot>
                <tbody style="height: 424px">
                    <?php
                    $a = 0;
                    foreach ($homeworks->items as $hwg) :
                        $get_stg = MWDB::get_something_in_group($hwg->id); //$rp_url = $get_stg->step['prt'] ? $practice_url : $homework_url;
                        if (empty($get_stg->step_of_user) && $hwg->price != 0 || !is_user_logged_in()) {
                            ?>
                        <?php } else { 
                            $a++;
                            ?>
                            <tr>
                                <td><?php echo $hwg->name ?></td>
                                <td><?php echo is_null($hwg->deadline) ? 'No deadline' : ( $hwg->deadline == '0000-00-00' ? 'No deadline' : ik_date_format($hwg->deadline) ) ?></td>
                                <td><div ><?php
                                        $count = MWDB::get_count_worksheets_group($hwg->id);
                                        $count_complete = MWDB::get_count_worksheets_completeed_group($hwg->id);
                                        echo $count[0]->count;
                                        ?></div>
                                </td>
                                <td style="color: #5280AC;">
                                    <div>
                                        <a href="#" class="class-detail-btn class-detail-btn-default css-link not-join<?php
                                        if (is_null($exist)) {
                                            echo 'not-join';
                                        } else {
                                            echo '';
                                        }
                                        ?>" ><?php _e('Click', 'iii-dictionary') ?></a>
                                        <div class="hidden">
                                            <div style="width: 800px"></div>
                                            <?php
                                            $namehomework = MWDB::get_name_homework_group($hwg->id);
                                            foreach ($namehomework as $nhw):
                                                echo ' - ' . $nhw->namehw . "<br>";
                                            endforeach;
                                            ?>
                                        </div>
                                    </div>
                                </td>
                                <?php
                                if (is_math_panel()) {
                                    ?>
                                    <td><a href="<?php echo locale_home_url() . '/?r=online-learning&amp;math&amp;critical=1&amp;lvid=' . $hwg->id ?>"><strong style="text-decoration: underline;" class="<?php echo $td_class ?>" btn-view-sheet data-levels="<?php echo $hwg->grade_id ?>"><?php echo "OPEN"//$txt ?></strong></a></td>
                                <?php } else { ?>
                                    <td><a href="<?php echo locale_home_url() . '/?r=online-learning&amp;english&amp;critical=1&amp;lvid=' . $hwg->id ?>"><strong style="text-decoration: underline;" class="<?php echo $td_class ?>" btn-view-sheet data-levels="<?php echo $hwg->grade_id ?>"><?php echo "OPEN"//$txt ?></strong></a></td>
                                <?php } ?>
                                <td></td>
                            </tr>
                            <?php
                        }
                    endforeach;
                    
                    if (!is_user_logged_in()) {
                        ?>
                        <tr><td colspan="6" class="padd-left-15" style="width: 1% !important;">You haven’t joined any groups yet. Please select from <a href="<?php echo local_home_url() ?>/?r=critical-lesson-math" style="text-decoration: underline;">Critical Math Subjects </a> section. </td></tr>
                        <?php for ($i = 1; $i < 12; $i++) {
                            ?>
                        <tr ><td class="row-full-1" colspan="6" style="width: 1% !important;"></td></tr>
                        <?php } ?>
                        <?php
                    }else if($a < 10) {
                        for($a; $a < 10; $a++) {
                            ?>
                            <tr ><td class="row-full-1" colspan="6" style="height: 39px;width: 1% !important;"></td></tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function load_tutoring_history() {
        $history = MWDB::get_tutoring_history();
        ?>
        <div>
            <table class="table table-striped table-condensed ik-table1  vertical-middle scroll-fix-head8" id="homeworkcritical">
                <thead class="homeworkcritical">
                    <tr>
                        <th style="padding-left: 5%;" class="text-color-custom-1"><?php _e('Tutor\'s name', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1"><?php _e('Date/Time', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" ><?php _e('Duration(min)', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1"><?php _e('Charged(points)', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1"><?php _e('Session Name', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1"><?php _e('Evaluation', 'iii-dictionary') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr><td colspan="6"><?php echo $pagination ?></td></tr>
                </tfoot>
                <tbody style="height: 400px;">
                    <?php if (empty($history)) : ?>                        
                        <?php for ($i = 0; $i < 13; $i++) { ?>
                            <tr ><td class="row-full-1" colspan="6" ></td></tr>
                        <?php } ?>
                    <?php else : ?>
                        <?php foreach ($history as $hwg) : ?>
                            <tr>
                                <td class="row-first8" ><?php echo get_user_by('id', $hwg->teacher_id)->display_name ?></td>
                                <td><?php echo is_null($hwg->datetime) ? '' : ( $hwg->datetime == '0000-00-00 00:00:00' ? '' : ik_date_format($hwg->datetime) ) ?></td>
                                <td ><?php echo $hwg->price ?></td>
                                <td ><?php echo $hwg->price ?></td>
                                <td><?php echo $hwg->session_name ?></td>                        
                                <td><a href="<?php echo home_url() . '/?r=online-learning' ?>" data-id='<?php echo $hwg->id ?>' class="btn btn-default btn-block btn-tiny grey btn-a-link css-link tutor-link"><?php _e('EVALUATE', 'iii-dictionary') ?></a></td>
                            </tr>
                            <?php
                        endforeach;
                        if (count($history) < 13) {
                            for ($i = count($homeworks); $i < 13; $i++) {
                                ?>
                                <tr ><td style="height : 35px" colspan="6" ></td></tr>
                                <?php
                            }
                        }
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * 
     * @param type $user_groups
     * @param type $type
     */
    public static function load_homework_group($user_groups, $type) {
//        var_dump($user_groups);die;
        //$is_sat_class_subscribed = is_sat_class_subscribed($class_type_id);
        ?>
        <div style="width: 100% !important">
            <table class="table table-striped table-condensed ik-table1 vertical-middle scroll-fix-head" id="homeworkcritical">
                <?php if ($type) { ?>
                    <thead class="homeworkcritical">
                        <tr>
                            <th class="th-title-list-home padd-left-15" colspan="5"><?php _e('Assignment from Teacher', 'iii-dictionary') ?></th>
                        </tr>
                    </thead>
                    <thead class="homeworkcritical">
                        <tr>
                            <th style="color: #f5f5f5; width: 57% !important" class="text-color-custom-1 padd-left-15"><?php _e('Class Name', 'iii-dictionary') ?></th>
                            <th  class="text-color-custom-1" style="width: 11% !important;"><?php _e('Teacher', 'iii-dictionary') ?></th>
                            <th  class="text-color-custom-1" style="width: 19% !important"><?php _e('No. of Worksheet', 'iii-dictionary') ?></th>
                            <th class="text-color-custom-1 css-mobile-padding1" style="width: 20%"><?php _e('', 'iii-dictionary') ?></th>
                            <th></th>
                        </tr>
                    </thead>

                    <tfoot>
                        <tr><td colspan="6"><?php //echo $pagination ?></td></tr>
                    </tfoot>
                <?php } else { ?>
                    <thead class="homeworkcritical">
                        <tr>
                            <th style="padding-left: 4%;color: #f5f5f5; width: 57% !important" class="text-color-custom-1"><?php _e('Class Name', 'iii-dictionary') ?></th>
                            <th  class="text-color-custom-1" style="width: 11% !important;padding-left: 2%;"><?php _e('Type', 'iii-dictionary') ?></th>
                            <th  class="text-color-custom-1" style="width: 19% !important"><?php _e('No. of Worksheet', 'iii-dictionary') ?></th>
                            <th class="text-color-custom-1" style="width: 20% !important"><?php _e('', 'iii-dictionary') ?></th>
                            <th></th>
                        </tr>
                    </thead>

                    <tfoot>
                        <tr><td colspan="6"><?php //echo $pagination ?></td></tr>
                    </tfoot>
                <?php } ?>
                <tbody style="max-height: 421px !important;">

                    <?php if (empty($user_groups->items)) : ?>

                        <tr>
                            <td colspan="5" class="padd-left-15"><?php _e('You haven\'t joined any groups yet.', 'iii-dictionary') ?></td>
                        </tr>
                        <?php for ($i = 0; $i < 9; $i++) { ?>
                        <tr ><td class="row-full-1" colspan="5" style="height: 39px"></td></tr>
                        <?php } ?>
                    <?php else : ?>
                        <?php foreach ($user_groups->items as $item) : ?>
                            <tr>
                                <td class="row-first padd-left-15" style="width: 55% !important"><?php echo $item->group_name ?></td>
                                <td class="padding-left-4"><?php echo $item->teacher ?></td> <!--echo $item->group_type_id == GROUP_CLASS ? 'SAT Prep.' : $item->teacher-->
                                <td class="padding-left-4" style="width: 14% !important"><?php echo $item->no_of_homework ?></td>
                                <td style="width: 28% !important;text-decoration: underline;color: #0065bb">
                                    <?php
                                    $filter['offset'] = 0;
                                    $filter['items_per_page'] = 99999999;
                                    $filter['homework_result'] = true;
                                    $filter['user_id'] = get_current_user_id();
                                    $filter['is_active'] = 1;
                                    $homeworks = MWDB::get_group_homeworks($item->group_id, $filter, $filter['offset'], $filter['items_per_page']);
//                                var_dump($homeworks1);
                                    if (!empty($homeworks->items)) {
                                        $status = [];
                                        $status_cp = [];
                                        foreach ($homeworks->items as $hw) :
                                            if (($hw->is_view) != 0) {
                                                array_push($status, '1');
                                            }
                                            if (($hw->practice_id) != NULL) {
                                                array_push($status_cp, '1');
                                            }
                                        endforeach;
                                    }
                                    if ($status == "") {
                                        ?>
                                        <?php if(is_math_panel()){?>
                                            <a href="<?php echo locale_home_url() . '/?r=online-learning&amp;homeworkagm-math&amp;gid=' . $item->group_id ?>" class="btn btn-default btn-block btn-tiny grey btn-a-link css-link" data="" ><?php _e('OPEN', 'iii-dictionary') ?></a>
                                        <?php }else{?>
                                            <a href="<?php echo locale_home_url() . '/?r=online-learning&amp;homeworkagm-english&amp;gid=' . $item->group_id ?>" class="btn btn-default btn-block btn-tiny grey btn-a-link css-link" data="" ><?php _e('OPEN', 'iii-dictionary') ?></a>
                                        <?php }?>
                                    <?php } else if (count($status_cp) == count($homeworks->items)) { ?>
                                            <?php if(is_math_panel()){?>
                                                <a href="<?php echo locale_home_url() . '/?r=online-learning&amp;homeworkagm-math&amp;gid=' . $item->group_id ?>" class="btn btn-default btn-block btn-tiny grey btn-a-link css-link" data="" ><?php _e('OPEN', 'iii-dictionary') ?></a>
                                            <?php }else{?>
                                                <a href="<?php echo locale_home_url() . '/?r=online-learning&amp;homeworkagm-english&amp;gid=' . $item->group_id ?>" class="btn btn-default btn-block btn-tiny grey btn-a-link css-link" data="" ><?php _e('OPEN', 'iii-dictionary') ?></a>
                                            <?php }?>
                                    <?php } else { ?>
                                            <?php if(is_math_panel()){?>
                                                <a href="<?php echo locale_home_url() . '/?r=online-learning&amp;homeworkagm-math&amp;gid=' . $item->group_id ?>" class="btn btn-default btn-block btn-tiny grey btn-a-link css-link" data="" ><?php _e('OPEN ', 'iii-dictionary') ?></a>
                                            <?php }else{?>
                                                <a href="<?php echo locale_home_url() . '/?r=online-learning&amp;homeworkagm-english&amp;gid=' . $item->group_id ?>" class="btn btn-default btn-block btn-tiny grey btn-a-link css-link" data="" ><?php _e('OPEN ', 'iii-dictionary') ?></a>
                                            <?php }?>
                                    <?php } ?>
                                </td>
                                <td></td>
                            </tr>
                            <?php
                        endforeach;
                        if (count($user_groups->items) < 11) {
                            ?>
                            <?php for ($i = count($user_groups->items); $i < 11; $i++) {
                                ?>
                                <tr ><td style="height : 39px;width: 1%" colspan="6" ></td></tr>
                                <?php
                            }
                        }
                    endif
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * 
     * @param type $user_subscriptions
     * @return type html list subscription status
     */
    public static function load_subscription_status($user_subscriptions) {
        $cart_items = get_cart_items();
        $is_math_panel = is_math_panel();
        ?>
        <div  class="ipad-portrait">
            <table class="table table-condensed ik-table1 ik-table-break-all text-center table-custom-color text-table-black scroll-fix-head6" id="user-subscriptions">
                <thead>
                    <tr>
                        <?php if (!is_math_panel()) { ?>
                            <th class="text-color-custom-1 css-auto-left padd-left-15" style="width: 29% !important;"><?php _e('Lesson Name', 'iii-dictionary') ?></th>
                        <?php } else { ?>
                            <th class="text-color-custom-1 css-auto-left padd-left-15 css-wid-subcrip"><?php _e('Lesson Name', 'iii-dictionary') ?></th>
                        <?php } ?>
                        <th class="text-color-custom-1 css-auto-left padd-left-15" style="width: 9% !important;"><?php _e('Size of class', 'iii-dictionary') ?></th>
                        <?php if ($is_math_panel) : ?>
                            <th class="text-color-custom-1 css-auto-left padd-left-15" style="width: 11% !important"><?php _e('No. of License', 'iii-dictionary') ?></th>
                        <?php else: ?>
                            <th class="text-color-custom-1 css-auto-left padd-left-15" style="width: 8% !important"><?php _e('No. of License', 'iii-dictionary') ?></th>
                        <?php endif; ?>
                        <?php if ($is_math_panel) : ?>
                            <th class="text-color-custom-1 css-auto-left css-mob-en padd-left-15"><?php _e('Sub. End', 'iii-dictionary') ?></th>
                        <?php else: ?>
                            <th class="text-color-custom-1 css-auto-left padd-left-15" style="padding-left: 9px;"><?php _e('Sub. End', 'iii-dictionary') ?></th>
                        <?php endif; ?>
                        <?php if (!$is_math_panel) { ?>
                            <th class="text-color-custom-1 css-auto-left css-mob-dic padd-left-15" style="width: 10%"><?php _e('Dictionary', 'iii-dictionary') ?></th>
                        <?php } ?>
                        <?php if (!$is_math_panel) { ?>
                            <th class="text-color-custom-1" style="width: 18% !important;"><?php _e('Group', 'iii-dictionary') ?></th>
                        <?php } else { ?>
                            <th class="text-color-custom-1" style="width:21% !important"><?php _e('Group', 'iii-dictionary') ?></th>
                        <?php } ?>

                        <th colspan="2"><?php _e('', 'iii-dictionary') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr><td colspan="7"><?php //echo $pagination ?></td></tr>
                </tfoot>
                <tbody>
                    <?php
                    if (!is_user_logged_in()) :
                        for ($i = 0; $i < 13; $i++) {
                            ?>
                            <tr >
                                <td style="height : 35px;width: 1% !important" colspan="9"></td>
                            </tr>
                            <?php
                        }
                    else :
                        ?>
                        <?php 
//                        var_dump($user_subscriptions);
                        if (empty($user_subscriptions->items)) : ?>
                            <tr><td colspan="7" style="padding-left: 5%;width: 1% !important"><?php _e('You haven\'t subscribed yet.', 'iii-dictionary') ?></td></tr>
                            <?php for ($i = 0; $i < 13; $i++) { ?>
                                <tr ><td style="height : 35px;width: 1% !important" colspan="7"></td></tr>
                            <?php } ?>
                        <?php else : ?>
                            <?php
                            foreach ($user_subscriptions->items as $code) :
                                $style1 = 'style="color:#7F7D7E;width: 20%!important"';
                                if (strtotime($code->expired_on) < strtotime(date('Y-m-d'))){
                                    $style = 'style="color:#7F7D7E;text-align: left;padding-left: 15px;width: 25%"';
                                }
                                else{
                                    $style = 'style="text-align: left;padding-left: 15px;width: 25%"';
                                }
                                ?>
                                <tr>
                                    <td <?php echo $style; ?>>
                                        <?php
                                        if (!is_null($code->sat_class)) {
                                            if($code->sat_class == "Vocabulary and Grammar") {
                                                echo "SAT Prep - Grammar Review";
                                            }else if($code->sat_class == "Writing Practice") {
                                                echo "SAT Prep - Writing Practice";
                                            }else if($code->sat_class == "SAT Test 1"){
                                                echo "English SAT Test 1";
                                            }else if($code->sat_class == "SAT Test 2"){
                                                echo "English SAT Test 2";
                                            }else if($code->sat_class == "SAT Test 3"){
                                                echo "English SAT Test 3";
                                            }else if($code->sat_class == "SAT Test 4"){
                                                echo "English SAT Test 4";
                                            }else if($code->sat_class == "SAT Test 5"){
                                                echo "English SAT Test 5";
                                            }else if($code->type =="ikMath Class - Kindergarten"){
                                                if($code->sat_class =="E Math K") {
                                                    echo "ikMath Class - E Math K";
                                                }else if($code->sat_class =="E Math 1") {
                                                    echo "ikMath Class - E Math 1";
                                                }else if($code->sat_class =="E Math 2") {
                                                    echo "ikMath Class - E Math 2";
                                                }else if($code->sat_class =="E Math 3") {
                                                    echo "ikMath Class - E Math 3";
                                                }else if($code->sat_class =="E Math 4") {
                                                    echo "ikMath Class - E Math 4";
                                                }else if($code->sat_class =="E Math 5") {
                                                    echo "ikMath Class - E Math 5";
                                                }else if($code->sat_class =="E Math 6") {
                                                    echo "ikMath Class - E Math 6";
                                                }else if($code->sat_class =="E Math 7") {
                                                    echo "ikMath Class - E Math 7";
                                                }else if($code->sat_class =="E Math 8") {
                                                    echo "ikMath Class - E Math 8";
                                                }else if($code->sat_class =="E Math 9") {
                                                    echo "ikMath Class - E Math 9";
                                                }else if($code->sat_class =="E Math 10") {
                                                    echo "ikMath Class - E Math 10";
                                                }else if($code->sat_class =="E Math 11") {
                                                    echo "ikMath Class - E Math 11";
                                                }else if($code->sat_class =="E Math 12") {
                                                    echo "ikMath Class - E Math 12";
                                                }
                                            }else if($code->type =="Math SAT I" || $code->type=="Math SAT II"){
                                                echo $code->sat_class;
                                            }
                                        } else {
                                            ?>
                                            <?php if (!$code->inherit) { ?>
                                                <?php if($code->type =='Dictionary'){ ?>
                                                    <?php if($code->dictionary=="E Learner's") { ?>
                                                            <?php echo "E Learner’s Dictionary"; ?><?php echo in_array($code->typeid, array(SUB_SAT_PREPARATION, SUB_MATH_SAT_I_PREP, SUB_MATH_SAT_II_PREP)) ? ' - ' . $code->sat_class : '' ?>
                                                    <?php }else if($code->dictionary=="Collegiate") { ?>
                                                            <?php echo "Collegiate Dictionary"; ?><?php echo in_array($code->typeid, array(SUB_SAT_PREPARATION, SUB_MATH_SAT_I_PREP, SUB_MATH_SAT_II_PREP)) ? ' - ' . $code->sat_class : '' ?>
                                                    <?php }else if($code->dictionary=="Medical") { ?>
                                                            <?php echo "Medical Dictionary"; ?><?php echo in_array($code->typeid, array(SUB_SAT_PREPARATION, SUB_MATH_SAT_I_PREP, SUB_MATH_SAT_II_PREP)) ? ' - ' . $code->sat_class : '' ?>
                                                    <?php }else if ($code->dictionary=="Intermediate") { ?>
                                                            <?php echo "Intermediate Dictionary"; ?><?php echo in_array($code->typeid, array(SUB_SAT_PREPARATION, SUB_MATH_SAT_I_PREP, SUB_MATH_SAT_II_PREP)) ? ' - ' . $code->sat_class : '' ?>
                                                    <?php }else if($code->dictionary=="Elementary") {?>
                                                            <?php echo "Elementary Dictionary"; ?><?php echo in_array($code->typeid, array(SUB_SAT_PREPARATION, SUB_MATH_SAT_I_PREP, SUB_MATH_SAT_II_PREP)) ? ' - ' . $code->sat_class : '' ?>
                                                    <?php }?>
                                                <?php }else{ ?>
                                                        <?php echo $code->type ?><?php echo in_array($code->typeid, array(SUB_SAT_PREPARATION, SUB_MATH_SAT_I_PREP, SUB_MATH_SAT_II_PREP)) ? ' - ' . $code->sat_class : '' ?>
                                                <?php } ?>
                                            <?php } else if (!$code->dictionary) { ?>
                                                <?php echo "Dictionary" . $code->dictionary; ?>
                                            <?php } else { ?>
                                                <?php echo $code->type; ?>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td ><?php echo in_array($code->typeid, array(SUB_TEACHER_TOOL, SUB_TEACHER_TOOL_MATH, SUB_GROUP)) ? $code->number_of_students : 'N/A' ?></td>
                                    <td ><?php echo in_array($code->typeid, array(SUB_DICTIONARY, SUB_SELF_STUDY, SUB_SELF_STUDY_MATH, SUB_GROUP)) ? $code->number_of_students : 'N/A' ?></td>
                                    <?php if ($code->total_date >= 0) { ?>
                                        <?php
                                        $d = $code->total_date;
                                        $date_str = "+" . $d . " days";
                                        $date = date('Y-m-d', strtotime($date_str));
                                        ?>
                                        <td ><?php echo $date; ?></td>
                                    <?php } else { ?>
                                        <td <?php echo "style='color:#cd0000 !important'"; ?> ><?php echo $code->expired_on ?></td>
                                    <?php } ?>
                                    <?php if (!$is_math_panel) { ?>
                                        <?php if ($code->dictionary == "") { ?>
                                            <td style="color: transparent !important;"><?php echo "aaaaaaaaaaaa" ?></td>
                                        <?php } else { ?>
                                            <td ><?php echo $code->dictionary ?></td>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if ($is_math_panel) : ?>
                                        <td <?php echo $style1; ?> ><?php echo is_null($code->group_name) ? 'N/A' : $code->group_name ?></td>
                                    <?php else: ?>
                                        <td ><?php echo is_null($code->group_name) ? 'N/A' : $code->group_name ?></td>
                                    <?php endif; ?>
                                    <?php
                                    $date1 = new DateTime();
                                    $date2 = new DateTime($code->expired_on);
                                    $interval = $date1->diff($date2);
                                    $months_left = $interval->d > 0 ? $interval->m + 1 : $interval->m;
                                    $checked_out_state = '';
                                    foreach ($cart_items as $item) {
                                        if ($item->sub_id == $code->id) {
                                            $checked_out_state = ' disabled';
                                        }
                                    }
                                    ?>

                                    <td style="width: 10% !important;">
                                        <div class="width-btn-sub" data-subid="<?php echo $code->id ?>" data-type="<?php echo $code->typeid ?>" data-did="<?php echo $code->dictionary_id ?>" data-size="<?php echo $code->number_of_students ?>" data-months="<?php echo $months_left ?>"<?php echo!is_null($code->group_name) ? ' data-group="' . $code->group_name . '"' : '' ?> data-sat-class="<?php echo $code->sat_class ?>" data-sat-class-id="<?php echo $code->sat_class_id ?>"  data-gid="<?php echo $code->group_id ?>">
                                            <button id-detail="<?php echo $code->id ?>" data-type="detail" class="text-bold-size-16 text-left-0 btn btn-default btn-block btn-tiny grey btn-a-link btn-view-detail css-link"><?php _e('DETAIL', 'iii-dictionary') ?></button>
                                        </div>
                                    </td>
                                    <?php if(count($user_subscriptions->items)) {?>
                                        <td style="width: 1% !important;"></td>
                                    <?php } ?>
                                </tr>
                            <?php endforeach; ?>
                            <?php
                            if (count($user_subscriptions->items) < 13) {
                                for ($i = count($user_subscriptions->items); $i < 13; $i++) {
                                    ?>
                                    <tr >
                                        <?php if (is_math_panel()) { ?>
                                            <?php if (count($user_subscriptions->items) == 1) { ?>
                                                <td style="height : 35px;width: 1%" colspan="8"></td>
                                                <td style="height : 35px;width: 1%" ></td>
                                            <?php } else { ?>
                                                <td style="height : 35px;width: 1%" colspan="8"></td>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <?php if (count($user_subscriptions->items) == 1) { ?>
                                                <td style="height : 35px;width: 1%" colspan="8"></td>
                                                <td style="height : 35px;width: 1%" ></td>
                                            <?php } else { ?>
                                                <td style="height : 35px;width: 1%" colspan="8"></td>
                                            <?php } ?>  
                                        <?php } ?>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        <?php endif; ?>
                    <?php endif; ?>               
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function load_purchase_history($purchased_history) {
        ?>
        <div >
            <table class="table table-custom-color table-condensed ik-table1 text-center table-custom-color text-table-black scroll-fix-head7">
                <thead>
                    <tr>
                        <th class="text-color-custom-1 css-auto-left padd-left-15"><?php _e('Lesson Name', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1"><?php _e('Activation Code', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1"><?php _e('Payment Method', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1"><?php _e('Paid Amount', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1"><?php _e('Purchased On', 'iii-dictionary') ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php 
//                        var_dump($purchased_history);
                    if (empty($purchased_history)) : ?>
                        <tr><td class="padd-left-15" style="width: 1% !important;"><?php _e('No history', 'iii-dictionary') ?></td></tr>
                        <?php for ($i = 0; $i < 13; $i++) { ?>
                        <tr style="display: block;"><td style="height : 35px;width: 1%"></td></tr>
                        <?php } ?>
                        <?php
                    else :
                        foreach ($purchased_history as $item) :
                            ?>
                            <tr>
                                <?php 
                                if(($item->payment_method) == null) { ?>
                                    <td class="css-auto-left padd-left-15"><?php echo "Admin Code"; ?></td>
                                <?php }else if(!empty($item->encoded_code)){ ?>
                                    <td class="css-auto-left padd-left-15"><?php echo "Point"; ?></td>
                                <?php }
                                if (($item->sub_type_id) == 12) { ?>
                                    <td class="css-auto-left padd-left-15"><?php echo "ikMath Class - " . $item->ik_name ?></td>
                                <?php } else if ($item->sub_type_id == 2){ ?>
                                        <?php if($item->dictionary_id ==1) { ?>
                                            <td class="css-auto-left padd-left-15"><?php echo "E Learner’s Dictionary"; ?></td>
                                        <?php }else if($item->dictionary_id ==2){ ?>
                                            <td class="css-auto-left padd-left-15"><?php echo "Collegiate Dictionary"; ?></td>
                                        <?php }else if($item->dictionary_id ==3){ ?>
                                            <td class="css-auto-left padd-left-15"><?php echo "Medical Dictionary"; ?></td>
                                        <?php }else if($item->dictionary_id ==4){ ?>
                                            <td class="css-auto-left padd-left-15"><?php echo "Intermediate Dictionary"; ?></td>
                                        <?php }else if($item->dictionary_id ==5){ ?>
                                            <td class="css-auto-left padd-left-15"><?php echo "Elementary Dictionary"; ?></td>
                                        <?php } ?>
                                <?php } else if ($item->sub_type_id == 3){ ?>
                                        <?php if($item->sat_class_id ==1){ ?>
                                            <td class="css-auto-left padd-left-15"><?php echo 'SAT Prep - Grammar Review'; ?></td>
                                        <?php }else if($item->sat_class_id ==2){ ?>
                                            <td class="css-auto-left padd-left-15"><?php echo 'SAT Prep - Writing Practice'; ?></td>
                                        <?php }else if($item->sat_class_id ==3){ ?>
                                            <td class="css-auto-left padd-left-15"><?php echo 'English SAT Test 1'; ?></td>
                                        <?php }else if($item->sat_class_id ==4){ ?>
                                            <td class="css-auto-left padd-left-15"><?php echo 'English SAT Test 2'; ?></td>
                                        <?php }else if($item->sat_class_id ==5){ ?>
                                            <td class="css-auto-left padd-left-15"><?php echo 'English SAT Test 3'; ?></td>
                                        <?php }else if($item->sat_class_id ==6){ ?>
                                            <td class="css-auto-left padd-left-15"><?php echo 'English SAT Test 4'; ?></td>
                                        <?php }else if($item->sat_class_id ==7){ ?>
                                            <td class="css-auto-left padd-left-15"><?php echo 'English SAT Test 5'; ?></td>
                                        <?php } ?>
                                <?php } else if($item->purchased_item_name=="Math SAT I" || $item->purchased_item_name =="Math SAT II"){ ?>
                                        <td class="css-auto-left padd-left-15"><?php echo $item->ik_name; ?></td>
                                <?php } else { ?>
                                        <td class="css-auto-left padd-left-15"><?php echo $item->purchased_item_name; ?></td>
                                <?php } ?>
                                <td><?php echo!empty($item->encoded_code) ? $item->encoded_code : 'NULL'; ?></td>
                                <td><?php
                                    if (($item->payment_method) == null) {
                                        echo "Admin Code";
                                    } else {
                                        echo $item->payment_method;
                                    }
                                    ?></td>
                                <td>$ <?php echo $item->amount ?></td>
                                <td><?php echo ik_date_format($item->purchased_on, 'm/d/Y H:m:i') ?></td>
                                <td style="height : 35px;width: 1%;"></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php
                        if (count($purchased_history)) {
                            for ($i = count($purchased_history); $i < 14; $i++) {
                                ?>
                                <tr >
                                    <td style="height : 35px" colspan="5"></td>
                                    <td style="height : 35px;width: 1%;"></td>
                                </tr>
                                <?php
                            }
                        }
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function load_homework_math_sat() {
        ?>
        <ul class="nav nav-tabs" id="menu-sat-i">
            <li class="active"><a data-toggle="tab" class="a-custom-color" href="#home-sat-i"><?php _e('Preparation', 'iii-dictionary') ?></a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu-sat-i-1"><?php _e('SAT 1A', 'iii-dictionary') ?></a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu-sat-i-2"><?php _e('SAT 1B', 'iii-dictionary') ?></a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu-sat-i-3"><?php _e('SAT 1C', 'iii-dictionary') ?></a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu-sat-i-4"><?php _e('SAT 1D', 'iii-dictionary') ?></a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu-sat-i-5"><?php _e('SAT 1E', 'iii-dictionary') ?></a></li>
        </ul>
        <div class="tab-content can-scroll"><div id="home-sat-i" class="tab-pane fade in active">
                <?php
                $current_page = max(1, get_query_var('page'));
                $filter = get_page_filter_session();

                $filter['orderby'] = 'ordering';
                $filter['items_per_page'] = 25;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                $filter['group_type'] = GROUP_CLASS;
                $filter['class_type'] = 9;


                set_page_filter_session($filter);
                $filter['offset'] = 0;
                $filter['items_per_page'] = 99999999;
                $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                MWHtml::load_form($groups, $filter['class_type']);
//                var_dump($groups);die;
                ?>
            </div>
            <div id="menu-sat-i-1" class="tab-pane fade">
                <?php
                $current_page = max(1, get_query_var('page'));
                $filter = get_page_filter_session();

                $filter['orderby'] = 'ordering';
                $filter['items_per_page'] = 25;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                $filter['group_type'] = GROUP_CLASS;
                $filter['class_type'] = 10;


                set_page_filter_session($filter);
                $filter['offset'] = 0;
                $filter['items_per_page'] = 99999999;
                $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                MWHtml::load_form($groups, $filter['class_type']);
                ?>
            </div>
            <div id="menu-sat-i-2" class="tab-pane fade">
                <?php
                $current_page = max(1, get_query_var('page'));
                $filter = get_page_filter_session();

                $filter['orderby'] = 'ordering';
                $filter['items_per_page'] = 25;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                $filter['group_type'] = GROUP_CLASS;
                $filter['class_type'] = 11;


                set_page_filter_session($filter);
                $filter['offset'] = 0;
                $filter['items_per_page'] = 99999999;
                $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                MWHtml::load_form($groups, $filter['class_type']);
                ?>

            </div>
            <div id="menu-sat-i-3" class="tab-pane fade">
                <?php
                $current_page = max(1, get_query_var('page'));
                $filter = get_page_filter_session();

                $filter['orderby'] = 'ordering';
                $filter['items_per_page'] = 25;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                $filter['group_type'] = GROUP_CLASS;
                $filter['class_type'] = 12;


                set_page_filter_session($filter);
                $filter['offset'] = 0;
                $filter['items_per_page'] = 99999999;
                $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                MWHtml::load_form($groups, $filter['class_type']);
                ?>

            </div>
            <div id="menu-sat-i-4" class="tab-pane fade">
                <?php
                $current_page = max(1, get_query_var('page'));

                $filter['orderby'] = 'ordering';
                $filter['items_per_page'] = 25;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                $filter['group_type'] = GROUP_CLASS;
                $filter['class_type'] = 13;


                set_page_filter_session($filter);
                $filter['offset'] = 0;
                $filter['items_per_page'] = 99999999;
                $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                MWHtml::load_form($groups, $filter['class_type']);
                ?>

            </div>
            <div id="menu-sat-i-5" class="tab-pane fade">
                <?php
                $current_page = max(1, get_query_var('page'));
                $filter = get_page_filter_session();

                $filter['orderby'] = 'ordering';
                $filter['items_per_page'] = 25;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                $filter['group_type'] = GROUP_CLASS;
                $filter['class_type'] = 14;


                set_page_filter_session($filter);
                $filter['offset'] = 0;
                $filter['items_per_page'] = 99999999;
                $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                MWHtml::load_form($groups, $filter['class_type']);
                ?>

            </div>
        </div>

        <?php
    }

    public static function load_homework_math_sat_ii() {
        ?>
        <ul class="nav nav-tabs" id="menu-sat-ii">
            <li class="active"><a data-toggle="tab" class="a-custom-color" href="#home-sat-ii"><?php _e('Preparation', 'iii-dictionary') ?></a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu-sat-ii-1"><?php _e('SAT 2A', 'iii-dictionary') ?></a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu-sat-ii-2"><?php _e('SAT 2B', 'iii-dictionary') ?></a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu-sat-ii-3"><?php _e('SAT 2C', 'iii-dictionary') ?></a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu-sat-ii-4"><?php _e('SAT 2D', 'iii-dictionary') ?></a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu-sat-ii-5"><?php _e('SAT 2E', 'iii-dictionary') ?></a></li>
        </ul>
        <div class="tab-content can-scroll"><div id="home-sat-ii" class="tab-pane fade in active">
                <?php
                $current_page = max(1, get_query_var('page'));
                $filter = get_page_filter_session();

                $filter['orderby'] = 'ordering';
                $filter['items_per_page'] = 25;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                $filter['group_type'] = GROUP_CLASS;
                $filter['class_type'] = 15;


                set_page_filter_session($filter);
                $filter['offset'] = 0;
                $filter['items_per_page'] = 99999999;
                $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                MWHtml::load_form($groups, $filter['class_type']);
                ?>
            </div>
            <div id="menu-sat-ii-1" class="tab-pane fade">
                <?php
                $current_page = max(1, get_query_var('page'));
                $filter = get_page_filter_session();

                $filter['orderby'] = 'ordering';
                $filter['items_per_page'] = 25;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                $filter['group_type'] = GROUP_CLASS;
                $filter['class_type'] = 16;


                set_page_filter_session($filter);
                $filter['offset'] = 0;
                $filter['items_per_page'] = 99999999;
                $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                MWHtml::load_form($groups, $filter['class_type']);
                ?>
            </div>
            <div id="menu-sat-ii-2" class="tab-pane fade">
                <?php
                $current_page = max(1, get_query_var('page'));
                $filter = get_page_filter_session();

                $filter['orderby'] = 'ordering';
                $filter['items_per_page'] = 25;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                $filter['group_type'] = GROUP_CLASS;
                $filter['class_type'] = 17;
                set_page_filter_session($filter);
                $filter['offset'] = 0;
                $filter['items_per_page'] = 99999999;
                $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                MWHtml::load_form($groups, $filter['class_type']);
                ?>

            </div>
            <div id="menu-sat-ii-3" class="tab-pane fade">
                <?php
                $current_page = max(1, get_query_var('page'));
                $filter = get_page_filter_session();

                $filter['orderby'] = 'ordering';
                $filter['items_per_page'] = 25;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                $filter['group_type'] = GROUP_CLASS;
                $filter['class_type'] = 18;


                set_page_filter_session($filter);
                $filter['offset'] = 0;
                $filter['items_per_page'] = 99999999;
                $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                MWHtml::load_form($groups, $filter['class_type']);
                ?>

            </div>
            <div id="menu-sat-ii-4" class="tab-pane fade">
                <?php
                $current_page = max(1, get_query_var('page'));
                $filter = get_page_filter_session();

                $filter['orderby'] = 'ordering';
                $filter['items_per_page'] = 25;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                $filter['group_type'] = GROUP_CLASS;
                $filter['class_type'] = 19;


                set_page_filter_session($filter);
                $filter['offset'] = 0;
                $filter['items_per_page'] = 99999999;
                $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                MWHtml::load_form($groups, $filter['class_type']);
                ?>

            </div>
            <div id="menu-sat-ii-5" class="tab-pane fade">
                <?php
                $current_page = max(1, get_query_var('page'));
                $filter = get_page_filter_session();

                $filter['orderby'] = 'ordering';
                $filter['items_per_page'] = 25;
                $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                $filter['group_type'] = GROUP_CLASS;
                $filter['class_type'] = 20;


                set_page_filter_session($filter);
                $filter['offset'] = 0;
                $filter['items_per_page'] = 99999999;
                $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                MWHtml::load_form($groups, $filter['class_type']);
                ?>

            </div>
        </div>

        <?php
    }

    public static function load_homework_sat() {
        ?>
        <ul class="nav nav-tabs" id="menu-sat">
            <li class="active"><a data-toggle="tab" class="a-custom-color" href="#home" id='vocabulary'>Vocabulary / Grammar</a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu1" id='writing'>Writing Skills</a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu2" id='sat-1'>SAT Test 1</a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu3" id='sat-2'>SAT Test 2</a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu4" id='sat-3'>SAT Test 3</a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu5" id='sat-4'>SAT Test 4</a></li>
            <li><a data-toggle="tab" class="a-custom-color" href="#menu6" id='sat-5'>SAT Test 5</a></li>
        </ul>
        <div class="tab-content can-scroll">

            <div id="home" class="tab-pane fade in active">
                <?php
                if (is_sat_class_subscribed(1)) {
                    $current_page = max(1, get_query_var('page'));
                    $filter = get_page_filter_session();
                    if (empty($filter)) {
                        $filter['orderby'] = 'ordering';
                        $filter['items_per_page'] = 25;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                        $filter['group_type'] = GROUP_CLASS;
                        $filter['class_type'] = 1;
                    } else {
                        $filter['class_type'] = 1;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                    }

                    set_page_filter_session($filter);
                    $filter['offset'] = 0;
                    $filter['items_per_page'] = 99999999;
                    $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                    MWHtml::load_form($groups, $filter['class_type']);
                } else {
                    MWHtml::load_form(null, 1);
                }
                ?>
            </div>
            <div id="menu1" class="tab-pane fade">
                <?php
                if (is_sat_class_subscribed(2)) {

                    $current_page = max(1, get_query_var('page'));
                    $filter = get_page_filter_session();
                    if (empty($filter)) {
                        $filter['orderby'] = 'ordering';
                        $filter['items_per_page'] = 25;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                        $filter['group_type'] = GROUP_CLASS;
                        $filter['class_type'] = 2;
                    } else {
                        $filter['class_type'] = 2;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                    }

                    set_page_filter_session($filter);
                    $filter['offset'] = 0;
                    $filter['items_per_page'] = 99999999;
                    $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                    MWHtml::load_form($groups, $filter['class_type']);
                } else {
                    MWHtml::load_form(null, 2);
                }
                ?>
            </div>
            <div id="menu2" class="tab-pane fade">
                <?php
                if (is_sat_class_subscribed(3)) {

                    $current_page = max(1, get_query_var('page'));
                    $filter = get_page_filter_session();
                    if (empty($filter)) {
                        $filter['orderby'] = 'ordering';
                        $filter['items_per_page'] = 25;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                        $filter['group_type'] = GROUP_CLASS;
                        $filter['class_type'] = 3;
                    } else {
                        $filter['class_type'] = 3;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                    }

                    set_page_filter_session($filter);
                    $filter['offset'] = 0;
                    $filter['items_per_page'] = 99999999;
                    $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                    MWHtml::load_form($groups, $filter['class_type']);
                } else {
                    MWHtml::load_form(null, 3);
                }
                ?>

            </div>
            <div id="menu3" class="tab-pane fade">
                <?php
                if (is_sat_class_subscribed(4)) {

                    $current_page = max(1, get_query_var('page'));
                    $filter = get_page_filter_session();
                    if (empty($filter)) {
                        $filter['orderby'] = 'ordering';
                        $filter['items_per_page'] = 25;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                        $filter['group_type'] = GROUP_CLASS;
                        $filter['class_type'] = 4;
                    } else {
                        $filter['class_type'] = 4;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                    }

                    set_page_filter_session($filter);
                    $filter['offset'] = 0;
                    $filter['items_per_page'] = 99999999;
                    $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                    MWHtml::load_form($groups, $filter['class_type']);
                } else {
                    MWHtml::load_form(null, 4);
                }
                ?>

            </div>
            <div id="menu4" class="tab-pane fade">
                <?php
                if (is_sat_class_subscribed(5)) {

                    $current_page = max(1, get_query_var('page'));
                    $filter = get_page_filter_session();
                    if (empty($filter)) {
                        $filter['orderby'] = 'ordering';
                        $filter['items_per_page'] = 25;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                        $filter['group_type'] = GROUP_CLASS;
                        $filter['class_type'] = 5;
                    } else {
                        $filter['class_type'] = 5;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                    }

                    set_page_filter_session($filter);
                    $filter['offset'] = 0;
                    $filter['items_per_page'] = 99999999;
                    $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                    MWHtml::load_form($groups, $filter['class_type']);
                } else {
                    MWHtml::load_form(null, 5);
                }
                ?>

            </div>
            <div id="menu5" class="tab-pane fade">
                <?php
                if (is_sat_class_subscribed(6)) {

                    $current_page = max(1, get_query_var('page'));
                    $filter = get_page_filter_session();
                    if (empty($filter)) {
                        $filter['orderby'] = 'ordering';
                        $filter['items_per_page'] = 25;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                        $filter['group_type'] = GROUP_CLASS;
                        $filter['class_type'] = 6;
                    } else {
                        $filter['class_type'] = 6;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                    }

                    set_page_filter_session($filter);
                    $filter['offset'] = 0;
                    $filter['items_per_page'] = 99999999;
                    $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                    MWHtml::load_form($groups, $filter['class_type']);
                } else {
                    MWHtml::load_form(null, 6);
                }
                ?>

            </div>
            <div id="menu6" class="tab-pane fade">
                <?php
                if (is_sat_class_subscribed(7)) {
                    $current_page = max(1, get_query_var('page'));
                    $filter = get_page_filter_session();
                    if (empty($filter)) {
                        $filter['orderby'] = 'ordering';
                        $filter['items_per_page'] = 25;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                        $filter['group_type'] = GROUP_CLASS;
                        $filter['class_type'] = 7;
                    } else {
                        $filter['class_type'] = 7;
                        $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
                    }

                    set_page_filter_session($filter);
                    $filter['offset'] = 0;
                    $filter['items_per_page'] = 99999999;
                    $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
                    MWHtml::load_form($groups, $filter['class_type']);
                } else {
                    MWHtml::load_form(null, 7);
                }
                ?>

            </div>

        </div>

        <?php
    }

    public static function load_form($groups, $class_type_id) {
        $is_math_panel = is_math_panel();
        if ($class_type_id !== NULL) {
            $is_sat_class_subscribed = is_sat_class_subscribed($class_type_id);
        }
        ?>
        <form action="<?php echo $form_action ?>" method="post" id="main-form">
            <div class="box-purple homeworkcritical-online"  style="height: 458px;margin-top: 0px;">
                <div style="">
                    <table class="table table-striped table-condensed ik-table1 ik-table-break-all scroll-fix-head2" id="homeworkcritical">
                        <thead class="homeworkcritical">
                            <tr>
                                <?php if($class_type_id==1) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('Vocabulary / Grammar', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==2) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('Writing Skills', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==3) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT Test 1', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==4) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT Test 2', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==5) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT Test 3', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==6) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT Test 4', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==7) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT Test 5', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==9) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('Preparation', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==10) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT 1A', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==11) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT 1B', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==12) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT 1C', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==13) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT 1D', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==14) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT 1E', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==15) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('Preparation', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==16) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT 2A', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==17) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT 2B', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==18) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT 2C', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==19) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT 2D', 'iii-dictionary') ?></th>
                                <?php }else if($class_type_id==20) { ?>
                                    <th class="th-title-list-sat padd-left-15" colspan="4"><?php _e('SAT 2E', 'iii-dictionary') ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <thead class="homeworkcritical"><!--SAT Preparation and Simulation Test ENGLISH -->
                            <tr>
                                <th  class="text-color-custom-1 padd-left-15" style="width: 57% !important"><?php _e('Course Name', 'iii-dictionary') ?></th>
                                <th  class="text-color-custom-1 css-mobile-th2"><?php _e('No. of Worksheets', 'iii-dictionary') ?></th>
                                <th  class="text-color-custom-1 " style="width: 8% !important"><?php _e('Detail', 'iii-dictionary') ?></th>
                                <th  class="text-color-custom-1 "><?php _e('', 'iii-dictionary') ?></th>
                                <th  class="text-color-custom-1 "><?php _e('', 'iii-dictionary') ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr><td colspan="5"><?php echo $pagination ?></td></tr>
                        </tfoot>
                        <tbody>
                            <?php
                            if (!is_user_logged_in()) {
                                ?>
                                <tr><td colspan="6" style="padding-left: 15px;">You haven’t joined any groups yet. Please select from <a href="<?php echo home_url() ?>/?r=login" style="text-decoration: underline;">SAT Preparation  </a> section. </td></tr>
                                <?php for ($i = 1; $i < 12; $i++) {
                                    ?>
                                    <tr ><td class="row-full-1" colspan="6" ></td></tr>
                                    <?php
                                }
                            } else {
                                if (!empty($groups->items)) {
                                    foreach ($groups->items as $group) :
//                                    var_dump($group->content);
                                        ?>
                                        <tr>
                                            <td class="row-first2" style="padding-left: 15px;"><?php echo $group->name ?></td>
                                            <td style="padding-left: 4%;width:14%;"><?php echo is_null($group->no_homeworks) ? 0 : $group->no_homeworks ?></td>
                                            <td  style="width:10% !important"><a href="#" class="class-detail-btn class-detail-btn-default css-link">Click</a>
                                                <div>
                                                    <?php
                                                    $filter['homework_result'] = true;
                                                    $filter['user_id'] = get_current_user_id();
                                                    $filter['is_active'] = 1;
                                                    $filter['offset'] = 0;
                                                    $filter['items_per_page'] = 99999999;
                                                    $homeworks = MWDB::get_group_homeworks($group->id, $filter, $filter['offset'], $filter['items_per_page']);
                                                    echo $group->detail;
                                                    echo '<h3 class="modal-title" style="color: #708b23;padding-left: 2px;" id="myModalLabel">List Homework in Group ' . $group->content . ' </h3>';
                                                    foreach ($homeworks->items as $hw):
                                                        echo ' - ' . $hw->sheet_name . "<br>";
                                                    endforeach;
                                                    ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
//                                            
                                                if ($is_math_panel) {
                                                    if ($is_sat_class_subscribed):
                                                        $sat_results = get_sat_class_score($group->id);
//                                                    var_dump($sat_results);die;
                                                        if (is_sat_class_completed($sat_results)) :
                                                            ?>
                                                            <a href="<?php echo home_url() . '/?r=online-learning&sat=' . $class_type_id . '&amp;gid=' . $group->id ?>" class="links-purprle-default-math css-link"><?php _e('OPEN', 'iii-dictionary') ?></a> <!--Completed-->
                                                        <?php else :
                                                            ?>
                                                            <a href="<?php echo home_url() . '/?r=online-learning&sat=' . $class_type_id . '&amp;gid=' . $group->id ?>" class="links-purprle-default-math css-link"><?php _e('OPEN', 'iii-dictionary') ?></a> <!--Working-->
                                                        <?php
                                                        endif;
                                                    else:
                                                        ?>
                                                        <a href="javascript:void(0);" class="links-purprle renew-btn-default-math css-link"
                                                        <?php
                                                        if ($class_type_id == 9) {
                                                            echo 'data-sat-class="SAT Preparation - Preparation" data-subscription-type="7" data-type="9"';
                                                        } else if ($class_type_id == 10) {
                                                            echo 'data-sat-class="SAT Preparation - SAT 1A" data-subscription-type="7" data-type="10"';
                                                        } else if ($class_type_id == 11) {
                                                            echo 'data-sat-class="SAT Preparation - SAT 1B" data-subscription-type="7" data-type="11"';
                                                        } else if ($class_type_id == 12) {
                                                            echo 'data-sat-class="SAT Preparation - SAT 1C" data-subscription-type="7" data-type="12"';
                                                        } else if ($class_type_id == 13) {
                                                            echo 'data-sat-class="SAT Preparation - SAT 1D" data-subscription-type="7" data-type="13"';
                                                        } else if ($class_type_id == 14) {
                                                            echo 'data-sat-class="SAT Preparation - SAT 1E" data-subscription-type="7" data-type="14"';
                                                        } else if ($class_type_id == 15) {
                                                            echo 'data-sat-class="SAT Preparation - Preparation" data-subscription-type="8" data-type="15"';
                                                        } else if ($class_type_id == 16) {
                                                            echo 'data-sat-class="SAT Preparation - SAT 1A" data-subscription-type="8" data-type="16"';
                                                        } else if ($class_type_id == 17) {
                                                            echo 'data-sat-class="SAT Preparation - SAT 1B" data-subscription-type="8" data-type="17"';
                                                        } else if ($class_type_id == 18) {
                                                            echo 'data-sat-class="SAT Preparation - SAT 1C" data-subscription-type="8" data-type="18"';
                                                        } else if ($class_type_id == 19) {
                                                            echo 'data-sat-class="SAT Preparation - SAT 1D" data-subscription-type="8" data-type="19"';
                                                        } else if ($class_type_id == 20) {
                                                            echo 'data-sat-class="SAT Preparation - SAT 1E" data-subscription-type="8" data-type="20"';
                                                        }
                                                        ?>
                                                           ><?php _e('RENEW', 'iii-dictionary') ?></a> <!--RENEW-->
                                                       <?php
                                                       endif;
                                                   } else {
                                                       if ($is_sat_class_subscribed):
                                                           $sat_results = get_sat_class_score($group->id);
                                                           if (is_sat_class_completed($sat_results)) {
                                                               ?>
                                                            <a href="<?php echo home_url() . '/?r=online-learning&eng-prac=' . $class_type_id . '&amp;gid=' . $group->id ?>" class="links-purprle-default-math css-link"><?php _e('OPEN', 'iii-dictionary') ?></a> <!--COMPLITE-->
                                                        <?php } else if (!empty($group->finished_question)) {
                                                            ?>
                                                            <a href="<?php echo home_url() . '/?r=online-learning&eng-prac=' . $class_type_id . '&amp;gid=' . $group->id ?>" class="links-purprle-default-math css-link"><?php _e('OPEN', 'iii-dictionary') ?></a> <!--WORKING-->
                                                        <?php } else {
                                                            ?>
                                                            <a href="<?php echo home_url() . '/?r=online-learning&eng-prac=' . $class_type_id . '&amp;gid=' . $group->id ?>" class="links-purprle-default-math css-link"><?php _e('OPEN', 'iii-dictionary') ?></a> <!--RENEW-->
                                                            <?php
                                                        }
                                                    else:
                                                        if (is_student_in_group(get_current_user_id(), $group->id)) {
                                                            ?>
                                                            <a href="javascript:void(0);" class="links-purprle renew-btn-default-english css-link"
                                                            <?php
                                                            if ($class_type_id == 1) {
                                                                echo 'data-sat-class="Grammar Review" data-subscription-type="3" data-type="1"';
                                                            } else if ($class_type_id == 2) {
                                                                echo 'data-sat-class="Writing Practice" data-subscription-type="3" data-type="2"';
                                                            } else if ($class_type_id == 3) {
                                                                echo 'data-sat-class="SAT practice Test" data-subscription-type="3" data-type="3"';
                                                            } else if ($class_type_id == 4) {
                                                                echo 'data-sat-class="SAT practice Test" data-subscription-type="3" data-type="4"';
                                                            } else if ($class_type_id == 5) {
                                                                echo 'data-sat-class="SAT practice Test" data-subscription-type="3" data-type="5"';
                                                            } else if ($class_type_id == 6) {
                                                                echo 'data-sat-class="SAT practice Test" data-subscription-type="3" data-type="6"';
                                                            } else if ($class_type_id == 7) {
                                                                echo 'data-sat-class="SAT practice Test" data-subscription-type="3" data-type="7"';
                                                            }
                                                            ?>
                                                               ><?php _e('Open', 'iii-dictionary') ?></a> <!--Renew-->
                                                               <?php
                                                           } else {
                                                               ?>
                                                            <a href="javascript:void(0);" class="links-purprle start-btn-default-english css-link"
                                                            <?php
                                                            if ($class_type_id == 1) {
                                                                echo 'data-sat-class="Grammar Review" data-subscription-type="3" data-type="1"';
                                                            } else if ($class_type_id == 2) {
                                                                echo 'data-sat-class="Writing Practice" data-subscription-type="3" data-type="2"';
                                                            } else if ($class_type_id == 3) {
                                                                echo 'data-sat-class="SAT practice Test" data-subscription-type="3" data-type="3"';
                                                            } else if ($class_type_id == 4) {
                                                                echo 'data-sat-class="SAT practice Test" data-subscription-type="3" data-type="4"';
                                                            } else if ($class_type_id == 5) {
                                                                echo 'data-sat-class="SAT practice Test" data-subscription-type="3" data-type="5"';
                                                            } else if ($class_type_id == 6) {
                                                                echo 'data-sat-class="SAT practice Test" data-subscription-type="3" data-type="6"';
                                                            } else if ($class_type_id == 7) {
                                                                echo 'data-sat-class="SAT practice Test" data-subscription-type="3" data-type="7"';
                                                            }
                                                            ?>
                                                               ><?php _e('OPEN', 'iii-dictionary') ?></a> <!--Start-->
                                                               <?php
                                                           }
                                                       endif;
                                                   }
                                                   ?> 

                                            </td>
                                            <td><?php echo '' ?></td>
                                        </tr>
                                        <?php
                                    endforeach;

                                    if (count($groups->items) < 13) {
                                        for ($i = count($groups->items); $i < 13; $i++) {
                                            ?>
                                            <tr ><td style="height : 35px;width: 1%" colspan="5" ></td></tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <?php
                                } else {
                                    ?>
                                    <?php if ($class_type_id == 2) { ?>
                                        <tr>
                                            <td colspan="4" style="padding-left: 15px"><?php _e('You haven’t joined any groups yet. Please select from&nbsp;', 'iii-dictionary') ?><span id="sub-writing-modal" class="txt-sat-prepa"><?php _e('SAT Preparation', 'iii-dictionary') ?></span><?php _e('&nbsp;Section', 'iii-dictionary') ?></td>
                                        </tr>
                                    <?php } else if ($class_type_id == 1) { ?>
                                        <tr>
                                            <td colspan="4" style="padding-left: 15px"><?php _e('You haven’t joined any groups yet. Please select from&nbsp;', 'iii-dictionary') ?><span id="sub-grammar-modal" class="txt-sat-prepa"><?php _e('SAT Preparation', 'iii-dictionary') ?></span><?php _e('&nbsp;Section', 'iii-dictionary') ?></td>
                                        </tr>
                                    <?php } else if ($class_type_id == 3) { ?>
                                        <tr>
                                            <td colspan="4" style="padding-left: 15px"><?php _e('You haven’t joined any groups yet. Please select from&nbsp;', 'iii-dictionary') ?><span class="btn-sub-sat-eng txt-sat-prepa" data-sat="3"><?php _e('SAT Preparation', 'iii-dictionary') ?></span><?php _e('&nbsp;Section', 'iii-dictionary') ?></td>
                                        </tr>
                                    <?php } else if ($class_type_id == 4) { ?>
                                        <tr>
                                            <td colspan="4" style="padding-left: 15px"><?php _e('You haven’t joined any groups yet. Please select from&nbsp;', 'iii-dictionary') ?><span class="btn-sub-sat-eng txt-sat-prepa" data-sat="4"><?php _e('SAT Preparation', 'iii-dictionary') ?></span><?php _e('&nbsp;Section', 'iii-dictionary') ?></td>
                                        </tr>
                                    <?php } else if ($class_type_id == 5) { ?>
                                        <tr>
                                            <td colspan="4" style="padding-left: 15px"><?php _e('You haven’t joined any groups yet. Please select from&nbsp;', 'iii-dictionary') ?><span class="btn-sub-sat-eng txt-sat-prepa" data-sat="5"><?php _e('SAT Preparation', 'iii-dictionary') ?></span><?php _e('&nbsp;Section', 'iii-dictionary') ?></td>
                                        </tr>
                                    <?php } else if ($class_type_id == 6) { ?>
                                        <tr>
                                            <td colspan="4" style="padding-left: 15px"><?php _e('You haven’t joined any groups yet. Please select from&nbsp;', 'iii-dictionary') ?><span class="btn-sub-sat-eng txt-sat-prepa" data-sat="6"><?php _e('SAT Preparation', 'iii-dictionary') ?></span><?php _e('&nbsp;Section', 'iii-dictionary') ?></td>
                                        </tr> 
                                    <?php } else if ($class_type_id == 7) { ?>
                                        <tr>
                                            <td colspan="4" style="padding-left: 15px"><?php _e('You haven’t joined any groups yet. Please select from&nbsp;', 'iii-dictionary') ?><span class="btn-sub-sat-eng txt-sat-prepa" data-sat="7"><?php _e('SAT Preparation', 'iii-dictionary') ?></span><?php _e('&nbsp;Section', 'iii-dictionary') ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php for ($i = 0; $i < 13; $i++) { ?>
                                        <tr ><td class="row-full-1" colspan="5" ></td></tr>

                                        <?php
                                    }
                                }
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
            <input type="hidden" name="jid" id="jid">
            <input type="hidden" name="cltid" id="cltid">
        </form>

        <?php
    }

    public static function load_group_message() {
        $filter = get_page_filter_session();
        if (empty($filter)) {
            $filter['orderby'] = 'ordering';
            $filter['order-dir'] = 'asc';
        } else {
            if (isset($_REAL_POST['filter']['orderby'])) {
                $filter['orderby'] = $_REAL_POST['filter']['orderby'];
                $filter['order-dir'] = $_REAL_POST['filter']['order-dir'];
            }

            $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
        }

        set_page_filter_session($filter);
        $groups = MWDB::get_user_group_messageboard(get_current_user_id(), $filter);
        ?>

        <div style="">
            <div class="col-sm-12" style="padding: 0px !important;">
                <table class="table table-striped table-condensed ik-table1  vertical-middle scroll-fix-head3" id="homeworkcritical">
                    <thead class="homeworkcritical">
                        <tr>
                            <th style="color: #f5f5f5; padding-left: 5%; width: 21%" class="text-color-custom-1 "><?php _e('Group', 'iii-dictionary') ?></th>
                            <th  class="text-color-custom-1" style="padding-left:6%;"><?php _e('Last Post', 'iii-dictionary') ?></th>
                            <th  class="text-color-custom-1" style="padding-left:4%;width: 8%;"><?php _e('No.of Posts', 'iii-dictionary') ?></th>
                            <th style="width:5%" class="text-color-custom-1"><span class="span-pencil"></span></th>
                            <th class="text-color-custom-1"><?php _e('See Members', 'iii-dictionary') ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr><td colspan="5"><?php echo $pagination ?></td></tr>
                    </tfoot>
                    <tbody><?php if (empty($groups)) { ?>
                            <tr><td colspan="5" style="padding-left: 5%;"><?php _e('You haven\'t joined any group yet.', 'iii-dictionary') ?></td></tr>
                            <?php for ($i = 0; $i < 13; $i++) { ?>
                                <tr ><td class="row-full-1" colspan="5"></td></tr>
                                <?php
                            }
                        }
                        if (!is_user_logged_in()) :
                            for ($i = 0; $i < 13; $i++) {
                                ?>
                                <tr ><td class="row-full-1" colspan="5"></td></tr>
                                <?php
                            }
                        else :
                            foreach ($groups as $item) :
                                ?>
                                <tr>
                                    <td class="row-first3" style="padding-left:5%">
                                        <p class="text_underline"  id="msg-write" group_id="<?php echo $item->id; ?>" ><?php echo $item->group_name ?></p>
                                        <div id="body-modal-load" class="hidden">
                                            <div class="col-sm-4 max-height " id="body1">
                                                <table class="table-custom class-width" >
                                                    <tbody class="class-width">
                                                        <?php
                                                        $members = MWDB::get_group_members($item->id);
                                                        foreach ($members as $member) :
                                                            ?><tr class="class-width">
                                                                <td class="class-width"style="padding-left: 9%; color: black"><?php echo $member->display_name ?></td>
                                                            </tr><?php endforeach ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-xs-12 text-320-show" style="display:none"><p class="col-sm-8 p3-write-msg">All Posts</p></div>
                                            <div class="col-sm-8 max-height " id="body2" >

                                                <div class=" posts-message-modal" >
                                                    <?php
                                                    $messages = MWDB::get_group_messages($item->id);
                                                    if (empty($messages)) :
                                                        ?>
                                                        <div class="box box-gray-custom post-block">
                                                            <p class="text-center"><?php _e('No post', 'iii-dictionary') ?></p>
                                                        </div>
                                                        <?php
                                                    else :
                                                        foreach ($messages as $key => $message) :
                                                            ?>
                                                            <div class="box box-gray-custom post-block-custom" id="<?php echo 'id_' . $key ?>">
                                                                <span style="right: 3%;padding-top: 2%;" data_id="<?php echo $message->id ?>" class="close remove-dialog"></span>
                                                                <div class="post-header">
                                                                    <span class="post-num"><?php echo $key + 1 . ')' ?></span>
                                                                    <span class="post-author"><?php echo $message->poster ?></span>
                                                                    <span class="post-date"><?php echo ik_date_format($message->posted_on, 'M d, Y H:i') ?></span>
                                                                </div>
                                                                <div class="post-content"><?php echo $message->message ?></div>
                                                            </div>
                                                            <?php
                                                        endforeach;
                                                    endif
                                                    ?>
                                                    <input class="count-new-msg hidden" value="<?php echo count($messages); ?>"/> <!--id="count-new-msg"-->
                                                </div>
                                            </div>

                                        </div>
                                    </td>
                                    <td ><?php
                                        if (empty($item->posted_on)) :
                                            _e('No post', 'iii-dictionary');
                                        else :
                                            echo ik_date_format($item->posted_on, 'M d, Y H:i')
                                            ?> <br> <?php _e('by', 'iii-dictionary') ?> <a href="#"><?php echo $item->poster ?></a><?php
                                        endif;
                                        ?>
                                    </td>
                                    <td><?php echo $item->replies ?></td>
                                    <td style="color: #0065bb;width: 12% !important;">
                                        <p class="m_text_underline" id="msg-write" group-name=" <?php echo $item->group_name ?> " group_id=" <?php echo $item->id; ?> "> <?php _e('WRITE', 'iii-dictionary') ?> </p>
                                        <div id="body-modal-load" class="hidden">
                                            <div class="col-sm-4 max-height " id="body1">
                                                <table class="table-custom class-width" >
                                                    <tbody class="class-width">
                                                        <?php
                                                        $members = MWDB::get_group_members($item->id);
                                                        foreach ($members as $member) :
                                                            ?><tr class="class-width">
                                                                <td class="class-width td-dialog-msg"><?php echo $member->display_name ?></td>
                                                            </tr><?php endforeach; ?>
                                                        <?php
                                                        if (count($members) < 12) {
                                                            for ($i = count($members); $i < 12; $i++) {
                                                                ?>
                                                                <tr class="class-width"><td class="class-width td-dialog-msg" style="height: 30px"></td></tr>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-xs-12 text-320-show" style="display:none"><p class="col-sm-8 p3-write-msg">All Posts</p></div>
                                            <div class="col-sm-8 max-height " id="body2" >

                                                <div class=" posts-message-modal" >
                                                    <?php
                                                    $messages = MWDB::get_group_messages($item->id);
                                                    if (empty($messages)) :
                                                        ?>
                                                        <div class="box box-gray-custom post-block" style="margin-bottom: 15px">
                                                            <p class="text-center"><?php _e('No post', 'iii-dictionary') ?></p>
                                                        </div>
                                                        <?php
                                                    else :
                                                        foreach ($messages as $key => $message) :
                                                            ?>
                                                            <div class="box box-gray-custom post-block-custom" id="<?php echo 'id_' . $key ?>">
                                                                <span style="right: 3%;padding-top: 2%;" data_id="<?php echo $message->id ?>" class="close remove-dialog"></span>
                                                                <div class="post-header">
                                                                    <span class="post-num"><?php echo $key + 1 . ')' ?></span>
                                                                    <span class="post-author"><?php echo $message->poster ?></span>
                                                                    <span class="post-date"><?php echo '&nbsp; - &nbsp;' . ik_date_format($message->posted_on, 'M d, Y H:i') ?></span>
                                                                </div>
                                                                <div class="post-content"><?php echo $message->message ?></div>
                                                            </div>
                                                            <?php
                                                        endforeach;
                                                    endif;
                                                    if (count($messages) < 13) {
                                                        for ($i = count($messages); $i < 13; $i++) {
                                                            ?>
                                                            <div class="box box-gray-custom post-block-custom">
                                                                <p class="text-center" style="height: 34px"></p>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                    <input class="count-new-msg hidden"  value="<?php echo count($messages); ?>"/> <!--id="count-new-msg"-->
                                                </div>
                                            </div>

                                        </div>
                                    </td>
                                    <td style="color: #0065bb">
                                        <p class="m_text_underline" id="msg-member"><?php _e('MEMBERS LIST', 'iii-dictionary') ?></p>
                                        <table class="hidden" id="members-list"><tbody><?php
                                                $members = MWDB::get_group_members($item->id);
                                                foreach ($members as $member) :
                                                    ?><tr>
                                                        <td style="padding-left: 5%"><?php echo $member->display_name ?></td>
                                                        <td><?php echo ik_date_format($member->joined_date, 'M d, Y') ?></td><?php ?>
                                                    </tr><?php endforeach ?>
                                                <?php
                                                if (count($members) < 9) {
                                                    for ($i = count($members); $i < 9; $i++) {
                                                        ?>
                                                        <tr ><td class="row-full-1" colspan="5"></td></tr>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="width:1%"></td>
                                </tr>
                                <?php
                            endforeach;
                            if (count($groups) < 13) {
                                for ($i = count($groups); $i < 13; $i++) {
                                    ?>
                                    <tr ><td class="row-full-1" colspan="5"></td></tr>
                                    <?php
                                }
                            }
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    public static function load_private_input_message() {
        $messages = MWDB::get_private_input_message_box();
        $count = 0;
        ?>
        <div style="">
            <table class="table table-striped table-condensed ik-table1 scroll-fix-head4" id="homeworkcritical">
                <thead class="homeworkcritical">
                    <tr>
                        <th class="text-color-custom-1" style="padding-left: 5%; width: 22%;"><?php _e('From', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 20%;"><?php _e('Status', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="padding-left: 4%;width: 33%"><?php _e('Subject', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 14%"><?php _e('Received Date', 'iii-dictionary') ?></th>
                        <th style="text-align: center;width: 11%"><span class="span-pencil"></span></th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr><td colspan="4"><?php echo $pagination ?></td></tr>
                </tfoot>
                <tbody>
                    <?php if (empty($messages)) : ?>
                        <tr>
                            <td colspan="6" style="padding-left: 5%;width: 1% !important;"><?php _e('You haven\'t received any message.', 'iii-dictionary') ?></td>
                        </tr>
                        <?php for ($i = 0; $i < 13; $i++) { ?>
                            <tr ><td style="height : 35px;width: 1% !important;" colspan="6" ></td></tr>
                        <?php } ?>
                        <?php
                    else :
                        if (is_user_logged_in()) {
                            foreach ($messages as $item) :
                                ?>
                                <tr>
                                    <td style="padding-left: 5%;"><?php echo get_user_by('id', $item->sender_id)->display_name; ?></td>
                                    <?php
                                    if ($item->status == 1) {
                                        ?>
                                        <td>Read</td> 
                                        <?php
                                    } else {
                                        ?>

                                        <td style="color:#0E0C9F !important;">Unread</td>
                                        <?php
                                        $count++;
                                    }
                                    ?>

                                    <td><?php echo ik_cut_str($item->subject, 55) ?></td>
                                    <td><?php echo ik_date_format($item->received_on, 'M d, Y H:i') ?></td>
                                    <td><a href="#received_msg" data-id="<?php echo $item->id ?>" data-date="<?php echo $item->received_on ?>" data-status="<?php echo $item->status ?>" class="btn btn-default btn-block btn-tiny grey btn-a-link btn_scroll css-link"><?php _e('Open', 'iii-dictionary') ?></a></td>
                                </tr>
                                <?php
                            endforeach;
                        }else{
                            for ($i = 1; $i < 13; $i++) {
                                echo '<tr ><td style="height : 35px;width: 1%" colspan="5" ></td></tr>';
                            }
                        } 
                        if (count($messages) < 13) {
                            for ($i = count($messages); $i < 13; $i++) {
                                echo '<tr ><td style="height : 35px;width: 1%" colspan="5" ></td></tr>';
                            }
                        }

                    endif;
                    ?>
                <div class="hidden" id="count-new-message"><?php echo $count; ?></div>
                </tbody>
            </table>
        </div>

        <?php
    }

    public static function load_private_out_message() {
        $messages = MWDB::get_private_output_message_box();
        $count = 0;
        ?>
        <div>
            <table class="table table-striped table-condensed ik-table1 scroll-fix-head5" id="homeworkcritical">
                <thead class="homeworkcritical">
                    <tr>
                        <th class="text-color-custom-1 padding-left-5" style="width:10%;"><?php _e('Recipient', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width:11%;padding-left: 4%;"><?php _e('Subject', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width:12%;padding-left: 5%;"><?php _e('Sent Date', 'iii-dictionary') ?></th>
                        <th style="width: 6%"><span class="span-pencil"></span></th>
                        <th style="width:1%"></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr><td colspan="4"><?php echo $pagination ?></td></tr>
                </tfoot>
                <tbody>
                    <?php if (empty($messages)) : ?>
                        <tr>
                            <td colspan="4" class="padding-left-5"><?php _e('You haven\'t received any message.', 'iii-dictionary') ?></td></tr>
                        <?php for ($i = 0; $i < 13; $i++) { ?>
                            <tr ><td class="row-full-1" colspan="4" ></td></tr>
                        <?php } ?>
                        <?php
                    else :
                        if (is_user_logged_in()) {
                            foreach ($messages as $item) :
                                ?>
                                <tr>
                                    <td class="row-first5"><?php echo $item->display_name; ?></td>
                                    <td><?php echo ik_cut_str($item->subject, 55) ?></td>
                                    <td><?php echo ik_date_format($item->sent_on, 'M d, Y H:i') ?></td>
                                    <td>
                                        <a href="#read-msg" data-id="<?php echo $item->id ?>" class="btn btn-default btn-block btn-tiny grey btn-a-link read-msg css-link" target="_blank" data-toggle="modal"><?php _e('Open', 'iii-dictionary') ?></a>
                                    </td>
                                    <td class="row-full-1"></td>
                                </tr>
                                <?php
                            endforeach;
                        } else{
                            for ($i = 1; $i < 13; $i++) {
                                echo '<tr ><td class="row-full-1" colspan="5" ></td></tr>';
                            }
                        }
                        if (count($messages) < 13) {
                            for ($i = count($messages); $i < 13; $i++) {
                                echo '<tr ><td class="row-full-1" colspan="5" ></td></tr>';
                            }
                        }
                    endif;
                    ?>
                <div class="hidden" id="count-new-message"><?php echo $count; ?></div>
                </tbody>
            </table>
        </div>

        <?php
    }

    public function load_group_homework($homeworkgroup) {
        ?>
        <div id="width-table" style="display: block; ">
            <table class="table table-striped table-condensed ik-table1 scroll-fix-head" id="homeworkcritical" style="border-bottom: 2px solid #000000; border-left: 3px solid #000000; border-right: 2px solid #000000; ">
                <thead class="homeworkcritical">
                    <tr>
                        <th class="text-color-custom-1" style="padding-left: 5%;width: 28%"><?php _e('Name', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 13%"><?php _e('Price', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 42% !important"><?php _e('Course Name', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 7%"><?php _e('Detail', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 6%"><?php _e('Join', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 5%"><?php _e('No. of W.S.', 'iii-dictionary') ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                </tfoot>
                <tbody>
                    <?php if (empty($homeworkgroup->items)) : ?>
                        <tr>
                            <td colspan="4"><?php _e('Haven\'t group', 'iii-dictionary') ?></td>
                        </tr>
                        <?php
                    else :
                        foreach ($homeworkgroup->items as $item) :
                            $get_stg = MWDB::get_something_in_group($item->id);
                            ?>
                            <tr>
                                <td style="padding-left: 5%;"><div class="width-columns-name-group"><?php echo $item->name ?></div></td>
                                <td style="font-weight: bold"><?php
                                    if ($item->price == 0) {
                                        echo 'FREE';
                                    } else {
                                        echo '$' . $item->price;
                                    }
                                    ?></td>

                                <td><div class="width-columns-name-worksheet"><?php echo $item->content ?></div></td>
                                <td style="font-weight: bold;text-decoration: underline"><div><a href="#" class="bold-font btn btn-default btn-block btn-tiny grey btn-a-link <?php
                                        if (is_null($exist)) {
                                            echo 'not-join';
                                        } else {
                                            echo '';
                                        }
                                        ?>" ><?php _e('DETAIL', 'iii-dictionary') ?></a>
                                        <div class="hidden">
                                            <div style="width: 800px"></div>
                                            <?php
                                            $namehomework = MWDB::get_name_homework_group($item->id);
                                            foreach ($namehomework as $nhw):
                                                echo ' - ' . $nhw->namehw . "<br>";
                                            endforeach;
                                            ?>
                                        </div>
                                    </div></td>
                                <td>
                                    <?php if (empty($get_stg->step_of_user) && $item->price != 0) { ?>
                                        <div ><a href="#" data-group-id="<?php echo $item->id ?>" class="bold-font btn btn-default btn-block btn-tiny grey btn-a-link a-join-group check-sub-class purchase-join-class-english" ><?php _e('JOIN', 'iii-dictionary') ?></a></div>
                                        <?php
                                    } else {
                                        $practice_url = MWHtml::get_practice_page_url($get_stg->step['assg']) . '&amp;hid=' . $get_stg->step['id'];
                                        $homework_url = MWHtml::get_practice_page_url($get_stg->step['assg']) . '&amp;mode=homework&amp;hid=' . $get_stg->step['id'];
                                        $rp_url = $get_stg->step['prt'] ? $practice_url : $homework_url;
                                        $rp_url = !empty($uref) ? $rp_url . '&ref=' . $uref : $rp_url;
                                        ?>
                                        <a href="<?php echo $rp_url ?>" class="bold-font btn btn-default btn-block btn-tiny grey btn-a-link prevent_detail-btn-<?php echo $is_math ? 'math' : 'english' ?> "><?php _e('START', 'iii-dictionary') ?></a>
                                    <?php } ?>
                                </td>
                                <td><div ><?php
                                        $count = MWDB::get_count_worksheets_group($item->id);
                                        $count_complete = MWDB::get_count_worksheets_completeed_group($item->id);
//                                        echo $count_complete[0]->count . '/' . $count[0]->count;
                                        echo $count[0]->count;
                                        ?></div></td>
                            </tr>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>

        <?php
    }

    public function load_group_homework_english($homeworkgroup) {
        ?>
        <div id="width-table" style="display: block; ">
            <table class="table table-striped table-condensed ik-table1 scroll-fix-head" id="homeworkcritical" style="border-bottom: 2px solid #000000; border-left: 3px solid #000000; border-right: 2px solid #000000; ">
                <thead class="homeworkcritical">
                    <tr>
                        <th class="text-color-custom-1" style="padding-left: 5%;width: 27%"><?php _e('Name', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 13%;padding-left: 2%"><?php _e('Price', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 42% !important"><?php _e('Course Name', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 7%;padding-left: 3%"><?php _e('Detail', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 4%"><?php _e('Join', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 10%"><?php _e('No. of W.S.', 'iii-dictionary') ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                </tfoot>
                <tbody>
                    <?php if (empty($homeworkgroup->items)) : ?>
                        <tr>
                            <td colspan="4"><?php _e('Haven\'t group', 'iii-dictionary') ?></td>
                        </tr>
                        <?php
                    else :
                        foreach ($homeworkgroup->items as $item) :
                            $get_stg = MWDB::get_something_in_group($item->id);
                            ?>
                            <tr>
                                <td style="padding-left: 5%;width: 54%"><div class="width-columns-name-group"><?php echo $item->name ?></div></td>
                                <td style="font-weight: bold;width: 17%"><?php
                                    if ($item->price == 0) {
                                        echo 'FREE';
                                    } else {
                                        echo '$' . $item->price;
                                    }
                                    ?></td>

                                <td style="width: 6%"><div class="width-columns-name-worksheet"><?php echo $item->content ?></div></td>
                                <td style="font-weight: bold;width: 3%;text-decoration: underline"><div><a href="#" class="bold-font btn btn-default btn-block btn-tiny grey btn-a-link <?php
                                        if (is_null($exist)) {
                                            echo 'not-join';
                                        } else {
                                            echo '';
                                        }
                                        ?>" ><?php _e('DETAIL', 'iii-dictionary') ?></a>
                                        <div class="hidden">
                                            <div style="width: 800px"></div>
                                            <?php
                                            $namehomework = MWDB::get_name_homework_group($item->id);
                                            foreach ($namehomework as $nhw):
                                                echo ' - ' . $nhw->namehw . "<br>";
                                            endforeach;
                                            ?>
                                        </div>
                                    </div></td>
                                <td style="width: 16%;padding-right: 2%;">
                                    <?php if (empty($get_stg->step_of_user) && $item->price != 0 || !is_user_logged_in()) { ?>
                                        <div ><a href="#modal-purchase-join-class-enlish" data-group-id="<?php echo $item->id ?>" class="bold-font btn btn-default btn-block btn-tiny grey btn-a-link a-join-group check-sub-class purchase-join-class-english" ><?php _e('JOIN', 'iii-dictionary') ?></a></div>
                                        <?php
                                    } else {
                                        $practice_url = MWHtml::get_practice_page_url($get_stg->step['assg']) . '&amp;hid=' . $get_stg->step['id'];
                                        $homework_url = MWHtml::get_practice_page_url($get_stg->step['assg']) . '&amp;mode=homework&amp;hid=' . $get_stg->step['id'];
                                        $rp_url = $get_stg->step['prt'] ? $practice_url : $homework_url;
                                        $rp_url = !empty($uref) ? $rp_url . '&ref=' . $uref : $rp_url;
                                        ?>
                                        <a href="<?php echo $rp_url ?>" class="bold-font btn btn-default btn-block btn-tiny grey btn-a-link prevent-detail-btn-<?php echo $is_math ? 'math' : 'english' ?> "><?php _e('START', 'iii-dictionary') ?></a>
                                    <?php } ?>
                                </td>
                                <td style="width: 16%;"><div ><?php
                                        $count = MWDB::get_count_worksheets_group($item->id);
                                        $count_complete = MWDB::get_count_worksheets_completeed_group($item->id);
                                        echo $count[0]->count;
                                        ?></div></td>
                            </tr>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>

        <?php
    }

    public function load_group_homework_math($homeworkgroup) {
        ?>
        <div id="width-table" style="display: block; ">
            <table class="table table-striped table-condensed ik-table1 scroll-fix-head" id="homeworkcritical" style="border-bottom: 2px solid #000000; border-left: 3px solid #000000; border-right: 2px solid #000000;">
                <thead class="homeworkcritical">
                    <tr>
                        <th class="text-color-custom-1" style="padding-left: 5%;width: 26%"><?php _e('Name', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 13%;padding-left: 1%"><?php _e('Price', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 43% !important"><?php _e('Course Name', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 7%;padding-left: 4%;"><?php _e('Detail', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 4%"><?php _e('Join', 'iii-dictionary') ?></th>
                        <th class="text-color-custom-1" style="width: 10%"><?php _e('No. of W.S.', 'iii-dictionary') ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                </tfoot>
                <tbody>
                    <?php if (empty($homeworkgroup->items)) : ?>
                        <tr>
                            <td colspan="4"><?php _e('Haven\'t group', 'iii-dictionary') ?></td>
                        </tr>
                        <?php
                    else :
                        foreach ($homeworkgroup->items as $item) :
                            $get_stg = MWDB::get_something_in_group($item->id);
                            ?>
                            <tr>
                                <td style="padding-left: 5%;width: 54%"><div class="width-columns-name-group"><?php echo $item->name ?></div></td>
                                <td style="font-weight: bold;width: 17%"><?php
                                    if ($item->price == 0) {
                                        echo 'FREE';
                                    } else {
                                        echo '$' . $item->price;
                                    }
                                    ?></td>

                                <td style="width: 6%"><div class="width-columns-name-worksheet1"><?php echo $item->content ?></div></td>
                                <td style="font-weight: bold;width: 3%;text-decoration: underline"><div><a href="#" class="bold-font btn btn-default btn-block btn-tiny grey btn-a-link <?php
                                        if (is_null($exist)) {
                                            echo 'not-join';
                                        } else {
                                            echo '';
                                        }
                                        ?>" ><?php _e('DETAIL', 'iii-dictionary') ?></a>
                                        <div class="hidden">
                                            <div style="width: 800px"></div>
                                            <?php
                                            $namehomework = MWDB::get_name_homework_group($item->id);
                                            foreach ($namehomework as $nhw):
                                                echo ' - ' . $nhw->namehw . "<br>";
                                            endforeach;
                                            ?>
                                        </div>
                                    </div></td>

                                <td style="width: 16%;padding-right: 2">
                                    <?php if (empty($get_stg->step_of_user) && $item->price != 0) { ?>
                                        <div ><a href="#modal-purchase-join-class-math" data-group-id="<?php echo $item->id ?>" data-name="<?php echo $item->name ?>" class="bold-font btn btn-default btn-block btn-tiny grey btn-a-link purchase-join-class-math"><?php _e('JOIN', 'iii-dictionary') ?></a></div>
                                        <?php
                                    } else {
                                        $practice_url = MWHtml::get_practice_page_url($get_stg->step['assg']) . '&amp;hid=' . $get_stg->step['id'];
                                        $homework_url = MWHtml::get_practice_page_url($get_stg->step['assg']) . '&amp;mode=homework&amp;hid=' . $get_stg->step['id'];
                                        $rp_url = $get_stg->step['prt'] ? $practice_url : $homework_url;
                                        $rp_url = !empty($uref) ? $rp_url . '&ref=' . $uref : $rp_url;
                                        ?>
                                        <a href="<?php echo $rp_url ?>" class="bold-font btn btn-default btn-block btn-tiny grey btn-a-link prevent_detail-btn-<?php echo $is_math ? 'math' : 'english' ?> "><?php _e('START', 'iii-dictionary') ?></a>
                                    <?php } ?>
                                </td>
                                <td style="width: 16%"><div ><?php
                                        $count = MWDB::get_count_worksheets_group($item->id);
                                        $count_complete = MWDB::get_count_worksheets_completeed_group($item->id);

                                        echo $count[0]->count;
                                        ?></div></td>
                            </tr>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /*
     * generate select level page content
     *
     * @param array $levels
     */

    public static function select_math_level_page($levels) {
        $route = get_route();
        $ref = locale_home_url() . '/?r=' . $route[0];
        ?>
        <div class="row css-padd1">
            <?php
            foreach ($levels as $level) :
                $sublevels = MWDB::get_grades(array('type' => 'MATH', 'level' => 2, 'parent_id' => $level->id, 'orderby' => 'ordering', 'order-dir' => 'asc'));
                ?>
                <?php if($level->show_panel ==1) {?>
                    <section class="col-sm-12 math-levels math-lv-<?php echo $level->id; ?>">
                        <h6 class="math-level-title"><?php echo $level->name ?></h6>
                        <div class="col-sm-12 pad-img-math">
                            <!--div image-->
                            <div  class="col-sm-3 col-md-2 col-xs-6 css-wid image-math-<?php echo $level->id; ?>"></div>
                            <!--div ul worksheet-->
                            <div class="col-sm-9 col-md-10">
                                <ul class="math-sublevels css-wid1" >
                                    <?php
                                    foreach ($sublevels as $subitem) :
                                        if ($subitem->show_panel == 1):
                                            ?>
                                            <li class="select-math-level" data-sub="<?php echo $level->name ?>" data-name="<?php echo $subitem->name ?>" data-toggle="modal" data-target=".bd-assignment-classes-created-modal-lg" data-level="<?php echo $subitem->id ?>"><?php echo $subitem->name ?></li>
                                            <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </section>
                <?php } ?>
            <?php endforeach; ?>
            <input type="hidden" id="uref" value="<?php echo rawurlencode(base64_encode($ref)) ?>">
        </div>

        <!--MODAL SHOW LIST WORKSHEET-->
        <div id="select-math-worksheet-dialog" class="modal fade modal-large modal-green css-modal-math-sheet" aria-hidden="true">
            <div class="modal-dialog css-dialog-math-sheet">
                <div class="modal-content css-content-math-sheet" style="padding:0px !important;">
                    <div id="cursors"></div>
                    <div class="modal-header" style="background: #fff;">
                        <div class="col-sm-11 form-group css-mb-math-modal2" style="margin-bottom: 0px !important">
                            <div id="math-level" class="css-font-helvetica-regular"></div>
                            <?php
                            if (strpos($_SERVER['REQUEST_URI'], "calculus") !== false) {
                                $color = "calculus";
                            } else if (strpos($_SERVER['REQUEST_URI'], "geometry") !== false) {
                                $color = "geometry";
                            } else if (strpos($_SERVER['REQUEST_URI'], "algebra-ii") !== false) {
                                $color = "algebra-ii";
                            } else if (strpos($_SERVER['REQUEST_URI'], "algebra-i") !== false) {
                                $color = "algebra-i";
                            } else if (strpos($_SERVER['REQUEST_URI'], "arithmetics") !== false) {
                                $color = "arithmetics";
                            }
                            ?>
                            <div id="math-sublevel" class="css-font-helvetica-bold font-math css-color-<?php echo $color ?>"></div>
                        </div>
                        <div class="col-sm-1 css-mb-math-modal" >
                            <a href="#" data-dismiss="modal" aria-hidden="true" class="icon-close-working-ws-01"></a>
                        </div>
                        <input type="hidden" name="data-level1" id="data-level1" val="0">
                    </div>

                    <div class="modal-body" style="padding: 0px 0px;">
                        <div class="row">
                            <div class="col-sm-12 form-group">
                                <hr class="css-hr-bbbbbb">
                                <div class="css-tab-math-modal">
                                    <table class="table ik-table2 ik-table-green" id="sel-worksheets" data-empty-msg="<?php _e('More Worksheets are coming!', 'iii-dictionary') ?>"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--MODAL WORKING WORKSHEET-->
        <div id="modal-working-worksheet" class="modal fade modal-large modal-green " aria-hidden="true" style="padding-left: 0px !important;">
            <div class="css-only-show-mb bg-cfcfcf title-mb-working-ws">
                <span id="notepad-button" class="css-notepad-btn"></span>
                <span><a href="<?php echo home_url() .'/?r=tutoring-plan' ?>" class="ic-math-tutoring css-only-show-mb"></a></span>
                <span class="css-btn-close-working-ws">
                    <a href="#"  class="icon-close-working-ws mb-ic-position btn-close-working-ws" style="margin-top: 13px;"></a>
                </span>
                <span id="speaker-button-mb" class="btn-ic-volum icon-sound"></span>
            </div>
            <div class="modal-dialog css-dialog-math-sheet">
                <div class="modal-content css-content-math-sheet" style="padding:0px !important;background: #F5F0A3 !important;">
                    <div id="cursors"></div>
                    <div class="modal-body" style="padding: 0px 0px 0px 0px;background: #fff !important;">
                        <div class="col-sm-2 css-align div-volum-mb css-only-show-tablet">
                            <span id="speaker-button" class="btn-ic-volum icon-sound"></span>
                            <span class="ic-vertical css-only-show-tablet" style="pointer-events: none;"></span>
                            <a href="#" aria-hidden="true" class="css-only-show-tablet icon-close-working-ws mb-ic-position btn-close-working-ws" style="margin-top: 13px;"></a>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group css-ma-auto css-pad0">
                                <div id="localtion-worksheet" class="col-sm-12 form-group" >
                                    <?php $route = get_route();
                                        if($route[0]=="calculus"){
                                            $route[0]="Calculus";
                                        }else if($route[0]=="geometry"){
                                            $route[0]="Geometry";
                                        }else if($route[0]=="algebra-ii"){
                                            $route[0]="Algebra 2";
                                        }else if($route[0]=="algebra-i"){
                                            $route[0]="Algebra 1";
                                        }
                                        else if($route[0]=="arithmetics"){
                                            $route[0]="Arithmetics";
                                        }
                                    ?>
                                    <ul class="breadcrumb css-bre" style="padding-bottom: 5px !important;">
                                        <div id="math-level" class="css-font-helvetica-regular css-head-ws1"></div>
                                        <hr class="css-hr1">
                                        <li><a href="#" id="parent" class="css-bre1"><?php echo $route[0]?></a></li>
                                        <li><a href="#" id="sub-parent" class="css-bre1 "></a></li>
                                        <li class="active css-bre1" id="is-location"></li>
                                    </ul>
                                </div>
                                <div class="" style="max-height: 100%; max-width: 100%;">
                                    <div class="hr-shadow hr-mb-working-ws css-only-show-tablet"></div>
                                    <div id="" class="hidden css-anwer-correct css-only-show-tablet txt-answer-correct-last">
                                    </div>
                                    <div class="col-sm-2 css-only-show-tablet" style="float: right">
                                        <span id="" class="hidden css-close-an-correct ic-close-an-correct"></span>
                                    </div>
                                    <form id="main-form" method="post" action="#">
                                    <table style="margin-bottom: 0px !important;" class="table ik-table2 ik-table-green css-modal-ws-content" id="sel-worksheets" data-empty-msg="<?php _e('No worksheets', 'iii-dictionary') ?>"></table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal show result homework math-->
        <div id="modal-view-result-homework" class="modal fade modal-red-brown">
            <div class="modal-dialog modal-custom-first">
                <div class="modal-content boder-black">
                    <div class="modal-header custom-header" >
                        <span style="right: 3%;margin-top: -3px !important;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                        <span ><h3 class="txt-wr"><?php _e('Worksheet - Score Result', 'iii-dictionary') ?></h3></span>
                    </div>
                    <div id="can-scroll" style="background: #fff;color: #000;padding-top: 3%">
                    </div>
                    <div class="modal-footer footer-custom" style="padding-left: 5%;padding-top: 0px;">
                        <span class="text-food-view-rs">Restart the Worksheet. This will overwrite the previous score.</span>
                        <input type="button" id="btn-ok-rs-homework" class="css-ok-rs" value="Restart the Worksheet">
                        <input type="button" id="close-wd-homework" class="css-close-wd" value="Close The Window">
                    </div>
                    <div class="hidden" id="load-prevent-modal"></div>
                </div>
            </div>
        </div>
        
        <!-- Modal popup message submit homework math-->
        <div id="modal-popup-message-submit" class="modal fade modal-red-brown">
            <div class="modal-dialog modal-custom-first img-answer-submit">
            </div>
        </div>
        
        <!-- Modal popup message submit homework math-->
        <div id="modal-popup-message-answer" class="modal fade modal-red-brown">
            <div class="modal-dialog modal-custom-first load-img-answer">
                    <!-- load data ajax -->
            </div>
        </div>
        
<!--         Student's Self-study Subscription 
        <div id="self-study-subscription-dialog" class="modal fade">
            <div class="modal-dialog modal-custom-first">
                <div class="modal-content boder-black">
                    <div class="modal-header custom-header">
                        <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                        <h3><?php _e('Student\'s Self-study Subscription', 'iii-dictionary') ?></h3>
                    </div>
                    <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                        <input type="hidden" name="sub-type" value="<?php echo!$is_math_panel ? SUB_SELF_STUDY : SUB_SELF_STUDY_MATH ?>" id="self-study-sub">
                        <?php $self_study_group = generate_self_study_group_name() ?>
                        <input type="hidden" name="group-name" value="<?php echo $self_study_group ?>">
                        <input type="hidden" name="group-pass" value="<?php echo $self_study_group ?>">
                        <input type="hidden" name="sat-months" id="self-sat-months" value="1">
                        <div class="modal-body">
                            <div class="row">
                                <div class=" form-group">

                                    <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Default Group for this subscription', 'iii-dictionary') ?></label>
                                    <p class="selected-class col-xs-12" style="padding: 5px 15px"><?php echo $self_study_group ?></p>
                                </div>
                                <div class="col-sm-12 form-group" id="ss-dict-block">
                                    <label class="font-dialog"><?php _e('Select the type of dictionary', 'iii-dictionary') ?></label>
                                    <?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary2', 'form-control', false) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="font-dialog"><?php _e('Number of Students', 'iii-dictionary') ?></label>
                                        <input type="number" name="no-students" class="form-control" min="1" max="1" value="1" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                        <select class="select-box-it form-control" name="self-study-months" id="sel-self-study-months">
                                            <?php for ($i = 1; $i <= 24; $i++) : ?>
                                                <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                            <?php endfor ?>
                                        </select>   
                                    </div>
                                </div>
                                <div class="col-sm-12 padding-top-2">
                                    <div class="box-gray-dialog" style="text-align: right">
                                        <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$</span> <span class="currency color708b23" id="ss-total-amount">0</span>
                                    </div>
                                </div>
                            </div>				
                        </div>
                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <?php if (is_math_panel()) { ?>
                                            <button type="submit" id="add-to-cart-ss-math" name="add-to-cart" class="btn-custom"><?php _e('Check out', 'iii-dictionary') ?></button>
                                        <?php } else { ?>
                                            <button type="submit" id="add-to-cart-ss-english" name="add-to-cart" class="btn-custom"><?php _e('Check out', 'iii-dictionary') ?></button>
                                        <?php } ?>
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
        </div>-->

        <!--modal-required subscribe-->
        <div id="require-modal1" class="modal fade modal-white ik-modal1 ik-modal-transparent" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <a href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog" style="margin-top: 13px;"></a>
                        <h3 style="color: #fff"><?php _e('Save to Folder', 'iii-dictionary') ?></h3>
                    </div>
                    <div class="modal-body" style="padding: 25px 25px 10px 25px;">
                    </div>
                    <div class="modal-footer" style="padding-bottom: 30px;">
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
        <!-- Student's Self-study Subscription -->
        <div id="self-study-subscription" class="modal fade">
            <div class="modal-dialog modal-custom-first">
                <div class="modal-content boder-black">
                    <div class="modal-header custom-header">
                        <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                        <h3><?php _e('Student\'s Self-study Subscription', 'iii-dictionary') ?></h3>
                    </div>
                    <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                        <input type="hidden" name="sub-type" value="9" id="self-study-sub"> <!--set value =9 vì nó là self-subscrible-math-->
                        <?php $self_study_group = generate_self_study_group_name() ?>
                        <input type="hidden" name="group-name" value="<?php echo $self_study_group ?>">
                        <input type="hidden" name="group-pass" value="<?php echo $self_study_group ?>">
                        <div class="modal-body">
                            <div class="row">
                                <div class=" form-group">

                                    <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Default Group for this subscription', 'iii-dictionary') ?></label>
                                    <p class="selected-class col-xs-12" style="padding: 5px 15px"><?php echo $self_study_group ?></p>
                                </div>
                                <div class="col-sm-12 form-group" id="ss-dict-block" style="display: none;">
                                    <label class="font-dialog"><?php _e('Select the type of dictionary', 'iii-dictionary') ?></label>
                                    <?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary2', 'form-control', true) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="font-dialog"><?php _e('Number of Students', 'iii-dictionary') ?></label>
                                        <input type="number" name="no-students" class="form-control" min="1" max="1" value="1" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                        <select class="select-box-it form-control" name="self-study-months" id="sel-self-study-months1">
                                            <?php for ($i = 1; $i <= 24; $i++) : ?>
                                                <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                            <?php endfor; ?>
                                        </select>   
                                    </div>
                                </div>
                                <div class="col-sm-12 padding-top-2">
                                    <div class="box-gray-dialog" style="text-align: right">
                                        <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$</span> <span class="currency color708b23" id="ss-total-amount">0</span>
                                    </div>
                                </div>
                            </div>				
                        </div>
                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <button type="submit" id="add-to-cart-ss-math" name="add-to-cart" class="btn-custom"><?php _e('Check out', 'iii-dictionary') ?></button>
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
        <?php
    }

    /*
     * generate a digit box for math homework
     *
     * @param string $digits
     * @param string $sign
     * @param int $empty_box
     */

    public static function math_digit_box($digits, $sign = null, $empty_box = 0, $stt = 0, $assign_id = 0) {
        $digits = str_split($digits);
        ?>

        <?php if ($stt == 8 && $assign_id == 8) { ?>
            <span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" ><?php echo 'Multiplicant' ?></span>
        <?php } else if ($stt == 8) { ?>
            <span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" ><?php echo 'Multiplier' ?></span>
        <?php } ?>
        <?php if ($stt == 7 && $assign_id == 7) { ?>
            <span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" ><?php echo 'Number 1' ?></span>
        <?php } else if ($stt == 7) { ?>
            <span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" ><?php echo 'Number 2' ?></span>
        <?php } ?>
        <span class="formula-steps">
            <?php if (!empty($sign)) : ?>
                <span class="math-number sign"><?php echo $sign ?></span>
                <?php
                if ($empty_box > 0) :
                    for ($i = 1; $i <= $empty_box; $i++) :
                        ?>
                        <span class="math-number empty">&nbsp;</span>
                        <?php
                    endfor;
                endif
                ?>
            <?php endif ?>
            <?php foreach ($digits as $d) : ?>
                <span class="math-number"><?php echo $d ?></span>
            <?php endforeach ?>
        </span>
        <?php
    }

    /*
     * generate a digit box for division math homework
     *
     * @param string $dividend
     * @param string $divisor
     */

    public static function math_digit_box_division($dividend, $divisor) {
        $dividend = str_split($dividend);
        $divisor = str_split($divisor);
        ?>
        <span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" ><?php echo 'Divisor / Dividend' ?></span>
        <span class="formula-steps">
            <?php foreach ($divisor as $d) : ?>
                <span class="math-number"><?php echo $d ?></span>
            <?php endforeach ?>
            <span class="math-number empty division-line">&nbsp;</span>
            <?php foreach ($dividend as $d) : ?>
                <span class="math-number dividend"><?php echo $d ?></span>
            <?php endforeach ?>
        </span>
        <?php
    }

    /*
     * gererate an answer box for math homework
     *
     * @param string $digits
     * @param int $step_number
     */

    public static function math_answer_box($digits, $step_number, $input_name_prefix = '', $assign_id = 0, $sign = null) {

        $digits = trim($digits);

        if ($digits !== '') {
            $digits = str_split($digits);
            ?>                      
            <span class="formula-steps" id="answer-step-<?php echo $step_number ?>">
                <span style=" float:left;font-weight: bold;font-size: 20pt;padding-left: 10%;" ><?php
                    if ($assign_id == 8) {
                        if ($step_number == 1) {
                            echo 'Partical Sum';
                        } else if ($step_number == 2) {
                            echo 'Carry';
                        } else if ($step_number == 3) {
                            echo 'Partical Sum';
                        } else if ($step_number == 4) {
                            echo 'Carry';
                        } else if ($step_number == 5) {
                            echo 'Partical Sum';
                        } else if ($step_number == 6) {
                            echo 'Carry';
                        } else if ($step_number == 7) {
                            echo 'Answer';
                        }
                    }
                    if ($assign_id == 9) {
                        if ($step_number == 1) {
                            echo '';
                        } else if ($step_number == 2) {
                            echo '';
                        } else if ($step_number == 3) {
                            echo '';
                        } else if ($step_number == 4) {
                            echo 'Steps';
                        } else if ($step_number == 5) {
                            echo '';
                        } else if ($step_number == 6) {
                            echo '';
                        } else if ($step_number == 7) {
                            echo '';
                        } else if ($step_number == 8) {
                            echo '';
                        } else if ($step_number == 9) {
                            echo 'Remainder';
                        } else if ($step_number == 10) {
                            echo 'Answer';
                        }
                    }
                    if ($assign_id == 10) {
                        if ($step_number == 1) {
                            echo '';
                        } else if ($step_number == 2) {
                            echo '';
                        } else if ($step_number == 3) {
                            echo 'Steps';
                        } else if ($step_number == 4) {
                            echo '';
                        } else if ($step_number == 5) {
                            echo '';
                        } else if ($step_number == 6) {
                            echo '';
                        } else if ($step_number == 7) {
                            echo 'Remainder';
                        } else if ($step_number == 8) {
                            echo 'Answer';
                        }
                    }
                    if ($assign_id == 7) {
                        if ($step_number == 1) {
                            echo 'Partial Sum';
                        } else if ($step_number == 2) {
                            if ($sign != null) {
                                echo 'Borrow';
                            } else {
                                echo 'Carry';
                            }
                        } else if ($step_number == 3) {
                            echo 'Answer';
                        }
                    }
                    ?></span>
                <?php foreach ($digits as $d) : ?>
                    <?php if ($d === '@') : ?>
                        <span class="math-number empty">&nbsp;</span>
                    <?php else : ?>
                        <span class="math-number input-box"><input type="text" class="s1" maxlength="1" autocomplete="off" data-answer="<?php echo $d ?>"<?php echo $input_name_prefix != '' ? ' name="' . $input_name_prefix . '['.$d.']"' : '' ?>></span>
                    <?php endif ?>
                <?php endforeach; ?>
            </span>
            <?php
        }
    }

    /*
     * return math problem image url
     *
     * @param string $file_name
     *
     * @return string
     */

    public static function math_image_url($file_name) {
        //$server_url = 'http://mwd.s3.amazonaws.com/mathimages/' . get_short_lang_code() . '/';
        $server_url = site_url() . '/media/mathimages/' . get_short_lang_code() . '/';
        return $server_url . $file_name;
    }

    /*
     * return math problem sound url
     *
     * @param string $file_name
     *
     * @return string
     */

    public static function math_sound_url($file_name) {
        $server_url = 'https://mwd.s3.amazonaws.com/mathsounds/' . get_short_lang_code() . '/';

        return $server_url . $file_name;
    }

    /*
     * return math problem sound url
     *
     * @param string $file_name
     *
     * @return string
     */

    public static function math_video_url($file_name) {
        $server_url = 'https://mwd.s3.amazonaws.com/mathvideo/' . get_short_lang_code() . '/';

        return $server_url . $file_name;
    }

    /*
     * return homework practice url
     */

    public static function get_practice_page_url($assignment_id) {
        $baseurl = site_home_url();
//echo $assignment_id;
        switch ($assignment_id) {
            case ASSIGNMENT_SPELLING: $page = 'spelling-practice';
                break;
            case ASSIGNMENT_VOCAB_GRAMMAR: $page = 'vocabulary-practice';
                break;
            case ASSIGNMENT_READING: $page = 'reading-comprehension';
                break;
            case ASSIGNMENT_WRITING: $page = 'writing-practice';
                break;
            case ASSIGNMENT_REPORT: $page = 'writing-report';
                break;
            case MATH_ASSIGNMENT_SINGLE_DIGIT:
            case MATH_ASSIGNMENT_TWO_DIGIT_MUL:
            case MATH_ASSIGNMENT_SINGLE_DIGIT_DIV:
            case MATH_ASSIGNMENT_TWO_DIGIT_DIV:
            case MATH_ASSIGNMENT_FLASHCARD:
            case MATH_ASSIGNMENT_FRACTION:
            case MATH_ASSIGNMENT_WORD_PROB:
            case MATH_ASSIGNMENT_QUESTION_BOX:
            case MATH_ASSIGNMENT_EQUATION:
                $baseurl = site_math_url();
                $page = 'arithmetics#modal-working-worksheet';
                break;
        }

        return $baseurl . '/?r=' . $page;
    }

    /*
     * Generate dictionaries select box
     */

    public static function select_dictionaries($selected = '', $subscription = false, $name = 'dictionary', $id = 'sel-dictionary', $class = '', $all = false) {
        global $wpdb;

        $class = $class != '' ? ' ' . $class : $class;

        $dictionaries = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'dict_dictionaries');
        ?>
        <select name="<?php echo $name ?>" class="select-box-it<?php echo $class ?>" id="<?php echo $id ?>">
            <option value="0"><?php _e('Select a dictionary', 'iii-dictionary') ?></option>			
            <?php for ($i = 0; $i < 5; $i++) : ?>
                <option value="<?php echo $dictionaries[$i]->id ?>"<?php echo $selected == $dictionaries[$i]->id ? ' selected' : '' ?>><?php echo $dictionaries[$i]->name ?></option>
            <?php endfor ?>
            <?php if ($subscription) : ?>
                <option value="0"<?php echo '0' == $selected ? ' selected' : '' ?>><?php _e('My Choice', 'iii-dictionary') ?></option>
            <?php endif ?>
        </select>
        <?php
    }

    /*
     * Generate select box number of students for subscription
     */

    public static function select_num_of_students_subscription($selected = '', $name = 'num-of-students', $id = 'sel-num-students') {
        ?>
        <select name="<?php echo $name ?>" id="<?php echo $id ?>" class="select-box-it">
            <option value=""><?php _e('Select one', 'iii-dictionary') ?></option>
            <?php for ($i = 1; $i <= 9; $i++) : $n = $i * STUDENT_MULTIPLIER ?>
                <option value="<?php echo $n ?>"<?php echo $selected == $n ? ' selected' : '' ?>><?php printf(__('%s students/licenses', 'iii-dictionary'), $n) ?></option>
            <?php endfor ?>
        </select>
        <?php
    }

    /*
     * Generate select box number of months for subscription
     */

    public static function select_num_of_months_dict_subscription($selected = '', $name = 'dict-num-of-months', $id = 'sel-dict-months') {
        ?>
        <select name="<?php echo $name ?>" id="<?php echo $id ?>" class="select-box-it">
            <option value=""><?php _e('Select one', 'iii-dictionary') ?></option>
            <option value="1"<?php echo '1' == $selected ? ' selected' : '' ?>>1 month</option>
            <option value="3"<?php echo '3' == $selected ? ' selected' : '' ?>>3 month</option>
            <?php for ($i = 1; $i <= 4; $i++) : $n = $i * DICTIONARY_MONTHS_MULTIPLIER ?>
                <option value="<?php echo $n ?>"<?php echo $selected == $n ? ' selected' : '' ?>><?php printf(__('%s months', 'iii-dictionary'), $n) ?></option>
            <?php endfor; ?>
        </select>
        <?php
    }

    /*
     * Generate select box number of months for teacher's tool subscription
     */

    public static function select_num_of_months_teacher_subscription($selected = '', $name = 'teacher-tool-months', $id = 'sel-teacher-tool', $month_only = false) {
        ?>
        <select class="select-box-it form-control" id="<?php echo $id ?>" name="<?php echo $name ?>">
            <option value=""><?php _e('Select one', 'iii-dictionary') ?></option>
            <option value="1"<?php echo '1' == $selected ? ' selected' : '' ?>>1 month</option>
            <?php for ($m = 2; $m <= 8; $m++) : ?>
                <option value="<?php echo $m ?>"<?php echo $m == $selected ? ' selected' : '' ?>><?php printf(__('%s months', 'iii-dictionary'), $m) ?></option>
            <?php endfor; ?>
            <option value="12"<?php echo '12' == $selected ? ' selected' : '' ?>>1 year</option>
        </select>
        <?php
    }

    /*
     * Generate select box credit code type
     */

    public static function select_credit_code_type($selected = '', $name = 'filter[type]', $id = '') {
        global $wpdb;

        $types = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'dict_subscription_type');
        ?>
        <select name="<?php echo $name ?>" class="select-box-it select-sapphire form-control">
            <option value=""><?php _e('--Type--', 'iii-dictionary') ?></option>
            <?php foreach ($types as $type) : ?>
                <?php if ($type->id != 4) : ?>
                    <option value="<?php echo $type->id ?>"<?php echo $selected == $type->id ? ' selected' : '' ?>><?php echo $type->name ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <?php
    }

    /*
     * output Credit Cards select box
     */

    public static function credit_cards($selected = '') {
        global $wpdb;

        $cards = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'dict_credit_cards');
        ?>
        <select name="credit-cards" class="select-box-it form-control" id="select-credit-card">
            <option value=""><?php _e('Select one', 'iii-dictionary') ?></option>
            <?php foreach ($cards as $card) : ?>
                <option value="<?php echo $card->id ?>"<?php echo $selected == $card->id ? ' selected' : '' ?>><?php echo $card->name ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    /*
     * output User's Credit Cards select box
     */

    public static function user_credit_cards($selected = '') {
        global $wpdb;

        $cards = $wpdb->get_results('SELECT uc.*, c.name FROM ' . $wpdb->prefix . 'dict_user_credit_cards AS uc
									 JOIN ' . $wpdb->prefix . 'dict_credit_cards AS c ON c.id = uc.card_type_id
									 WHERE exp_date > NOW() AND user_id = ' . get_current_user_id());
        ?>
        <select name="user-credit-cards" class="select-box-it form-control">
            <option value=""><?php _e('Select one', 'iii-dictionary') ?></option>
            <?php foreach ($cards as $card) : ?>
                <option value="<?php echo $card->id ?>"<?php echo $selected == $card->id ? ' selected' : '' ?>><?php echo $card->name . ' &mdash; ' . $card->display_card_number ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    /*
     * menu language switcher
     *
     */

    public static function sel_lang_switcher($x = 0) {
        $langs = array(
            'en' => 'English',
            'ja' => '日本語',
            'ko' => '한국어',
            'vi' => 'Tiếng Việt',
            'zh' => '中文',
            'zh-tw' => '中國'
        );

        $cur_lang = get_short_lang_code();
        ?>
        <div id="lang-switcher-block" class="hidden-xs">
            <select class="select-box-it select-sapphire" id="lang-switcher">
                <?php foreach ($langs as $code => $lang) : ?>
                    <?php if ($x == 1) { ?>
                        <option value="<?php
                        echo is_math_panel() ? str_replace('://', '://math.', site_url()) : site_url();
                        echo '/' . $code;
                        ?>"<?php echo $cur_lang == $code ? ' selected' : '' ?>><?php echo $lang ?></option>
                            <?php } elseif ($x == 2) {
                                ?>
                        <option value="<?php
                        echo is_math_panel() ? str_replace('://', '://math.', site_url()) : site_url();
                        echo '/' . $code . '/?r=ensat'
                        ?>"<?php echo $cur_lang == $code ? ' selected' : '' ?>><?php echo $lang ?></option>
                            <?php } else { ?>
                        <option value="<?php
                        echo is_math_panel() ? str_replace('://', '://math.', site_url()) : site_url();
                        echo '/' . $code . '/home'
                        ?>"<?php echo $cur_lang == $code ? ' selected' : '' ?>><?php echo $lang ?></option>
                            <?php } endforeach; ?>
            </select>
        </div>
        <?php
    }

    /*
     * language type
     *
     */

    public static function language_type($select) {
        $langs = array(
            'en' => 'English',
            'ja' => '日本語',
            'ko' => '한국어',
            'vi' => 'Tiếng Việt',
            'zh' => '中文',
            'zh-tw' => '中國'
        );

        //$cur_lang = get_short_lang_code();
        ?>
        <select name="language_type" class="form-control language_type select-box-it">
            <?php foreach ($langs as $code => $lang) : ?>
                <option value="<?php echo $code; ?>"<?php echo $select == $code ? ' selected' : '' ?>><?php echo $lang ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    /*
     * subscribe dictionary popup
     *
     * @param mixed $dictionary
     *
     */

    public static function subscribe_dictionary_popup($dictionary, $search_count = false, $number_of_times = 0, $is_dictionary_subscribed = false) {
        $blocked = false;
        if ($search_count && $_SESSION['remind_count'][$dictionary] >= $number_of_times) {
            $blocked = true;
        }
        ?>
        <div id="subscribe-modal-dialog" class="modal fade modal-red-brown subscribe-modal" aria-hidden="true"<?php echo $blocked ? ' data-backdrop="static"' : '' ?>>
            <div class="modal-dialog">
                <div class="modal-content" style="background: #fff; color: #000;">
                    <div class="modal-header" style="background: #848484">           
                        <h3 style="color: #fff"><?php _e('Please subscribe to continue', 'iii-dictionary') ?>
                            <span style="margin-top: 1%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog close-submodal"></span>                   
                        </h3>
                    </div>
                    <div class="modal-body" style="font-size: 16px;">
                        <small style="font-weight: 900;font-size: 115%"><?php printf(__('You have tried %s words. How did you like it!', 'iii-dictionary'), $number_of_times) ?></small>
                        <div class="hr-line1"></div>
                        <?php
                        switch ($dictionary) :

                            case 'elearner':
                            case 1:
                                ?>

                                <div class="cover-thumb">
                                    <img src="<?php echo get_template_directory_uri(); ?>/library/images/learner-cover-small.png" alt="">
                                </div>
                                <div class="popup-content-header">
                                    <h3 style="color: #75882A"><?php _e('Why Subscribe?', 'iii-dictionary') ?></h3>
                                    <ul class="ul-bullet-style7">					
                                        <li><?php _e('You can look up five words in the dictionary, then every additional word searched will bring up this pop-up untill you subscribe.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Get the latest updates; additional features', 'iii-dictionary') ?></li>
                                    </ul>
                                </div>
                                <div class="popup-content-body">
                                    <h3 style="color: #75882A"><?php _e('Features', 'iii-dictionary') ?></h3>
                                    <ul class="ul-bullet-icon-yes2">
                                        <li><?php _e('More than <span class="semi-bold">22,000</span> idioms, verbal collocations, and commonly used phrases from American and British English.', 'iii-dictionary') ?></li>
                                        <li><?php _e('More than <span class="semi-bold">160,000</span> example sentences the most of any learner\'s dictionary.', 'iii-dictionary') ?></li>
                                        <li><?php _e('<span class="semi-bold">100,000</span> words and phrases with <span class="semi-bold">3,000</span> core vocabulary words identified.', 'iii-dictionary') ?></li>
                                        <li><?php _e('More than <span class="semi-bold">12,000</span> usage labels, notes, and paragraphs.', 'iii-dictionary') ?></li>
                                        <li><?php _e('<span class="semi-bold">33,000</span> IPA pronunciations.', 'iii-dictionary') ?></li>
                                    </ul>
                                </div>

                                <?php
                                break;

                            case 'collegiate':
                            case 2:
                                ?>

                                <div class="cover-thumb">
                                    <img src="<?php echo get_template_directory_uri(); ?>/library/images/collegiate-cover-small.png" alt="">
                                </div>
                                <div class="popup-content-header">
                                    <h3 style="color: #75882A"><?php _e('Why Subscribe?', 'iii-dictionary') ?></h3>
                                    <ul class="ul-bullet-style7">					
                                        <li><?php _e('You can look up five words in the dictionary, then every additional word searched will bring up this pop-up untill you subscribe.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Get the latest updates; additional features', 'iii-dictionary') ?></li>
                                    </ul>
                                </div>
                                <div class="popup-content-body">
                                    <h3 style="color: #75882A"><?php _e('Features', 'iii-dictionary') ?></h3>
                                    <ul class="ul-bullet-icon-yes2">
                                        <li><?php _e('Over <span class="semi-bold">275,000</span> Synonyms &amp; related words.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Over <span class="semi-bold">115,000</span> Audio pronunciations.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Over <span class="semi-bold">225,000</span> Definitions.', 'iii-dictionary') ?></li>
                                        <br><li><?php _e('Over <span class="semi-bold">700</span> Illustrations.', 'iii-dictionary') ?></li>
                                        <br><li><?php _e('Deluxe audio edition.', 'iii-dictionary') ?></li>
                                        <br><li><?php _e('Faster search with high-performance database engine.', 'iii-dictionary') ?></li>
                                    </ul>
                                </div>
                                <a href="#dic-donwload" id="click-dic-donwload" data-toggle="modal" style="display: none;"></a>
                                <?php
                                break;

                            case 'medical':
                            case 3:
                                ?>

                                <div class="cover-thumb">
                                    <img src="<?php echo get_template_directory_uri(); ?>/library/images/medical-cover-small.png" alt="">
                                </div>
                                <div class="popup-content-header">
                                    <h3 style="color: #75882A"><?php _e('Why Subscribe?', 'iii-dictionary') ?></h3>
                                    <ul class="ul-bullet-style7">					
                                        <li><?php _e('You can look up five words in the dictionary, then every additional word searched will bring up this pop-up untill you subscribe.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Get the latest updates; additional features', 'iii-dictionary') ?></li>
                                    </ul>
                                </div>
                                <div class="popup-content-body">
                                    <h3 style="color: #75882A"><?php _e('Features', 'iii-dictionary') ?></h3>
                                    <ul class="ul-bullet-icon-yes2">
                                        <li><?php _e('Over <span class="semi-bold">59,000</span> entries explain today\'s most widely used health-care terms.', 'iii-dictionary') ?></li>
                                        <li><?php _e('More than <span class="semi-bold">8,000</span> example phrases show how words are used in context.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Affordable quick reference.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Accessible guide to medical language.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Faster search with high-performance database engine.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Unmatched quality from the reference experts at Merriam-Webster.', 'iii-dictionary') ?></li>
                                    </ul>
                                </div>

                                <?php
                                break;

                            case 'intermediate':
                            case 4:
                                ?>

                                <div class="cover-thumb">
                                    <img src="<?php echo get_template_directory_uri(); ?>/library/images/intermediate-cover-small.png" alt="">
                                </div>
                                <div class="popup-content-header">
                                    <h3 style="color: #75882A"><?php _e('Why Subscribe?', 'iii-dictionary') ?></h3>
                                    <ul class="ul-bullet-style7">					
                                        <li><?php _e('You can look up five words in the dictionary, then every additional word searched will bring up this pop-up untill you subscribe.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Get the latest updates; additional features', 'iii-dictionary') ?></li>
                                    </ul>
                                </div>
                                <div class="popup-content-body">
                                    <h3 style="color: #75882A"><?php _e('Features', 'iii-dictionary') ?></h3>
                                    <ul class="ul-bullet-icon-yes2">
                                        <li><?php _e('Nearly <span class="semi-bold">70,000</span> entries including new words and definitions from the fields of science, technology, entertainment, and health.', 'iii-dictionary') ?></li>
                                        <li><?php _e('More than <span class="semi-bold">22,000</span> usage examples.', 'iii-dictionary') ?></li>
                                        <li><?php _e('More than <span class="semi-bold">1,000</span> illustrations.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Abundant word history paragraphs and synonym paragraphs.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Written especially for the needs of students grades <span class="semi-bold">6-8</span>, ages <span class="semi-bold">11-14</span>', 'iii-dictionary') ?></li>
                                    </ul>
                                </div>

                                <?php
                                break;

                            case 'elementary':
                            case 5:
                                ?>

                                <div class="cover-thumb">
                                    <img src="<?php echo get_template_directory_uri(); ?>/library/images/elementary-cover-small.png" alt="">
                                </div>
                                <div class="popup-content-header">
                                    <h3 style="color: #75882A"><?php _e('Why Subscribe?', 'iii-dictionary') ?></h3>
                                    <ul class="ul-bullet-style7">					
                                        <li><?php _e('You can look up five words in the dictionary, then every additional word searched will bring up this pop-up untill you subscribe.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Get the latest updates; additional features', 'iii-dictionary') ?></li>
                                    </ul>
                                </div>
                                <div class="popup-content-body">
                                    <h3 style="color: #75882A"><?php _e('Features', 'iii-dictionary') ?></h3>
                                    <ul class="ul-bullet-icon-yes2">
                                        <li><?php _e('Over <span class="semi-bold">36,000</span> fully revised entries.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Expanded usage examples include more than <span class="semi-bold">1,300</span> quotes from classic and contemporary children\'s literature.', 'iii-dictionary') ?></li>
                                        <li><?php _e('<span class="semi-bold">250</span> word history paragraphs and over <span class="semi-bold">130</span> synonym paragraphs.', 'iii-dictionary') ?></li>
                                        <li><?php _e('More than <span class="semi-bold">900</span> new, colorful illustrations, photographs, and diagrams.', 'iii-dictionary') ?></li>
                                        <li><?php _e('Essential dictionary for children grades <span class="semi-bold">3-5</span>, ages <span class="semi-bold">8-11</span>', 'iii-dictionary') ?></li>
                                    </ul>
                                </div>

                                <?php
                                break;

                        endswitch
                        ?>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <a href="#" class="btn-custom confirm sub-now"></span><?php _e('Yes, Subscribe Now', 'iii-dictionary') ?></a>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <!--                                if ($blocked) : 
                                                                    <a href="<?php echo locale_home_url() ?>" class="btn btn-block grey confirm"><span class="icon-goto"></span><?php _e('Homepage', 'iii-dictionary') ?></a>
                                                                else : 
                                                                    <a href="#" data-dismiss="modal" class="btn btn-block grey confirm"><span class="icon-cancel"></span><?php _e('Not now', 'iii-dictionary') ?></a>
                                                                endif -->
                                <div class="form-group">
                                    <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary close-submodal"><?php _e('Cancel', 'iii-dictionary') ?></a>
                                </div>    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /*
     * display messages in $_SESSION['message'] using modal
     */

    public static function ik_site_messages() {
        $messages = ik_get_message_queue();
    //link download of apps with system of user.
	$link_url = ik_link_apps();
	$is_math_panel = is_math_panel();
	
	if(isset($_GET['action'])) {
		$action = $_GET['action'];
	}
	else {
		$action = 'login';
		$page_header_title = __('Login', 'iii-dictionary');
	}
        switch($action)
	{
		case 'login':

			$page_title_tag = __('Login', 'iii-dictionary');

			if(isset($_POST['wp-submit']))
			{
				$creds['user_login'] = $_POST['log'];
				$creds['user_password'] = $_POST['pwd'];
				//$creds['remember'] = true;
				$user = wp_signon($creds, false);

				if(is_wp_error($user))
				{
					ik_enqueue_messages(__('Please check your Login Email address or Password and try it again.', 'iii-dictionary'), 'error');

					if(!isset($_SESSION['login_tries'])) {
						$_SESSION['login_tries'] = 1;
					} else {
						$_SESSION['login_tries'] += 1;

						if($_SESSION['login_tries'] >= 3) {
							ik_enqueue_messages(__('Did you forget your password? Please try "Forgot Password"', 'iii-dictionary'), 'message');
						}
					}					
				}
				else {
					$user_id = wp_get_current_user();
					if(!$user_id->language_type){
						update_user_meta( $user_id->ID, 'language_type','en');	
					} 
					$_SESSION['notice-dialog'] = 1;
					if(isset($_SESSION['mw_referer'])){
						$segment = explode('/',$_SESSION['mw_referer']);
						if(isset($segment[3]) && $segment[3] == 'wp-content'){
							$_SESSION['mw_referer'] = locale_home_url();
						}
					}
					$_SESSION['mw_referer'] = isset($_SESSION['mw_referer']) ? $_SESSION['mw_referer'] : locale_home_url();

					wp_redirect($_SESSION['mw_referer']);
					exit;
				}
			}

			break;

		case 'forgotpassword' :
		
			$page_header_title = __('Lost Password', 'iii-dictionary');
			$page_title_tag = __('Lost Password', 'iii-dictionary');

			if(isset($_POST['wp-submit']))
			{
				$has_err = false;
				if(empty($_POST['user_login'])) {
					ik_enqueue_messages(__('Please enter a username or e-mail address.', 'iii-dictionary'), 'error');
					$has_err = true;
				}
				else if(is_email($_POST['user_login'])) {
					$user_data = get_user_by('email', trim($_POST['user_login']));
					if(empty($user_data)) {
						ik_enqueue_messages(__('There is no user registered with that email address.', 'iii-dictionary'), 'error');
						$has_err = true;
					}
				}
				else {
					$login = trim($_POST['user_login']);
					$user_data = get_user_by('login', $login);
				}

				if (!$user_data) {
					ik_enqueue_messages(__('Invalid username or e-mail.', 'iii-dictionary'), 'error');
					$has_err = true;
				}

				if(!$has_err)
				{
					// Redefining user_login ensures we return the right case in the email.
					$user_login = $user_data->user_login;
					$user_email = $user_data->user_email;

					// Generate something random for a password reset key.
					$key = wp_generate_password( 20, false );

					// Now insert the key, hashed, into the DB.
					if ( empty( $wp_hasher ) ) {
						require_once ABSPATH . WPINC . '/class-phpass.php';
						$wp_hasher = new PasswordHash( 8, true );
					}
					//$hashed = $wp_hasher->HashPassword( $key );
					$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
					$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );

					$message .= __('<p style="font-size: 14px; font-family: Lucida Console;">We’ve received a request to reset the password for your account: https://iktutor.com</p>', 'iii-dictionary') . " ";
        
                $message .= sprintf(__('<p style="font-size: 14px; font-family: Lucida Console;">Username: %s</p>', 'iii-dictionary'), $user_login) ;
        
                $message .= __('<p style="font-size: 14px; font-family: Lucida Console;">To reset your password, visit the following address:</p>', 'iii-dictionary');
                $message .= '<p style="font-size: 14px; font-family: Lucida Console;">' . network_site_url('?r=login&action=resetpass&key=' . $key . '&login=' . rawurlencode($user_login)).'</p>' ;
                $message .= __('<p style="font-size: 14px; font-family: Lucida Console;">If this was a mistake, just ignore this email and nothing will happen.</p>', 'iii-dictionary');

                $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

                $title = sprintf(__('You have requested the password change', 'iii-dictionary'), $blogname);

					$title = apply_filters( 'retrieve_password_title', $title );

					$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

					if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
						ik_enqueue_messages(__('The e-mail could not be sent.', 'iii-dictionary') . "<br>\n" . __('Possible reason: your host may have disabled the mail() function.', 'iii-dictionary'), 'error');
					}
					else {
						ik_enqueue_messages(__('Please check your e-mail for the confirmation link.', 'iii-dictionary'), 'message');
					}

					wp_redirect(locale_home_url() . '/?r=login');
					exit;
				}
			}
			else {
				if(isset( $_GET['error'])) {
					if('invalidkey' == $_GET['error']) {
						ik_enqueue_messages(__('Sorry, that key does not appear to be valid.', 'iii-dictionary'), 'error');
					}
					else if('expiredkey' == $_GET['error']) {
						ik_enqueue_messages(__('Sorry, that key has expired. Please try again.', 'iii-dictionary'), 'error');
					}
				}
			}

			break;

		case 'resetpass' :

			$page_header_title = __('Reset Password', 'iii-dictionary');
			$page_title_tag = __('Reset Password', 'iii-dictionary');

			if(isset($_GET['key']) && isset($_GET['login'])) {
				$rp_login = esc_html( stripslashes($_GET['login']) );
				$rp_key   = esc_html( $_GET['key'] );
				$user = check_password_reset_key( $rp_key, $rp_login );
			}
			else if(isset($_POST['rp_key']) && isset($_POST['rp_login'])) {
				$rp_login = esc_html( stripslashes($_POST['rp_login']) );
				$rp_key   = esc_html( $_POST['rp_key'] );
				$user = check_password_reset_key( $rp_key, $rp_login );
			}
			else {
				$user = false;
			}

			if(!$user || is_wp_error($user)) {
				if($user && $user->get_error_code() === 'expired_key')
					wp_redirect(site_url( '?r=login&action=forgotpassword&error=expiredkey' ));
				else
					wp_redirect(site_url( '?r=login&action=forgotpassword&error=invalidkey' ));
				exit;
			}

			if(isset($_POST['wp-submit']))
			{
				$has_err = false;
				if(isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2']) {
					ik_enqueue_messages(__('The passwords do not match.', 'iii-dictionary'), 'error');
					$has_err = true;
				}

				if(!$has_err && isset( $_POST['pass1'] ) && !empty( $_POST['pass1'])) {
					reset_password($user, $_POST['pass1']);
					ik_enqueue_messages(__('Your password has been reset.', 'iii-dictionary'), 'success');

					wp_redirect(locale_home_url() . '/?r=login');
					exit;
				}
			}

			break;
	}
//        var_dump($messages);
        $other = !empty($messages) ? ik_get_other_queue($messages) : array();
        if (!empty($messages)) :
            ?>
            <div class="modal fade " id="site-messages-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-custom-first">
                    <div class="modal-content modal-content-custom">
                        <div class="modal-header custom-header">
                            <span style="right: 3%;padding-top: 4% !important;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                            <h3 style="padding-left: 1%"><?php _e('Message', 'iii-dictionary') ?></h3>
                        </div>
                        <div class="modal-body body-custom">
                            <div class="row">
                                <div class="col-xs-12" id="css-modal-site">
                                    <?php foreach ($messages as $msg) : ?>
                                        <div class="<?php echo $msg->type ?>-message">
                                            <?php if (empty($msg->label)) : ?>
                                                <strong><?php echo $msg->msg ?> </strong>
                                                <?php
                                                if (!empty($msg->msg_second)) {
                                                    $str = "You can start/restart at any time from the";
                                                    if (strpos($msg->msg_second, $str) !== false) {
                                                        $msg->msg_second = "You can start/restart at any time from the \"Student's Box\".";
                                                    }
                                                    ?>
                                                    <p style="font-weight: normal;"><?php echo $msg->msg_second ?> </p>
                                                <?php } ?>
                                            <?php else : ?>
                                                <?php echo $msg->msg ?>
                                            <?php endif ?>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($other)) : ?>
                            <?php
                            switch ($other['order']) {
                                case 1 : MWHtml::get_first_assign($other);
                                    break;
                                case 2 : MWHtml::auto_active_code_dic($other);
                                    break;
                            }
                            ?>	
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <!--        modal message thành công-->
            <div class="modal fade " id="modal-success1" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-custom-first">
                    <div class="modal-content modal-content-custom">
                        <div class="modal-header custom-header">
                            <span style="right: 3%;padding-top: 2% !important;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                            <h3 style="padding-left: 0%"><?php _e('Message', 'iii-dictionary') ?></h3>
                        </div>
                        <div class="modal-body body-custom">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="error-message">
                                        <strong>Success</strong>: You can use library.</div>
                                </div>
                            </div>
                        </div>                      
                    </div>
                </div>
            </div>


            <script>(function ($) {
                    $(function () {
                        $('#site-messages-modal').modal({
                            backdrop: 'static',
                            keyboard: false
                        })
                        $("#site-messages-modal").modal("show");
                        $("#apply-credit-code1").click(function (e) {
                            e.preventDefault();
                            $code = $("#apply-credit-code1").attr("data-active");
                            $.post(home_url + "/?r=ajax/validatecredit", {c: $code}, function (data) {
                                if (data != null) {
                                    $("#site-messages-modal").modal("hide");
                                    window.location.href = home_url + "?r=dictionary/elearner";
                                }
                            });
                        });
                        $('#link-modal-tutoring-new').click(function () {
                            window.location.href = home_url + "/?r=tutoring-plan";
                        });
                        $('#ok-next-spelling-practice').click(function () {
                            window.location.href = home_url + "?r=spelling-practice";
                        });
                        $('#ok-close-sub-math').click(function (e) {
                            e.preventDefault();
                            $("#site-messages-modal").modal("hide");
                        });
                    });
                })(jQuery);</script>
            <?php
        endif;
    }

    /*
     * lock a page
     */

    public static function ik_lockpage_dialog() {
        if (!empty($_SESSION['lock-page'])) :
            ?>
            <div class="lockpage-dialog-container">
                <div class="modal-backdrop in"></div>
                <div class="ik-dialog">
                    <div class="ik-dialog-content">
                        <div class="ik-dialog-header">
                            <h3 class="ik-dialog-title"><?php echo $_SESSION['lock-page']['title'] ?></h3>
                        </div>
                        <div class="ik-dialog-body">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <?php echo $_SESSION['lock-page']['body'] ?>
                                </div>
                                <div class="col-sm-3 col-sm-offset-9">
                                    <a href="<?php echo $_SESSION['lock-page']['return_url'] ?>" class="btn btn-default btn-block orange"><?php _e('OK', 'iii-dictionary') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $_SESSION['lock-page'] = null;
        endif;
    }

    /*
     * get all sel assigment.
     * @param : as two function math/english above
     */

    public static function get_sel_assignments($selected = '', $get_form = false, $questions = array(), $first_option = '', $name = 'assignments', $class = '', $id = 'assignments', $vocab_option = true) {
        global $wpdb;

        $types = $wpdb->get_results(
                'SELECT a.id, has.name
			 FROM ' . $wpdb->prefix . 'dict_homework_assignments AS a
			 JOIN ' . $wpdb->prefix . 'dict_homework_assignment_langs AS has ON has.assignment_id = a.id
			 WHERE  lang = \'' . get_short_lang_code() . '\''
        );

        $class = $class == '' ? '' : ' ' . $class;
        $html = '';
        ?>
        <select class="select-box-it<?php echo $class ?>" name="<?php echo $name ?>" id="<?php echo $id ?>">
            <?php if ($first_option != '') : ?>
                <option value=""><?php echo $first_option ?></option>
            <?php endif ?>
            <?php
            foreach ($types as $type) :
                if ($type->id != ASSIGNMENT_VOCAB_BUILDER && $type->id != ASSIGNMENT_REPORT) :
                    ?>
                    <option value="<?php echo $type->id ?>"<?php echo $selected == $type->id ? ' selected' : '' ?>><?php echo $type->name ?></option>
                    <?php
                else :
                    if (!is_admin_panel() && $vocab_option) :
                        ?>
                        <option value="<?php echo $type->id ?>"<?php echo $selected == $type->id ? ' selected' : '' ?>><?php echo $type->name ?></option>
                        <?php
                    endif;
                endif
                ?>
            <?php endforeach ?>
        </select>

        <?php
        return $html;
    }

    /* get expire subscription for home.
     * @param
     * @result
     */

    public static function get_exp_subscription() {
        $current_user_id = get_current_user_id();
        $current_page = max(1, get_query_var('page'));
        $filter = get_page_filter_session();
        if (empty($filter)) {
            $filter['items_per_page'] = 10;
            $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
        } else {
            $filter['offset'] = $filter['items_per_page'] * ($current_page - 1);
        }

        set_page_filter_session($filter);
        $filter['offset'] = 0;
        $filter['items_per_page'] = 99999999;
        $user_subscrip1tions = MWDB::get_user_subscriptions($current_user_id, $filter);
        
        $total_pages = ceil($user_subscriptions->total / $filter['items_per_page']);

        $pagination = paginate_links(array(
            'format' => '?page=%#%',
            'current' => $current_page,
            'total' => $total_pages
        ));
        $cart_items = get_cart_items();
        ?>
        <form id="main-form" method="post" action="">
            <div class="row">
                <div class="col-xs-12">
                    <div class="row" style="font-size: 14px">
                        <div class="col-sm-12">
                            <div id="can-scroll-x" style="max-height: 500px">
                                <table class="table table-striped table-condensed ik-table1 ik-table-break-all text-center table-custom-color text-table-black" id="user-subscriptions">
                                    <thead>
                                        <tr>
                                            <th><?php _e('Type', 'iii-dictionary') ?></th>
                                            <th class="text-color-custom-1"><?php _e('No. of Students', 'iii-dictionary') ?></th>
                                            <th class="text-color-custom-1"><?php _e('No. of Users', 'iii-dictionary') ?></th>
                                            <th class="text-color-custom-1"><?php _e('Sub. End', 'iii-dictionary') ?></th>
                                            <th class="text-color-custom-1"><?php _e('Dictionary', 'iii-dictionary') ?></th>
                                            <th class="text-color-custom-1"><?php _e('Group', 'iii-dictionary') ?></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr><td colspan="8"><?php echo $pagination ?></td></tr>
                                    </tfoot>
                                    <tbody>
                                        <?php if (empty($user_subscriptions->items)) : ?>
                                            <tr><td colspan="8"><?php _e('You haven\'t subscribed yet.', 'iii-dictionary') ?></td></tr>
                                        <?php else : ?>
                                            <?php foreach ($user_subscriptions->items as $code) : ?>
                                                <tr<?php echo $code->expired_on < date('Y-m-d', time()) ? ' class="text-muted"' : '' ?>>
                                                    <td><?php if (!$code->inherit) : ?>
                                                            <?php echo $code->type ?><?php echo in_array($code->typeid, array(SUB_SAT_PREPARATION, SUB_MATH_SAT_I_PREP, SUB_MATH_SAT_II_PREP)) ? ' - ' . $code->sat_class : '' ?>
                                                        <?php else : ?>
                                                            <?php echo $code->type ?>
                                                        <?php endif ?>
                                                    </td>
                                                    <?php
                                                    $date1 = new DateTime();
                                                    $date2 = new DateTime($code->expired_on);
                                                    $interval = $date1->diff($date2);
                                                    $_expire = check_exp_subscription(date_diff($date1, $date2)->format('%R %a'));
                                                    $months_left = $interval->d > 0 ? $interval->m + 1 : $interval->m;
                                                    $checked_out_state = '';
                                                    foreach ($cart_items as $item) {
                                                        if ($item->sub_id == $code->id) {
                                                            $checked_out_state = ' disabled';
                                                        }
                                                    }
                                                    ?>
                                                    <td ><?php echo in_array($code->typeid, array(SUB_TEACHER_TOOL, SUB_TEACHER_TOOL_MATH)) ? $code->number_of_students : 'N/A' ?></td>
                                                    <td ><?php echo in_array($code->typeid, array(SUB_DICTIONARY, SUB_SELF_STUDY, SUB_SELF_STUDY_MATH)) ? $code->number_of_students : 'N/A' ?></td>
                                                    <td <?php echo $_expire ? ' omg_expire' : ''; ?>><span><?php echo ik_date_format($code->expired_on) ?></span></td>
                                                    <td ><?php echo $code->dictionary ?></td>
                                                    <td ><?php echo is_null($code->group_name) ? 'N/A' : $code->group_name; ?></td>
                                                    <td data-subid="<?php echo $code->id ?>" data-type="<?php echo $code->typeid ?>" data-did="<?php echo $code->dictionary_id ?>" data-size="<?php echo $code->number_of_students ?>" data-months="<?php echo $months_left ?>"<?php echo!is_null($code->group_name) ? ' data-group="' . $code->group_name . '"' : '' ?> data-sat-class="<?php echo $code->sat_class ?>" data-sat-class-id="<?php echo $code->sat_class_id ?>">
                                                        <button type="button" class="no-border go-now" ><?php _e('GO NOW', 'iii-dictionary') ?></button>
                                                        <div class="all-button-sub" data-subid="<?php echo $code->id ?>" data-type="<?php echo $code->typeid ?>" data-did="<?php echo $code->dictionary_id ?>" data-size="<?php echo $code->number_of_students ?>" data-months="<?php echo $months_left ?>"<?php echo!is_null($code->group_name) ? ' data-group="' . $code->group_name . '"' : '' ?> data-sat-class="<?php echo $code->sat_class ?>" data-sat-class-id="<?php echo $code->sat_class_id ?>">
                                                            <?php if (!$code->inherit) : ?>
                                                                <?php if (in_array($code->typeid, array(SUB_TEACHER_TOOL_MATH, SUB_TEACHER_TOOL))) : ?>
                                                                    <div class="border-bot"><button type="button" class="no-border-menu extend-sub-btn" data-task="add"<?php echo $checked_out_state ?>><?php _e('Add Members', 'iii-dictionary') ?></button></div>
                                                                <?php endif ?>
                                                                <div class="border-bot" data-subid="<?php echo $code->id ?>" data-type="<?php echo $code->typeid ?>" data-did="<?php echo $code->dictionary_id ?>" data-size="<?php echo $code->number_of_students ?>" data-months="<?php echo $months_left ?>"<?php echo!is_null($code->group_name) ? ' data-group="' . $code->group_name . '"' : '' ?> data-sat-class="<?php echo $code->sat_class ?>" data-sat-class-id="<?php echo $code->sat_class_id ?>"><button type="button" class="no-border-menu extend-sub-btn" <?php echo $checked_out_state ?>><?php _e('Renew Subscription', 'iii-dictionary') ?></button></div>
                                                                <div class="pad5050"><a href="#" data-subid="<?php echo $code->id ?>" class="no-border-menu view-subscription" data-toggle="modal"><?php _e('Detail', 'iii-dictionary') ?></a></div>
                                                            <?php endif ?>
                                                        </div>
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
        </form>
        <?php
    }

    /* get sub & mes to notice for user
     * @param : null
     * @result : structure html 
     */

    public static function notice_sub_mes() {
        $id_msg = MWDB::get_id_display_at_login();
        $waiting = MWDB::get_list_waitting_tutoring();
        $confirmed = MWDB::get_list_confirmed_tutoring();
        $arr_yesterday = [];
        
    foreach ($waiting as $value) {
//        var_dump($value);die;
        $_date = $value->date;
        $_time = $value->time;
        $_time = explode("~", $_time); // 1:1:AM
        $_st = explode(":", $_time[1]); 
        $_st_hour = $_st[0];
        $_st_minute = $_st[1];
        $_st_part = $_st[2];
//        if(trim($_st_part) == "PM"){
//            $_st_hour = (int)$_st_hour + 12;
//        }
        $newDate = date("d-m-Y", strtotime($_date));
        $newDate = $newDate ." ".$_st_hour.":".$_st_minute;
        $time_zone = (int)$value->time_zone;
        $data = (int)strtotime($newDate);
        $b = (int)strtotime('now +'.$time_zone .'hour');
        $c = (int)strtotime('+1day +'.$time_zone.'hour');
        $d = date("d-m-Y H:i", $b);
        $now = (int)strtotime($d);
//        var_dump($newDate."__");
//        var_dump($now."__");
//        var_dump($c);die;
        if($data >  $now && $data < $c){
//            var_dump(1);die;
            $id = $value->id;
            array_push($arr_yesterday,$id);
        }
    }
    
    foreach ($confirmed as $value) {
//        var_dump($value);die;
        // Xu ly date
        $_date_cf = $value->date;
        $_time_cf = $value->time;
        $_time_cf = explode("~", $_time_cf); // 1:1:AM
        $_st_cf = explode(":", $_time_cf[1]); 
        $_st_hour_cf = $_st_cf[0];
//        var_dump($_tupdate_total_pointime_cf);die;
        $_st_minute_cf = $_st_cf[1];
        $_st_part_cf = $_st_cf[2];
//        if(trim($_st_part_cf) == "PM"){
//            $_st_hour_cf = (int)$_st_hour_cf + 12;
//        }
        $time_zone1 = (int)$value->time_zone;
        $newDate_cf = date("d-m-Y", strtotime($_date_cf));
        $newDate_cf = $newDate_cf ." ".$_st_hour_cf.":".$_st_minute_cf;
        $data_cf = (int)strtotime($newDate_cf);
        $b_cf = (int)strtotime('now +'.$time_zone1 .'hour');
        $c_cf = (int)strtotime('+1day +'.$time_zone1.'hour');
        $d_cf = date("d-m-Y H:i", $b_cf);
        $now_cf = (int)strtotime($d_cf);
//        var_dump($data_cf);
//        var_dump("___".$data_cf);
//        var_dump("___".$newDate_cf);die;
        if($data_cf >  $now_cf && $data_cf < $c_cf){
//            var_dump(1);die;
            $id_cf = $value->id;
            array_push($arr_yesterday,$id_cf);
        }
// 
    }
        ?>
    <?php if(isset($_SESSION['notice-dialog']) && $_SESSION['notice-dialog'] == 1 && is_user_logged_in() ) : ?>
    <?php // if(is_user_logged_in() ) : ?>
        <div id="modal-reminder-schedule" class="modal fade">
            <div class="modal-dialog ">
                <div class="modal-content boder-black">
                    <span style="right: 3%;padding-top: 2%;margin-top: 10px;" href="#" data-dismiss="modal" aria-hidden="true" class="close icon-close3" ></span>
                    <div class="modal-body" style="color: #000;" id="body-modal-reminder">
                        <div>You have Scheduled Tutoring coming up soon.</div>
                    </div>
                    <div id="content-reminder-modal" style="width: 88%; margin: 0 auto;">
                        <!--load data ajax-->
                    </div>
                    <div class="modal-footer footer-custom css-modal-reminder">
                        <div class="cancel-schedule txt-request-day txt-message-error"></div>
                        <div class="css-btn-go-to"><a href="<?php echo home_url().'?r=online-learning&ikmath-plan'?>" id="btn-go-to-ikmath" style="color: #fecb3f">Go to Tutoring Plan Page</a></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif;?>
        <?php if (!empty($id_msg) && is_user_logged_in()) : ?>
            <div id="display-at-login-dialog" class="modal fade modal-red-brown" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <a href="#" data-dismiss="modal" aria-hidden="true" class="close"></a>
                            <h3><?php _e('Messages from Support', 'iii-dictionary') ?></h3>
                        </div>
                        <div class="modal-body ">
                            <?php
                            $msg = MWDB::get_received_private_message($id_msg);
                            echo $msg->message;
                            ?>
                        </div>
                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-sm-6 col-sm-offset-6">
                                    <button type="button" id="submit-display-at-login" class="btn btn-block orange confirm"><span class="icon-check"></span><?php _e('OK', 'iii-dictionary') ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>
        <?php
        if (is_home_page() && isset($_SESSION['notice-dialog']) && $_SESSION['notice-dialog'] == 1 && is_user_logged_in()) :
            $teacher_tool_price = mw_get_option('teacher-tool-price');
            $self_study_price = mw_get_option('self-study-price');
            $self_study_price_math = mw_get_option('math-self-study-price');
            $dictionary_price = mw_get_option('dictionary-price');
            $is_math_panel = is_math_panel();
            ?>
            <!--            <div id="current-subscription-dialog" class="modal fade modal-join-group " aria-hidden="true" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog">
                                <div class="modal-content modal-content-custom" style="margin-top: 20% !important;">
                                    <div class="modal-header omg_home-sub-header custom-header">
                                        <span style="right: 3%;padding-top: 3%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                                        <h3 style="padding-left: 0px !important;"><?php //_e('SUBSCRIPTION ATTENTION', 'iii-dictionary') ?></h3>
                                    </div>
                                    <div class="modal-body body-custom">
                                        <div class="col-xs-2" id="icon-sub-dialog"></div>
                                        <div class="col-xs-10 margin-bottom-7">
                                            <div><h3>One or more subscription will be expire soon </h3></div>
                                            <div><p style="font-size: 14px">Some of your subscription will be expire within 10 day. Would you like to extend the subscription </p></div>
                                        </div>
                                        <div><?php //MWHtml::get_exp_subscription(); ?></div>
                                    </div>

                                </div>
                            </div>
                        </div>-->

            <div id="message-from-support-dialog" class="modal fade modal-red-brown" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body omg_message-support">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <h2><?php _e('Respone from ikLearn', 'iii-dictionary') ?></h2>
                                        </div>
                                        <div class="col-sm-6">
                                            <label><?php printf(__('You have a message from Support. %s Go to "Private Messages" under "My Account"'), '<br />') ?></label>
                                        </div>
                                        <div class="col-sm-2">
                                            <a href="<?php echo home_url() ?>/?r=private-messages">Check Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div id="modal-detail-sub" class="modal fade in" style="position: absolute;bottom: initial">
                <div class="modal-dialog modal-custom-first" style="margin-top: 100px;">
                    <div class="modal-content boder-black">
                        <div class="modal-header custom-header">
                            <span style="right: 3%;padding-top: 3%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                            <h3 style="padding-left: 0px !important;"><?php _e('Subscription Detail', 'iii-dictionary') ?></h3>
                        </div>
                        <div id="sub-content-detail">                            
                        </div>

                    </div>
                </div>
            </div>

            <!-- renew subscription -->
            <div id="additional-subscription-dialog" class="modal fade">
                <div class="modal-dialog modal-custom-first">
                    <div class="modal-content boder-black">
                        <div class="modal-header custom-header">
                            <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog close-renewsub"></span>
                            <h3 id="addi-popup-title" data-ts-text="<?php _e('Teacher\'s Homework Tool Subscription', 'iii-dictionary') ?>" data-ds-text="<?php _e('Dictionary Subscription', 'iii-dictionary') ?>" data-ext-text="<?php _e('Purchase Additional Subscriptions', 'iii-dictionary') ?>"><?php _e('Purchase Additional Subscriptions (Renewal)', 'iii-dictionary') ?></h3>
                        </div>
                        <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                            <input type="hidden" id="addi-sub-type" name="sub-type" value="">
                            <input type="hidden" id="addi-gid" name="assoc-group" value="">
                            <input type="hidden" id="addi-gname" name="group-name" value="">
                            <input type="hidden" id="addi-gpass" name="group-pass" value="">
                            <input type="hidden" id="sub-id" name="sub-id" value="0">
                            <div class="modal-body">
                                <div class="hidden">
                                    <?php MWHtml::select_dictionaries('', false, 'dictionary', 'sel-dictionary', 'form-control', true) ?>
                                </div>
                                <div class="row">
                                    <h3 id="type-group" style="color: black;margin-bottom: 4%"></h3>
                                    <div style="    border-bottom: 1px solid #ccc;margin-bottom: 4%"></div>
                                    <div class="div-span-1"><span class="span-purchase-red"></span><p class="p-purchase italic color4c4c4c" id="name-group"></p></div>
                                    <div id="lib-class"><span class="span-purchase-red"></span><p class="p-purchase italic color4c4c4c" id="name-dic"></p></div>
                                    <div class="div-span-2"><span class="span-purchase-red"></span><p class="p-purchase italic color4c4c4c" id="number-std"></p></div>
                                    <div class="div-span-1"><span class="span-purchase-red"></span><p class="p-purchase italic color4c4c4c" id="number-month"></p></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 div-span-1" style="padding-top: 3%;">
                                        <div class="form-group">
                                            <?php $min_no_of_student = mw_get_option('min-students-subscription') ?>
                                            <label id="num-of-student-lbl" class="font-dialog"><?php _e('Number of Students', 'iii-dictionary') ?></label>
                                            <input type="number" name="no-students" id="student_num" class="form-control" data-min="<?php echo $min_no_of_student ?>" value="<?php echo $min_no_of_student ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-12 div-span-2" style="padding-top: 3%;">
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
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="box-dialog" style="padding-top: 2%;">
                                            <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$</span> <span id="total-amount" class="color708b23">0</span>
                                        </div>
                                    </div>
                                </div>              
                            </div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <button type="submit" id="add-to-cart" name="add-to-cart" class="btn-custom confirm"><?php _e('Check out', 'iii-dictionary') ?></button>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary close-renewsub"><?php _e('Cancel', 'iii-dictionary') ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>          
                        </form>
                    </div>
                </div>
            </div>

            <!-- sat-subscription-dialog -->
            <div id="sat-subscription-dialog" class="modal fade">
                <div class="modal-dialog modal-custom-first">
                    <div class="modal-content boder-black">
                        <div class="modal-header custom-header">
                            <span style="right: 3%;padding-top: 2%;" href="#" data-dismiss="modal" aria-hidden="true" class="close close-dialog"></span>
                            <h3><?php _e('Purchase SAT Subscriptions', 'iii-dictionary') ?></h3>
                        </div>
                        <form method="post" action="<?php echo home_url_ssl() ?>/?r=payments">
                            <input type="hidden" name="sub-type" id="sat-sub-type" value="0">
                            <input type="hidden" name="sat-class" id="sat-class" value="">
                            <input type="hidden" name="sub-id" value="0">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="font-dialog col-xs-12" style="border-bottom: 1px solid #d6d6d6;"><?php _e('Selected Class', 'iii-dictionary') ?></label>
                                            <p class="selected-class col-xs-12" id="selected-class" ></p>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-12" style="padding-top: 3%">
                                        <div class="form-group">
                                            <label class="font-dialog"><?php _e('Number of Months', 'iii-dictionary') ?></label>
                                            <select class="select-box-it form-control" name="sat-months" id="sel-sat-months">
                                                <?php for ($i = 1; $i <= 24; $i++) : ?>
                                                    <option value="<?php echo $i ?>"><?php printf(_n('%s month', '%s months', $i, 'iii-dictionary'), $i) ?></option>
                                                <?php endfor ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                    if (!is_math_panel()) :
                                        $select_class_options = array(CLASS_SAT1 => __('SAT Test 1', 'iii-dictionary'), CLASS_SAT2 => __('SAT Test 2', 'iii-dictionary'), CLASS_SAT3 => __('SAT Test 3', 'iii-dictionary'),
                                            CLASS_SAT4 => __('SAT Test 4', 'iii-dictionary'), CLASS_SAT5 => __('SAT Test 5', 'iii-dictionary'))
                                        ?>

                                        <div class="col-sm-12" id="sat-test-block" style="display: none">
                                            <div class="form-group">
                                                <label class="font-dialog"><?php _e('Practice Test', 'iii-dictionary') ?></label>
                                                <select class="select-box-it form-control sel-sat-class">
                                                    <?php foreach ($select_class_options as $key => $value) : ?>
                                                        <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php
                                    else :
                                        $select1_class_options = array(CLASS_MATH_SAT1A => __('SAT 1A', 'iii-dictionary'), CLASS_MATH_SAT1B => __('SAT 1B', 'iii-dictionary'),
                                            CLASS_MATH_SAT1C => __('SAT 1C', 'iii-dictionary'), CLASS_MATH_SAT1D => __('SAT 1D', 'iii-dictionary'), CLASS_MATH_SAT1E => __('SAT 1E', 'iii-dictionary'));
                                        $select2_class_options = array(CLASS_MATH_SAT2A => __('SAT 2A', 'iii-dictionary'), CLASS_MATH_SAT2B => __('SAT 2B', 'iii-dictionary'),
                                            CLASS_MATH_SAT2C => __('SAT 2C', 'iii-dictionary'), CLASS_MATH_SAT2D => __('SAT 2D', 'iii-dictionary'), CLASS_MATH_SAT2E => __('SAT 2E', 'iii-dictionary'));
                                        $select3_class_options = array(CLASS_MATH_IK => __('Math Kindergarten', 'iii-dictionary'),
                                            CLASS_MATH_IK1 => __('Math Grade 1', 'iii-dictionary'),
                                            CLASS_MATH_IK2 => __('Math Grade 2', 'iii-dictionary'),
                                            CLASS_MATH_IK3 => __('Math Grade 3', 'iii-dictionary'),
                                            CLASS_MATH_IK4 => __('Math Grade 4', 'iii-dictionary'),
                                            CLASS_MATH_IK5 => __('Math Grade 5', 'iii-dictionary'),
                                            CLASS_MATH_IK6 => __('Math Grade 6', 'iii-dictionary'),
                                            CLASS_MATH_IK7 => __('Math Grade 7', 'iii-dictionary'),
                                            CLASS_MATH_IK8 => __('Math Grade 8', 'iii-dictionary'),
                                            CLASS_MATH_IK9 => __('Math Grade 9', 'iii-dictionary'),
                                            CLASS_MATH_IK10 => __('Math Grade 10', 'iii-dictionary'),
                                            CLASS_MATH_IK11 => __('Math Grade 11', 'iii-dictionary'),
                                            CLASS_MATH_IK12 => __('Math Grade 12', 'iii-dictionary'))
                                        ?>

                                        <div class="col-xs-12" id="sat-test-i-block" style="display: none">
                                            <div class="form-group">
                                                <label class="font-dialog"><?php _e('Simulated Test', 'iii-dictionary') ?></label>
                                                <select class="select-box-it form-control sel-sat-class">
                                                    <?php foreach ($select1_class_options as $key => $value) : ?>
                                                        <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-12" id="sat-test-ii-block" style="display: none">
                                            <div class="form-group">
                                                <label class="font-dialog"><?php _e('Simulated Test', 'iii-dictionary') ?></label>
                                                <select class="select-box-it form-control sel-sat-class">
                                                    <?php foreach ($select2_class_options as $key => $value) : ?>
                                                        <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-12" id="ik-test-class-block" style="display: none">
                                            <div class="form-group">
                                                <label class="font-dialog"><?php _e('Simulated Test', 'iii-dictionary') ?></label>
                                                <select class="select-box-it form-control sel-sat-class" id="sel-sat-class">
                                                    <?php foreach ($select3_class_options as $key => $value) : ?>
                                                        <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php endif ?>

                                    <div class="col-sm-12 padding-top-2">
                                        <div class="box-gray-dialog" style="text-align: right">
                                            <?php _e('Total amount:', 'iii-dictionary') ?> <span class="currency color708b23">$<span id="total-amount-sat" class="color708b23">0</span></span>
                                        </div>
                                    </div>
                                </div>              
                            </div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <button type="submit" name="add-to-cart" class="btn-custom confirm"><?php _e('Check out', 'iii-dictionary') ?></button>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <a href="#" data-dismiss="modal" aria-hidden="true" class="btn-custom-1 secondary"><?php _e('Cancel', 'iii-dictionary') ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>          
                        </form>
                    </div>
                </div>
            </div>
        <?php endif ?>
        <script>
            //javscript reated to home
            //push modal 
            var is_prvm = '<?php echo check_message_user() ?>';
            var is_expr = '<?php echo isset($_SESSION['exp_sb'])?$_SESSION['exp_sb']:'' ?>';
            var is_dpal = '<?php echo!empty($id_msg) ? 1 : 0; ?>';
            var is_id = <?php echo!empty($id_msg) ? $id_msg : 0 ?>;
            var is_login = <?php echo is_user_logged_in() ? 1 : 0 ?>;
            var is_grouplist = <?php
        $get_url = explode('/', $_SERVER['REQUEST_URI']);
        echo $get_url[2] == 'grouplist' ? 1 : 0
        ?>;
            var is_myc = <?php
        $router = get_route();
        echo $router[0] == 'manage-your-class' ? 1 : 0
        ?>;
        <?php if (is_home_page() && isset($_SESSION['notice-dialog']) && $_SESSION['notice-dialog'] == 1 && is_user_logged_in()) : ?>
                var ttp = <?php echo (int) $teacher_tool_price ?>;
                var ssp = <?php echo (int) $self_study_price ?>;
                var ssp_math = <?php echo (int) $self_study_price_math ?>;
                var dp = <?php echo (int) $dictionary_price ?>;
                var sub_sat = <?php echo SUB_SAT_PREPARATION ?>;
                var sub_dic = <?php echo SUB_DICTIONARY ?>;
                var sub_teach = <?php echo SUB_TEACHER_TOOL ?>;
                var adp = <?php echo mw_get_option('all-dictionary-price') ?>;
                var student_multiplier = <?php echo STUDENT_MULTIPLIER ?>;
                var min_student = <?php echo mw_get_option('min-students-subscription') ?>;
                var satGp = <?php echo mw_get_option('sat-grammar-price') ?>;
                var satWp = <?php echo mw_get_option('sat-writing-price') ?>;
                var satStp = <?php echo mw_get_option('sat-test-price') ?>;
                var satMIP = <?php echo mw_get_option('math-sat1-price') ?>;
                var satMIIP = <?php echo mw_get_option('math-sat2-price') ?>;
                var satMIKP1 = <?php echo mw_get_option('math-ik-price1') ?>;
                var satMIKP2 = <?php echo mw_get_option('math-ik-price2') ?>;
                var satMIKP3 = <?php echo mw_get_option('math-ik-price3') ?>;
                var satMIKP4 = <?php echo mw_get_option('math-ik-price4') ?>;
                var satMIKP5 = <?php echo mw_get_option('math-ik-price5') ?>;
                var satMIKP6 = <?php echo mw_get_option('math-ik-price6') ?>;
                var satMIKP7 = <?php echo mw_get_option('math-ik-price7') ?>;
                var satMIKP8 = <?php echo mw_get_option('math-ik-price8') ?>;
                var satMIKP9 = <?php echo mw_get_option('math-ik-price9') ?>;
                var satMIKP10 = <?php echo mw_get_option('math-ik-price10') ?>;
                var satMIKP11 = <?php echo mw_get_option('math-ik-price11') ?>;
                var satMIKP12 = <?php echo mw_get_option('math-ik-price12') ?>;
                var satMIKP = <?php echo mw_get_option('math-ik-price') ?>;
                var ptsr = <?php echo mw_get_option('point-exchange-rate') ?>;
                var M_SINGLE = "<?php _e('month', 'iii-dictionary') ?>";
                var M_PLURAL = "<?php _e('months', 'iii-dictionary') ?>";
                var DICT_EMPTY_ERR = "<?php _e('Please select a Dictionary', 'iii-dictionary') ?>";
                var GRP_EMPTY_ERR = "<?php _e('Please select a group', 'iii-dictionary') ?>";
                var GRP_EXIST_ERR = "<?php _e('This group name is already taken. Please choose a different name.', 'iii-dictionary') ?>";
                var GRP_PW_ERR = "<?php _e('Group password cannot empty', 'iii-dictionary') ?>";
                var M_EMPTY_ERR = "<?php _e('Please select Number of Months', 'iii-dictionary') ?>";
                var NUMBER_INV = "<?php _e('Invalid number', 'iii-dictionary') ?>";
                var LBL_NO_USERS = "<?php _e('Number of Users', 'iii-dictionary') ?>";
                var LBL_NO_M = "<?php _e('Number of Months', 'iii-dictionary') ?>";
                var LBL_NO_STUDENTS = "<?php _e('Number of Students', 'iii-dictionary') ?>";
                var LBL_NO_STUDENTS_ADD = "<?php _e('Number of Students to Increase', 'iii-dictionary') ?>";
                var LBL_NO_M_REMAIN = "<?php _e('Number of Remaining Months', 'iii-dictionary') ?>";
                var LBL_NO_M_ADD = "<?php _e('Number of Months', 'iii-dictionary') ?>";
                var _ISMATH = <?php echo $is_math_panel ? 1 : 0 ?>;
                var _IM4 = <?php echo!empty($_SESSION['method_point']) ? $_SESSION['method_point'] : 0 ?>;
        <?php endif; ?>
            (function ($) {
                var md_sub = $('#current-subscription-dialog');
                var md_mg = $('#message-from-support-dialog');
                var md_dpal = $('#display-at-login-dialog');
                var md_viewsub = $('#modal-detail-sub');
                if (is_dpal == '1') {

                    md_dpal.modal('show');
                    md_dpal.on('click', function () {
                        if (is_expr == '1' && !md_dpal.data('bs.modal').isShown) {
                            md_sub.modal('show');
                            md_sub.on('click', function () {
                                if (is_prvm == '1' && !md_sub.data('bs.modal').isShown) {
                                    md_mg.modal('show');
                                }
                            });
                        } else {
                            if (is_prvm == '1' && !md_dpal.data('bs.modal').isShown) {
                                md_mg.modal('show');
                            }
                            if (is_expr == '') {
                                md_sub.modal('hide');
                                md_sub.data('bs.modal', null);
                            }
                        }
                    });

                } else if (is_expr == '1' && is_dpal == '0') {
                    md_sub.modal('show');
                    md_sub.on('click', function () {
                        if (is_prvm == '1' && !md_sub.data('bs.modal').isShown) {
                            md_mg.modal('show');
                        }
                    });
                } else if (is_prvm == '1') {
                    md_mg.modal('show');
                }

                $("#submit-display-at-login").click(function (e) {
                    e.preventDefault();
                    $.post("<?php echo home_url() ?>/?r=ajax/status_msg", {id: is_id});
                    md_dpal.find('a.close').trigger('click');
                });

                $(".join-class-lang-btn").on("click", function () {
                    var get_jcid = $(this).attr('data-jcid');
                    var get_free = $(this).attr('data-free');

                    if (is_login == 0) {
                        $(location).attr('href', '<?php echo locale_home_url() ?>/?r=login');
                    } else {
                        $('#data-join').val(get_jcid);
                        if (get_free == 1) {
                            $('#lang-form').submit();
                        } else {
                            var _gprice = $(this).parents('tr').find('td:eq(1)').text();
                            $('#_gprice').text(_gprice);
                            $('#require-pay-modal').modal();
                            return false;
                        }
                    }

                });

                $(".lang-writing-btn").on('click', function () {
                    var tthis = $('#language-writing-modal');
                    tthis.find('.modal-body').html($(this).next().html());
                    tthis.modal();
                    return false;
                });


                $("#class-with-lang").on('click', function () {
                    md_down(".md-lang");
                    return false;
                });
                $(".md-close").on('click', function () {
                    md_up(".md-lang");
                    return false;
                });
                $(".md-myc-close").on('click', function () {
                    md_up(".md-myc");
                    return false;
                });

                function md_up(_md) {
                    $(_md).slideUp('slow');
                }
                function md_down(_md) {
                    $(_md).slideDown('slow');
                }

                if (is_grouplist == 1) {
                    $('#class-with-lang').click();
                    $('.md-lang').find(".md-close").unbind().attr('href', '<?php echo locale_home_url() ?>');
                }
                if (is_myc == 1) {
                    md_down(".md-myc");
                }
                var arr_yesterday = <?php echo json_encode($arr_yesterday); ?>; 
                var check_login = <?php if(is_user_logged_in()){echo 1;}else{echo 0;} ?>; 
                if(arr_yesterday.length >=1 && check_login==1){
                    $('#modal-reminder-schedule').modal("show");
                    $.post(home_url + "/?r=ajax/get-info-schedule-reminder",{id: arr_yesterday}, //Hien tai dang set cung 1 du lieu
                        function(data){
                            $('#content-reminder-modal').html(data);
                        }  
                    );
                }
                <?php if (is_home_page() && isset($_SESSION['notice-dialog']) && $_SESSION['notice-dialog'] == 1 && is_user_logged_in()) : ?>
                    $(".view-subscription").on('click', function (e) {
                        e.preventDefault();
                        var subid = $(this).attr('data-subid');
                        $.get(home_url + "/?r=ajax/get_detail_sub", {subid: subid}, function (data) {
                            $('#modal-detail-sub #sub-content-detail').html(data);
                        });

                        is_expr = 0;
                        md_sub.modal('hide');
                        md_sub.data('bs.modal', null);
                        md_mg.modal('hide');
                        md_dpal.modal('hide');

                        //md_viewsub.show();
                        md_viewsub.modal('show');

                        md_viewsub.on('click', function () {
                            md_viewsub.modal('hide');
                            md_sub.modal('show');
                        });
                    });

                    $("#sel-teacher-tool, #student_num, #sel-dictionary").change(function () {
                        calc_total_price();
                    });

                    $("#sel-sat-months").change(function () {
                        calc_sat_total_price();
                    });
                    $("#sel-sat-class").change(function () {
                        calc_sat_total_price();
                    });
                    $(".sel-sat-class").change(function () {
                        $("#sat-class").val($(this).val());
                    });
                    $("#sel-self-study-months").change(function () {
                        calc_self_study_price();
                        calc_self_study_price_math();
                    });
                    $('#current-subscription-dialog').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    function calc_total_price() {
                        var students = isNaN(parseInt($("#student_num").val())) ? 0 : parseInt($("#student_num").val());
                        var months = isNaN(parseInt($("#sel-teacher-tool").val())) ? 0 : parseInt($("#sel-teacher-tool").val());
                        switch ($("#addi-sub-type").val()) {
                            case "1":
                            case "6":
                                $("#total-amount").text(students * months * ttp / 100);
                                break;
                            case "2":
                                var p = $("#sel-dictionary").val() == "6" ? adp : dp;
                                $("#total-amount").text(students * months * p / 100);
                                break;
                            case "5":
                                $("#total-amount").text(months * ssp);
                                break;
                            case "9":
                                $("#total-amount").text(months * ssp_math);
                                break;
                        }
                    }
                    function calc_sat_total_price() {
                        var p;
                        var d = $("#sat-class").val();
                        if (parseInt($("[name='subscription-type']:checked").val()) == 12) {
                            var d = $("#sel-sat-class").val();
                        }
                        switch (d) {
                            case "1":
                                p = satGp;
                                break;
                            case "2":
                                p = satWp;
                                break;
                            case "3":
                            case "4":
                            case "5":
                            case "6":
                            case "7":
                                p = satStp;
                                break;
                            case "9":
                            case "10":
                            case "11":
                            case "12":
                            case "13":
                            case "14":
                                p = satMIP;
                                break;
                            case "15":
                            case "16":
                            case "17":
                            case "18":
                            case "19":
                            case "20":
                                p = satMIIP;
                                break;
                            case "38":
                                p = satMIKP;
                                break;
                            case "39":
                                p = satMIKP1;
                                break;
                            case "40":
                                p = satMIKP2;
                                break;
                            case "41":
                                p = satMIKP3;
                                break;
                            case "42":
                                p = satMIKP4;
                                break;
                            case "43":
                                p = satMIKP5;
                                break;
                            case "44":
                                p = satMIKP6;
                                break;
                            case "45":
                                p = satMIKP7;
                                break;
                            case "46":
                                p = satMIKP8;
                                break;
                            case "47":
                                p = satMIKP9;
                                break;
                            case "48":
                                p = satMIKP10;
                                break;
                            case "49":
                                p = satMIKP11;
                                break;
                            case "50":
                                p = satMIKP12;
                                break;

                        }
                        $("#total-amount-sat").text(parseInt($("#sel-sat-months").val()) * p);
                    }
                    function calc_self_study_price() {
                        var months = parseInt($("#sel-self-study-months").val());
                        $("#ss-total-amount").text(months * ssp);
                    }
                    function calc_self_study_price_math() {
                        var months = parseInt($("#sel-self-study-months").val());
                        $("#ss-total-amount").text(months * ssp_math);
                    }
        <?php endif; ?>
        // Srcoll cho modal curren sub
                if ($('#can-scroll-x').height() > 498) {
                    jQuery("#can-scroll-x").mCustomScrollbar({
                        axis: "yx",
                        theme: "rounded-dark",
                        scrollButtons: {enable: true},
                        advanced: {autoExpandHorizontalScroll: true}
                    });
                }
        // check chế độ mobile luôn scroll            
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    jQuery("#can-scroll-x").mCustomScrollbar({
                        axis: "yx",
                        theme: "rounded-dark",
                        scrollButtons: {enable: true},
                        advanced: {autoExpandHorizontalScroll: true}
                    });
                    //-----------------------------------------                  
                }

            })(jQuery);

        </script>
        <?php
        $_SESSION['notice-dialog'] = null;
        $_SESSION['exp_sb'] = null;
    }

    /* get first practice/test when user join group
     * @param id group in message
     * @ result button html
     */

    public static function get_first_assign($group_id) {

        $homeworks = is_first_assign($group_id['group_id']);
        $uref = rawurlencode(base64_encode(home_url() . $_SERVER['REQUEST_URI']));

        if (!empty($homeworks->items)) {
            foreach ($homeworks->items as $hw) {
                if (!$hw->finished || is_null($hw->finished)) {
                    $practice_url = MWHtml::get_practice_page_url($hw->assignment_id) . '&amp;hid=' . $hw->hid;
                    $homework_url = MWHtml::get_practice_page_url($hw->assignment_id) . '&amp;mode=homework&amp;hid=' . $hw->hid;
                    $rp_url = $hw->for_practice ? $practice_url : $homework_url;
                    $rp_url = !empty($uref) ? $rp_url . '&ref=' . $uref : $rp_url;
                    $router = get_route();
                    ?>
                    <div class="modal-footer modal-margin">
                        <div class="row">
                            <div class="col-xs-12 width-50">
                                <a href="<?php echo $rp_url ?>" class="btn btn-block btn-black-matte"><span></span><?php _e('Start Now', 'iii-dictionary') ?></a>
                            </div>
                            <div class="col-xs-12 width-50-se">
                                <?php if ($router[0] != 'manage-subscription') { ?>
                                    <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block btn-white-matte"><span></span><?php _e('Not Now', 'iii-dictionary') ?></a>
                                <?php } else { ?>
                                    <a href="<?php echo home_url() ?>/?r=homework-status" class="btn btn-block btn-white-matte"><span></span><?php _e('Not Now', 'iii-dictionary') ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
        }
    }

    /* button for user to automatic active code dictionary
     * @param activate_code
     * @return html
     */

    public static function auto_active_code_dic($activate_code) {
        ?>
        <div class="modal-footer">
            <div class="row">
                <div class="col-xs-5 col-xs-offset-2" style="margin-left: 0%;width: 50%">
                    <button type="button" data-toggle="modal" data-dismiss="modal" id="apply-credit-code1" style="float: right; border: none !important; box-shadow: none;" data-active='<?php echo $activate_code['activate_code'] ?>' class="btn btn-default btn-block btn-custom"></span><?php _e('Yes, Activate Now', 'iii-dictionary') ?></button>
                </div>
                <div class="col-xs-5" style="float: right; width: 50%;">
                    <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block btn-custom-1" style="box-shadow: none;"></span><?php _e('No, Activate Later', 'iii-dictionary') ?></a>
                </div>
            </div>
            <br>
            <span class="ic-mess-purchase">Go to "Purchase History" to access the code again </span>
        </div>
        <?php
    }

    /* get group with  assign lang
     * @param /lang in url
     * @result html
     */

    public static function get_list_lang() {
        $_lang = get_short_lang_code();
        $is_math = is_math_panel();
        $current_page = max(1, get_query_var('page'));
        $filter['offset'] = 0;
        $filter['items_per_page'] = 99999999;
        $filter['group_type'] = GROUP_CLASS;
        $filter['lang'] = is_math_panel() ? get_short_lang_code() . '-math' : get_short_lang_code() . '-en';
        $filter['orderby'] = 'ordering';
        $filter['order-dir'] = 'ASC';
        $groups = MWDB::get_groups($filter, $filter['offset'], $filter['items_per_page']);
        $uref = rawurlencode(base64_encode(home_url() . $_SERVER['REQUEST_URI']));
        $_mark = ($_lang != 'ja') ? '$' : '';
     
          $total_pages = ceil($groups->total / $filter['items_per_page']);

          $pagination = paginate_links(array(
          'format' => '?page=%#%',
          'current' =>  $current_page,
          'total' => $total_pages
          ));
      
        ?>
        <form id="lang-form" method="post" action="">
            <div class="md-lang" >
                <div class="md-lang-content">
                    <div class="md-header md-header-<?php echo $is_math ? 'math' : 'english' ?>">
                        <a href="#" class="md-close"><?php _e('Close X', 'iii-dictionary') ?></a>
                    </div>
                    <div class="md-body">
                        <div class="row">
                            <div class="col-sm-12 md-select-<?php echo $is_math ? 'math' : 'english' ?>">
                                <p class="md-select-p"><span class="arrow"></span><?php _e('Select a lesson and click Join to start your lesson', 'iii-dictionary') ?></p>
                            </div>
                            <div class="col-sm-12 md-table md-table-<?php echo $is_math ? 'math' : 'english' ?>">
                                <div class="scroll-list2 md-cl-<?php echo $is_math ? 'math' : 'english' ?> scrollbar-white md-eng-math">
                                    <table class="table table-striped table-condensed ik-table1 text-center md-tbody-<?php echo $is_math ? 'math' : 'english' ?>">
                                        <thead>
                                            <tr>
                                                <th><?php _e('Group Name', 'iii-dictionary') ?></th>
                                                <th><?php ($_lang != 'ja') ? _e('Price', 'iii-dictionary') : _e('Point', 'iii-dictionary') ?></th>
                                                <th><?php _e('Course Name', 'iii-dictionary') ?></th>
                                                <th><?php _e('Detail', 'iii-dictionary') ?></th>
                                                <th><?php _e('Join', 'iii-dictionary') ?></th>
                                                <th><?php _e('No.', 'iii-dictionary') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($groups->items)) : ?>
                                                <tr class='text-center'><td colspan="6"><?php _e('No Class at his moment', 'iii-dictionary') ?></td></tr>
                                                <?php
                                            else :
                                                foreach ($groups->items as $item) : $get_stg = MWDB::get_something_in_group($item->id);
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $item->name ?></td>
                                                        <td><?php echo ($item->price != 0) ? $_mark . '' . $item->price : 'Free' ?></td>
                                                        <td class="text-left"><?php echo $item->content ?></td>
                                                        <td><a href="#" class="prevent_detail-btn-<?php echo $is_math ? 'math' : 'english' ?> hidden-div lang-writing-btn"><?php _e('detail', 'iii-dictionary') ?></a><div><?php echo $item->detail ?></div></td>
                                                        <td>
                                                            <?php if (empty($get_stg->step_of_user)) { ?>
                                                                <a href="#" data-free=<?php echo ( $item->price == 0 ) ? '1' : '0' ?>  data-jcid="<?php echo $item->id ?>"  class="prevent_detail-btn-<?php echo $is_math ? 'math' : 'english' ?> join-class-lang-btn"><?php _e('Join', 'iii-dictionary') ?></a>
                                                                <?php
                                                            } else {
                                                                $practice_url = MWHtml::get_practice_page_url($get_stg->step['assg']) . '&amp;hid=' . $get_stg->step['id'];
                                                                $homework_url = MWHtml::get_practice_page_url($get_stg->step['assg']) . '&amp;mode=homework&amp;hid=' . $get_stg->step['id'];
                                                                $rp_url = $get_stg->step['prt'] ? $practice_url : $homework_url;
                                                                $rp_url = !empty($uref) ? $rp_url . '&ref=' . $uref : $rp_url;
                                                                ?>
                                                                <a href="<?php echo $rp_url ?>" class="prevent_detail-btn-<?php echo $is_math ? 'math' : 'english' ?> "><?php _e('Continue', 'iii-dictionary') ?></a>
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo!empty($get_stg->step_of_user) ? $get_stg->step_of_user['completed_homework'] . '/' . $get_stg->total_hw['total_hw'] : 0 . '/' . $get_stg->total_hw['total_hw'] ?></td>
                                                    </tr>
                                                    <?php
                                                endforeach;
                                            endif
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade modal-small modal-md-color-<?php echo $is_math ? 'math' : 'english' ?>" id="language-writing-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header md-modal-header">
                            <a href="#" data-dismiss="modal" aria-hidden="true" class="md-modal-close">X</a>
                            <p class="md-modal-title"><?php _e('Class Detail', 'iii-dictionary') ?></p>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-xs-5">
                                    <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block orange"><?php _e('Close', 'iii-dictionary') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="require-pay-modal" class="modal fade modal-small modal-md-color-<?php echo $is_math ? 'math' : 'english' ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header  md-modal-header">
                            <a href="#" data-dismiss="modal" aria-hidden="true" class="md-modal-close">X</a>
                            <p class="md-modal-title"><?php _e('Messages', 'iii-dictionary') ?></p>
                        </div>
                        <div class="modal-body">
                            <?php _e('Please purchase points and pay by the points.', 'iii-dictionary') ?>
                            <?php printf(__('%s- You need %s.', 'iii-dictionary'), '<br />', '<span id="_gprice"></span>') ?>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-block orange"><?php _e('OK', 'iii-dictionary') ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="data-join" id="data-join" value="" />
        </form>
        <?php
    }

    public static function manage_your_class() {
        ?>
        <div class="md-myc" >
            <div class="md-myc-content">
                <div class="md-myc-header container">
                    <a href="#" class="md-myc-close"><?php _e('Close X', 'iii-dictionary') ?></a>
                    <div class="row">
                        <div class="col-md-11 col-md-offset-1">
                            <h2><?php _e('Create a Group or Class, and Manage your Class', 'iii-dictionary') ?></h2>
                        </div>
                    </div>
                </div>
                <div class="md-myc-body container" style="padding-top: 20px;">
                    <div class="row">
                        <div class="col-md-11 col-md-offset-1">
                            <div class="row">
                                <div class="col-md-7 omg-mrt20px ">
                                    <div class="row">
                                        <div class="col-md-2"><span class="md-myc-step"><?php _e('step 1', 'iii-dictionary') ?></span></div>
                                        <div class="col-md-10">
                                            <?php _e('Create a group (class) under', 'iii-dictionary') ?>
                                            <a href="<?php echo locale_home_url() ?>/?r=create-group" class='omg_yellow-link'><?php _e('"Teacher &#8594; Create A group or Class."', 'iii-dictionary') ?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-7 omg-mrt20px ">
                                    <div class="row">
                                        <div class="col-md-2"><span class="md-myc-step"><?php _e('step 2', 'iii-dictionary') ?></span></div>
                                        <div class="col-md-10">
                                            <?php printf(__('Send "assignment" to the class/group from %s', 'iii-dictionary'), "<br/>") ?>
                                            <a href="<?php echo locale_home_url() ?>/?r=homework-assignment" class='omg_yellow-link'><?php _e('"Teacher &#8594; Homework Assignment." ', 'iii-dictionary') ?></a>
                                            <?php printf(__('"Assignments" are worksheets, and it can be sent as "practice mode" or "test mode". In test mode. students will not see 
														the answer until it has been graded. In practice mode, the worksheet will not be graded.', 'iii-dictionary'), '<br />', '<br />'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-7 omg-mrt20px ">
                                    <div class="row">
                                        <div class="col-md-2"><span class="md-myc-step"><?php _e('step 3', 'iii-dictionary') ?></span></div>
                                        <div class="col-md-10">
                                            <?php _e('Give group name and password to students and so they can join the group.', 'iii-dictionary'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-7 omg-mrt20px ">
                                    <div class="row">
                                        <div class="col-md-2"><span class="md-myc-step"><?php _e('step 4', 'iii-dictionary') ?></span></div>
                                        <div class="col-md-10">
                                            <?php _e('Students see the assignment at ', 'iii-dictionary') ?>
                                            <a href="<?php echo locale_home_url() ?>/?r=homework-status" class='omg_yellow-link'><?php _e('"Student &#8594; Homework Status."', 'iii-dictionary') ?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-md-7 omg-mrt20px ">
                                    <div class="row">
                                        <div class="col-md-2"><span class="md-myc-step"><?php _e('step 5', 'iii-dictionary') ?></span></div>
                                        <div class="col-md-10">
                                            <?php _e('When students finish the assignment, the teacher can see the auto-graded  assignment at ', 'iii-dictionary') ?>
                                            <a href="<?php echo locale_home_url() ?>/?r=teachers-box" class='omg_yellow-link'><?php _e('"Teacher &#8594; Teacher\'s Box."', 'iii-dictionary') ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md-myc-footer container">
                    <div class="row">
                        <div class="col-md-11 col-md-offset-1">
                            <div class="row">
                                <div class="col-md-7 omg-mrt20px ">
                                    <div class="row">
                                        <div class="col-md-2"><span class="md-myc-touch">&nbsp;</span></div>
                                        <div class="col-md-10 myc-footer-dis">

                                            <?php _e('Teachers  may use ready-made worksheets as assignments, or create their own worksheet (in English only). "English Writing" assignments will not be auto-graded. Teachers can manually grade "English Writing" assignments if they wish to do so.  "Project Worksheet" is to submit report to the teacher.', 'iii-dictionary'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public static function get_main_folder_name($name = 'main-folder-media', $class = '', $id = 'main-folder-media', $dic = 0) {
        $theme_root = get_theme_root();
        ?>
        <select class="form-control selectboxit select-box-it<?php echo $class ?>" name="<?php echo $name ?>" id="<?php echo $id ?>">
            <option value="" ><?php _e('Select a directory', 'iii-dictionary') ?></option>
            <?php if ($dic == 1) { ?> <option value="media" ><?php _e('root', 'iii-dictionary') ?></option> <?php }?>
            <?php foreach (glob('media/*', GLOB_ONLYDIR) as $data) : ?>
                <option value="<?php echo $data ?>" <?php if ($id == 's_main_folder' && $_SESSION['media']['main-dic'] == $data) echo "selected"; ?>><?php echo $dic == 1 ? ' &rarr; ' . basename($data) : basename($data) ?></option>
            <?php endforeach ?>
        </select>
        <?php
    }

    public static function get_sub_folder_name($name = 'sub-folder-media', $class = '', $id = 'sub-folder-media') {
        ?>
        <select class="select-box-it<?php echo $class ?>" name="<?php echo $name ?>" id="<?php echo $id ?>">
            <option value="" ><?php _e('Select a directory', 'iii-dictionary') ?></option>
        </select>
        <?php
    }

    public static function list_folder_file($dir, $parent = '', $root = 0, $sfile,$seach) {
//        var_dump($parent);
        $scan = scandir($dir);
        $ignore = array('.', '..');
        if (count($scan) > 2) {
            foreach ($scan as $data) {
                if (!in_array($data, $ignore)) {
                    if (is_dir($dir . '/' . $data)) {
                        MWHtml::list_folder_file($dir . '/' . $data, $dir . '/' . $data, 0, $sfile,$seach);
                    } else {
                        if (empty($sfile)) {
                            _END_:
                            ?>	
                            <tr>
                                <td><?php echo $data ?></td>
                                <td><?php echo $parent ?></td>
                                <td><?php echo date("m/d/Y", time() - @filemtime(utf8_decode($data))) ?></td>
                                <?php 
                                    if($seach ==1 ){ ?><td><a class="btn btn-default btn-block btn-tiny grey" href="<?php echo $dir.'/'.$data;?>" download>Download</a></td><?php }
                                ?>
                            </tr>
                            <?php
                        } else {
                            if ($sfile == $data) {
                                $s_result = 1;
                                goto _END_;
                            }
                        }
                    }
                }
            }
        }
    }

}
