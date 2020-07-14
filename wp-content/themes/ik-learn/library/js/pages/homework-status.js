(function($){
	$(function(){
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
                            document.getElementById('text-warning1').style.color = '#FFFF00';
                            $("#text-warning1").html('This group is subscription only. Monthly $'+data.data+'.'+' Agree?');
                            $("#join-group-dialog").modal();
                        },
                    });
                });

		$("#join-group-dialog").on("show.bs.modal", function (e) {
			$("#rdo-yes").prop("checked", true);
		});

		$(".leave-grp-btn").click(function(e){
			var tthis = $(this);
			$("#lev-group-name").text(tthis.attr("data-gname"));
			$("#gid").val(tthis.attr("data-gid"));
			$("#leave-group-dialog").modal();
		});

		$(".request-grading").click(function(){
			$("#grading-cost").text($(this).attr("data-cost"));
			$("#hrid").val($(this).attr("data-hrid"));
			$("#hid").val($(this).attr("data-hid"));
			if(ypoints < parseInt($(this).attr("data-cost"))){
				$("#request-grading-err").html(JS_MESSAGES.point_err);
			}
			$("#request-grading-dialog").modal();
		});

		$(".goto-homework").click(function(e){                 
			e.preventDefault();
                        if(!annoying){
			
                                var html = "";
                                if($(this).attr("data-for-practice") == 1){
                                        html += JS_MESSAGES.practice_inst;
                                        $("#btn-practice").attr("href", $(this).attr("data-practice-url") + "&ref=" + $("#uref").val());
                                }else{
                                        html += JS_MESSAGES.test_inst;
                                        $("#btn-practice").attr("href", $(this).attr("data-homework-url") + "&ref=" + $("#uref").val());
                                }
                                if($("#unfinished_homework").val() == 1 && $(this).attr("data-startnew") == 1){
                                        html += '<hr><p class="text-warning2">' + JS_MESSAGES.unfinished_homework + '</p>';
                                }
                                $("#switch-mode-dialog .modal-body").html(html);

                                $("#switch-mode-dialog").modal("show");
			}else{
				var modal = $("#require-modal");
				if(!isuserloggedin){
					modal.find("h3").text(JS_MESSAGES.login_req_h);
					modal.find(".modal-body").html(JS_MESSAGES.login_req_err);
					modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + JS_MESSAGES.login_req_lbl);
				}else{
					modal.find("h3").text(JS_MESSAGES.sub_req_h);
					modal.find(".modal-body").html(JS_MESSAGES.sub_req_err);
					modal.find(".modal-footer a").html('<span class="icon-issue2"></span> ' + JS_MESSAGES.sub_req_lbl);
				}
				modal.modal();
			}
		});
	});		
})(jQuery);