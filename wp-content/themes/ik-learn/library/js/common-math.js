(function ($) {
    var nurls = 'https://notepad.iktutor.com:3000';
    var controlpencil = true;
    var controlrubber = false;
    var positionx = '23';
    var positiony = '0';
    var lastEmit = $.now();
    var touchX, touchY;
    var iniziotocco = true;

    var doc = $(document);
    var color = '#000000';
    // A flag for drawing activity
    var drawing = false;
    var stanza = '';
    var clients = {};
    var cursors = {};
    var username = 'user';
    var id = '99999';
    var socket = io.connect(nurls);
    var _CMODE = '';
    var _ANSWER_TIME = '';
    var _select = 0; // save input user click type "Question Box" to answer
    var _stt = 0;// save stt input user click type "Question Box" to answer to handing when click preview button
    var arr_stt = [];
    $(function () {
        // DEFINED FUNCTIONS

        // Nhập câu trả lời loại 1 thì màu thay đổi màu cho input
        function key_up_answer_type1() {
            $('.input-answer').keyup(function () {
//                alert(1);
                var url = window.location.href;
                if(url.indexOf("&ismode=") != -1) {
                    var str = url.split("&ismode=");
                    var mode = str[1];
                } else {
                    mode = 1;
                }
                var answer = $(this).attr("data-answer");
                answer = answer.replace(" ","");
//                console.log(mode);
                if(mode == 1){
                    if($(this).val() == answer){
                        $(this).parent().css("border-color","");
                        $(this).parent().css("border-color","#13ad0f");
                    }else{
                        $(this).parent().css("border-color","");
                        $(this).parent().css("border-color","#ff0000");
                    }
                }
            });
            $('.input-answer').click(function () {
                _select = $(this).attr("data-answer");
                var url = window.location.href;
                if(url.indexOf("&ismode=") != -1) {
                    var str = url.split("&ismode=");
                    var mode = str[1];
                } else {
                    mode = 1;
                }   
                var answer = $(this).attr("data-answer");
//                console.log(mode);
                if(mode == 1){
                    if($(this).val() == answer){
                        $(this).parent().css("border-color","");
                        $(this).parent().css("border-color","#13ad0f");
                    }else{
                        $(this).parent().css("border-color","");
                        $(this).parent().css("border-color","#ff0000");
                    }
                }
                return _select;
            });
        }
        
        // Xử lý sự kiện click button answer
        function btn_answer(){
            $('.btn-answer').click(function(){
                var url = window.location.href;
                var str1 = url.split("&sid=");
                var str2 = str1[1].split("&");

                var sid = str2[0];

                // get hid
                if(url.indexOf('&hid=') != -1){
                    var str11 = url.split("&hid=");
                    var str22 = str11[1].split("&");
                    var hid = str22[0];
                }
                $.get(home_url + "/?r=ajax/get_assignment_id_by_sid", {sid: sid}, function (data) {

                    var assignment_id = data;
                    var data_answer = '';
                    var correct_answer = '';
                    // Get dữ liệu ra loại 1 toán gồm các Add and Sub & Single Digit Multiplication / Two Digit Multiplication / Long Division by Single Digit / Long Division by Two Digits
                    // Kiểm tra nếu assignment_id 
                    if (assignment_id == 7 || assignment_id == 8 || assignment_id == 9 || assignment_id == 10) {
                        $.get(home_url+"/?r=ajax/get_answer_last_type1",{sid:sid},function(data){
                            $('.txt-answer-correct-last').removeClass("hidden");
                            $('.ic-close-an-correct').removeClass("hidden");
                            $('.txt-answer-correct-last').html('Answer: '+data);
                        });
                        $('.ic-close-an-correct').click(function(){
                            $('.txt-answer-correct-last').addClass("hidden");
                            $('.ic-close-an-correct').addClass("hidden");
                        });
                    } else if(assignment_id == 11 || assignment_id == 15) {
                        var id = $('#sheet-current').html();
                        var value = $('#flashcard-q'+id).find(".answer-box").attr("data-answer");
                        $('.txt-answer-correct-last').removeClass("hidden");
                        $('.ic-close-an-correct').removeClass("hidden");
                        $('.txt-answer-correct-last').html('Answer: '+value);
                        $('.ic-close-an-correct').click(function(){
                            $('.txt-answer-correct-last').addClass("hidden");
                            $('.ic-close-an-correct').addClass("hidden");
                        });
                    }
                    else if (assignment_id == 12) {
                        //get_answer_sheet
                        var id = $('#sheet-current').html();
                        $.get(home_url + "/?r=ajax/get_answer_sheet", {id:id,sid: sid}, function (result12) {
                            $('.txt-answer-correct-last').removeClass("hidden");
                            $('.ic-close-an-correct').removeClass("hidden");
                            $('.txt-answer-correct-last').html('Answer: '+result12);
                        });
                        $('.ic-close-an-correct').click(function(){
                            $('.txt-answer-correct-last').addClass("hidden");
                            $('.ic-close-an-correct').addClass("hidden");
                        });
                    }
                    else if (assignment_id == 13) {
                        // No Answer
                        $.get(home_url + "/?r=ajax/get_answer_correct_by_sid", {sid: sid}, function (result13) {
                            $('.txt-answer-correct-last').removeClass("hidden");
                            $('.ic-close-an-correct').removeClass("hidden");
                            if(result13.indexOf("no")>-1) {
                                $('.txt-answer-correct-last').html('Answer: '+"N/A");
                            }else{
                                $('.txt-answer-correct-last').html('Answer: '+result13);
                            }
                            $('.ic-close-an-correct').click(function(){
                                $('.txt-answer-correct-last').addClass("hidden");
                                $('.ic-close-an-correct').addClass("hidden");
                            });
                        });
                    } else if (assignment_id == 14) {
                        $('.txt-answer-correct-last').removeClass("hidden");
                        $('.ic-close-an-correct').removeClass("hidden");
                        if(_select == '0') {
                            var id = $('#sheet-current').html();
                            var value = $('#qbox-step-q'+id).find(".answer-box").attr("data-answer");
                            $('.txt-answer-correct-last').html('Answer: '+value);
                        }else{
                            $('.txt-answer-correct-last').html('Answer: '+_select);
                        }
                        $('.ic-close-an-correct').click(function(){
                            $('.txt-answer-correct-last').addClass("hidden");
                            $('.ic-close-an-correct').addClass("hidden");
                        });
                    }
                });
            }); 
        }
        
        function btn_submit(){
            $('.btn-submit').click(function (e) {
                // Có 5 loại câu trả lời toán xem V_136
                // get sid 
                var url = window.location.href;
                var str1 = url.split("&sid=");
//                console.log(str1[1]);
                var str2 = str1[1].split("&");

                var sid = str2[0];

                // get hid
                var str11 = url.split("&hid=");
                var str22 = str11[1].split("&");
                var hid = str22[0];
                $.get(home_url + "/?r=ajax/get_assignment_id_by_sid", {sid: sid}, function (data) {

                    var assignment_id = data;
                    var data_answer = '';
                    var correct_answer = '';
                    // Get dữ liệu ra loại 1 toán gồm các Add and Sub & Single Digit Multiplication / Two Digit Multiplication / Long Division by Single Digit / Long Division by Two Digits
                    // Kiểm tra nếu assignment_id 
                    if (assignment_id == 7 || assignment_id == 8 || assignment_id == 9 || assignment_id == 10) {
                        var items = document.getElementsByClassName('s1');
                        var j = 1;
                        for (var i = 0; i < items.length; i++) {
                            data_answer += items[i].name + ':' + items[i].value + ',';
                            correct_answer += items[i].dataset.answer;
                        }
                        data_answer = data_answer.slice(0, -1);
                        data_answer = '{' + data_answer + '}';
                        $.get(home_url + "/?r=ajax/set_answer_test_mode_type1", {hid: hid, data_answer: data_answer, data_correct_answer: correct_answer}, function (data) {
                            //                                    console.log(data);
                        });
                    } 
                    else if (assignment_id == 11 || assignment_id == 15) {
                        var response = []; // Array đáp án
                        var answer = ''; // đáp án
                        var items = document.getElementsByClassName('answer-box');
                        for (var i = 0; i < items.length; i++) {
                            response.push(items[i].dataset.answer);
                        }
                        var id = $('#sheet-current').html();
                        answer = $('#flashcard-q'+id).find(".answer-box").val();
                        $.get(home_url + "/?r=ajax/set_answer_test_mode_type2", {hid: hid, data_answer: answer,stt:id, data_correct_answer: response}, function (data) {
                                    console.log(data);
                        });
                    }
                    else if (assignment_id == 12) {
                        var response = []; // Array đáp án
                        var data_answer = []; // Array đáp án
                        var items = document.getElementsByClassName('answer-box');
                        for (var i = 0; i < items.length; i++) {
                            if (items[i].value) {
                                data_answer.push(items[i].dataset.name + "=>" + items[i].value);
                            } else {
                                data_answer.push("");
                            }
                            response.push(items[i].dataset.name + "=>" + items[i].dataset.answer);
                        }
                        var stt = response[response.length - 1];
                        var stt1 = stt.split("=>");
                        stt1 = stt1[0];
                        stt1 = stt1.split("q");
                        var count = stt1[1];  // số cần for
                        var arr_as = []; // những câu trả lời
                        var arr_corr = []; // Những đáp án
                        //                    console.log(data_answer);
                        // get array đáp án
                        for (var n = 1; n <= count; n++) {
                            var corec = '';
                            var key = 'q' + n;
                            for (var m = 0; m < response.length; m++) {
                                var strr = response[m].split("=>");
                                strr1 = strr[0];
                                if (strr1 == key) {
                                    corec += strr[1] + '/';
                                }
                            }
                            corec = corec.slice(0, -1);
                            arr_corr.push(corec);
                        }
                        // get array câu trả lời   
                        for (var j = 1; j <= count; j++) {
                            var ans = '';
                            var key_an = 'q' + j;

                            for (var k = 0; k < data_answer.length; k++) {
                                if (data_answer[k]) {
                                    var strr_an = data_answer[k].split("=>");
                                    strr2 = strr_an[0];
                                    if (strr2 == key_an) {
                                        ans += strr_an[1] + '/';
                                    }
                                }
                            }
                            ans = ans.slice(0, -1);
                            arr_as.push(ans);
                        }
                        $.get(home_url + "/?r=ajax/set_answer_test_mode_type3", {hid: hid, data_answer: arr_as, data_correct_answer: arr_corr}, function (data) {
                            //                        console.log(data);
                        });
                    } else if (assignment_id == 13) {
                        var answer = $(".css-ans-ws").val();  // câu trả lời
                        var correct_answer = $(".css-ans-ws").attr('data-answer'); // Đáp án
                        $.get(home_url + "/?r=ajax/set_answer_test_mode_type4", {hid: hid, data_answer: answer, data_correct_answer: correct_answer}, function (data) {
                            //                        console.log(data);
                        });
                    } else if (assignment_id == 14) {
                        var items = document.getElementsByClassName('answer-box');
                        var j = 1;
                        for (var i = 0; i < items.length; i++) {
                            data_answer += items[i].value + ',';
                            correct_answer += items[i].dataset.answer + ',';
                        }
                        data_answer = data_answer.slice(0, -1);
                        correct_answer = correct_answer.slice(0, -1);
                        $.get(home_url + "/?r=ajax/set_answer_test_mode_type5", {hid: hid, data_answer: data_answer, data_correct_answer: correct_answer}, function (data) {
//                            console.log(data);
                        });
                    }

                });
                $('#modal-popup-message-submit').modal("show");
                setTimeout(function () {
                    $("#modal-popup-message-submit").modal("hide");
                }, 2000);
            });
        }
        
        function btn_next_ws(){
            function turn_speaker(id, state) {
                var e = document.getElementById(id);
                if (localStorage.speaker_on == "true" && e != null) {
                    if (state == "off") {
                        e.pause();
                        e.currentTime = 0;
                    } else {
                        e.play();
                    }
                }
            }
            $('.ic-next-work').click(function () {
                if(_select == 0){
                    _select = 0;
                }
                var total = $('#homework-content img').length;
                if (total == 0) {
                    total = $('#homework-content div.flashcard-question').length;
                }
                var id = $('#sheet-current').html();
                if (id < total) {
                    var id_next = parseInt(id) + 1;
                    // Biáº¿n param Ä‘á»ƒ check náº¿u lĂ  1 tá»©c lĂ  gá»™p Step náº¿u lĂ  2 tá»©c lĂ  Step Ä‘Æ¡n (bug #7 list V163) 
                    var param = $('#array-param' + id_next).val();
                    $('#sheet-current').html(id_next);
                    if (param == 1 || $("#word-prob-step-q" + id_next).attr("data-ctrl") == 1) {
                        $("#word-prob-step-q" + id).css("display", "block");
                    } else {
                        for (i = 1; i <= total; i++) {
                            $("#word-prob-step-q" + i).css("display", "none");
                        }
                    }
                    $("#word-prob-step-q" + id_next).css("display", "block");
                    $("#word-prob-video-q" + id_next).css("display", "block");
                    turn_speaker("word-prob-sound-q" + id_next, "on");
                    turn_speaker("word-prob-sound-q" + id, "off");
                    $('#flashcard-q' + id).addClass("hidden");
                    $('#flashcard-q' + id_next).removeClass("hidden");
                    $('#txt-answer-question-box-q' + id_next).removeClass("hidden");
                } else {
                    $('#sheet-current').html(total);
                }
            });
        }
        
        function btn_preview_ws(){
            $('.ic-preview-work').click(function () {
                var id = $('#sheet-current').html();
                var id_total = $('#total-sh').html();
                if(_select == 0){
                    _select = 0;
                }
                if (id > 1) {
                    var id_pre = parseInt(id) - 1;
                    var param = $('#array-param' + id_pre).val();
                    $('#sheet-current').html(id_pre);
//                    console.log("pr" + param);
                    if (param == 1) {
                        for (i = 1; i <= total; i++) {
                            $("#word-prob-step-q" + i).css("display", "none");
                        }
                        for ($i = id_pre; $i > 0; $i--) {
//                            console.log("$i" + $i);
                            if ($('#array-param' + $i).val() == 1) {

                                $("#word-prob-step-q" + $i).css("display", "block");
                            } else {
                                $("#word-prob-step-q" + $i).css("display", "block");
                                break;
                            }
                        }
                    } else {
                        $("#word-prob-step-q" + id).css("display", "none");
                    }
                    if ($('#word-prob-step-q' + id_pre).attr("data-ctrl") == 1) {
                        for (var j = id_pre; j > 0; j--) {
                            if ($('#word-prob-step-q' + j).attr("data-ctrl") == 1) {
                                $("#word-prob-step-q" + j).css("display", "block");
                            } else {
                                $("#word-prob-step-q" + j).css("display", "block");
                                break;
                            }
                        }
                    } else {
                        for (var m = 1; m <= id_total; m++) {
                            $("#word-prob-step-q" + m).css("display", "none");
                        }
                        $("#word-prob-step-q" + id_pre).css("display", "block");
                    }
                    $("#word-prob-step-q" + id_pre).css("display", "block");
                    $('#flashcard-q' + id).addClass("hidden");
                    $('#flashcard-q' + id_pre).removeClass("hidden");
                    $('#txt-answer-question-box-q' + id).addClass("hidden");
                } else {
                    $('#sheet-current').html(1);
                }
                turn_speaker("word-prob-sound-q" + id_pre, "on");
                turn_speaker("word-prob-sound-q" + id, "off");
            });
        }
        
        function btn_notepad(){
            $('.css-notepad-btn').click(function (e) {
                if (!$("#menu-notepad").hasClass('open')) {
                    $("#menu-notepad").css("left", "0px");
                    $("#menu-notepad").addClass("open");
                    $('.canvas-math').removeClass("hidden");
                    $('#pencil-1').prop('checked', true);
                    $('input[name="eraser"]').prop('checked', false);
                    controlpencil = true;
                } else {
                    $("#menu-notepad").removeAttr("style");
                    $("#menu-notepad").removeClass("open");
                    $('.canvas-math').addClass("hidden");
                    $('#eraser-50').prop('checked', false);
                    $('#divrubber').attr("style", "display:none");
                    $("#pencilrubber").attr('data-title', 'Eraser');
                }
            });
        }
        
        function set_time_ws(){
            if (_CMODE == "practice") {
                if (_SHOW_TIME > 0) {
                    var _aInterval;
                    _aInterval = setInterval(function () {
                        var _c = $("#question-nav").find("li.active"), _i = $("#flashcard-q" + _c.attr("data-n")).find("input");
                        _i.val(_i.attr("data-answer"));
                        if (_c.next().length > 0)
                            setTimeout(function () {
                                _c.next().click()
                            }, 1500);
                        else
                            clearInterval(_aInterval);
                    }, _SHOW_TIME * 1000);
                }

                var timer;
                $("input.answer-box").keyup(function () {
                    var tthis = $(this);
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        if (tthis.val() == "") {
                            tthis.parent().removeClass("has-correct has-incorrect");
                        } else {
                            var user_type = tthis.val();
                            var answer = tthis.attr("data-answer");
                            user_type = user_type.replace(/\s/g, '');
                            user_type = user_type.toUpperCase();
                            answer = answer.replace(/\s/g, '');
                            answer = answer.toUpperCase();
                            if (compare_fraction(user_type, answer)) {
                                tthis.parent().removeClass("has-incorrect").addClass("has-correct");
                            } else {
                                tthis.parent().removeClass("has-correct").addClass("has-incorrect");
                            }
                        }
                    }, 100);
                });
                function compare_fraction(answer, result) {
                    var _parse_result = result.split(' ');
                    var _case = 0;
                    var _result = false;
                    if (_parse_result.length > 1) { /* 1 : - , 2 : 2 2/3*/
                        _case = (isNaN(_parse_result[0])) ? 1 : 2;
                    }

                    switch (_case) {
                        case 1  :
                            _result = (answer.replace('- ', '-') == result.replace('- ', '-')) ? true : false;
                            break;
                        case 2  :
                            _result = answer == result ? true : false;
                            if (!_result) {
                                var _parse_part = _parse_result[1].split('/');

                                result = ((parseInt(_parse_result[0]) * parseInt(_parse_part[1]) + parseInt(_parse_part[0]))) + '/' + parseInt(_parse_part[1]);
                                _result = (answer.replace('- ', '-') == result) ? true : false;
                            }
                            break;
                        default :
                            _result = (answer.replace('- ', '-') == result.replace('- ', '-')) ? true : false;
                            break;
                    }
                    return _result;
                }

                var timer2;
                $("#input-answer").keyup(function (e) {
                    var tthis = $(this), title;
                    clearTimeout(timer2);
                    timer2 = setTimeout(function () {
                        if (tthis.val() == "") {
                            tthis.tooltip("destroy");
                        } else {
                            var user_type = tthis.val();
                            var answer = tthis.attr("data-answer");
                            user_type = user_type.replace(/\s/g, '');
                            user_type = user_type.toUpperCase();
                            answer = answer.replace(/\s/g, '');
                            answer = answer.toUpperCase();
                            if (user_type == answer) {
                                title = tthis.attr("data-correct");
                                tthis.addClass("correct").removeClass("incorrect");
                            } else {
                                title = tthis.attr("data-incorrect");
                                tthis.removeClass("correct").addClass("incorrect");
                            }
                            tthis.attr("title", title).tooltip("fixTitle").tooltip("show");
                            $("div.col-xs-9 .tooltip").css({"font-size": "25px"});
                        }
                    }, 200);
                });
            } else {
                if (_ANSWER_TIME > 0) {
                    var _aInterval;
                    _aInterval = setInterval(function () {
                        var _c = $("#question-nav").find("li.active"), _i = $("#flashcard-q" + _c.attr("data-n")).find("input");
                        if (_c.next().length > 0)
                            setTimeout(function () {
                                _c.next().click()
                            }, 1500);
                        else {
                            clearInterval(_aInterval);
                            $("#submit-homework-modal").modal();
                        }
                    }, _ANSWER_TIME * 1000);
                }
            }
        }
    // END FUNCTIONS    
        if (window.location.href.indexOf("&prid") > -1) {
            var url = window.location.href;
            var string = url.split('&prid=');
            var id = string[1];
//            console.log(id);
            $('#select-math-worksheet-dialog').modal('show');
            $.get(home_url + "/?r=ajax/math_worksheet/get", {lid: id}, function (data) {
//                console.log(data);
                var worksheets = JSON.parse(data), _tbl = $("#sel-worksheets");

                _tbl.html("");
                if (worksheets.length > 0) {
                    $.each(worksheets, function (i, v) {
                        var css = '',
                                url = "href='" + home_url + "/" + LANG_CODE + "/?r=math-homework&sid=" + v.sid + "&id_parent=" + id + "&ref=" + $("#uref").val() + "'",
                                SUB = '3';
                        if (v.type != SUB || (v.type == SUB && v.is == true)) {
                            v.sub = '';
                        }
                        if (v.type == SUB && v.sub == 'text-muted') {
                            css = 'btn-info';
                            url = '';
                        } else {
                            css += 'orange';
                        }

                        if (css == 'orange') {
                            _tbl.append("<tr class='" + v.sub + "'><td>" + v.name + "</td>" +
                                    "<td style='width: 125px'><a " + url + " class='btn btn-block btn-tiny  " + css + "'><span class='icon-start'></span>Start</a></td></tr>"
                                    );
                        } else {
                            _tbl.append("<tr class='" + v.sub + "'><td>" + v.name + "</td>" +
                                    "<td style='width: 125px'><button class='btn btn-block btn-tiny gray' id='test-click'><span class='icon-start'></span>Start</button></td></tr>"
                                    );
                        }
                    });
                } else {
                    _tbl.append("<tr><td>" + _tbl.attr("data-empty-msg") + "</td></tr>");
                }
            });
        }
        $(".select-math-level").click(function () { 
            $('#data-level1').val($(this).attr('data-level'));
            $("#math-sublevel").text($(this).text());
            var subname = $(this).text();
            $("#math-level").text($(this).parents(".math-levels").find("h6").text());
            $("#math-category").text($(".page-title").text());
            $("#start-math-worksheet").attr("href", "#");
            $("#sel-worksheets").html("");
            var id_parent = $(this).attr("data-level");
//            console.log("subname" + subname + "id_parent" + id_parent);
            $.get(home_url + "/?r=ajax/math_worksheet/get", {lid: $(this).attr("data-level")}, function (data) {
                var worksheets = JSON.parse(data), _tbl = $("#sel-worksheets");
                _tbl.html("");
                if (worksheets.length > 0) {
                    $.each(worksheets, function (i, v) {
                        var css = '',
                                url = "href='" + home_url + "/" + LANG_CODE + "/?r=math-homework&sid=" + v.sid + "&id_parent=" + id_parent + "&ref=" + $("#uref").val() + "'",
                                SUB = '3';
                        if (v.type != SUB || (v.type == SUB && v.is == true)) {
                            v.sub = '';
                        }
                        if (v.type == SUB && v.sub == 'text-muted') {
                            css = 'btn-info';
                            url = '';
                        } else {
                            css += 'orange';
                        }

                        if (css == 'orange') {
                            _tbl.append("<tr class='" + v.sub + "'>" +
                                    "<td class='td-math-modal'><a " + url + " data-id=" + v.sid + " data-name='" + subname + "' data-subname='" + v.name + "' class='btn btn-block btn-tiny css-btn-math-sheet btn-show-worksheet " + css + "'></span>START</a></td>"
                                    + "<td >" + v.name + "</td></tr>"
                                    );
                        } else {
                            _tbl.append("<tr class='" + v.sub + "'>" +
                                    "<td class='td-math-modal'><button data-id=" + v.sid + " data-name='" + subname + "' data-subname='" + v.name + "' class='btn btn-block btn-tiny gray css-btn-math-sheet css-bg-d9d9d9 btn-show-worksheet ' disabled='disabled' id='test-click'></span>START</button></td>"
                                    + "<td >" + v.name + "</td></tr>"
                                    );
                        }
                    });
                } else {
                    _tbl.append("<tr><td>" + _tbl.attr("data-empty-msg") + "</td></tr>");
                }

                $('.btn-show-worksheet').click(function (e) {
                    var name = $(this).attr("data-name");
                    var sub_name = $(this).attr("data-subname");
                    var sid = $(this).attr("data-id");
                    var str = window.location.href;
                    var str_new = str.split("#");
                    var url = str_new[0];
                    var add_sid = url + "#modal-working-worksheet&sid=" + sid;
                    e.preventDefault();
                    $('#modal-working-worksheet').modal('show');
                    window.history.pushState({path: add_sid}, '', add_sid);
                    var sid = $(this).attr("data-id");
                    $('#sub-parent').html(name);
                    $('#is-location').html(sub_name);
                    if (typeof localStorage.speaker_on === "undefined") {
                        localStorage.speaker_on = true;
                    }
//                        console.log(localStorage.speaker_on);
                    localStorage.speaker_on == "false" ? $("#speaker-button").addClass("off") : $("#speaker-button").removeClass("off");

                    function turn_speaker(id, state) {
                        var e = document.getElementById(id);
                        if (localStorage.speaker_on == "true" && e != null) {
                            if (state == "off") {
                                e.pause();
                                e.currentTime = 0;
                            } else {
                                e.play();
                            }
                        }
                    }
                    $("#modal-working-worksheet").on("show.bs.modal", function (e) {
                        $('.txt-answer-correct-last').addClass("hidden");
                        $('.ic-close-an-correct').addClass("hidden");
                        $('#speaker-button').removeAttr("class");
                        $('#speaker-button').addClass("btn-ic-volum icon-sound");
                        $('#speaker-button-mb').removeAttr("class");
                        $('#speaker-button-mb').addClass("btn-ic-volum icon-sound");
                        localStorage.speaker_on = true;
                    });


                    $.get(home_url + "/?r=ajax/load_info_worksheet", {sid: sid}, function (data) {
                        var obj = JSON.parse(data);
                        $('#modal-working-worksheet').find("#math-level").html(obj['sheet_name']);
                    });
                    $.get(home_url + "/?r=ajax/load_working_worksheet", {sid: sid, ismode: ismode}, function (data) {
                        $('#modal-working-worksheet').find("#sel-worksheets").html(data);
                        $.get(home_url + "/?r=ajax/load_info_worksheet", {sid: sid}, function (data) {
                            var obj = JSON.parse(data);
                            $('#modal-working-worksheet').find("#math-level-mb").html(obj['sheet_name']);
                        });
                        $('#flashcard-q1').removeClass("hidden");
                        $('#txt-answer-question-box-q1').removeClass("hidden");
                        $(".preview-btn-math").click(function () {
                            $("#modal-homework-math").modal("show");
                        });
                        $(".ic-main-btn").click(function () {
                            $(".ic-main-btn").css("display", "none");
                            $(".ic-submit-btn").css("display", "block");
                            $(".ic-answer-btn").css("display", "block");
                            $(".image-surround").css("display", "block");
                        });

                        $(".ic-close-main").click(function () {
                            $(".ic-main-btn").css("display", "block");
                            $(".ic-submit-btn").css("display", "none");
                            $(".ic-answer-btn").css("display", "none");
                            $(".image-surround").css("display", "none");
                        });
                        key_up_answer_type1();
                        btn_answer();
                        btn_submit();
                        // Handing show image and click Next or Preview worksheet        
                        $('#word-prob-step-q1').css("display", "block");
                        var total = $('#homework-content img').length;
                        if (total == 0) {
                            total = $('#homework-content div.flashcard-question').length;
                        }
                        $('#total-sh').html(total);

                        //Draw Notepad
                        $('#divrubber').draggable();

                        var divrubber = $('#divrubber');
                        var canvas = $('#math');
                        var ctx = canvas[0].getContext('2d');
                        var spessore = 1;
                        var colorem;
                        canvas[0].width = $('.css-content-math-sheet').width();//window.innerWidth;
                        canvas[0].height = $('.css-content-math-sheet').height();//window.innerHeight - 0;

                        if (ctx) {
                            window.addEventListener('resize', resizecanvas, false);
                            window.addEventListener('orientationchange', resizecanvas, false);
                            resizecanvas();
                        }

                        // ctx setup
                        ctx.lineCap = "round";
                        ctx.lineJoin = "round";
                        ctx.lineWidth = 2;
                        ctx.font = "20px Tahoma";

                        socket.emit('setuproom', {
                            'room': '999999',
                            'id': username,
                            'usernamerem': username
                        });

                        socket.on('setuproomserKO', function (data) {
                            stanza = data.room;
                        });

                        socket.on('setuproomser', function (data) {
                            stanza = data.room;
                        });

                        socket.on('doppioclickser', function (data) {
                            ctx.fillStyle = data.color;
                            ctx.font = data.fontsizerem + "px Tahoma";
                            ctx.fillText(data.scrivi, data.x, data.y);
                        });

                        socket.on('moving', function (data) {

                            if (!(data.id in clients)) {
                                // a new user has come online. create a cursor for them
                                cursors[data.id] = $('<div class="cursor"><div class="identif">' + data.usernamerem + '</div>').appendTo('#cursors');
                            }
                            // Move the mouse pointer

                            cursors[data.id].css({
                                'left': data.x,
                                'top': data.y
                            });

                            // Is the user drawing?
                            if (data.drawing && clients[data.id]) {

                                // Draw a line on the canvas. clients[data.id] holds
                                // the previous position of this user's mouse pointer

                                ctx.strokeStyle = data.color;
                                //  drawLinerem(clients[data.id].x, clients[data.id].y, data.x, data.y,data.spessremo,data.color);
                                drawLinerem(clients[data.id].x, clients[data.id].y, data.x, data.y, data.spessremo, data.color);
                            }

                            // Saving the current client state
                            clients[data.id] = data;
                            clients[data.id].updated = $.now();
                        });

                        socket.on('toccomoving', function (data) {

                            if (!(data.id in clients)) {
                                // a new user has come online. create a cursor for them
                                cursors[data.id] = $('<div class="cursor"><div class="identif">' + data.usernamerem + '</div>').appendTo('#cursors');
                            }
                            // Move the mouse pointer

                            // Is the user drawing?
                            if (data.drawing && clients[data.id]) {

                                cursors[data.id].css({
                                    'left': data.x,
                                    'top': data.y
                                });

                                // Draw a line on the canvas. clients[data.id] holds
                                // the previous position of this user's mouse pointer

                                ctx.strokeStyle = data.color;
                                //  drawLinerem(clients[data.id].x, clients[data.id].y, data.x, data.y,data.spessremo,data.color);
                                drawLinerem(clients[data.id].x, clients[data.id].y, data.x, data.y, data.spessremo, data.color);
                            }

                            // Saving the current client state
                            clients[data.id] = data;
                            clients[data.id].updated = $.now();
                        });

                        socket.on('rubberser', function (data) {

                            if (!(data.id in clients)) {
                                // a new user has come online. create a cursor for them
                                cursors[data.id] = $('<div class="cursor"><div class="identif">' + data.usernamerem + '</div>').appendTo('#cursors');
                            }
                            // Move the mouse pointer

                            // Is the user drawing?
                            if (data.controlrubber && clients[data.id]) {

                                cursors[data.id].css({
                                    'left': data.x,
                                    'top': data.y
                                });

                                ctx.clearRect(data.x, data.y, data.width, data.height);

                            }

                            // Saving the current client state
                            clients[data.id] = data;
                            clients[data.id].updated = $.now();
                        });

                        $("#pencilrubber").click(function () {
                            $('input[name="pencil"]').prop('checked', false);
                            if ($(this).attr('data-title') === 'Eraser') {
                                if ($('input[name="eraser"]').val() != 100) {
                                    var e_size = $('input[name="eraser"]').val();
                                    if (e_size == 80) {
                                        $('#controlrubber').addClass('css-cursor-80');
                                        $('#controlrubber').removeClass('css-cursor-50');
                                        $('#controlrubber').removeClass('css-cursor-100');
                                    } else if (e_size == 50) {
                                        $('#controlrubber').addClass('css-cursor-50');
                                        $('#controlrubber').removeClass('css-cursor-80');
                                        $('#controlrubber').removeClass('css-cursor-100');
                                    }
                                } else {
                                    $('#divrubber').attr('style', "width:100px;height:100px");
                                    $('#eraser-200').prop('checked', true);
                                    $('#controlrubber').addClass('css-cursor-100');
                                    $('#controlrubber').removeClass('css-cursor-50');
                                    $('#controlrubber').removeClass('css-cursor-80');
                                }
                                //$('.minicolors').hide();
                                $(this).attr('data-title', 'Pencil');
                                $('#divrubber').css("display", "block");
                                controlpencil = false;
                                rubbersize = divrubber.width();
                            } else {
                                $(this).attr('data-title', 'Eraser');
                                $('#divrubber').css("display", "none");
                                controlpencil = true;
                            }
                        });

                        $('input[name="color"]').change(function () {
                            var val = $(this).val();
                            colorem = val;
                            //$("input.minicolors").val(String(val));

                            // $('input.minicolors').minicolors('settings', {
                            //     value: String(val)
                            // });            
                        });

                        $('input[name="eraser"]').change(function () {
                            var val = $(this).val();
                            divrubber.width(val);
                            divrubber.height(val);
                            $("#pencilrubber").attr('data-title', 'Pencil');
                            $('#divrubber').css("display", "block");
                            controlpencil = false;
                            rubbersize = divrubber.width();
                            if (val == 50) {
                                rubbersize = 50;
                                $('#controlrubber').addClass('css-cursor-50');
                                $('#controlrubber').removeClass('css-cursor-80');
                                $('#controlrubber').removeClass('css-cursor-100');
                            } else if (val == 80) {
                                rubbersize = 80;
                                $('#controlrubber').addClass('css-cursor-80');
                                $('#controlrubber').removeClass('css-cursor-50');
                                $('#controlrubber').removeClass('css-cursor-100');
                            } else if (val == 100) {
                                rubbersize = 100;
                                $('#controlrubber').addClass('css-cursor-100');
                                $('#controlrubber').removeClass('css-cursor-80');
                                $('#controlrubber').removeClass('css-cursor-50');
                            }
                            $('input[name="pencil"]').prop('checked', false);

                        });

                        $('input[name="pencil"]').change(function () {
                            var val = $(this).val();
                            spessore = val;
                            $('input[name="eraser"]').prop('checked', false);
                            $('#divrubber').css("display", "none");
                            controlpencil = true;
                            $("#pencilrubber").attr('data-title', 'Eraser');
                        });
                        $('#clear-state').click(function () {
                            ctx.clearRect(0, 0, canvas[0].width, canvas[0].height);
                        });

                        var prev = {};

                        canvas[0].addEventListener('touchstart', function (e) {
                            e.preventDefault();
                            getTouchPos();
                            var strokeStyle = $("input[name='color']:checked").val();
                            var lineWidth = $("input[name='pencil']:checked").val();
                            socket.emit('mousemove', {
                                'x': touchX,
                                'y': touchY,
                                'drawing': drawing,
                                'color': strokeStyle,
                                'id': id,
                                'usernamerem': username,
                                'spessremo': lineWidth,
                                'room': stanza
                            });
                            $(".cursor").css("zIndex", 6);
                            drawing = true;
                            // Hide the instructions
                            //instructions.fadeOut();

                        }, false);

                        canvas[0].addEventListener('touchend', function (e) {
                            e.preventDefault();
                            drawing = false;
                            $(".cursor").css("zIndex", 8);
                        }, false);

                        canvas[0].addEventListener('touchmove', function (e) {
                            e.preventDefault();
                            var strokeStyle = $("input[name='color']:checked").val();
                            var lineWidth = $("input[name='pencil']:checked").val();
                            if ($.now() - lastEmit > 25) {
                                if (controlpencil) {
                                    prev.x = touchX;
                                    prev.y = touchY;
                                    getTouchPos();

                                    drawLine(prev.x, prev.y, touchX, touchY);

                                    lastEmit = $.now();
                                    socket.emit('mousemove', {
                                        'x': touchX,
                                        'y': touchY,
                                        'drawing': drawing,
                                        'color': strokeStyle,
                                        'id': id,
                                        'usernamerem': username,
                                        'spessremo': lineWidth,
                                        'room': stanza
                                    });
                                }
                            }

                        }, false);

                        canvas.on('mousedown', function (e) {
                            e.preventDefault();
                            prev.x = e.clientX;
                            prev.y = e.clientY;
                            var strokeStyle = $("input[name='color']:checked").val();
                            var lineWidth = $("input[name='pencil']:checked").val();
                            socket.emit('mousemove', {
                                'x': prev.x,
                                'y': prev.y,
                                'drawing': drawing,
                                'color': strokeStyle,
                                'id': id,
                                'usernamerem': username,
                                'spessremo': lineWidth,
                                'room': stanza
                            });

                            drawing = true;
                            $(".cursor").css("zIndex", 6);
                            // Hide the instructions
                            //instructions.fadeOut();
                        });

                        canvas.on('mouseup mouseleave', function () {
                            drawing = false;
                            $(".cursor").css("zIndex", 8);
                        });

                        canvas.on('mousemove', function (e) {
                            posmousex = e.clientX;
                            posmousey = e.clientY;
                            var strokeStyle = $("input[name='color']:checked").val();
                            var lineWidth = $("input[name='pencil']:checked").val();

                            if ($.now() - lastEmit > 25) {

                                if (drawing && (controlpencil)) {
                                    //     ctx.strokeStyle = document.getElementById('minicolore').value;
                                    drawLine(prev.x, prev.y, e.clientX, e.clientY);
                                    prev.x = e.clientX;
                                    prev.y = e.clientY;
                                    lastEmit = $.now();

                                    socket.emit('mousemove', {
                                        'x': prev.x,
                                        'y': prev.y,
                                        'drawing': drawing,
                                        'color': strokeStyle,
                                        'id': id,
                                        'usernamerem': username,
                                        'spessremo': lineWidth,
                                        'room': stanza
                                    });

                                }
                            }
                            // Draw a line for the current user's movement, as it is
                            // not received in the socket.on('moving') event above

                        });
                        divrubber.on('mouseup mouseleave', function (e) {
                            drawing = false;
                            controlrubber = false;
                        });

                        divrubber.on('mousemove', function (e) {
                            //                    if (document.getElementById('controlrubber').checked) {

                            ctx.clearRect(divrubber.position().left, divrubber.position().top + 190, rubbersize + 4, rubbersize + 4);

                            controlrubber = true;

                            socket.emit('rubber', {
                                'x': divrubber.position().left,
                                'y': divrubber.position().top + 190,
                                'id': id,
                                'usernamerem': username,
                                'controlrubber': controlrubber,
                                'width': rubbersize + 4,
                                'height': rubbersize + 4,
                                'room': stanza
                            });
                        });

                        divrubber.on('mousedown', function (e) {
                            // controlrubber= true;
                            drawing = false;
                        });

                        function resizecanvas() {
                            
                            var imgdata = ctx.getImageData(0, 0, canvas[0].width, canvas[0].height);
                            var w = $('.css-content-math-sheet').width();//window.innerWidth;
                            var h = $('.css-content-math-sheet').height();//window.innerHeight - 0;
                            canvas[0].width = w;//window.innerWidth;
                            canvas[0].height = h;//window.innerHeight - 0;
                            ctx.putImageData(imgdata, 0, 0);
                        }

                        function getTouchPos(e) {
                            if (!e)
                                var e = event;

                            if (e.touches) {
                                if (e.touches.length == 1) { // Only deal with one finger
                                    var touch = e.touches[0]; // Get the information for finger #1
                                    //   touchX=touch.clientX-touch.target.offsetLeft;
                                    // touchY=touch.clientY-touch.target.offsetTop;
                                    touchX = touch.clientX;
                                    touchY = touch.clientY;
                                }
                            }
                        }

                        function drawLine(fromx, fromy, tox, toy) {
                            var strokeStyle = $("input[name='color']:checked").val();
                            var lineWidth = $("input[name='pencil']:checked").val();

                            ctx.strokeStyle = strokeStyle;
                            ctx.lineWidth = lineWidth;
                            ctx.beginPath();
                            ctx.moveTo(fromx, fromy);
                            ctx.lineTo(tox, toy);
                            ctx.stroke();
                        }

                        function drawLinerem(fromx, fromy, tox, toy, spessore, colorem) {
                            ctx.strokeStyle = colorem;
                            ctx.lineWidth = spessore;
                            ctx.beginPath();
                            ctx.moveTo(fromx, fromy);
                            ctx.lineTo(tox, toy);
                            ctx.stroke();
                            fromx = tox;
                            fromy = toy;
                        }

                        btn_next_ws();
                        btn_preview_ws();
                        btn_notepad();
                        $('.close-menu').click(function (e) {
                            if ($("#menu-notepad").hasClass('open')) {
                                $("#menu-notepad").css("left", "-200px");
                                $("#menu-notepad").removeClass("open");
                                $('#divrubber').attr("style", "display:none");
                                $("#pencilrubber").attr('data-title', 'Eraser');
                                $('#math').addClass("hidden");
                            }
                        });
                        // END handing show image and click Next or Preview worksheet  

                        $("#modal-view-result-homework").on("hidden.bs.modal", function (e) {
                            e.preventDefault();
                            $("#modal-view-result-homework .modal-dialog").removeAttr("style");
                        });
                        $("#modal-view-result-homework").on("show.bs.modal", function () {
                            $("#modal-view-result-homework .modal-dialog").removeAttr("style");
                            $("#modal-view-result-homework #load-prevent-modal").addClass("hidden");
                        });

                        $(".hidden-modal-preview").click(function (e) {
                            e.preventDefault();
                            $("#modal-view-result-homework #load-prevent-modal").addClass("hidden");
                            $("#modal-view-result-homework .modal-dialog").removeAttr("style");
                        });
                        $("#question-nav").find("li").click(function () {
                            $("#question-nav").find("li").removeClass("active");
                            $("div.flashcard-question").addClass("hidden");
                            $(this).addClass("active");
                            $("#flashcard-q" + $(this).attr("data-n")).removeClass("hidden");
                        });

                        $("#question-nav").find("li:first").click();
                        function turn_speaker(id, state) {
                            var e = document.getElementById(id);
                            if (localStorage.speaker_on == "true" && e != null) {
                                if (state == "off") {
                                    e.pause();
                                    e.currentTime = 0;
                                } else {
                                    e.play();
                                }
                            }
                        }
                        set_time_ws();
                    });
                    $("#modal-working-worksheet").on("hidden.bs.modal", function (e) {
                        var id = $('#sheet-current').html();
                        turn_speaker("word-prob-sound-q" + id, "off");
                        turn_speaker("word-prob-video-q" + id, "off");
                    });

                    //end JS load content modal        
                });
                $(".btn-ic-volum").click(function () {
                    if ($(this).hasClass("off")) {
                        localStorage.speaker_on = true;
                        $(this).removeClass('off');
                        $(this).removeClass('icon-sound-mute');
                        $(this).addClass('icon-sound');
                    } else {
                        localStorage.speaker_on = false;
                        $(this).removeClass('icon-sound');
                        $(this).addClass('icon-sound-mute');
                        $(this).addClass('off');
                    }
                    if (localStorage.speaker_on == "false") {
                        var elements = document.getElementsByTagName("audio");
                        for (i = 0; i < elements.length; i++) {
                            elements[i].pause();
                        }
                    }
                });
            });

            function calc_self_study_price_math() {
                var months = parseInt($("#sel-self-study-months").val());
                $("#ss-total-amount").text(months * 20);
            }
//            $('#self-study-subscription-dialog').on('show.bs.modal', function () {
//                $('#self-study-sub').val("9");
//                $("#sel-self-study-months").selectBoxIt('selectOption', '1'.toString()).data("selectBox-selectBoxIt");
//                $("#sel-self-study-months").data("selectBox-selectBoxIt").refresh();
//                calc_self_study_price_math();
//            });
            $('#sel-self-study-months').change(function () {
                $('#self-sat-months').val($('#sel-self-study-months').val());
                calc_self_study_price_math();
            });
            $("#select-math-worksheet-dialog").modal();
            $('.body-math').addClass("modal-non-overflow");
            $('#select-math-worksheet-dialog').on("hidden.bs.modal",function(e){
                e.preventDefault();
                $('.body-math').removeClass("modal-non-overflow");
            });
        });
        $('#parent').click(function () {
            $("#modal-working-worksheet").modal("hide");
            $("#select-math-worksheet-dialog").modal("hide");
        });

        // XU LY NEU NGUOI DUNG DANG SHOW MODAL WORKING WORKSHEET MA REFRESH TRANG
        if (window.location.href.indexOf("#modal-working-worksheet") > -1 && (window.location.href.indexOf("&sid") > -1)) {
            var str = window.location.href;
            var str1 = str.split("&sid=");
            if (str1[1].includes("&")) {
                var str2 = str1[1].split("&");
                var sid = str2[0];
                if (window.location.href.indexOf("&ismode") > -1) {
                    var str3 = str1[1].split("&ismode=");
                    var ismode = str3[1];
                }
            } else {
                var ismode = 1;
                var sid = str1[1];
            }
            $("#sel-worksheets").html("");
            $("#select-math-worksheet-dialog").modal();
            $.get(home_url + "/?r=ajax/load_info_worksheet", {sid: sid}, function (data) {
                var oob = JSON.parse(data);
                var lid = oob['grade_id'];
                $.get(home_url + "/?r=ajax/math_worksheet/get", {lid: lid}, function (data) {
                    var worksheets = JSON.parse(data), _tbl = $("#select-math-worksheet-dialog #sel-worksheets");
                    var subname = oob['sublevel_name'];
                    $("#select-math-worksheet-dialog #math-sublevel").text(subname);
                    $("#select-math-worksheet-dialog #math-level").text(oob['level_name']);
                    _tbl.html("");
                    if (worksheets.length > 0) {
                        $.each(worksheets, function (i, v) {
                            var css = '',
                                    url = "href='" + home_url + "/" + LANG_CODE + "/?r=math-homework&sid=" + v.sid + "&id_parent=" + lid + "'",
                                    SUB = '3';
                            if (v.type != SUB || (v.type == SUB && v.is == true)) {
                                v.sub = '';
                            }
                            if (v.type == SUB && v.sub == 'text-muted') {
                                css = 'btn-info';
                                url = '';
                            } else {
                                css += 'orange';
                            }

                            if (css == 'orange') {
                                _tbl.append("<tr class='" + v.sub + "'>" +
                                        "<td style='width: 125px'><a " + url + " data-id=" + v.sid + " data-name='" + subname + "' data-subname='" + v.name + "' class='btn btn-block btn-tiny css-btn-math-sheet btn-show-worksheet " + css + "'></span>START</a></td>"
                                        + "<td >" + v.name + "</td></tr>"
                                        );
                            } else {
                                _tbl.append("<tr class='" + v.sub + "'>" +
                                        "<td style='width: 125px'><button data-id=" + v.sid + " data-name='" + subname + "' data-subname='" + v.name + "' class='btn btn-block btn-tiny gray css-btn-math-sheet css-bg-d9d9d9 btn-show-worksheet ' disabled='disabled' id='test-click'></span>START</button></td>"
                                        + "<td >" + v.name + "</td></tr>"
                                        );
                            }
                        });
                    } else {
                        _tbl.append("<tr><td>" + _tbl.attr("data-empty-msg") + "</td></tr>");
                    }
                    $(".ic-main-btn").click(function () {
                        $(".ic-main-btn").css("display", "none");
                        $(".ic-submit-btn").css("display", "block");
                        $(".ic-answer-btn").css("display", "block");
                        $(".image-surround").css("display", "block");
                    });
                    $(".ic-close-main").click(function () {
                        $(".ic-main-btn").css("display", "block");
                        $(".ic-submit-btn").css("display", "none");
                        $(".ic-answer-btn").css("display", "none");
                        $(".image-surround").css("display", "none");
                    });
                    
                    key_up_answer_type1();
                    btn_answer();
                    btn_submit();
                    $('.btn-show-worksheet').click(function (e) {
                        var url_old = window.location.href;
                        var name = $(this).attr("data-name");
                        var sub_name = $(this).attr("data-subname");
                        var sid = $(this).attr("data-id");
                        var str = window.location.href;
                        var str_new = str.split("#");
                        var url = str_new[0];
                        var add_sid = url + "#modal-working-worksheet&sid=" + sid;
                        e.preventDefault();
                        $('#modal-working-worksheet').modal('show');
                        if(url_old.indexOf("&ismode") == -1) {
                            window.history.pushState({path: add_sid}, '', add_sid);
                        } else {
                            window.history.pushState({path: add_sid}, '', url_old);
                        }
                        var sid = $(this).attr("data-id");
                        $('#sub-parent').html(name);
                        $('#is-location').html(sub_name);
                        if (typeof localStorage.speaker_on === "undefined") {
                            localStorage.speaker_on = true;
                        }
                        //                        console.log(localStorage.speaker_on);
                        localStorage.speaker_on == "false" ? $("#speaker-button").addClass("off") : $("#speaker-button").removeClass("off");

                        function turn_speaker(id, state) {
                            var e = document.getElementById(id);
                            if (localStorage.speaker_on == "true" && e != null) {
                                if (state == "off") {
                                    e.pause();
                                    e.currentTime = 0;
                                } else {
                                    e.play();
                                }
                            }
                        }

                        $.get(home_url + "/?r=ajax/load_info_worksheet", {sid: sid}, function (data) {
                            var obj = JSON.parse(data);
                            $('#modal-working-worksheet').find("#math-level").html(obj['sheet_name']);
                        });
                    //--------------------------------------------------------//    
                    
                        $.get(home_url + "/?r=ajax/load_working_worksheet", {sid: sid, ismode: ismode}, function (data) {
                            $('#modal-working-worksheet').find("#sel-worksheets").html(data);
                            $.get(home_url + "/?r=ajax/load_info_worksheet", {sid: sid}, function (data) {
                                var obj = JSON.parse(data);
                                $('#modal-working-worksheet').find("#math-level-mb").html(obj['sheet_name']);
                            });
                            $('#flashcard-q1').removeClass("hidden");
                            $('#txt-answer-question-box-q1').removeClass("hidden");
                            $(".preview-btn-math").click(function () {
                                $("#modal-homework-math").modal("show");
                            });
                            $(".ic-main-btn").click(function () {
                                $(".ic-main-btn").css("display", "none");
                                $(".ic-submit-btn").css("display", "block");
                                $(".ic-answer-btn").css("display", "block");
                                $(".image-surround").css("display", "block");
                            });
                            $(".ic-close-main").click(function () {
                                $(".ic-main-btn").css("display", "block");
                                $(".ic-submit-btn").css("display", "none");
                                $(".ic-answer-btn").css("display", "none");
                                $(".image-surround").css("display", "none");
                            });

                            
                            key_up_answer_type1();
                            btn_answer();
                            btn_submit();
                            
                            // Handing show image and click Next or Preview worksheet        
                            $('#word-prob-step-q1').css("display", "block");
                            var total = $('#homework-content img').length;
                            if (total == 0) {
                                total = $('#homework-content div.flashcard-question').length;
                            }
                            $('#total-sh').html(total);

                            //Draw Notepad
                            $('#divrubber').draggable();

                            var divrubber = $('#divrubber');
                            var canvas = $('#math');
                            var ctx = canvas[0].getContext('2d');
                            var spessore = 1;
                            var colorem;
                            canvas[0].width = $('.css-content-math-sheet').width();//window.innerWidth;
                            canvas[0].height = $('.css-content-math-sheet').height();//window.innerHeight - 0;

                            if (ctx) {
                                window.addEventListener('resize', resizecanvas, false);
                                window.addEventListener('orientationchange', resizecanvas, false);
                                resizecanvas();
                            }

                            // ctx setup
                            ctx.lineCap = "round";
                            ctx.lineJoin = "round";
                            ctx.lineWidth = 2;
                            ctx.font = "20px Tahoma";

                            socket.emit('setuproom', {
                                'room': '999999',
                                'id': username,
                                'usernamerem': username
                            });

                            socket.on('setuproomserKO', function (data) {
                                stanza = data.room;
                            });

                            socket.on('setuproomser', function (data) {
                                stanza = data.room;
                            });

                            socket.on('doppioclickser', function (data) {
                                ctx.fillStyle = data.color;
                                ctx.font = data.fontsizerem + "px Tahoma";
                                ctx.fillText(data.scrivi, data.x, data.y);
                            });

                            socket.on('moving', function (data) {

                                if (!(data.id in clients)) {
                                    // a new user has come online. create a cursor for them
                                    cursors[data.id] = $('<div class="cursor"><div class="identif">' + data.usernamerem + '</div>').appendTo('#cursors');
                                }
                                // Move the mouse pointer

                                cursors[data.id].css({
                                    'left': data.x,
                                    'top': data.y
                                });

                                // Is the user drawing?
                                if (data.drawing && clients[data.id]) {

                                    // Draw a line on the canvas. clients[data.id] holds
                                    // the previous position of this user's mouse pointer

                                    ctx.strokeStyle = data.color;
                                    //  drawLinerem(clients[data.id].x, clients[data.id].y, data.x, data.y,data.spessremo,data.color);
                                    drawLinerem(clients[data.id].x, clients[data.id].y, data.x, data.y, data.spessremo, data.color);
                                }

                                // Saving the current client state
                                clients[data.id] = data;
                                clients[data.id].updated = $.now();
                            });

                            socket.on('toccomoving', function (data) {

                                if (!(data.id in clients)) {
                                    // a new user has come online. create a cursor for them
                                    cursors[data.id] = $('<div class="cursor"><div class="identif">' + data.usernamerem + '</div>').appendTo('#cursors');
                                }
                                // Move the mouse pointer

                                // Is the user drawing?
                                if (data.drawing && clients[data.id]) {

                                    cursors[data.id].css({
                                        'left': data.x,
                                        'top': data.y
                                    });

                                    // Draw a line on the canvas. clients[data.id] holds
                                    // the previous position of this user's mouse pointer

                                    ctx.strokeStyle = data.color;
                                    //  drawLinerem(clients[data.id].x, clients[data.id].y, data.x, data.y,data.spessremo,data.color);
                                    drawLinerem(clients[data.id].x, clients[data.id].y, data.x, data.y, data.spessremo, data.color);
                                }

                                // Saving the current client state
                                clients[data.id] = data;
                                clients[data.id].updated = $.now();
                            });

                            socket.on('rubberser', function (data) {

                                if (!(data.id in clients)) {
                                    // a new user has come online. create a cursor for them
                                    cursors[data.id] = $('<div class="cursor"><div class="identif">' + data.usernamerem + '</div>').appendTo('#cursors');
                                }
                                // Move the mouse pointer

                                // Is the user drawing?
                                if (data.controlrubber && clients[data.id]) {

                                    cursors[data.id].css({
                                        'left': data.x,
                                        'top': data.y
                                    });

                                    ctx.clearRect(data.x, data.y, data.width, data.height);

                                }

                                // Saving the current client state
                                clients[data.id] = data;
                                clients[data.id].updated = $.now();
                            });

                            $("#pencilrubber").click(function () {
                                $('input[name="pencil"]').prop('checked', false);
                                if ($(this).attr('data-title') === 'Eraser') {
                                    if ($('input[name="eraser"]').val() != 100) {
                                        var e_size = $('input[name="eraser"]').val();
                                        if (e_size == 80) {
                                            $('#controlrubber').addClass('css-cursor-80');
                                            $('#controlrubber').removeClass('css-cursor-50');
                                            $('#controlrubber').removeClass('css-cursor-100');
                                        } else if (e_size == 50) {
                                            $('#controlrubber').addClass('css-cursor-50');
                                            $('#controlrubber').removeClass('css-cursor-80');
                                            $('#controlrubber').removeClass('css-cursor-100');
                                        }
                                    } else {
                                        $('#divrubber').attr('style', "width:100px;height:100px");
                                        $('#eraser-200').prop('checked', true);
                                        $('#controlrubber').addClass('css-cursor-100');
                                        $('#controlrubber').removeClass('css-cursor-50');
                                        $('#controlrubber').removeClass('css-cursor-80');
                                    }
                                    //$('.minicolors').hide();
                                    $(this).attr('data-title', 'Pencil');
                                    $('#divrubber').css("display", "block");
                                    controlpencil = false;
                                    rubbersize = divrubber.width();
                                } else {
                                    $(this).attr('data-title', 'Eraser');
                                    $('#divrubber').css("display", "none");
                                    controlpencil = true;
                                }
                            });

                            $('input[name="color"]').change(function () {
                                var val = $(this).val();
                                colorem = val;
                                //$("input.minicolors").val(String(val));

                                // $('input.minicolors').minicolors('settings', {
                                //     value: String(val)
                                // });            
                            });

                            $('input[name="eraser"]').change(function () {
                                var val = $(this).val();
                                divrubber.width(val);
                                divrubber.height(val);
                                $("#pencilrubber").attr('data-title', 'Pencil');
                                $('#divrubber').css("display", "block");
                                controlpencil = false;
                                rubbersize = divrubber.width();
                                if (val == 50) {
                                    rubbersize = 50;
                                    $('#controlrubber').addClass('css-cursor-50');
                                    $('#controlrubber').removeClass('css-cursor-80');
                                    $('#controlrubber').removeClass('css-cursor-100');
                                } else if (val == 80) {
                                    rubbersize = 80;
                                    $('#controlrubber').addClass('css-cursor-80');
                                    $('#controlrubber').removeClass('css-cursor-50');
                                    $('#controlrubber').removeClass('css-cursor-100');
                                } else if (val == 100) {
                                    rubbersize = 100;
                                    $('#controlrubber').addClass('css-cursor-100');
                                    $('#controlrubber').removeClass('css-cursor-80');
                                    $('#controlrubber').removeClass('css-cursor-50');
                                }
                                $('input[name="pencil"]').prop('checked', false);

                            });

                            $('input[name="pencil"]').change(function () {
                                var val = $(this).val();
                                spessore = val;
                                $('input[name="eraser"]').prop('checked', false);
                                $('#divrubber').css("display", "none");
                                controlpencil = true;
                                $("#pencilrubber").attr('data-title', 'Eraser');
                            });

                            $('#clear-state').click(function () {
                                ctx.clearRect(0, 0, canvas[0].width, canvas[0].height);
                            });

                            var prev = {};

                            canvas[0].addEventListener('touchstart', function (e) {
                                e.preventDefault();
                                getTouchPos();
                                var strokeStyle = $("input[name='color']:checked").val();
                                var lineWidth = $("input[name='pencil']:checked").val();
                                socket.emit('mousemove', {
                                    'x': touchX,
                                    'y': touchY,
                                    'drawing': drawing,
                                    'color': strokeStyle,
                                    'id': id,
                                    'usernamerem': username,
                                    'spessremo': lineWidth,
                                    'room': stanza
                                });
                                $(".cursor").css("zIndex", 6);
                                drawing = true;
                                // Hide the instructions
                                //instructions.fadeOut();

                            }, false);

                            canvas[0].addEventListener('touchend', function (e) {
                                e.preventDefault();
                                drawing = false;
                                $(".cursor").css("zIndex", 8);
                            }, false);

                            canvas[0].addEventListener('touchmove', function (e) {
                                e.preventDefault();
                                var strokeStyle = $("input[name='color']:checked").val();
                                var lineWidth = $("input[name='pencil']:checked").val();
                                if ($.now() - lastEmit > 25) {
                                    if (controlpencil) {
                                        prev.x = touchX;
                                        prev.y = touchY;
                                        getTouchPos();

                                        drawLine(prev.x, prev.y, touchX, touchY);

                                        lastEmit = $.now();
                                        socket.emit('mousemove', {
                                            'x': touchX,
                                            'y': touchY,
                                            'drawing': drawing,
                                            'color': strokeStyle,
                                            'id': id,
                                            'usernamerem': username,
                                            'spessremo': lineWidth,
                                            'room': stanza
                                        });
                                    }
                                }

                            }, false);

                            canvas.on('mousedown', function (e) {
                                e.preventDefault();
                                prev.x = e.clientX;
                                prev.y = e.clientY;
                                var strokeStyle = $("input[name='color']:checked").val();
                                var lineWidth = $("input[name='pencil']:checked").val();
                                socket.emit('mousemove', {
                                    'x': prev.x,
                                    'y': prev.y,
                                    'drawing': drawing,
                                    'color': strokeStyle,
                                    'id': id,
                                    'usernamerem': username,
                                    'spessremo': lineWidth,
                                    'room': stanza
                                });

                                drawing = true;
                                $(".cursor").css("zIndex", 6);
                                // Hide the instructions
                                //instructions.fadeOut();
                            });

                            canvas.on('mouseup mouseleave', function () {
                                drawing = false;
                                $(".cursor").css("zIndex", 8);
                            });

                            canvas.on('mousemove', function (e) {
                                posmousex = e.clientX;
                                posmousey = e.clientY;
                                var strokeStyle = $("input[name='color']:checked").val();
                                var lineWidth = $("input[name='pencil']:checked").val();

                                if ($.now() - lastEmit > 25) {

                                    if (drawing && (controlpencil)) {
                                        //     ctx.strokeStyle = document.getElementById('minicolore').value;
                                        drawLine(prev.x, prev.y, e.clientX, e.clientY);
                                        prev.x = e.clientX;
                                        prev.y = e.clientY;
                                        lastEmit = $.now();

                                        socket.emit('mousemove', {
                                            'x': prev.x,
                                            'y': prev.y,
                                            'drawing': drawing,
                                            'color': strokeStyle,
                                            'id': id,
                                            'usernamerem': username,
                                            'spessremo': lineWidth,
                                            'room': stanza
                                        });

                                    }
                                }
                                // Draw a line for the current user's movement, as it is
                                // not received in the socket.on('moving') event above

                            });
                            divrubber.on('mouseup mouseleave', function (e) {
                                drawing = false;
                                controlrubber = false;
                            });

                            divrubber.on('mousemove', function (e) {
                                //                    if (document.getElementById('controlrubber').checked) {

                                ctx.clearRect(divrubber.position().left, divrubber.position().top + 190, rubbersize + 4, rubbersize + 4);

                                controlrubber = true;

                                socket.emit('rubber', {
                                    'x': divrubber.position().left,
                                    'y': divrubber.position().top + 190,
                                    'id': id,
                                    'usernamerem': username,
                                    'controlrubber': controlrubber,
                                    'width': rubbersize + 4,
                                    'height': rubbersize + 4,
                                    'room': stanza
                                });
                                //                    }

                            });

                            divrubber.on('mousedown', function (e) {
                                // controlrubber= true;
                                drawing = false;
                            });

                            function resizecanvas() {
                                var imgdata = ctx.getImageData(0, 0, canvas[0].width, canvas[0].height);
                                var w = $('.css-content-math-sheet').width();//window.innerWidth;
                                var h = $('.css-content-math-sheet').height();//window.innerHeight - 0;
                                canvas[0].width = w;//window.innerWidth;
                                canvas[0].height = h;//window.innerHeight - 0;
                                ctx.putImageData(imgdata, 0, 0);
                            }

                            function getTouchPos(e) {
                                if (!e)
                                    var e = event;

                                if (e.touches) {
                                    if (e.touches.length == 1) { // Only deal with one finger
                                        var touch = e.touches[0]; // Get the information for finger #1
                                        //   touchX=touch.clientX-touch.target.offsetLeft;
                                        // touchY=touch.clientY-touch.target.offsetTop;
                                        touchX = touch.clientX;
                                        touchY = touch.clientY;
                                    }
                                }
                            }

                            function drawLine(fromx, fromy, tox, toy) {
                                var strokeStyle = $("input[name='color']:checked").val();
                                var lineWidth = $("input[name='pencil']:checked").val();

                                ctx.strokeStyle = strokeStyle;
                                ctx.lineWidth = lineWidth;
                                ctx.beginPath();
                                ctx.moveTo(fromx, fromy);
                                ctx.lineTo(tox, toy);
                                ctx.stroke();
                            }

                            function drawLinerem(fromx, fromy, tox, toy, spessore, colorem) {
                                ctx.strokeStyle = colorem;
                                ctx.lineWidth = spessore;
                                ctx.beginPath();
                                ctx.moveTo(fromx, fromy);
                                ctx.lineTo(tox, toy);
                                ctx.stroke();
                                fromx = tox;
                                fromy = toy;
                            }

                            btn_next_ws();
                            btn_preview_ws();
                            btn_notepad();
                            $('.close-menu').click(function (e) {
                                if ($("#menu-notepad").hasClass('open')) {
                                    $("#menu-notepad").css("left", "-200px");
                                    $("#menu-notepad").removeClass("open");
                                    $('#divrubber').attr("style", "display:none");
                                    $("#pencilrubber").attr('data-title', 'Eraser');
                                    $('#math').addClass("hidden");
                                }
                            });
                            // END handing show image and click Next or Preview worksheet  

                            $("#modal-view-result-homework").on("hidden.bs.modal", function (e) {
                                e.preventDefault();
                                $("#modal-view-result-homework .modal-dialog").removeAttr("style");
                            });
                            $("#modal-view-result-homework").on("show.bs.modal", function () {
                                $("#modal-view-result-homework .modal-dialog").removeAttr("style");
                                $("#modal-view-result-homework #load-prevent-modal").addClass("hidden");
                            });

                            $(".hidden-modal-preview").click(function (e) {
                                e.preventDefault();
                                $("#modal-view-result-homework #load-prevent-modal").addClass("hidden");
                                $("#modal-view-result-homework .modal-dialog").removeAttr("style");
                            });
                            $("#question-nav").find("li").click(function () {
                                $("#question-nav").find("li").removeClass("active");
                                $("div.flashcard-question").addClass("hidden");
                                $(this).addClass("active");
                                $("#flashcard-q" + $(this).attr("data-n")).removeClass("hidden");
                            });

                            $("#question-nav").find("li:first").click();
                            function turn_speaker(id, state) {
                                var e = document.getElementById(id);
                                if (localStorage.speaker_on == "true" && e != null) {
                                    if (state == "off") {
                                        e.pause();
                                        e.currentTime = 0;
                                    } else {
                                        e.play();
                                    }
                                }
                            }
                            set_time_ws(); 
                            
                        });
                        $("#modal-working-worksheet").on("hidden.bs.modal", function (e) {
                            var id = $('#sheet-current').html();
                            turn_speaker("word-prob-sound-q" + id, "off");
                            turn_speaker("word-prob-video-q" + id, "off");
                        });

                        //end JS load content modal        
                    });
                });
            });
            $("#modal-working-worksheet").on("show.bs.modal", function (e) {
                $('.txt-answer-correct-last').addClass("hidden");
                $('.ic-close-an-correct').addClass("hidden");
                $('#speaker-button').removeAttr("class");
                $('#speaker-button').addClass("btn-ic-volum icon-sound");
                $('#speaker-button-mb').removeAttr("class");
                $('#speaker-button-mb').addClass("btn-ic-volum icon-sound");
                localStorage.speaker_on = true;
            });
            $('#modal-working-worksheet').modal('show');
            window.history.pushState({path: str}, '', str);
            if (typeof localStorage.speaker_on === "undefined") {
                localStorage.speaker_on = true;
            }
            localStorage.speaker_on == "false" ? $("#speaker-button").addClass("off") : $("#speaker-button").removeClass("off");

            function turn_speaker(id, state) {
                var e = document.getElementById(id);
                if (localStorage.speaker_on == "true" && e != null) {
                    if (state == "off") {
                        e.pause();
                        e.currentTime = 0;
                    } else {
                        e.play();
                    }
                }
            }

            $(".btn-ic-volum").click(function () {
                if ($(this).hasClass("off")) {
                    localStorage.speaker_on = true;
                    $(this).removeClass('off');
                    $(this).removeClass('icon-sound-mute');
                    $(this).addClass('icon-sound');
                } else {
                    localStorage.speaker_on = false;
                    $(this).removeClass('icon-sound');
                    $(this).addClass('icon-sound-mute');
                    $(this).addClass('off');
                }
                if (localStorage.speaker_on == "false") {
                    var elements = document.getElementsByTagName("audio");
                    for (i = 0; i < elements.length; i++) {
                        elements[i].pause();
                    }
                }
            });

            $.get(home_url + "/?r=ajax/load_info_worksheet", {sid: sid}, function (data) {
                var obj = JSON.parse(data);
//                            console.log(obj);
                $('#modal-working-worksheet').find("#math-level").html(obj['sheet_name']);
                $('#is-location').html(obj['sheet_name']);
                $('#sub-parent').html(obj['sublevel_name']);
            });
            $.get(home_url + "/?r=ajax/load_working_worksheet", {sid: sid, ismode: ismode}, function (data) {
                $('#modal-working-worksheet').find("#sel-worksheets").html(data);
                $('#flashcard-q1').removeClass("hidden");
                $('#txt-answer-question-box-q1').removeClass("hidden");
                $(".preview-btn-math").click(function () {
                    $("#modal-homework-math").modal("show");
                });
                $.get(home_url + "/?r=ajax/load_info_worksheet", {sid: sid}, function (data) {
                    var obj = JSON.parse(data);
                    $('#modal-working-worksheet').find("#math-level-mb").html(obj['sheet_name']);
                });
                // Handing show image and click Next or Preview worksheet        
                $('#word-prob-step-q1').css("display", "block");
                var total = $('#homework-content img').length;
                if (total == 0) {
                    total = $('#homework-content div.flashcard-question').length;
                }
                $('#total-sh').html(total);

                //Draw Notepad
                $('#divrubber').draggable();

                var divrubber = $('#divrubber');
                var canvas = $('#math');
                var ctx = canvas[0].getContext('2d');
                var spessore = 1;
                var colorem;
                canvas[0].width = $('.css-content-math-sheet').width();//window.innerWidth;
                canvas[0].height = $('.css-content-math-sheet').height();//window.innerHeight - 0;

                if (ctx) {
                    window.addEventListener('resize', resizecanvas, false);
                    window.addEventListener('orientationchange', resizecanvas, false);
                    resizecanvas();
                }

                // ctx setup
                ctx.lineCap = "round";
                ctx.lineJoin = "round";
                ctx.lineWidth = 2;
                ctx.font = "20px Tahoma";

                socket.emit('setuproom', {
                    'room': '999999',
                    'id': username,
                    'usernamerem': username
                });

                socket.on('setuproomserKO', function (data) {
                    stanza = data.room;
                });

                socket.on('setuproomser', function (data) {
                    stanza = data.room;
                });

                socket.on('doppioclickser', function (data) {
                    ctx.fillStyle = data.color;
                    ctx.font = data.fontsizerem + "px Tahoma";
                    ctx.fillText(data.scrivi, data.x, data.y);
                });

                socket.on('moving', function (data) {

                    if (!(data.id in clients)) {
                        // a new user has come online. create a cursor for them
                        cursors[data.id] = $('<div class="cursor"><div class="identif">' + data.usernamerem + '</div>').appendTo('#cursors');
                    }
                    // Move the mouse pointer

                    cursors[data.id].css({
                        'left': data.x,
                        'top': data.y
                    });

                    // Is the user drawing?
                    if (data.drawing && clients[data.id]) {

                        // Draw a line on the canvas. clients[data.id] holds
                        // the previous position of this user's mouse pointer

                        ctx.strokeStyle = data.color;
                        //  drawLinerem(clients[data.id].x, clients[data.id].y, data.x, data.y,data.spessremo,data.color);
                        drawLinerem(clients[data.id].x, clients[data.id].y, data.x, data.y, data.spessremo, data.color);
                    }

                    // Saving the current client state
                    clients[data.id] = data;
                    clients[data.id].updated = $.now();
                });

                socket.on('toccomoving', function (data) {

                    if (!(data.id in clients)) {
                        // a new user has come online. create a cursor for them
                        cursors[data.id] = $('<div class="cursor"><div class="identif">' + data.usernamerem + '</div>').appendTo('#cursors');
                    }
                    // Move the mouse pointer

                    // Is the user drawing?
                    if (data.drawing && clients[data.id]) {

                        cursors[data.id].css({
                            'left': data.x,
                            'top': data.y
                        });

                        // Draw a line on the canvas. clients[data.id] holds
                        // the previous position of this user's mouse pointer

                        ctx.strokeStyle = data.color;
                        //  drawLinerem(clients[data.id].x, clients[data.id].y, data.x, data.y,data.spessremo,data.color);
                        drawLinerem(clients[data.id].x, clients[data.id].y, data.x, data.y, data.spessremo, data.color);
                    }

                    // Saving the current client state
                    clients[data.id] = data;
                    clients[data.id].updated = $.now();
                });

                socket.on('rubberser', function (data) {

                    if (!(data.id in clients)) {
                        // a new user has come online. create a cursor for them
                        cursors[data.id] = $('<div class="cursor"><div class="identif">' + data.usernamerem + '</div>').appendTo('#cursors');
                    }
                    // Move the mouse pointer

                    // Is the user drawing?
                    if (data.controlrubber && clients[data.id]) {

                        cursors[data.id].css({
                            'left': data.x,
                            'top': data.y
                        });

                        ctx.clearRect(data.x, data.y, data.width, data.height);

                    }

                    // Saving the current client state
                    clients[data.id] = data;
                    clients[data.id].updated = $.now();
                });

                $("#pencilrubber").click(function () {
                    $('input[name="pencil"]').prop('checked', false);
                    if ($(this).attr('data-title') === 'Eraser') {
                        if ($('input[name="eraser"]').val() != 100) {
                            var e_size = $('input[name="eraser"]').val();
                            if (e_size == 80) {
                                $('#controlrubber').addClass('css-cursor-80');
                                $('#controlrubber').removeClass('css-cursor-50');
                                $('#controlrubber').removeClass('css-cursor-100');
                            } else if (e_size == 50) {
                                $('#controlrubber').addClass('css-cursor-50');
                                $('#controlrubber').removeClass('css-cursor-80');
                                $('#controlrubber').removeClass('css-cursor-100');
                            }
                        } else {
                            $('#divrubber').attr('style', "width:100px;height:100px");
                            $('#eraser-200').prop('checked', true);
                            $('#controlrubber').addClass('css-cursor-100');
                            $('#controlrubber').removeClass('css-cursor-50');
                            $('#controlrubber').removeClass('css-cursor-80');
                        }
                        //$('.minicolors').hide();
                        $(this).attr('data-title', 'Pencil');
                        $('#divrubber').css("display", "block");
                        controlpencil = false;
                        rubbersize = divrubber.width();
                    } else {
                        $(this).attr('data-title', 'Eraser');
                        $('#divrubber').css("display", "none");
                        controlpencil = true;
                    }
                });

                $('input[name="color"]').change(function () {
                    var val = $(this).val();
                    colorem = val;
                            
                });
                
                $('input[name="eraser"]').change(function () {
                    var val = $(this).val();
                    divrubber.width(val);
                    divrubber.height(val);
                    $("#pencilrubber").attr('data-title', 'Pencil');
                    $('#divrubber').css("display", "block");
                    controlpencil = false;
                    rubbersize = divrubber.width();
                    if (val == 50) {
                        rubbersize = 50;
                        $('#controlrubber').addClass('css-cursor-50');
                        $('#controlrubber').removeClass('css-cursor-80');
                        $('#controlrubber').removeClass('css-cursor-100');
                    } else if (val == 80) {
                        rubbersize = 80;
                        $('#controlrubber').addClass('css-cursor-80');
                        $('#controlrubber').removeClass('css-cursor-50');
                        $('#controlrubber').removeClass('css-cursor-100');
                    } else if (val == 100) {
                        rubbersize = 100;
                        $('#controlrubber').addClass('css-cursor-100');
                        $('#controlrubber').removeClass('css-cursor-80');
                        $('#controlrubber').removeClass('css-cursor-50');
                    }
                    $('input[name="pencil"]').prop('checked', false);

                });

                $('input[name="pencil"]').change(function () {
                    var val = $(this).val();
                    spessore = val;
                    $('input[name="eraser"]').prop('checked', false);
                    $('#divrubber').css("display", "none");
                    controlpencil = true;
                    $("#pencilrubber").attr('data-title', 'Eraser');
                });

                $('#clear-state').click(function () {
                    ctx.clearRect(0, 0, canvas[0].width, canvas[0].height);
                });

                var prev = {};

                canvas[0].addEventListener('touchstart', function (e) {
                    e.preventDefault();
                    getTouchPos();
                    var strokeStyle = $("input[name='color']:checked").val();
                    var lineWidth = $("input[name='pencil']:checked").val();
                    socket.emit('mousemove', {
                        'x': touchX,
                        'y': touchY,
                        'drawing': drawing,
                        'color': strokeStyle,
                        'id': id,
                        'usernamerem': username,
                        'spessremo': lineWidth,
                        'room': stanza
                    });
                    $(".cursor").css("zIndex", 6);
                    drawing = true;
                    // Hide the instructions
                    //instructions.fadeOut();

                }, false);

                canvas[0].addEventListener('touchend', function (e) {
                    e.preventDefault();
                    drawing = false;
                    $(".cursor").css("zIndex", 8);
                }, false);

                canvas[0].addEventListener('touchmove', function (e) {
                    e.preventDefault();
                    var strokeStyle = $("input[name='color']:checked").val();
                    var lineWidth = $("input[name='pencil']:checked").val();
                    if ($.now() - lastEmit > 25) {
                        if (controlpencil) {
                            prev.x = touchX;
                            prev.y = touchY;
                            getTouchPos();

                            drawLine(prev.x, prev.y, touchX, touchY);

                            lastEmit = $.now();
                            socket.emit('mousemove', {
                                'x': touchX,
                                'y': touchY,
                                'drawing': drawing,
                                'color': strokeStyle,
                                'id': id,
                                'usernamerem': username,
                                'spessremo': lineWidth,
                                'room': stanza
                            });
                        }
                    }

                }, false);

                canvas.on('mousedown', function (e) {
                    e.preventDefault();
                    prev.x = e.clientX;
                    prev.y = e.clientY;
                    var strokeStyle = $("input[name='color']:checked").val();
                    var lineWidth = $("input[name='pencil']:checked").val();
                    socket.emit('mousemove', {
                        'x': prev.x,
                        'y': prev.y,
                        'drawing': drawing,
                        'color': strokeStyle,
                        'id': id,
                        'usernamerem': username,
                        'spessremo': lineWidth,
                        'room': stanza
                    });

                    drawing = true;
                    $(".cursor").css("zIndex", 6);
                    // Hide the instructions
                    //instructions.fadeOut();
                });

                canvas.on('mouseup mouseleave', function () {
                    drawing = false;
                    $(".cursor").css("zIndex", 8);
                });

                canvas.on('mousemove', function (e) {
                    posmousex = e.clientX;
                    posmousey = e.clientY;
                    var strokeStyle = $("input[name='color']:checked").val();
                    var lineWidth = $("input[name='pencil']:checked").val();

                    if ($.now() - lastEmit > 25) {

                        if (drawing && (controlpencil)) {
                            //     ctx.strokeStyle = document.getElementById('minicolore').value;
                            drawLine(prev.x, prev.y, e.clientX, e.clientY);
                            prev.x = e.clientX;
                            prev.y = e.clientY;
                            lastEmit = $.now();

                            socket.emit('mousemove', {
                                'x': prev.x,
                                'y': prev.y,
                                'drawing': drawing,
                                'color': strokeStyle,
                                'id': id,
                                'usernamerem': username,
                                'spessremo': lineWidth,
                                'room': stanza
                            });

                        }
                    }
                    // Draw a line for the current user's movement, as it is
                    // not received in the socket.on('moving') event above
//                    }
                });
                divrubber.on('mouseup mouseleave', function (e) {
                    drawing = false;
                    controlrubber = false;
                });

                divrubber.on('mousemove', function (e) {
//                    if (document.getElementById('controlrubber').checked) {

                    ctx.clearRect(divrubber.position().left, divrubber.position().top + 190, rubbersize + 4, rubbersize + 4);

                    controlrubber = true;

                    socket.emit('rubber', {
                        'x': divrubber.position().left,
                        'y': divrubber.position().top + 190,
                        'id': id,
                        'usernamerem': username,
                        'controlrubber': controlrubber,
                        'width': rubbersize + 4,
                        'height': rubbersize + 4,
                        'room': stanza
                    });
//                    }

                });

                divrubber.on('mousedown', function (e) {
                    // controlrubber= true;
                    drawing = false;
                });

                function resizecanvas() {
                    var imgdata = ctx.getImageData(0, 0, canvas[0].width, canvas[0].height);
                    var w = $('.css-content-math-sheet').width();//window.innerWidth;
                    var h = $('.css-content-math-sheet').height();//window.innerHeight - 0;
                    canvas[0].width = w;//window.innerWidth;
                    canvas[0].height = h;//window.innerHeight - 0;
                    ctx.putImageData(imgdata, 0, 0);
                }

                function getTouchPos(e) {
                    if (!e)
                        var e = event;

                    if (e.touches) {
                        if (e.touches.length == 1) { // Only deal with one finger
                            var touch = e.touches[0]; // Get the information for finger #1
                            //   touchX=touch.clientX-touch.target.offsetLeft;
                            // touchY=touch.clientY-touch.target.offsetTop;
                            touchX = touch.clientX;
                            touchY = touch.clientY;
                        }
                    }
                }

                function drawLine(fromx, fromy, tox, toy) {
                    var strokeStyle = $("input[name='color']:checked").val();
                    var lineWidth = $("input[name='pencil']:checked").val();

                    ctx.strokeStyle = strokeStyle;
                    ctx.lineWidth = lineWidth;
                    ctx.beginPath();
                    ctx.moveTo(fromx, fromy);
                    ctx.lineTo(tox, toy);
                    ctx.stroke();
                }

                function drawLinerem(fromx, fromy, tox, toy, spessore, colorem) {
                    ctx.strokeStyle = colorem;
                    ctx.lineWidth = spessore;
                    ctx.beginPath();
                    ctx.moveTo(fromx, fromy);
                    ctx.lineTo(tox, toy);
                    ctx.stroke();
                    fromx = tox;
                    fromy = toy;
                }

                btn_next_ws();
                btn_preview_ws();
                btn_notepad();
                $('.close-menu').click(function (e) {
                    if ($("#menu-notepad").hasClass('open')) {
                        $("#menu-notepad").css("left", "-200px");
                        $("#menu-notepad").removeClass("open");
                        $('#divrubber').attr("style", "display:none");
                        $("#pencilrubber").attr('data-title', 'Eraser');
                        $('#math').addClass("hidden");
                    }
                });
                // END handing show image and click Next or Preview worksheet  

                $("#modal-view-result-homework").on("hidden.bs.modal", function (e) {
                    e.preventDefault();
                    $("#modal-view-result-homework .modal-dialog").removeAttr("style");
                });
                $("#modal-view-result-homework").on("show.bs.modal", function () {
                    $("#modal-view-result-homework .modal-dialog").removeAttr("style");
                    $("#modal-view-result-homework #load-prevent-modal").addClass("hidden");
                });

                $(".hidden-modal-preview").click(function (e) {
                    e.preventDefault();
                    $("#modal-view-result-homework #load-prevent-modal").addClass("hidden");
                    $("#modal-view-result-homework .modal-dialog").removeAttr("style");
                });
                $("#question-nav").find("li").click(function () {
                    $("#question-nav").find("li").removeClass("active");
                    $("div.flashcard-question").addClass("hidden");
                    $(this).addClass("active");
                    $("#flashcard-q" + $(this).attr("data-n")).removeClass("hidden");
                });

                $("#question-nav").find("li:first").click();
                function turn_speaker(id, state) {
                    var e = document.getElementById(id);
                    if (localStorage.speaker_on == "true" && e != null) {
                        if (state == "off") {
                            e.pause();
                            e.currentTime = 0;
                        } else {
                            e.play();
                        }
                    }
                }
                set_time_ws();
                
            });
            $("#modal-working-worksheet").on("hidden.bs.modal", function (e) {
                var id = $('#sheet-current').html();
                turn_speaker("word-prob-sound-q" + id, "off");
                turn_speaker("word-prob-video-q" + id, "off");
            });

            // Ket thuc viec tai du lieu len modal     
            $(".btn-close-working-ws").click(function (e) {
                e.preventDefault();
                var url = window.location.href;
                if(url.indexOf("&back") == -1){
                    if(url.indexOf("&ismode") == -1) {
                        var string = url.split("&");
                        location.replace(string[0]);
                    }
                    $("#modal-working-worksheet").modal("hide");
                    $('.body-math').addClass("modal-non-overflow");
                }
                else if(url.indexOf("&lvid") !== -1){
                    var str = url.split('&lvid='); 
                    var str1 = str[1].split('&');
                    var lvid = str1[0];
                    var url_home = home_url+'?r=online-learning&lvid='+lvid;
                    location.replace(url_home);
                }else if(url.indexOf("&issat-math=1") !== -1){
                    var str = url.split('&gid='); 
                    var str1 = str[1].split('&');
                    var gid = str1[0];
                    var url_home = home_url+'?r=online-learning&issat-math=1&gid='+gid;
                    location.replace(url_home);
                }else if(url.indexOf("&homeworkagm-math") !== -1){
                    var str = url.split('&gid='); 
                    var str1 = str[1].split('&');
                    var gid = str1[0];
                    if(url.indexOf("&ba-preview-modal") !== -1){
                        var h_str = url.split('&hid='); 
                        var h_str1 = h_str[1].split('&');
                        var hid = h_str1[0];
                        var url_home = home_url+'?r=online-learning&ba-preview-modal&homeworkagm-math&hid='+hid+'&gid='+gid;
                    }else{
                        var url_home = home_url+'?r=online-learning&homeworkagm-math&gid='+gid;
                    }
                    location.replace(url_home);
                }else if(url.indexOf("&homeworkagm-english") !== -1){
                    var str = url.split('&gid='); 
                    var str1 = str[1].split('&');
                    var gid = str1[0];
                    if(url.indexOf("&ba-preview-modal") !== -1){
                        var h_str = url.split('&hid='); 
                        var h_str1 = h_str[1].split('&');
                        var hid = h_str1[0];
                        var url_home = home_url_english+'?r=online-learning&ba-preview-modal&homeworkagm-english&hid='+hid+'&gid='+gid;
                    }else{
                        var url_home = home_url_english+'?r=online-learning&homeworkagm-english&gid='+gid;
                    }
                    location.replace(url_home);
                }else if(url.indexOf("&sat") !== -1){ // sat is check finish
                    var str = url.split('&gid='); 
                    var str1 = str[1].split('&');
                    var gid = str1[0];
                    
                    var sat_str = url.split('&sat='); 
                    var sat_str1 = sat_str[1].split('&');
                    var sat = sat_str1[0];
                    var url_home = home_url+'?r=online-learning&sat='+sat+'&gid='+gid;
                    location.replace(url_home);
                }else{
                    // if &back when close modal back to list ws at online-learning page
                    var str = url.split('&gid='); 
                    var str1 = str[1].split('&');
                    var gid = str1[0];
                    var url_home = home_url+'?r=online-learning&gid='+gid;
                    location.replace(url_home);
                }
            });
            $('#select-math-worksheet-dialog').on("hidden.bs.modal",function(e){
                e.preventDefault();
                $('.body-math').removeClass("modal-non-overflow");
            });
        }
    });
})(jQuery);