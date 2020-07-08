var flashs = [];
var change = false;
(function ($) {
    $(function () {
        

        toggle_fc_type($("#sel-fc-type").val());

        $("#sel-fc-folders").val(2);
        $("#sel-fc-folders").selectBoxIt('selectOption', folderid.toString()).data("selectBox-selectBoxIt");
        $("#sel-fc-folders").data("selectBox-selectBoxIt").refresh();
        // set default selected for sel-fc-folders
        if (!jQuery("#sel-fc-folders").val()) {
            jQuery("#sel-fc-folders option:first").attr('selected', 'selected');
        }

        $("#sel-fc-type").change(function () {
            //toggle_fc_type($(this).val());
        });

        $("#sel-fc-folders").change(function () {
            var fid = parseInt($(this).val());
            //var url = window.location.href;
            //location.href = home_url+'/?r=flash-cards&id='+fid;
            flashs = [];
            if (fid == 1) {
                setup_teacher_cards();
            } else {
                setup_my_own_cards(true);
            }
            update_header_width();
            $("#dictionary-block").hide();

        });

        $("#sel-teacher-sets").change(function () {
            setup_teacher_cards();
            update_header_width();
            $("#dictionary-block").hide();
        });

        function toggle_fc_type(id) {
            switch (id) {
                case "my-own":
                    $("#teacher-sets-block").hide();
                    $("#my-own-block").show();
                    setup_my_own_cards(true);
                    break;
                case "teacher-sets":
                    $("#my-own-block").hide();
                    $("#teacher-sets-block").show();
                    setup_teacher_cards();
                    break;
            }
            update_header_width();
        }

        function setup_my_own_cards(change) {

            if (folderid != 0 && !change) {
                var fid = folderid;
//                        var selectBox = $("select#sel-fc-folders").selectBoxIt().data("selectBoxIt");
//                        selectBox.selectOption(folderid);
            } else {
                var fid = parseInt($("#sel-fc-folders").val());
            }

            var rows, m;

            $("#teacher-sets-block").hide();
            $("#flashcard-set-header").hide();

            if (!$.isEmptyObject(fc_folders[fid])) {
                $.each(fc_folders[fid], function (i, v) {
                    if (!v.memorized) {
                        flashs.push(v);
                    }
                    m = v.memorized == 1 ? "<span class='icon-yes2'></span>" : "<span class='icon-no2'></span>";
                    rows += '<tr>' +
                            '<td class="fh">' + v.word + '</td>' +
                            '<td><input type="text" class="flashcard-note" data-id="' + v.word_id + '" autocomplete="off" value="' + v.notes + '"></td>' +
                            '<td><a class="toggle-memorized" data-id="' + v.word_id + '" href="#">' + m + '</a></td>' +
                            '<td><a href="#" data-id="' + v.word_id + '" class="delete-card">delete</a></td>' +
                            '</tr>';
                });
                $("#fc-table tbody").html(rows);
            } else {
                flashs = [];
                $("#fc-table tbody").html('<tr><td>There\'s no flashcard in this folder</td></tr>');
            }
        }

        function setup_teacher_cards() {
            if (fc_sets.length > 0) {
                $("#teacher-sets-block").show();
                $("#flashcard-set-header").show();
                var setid = parseInt($("#sel-teacher-sets").val());
                $("#set-header span").html(fc_sets[setid].header);
                $("#set-teacher span").html(fc_sets[setid].teacher);
                $("#set-group span").html(fc_sets[setid].group);
                $("#set-date span").html(fc_sets[setid].date);
                $("#set-comment span").html(fc_sets[setid].comment);
                var rows;
                flashs = [];
                if (!$.isEmptyObject(fc_sets[setid].words)) {
                    $.each(fc_sets[setid].words, function (i, v) {
                        if (!v.memorized) {
                            flashs.push(v);
                        }
                        m = v.memorized == 1 ? "<span class='icon-yes2'></span>" : "<span class='icon-no2'></span>";
                        rows += '<tr>' +
                                '<td class="fh">' + v.word + '</td>' +
                                '<td><input type="text" class="flashcard-note" data-id="' + v.word_id + '" autocomplete="off" value="' + v.notes + '"></td>' +
                                '<td><a class="toggle-memorized" data-id="' + v.word_id + '" href="#">' + m + '</a></td>' +
                                '<td></td>' +
                                '</tr>';
                    });
                    $("#fc-table tbody").html(rows);
                } else {
                    flashs = [];
                    $("#fc-table tbody").html('<tr><td>There\'s no flashcard in this folder</td></tr>');
                }
            }
        }

        $("#fc-table").on("click", ".fh", function (e) {
            e.preventDefault();
            var a = $(this).parent().find("a");
            $.get(home_url + "/?r=ajax/flashcard/lookup", {id: a.attr("data-id")}, function (data) {
                $("#dictionary-block").show();
                $("#dictionary-block").html(data);

            });
        });

        var timer;
        $("#fc-table").on("keyup", ".flashcard-note", function (e) {
            e.preventDefault();
            var tthis = $(this);
            clearTimeout(timer);
            timer = setTimeout(function () {
                var $id = tthis.attr("data-id");
//                            if($("#sel-fc-type").val() == "teacher-sets"){
//                                    fc_sets[parseInt($("#sel-teacher-sets").val())].words["w" + $id].notes = tthis.val();
//                            }else{
//                                    fc_folders[parseInt($("#sel-fc-folders").val())]["w" + $id].notes = tthis.val();
//                            }
                $.post(home_url + "/?r=ajax/flashcard/savenotes", {id: $id, notes: tthis.val()});
            }, 500);
        });

        $("#fc-table").on("click", ".toggle-memorized", function (e) {
            e.preventDefault();
            var $id = parseInt($(this).attr("data-id"));
            $.post(home_url + "/?r=ajax/flashcard/memorized", {id: $id}, function (data) {
                if($("#sel-fc-type").val() == "teacher-sets"){
                        fc_sets[parseInt($("#sel-teacher-sets").val())].words["w" + $id].memorized = Math.abs(fc_sets[parseInt($("#sel-teacher-sets").val())].words["w" + $id].memorized - 1);
                }else{
                        fc_folders[parseInt($("#sel-fc-folders").val())]["w" + $id].memorized = Math.abs(fc_folders[parseInt($("#sel-fc-folders").val())]["w" + $id].memorized - 1);
                }
            });
            var $child = $(this).children();
            if ($child.hasClass("icon-yes2")) {
                $child.removeClass("icon-yes2");
                $child.addClass("icon-no2");
                if ($("#sel-fc-type").val() == "teacher-sets") {
                    flashs.push(fc_sets[parseInt($("#sel-teacher-sets").val())].words["w" + $id]);
                } else {
                    flashs.push(fc_folders[parseInt($("#sel-fc-folders").val())]["w" + $id]);
                }
            } else {
                $child.removeClass("icon-no2");
                $child.addClass("icon-yes2");
                $.each(flashs, function (i, v) {
                    if (v.word_id == $id) {
                        flashs.splice(i, 1);
                        return false;
                    }
                });
            }
        });

        $("#fc-table").on("click", ".delete-card", function (e) {
            e.preventDefault();
            $.post(home_url + "/?r=ajax/flashcard/delete", {id: $(this).attr("data-id")});
            //delete fc_folders[parseInt($("#sel-fc-folders").val())]["w" + parseInt($(this).attr("data-id"))];
            $(this).parents("tr").remove();
        });
        $('#btn-ok-close').click(function(){
            $('#modal-message-not-delete').modal('hide');
        });
        $("#flash-card-mode").click(function () {
            if (flashs.length == 0) {
                $("#require-modal").modal();
            } else {
                setup_flash();
                $("#flashcard-modal").modal();
            }
        });

        $("#memorized-radio").change(function (e) {
            e.preventDefault();
            if ($(this).is(":checked")) {
                if ($("#sel-fc-type").val() == "teacher-sets") {
                    fc_sets[parseInt($("#sel-teacher-sets").val())].words['w' + flashs[0].word_id].memorized = 1;
                } else {
                    fc_folders[parseInt($("#sel-fc-folders").val())]['w' + flashs[0].word_id].memorized = 1;
                }
                $.post(home_url + "/?r=ajax/flashcard/memorized", {id: flashs[0].word_id, memorized: 1});
                flashs.splice(0, 1);
            }
        });
        $('#as-memory').click (function (){
            if ($("#sel-fc-type").val() == "teacher-sets") {
                    fc_sets[parseInt($("#sel-teacher-sets").val())].words['w' + flashs[0].word_id].memorized = 1;
                } else {
                    fc_folders[parseInt($("#sel-fc-folders").val())]['w' + flashs[0].word_id].memorized = 1;
                }
            $.post(home_url + "/?r=ajax/flashcard/memorized", {id: flashs[0].word_id, memorized: 1});
                flashs.splice(0, 1);
        });
        $('#flashcard-modal').on('hidden.bs.modal',function(){
            flashs = [];
            var fid = parseInt($("#sel-fc-folders").val());
            if (fid == 1) {
                setup_teacher_cards();
            } else {
                setup_my_own_cards(true);
            }
            update_header_width();
            $("#dictionary-block").hide();
        });
        $("#next-flashcard").click(function () {
            setup_flash();
        });
        
        function setup_flash() {
            if(!jQuery.isEmptyObject(flashs)){
                shuffle(flashs);
                $("#answer-block").text(flashs[0].word);
                //$("#hints").text('My sentense: My gr');
                $("#memorized-radio").prop("checked", false);
            }
            else {
                $('#flashcard-modal').modal('hide');
                $('#message-full-memorized').modal('show');
            }
        }
        $('#memorized-ok').click(function(){
            $('#message-full-memorized').modal('hide');
        });

        $("body").tooltip({
            selector: '[data-toggle="tooltip"]',
            container: "body",
            trigger: "focus",
            html: true
        });

        $(".scroll-list2").mCustomScrollbar({
            theme: "rounded",
            mouseWheel: {scrollAmount: 120},
            callbacks: {
                onOverflowY: function () {
                    $(this).css("padding-right", "5px");
                    $(this).find(".mCSB_inside > .mCSB_container").css("margin-right", "20px");
                    update_header_width();
                },
                onOverflowYNone: function () {
                    $(this).css("padding-right", "");
                    $(this).find(".mCSB_inside > .mCSB_container").css("margin-right", "");
                    update_header_width();
                }
            }
        });

        $(window).resize(function () {
            update_header_width();
        });

        function update_header_width() {
            if ($(".flashcard-table tr td").length > 1) {
                $(".flashcard-table-header > div:first-child").outerWidth($(".flashcard-table tr td:first-child").outerWidth());
                $(".flashcard-table-header > div:nth-child(2)").outerWidth($(".flashcard-table tr td:nth-child(2)").outerWidth());
                $(".flashcard-table-header > div:nth-child(3)").outerWidth($(".flashcard-table tr td:nth-child(3)").outerWidth());
            }
        }
        jQuery(".flashcard-table-content").mCustomScrollbar({
            axis: "y",
            theme: "rounded-dark",
            scrollButtons: {enable: true}
        });
        var fc_name = $("#fc-folder-name1");
       $('#btn-create').click(function(e) {
            e.preventDefault();
            $selbox = $("#fc-folder-name1");
            if ($selbox.val().trim() == "" || $selbox.val().toLowerCase()=='sample' || $selbox.val().toLowerCase()=='teacher') {
                $selbox.popover({content: '<span class="text-danger">' +'You have not entered a folder name or folder name already exits.'+ '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                setTimeout(function () {
                    $selbox.popover("destroy")
                }, 2000);
                $valid = false; 
            }
            else {
                $.post(home_url + "/?r=ajax/flashcard/check_exist_folder",{name_folder: $selbox.val()}, function(data){
                    if(data == 1) {
                        var text = 'Name folder already exits. Please enter a different folder name!';
                        $selbox.popover({content: '<span class="text-danger">' +text+ '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                        setTimeout(function () {
                            $selbox.popover("destroy")
                        }, 2000);
                        $valid = false;
                    } else {
                        $.post(home_url + "/?r=ajax/flashcard/addfolder1",{n: fc_name.val()}, function(data){
                        $('#fl-create-folder-modal').modal('hide'); 
                        $("#create-folder-success").modal('show');
                        });   
                    }
                });
            }
        });
        $('#create-ok').click(function (){
            //$("#create-folder-success").modal('hide');
            location.reload(); 
        }); 
        $('#create-folder').click(function (){
            if(!check_sub_any_dic){
                $("#require-modal1").modal();       
            } else {
                $('#fl-create-folder-modal').modal('show');
            }
         });
        $('.sub-dictionary-now').click(function () {
            $("#require-modal1").modal('hide');
            $("#modal-sub-dictionary").modal('show');
        });
        $('#ok-modal-req-sub').click(function () {
            $("#require-modal1").modal('hide');
            $("#modal-sub-dictionary").modal('show');
        });
        $('#close-modal').click(function () {
            ('#require-modal1').modal('hide');
        });
        $('#fc-folder-form').click(function () {
            $('#fl-create-folder-modal').modal('hide');
        });
        $('#flash-card-delete-folder').click(function () {
            $name_folder = $("#sel-fc-folders option:selected").text();
            //$name_folder = $('#sel-fc-folders').val();
            if ($name_folder == 'Sample' || $name_folder == 'Teacher') {
                $('#modal-message-not-delete').modal('show');
            } else {
                $('#fl-delete-folder-modal').modal('show');
            }
        });
        $('#delete-folder-cancel').click(function () {
            $('#fl-delete-folder-modal').modal('hide');
        });
        $('#delete-folder-ok').click (function (e){
            e.preventDefault();
            $.post(home_url + "/?r=ajax/flashcard/deletefolder",{n: $name_folder}, function(data){
                   location.reload(); 
                });  
        });
        function price_subcrible_dictionary() {
            $("#addi-sub-type").val(2);
            var students = isNaN(parseInt($("#student_num").val())) ? 0 : parseInt($("#student_num").val());
            var months = isNaN(parseInt($("#sel-teacher-tool").val())) ? 0 : parseInt($("#sel-teacher-tool").val());
            var p = $("#sel-dictionary").val() == "6" ? adp : dp;
            $("#total-amount").text(students * months * p / 100);
        }
        $("#sel-teacher-tool,#student_num,#sel-dictionary").change(function () {
            if ($('#sel-dictionary option:selected').val() == '') {
                $("#total-amount").text(0);
            } else {
                price_subcrible_dictionary();
            }
        });
        $('#add-to-cart').click(function (e) {
            $selected = $('#sel-dictionary option:selected');
            if ($selected.val() == '') {
                e.preventDefault();
                $selbox = $("#sel-dictionarySelectBoxItContainer");
                $selbox.popover({content: '<span class="text-danger">' + 'You not selected dictionary' + '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                setTimeout(function () {
                    $selbox.popover("destroy")
                }, 2000);
            } else {
                price_subcrible_dictionary();
            }
        });
        $('#save-change').click(function () {
            $selbox = $(this);
            $selbox.popover({content: '<span class="text-success">' + "Successfully Saved" + '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
            setTimeout(function () {
                $selbox.popover("destroy")
            }, 500);
            $valid = false;
        });
        $('#save-change').on('hidden.bs.popover', function () {
            window.location.reload();
        })
        $('#btn-ok-save-changes').click(function (e) {
            e.preventDefault();
            var id_folder = $('#save-change').attr('data-id');
            window.location.href += '&id' +id_folder;
            location.reload();
        });
        $('#flashcard-modal').on('hidden.bs.modal', function () {
            $('#message-modal-save-changes').modal('hide');
//            location.reload();
        });
        $('#btn-ok-full-memory').click(function (){
            $('#require-modal').modal('hide');
        });
        $(function() {
            var url = window.location.href;
            var id = url.split("&id");
            $("#sel-fc-folders").selectBoxIt('selectOption', id[1].toString()).data("selectBox-selectBoxIt");
            $("#sel-fc-folders").data("selectBox-selectBoxIt").refresh();
        });
    });
})(jQuery);