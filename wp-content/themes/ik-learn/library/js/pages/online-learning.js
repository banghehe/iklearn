(function ($) {
    $(function () {
        $('#write-message').on('hidden.bs.modal', function () {
            $("#message_ifr").contents().find("#tinymce p").remove();
        });

        $('#modal-view-result-homework').on('hidden.bs.modal', function () {
            $("#modal-view-result-homework").find("#can-scroll").attr('class', '');
        });

        var currentdate = new Date();
        datetime = currentdate.getFullYear() + "-"
                + (currentdate.getMonth() + 1) + "-"
                + currentdate.getDate() + " "
                + currentdate.getHours() + ":"
                + currentdate.getMinutes() + ":"
                + currentdate.getSeconds();
        $('.tab').click(function (e) {
            var id_tab = $(this).attr('data-tab');
            $('.tab').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').hide();
            $('#' + id_tab).show();
        });
        $('.tab1').click(function (e) {
            var id_tab = $(this).attr('data-tab');
            $('.tab1').removeClass('active');
            $(this).addClass('active');
            $('.tab-content1').hide();
            $('#' + id_tab).show();
        });
        $("#join-group").click(function (e) {
            e.preventDefault();
            var a = $('#gname').val();
            var b = $('#gpass').val();
            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: ajaxurl,
                data: {action: "add_foobar", gname: a, gpass: b},
                success: function (data, textStatus, jqXHR) {
//                    document.getElementById('text-join-group').style.color = '#FFFF00';
                    $("#text-join-group").html('This class is subscription only. Monthly $' + data.data + '.' + ' Agree?');
                    $("#join-group-dialog").modal();
                },
            });
        });
        $('.radio input:checkbox').click(function () {
            $('.radio input:checkbox').not(this).prop('checked', false);
        });
        $("#join-group-dialog").on("show.bs.modal", function (e) {
            $("#rdo-yes").prop("checked", true);
        });
        
        $(".leave-grp-btn").click(function (e) {
            var tthis = $(this);
            $("#lev-group-name").text(tthis.attr("data-gname"));
            $("#gid").val(tthis.attr("data-gid"));
            $("#leave-group-dialog").modal();
        });

        $(".request-grading").click(function () {
            $("#grading-cost").text($(this).attr("data-cost"));
            $("#hrid").val($(this).attr("data-hrid"));
            $("#hid").val($(this).attr("data-hid"));
            if (ypoints < parseInt($(this).attr("data-cost"))) {
                $("#request-grading-err").html(JS_MESSAGES.point_err);
            }
            $("#request-grading-dialog").modal();
        });

        $('#icon-accept-writing').click(function () {
            $('#switch-mode-dialog-writing').modal('hide');
        });
        $(".retry-homework").click(function (e) {
            e.preventDefault();
            var a = $('#g-name').val();
            var form = document.getElementById("formsubmit");
            if (annoying == true) {
                var modal = $("#require-modal");
                if (!isuserloggedin) {
                    modal.find("h3").text(JS_MESSAGES.login_req_h);
                    modal.find(".modal-body").html(JS_MESSAGES.login_req_err);
                    modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + JS_MESSAGES.login_req_lbl);
                } else {
                    modal.find("h3").text(JS_MESSAGES.sub_req_h);
                    modal.find(".modal-body").html(JS_MESSAGES.sub_req_err + a + ' to Start');
                    modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + JS_MESSAGES.sub_req_lbl);
                }
                modal.modal();
            } else {

            }
        });
        $("li[id^='li-private']").click(function () {
            if ((window.matchMedia('screen and (max-width: 767px)').matches)) {
                $('.can-scroll').css('height', '350px');
            }
        });
        $("li[id^='li-private-hidden']").click(function () {
            if ((window.matchMedia('screen and (max-width: 767px)').matches)) {
                $('.can-scroll').css('height', '');
            }
        });
        $("li[id^='li-math']").click(function () {
            if ((window.matchMedia('screen and (max-width: 767px)').matches)) {
                $('.can-scroll-math').css('height', '350px');
            }
        });
        $("li[id^='li-math-hidden']").click(function () {
            if ((window.matchMedia('screen and (max-width: 767px)').matches)) {
                $('.can-scroll-math').css('height', '');
            }
        });
        $("p#msg-member").click(function () {
            var height = jQuery('#content')[0].scrollHeight;
            $(window).scrollTop(height / 2);
            $("#members-list-modal").find("tbody").html($(this).next().find("tbody").html());
            $("#members-list-dialog").modal();
        });
        $('#li-private-msg').click(function () {
            $('#menu-msg li:nth-child(4) > a').click();
            var time1 = setTimeout(function () {
                $('#menu-msg li:nth-child(4)').addClass('active');
            }, 100);
        });
        $('#li-private-in-msg').click(function () {
            var time2 = setTimeout(function () {
                $('#menu-msg li:nth-child(2)').addClass('active');
            }, 100);
        });
        $('#li-private-out-msg').click(function () {
            var time3 = setTimeout(function () {
                $('#menu-msg li:nth-child(2)').addClass('active');
            }, 100);
        });
        $("p#msg-write").click(function () {
            thiss = $(this);
            var height = jQuery('#content')[0].scrollHeight;
            $(window).scrollTop(height / 2);
            $('.modal-body-message').html($(this).next().html());
            $('#group_id_post').val($(this).attr('group_id'));
            $('#write-message').find('#body2').mCustomScrollbar({
                theme: "rounded-dark",
                scrollButtons: {enable: true}
            });
            $('.modal-header-write-msg #group-name').html('Group Name: ' + $(this).attr('group-name'));
            $('#write-message').modal();
            $('#write-message').find('#body2').mCustomScrollbar({
                theme: "rounded-dark",
                scrollButtons: {enable: true}
            });

        });
        var count = $('#count-new-message').text();
        $('#text-new-msg').text(' ' + count);
        $('.post-reply').click(function (e) {
            e.preventDefault();
            $.get(home_url + "/?r=ajax/groupmessage", {group_id: $('#group_id_post').val(), message: jQuery('#write-message').find('iframe').contents().find('#tinymce').html(), date: datetime}, function (data) {
                if (data.length != 0) {
                    data = JSON.parse(data);
                    for (i = $('#write-message').find('.count-new-msg').val(); i < data.length; i++) {
                        var key = parseInt(i) + 1;
                        html = '<div class="box box-gray-custom post-block-custom" id="id_' + key + '">';
                        html += '<span style="right: 3%;padding-top: 2%;"  class="close remove-dialog"></span>';
                        html += '<div class="post-header">';
                        html += '<span class="post-num">' + key + ') </span>';
                        html += '<span class="post-author">' + data[i]['poster'] + '</span>';
                        html += '<span class="post-date">' + data[i]['posted_on'] + '</span></div>';
                        html += '<div class="post-content">' + data[i]['message'] + '</div></div>';
                        $('#write-message').find('.posts-message-modal').prepend(html);
                        thiss.next().find('.posts-message-modal').prepend(html);
                        $('#write-message').find('.count-new-msg').attr("value", key);
                        thiss.next().find('.count-new-msg').attr("value", key);
                        thiss.parent().prev().html(key);
                        jQuery('#write-message').find('#body2').mCustomScrollbar('scrollTo', '0');
                    }
                    var x = document.getElementById("snackbar")
                    x.className = "show";
                    setTimeout(function () {
                        x.className = x.className.replace("show", "");
                    }, 3000);
                    $("#message_ifr").contents().find("#tinymce p").remove();
                }
            });
        });

        $('.read-msg').click(function (e) {
            e.preventDefault();
            var height = jQuery('#content')[0].scrollHeight;
            $(window).scrollTop(height / 2);
            id = $(this).attr('data-id');
            $.get(home_url + "/?r=ajax/getsentmsg", {id: id}, function (data) {
                data = JSON.parse(data);
                $('#box_msg_sent').html(data);
            });
            $('#menu-msg-private > li:first > a').click();
        });

        $('.btn_scroll').click(function (e) {
            e.preventDefault();
            $("#received_msg").modal("show");
            var date = $(this).attr('data-date');
            var id = $(this).attr('data-id');
            var status = $(this).attr('data-status');
            var height = jQuery('#content')[0].scrollHeight;
            $(window).scrollTop(height / 2);
            id = $(this).attr('data-id');
            $.get(home_url + "/?r=ajax/getreceivedmsg", {id: id}, function (data) {
                data = JSON.parse(data);
                if (data.trim() == "") {
                    html = '<b>Subject of the Message</b><hr>';
                    html += date + '<br><br>';
                    html += 'This is testing mail to all classes I am currently in.';
                    html += 'I beleive this is your first time joining this classroom right? We have many great study programs by using online program tool.';
                    html += 'You can expect lots of worksheets and make friends from this class! I am so excited to introduce this class and I am sure you will love all the lessons from here.<br>';
                    html += '<br>If you want to keep this class, you can simply stay, otherwise you can leave any time you want.<br>';
                    html += '<br>If you have any question feel free to ask me anytime!';
                    html += '<br><br><br>Thank you!';
                    html += '<input type="hidden" id="id" name="id" value="' + id + '">'
                    html += '<input type="hidden" id="status" value="' + status + '">'
                    $('#box-msg').html(html);

                } else {
                    data += '<input type="hidden" id="id" name="id" value="' + id + '">'
                    data += '<input type="hidden" id="status" name="status" value="' + status + '">'
                    $('#box-msg').html(data);
                }
            });
            $('#menu-msg-private > li:first > a').click();
        });

        $('.tutor-link').click(function (e) {
            e.preventDefault();
            $('#tutor-evaluation').modal('show');
            var id = $(this).attr("data-id");
            $('#idchat').val(id);
//            document.getElementById('txt_id_disable').innerHTML = $data_id;
        });

        $(document).on("click", ".view-result-hw", function (e) {
            e.preventDefault();
            id = $(this).attr('data-id');
            $.get(home_url + "/?r=ajax/get_result_hw", {hw_id: id}, function (data) {
                data = JSON.parse(data);
                jQuery('#modal-homework #level-hw').html(data[0]['grade'])
                if (data[0]['assignment_id'] == ASSIGNMENT_WRITING) {
                    jQuery('#modal-homework #score-hw').html(data[0]['score'])
                } else {
                    jQuery('#modal-homework #score-hw').html(data[0]['correct_answers_count'] + 'correct, ' + data[0]['score'] + '%')
                }
                jQuery('#modal-homework #la-hw').html(data[0]['attempted_on'])
                jQuery('#modal-homework #cd-hw').html(data[0]['submitted_on'])
                jQuery('#modal-homework #l-hw').html(data[0]['sheet_name'])
                jQuery('#modal-homework').modal();
            });
        });

        $(document).on("click", ".remove-dialog", function (e) {
            e.preventDefault();
            id_remove = $(this).closest('div').attr('id');
            $(this).closest('div').remove();
            thiss.next().find('#' + id_remove).remove();
            $.post(home_url + "/?r=ajax/groupmessageremove", {id: $(this).attr('data_id')}, function (data) {
                var x = document.getElementById("snackbar");
                x.innerHTML = "You've remove message";
                x.className = "show";
                setTimeout(function () {
                    x.className = x.className.replace("show", "");
                }, 2000);
            });
        });
// Kết quả view modal và link chia làm 2 loại viết và không phải là viết (bao gồm nhiều lựa chọn và math)
// Kiểm tra là viết ta check assignment_id == 4
        $(document).on("click", ".view-result-none-writing", function (e) {
            e.preventDefault();
            var url = $(this).attr("data-practice-url");
            var url_preview = $(this).attr("data-practice-url-preview");
            var id = $(this).attr('id');
            $('#btn-ok-rs-homework').attr("data-url", url);
            $('#btn-ok-rs-homework').attr("hid", id);
            var id_group = $(this).attr('id_g');
            var status = $(this).attr('data-status');
            var mode = $(this).attr('data-mode');
            $.get(home_url + "/?r=ajax/view_result_non_writing", {hw_id: id, st: status, id_g: id_group, mode: mode}, function (data) {
                $('#modal-view-result-homework #can-scroll').html(data);
                $('#modal-view-result-homework').modal({backdrop: 'static', keyboard: false});
                jQuery(".scroll-list").mCustomScrollbar({
                    theme: "rounded-dark",
                    mouseWheel: {scrollAmount: 120},
                    callbacks: {
                        onOverflowY: function () {
                            jQuery(this).parents(".box").css("padding-right", "10px");
                            jQuery(this).parents(".box").find(".row.grid-table-head").css("padding-right", "30px");
                        },
                        onOverflowYNone: function () {
                            jQuery(this).parents(".box").css("padding-right", "");
                            jQuery(this).parents(".box").find(".row.grid-table-head").css("padding-right", "");
                        }
                    }
                });
                $('#preview-btn-math').click(function () {
                    window.location.replace(url_preview);
                });
            });
        });
        
        if (window.location.href.indexOf("&ba-preview-modal") > -1) { // handing preview modal and back
            var h = window.location.href.split('&hid=');
            var h_str = h[1].split('&');
            var id = h_str[0];
            $('#'+id).trigger('click');
        }  
        
        $(document).on("click", ".view-result-hw-wr", function () {
            jQuery('#modal-view-result-homework').modal({backdrop: 'static', keyboard: false});
        });
        
        $(document).ready(function () {
            $('#mybutton').trigger('click');
        });
        
        $(function () {
            var url = window.location.href;
            var id = url.split('&bid=');
            if (id[1] == '1') {
                $(".cs-options ul").find("[data-value='homeworkfrom']").addClass('cs-selected');
                jQuery('#loadhomework').html(a2);
                if (window.location.href.indexOf("&crit-math") > -1) {
                    $('.cs-select .cs-placeholder').html('Critical Math Subjects');
                } else {
                    $('.cs-select .cs-placeholder').html('Critical English Subjects');
                }
            }
        });
        jQuery(".cs-options ").on("click", "li", (function () {
            jQuery('.cs-select .cs-options').css("visibility", "");
            jQuery('.cs-skin-border .cs-options').css("opacity", "");
            jQuery('.cs-select').first().removeClass("cs-active");

            var selected = jQuery('.cs-options li.cs-selected').attr('data-value');
            if (selected == 'homeworkagm') {
                jQuery('#loadhomework').html(a1);
                jQuery('#loadhomework').css("display", "block");
                jQuery('#view-result').css("display", "none");
                jQuery('#view-result-wrid').css("display", "none");
                jQuery('#loadhomework_group').css("display", "none");
                jQuery('.cs-options').removeClass('visible')
                jQuery('#span-icon').addClass('span-icon-down');
                jQuery('#span-icon').removeClass('span-icon-up');
            } else if (selected == 'homeworkfrom') {
                jQuery('#loadhomework').html(a2);
                jQuery('#loadhomework').css("display", "block");
                jQuery('#view-result').css("display", "none");
                jQuery('#loadhomework_group').css("display", "none");
                jQuery('#view-result-wrid').css("display", "none");
                jQuery('.cs-options').removeClass('visible')
                jQuery('#span-icon').addClass('span-icon-down');
                jQuery('#span-icon').removeClass('span-icon-up');
            } else if (selected == 'sattest') {
                jQuery('#loadhomework').html(a3);
                jQuery('#loadhomework').css("display", "block");
                jQuery('#view-result').css("display", "none");
                jQuery('#loadhomework_group').css("display", "none");
                jQuery('#view-result-wrid').css("display", "none");
                jQuery('.cs-options').removeClass('visible')
                jQuery('#span-icon').addClass('span-icon-down');
                jQuery('#span-icon').removeClass('span-icon-up');

                // Handing SAT Preration and Sat Practive on math
                $('.renew-btn-default-math').click(function () {
                    $('#sub-modal-math-sat').modal('show');
                    var sub_type = $(this).attr('data-subscription-type');
                    var data_type = $(this).attr('data-type');
                    var class_type = $(this).attr('class-type');
                    $('#ok-modal-sub-sat').attr('sub-type', sub_type);
                    $('#ok-modal-sub-sat').attr('data-type', data_type);
                    $('#ok-modal-sub-sat').attr('class-type', class_type);
                });

                $('.start-btn-default-math').click(function () {
                    var text_class = $(this).attr('data-sat-class');
                    $('#sub-modal-math-sat-new').modal('show');
//                    $('#sub-modal-math-sat-new .modal-body').html('Please subscribe '+ text_class + ' to start');
                    $('#sub-modal-math-sat-new .modal-body').html('Your subscription has expired. Please subscribe to SAT I Preparation to start.');
                    var sub_type = $(this).attr('data-subscription-type');
                    var data_type = $(this).attr('data-type');
                    var class_type = $(this).attr('class-type');
                    $('#ok-modal-sub-sat-new').attr('sub-type', sub_type);
                    $('#ok-modal-sub-sat-new').attr('data-type', data_type);
                    $('#ok-modal-sub-sat-new').attr('class-type', class_type);
                });
                $('#ok-modal-sub-sat-new').click(function () {
                    $('#sub-modal-math-sat-new').modal('hide');
                    var id = $(this).attr('sub-type');
                    var sub_id = $(this).attr('data-type');
                    if (sub_id == 9) {
                        $('#sat1-preparation-subscrible-modal').modal("show");
                    } else if (sub_id == 15) {
                        $('#sat2-preparation-subscrible-modal').modal("show");
                    } else if (sub_id > 9 && sub_id < 15) {
                        $('#sat1-subscrible-modal').modal('show');
                        $("#select-sat1-class").selectBoxIt('selectOption', sub_id.toString()).data("selectBox-selectBoxIt");
                        $("#select-sat1-class").data("selectBox-selectBoxIt").refresh();
                    } else if (sub_id > 15 && sub_id < 21) {
                        $('#sat2-subscrible-modal').modal('show');
                        $("#select-sat2-class").selectBoxIt('selectOption', sub_id.toString()).data("selectBox-selectBoxIt");
                        $("#select-sat2-class").data("selectBox-selectBoxIt").refresh();
                    }
                });
                $('#ok-modal-sub-sat').click(function () {
                    $('#sub-modal-math-sat').modal('hide');
                    var id = $(this).attr('sub-type');
                    var sub_id = $(this).attr('data-type');
                    if (sub_id == 9) {
                        $('#sat1-preparation-subscrible-modal').modal("show");
                    } else if (sub_id == 15) {
                        $('#sat2-preparation-subscrible-modal').modal("show");
                    } else if (sub_id > 9 && sub_id < 15) {
                        $('#sat1-subscrible-modal').modal('show');
                        $("#select-sat1-class").selectBoxIt('selectOption', sub_id.toString()).data("selectBox-selectBoxIt");
                        $("#select-sat1-class").data("selectBox-selectBoxIt").refresh();
                    } else if (sub_id > 15 && sub_id < 21) {
                        $('#sat2-subscrible-modal').modal('show');
                        $("#select-sat2-class").selectBoxIt('selectOption', sub_id.toString()).data("selectBox-selectBoxIt");
                        $("#select-sat2-class").data("selectBox-selectBoxIt").refresh();
                    }
                });
                function calc_sat2_price() {
                    var months = parseInt($("#select-sat2-months").val());
                    $("#total-amount-sat2-class").text(parseInt(months * satMIIP));
                }
                $('#sat2-subscrible-modal').on('show.bs.modal', function () {
                    calc_sat2_price();
                    var sat2_class = $('#select-sat2-class').val();
                    $('#sat1-class').val(sat2_class);
                });
                $('#select-sat2-class').change(function () {
                    var sat2_class = $('#select-sat2-class').val();
                    $('#sat2-class').val(sat2_class);
                    calc_sat2_price();
                });
                $('#select-sat1-class').change(function () {
                    var sat1_class = $('#select-sat1-class').val();
                    $('#sat1-class').val(sat1_class);
                    calc_sat1_price();
                });
                function calc_sat1_price() {
                    var months = parseInt($("#select-sat1-months").val());
                    $("#total-amount-sat1-class").text(parseInt(months * satMIP))
                }
                $('#sat1-subscrible-modal').on('show.bs.modal', function () {
                    calc_sat1_price();
                    var sat1_class = $('#select-sat1-class').val();
                    $('#sat1-class').val(sat1_class);
                });
                function calc_sat1_preparation_price() {
                    var months = parseInt($("#select-sat1-preparation-months").val());
                    $("#total-amount-sat1-preparation").text(parseInt(months * sat1Pre));
                }
                $('#sat1-preparation-subscrible-modal').on('show.bs.modal', function () {
                    calc_sat1_preparation_price();
                });
                $('#select-sat1-preparation-months').change(function () {
                    calc_sat1_preparation_price();
                });
                $('#sat2-preparation-subscrible-modal').on('show.bs.modal', function () {
                    calc_sat2_preparation_price();
                });
                function calc_sat2_preparation_price() {
                    var months = parseInt($("#select-sat2-preparation-months").val());
                    $("#total-amount-sat2-preparation").text(parseInt(months * sat2Pre));
                }
                $('#select-sat2-preparation-months').change(function () {
                    calc_sat2_preparation_price();
                });
                $("#sub-grammar-modal").click(function(){
                    $("#grammar-subscription-dialog").modal("show");
                });
                $('#grammar-subscription-dialog').on('show.bs.modal',function(){
                    $("#sel-grammar-months").selectBoxIt('selectOption',"1").data("selectBox-selectBoxIt");
                    $("#sel-grammar-months").data("selectBox-selectBoxIt").refresh();
                    $("#total-amount-grammar").html(satGp);
                });
                $("#sel-grammar-months").change(function () {
                    var month = $(this).val();
                    $("#total-amount-grammar").html(month * satGp);
                });
                $("#sub-writing-modal").click(function(){
                    $("#writing-subscription-dialog").modal("show");
                });
                $('#writing-subscription-dialog').on('show.bs.modal',function(){
                    $("#sel-writting-months").selectBoxIt('selectOption',"1").data("selectBox-selectBoxIt");
                    $("#sel-writting-months").data("selectBox-selectBoxIt").refresh();
                    $("#total-amount-writting").html(satWp);
                });
                $("#sel-writting-months").change(function () {
                    var month = $(this).val();
                    $("#total-amount-writting").html(month * satWp);
                });
                $(".btn-sub-sat-eng").click(function(){
                    $("#sat-english-subscription-dialog").modal("show");
                    var sat_id = $(this).attr("data-sat");
                    $("#sat-english-class-sub").selectBoxIt('selectOption',sat_id).data("selectBox-selectBoxIt");
                    $("#sat-english-class-sub").data("selectBox-selectBoxIt").refresh();
                });
                $('#sat-english-subscription-dialog').on('show.bs.modal',function(){
                    $("#sel-sat-english-months").selectBoxIt('selectOption',"1").data("selectBox-selectBoxIt");
                    $("#sel-sat-english-months").data("selectBox-selectBoxIt").refresh();
                    $("#total-amount-sat-english").html(satStp);
                });
                $("#sel-sat-english-months").change(function () {
                    var month = $(this).val();
                    $("#total-amount-sat-english").html(month * satStp);
                });
                $("#sat-english-class-sub").change(function () {
                    $("#sat-english-subscription-dialog #sat-class").val($(this).val());
                });
                
            } else if (selected == 'tutoring_plan') {
                jQuery('#loadhomework').html(a5);
                jQuery('#loadhomework').css("display", "block");
                jQuery('#view-result').css("display", "none");
                jQuery('#loadhomework_group').css("display", "none");
                jQuery('#view-result-wrid').css("display", "none");
                jQuery('.cs-options').removeClass('visible')
                jQuery('#span-icon').addClass('span-icon-down');
                jQuery('#span-icon').removeClass('span-icon-up');
                var id = 0;
//                var id = $('#select-schedule-tutoring').val();
                $('#load-schedule-tutoring-select').empty();
                $.post(home_url + "/?r=ajax/load_tutoring_plan", {'id_schedule': id}, function (result) {
                    $('#load-schedule-tutoring-select').html(result);
                });
                $("ul").on("click", ".init", function () {
                    $(this).closest("ul").children('li:not(.init)').toggle();
                });
                $('#li-math4-private').on('click', function (e) {
                    $('#tutoring-id-plan').removeClass("hidden");
                    $('#ikmath-tutoring').addClass("hidden");
                });
                $('#li-math5-private').on('click', function (e) {
                    $('#ikmath-tutoring').removeClass("hidden");
                    $('#tutoring-id-plan').addClass("hidden");
                });
                var allOptions = $("#test").children('li:not(.init)');
                $("#test").on("click", "li:not(.init)", function () {
                    allOptions.removeClass('selected');
                    $(this).addClass('selected');
                    $("#test").children('.init').html($(this).html());
                    allOptions.toggle();
                })
                // Tutoring plan
                $('.click-test').click(function (e) {
                    var id = $(this).attr('data-value');
                    $('#load-schedule-tutoring-select').empty();
//            alert(id);
                    $.post(home_url + "/?r=ajax/load_tutoring_plan", {'id_schedule': id}, function (result) {
                        $('#load-schedule-tutoring-select').html(result);
                        $(".class-detail-btn").click(function (e) {
                            e.preventDefault();
                            var modal = $("#class-detail-modal");
                            modal.find(".modal-body").html($(this).next().html());
                            modal.modal();
                        });
                        $(".start-class-btn").click(function (e) {
                            e.preventDefault();
                            attr = $(this);
                            if (!annoying) {
                                $("#jid").val($(this).attr("data-jid"));
                                $("#main-form").submit();
                            } else {
                                var modal = $("#require-modal");
                                if (!isuserloggedin) {
                                    modal.find("h3").text(JS_MESSAGES.login_req_h);
                                    modal.find(".modal-body").html(JS_MESSAGES.login_req_err);
                                    modal.find(".modal-footer button").html(JS_MESSAGES.login_req_lbl);
                                } else {
                                    modal.find('#sub-modal').attr('data-sat-class', attr.attr('data-sat-class'));
                                    modal.find('#sub-modal').attr('data-subscription-type', attr.attr('data-subscription-type'));
                                    modal.find('#sub-modal').attr('data-type', attr.attr('data-type'));
                                    modal.find("h3").text(JS_MESSAGES.sub_req_h);
                                    modal.find(".modal-body").html(JS_MESSAGES.sub_req_err);
                                    modal.find(".modal-footer button").html(JS_MESSAGES.sub_req_lbl);
                                }
                                modal.modal();
                            }
                        });
                    });
                });
            } else if (selected == 'ikmath_courses') {
                jQuery('#loadhomework').html(a4);
                jQuery('#loadhomework').css("display", "block");
                jQuery('#view-result').css("display", "none");
                jQuery('#view-result-wrid').css("display", "none");
                jQuery('#loadhomework_group').css("display", "none");
                jQuery('.cs-options').removeClass('visible')
                jQuery('#span-icon').addClass('span-icon-down');
                jQuery('#span-icon').removeClass('span-icon-up');
                $('#load-class-math-follow-select').empty();
                var text_ikmath = $('#test-ikmath .init');
                var name = text_ikmath.html();
                var array_ikmath = {38: 'Math Kindergarten', 39: 'Math Grade 1', 40: 'Math Grade 2', 41: 'Math Grade 3', 42: 'Math Grade 4', 43: 'Math Grade 5', 44: 'Math Grade 6', 45: 'Math Grade 7', 46: 'Math Grade 8', 47: 'Math Grade 9', 48: 'Math Grade 10', 49: 'Math Grade 11', 50: 'Math Grade 12'};
                $.each(array_ikmath, function (key, value) {
                    if (value == name) {
                        id_name = key;
                    }
                });
                if (text_ikmath.html() == "Please Subscribe") {
                    $.post(home_url + "/?r=ajax/load_class_ikmath_course", {'id_class_selected': 0}, function (result) {
                        $('#load-class-math-follow-select').html(result);
                        $(".class-detail-btn").click(function (e) {
                            e.preventDefault();
                            var modal = $("#class-detail-modal");
                            modal.find(".modal-body").html($(this).next().html());
                            modal.modal();
                        });
                    });
                } else {
                    $.post(home_url + "/?r=ajax/load_class_ikmath_course", {'id_class_selected': id_name}, function (result) {
                        $('#load-class-math-follow-select').html(result);
                        $(".class-detail-btn").click(function (e) {
                            e.preventDefault();
                            var modal = $("#class-detail-modal");
                            modal.find(".modal-body").html($(this).next().html());
                            modal.modal();
                        });
                    });
                }
                $('.click-test-ikmath').click(function (e) {
                    var id = $(this).attr('data-value');
                    $('#load-class-math-follow-select').empty();
                    $.post(home_url + "/?r=ajax/load_class_ikmath_course", {'id_class_selected': id}, function (result) {
                        $('#load-class-math-follow-select').html(result);
                        $(".class-detail-btn").click(function (e) {
                            e.preventDefault();
                            var modal = $("#class-detail-modal");
                            modal.find(".modal-body").html($(this).next().html());
                            modal.modal();
                        });
                    });
                });
                $("#test-ikmath").on("click", ".init", function () {
                    $(this).closest("#test-ikmath").children('li:not(.init)').toggle();
                });

                var allOptions = $("#test-ikmath").children('li:not(.init)');
                $("#test-ikmath").on("click", "li:not(.init)", function () {
                    allOptions.removeClass('selected');
                    $(this).addClass('selected');
                    $("#test-ikmath").children('.init').html($(this).html());
                    allOptions.toggle();
                })
                var id = $('#select-class-math-course').val();
                $('#load-class-math-follow-select').empty();
                $(".start-class-btn").click(function (e) {
                    e.preventDefault();
                    attr = $(this);
                    if (!annoying) {
                        $("#jid").val($(this).attr("data-jid"));
                        $("#main-form").submit();
                    } else {
                        var modal = $("#require-modal");
                        if (!isuserloggedin) {
                            modal.find("h3").text(JS_MESSAGES.login_req_h);
                            modal.find(".modal-body").html(JS_MESSAGES.login_req_err);
                            modal.find(".modal-footer button").html(JS_MESSAGES.login_req_lbl);
                        } else {
                            modal.find('#sub-modal').attr('data-sat-class', attr.attr('data-sat-class'));
                            modal.find('#sub-modal').attr('data-subscription-type', attr.attr('data-subscription-type'));
                            modal.find('#sub-modal').attr('data-type', attr.attr('data-type'));
                            modal.find("h3").text(JS_MESSAGES.sub_req_h);
                            modal.find(".modal-body").html(JS_MESSAGES.sub_req_err);
                            modal.find(".modal-footer button").html(JS_MESSAGES.sub_req_lbl);
                        }
                        modal.modal();
                    }
                });
//                });

            }
            $(".class-detail-btn").click(function (e) {
                e.preventDefault();
                var modal = $("#class-detail-modal");
                modal.find('.modal-body').html($(this).next().html());

                modal.modal();
            });

            $(".view-score, .view-score-default").click(function (e) {
                e.preventDefault();
                $("#table-score").find("tbody").html($(this).next().find("tbody").html());
                $("#view-score-modal").modal();
            });
            $(".start-class-btn, .start-class-btn-default").click(function (e) {
                e.preventDefault();
                attr = $(this);
                if ($(this).attr("data-annoying") == 'false') {
                    $("#jid").val($(this).attr("data-jid"));
                    $('#cltid').val($(this).attr("data-cltid"));
                    $("#main-form").submit();
                } else {
                    var modal = $("#require-modal");
                    if (!isuserloggedin) {
                        modal.find("h3").text(JS_MESSAGES.login_req_h);
                        modal.find(".modal-body").html(JS_MESSAGES.login_req_err);
                        modal.find(".modal-footer button").html(JS_MESSAGES.login_req_lbl);
                    } else {
                        modal.find('#sub-modal').attr('data-sat-class', attr.attr('data-sat-class'));
                        modal.find('#sub-modal').attr('data-subscription-type', attr.attr('data-subscription-type'));
                        modal.find('#sub-modal').attr('data-type', attr.attr('data-type'));
                        modal.find(".modal-body").html(JS_MESSAGES.sub_req_err_sat);
                        modal.find(".modal-footer button").html(JS_MESSAGES.sub_req_lbl);
                    }
                    modal.modal();
                }
            });

            $(".working-btn, .working-btn-default").click(function (e) {
                var modal = $("#require-modal");
                attr = $(this);
                if (!isuserloggedin) {
                    modal.find("h3").text(JS_MESSAGES.login_req_h);
                    modal.find(".modal-body").html(JS_MESSAGES.login_req_err);
                    modal.find(".modal-footer button").html(JS_MESSAGES.login_req_lbl);
                } else {
                    modal.find('#sub-modal').attr('data-sat-class', attr.attr('data-sat-class'));
                    modal.find('#sub-modal').attr('data-subscription-type', attr.attr('data-subscription-type'));
                    modal.find('#sub-modal').attr('data-type', attr.attr('data-type'));
                    modal.find("h3").text(JS_MESSAGES.sub_req_h);
                    modal.find(".modal-body").html('Your subscription has expired. Please subscribe to SAT I Preparation to start.');
                    modal.find(".modal-footer button").html(JS_MESSAGES.sub_req_lbl);
                }
                modal.modal();
            });
        }));
//
//        jQuery(".homeworkcritical-online").mCustomScrollbar({
//            axis: "yx",
//            theme: "rounded-dark",
//            scrollButtons: {enable: true}
//        });
        String.prototype.replaceAt = function (index, character) {
            return this.substr(0, index) + character + this.substr(index, character.length);
        }
        var url = $("#cartoonVideo").attr('src');
        jQuery(".view-result").mCustomScrollbar({
            theme: "rounded-dark",
            scrollButtons: {enable: true}
        });
        jQuery("#div-text").click(function (e) {
            if (jQuery('.cs-options').hasClass('visible')) {
                jQuery('.cs-select .cs-options').css("visibility", "");
                jQuery('.cs-skin-border .cs-options').css("opacity", "");
                jQuery('.cs-options').removeClass('visible')
                jQuery('#span-icon').addClass('span-icon-down');
                jQuery('#span-icon').removeClass('span-icon-up');
            } else {
                jQuery('.cs-select .cs-options').css("visibility", "visible");
                jQuery('.cs-skin-border .cs-options').css("opacity", "1");
                jQuery('.cs-options').addClass('visible')
                jQuery('#span-icon').addClass('span-icon-up');
                jQuery('#span-icon').removeClass('span-icon-down');
            }
        });
        jQuery('#li-private-hidden2').click(function () {
            var $this = jQuery(this),
                    clickNum = $this.data('clickNum');
            if (!clickNum) {
                clickNum = 1;
            }
            if (clickNum == 1) {
                if ($('li.li-private-1').hasClass('active')) {
                    jQuery('#li-private-hidden2').addClass('active')
                }
            }
            if (clickNum % 2 == 0) {
                setTimeout(function () {
                    jQuery('*[id^="li-private"]').removeClass('active')
                    jQuery('*[id^="menu-message"]').removeClass('active in')
                    jQuery('#li-private-hidden1').addClass('active')
                    jQuery('#menu-message-icon').addClass('active in')
                }, 200);
            } else if (clickNum == 1) {
                if ($('#li-private-1').hasClass('active') || $('#li-private-msg').hasClass('active') || $('#li-private-hidden1').hasClass('active')) {
                    jQuery('#menu-message-received').removeClass('active in');
                }
            } else {
                setTimeout(function () {
                    jQuery('#li-private-1').addClass('active')
                    jQuery('#menu-message-homemessage').addClass('active in')
                }, 200);
            }
            $this.data('clickNum', ++clickNum);
        });
        jQuery('#li-math-hidden').click(function () {
            var $this = jQuery(this),
                    clickNum = $this.data('clickNum');
            if (!clickNum) {
                clickNum = 1;
            }
            if (clickNum % 2 == 0) {

                setTimeout(function () {
                    jQuery('*[id^="li-math"]').removeClass('active')
                    jQuery('*[id^="ikmath"]').removeClass('active in')
                    jQuery('#li-math-hidden-1').addClass('active')
                    jQuery('#ikmath-icon-1').addClass('active in')
                }, 50);
            } else {
                setTimeout(function () {
                    jQuery('#li-math4-private').addClass('active')
                    jQuery('#ikmath-sub').addClass('active in')
                }, 50);
            }
            $this.data('clickNum', ++clickNum);
        });
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
        function calc_self_study_price() {
            var months = parseInt($("#sel-self-study-months").val());
            $("#ss-total-amount").text(months * ssp);
        }
        function calc_self_study_price_math() {
            var months = parseInt($("#sel-self-study-months").val());
            $("#ss-total-amount").text(months * ssp_math);
        }
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
        $("#english-detail-list, #math-detail-list").click(function (e) {
            $("#self-study-detail,#teacher-homework-tool-dialog").modal("hide").one("hidden.bs.modal", function () {
                $("#self-study-detail-list").modal();
            });
            $(".logged-in").css('padding-right', '0');
            $("#self-study-detail-list").modal().one();
        });
        $(".choose-sub-btn").click(function () {
            $('#require-modal').modal('hide');
            calc_sat_total_price();
            var tthis = $(this);
            var t = parseInt(tthis.attr('data-subscription-type'));
            $("#sub-id").val(0);
            $("#student_num").prop("disabled", false);
            $("#sel-dictionary").data("selectBox-selectBoxIt").enable().selectOption(0);
            $("#sel-teacher-tool").data("selectBox-selectBoxIt").enable().selectOption(0);
            $("#num-of-months-lbl").text(LBL_NO_M);
            $("#addi-sub-type").val(t);
//            console.log('sub-type = ' + t);
            switch (t) {
                case 1:
                case 6:
                    $("#teacher-homework-tool-dialog").modal("hide").one("hidden.bs.modal", function () {
                        $("#teacher-sub-details-dialog").modal();
                    });
                    $(".logged-in").css('padding-right', '0');

                    $("#teacher-sub-details-dialog").modal().one();
                    break;
                case 2:
                    var $modal = $("#additional-subscription-dialog-continue");
                    var $modal_title = $modal.find(".modal-header h3");
                    $modal_title.text($modal_title.attr("data-ds-text"));
                    $("#selected-group-label").hide();
                    $("#num-of-student-lbl").text(LBL_NO_USERS);
                    $("#student_num").val(1).attr("min", 1);
                    $("#merriam-webster-dictionary").modal("hide").one("hidden.bs.modal", function () {
                        calc_total_price();
                        $modal.modal();
                    });
                    $(".logged-in").css('padding-right', '0');
                    if ($('#sel-dictionary option:selected').val() == '') {
                        $("#total-amount").text(0);
                    } else {
                        calc_total_price();
                    }
                    $modal.modal().one();
                    break;
                case 3:
                case 7:
                case 8:
                case 12:
                    var typeid = parseInt(tthis.attr("data-type"));
                    $("#sat-sub-type").val(t);
                    switch (typeid) {
                        case 3:
                            $("#sat-test-block").show();
                            $("#sat-practice-test").modal('hide');
                            break;
                        case 10:
                            $("#sat-test-i-block").show();
                            $("#sat-test-ii-block").hide();
                            $("#ik-test-class-block").hide();
                            break;
                        case 16:
                            $("#sat-test-ii-block").show();
                            $("#sat-test-i-block").hide();
                            $("#ik-test-class-block").hide();
                            break;
                        case 38:
                            $("#ik-test-class-block").show();
                            $("#sat-test-i-block").hide();
                            $("#sat-test-ii-block").hide();
                            break;
                        default:
                            $("#sat-test-block").hide();
                            $("#sat-test-i-block").hide();
                            $("#sat-test-ii-block").hide();
                            $("#ik-test-class-block").hide();
                            break;
                    }
                    $("#sat-class").val(typeid);
                    $("#sat-class").val(typeid);
                    $("#selected-class").text(tthis.attr("data-sat-class"));
                    $("#sel-sat-months").data("selectBox-selectBoxIt").selectOption(0);
                    $("#grammar-review, #writing-practice,#ikmath-classes,#sat-i-preparation, #sat-ii-preparation, #sat-ii-simulated-test, #sat-ii-simulated-test-new").modal("hide").one("hidden.bs.modal", function () {
                        calc_sat_total_price();
                        $("#sat-subscription-dialog").modal();
                    });
                    $(".logged-in").css('padding-right', '0');
                    $("#sat-subscription-dialog").modal().one();
                    break;
                case 4:
                    $("#purchase-points").modal("hide").one("hidden.bs.modal", function () {
                        calc_self_study_price();
                        $("#purchase-points-dialog").modal();
                    });
                    $("#purchase-points-dialog").modal().one();
                    $(".logged-in").css('padding-right', '0');
                    break;
                case 5:
                    $("#ss-dict-block").show();
                    $("#self-study-detail").modal("hide").one("hidden.bs.modal", function () {
                        calc_self_study_price();
                        $("#self-study-subscription-dialog").modal();
                    });
                    $(".logged-in").css('padding-right', '0');

                    $("#self-study-subscription-dialog").modal().one();
                    $(".logged-in").css('padding-right', '0');
                    if ($('#sel-dictionary2 option:selected').val() == '') {
                        $("#ss-total-amount").text(0);
                    } else {
                        calc_self_study_price();
                    }
                    break;
                case 9:
                    $("#ss-dict-block").hide();
                    $("#self-study-detail").modal("hide").one("hidden.bs.modal", function () {
                        calc_self_study_price();
                        $("#self-study-subscription-dialog").modal();
                    });
                    calc_self_study_price_math();
                    $("#self-study-subscription-dialog").modal().one();
                    $(".logged-in").css('padding-right', '0');
                    break;
            }
        });
        if (window.location.href.indexOf("check") > -1)
        {
            var height = jQuery('#content')[0].scrollHeight;
            $(window).scrollTop(height / 2);
            $('#li-private-msg').click()
        }
        if (window.location.href.indexOf("received") > -1)
        {
            var height = jQuery('#content')[0].scrollHeight;
            $(window).scrollTop(700);
            $('#li-private-msg').click();
        }

        $("#add_tutor_evaluation").click(function (e) {
            e.preventDefault();
            var id = $('#idchat').val();//$(this).attr("data-id");
            var txt_eval = $('#txt_evaluation').val();
            if (id != '' && txt_eval != '')
            {
                $.post(home_url + "/?r=ajax/update_evaluation", {id: id, txt_eval: txt_eval}, function (data) {
                    if (data) {
                        $('#tutor-evaluation').modal('hide');
                        $("#evaluation_ok").modal("show");
                    } else
                        alert('Update error');
                });
            } else {
                $('#tutor-evaluation').modal('hide');
                $("#evaluation_error").modal("show");
            }
        });
        // ikmath course
        $(document).on("change", "#select-class-math-course", function (e) {
            e.preventDefault();
            var id = $('#select-class-math-course').val();
            $('#load-class-math-follow-select').empty();
            $.post(home_url + "/?r=ajax/load_class_ikmath_course", {'id_class_selected': id}, function (result) {
                $('#load-class-math-follow-select').html(result);
                $(".class-detail-btn").click(function (e) {
                    e.preventDefault();
                    var modal = $("#class-detail-modal");
                    modal.find(".modal-body").html($(this).next().html());
                    modal.modal();
                });
                $(".start-class-btn").click(function (e) {
                    e.preventDefault();
                    attr = $(this);
                    if (!annoying) {
                        $("#jid").val($(this).attr("data-jid"));
                        $("#main-form").submit();
                    } else {
                        var modal = $("#require-modal");
                        if (!isuserloggedin) {
                            modal.find("h3").text(JS_MESSAGES.login_req_h);
                            modal.find(".modal-body").html(JS_MESSAGES.login_req_err);
                            modal.find(".modal-footer button").html(JS_MESSAGES.login_req_lbl);
                        } else {
                            modal.find('#sub-modal').attr('data-sat-class', attr.attr('data-sat-class'));
                            modal.find('#sub-modal').attr('data-subscription-type', attr.attr('data-subscription-type'));
                            modal.find('#sub-modal').attr('data-type', attr.attr('data-type'));
                            modal.find("h3").text(JS_MESSAGES.sub_req_h);
                            modal.find(".modal-body").html(JS_MESSAGES.sub_req_err);
                            modal.find(".modal-footer button").html(JS_MESSAGES.sub_req_lbl);
                        }
                        modal.modal();
                    }
                });
            });
        });
        if (window.location.href.indexOf("&sat") > -1) {
            $('#div-select').find('.cs-placeholder').html("SAT Preparation and Simulation Test");
        }
        if (window.location.href.indexOf("&back-en-sat") > -1) {
            $('#div-select').find('.cs-placeholder').html("SAT Preparation and Simulation Test");
        }
        if (window.location.href.indexOf("&eng-prac") > -1) {
            $('#div-select').find('.cs-placeholder').html("SAT Preparation and Simulation Test");
        }
        if (window.location.href.indexOf("&english") > -1) {
            $('#div-select').find('.cs-placeholder').html("Critical English Subjects");
        }
        if (window.location.href.indexOf("&math") > -1) {
            $('#div-select').find('.cs-placeholder').html("Critical Math Subjects");
        }
        if (window.location.href.indexOf("&homeworkagm-english") > -1 || window.location.href.indexOf("&homeworkagm-math") > -1) {
            $('#div-select').find('.cs-placeholder').html("Assignment from Teacher");
        }
        if (window.location.href.indexOf("&backhometeacher") > -1) {
            $('#div-select').find('.cs-placeholder').html("Assignment from Teacher");
        }
        if (window.location.href.indexOf("&backhometeacher-english") > -1) {
            $('#div-select ul li:nth-child(3)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a1);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
        }
        if (window.location.href.indexOf("&backhometeacher-math") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a1);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
        }
        // Handing Back Math Sat 1
        if (window.location.href.indexOf("&back-sat14") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat-i li:nth-child(6)').addClass('active');
            $('#menu-sat-i-5').addClass('active in');
            $('#home-sat-i').removeClass('active in');
            $('#menu-sat-i li:nth-child(1)').removeClass('active');
        }
        if (window.location.href.indexOf("&back-sat13") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat-i li:nth-child(5)').addClass('active');
            $('#menu-sat-i-4').addClass('active in');
            $('#home-sat-i').removeClass('active in');
            $('#menu-sat-i li:nth-child(1)').removeClass('active');
        }
        if (window.location.href.indexOf("&back-sat12") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat-i li:nth-child(4)').addClass('active');
            $('#menu-sat-i-3').addClass('active in');
            $('#home-sat-i').removeClass('active in');
            $('#menu-sat-i li:nth-child(1)').removeClass('active');
        }
        if (window.location.href.indexOf("&back-sat11") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat-i li:nth-child(3)').addClass('active');
            $('#menu-sat-i-2').addClass('active in');
            $('#home-sat-i').removeClass('active in');
            $('#menu-sat-i li:nth-child(1)').removeClass('active');
        }
        if (window.location.href.indexOf("&back-sat10") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat-i li:nth-child(2)').addClass('active');
            $('#menu-sat-i-1').addClass('active in');
            $('#home-sat-i').removeClass('active in');
            $('#menu-sat-i li:nth-child(1)').removeClass('active');
        }
        if (window.location.href.indexOf("&back-sat9") > -1) {
            $('#div-select ul li:nth-child(3)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
        }
        // Handing Back Math Sat 2    
        if (window.location.href.indexOf("&back-sat15") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
        }
        if (window.location.href.indexOf("&back-sat16") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat-ii li:nth-child(2)').addClass('active');
            $('#menu-sat-ii-1').addClass('active in');
            $('#home-sat-ii').removeClass('active in');
            $('#menu-sat-ii li:nth-child(1)').removeClass('active');
        }
        if (window.location.href.indexOf("&back-sat17") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat-ii li:nth-child(3)').addClass('active');
            $('#menu-sat-ii-2').addClass('active in');
            $('#home-sat-ii').removeClass('active in');
            $('#menu-sat-ii li:nth-child(1)').removeClass('active');
        }
        if (window.location.href.indexOf("&back-sat18") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat-ii li:nth-child(4)').addClass('active');
            $('#menu-sat-ii-3').addClass('active in');
            $('#home-sat-ii').removeClass('active in');
            $('#menu-sat-ii li:nth-child(1)').removeClass('active');
        }
        if (window.location.href.indexOf("&back-sat19") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat-ii li:nth-child(5)').addClass('active');
            $('#menu-sat-ii-4').addClass('active in');
            $('#home-sat-ii').removeClass('active in');
            $('#menu-sat-ii li:nth-child(1)').removeClass('active');
        }

        if (window.location.href.indexOf("&back-sat20") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat-ii li:nth-child(6)').addClass('active');
            $('#menu-sat-ii-5').addClass('active in');
            $('#home-sat-ii').removeClass('active in');
            $('#menu-sat-ii li:nth-child(1)').removeClass('active');
        }
        if (window.location.href.indexOf("&back-en-sat1") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
        }
        if (window.location.href.indexOf("&back-en-sat2") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat li:nth-child(2)').addClass('active');
            $('#menu-sat li:nth-child(1)').removeClass('active');
            $('#menu1').addClass('active in');
            $('#home').removeClass('active in');
        }
        if (window.location.href.indexOf("&back-en-sat3") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat li:nth-child(3)').addClass('active');
            $('#menu-sat li:nth-child(1)').removeClass('active');
            $('#menu2').addClass('active in');
            $('#home').removeClass('active in');
        }
        if (window.location.href.indexOf("&back-en-sat4") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat li:nth-child(4)').addClass('active');
            $('#menu-sat li:nth-child(1)').removeClass('active');
            $('#menu3').addClass('active in');
            $('#home').removeClass('active in');
        }
        if (window.location.href.indexOf("&back-en-sat5") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat li:nth-child(5)').addClass('active');
            $('#menu-sat li:nth-child(1)').removeClass('active');
            $('#menu4').addClass('active in');
            $('#home').removeClass('active in');
        }
        if (window.location.href.indexOf("&back-en-sat6") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat li:nth-child(6)').addClass('active');
            $('#menu-sat li:nth-child(1)').removeClass('active');
            $('#menu5').addClass('active in');
            $('#home').removeClass('active in');
        }
        if (window.location.href.indexOf("&back-en-sat7") > -1) {
            $('#div-select ul li:nth-child(5)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').html(a3);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#menu-sat li:nth-child(7)').addClass('active');
            $('#menu-sat li:nth-child(1)').removeClass('active');
            $('#menu6').addClass('active in');
            $('#home').removeClass('active in');
        }
        //Handing Back Ikmath Course    
        if (window.location.href.indexOf("&backik=1") > -1) {
            $('.cs-placeholder').html("ikMath Courses");
            jQuery('#loadhomework').html(a4);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('.cs-options').removeClass('visible')
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            $('#load-class-math-follow-select').empty();
            // Get id from URL contain "back-ikmath"    
            var url = window.location.href;
            string = url.split("back-ikmath=");
            var text_ikmath = $('#test-ikmath .init');
            var array_ikmath = {38: 'Math Kindergarten', 39: 'Math Grade 1', 40: 'Math Grade 2', 41: 'Math Grade 3', 42: 'Math Grade 4', 43: 'Math Grade 5', 44: 'Math Grade 6', 45: 'Math Grade 7', 46: 'Math Grade 8', 47: 'Math Grade 9', 48: 'Math Grade 10', 49: 'Math Grade 11', 50: 'Math Grade 12'};
//                $.each( array_ikmath, function( key, value ) {
//                    if(value == name) {
//                       id_name = key;
//                    }
//                });
            var id_name = string[1];
            var name = array_ikmath[id_name];
            text_ikmath.html(name);
            if (text_ikmath.html() == "Please Subscribe") {
                $.post(home_url + "/?r=ajax/load_class_ikmath_course", {'id_class_selected': 0}, function (result) {
                    $('#load-class-math-follow-select').html(result);
                    $(".class-detail-btn").click(function (e) {
                        e.preventDefault();
                        var modal = $("#class-detail-modal");
                        modal.find(".modal-body").html($(this).next().html());
                        modal.modal();
                    });
                });
            } else {
                $.post(home_url + "/?r=ajax/load_class_ikmath_course", {'id_class_selected': id_name}, function (result) {
                    $('#load-class-math-follow-select').html(result);
                    $(".class-detail-btn").click(function (e) {
                        e.preventDefault();
                        var modal = $("#class-detail-modal");
                        modal.find(".modal-body").html($(this).next().html());
                        modal.modal();
                    });
                });
            }
            $('.click-test-ikmath').click(function (e) {
                var id = $(this).attr('data-value');
                $('#load-class-math-follow-select').empty();
                $.post(home_url + "/?r=ajax/load_class_ikmath_course", {'id_class_selected': id}, function (result) {
                    $('#load-class-math-follow-select').html(result);
                    $(".class-detail-btn").click(function (e) {
                        e.preventDefault();
                        var modal = $("#class-detail-modal");
                        modal.find(".modal-body").html($(this).next().html());
                        modal.modal();
                    });
                });
            });
            $("#test-ikmath").on("click", ".init", function () {
                $(this).closest("#test-ikmath").children('li:not(.init)').toggle();
            });

            var allOptions = $("#test-ikmath").children('li:not(.init)');
            $("#test-ikmath").on("click", "li:not(.init)", function () {
                allOptions.removeClass('selected');
                $(this).addClass('selected');
                $("#test-ikmath").children('.init').html($(this).html());
                allOptions.toggle();
            })
            var id = $('#select-class-math-course').val();
            $('#load-class-math-follow-select').empty();
            $(".start-class-btn").click(function (e) {
                e.preventDefault();
                attr = $(this);
                if (!annoying) {
                    $("#jid").val($(this).attr("data-jid"));
                    $("#main-form").submit();
                } else {
                    var modal = $("#require-modal");
                    if (!isuserloggedin) {
                        modal.find("h3").text(JS_MESSAGES.login_req_h);
                        modal.find(".modal-body").html(JS_MESSAGES.login_req_err);
                        modal.find(".modal-footer button").html(JS_MESSAGES.login_req_lbl);
                    } else {
                        modal.find('#sub-modal').attr('data-sat-class', attr.attr('data-sat-class'));
                        modal.find('#sub-modal').attr('data-subscription-type', attr.attr('data-subscription-type'));
                        modal.find('#sub-modal').attr('data-type', attr.attr('data-type'));
                        modal.find("h3").text(JS_MESSAGES.sub_req_h);
                        modal.find(".modal-body").html(JS_MESSAGES.sub_req_err);
                        modal.find(".modal-footer button").html(JS_MESSAGES.sub_req_lbl);
                    }
                    modal.modal();
                }
            });
        }
        if (window.location.href.indexOf("&issat-math") > -1) {
            $('.cs-placeholder').html("ikMath Courses");
        }

        $('#close-wd-homework,.close-modal-view-homework').click(function () {
            if(window.location.href.indexOf("&ba-preview-modal") > -1) {
                var old_url = window.location.href;
                var str = old_url.split("&ba-preview-modal");
                var url = str[0]+str[1];
                window.location.replace(url);
            }else{
                location.reload();
            }
        });
        
        
        $('.view-result-writing').click(function (e) {
            e.preventDefault();
            var url = $(this).attr("data-practice-url");
            $('#btn-ok-rs-homework').attr("data-url", url);
            var id = $(this).attr('id');
            $('.show-evaluation-english').attr("data-id", id);
            var status = $(this).attr('data-status');
            var mode = $(this).attr('data-mode');
            $.get(home_url + "/?r=ajax/view_result_writing/get_result_writing", {hw_id: id, st: status, select: 0, mode: mode}, function (data) {
                $('#modal-view-result-writing #can-scroll').html(data);
                $('#modal-view-result-writing').modal();
                $('#css-ul-dropdown').click(function () {
                    if ($('.click-test-ikmath').hasClass('active')) {
                        $('.click-test-ikmath').removeClass('active');
                        $('.click-test-ikmath').css("display", "none");
                        $('.init').css("display", "block");
                    } else {
                        $('.click-test-ikmath').css("display", "block");
                        $('.click-test-ikmath').addClass('active');
                        $('.init').css("display", "none");
                    }
                });
                $('.click-test-ikmath').click(function (e) {
                    e.preventDefault();
                    var page = $(this).attr('data-value');
                    $('.init').html("Page " + page);
                    // page sẽ là giá trị option ta phải -1 để các question get từ db sẽ có question[0]    
                    var id_select = page - 1;
                    $.get(home_url + "/?r=ajax/view_result_writing/update-question", {hw_id: id, select: id_select}, function (data) {
                        $('.css-name-sheet').html(data);
                    });
                    $.get(home_url + "/?r=ajax/view_result_writing/update-assignment", {hw_id: id, select: id_select}, function (data) {
                        $('.css-assing').html(data);
                    });
                    $.get(home_url + "/?r=ajax/view_result_writing/update-answer", {hw_id: id, select: id_select}, function (data) {
                        $('.css-answer').html(data);
                    });
                    $.get(home_url + "/?r=ajax/view_result_writing/update-note-teacher", {hw_id: id, select: id_select}, function (data) {
                        $('.css-note1').html(data);
                    });
                });
                $('.show-evaluation-english').click(function () {
                    var id_eva = $(this).attr("data-id");
                    $('.add-evaluation-english').attr("data-id-valua", id_eva);
                    $('#modal-view-result-writing').modal('hide');
                    $('#modal-enter-evaluation-english').modal('show');
                });
                $('#btn-ok-rs-homework-english').click(function () {

                    $('#modal-view-result-writing').modal('hide');
                });
                $('.css-close-wd').click(function () {
                    $('#modal-enter-evaluation-english').modal('hide');
                });
                $(".add-evaluation-english").click(function (e) {
                    e.preventDefault();
                    $('#modal-enter-evaluation-english').modal('hide');
                    var id_new = $(this).attr("data-id-valua");
                    var txt_eval = $('.txt-evaluation-sub').val();
//                     console.log(id_new);
//                     console.log(txt_eval);
                    if (id_new != '' && txt_eval != '')
                    {
                        $.post(home_url + "/?r=ajax/update-evaluation-english", {id: id_new, txt_eval: txt_eval}, function (data) {
                            if (data) {
                                $('#modal-view-result-homework').modal('hide');
                                $("#evaluation_ok").modal("show");
                            } else
                                alert('Update error');
                        });
                    } else {
                        $('#modal-view-result-homework').modal('hide');
                        $("#evaluation_error").modal("show");
                    }
                });
            });

        });

        $(".class-detail-btn-default").click(function (e) {
            e.preventDefault();
            var modal = $("#class-detail-modal");
            modal.find(".modal-body").html($(this).next().html());
            modal.modal();
        });
        $(document).ready(function () {
            var content = "<div class='css-color-000 css-fo css-font-sel css-bottom-10'><b>Self-study Subscription</b></div><div class='css-color-000'>If you are subscribed to Self-study, </div><div class='css-color-000'>You can do following lessons:</div><ul class='css-list-sub'><li><a href=" + home_url + "/?r=spelling-practice>Spelling Practice</a></li><li><a href=" + home_url + "/?r=vocabulary-practice>Vocabulary and Grammar</a></li><li><a href=" + home_url + "/?r=reading-comprehension>Reading Comprehension</a></li><li><a href=" + home_url + "/?r=writing-practice>Writing Practice</a></li><li><a href=" + home_url + "/?r=flash-cards>Vocabulary Builder</a></li><ul>";
            $('#icon-info-sub').popover({content: '<span class="text-danger">' + content + '</span>', html: true, placement: "bottom"});
        });
        $(document).ready(function () {
            var content = "<div class='css-color-000 css-fo css-font-sel css-bottom-10'><b>Self-study Subscription</b></div><div class='css-color-000'>If you are subscribed to Self-study, </div><div class='css-color-000'>You can do following lessons:</div><ul class='css-list-sub'><li><a href=" + home_url + "/?r=arithmetics>Elementary</a></li><li><a href=" + home_url + "/?r=algebra-i>Algebra 1</a></li><li><a href=" + home_url + "/?r=algebra-ii>Algebra 2</a></li><li><a href=" + home_url + "/?r=geometry>Geometry</a></li><li><a href=" + home_url + "/?r=calculus>Calculus</a></li><ul>";
            $('#icon-info-sub-math').popover({content: '<span class="text-danger">' + content + '</span>', html: true, placement: "bottom"});
        });
        if (window.location.href.indexOf("&ikmath-plan") > -1) {
//            alert(1);
            $('#div-select ul li:nth-child(4)').click();
            $('#div-select div').removeClass("cs-active");
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible');
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            jQuery('#loadhomework').html(a5);
            jQuery('#loadhomework').css("display", "block");
            jQuery('#view-result').css("display", "none");
            jQuery('#loadhomework_group').css("display", "none");
            jQuery('#view-result-wrid').css("display", "none");
            jQuery('.cs-options').removeClass('visible');
            jQuery('#span-icon').addClass('span-icon-down');
            jQuery('#span-icon').removeClass('span-icon-up');
            var id = 0;
//                var id = $('#select-schedule-tutoring').val();
            $('#load-schedule-tutoring-select').empty();
            $.post(home_url + "/?r=ajax/load_tutoring_plan", {'id_schedule': id}, function (result) {
                $('#load-schedule-tutoring-select').html(result);
            });
            $("ul").on("click", ".init", function () {
                $(this).closest("ul").children('li:not(.init)').toggle();
            });
            $('#li-math4-private').on('click', function (e) {
                $('#tutoring-id-plan').removeClass("hidden");
                $('#ikmath-tutoring').addClass("hidden");
            });
            $('#li-math5-private').on('click', function (e) {
                $('#ikmath-tutoring').removeClass("hidden");
                $('#tutoring-id-plan').addClass("hidden");
            });
            var allOptions = $("#test").children('li:not(.init)');
            $("#test").on("click", "li:not(.init)", function () {
                allOptions.removeClass('selected');
                $(this).addClass('selected');
                $("#test").children('.init').html($(this).html());
                allOptions.toggle();
            });
            // Tutoring plan
            $('.click-test').click(function (e) {
                var id = $(this).attr('data-value');
                $('#load-schedule-tutoring-select').empty();
//            alert(id);
                $.post(home_url + "/?r=ajax/load_tutoring_plan", {'id_schedule': id}, function (result) {
                    $('#load-schedule-tutoring-select').html(result);
                    $(".class-detail-btn").click(function (e) {
                        e.preventDefault();
                        var modal = $("#class-detail-modal");
                        modal.find(".modal-body").html($(this).next().html());
                        modal.modal();
                    });
                    $(".start-class-btn").click(function (e) {
                        e.preventDefault();
                        attr = $(this);
                        if (!annoying) {
                            $("#jid").val($(this).attr("data-jid"));
                            $("#main-form").submit();
                        } else {
                            var modal = $("#require-modal");
                            if (!isuserloggedin) {
                                modal.find("h3").text(JS_MESSAGES.login_req_h);
                                modal.find(".modal-body").html(JS_MESSAGES.login_req_err);
                                modal.find(".modal-footer button").html(JS_MESSAGES.login_req_lbl);
                            } else {
                                modal.find('#sub-modal').attr('data-sat-class', attr.attr('data-sat-class'));
                                modal.find('#sub-modal').attr('data-subscription-type', attr.attr('data-subscription-type'));
                                modal.find('#sub-modal').attr('data-type', attr.attr('data-type'));
                                modal.find("h3").text(JS_MESSAGES.sub_req_h);
                                modal.find(".modal-body").html(JS_MESSAGES.sub_req_err);
                                modal.find(".modal-footer button").html(JS_MESSAGES.sub_req_lbl);
                            }
                            modal.modal();
                        }
                    });
                });
            });
        }
        // Clear answer math
        $('#btn-ok-rs-homework').click(function () {
            var hid = $(this).attr("hid");
            $.get(home_url + '/?r=ajax/clear_answer', {hid: hid}, function (data) {
                console.log(data);
                $('.rs-score1').html("0%");
                $('.rs-score1').removeAttr("style");
                $('.rs-score1').attr("style", "color:#cd003d");
                $('.td-answer').html("");
            });
        });
    });
})(jQuery);