/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

(function ($) {
    $(function () {
            jQuery('*[id^="li-purchase"]').removeClass('active');
            jQuery('*[id^="pur-"]').removeClass('active in');
            jQuery('#li-purchase-hidden').addClass('active');
            jQuery('#li-purchase-1').addClass('active');
            jQuery('#pur-1').addClass('active in');
            jQuery('#pur-substatus').addClass('active in');
            $(".btn-view-detail").click(function (e) {
            e.preventDefault();
            id = $(this).attr('id-detail');
            $.get(home_url + "/?r=ajax/getviewdetail", {id: id}, function (data) {
//                console.log(data);
                if (data.length != 0) {
                    data = JSON.parse(data);
//                    console.log(data);
                    data = data['items'][0];
                    
                    html = '<div>';
                    html += '<table class="table table-striped table-style3 table-custom-2">';
                    html += '<tr><td class="txt-padd-left-5" style="width: 200px;">Lesson Name:</td>';
                    if (data['type']) {
                        if(data['sat_class']){
                            html += '<td colspan="2">' + data['sat_class'] + '</td></tr>';
                        }else{
                            html += '<td colspan="2">' + data['type'] + '</td></tr>';
                        }
                    }else{
                        html += '<td colspan="2">' + "N/A" + '</td></tr>';
                    }
                    html += '<tr><td class="txt-padd-left-5">Start/Purchased Date:</td>';
                    if (data['activated_on'] != null) {
                        html += '<td colspan="2">' + data['activated_on'] + '</td></tr>';
                    }else{
                        html += '<td colspan="2">' + "N/A" + '</td></tr>';
                    }
                    html += '<tr><td class="txt-padd-left-5">Duration:</td>';
                    if (data['total_date'] != null) {
                        html += '<td colspan="2">' + data['total_date'] +" days"+ '</td></tr>';
                    }else{
                        html += '<td colspan="2">' + "N/A" + '</td></tr>';
                    }
                    html += '<tr><td class="txt-padd-left-5">Dictionary Type:</td>';
                    if (data['dictionary'] != null) {
                        html += '<td colspan="2">' + data['dictionary'] + " Dictionary"+'</td></tr>';
                    }else{
                        html += '<td colspan="2">' + "N/A" + '</td></tr>';
                    }
                    html += '<tr><td class="txt-padd-left-5">Number of User:</td>';
                    if (data['number_of_students'] != null) {
                        html += '<td colspan="2">' + data['number_of_students'] + '</td></tr>';
                    }else{
                        html += '<td colspan="2">' + "N/A" + '</td></tr>';
                    }
                    html += '<tr><td class="txt-padd-left-5">Payment Method:</td>';
                    if (data['name'] != null) {
                        html += '<td colspan="2">' + data['name'] + '</td></tr>';
                    }else{
                        html += '<td colspan="2">' + "N/A" + '</td></tr>';
                    }
                    html += '<tr><td class="txt-padd-left-5">Paid Amount:</td>';
                    if (data['amount'] != null) {
                        html += '<td colspan="2">' +"$ "+data['amount'] + '</td></tr>';
                    }else{
                        html += '<td colspan="2">' + "N/A" + '</td></tr>';
                    }
                    html += '<tr><td class="txt-padd-left-5">Activation Code:</td>';
                    if (data['encoded_code'] != null) {
                        html += '<td colspan="2">' + data['encoded_code'] + '</td></tr>';
                    }else{
                        html += '<td colspan="2">' + "Not Used" + '</td></tr>';
                    }
                    html += '<tr><td class="txt-padd-left-5" style="vertical-align: inherit !important;">Renew:</td>';
                    html += '<td class="css-td-renew">';
                    html += '<div class="width-btn-sub" data-subid="'+data['id']+'"'+' data-subscription-type="'+data['typeid'] +'"'+' data-did="'+data['dictionary_id']+'"'+' data-size="'+ data['number_of_students'] +'"'+ 'data-months="'+ data['number_of_months'] +'"'+'data-group="'+  data['group_name'] + '"'+' data-sat-class="'+ data['sat_class']+'"'+' data-type="'+ data['sat_class_id'] +'"'+'data-gid="'+data['group_id']+'">';
                    html += '<button type="butt1on" data-type="renew" class="text-bold-size-16 text-right-0 btn btn-default btn-block btn-tiny grey extend-sub-btn btn-a-link css-link css-text-re" <?php echo $checked_out_state ?>'+ "Click to Renew" +'</button>';
                    html += '</div>';
                    html += '</td></tr>';
                    html += '</table></div>';
                    $('#view-detail').html(html);
                    $('#view-subscription-detail').modal();
                }
            });
        });

        $(document).on('click', "#paymyseft", function () {
            var $box = $(this);
            if ($box.is(":checked")) {
                $('#paystudent').prop("checked", false);
            }
        });
        $(document).on('click', "#paystudent", function () {
            var $box = $(this);
            if ($box.is(":checked")) {
                $('#paymyseft').prop("checked", false);
            }
        });
        $(document).on('click', ".extend-sub-btn", function (e) { 
            e.preventDefault();
            $("#view-subscription-detail").modal("hide");
            var t = parseInt($(this).parent().attr('data-subscription-type'));
            console.log("dataTest" +t);
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
                    $("#additional-subscription-dialog-continue").modal("show");
                    break;
                case 3:
                    var type = $(this).parent().attr("data-type");
                    if(type == 1){
                        $("#grammar-subscription-dialog").modal("show");
                    }else if(type == 2){
                        $("#writing-subscription-dialog").modal("show");
                    }else if(type == 3){
                        $("#sat-english-subscription-dialog").modal("show");
                    }else if(type == 4){
                        $("#sat-english-subscription-dialog").modal("show");
                    }
                break;
                case 7:
                    if($(this).parent().attr("data-type")==9){
                        $("#sat-1-preparation-dialog").modal("show");
                    }else if($(this).parent().attr("data-type")==10){
                        $("#sat-1-dialog").modal("show");
                    }
                    break;
                case 8:
                    if($(this).parent().attr("data-type")==15){
                        $("#sat-2-preparation-dialog").modal("show");
                    }else if($(this).parent().attr("data-type")==16){
                        $("#sat-2-dialog").modal("show");
                    }
                    break;
                case 12:
                    $("#sat-subscription-dialog-ikmath").modal("show");
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
                        $('#self-sat-months').val($('#sel-self-study-months').val());
                        calc_self_study_price();
                        $("#self-study-subscription-dialog").modal();
                    });
                    $(".logged-in").css('padding-right', '0');

                    $("#self-study-subscription-dialog").modal().one();
                    $(".logged-in").css('padding-right', '0');
                    if($('#sel-dictionary2 option:selected').val()=='') { 
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
        function renew_calc_total_price(subtype) {
            var students = isNaN(parseInt($("#student_num").val())) ? 0 : parseInt($("#student_num").val());
            var months = isNaN(parseInt($("#sel-teacher-tool1").val())) ? 0 : parseInt($("#sel-teacher-tool1").val());
//            console.log(students);
//            console.log(months);
            switch (subtype) {
                case "1":
                case "6":
                    $("#total-amount-renew").text(students * months * ttp / 100);
                    break;
                case "2":
                    var months = isNaN(parseInt($("#sel-teacher-tool1").val())) ? 0 : parseInt($("#sel-teacher-tool1").val());                    
                    var p = $("#sel-dictionary").val() == "6" ? adp : dp; 
                    var total = students * months * p / 100;
                    
                    $("#total-amount-renew").text(students * months * p / 100);
                    break;
                case "5":
                    $("#total-amount-renew").text(months * ssp);
                    break;
                case "9":
                    $("#total-amount-renew").text(months * ssp_math);
                    break;
                case "12":
                    $("#total-amount-renew").text(months * satMIKP);
                    break;
            }
        }
        function calc_total_price() {
            var students = isNaN(parseInt($("#student_num").val())) ? 0 : parseInt($("#student_num").val());
            var months = isNaN(parseInt($("#sel-teacher-tool").val())) ? 0 : parseInt($("#sel-teacher-tool").val());
//            console.log("tyeeeee "+$("#addi-sub-type").val());
            switch ($("#addi-sub-type").val()) {
                case "1":
                case "6":
                    $("#total-amount").text(students * months * ttp / 100);
                    break;
                case "2":
                    var months = isNaN(parseInt($("#sel-teacher-tool").val())) ? 0 : parseInt($("#sel-teacher-tool").val());
//                    console.log('total = '+students * months * p / 100);
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
        function ik_math_class_price(){
            var d = $("#sel-sat-class").val();
            switch (d) {
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
        function calc_sat_total_price() {
            var p;
            var d = $("#sel-sat-class").val();
//            alert(parseInt($("[name='subscription-type']:checked").val()));
//            if (parseInt($("[name='subscription-type']:checked").val()) == 12) {
//                var d = $("#sel-sat-class").val();
//            }
//            alert(d);
            if(!d) {
                var d = $('#sat-class').val();
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
//            console.log(p);
//            console.log($("#sel-sat-months").val());
            $("#total-amount-sat").text(parseInt($("#sel-sat-months").val()) * p);
        }
        function calc_sat_total_price_ikmath() {
            var p;
            p = $("#sel-sat-class-ikmath").val();
            var d = $("#sel-sat-class-ikmath").val();
            console.log(d);
            switch (d) {
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
            $("#total-amount-sat-ikmath").text(parseInt($("#sel-sat-months-ikmath").val()) * p);
        }
        
        $('#additional-subscription-dialog').on('show.bs.modal',function(){
            calc_sat_total_price();
            $("#sel-teacher-tool1").selectBoxIt('selectOption', '1').data("selectBox-selectBoxIt");
            $("#sel-teacher-tool1").data("selectBox-selectBoxIt").refresh();
        });
        function calc_self_study_price() {
            var id = $('#sel-dictionary2').val();
            if(id !='') {
                var months = parseInt($("#sel-self-study-months").val());
                $("#ss-total-amount").text(months * ssp);
            }
        }
        function calc_self_study_price_math() {
            var months = parseInt($("#sel-self-study-months").val());
            $("#ss-total-amount").text(months * ssp_math);
        }
        $("#sel-sat-months").change(function () {
            var pr =  $('#total-amount-sat').val();
            var mon = $('#sel-sat-months').val();
           // alert(pr + '' +mon); 
           calc_sat_total_price();
        });
        $("#sel-sat-class").change(function () {
            ik_math_class_price();
            if($("#sat-sub-type").val()==7) {
                var type = $("#sel-sat-class").val();
                $("#sat-class-7").val(type);
            }else if($("#sat-sub-type").val()==12){
                var type = $("#sel-sat-class").val();
                $("#sat-class-12").val(type);
            }
        });
//        $(".sel-sat-class").change(function(){
//            $("#sat-class").val($(this).val());
//        });
        
        $("#english-detail-list, #math-detail-list").click(function (e) {
            $("#self-study-detail,#teacher-homework-tool-dialog").modal("hide").one("hidden.bs.modal", function () {
                        $("#self-study-detail-list").modal();
                    });
                    $(".logged-in").css('padding-right', '0');
                    $("#self-study-detail-list").modal().one();
        });
        $(document).on("click", ".leave-group", function () {
            $('#modal-leave_group #leave-group-id').attr('data-id',$(this).attr('data-id'));
            $('#modal-leave_group #name-group-leave').html($(this).attr('data-name'));
            $('#modal-leave_group').modal();
        });
        // leaving group
        $(document).on("click", ".btn-leave-group", function (e) {
            e.preventDefault();
            id = $(this).attr('data-id');
            $.get(home_url + "/?r=ajax/leave_group", {id_group: id}, function (data) {
                if(data==1){
                    $('#snackbar').html('Successfully left group.');
                    
                }else{
                    $('#snackbar').html('Cannot leave Group.');
                }
                    var x = document.getElementById("snackbar")
                    x.className = "show";
                    setTimeout(function () {
                        x.className = x.className.replace("show", "");
                    }, 3000);
                    location.reload();
            });
        });
        
        $(".choose-sub-btn").click(function () {
            calc_sat_total_price();
            var tthis = $(this);
            var t = parseInt(tthis.attr('data-subscription-type'));
            $("#sub-id").val(0);
            $("#student_num").prop("disabled", false);
            $("#sel-dictionary").data("selectBox-selectBoxIt").enable().selectOption(0);
            $("#sel-teacher-tool").data("selectBox-selectBoxIt").enable().selectOption(0);
            $("#num-of-months-lbl").text(LBL_NO_M);
            $("#addi-sub-type").val(t);
//            console.log('sub-type = '+t);
            switch (t) {
                case 1:
                $("#teacher-sub-details-dialog").modal('show');
                    // handing Click button "Continute" check group 
                    $("#sub-continue-math").click(function (e) {
                        e.preventDefault();
                        var tthis = $(this);
                        var $sub_modal = $("#teacher-home-tool-modal-english");
                        var $modal_title = $sub_modal.find(".modal-header h3");
                        var subtype = parseInt($("[name='subscription-type']:checked").val());
                        var $valid = true, $waiting = false, $gid, $selbox, $tgname, $tgpass;
                        $("#sub-id").val(0);

                        $(".tops").remove();
                        $("#sel-teacher-tool").data("selectBox-selectBoxIt").enable().refresh();
                        $("#student_num").prop("disabled", false).val($("#student_num").attr("data-min")).attr("min", $("#student_num").attr("data-min"));
                        $("#num-of-months-lbl").text(LBL_NO_M);
                        $("#total-amount").text(0);

                        $("#selected-group-label").show();
                        $modal_title.text($modal_title.attr("data-ts-text"));
                        $("#num-of-student-lbl").text(LBL_NO_STUDENTS);
                        $gid = $("#sel-group-teacher");
                        $selbox = $("#sel-group-teacherSelectBoxItContainer");
                        $tgname = $("#teacher-gname");
                        $tgpass = $("#teacher-gpass");
                        if ($gid.val() == "" && $tgname.val().trim() == "") {
                            $selbox.popover({content: '<span class="text-danger">' + GRP_EMPTY_ERR + '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                            setTimeout(function () {
                                $selbox.popover("destroy")
                            }, 2000);
                            $valid = false;
                        }
                        if ($tgname.val().trim() != "" && $tgpass.val().trim() == "") {
                            $tgpass.popover({content: '<span class="text-danger">' + GRP_PW_ERR + '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                            setTimeout(function () {
                                $tgpass.popover("destroy")
                            }, 2000);
                            $valid = false;
                        }

                        var gn = $tgname.val();
                        if (gn == "") {
                            gn = $("#sel-group-teacher :selected").text();
                        } else {
                            tthis.button("loading");
                            $waiting = true;
                            $.post(home_url + "/?r=ajax/group/availability", {gn: gn}, function (data) {
                                if (parseInt(data) == 0) {
                                    $tgname.popover({content: '<span class="text-danger">' + GRP_EXIST_ERR + '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                                    setTimeout(function () {
                                        $tgname.popover("destroy")
                                    }, 2000);
                                    $valid = false;
                                }
                                tthis.button("reset");
                                if ($valid) {
                                    $("#teacher-sub-details-dialog").modal("hide");
                                    $sub_modal.modal("show"); 
                                }
                            });
                            
                        }
                        $("#addi-selected-group").text(gn);
                        $("#addi-gid").val($gid.val());
                        $("#addi-gname").val($tgname.val());
                        $("#addi-gpass").val($tgpass.val());

                        if ($valid && !$waiting) {
                            $("#teacher-sub-details-dialog").modal("hide");
                            $sub_modal.modal("show");       
                        }
                    });
                    break;
                case 6:
                    $("#teacher-sub-details-dialog").modal('show');
                    // handing Click button "Continute" check group 
                    $("#sub-continue").click(function (e) {
                        e.preventDefault();
                        var tthis = $(this);
                        var $sub_modal = $("#teacher-home-tool-modal-math");
                        var $modal_title = $sub_modal.find(".modal-header h3");
                        var subtype = parseInt($("[name='subscription-type']:checked").val());
                        var $valid = true, $waiting = false, $gid, $selbox, $tgname, $tgpass;
                        $("#sub-id").val(0);

                        $(".tops").remove();
                        $("#sel-teacher-tool").data("selectBox-selectBoxIt").enable().refresh();
                        $("#student_num").prop("disabled", false).val($("#student_num").attr("data-min")).attr("min", $("#student_num").attr("data-min"));
                        $("#num-of-months-lbl").text(LBL_NO_M);
                        $("#total-amount").text(0);

                        $("#selected-group-label").show();
                        $modal_title.text($modal_title.attr("data-ts-text"));
                        $("#num-of-student-lbl").text(LBL_NO_STUDENTS);
                        $gid = $("#sel-group-teacher");
                        $selbox = $("#sel-group-teacherSelectBoxItContainer");
                        $tgname = $("#teacher-gname");
                        $tgpass = $("#teacher-gpass");
                        if ($gid.val() == "" && $tgname.val().trim() == "") {
                            $selbox.popover({content: '<span class="text-danger">' + GRP_EMPTY_ERR + '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                            setTimeout(function () {
                                $selbox.popover("destroy")
                            }, 2000);
                            $valid = false;
                        }
                        if ($tgname.val().trim() != "" && $tgpass.val().trim() == "") {
                            $tgpass.popover({content: '<span class="text-danger">' + GRP_PW_ERR + '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                            setTimeout(function () {
                                $tgpass.popover("destroy")
                            }, 2000);
                            $valid = false;
                        }

                        var gn = $tgname.val();
                        if (gn == "") {
                            gn = $("#sel-group-teacher :selected").text();
                        } else {
                            tthis.button("loading");
                            $waiting = true;
                            $.post(home_url + "/?r=ajax/group/availability", {gn: gn}, function (data) {
                                if (parseInt(data) == 0) {
                                    $tgname.popover({content: '<span class="text-danger">' + GRP_EXIST_ERR + '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                                    setTimeout(function () {
                                        $tgname.popover("destroy")
                                    }, 2000);
                                    $valid = false;
                                }
                                tthis.button("reset");
                                if ($valid) {
                                    $("#teacher-sub-details-dialog").modal("hide");
                                    $sub_modal.modal("show"); 
                                }
                            });
                            
                        }
                        $("#addi-selected-group").text(gn);
                        $("#addi-gid").val($gid.val());
                        $("#addi-gname").val($tgname.val());
                        $("#addi-gpass").val($tgpass.val());

                        if ($valid && !$waiting) {
                            $("#teacher-sub-details-dialog").modal("hide");
                            $sub_modal.modal("show");       
                        }
                    });
                    break;
                case 2:
                    var $modal = $("#additional-subscription-dialog-continue");
                    $modal.modal();
                    break;
                case 3:
                    var type = $(this).attr("data-type");
                    if(type == 1){
                        $("#grammar-subscription-dialog").modal("show");
                    }else if(type == 2){
                        $("#writing-subscription-dialog").modal("show");
                    }else if(type == 3){
                        $("#sat-english-subscription-dialog").modal("show");
                    }
                break;
                case 7:
                    if($(this).attr("data-type")==9){
                        $("#sat-1-preparation-dialog").modal("show");
                    }else if($(this).attr("data-type")==10){
                        $("#sat-1-dialog").modal("show");
                    }
                    break;
                case 8:
                    if($(this).attr("data-type")==15){
                        $("#sat-2-preparation-dialog").modal("show");
                    }else if($(this).attr("data-type")==16){
                        $("#sat-2-dialog").modal("show");
                    }
                    break;
                case 12:
                    $("#sat-subscription-dialog-ikmath").modal("show");
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
                    $("#self-study-subscription-dialog").modal("show");
                    break;
                case 9:
                    $("#self-study-subscription-dialog-math").modal("show");
                    break;
            }
        });
        
        jQuery('#li-purchase-hidden').click(function () {
            var $this = jQuery(this),
                    clickNum = $this.data('clickNum');
            if (!clickNum) {
                clickNum = 2;
            }
            if (clickNum % 2 == 0) {
                setTimeout(function () {
                    jQuery('*[id^="li-purchase"]').removeClass('active')
                    jQuery('*[id^="pur-"]').removeClass('active in')
                    jQuery('#li-purchase-hidden-1').addClass('active')
                    jQuery('#pur-1').addClass('active in')
                }, 50);
            } else {
                setTimeout(function () {
                    jQuery('#li-purchase-1').addClass('active')
                    jQuery('#pur-substatus').addClass('active in')
                }, 50);
            }
            $this.data('clickNum', ++clickNum);
        });
        
        // to top right away
        if ( window.location.hash ) scroll(0,0);
        // void some browsers issue
        setTimeout( function() { scroll(0,0); }, 1);

        $(function() {

    // your current click function
            $('.scroll').on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $($(this).attr('href')).offset().top + 'px'
                }, 1000, 'swing');
            });

    // *only* if we have anchor on the url
            if(window.location.hash) {

                // smooth scroll to the anchor id
                $('html, body').animate({
                    scrollTop: $(window.location.hash).offset().top + 'px'
                }, 1000, 'swing');
            }

                });
                $('#add-to-cart').click(function (e){
    //Merriam-Webster Dictionary (data-type = 2)
                            $selected = $('#sel-dictionary option:selected');
                            if($selected.val() == '') {
                                e.preventDefault();
                                $selbox = $("#sel-dictionarySelectBoxItContainer");
                                $selbox.popover({content: '<span class="text-danger">' +'You not selected dictionary'+ '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                                setTimeout(function () {
                                    $selbox.popover("destroy")
                                }, 2000);
                            } else {
                                calc_total_price();
                            }
                    });   
                $("#sel-teacher-tool, #student_num, #sel-dictionary").change(function () {
                    if($('#sel-dictionary option:selected').val()=='') { 
                        $("#total-amount").text(0);
                    } else {
                       calc_total_price();
                    }
                    $('#sat-month-teach-tool').val($('#sel-teacher-tool').val());
                });    
    //Student Self-study (data-type = 5)
                $('#add-to-cart-ss').click(function (e){
                        $selected2 = $('#sel-dictionary2 option:selected');
                            if($selected2.val() == '') {
                                e.preventDefault();
                                $selbox = $("#sel-dictionary2SelectBoxItContainer");
                                $selbox.popover({content: '<span class="text-danger">' +'You not selected dictionary'+ '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                                setTimeout(function () {
                                    $selbox.popover("destroy")
                                }, 2000);
                            } else {
                                calc_self_study_price();
                            }
                        }); 
                $("#sel-self-study-months,#sel-dictionary2").change(function () {
                    if($('#sel-dictionary2 option:selected').val()=='') { 
                        $("#ss-total-amount").text(0);
                    }else {
                        calc_self_study_price();
                    }
                    if(window.location.href.indexOf("math.") > -1) {
                        calc_self_study_price_math();
                    }
                    $('#self-sat-months').val($('#sel-self-study-months').val());
                });
    // handing dictionary(additional-subscription-dialog) RENEW
                $('#sel-teacher-tool1').change (function (){
                    var subtype = $('#addi-sub-type').val();
                    renew_calc_total_price(subtype);
                });
                $('.sel-sat-class').change (function (){
                    $('#sat-class').val($('#sat-class-sub').val());
                    if($("#sat-sub-type").val() ==7) {
                        $('#sat-class-7').val($('#sat-class-sub').val());
                    }
                    if($("#sat-sub-type").val() ==12) {
                        $('#sat-class-12').val($('#sat-class-sub').val());
                    }
                    if($("#sat-sub-type").val() ==8) {
                        $('#sat-class-8').val($('.sel-sat-class').val());
                    }
                });
                $('#add-to-cart-ss-english').click(function (e){
                    var id = $('#sel-dictionary2').val();
                    if(id == 0) {
                        e.preventDefault();
                        $selbox = $("#sel-dictionary2SelectBoxIt");
                        $selbox.popover({content: '<span class="text-danger">' +'You not selected dictionary'+ '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                        setTimeout(function () {
                            $selbox.popover("destroy")
                        }, 2000);
                    } else {
                    calc_self_study_price();
                    }
                }); 
                
        // Set các giá trị khi modal subcrible show
            $('#sat-subscription-dialog').on('show.bs.modal',function(){
                calc_sat_total_price();
                if($("#sat-sub-type").val()==7) {
                    var type = $("#sat-class").val();
                    $("#sat-class-7").val(type);
                }else if($("#sat-sub-type").val()==12){
                    $("#sat-class-12").val("38");
                }
                else if($("#sat-sub-type").val()==8){
                    var type = $("#sat-class").val();
                    $("#sat-class-8").val(type);
                }
            }); 
            $('#sat-1-preparation-dialog').on('show.bs.modal',function(){
                $("#sel-sat-months-pre1").selectBoxIt('selectOption',"1").data("selectBox-selectBoxIt");
                $("#sel-sat-months-pre1").data("selectBox-selectBoxIt").refresh();
                $("#total-amount-pre1").html(satMIP);
            });
            $('#sat-2-preparation-dialog').on('show.bs.modal',function(){
                $("#sel-sat-months-pre2-math").selectBoxIt('selectOption',"1").data("selectBox-selectBoxIt");
                $("#sel-sat-months-pre2-math").data("selectBox-selectBoxIt").refresh();
                $("#total-amount-pre2-math").html(satMIIP);
            });
            $('#sat-1-dialog').on('show.bs.modal',function(){
                $("#sel-sat-months-sat1-math").selectBoxIt('selectOption',"1").data("selectBox-selectBoxIt");
                $("#sel-sat-months-sat1-math").data("selectBox-selectBoxIt").refresh();
                $("#sat-class-sub-sat1-math").selectBoxIt('selectOption',"10").data("selectBox-selectBoxIt");
                $("#sat-class-sub-sat1-math").data("selectBox-selectBoxIt").refresh();
                $("#total-amount-sat1").html(satStp);
            });
            $('#sat-2-dialog').on('show.bs.modal',function(){
                $("#sel-sat-months-sat2-math").selectBoxIt('selectOption',"1").data("selectBox-selectBoxIt");
                $("#sel-sat-months-sat2-math").data("selectBox-selectBoxIt").refresh();
                $("#sat-class-sub-sat2-math").selectBoxIt('selectOption',"10").data("selectBox-selectBoxIt");
                $("#sat-class-sub-sat2-math").data("selectBox-selectBoxIt").refresh();
                $("#total-amount-sat2").html(satStp);
            });
            $('#grammar-subscription-dialog').on('show.bs.modal',function(){
                $("#sel-grammar-months").selectBoxIt('selectOption',"1").data("selectBox-selectBoxIt");
                $("#sel-grammar-months").data("selectBox-selectBoxIt").refresh();
                $("#total-amount-grammar").html(satGp);
            });
            $('#writing-subscription-dialog').on('show.bs.modal',function(){
                $("#sel-writting-months").selectBoxIt('selectOption',"1").data("selectBox-selectBoxIt");
                $("#sel-writting-months").data("selectBox-selectBoxIt").refresh();
                $("#total-amount-writting").html(satWp);
            });
            $('#sat-english-subscription-dialog').on('show.bs.modal',function(){
                $("#sel-sat-english-months").selectBoxIt('selectOption',"1").data("selectBox-selectBoxIt");
                $("#sel-sat-english-months").data("selectBox-selectBoxIt").refresh();
                $("#sat-english-class-sub").selectBoxIt('selectOption',"3").data("selectBox-selectBoxIt");
                $("#sat-english-class-sub").data("selectBox-selectBoxIt").refresh();
                $("#total-amount-sat-english").html(satStp);
            });
            $('#sat-subscription-dialog-ikmath').on('show.bs.modal',function(){
                $("#sel-sat-months-ikmath").selectBoxIt('selectOption',"1").data("selectBox-selectBoxIt");
                $("#sel-sat-months-ikmath").data("selectBox-selectBoxIt").refresh();
                $("#sel-sat-class-ikmath").selectBoxIt('selectOption',"38").data("selectBox-selectBoxIt");
                $("#sel-sat-class-ikmath").data("selectBox-selectBoxIt").refresh();
                $("#total-amount-sat-ikmath").html(satMIKP);
            });
            $('#self-study-subscription-dialog').on('show.bs.modal',function(){
                $("#sel-dictionary2").selectBoxIt('selectOption',"0").data("selectBox-selectBoxIt");
                $("#sel-dictionary2").data("selectBox-selectBoxIt").refresh();
                $("#sel-self-study-months").selectBoxIt('selectOption',"1").data("selectBox-selectBoxIt");
                $("#sel-self-study-months").data("selectBox-selectBoxIt").refresh();
                $("#ss-total-amount").html(0);
            });
            $('#additional-subscription-dialog-continue').on('show.bs.modal',function(){
                $("#additional-subscription-dialog-continue #sel-teacher-tool").selectBoxIt('selectOption',"3").data("selectBox-selectBoxIt");
                $("#additional-subscription-dialog-continue #sel-teacher-tool").data("selectBox-selectBoxIt").refresh();
                $("#additional-subscription-dialog-continue #sel-dictionary").selectBoxIt('selectOption', '0'.toString()).data("selectBox-selectBoxIt");
                $("#additional-subscription-dialog-continue #sel-dictionary").data("selectBox-selectBoxIt").refresh();
                $("#additional-subscription-dialog-continue #student-num").val("1");
                $("#total-amount").html(0);
            });
            $('#self-study-subscription-dialog-math').on('show.bs.modal',function(){
                $("#sel-self-study-months-math").selectBoxIt('selectOption',"1").data("selectBox-selectBoxIt");
                $("#sel-self-study-months-math").data("selectBox-selectBoxIt").refresh();
                $("#ss-total-amount-math-self").html(ssp_math);
            });
        // Set các tổng tiền khi thay đổi tháng  sel-sat-months-pre1
            $("#sel-sat-months-pre1").change(function () {
                var month = $("#sel-sat-months-pre1").val();
                $("#total-amount-pre1").html(satMIP * month);
            });
            $("#sel-sat-months-pre1").change(function () {
                var month = $("#sel-sat-months-pre1").val();
                $("#sat-class-sub-sat1").html(satMIP * month);
            });
            $("#sel-sat-months-sat1").change(function () {
                var month = $("#sel-sat-months-pre1").val();
                $("#sat-class-sub-sat1").html(satMIP * month);
            });
            $("#sel-grammar-months").change(function () {
                var month = $(this).val();
                $("#total-amount-grammar").html(month * satGp);
            });
            $("#sel-writting-months").change(function () {
                var month = $(this).val();
                $("#total-amount-writting").html(month * satWp);
            });
            $("#sel-sat-english-months").change(function () {
                var month = $(this).val();
                $("#total-amount-sat-english").html(month * satStp);
            });
            $("#sat-english-class-sub").change(function () {
                $("#sat-english-subscription-dialog #sat-class").val($(this).val());
            });
            $("#sel-sat-months-sat1-math").change(function () {
                var month = $(this).val();
                $("#total-amount-sat1").html(month * satMIP);
            });
            $("#sat-class-sub-sat1-math").change(function () {
                $("#sat-1-dialog #sat-class").val($(this).val());
            });
            $("#sel-sat-months-pre2-math").change(function () {
                var month = $(this).val();
                $("#total-amount-pre2-math").html(month * satMIIP);
            });
            $("#sat-class-sub-sat2-math").change(function () {
                if(!$(this).val()){
                    $("#sat-2-dialog #sat-class").val(16);
                }else{
                    $("#sat-2-dialog #sat-class").val($(this).val());
                }
            });
            $("#sel-sat-months-sat2-math").change(function () {
                var month = $(this).val();
                $("#total-amount-sat2").html(month * satStp);
            });
            $("#sel-self-study-months-math").change(function () {
                var month = $(this).val();
                $("#ss-total-amount-math-self").html(month * ssp_math);
            });
            $("#sel-sat-class-ikmath").change(function () {
                calc_sat_total_price_ikmath();
                $("#sel-sat-class-ikmath #sat-class").val($(this).val());
            });
            $("#sel-sat-months-ikmath").change(function () {
                calc_sat_total_price_ikmath();
            });
            $("#sel-self-study-months").change(function () {
                if($("#sel-dictionary2").val() == 0 ){
                    $("#ss-total-amount").html("0");
                }else{
                    var month = parseInt($(this).val());
                    $("#ss-total-amount").html(ssp * month);
                }
            });
            $("#sel-dictionary2").change(function () {
                if($(this).val() == 0 ){
                    $("#ss-total-amount").html("0");
                }else{
                    var month = parseInt($("#sel-self-study-months").val());
                    $("#ss-total-amount").html(ssp * month);
                }
            });
            
            $("#sel-dictionary ,#sel-teacher-tool ,#student_num").change(function () {
                if($("#sel-dictionary").val() == 0 ){
                    $("#total-amount").html("0");
                }else{
                    var month = parseInt($("#sel-teacher-tool").val());
                    var student = parseInt($("#student_num").val());
                    $("#total-amount").html(dp * month * student /100);
                }
            });
        // handing change month and student "Merriam-Webster Dictionary" MODAL
            function merriam_webster_dictionary_price() {
                var student = parseInt($('#additional-subscription-dialog-continue #student-num').val());
                var month = parseInt($('#additional-subscription-dialog-continue #sel-teacher-tool').val());
                if($('#additional-subscription-dialog-continue #sel-dictionary').val()==0 ){
                }else{
                    $('#additional-subscription-dialog-continue #total-amount').html(student*month);
                }
            }
            $('#additional-subscription-dialog-continue #add-to-cart-ss-english-continute').click(function (e){
                    var id = $('#additional-subscription-dialog-continue #sel-dictionary').val();
                    if(id == 0) {
                        e.preventDefault();
                        $selbox = $("#additional-subscription-dialog-continue #sel-dictionarySelectBoxIt");
                        $selbox.popover({content: '<span class="text-danger">' +'You not selected dictionary'+ '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                        setTimeout(function () {
                            $selbox.popover("destroy")
                        }, 2000);
                    } else {
                    merriam_webster_dictionary_price();
                    }
            }); 
            $('#additional-subscription-dialog-continue #sel-dictionary, #additional-subscription-dialog-continue #sel-teacher-tool, #additional-subscription-dialog-continue #student-num').change(function(){
                merriam_webster_dictionary_price();
            });
        // handing change month and student "Teacher's Homework Tool" MODAL ENGLISH
            function teacher_homework_tool_price() {
                var student = parseInt($('#teacher-home-tool-modal-english #student-num-add-continute-english').val());
                var month = parseInt($('#teacher-home-tool-modal-english #sel-teacher-tool').val());
                console.log(student);
                console.log(month);
                if($('#teacher-home-tool-modal-english #sel-dictionary').val()==0 ){
                }else{
                    $('#teacher-home-tool-modal-english #total-amount-add-continute-english').html(student*month*ttp/100);
                }
            }
            $('#teacher-home-tool-modal-english #add-to-cart-ss-english-continute').click(function (e){
                    var id = $('#teacher-home-tool-modal-english #sel-dictionary').val();
                    if(id == 0) {
                        e.preventDefault();
                        $selbox = $("#teacher-home-tool-modal-english #sel-dictionarySelectBoxIt");
                        $selbox.popover({content: '<span class="text-danger">' +'You not selected dictionary'+ '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                        setTimeout(function () {
                            $selbox.popover("destroy")
                        }, 2000);
                    } else {
                    teacher_homework_tool_price();
                    }
            }); 
            $('#teacher-home-tool-modal-english #sel-dictionary, #teacher-home-tool-modal-english #sel-teacher-tool, #teacher-home-tool-modal-english #student-num-add-continute-english').change(function(){
                teacher_homework_tool_price();
            });
        });
     })(jQuery);