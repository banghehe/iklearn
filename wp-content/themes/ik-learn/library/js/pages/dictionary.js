(function($){
	$(function(){
		$("#search-form").submit(function(e){
			e.preventDefault();
			window.location.href = home_url + "/?r=dictionary/" + dictionary_slug + "/" + $("#keyword").val();
		});

		$(".history-see-more").click(function(e){
			e.preventDefault();
			$("#search-history-modal").modal();
		});

		$(".xbtn").click(function(e){
                        e.preventDefault();
			var e = $(this).attr("data-entry");
			$.post(home_url + "/?r=ajax/history/remove",{id: e, d: dictionary_slug});
			$("[data-entry='" + e + "']").parent().remove()
		});

		var timer;
		$("#keyword").keyup(function(e){
                    e.preventDefault();
//                    if(!is_login) {
////                        jQuery("#show_login").click();
//                        $("#subscribe-modal-dialog").modal("show");
//                    }else {
			var kw = $("#keyword");
			if(kw.val().trim() ==""){return false;}
			clearTimeout(timer);
			timer = setTimeout(function(){
				$.get(
					home_url + "/?r=ajax/dictionary",
					{d: dictionary_slug, w: kw.val()},
					function(data){
						$("#seach-results").html(data);
						$("#seach-results").show();						
					}
				);
				kw.focus();
			}, 600);
//                    }
		});

		$("#keyword").blur(function(){
			setTimeout(function(){$("#seach-results").hide();}, 300);			
		});

		$(".english-quiz-tab-sm, .english-quiz-tab").click(function(e){
			e.preventDefault();
			$(".quiz-box").removeClass().addClass("quiz-box quiz-english");
			$("#quiz-header-title").text("English");
			$(".quiz-get-more").show();
			$("#quiz-answer").text("");
			trivia = 1;
			get_quiz();
		});

		$(".science-quiz-tab-sm, .science-quiz-tab").click(function(e){
			e.preventDefault();
			$(".quiz-box").removeClass().addClass("quiz-box quiz-science");
			$("#quiz-header-title").text("Science");
			$(".quiz-get-more").hide();
			$("#quiz-answer").text("");
			trivia = 2;
			get_quiz();
		});
                $(".close-submodal").click(function () {
                    location.href = home_url + "/?r=dictionary/" + dictionary_slug+"&a=1";
                });
		$(".history-quiz-tab-sm, .history-quiz-tab").click(function(e){
			e.preventDefault();
			$(".quiz-box").removeClass().addClass("quiz-box quiz-history");
			$("#quiz-header-title").text("History");
			$(".quiz-get-more").hide();
			trivia = 3;
			$("#quiz-answer").text("");
			get_quiz();
		});

		$(".general-quiz-tab-sm, .general-quiz-tab").click(function(e){
			e.preventDefault();
			$(".quiz-box").removeClass().addClass("quiz-box quiz-general");
			$("#quiz-header-title").text("General");
			$(".quiz-get-more").hide();
			$("#quiz-answer").text("");
			trivia = 4;
			get_quiz();
		});

		$("#next-quiz").click(function(e){
			e.preventDefault();
			$("#quiz-answer").text("");
			get_quiz();
		});

		$("#get-answer").click(function(e){
			e.preventDefault();
			$("#quiz-answer").text(trivia_a);
		});

		$("#save-flash-cards").click(function(){
			if(!parseInt($(this).attr("data-islogin"))){
				$("#require-modal1 .modal-header h3").html(JS_MESSAGES.login_req_h);
				$("#require-modal1 .modal-body").html(JS_MESSAGES.login_req_err);
				$("#require-modal1 .modal-footer a").attr("href", home_url + "/?r=login").html('<span class="icon-user"></span> ' + JS_MESSAGES.login_req_lbl);
				$("#require-modal1").modal();
				return;
			}
			$("#save-flashcard-modal").modal();
		});

		$("#add-flashcard").click(function(e){
                    e.preventDefault();
                    var tthis = $(this);
                    var scrolldiv = tthis.parents(".modal-content").find(".scroll-list3");
                    if(annoying){           
                            $("#save-flashcard-modal").modal('hide');
                            $("#require-modal1").modal();
                    }else{
                        var fid = $("[name='flashcard-folders']:checked").val();
                        if(typeof fid == "undefined"){
                            scrolldiv.attr("title", JS_MESSAGES.folder_sel).tooltip("fixTitle").tooltip("show");
                            setTimeout(function(){scrolldiv.tooltip("hide")}, 1500);
                        }else {
                            $.post(home_url + "/?r=ajax/flashcard/check_exist",{id_folder: fid,word: entry}, function(data){
                                if(data == 1) {
                                    $("#save-flashcard-modal").modal("hide");
                                    $('#message-modal-save').modal('show');
                                    $('#message-save').text('You already have same word in the folder. Please select a different word.');
                                } else {
                                    tthis.button("loading");
                                    $.post(home_url + "/?r=ajax/flashcard/addcard",{did: dictionary_slug, e: entry, fid: fid}, function(data){
                                        data = JSON.parse(data);
                                        tthis.button("reset");
                                        if(data.status == 1){
                                            $("#save-flashcard-modal").modal("hide");
                                        }
                                    });
                                    $("#save-to-folder-modal").modal();
                                    $('#message-modal-save').modal('show');
                                    $('#message-save').text('Successfully saved word to flash card.');
                                }
                            }); 
                        }
                    }
		});
                $('#btn-ok-save').click (function (){
                    $('#message-modal-save').modal('hide');
                });
		$("#fc-folder-form").click(function(){
			$("#save-flashcard-modal").modal("hide");
                        if(annoying){
						$("#require-modal1").modal();
					}else{
						$("#create-folder-modal").modal();
					}
		});
                
				
		$("#create-folder-modal").on("shown.bs.modal", function(){
			$("#fc-folder-name").focus();
		});

		$("#create-flashcard-folder").click(function(e){
                        e.preventDefault();
			var fc_name = $("#fc-folder-name1");
			var tthis = $(this);
			if(fc_name.val().trim() != ""){
				tthis.button("loading");
				$.post(home_url + "/?r=ajax/flashcard/addfolder",{did: dictionary_slug,n: fc_name.val()}, function(data){
					var fid = JSON.parse(data);
					var ele = '<li>' +
								'<div class="radio radio-style2">' +
									'<input id="folder_' + fid[0] + '" type="radio" name="flashcard-folders" value="' + fid[0] + '" checked>' +
									'<label for="folder_' + fid[0] + '">' + fc_name.val() + '</label>' +
								'</div>' +
							'</li>';
					$(".flashcard-folders").append(ele);
					fc_name.val("");
					tthis.button("reset");
				});
			}
			$("#create-folder-modal").modal("hide");
			$("#create-folder-modal").one("hidden.bs.modal", function(){
				$("#save-flashcard-modal").modal();
			});
		});

		$(".scroll-list3").mCustomScrollbar({
			theme: "rounded",
			mouseWheel:{scrollAmount:120}
		});
                $('#ok-save-to-folder-modal').click(function (){
                    $('#save-to-folder-modal').modal('hide');
                });
                $('#ok-modal-req-sub').click(function (){
                    $('#require-modal1').modal('hide');
                    jQuery('#show_login').click();
                });
// Show modal modal-subscrible-dictionary-now                
                $('.sub-dictionary-now').click (function (e){
                    e.preventDefault();
                    $('#require-modal1').modal('hide');
                    $('#modal-subscrible-dictionary-now').modal('show');
                });
                $('.sub-dictionary-now1').click (function (e){
                    e.preventDefault();
                    $('#save-flashcard-modal').modal('hide');
                    $('#modal-subscrible-dictionary-now').modal('show');
                });
// handing price on modal-subscrible-dictionary-now
                function price_subcrible_dictionary() {
                    $("#addi-sub-type").val(2);
                    var students = isNaN(parseInt($("#student_num").val())) ? 0 : parseInt($("#student_num").val());
                    var months = isNaN(parseInt($("#sel-teacher-tool").val())) ? 0 : parseInt($("#sel-teacher-tool").val());
                    var p = $("#sel-dictionary").val() == "6" ? adp : dp;                  
                    $("#total-amount").text(students * months * p / 100);
                }
                $("#sel-teacher-tool,#student_num,#sel-dictionary").change(function () {
                    if($('#sel-dictionary option:selected').val()=='') { 
                        $("#total-amount").text(0);
                    } else {
                    price_subcrible_dictionary();
                }
                });
                $('#add-to-cart').click(function (e){
                    $selected = $('#sel-dictionary option:selected');
                    if($selected.val() == '') {
                        e.preventDefault();
                        $selbox = $("#sel-dictionarySelectBoxItContainer");
                        $selbox.popover({content: '<span class="text-danger">' +'You not selected dictionary'+ '</span>', html: true, trigger: "hover", placement: "bottom"}).popover("show");
                        setTimeout(function () {
                            $selbox.popover("destroy")
                        }, 2000);
                    } else {
                       price_subcrible_dictionary();
                    }
                });
	});
})(jQuery);

function get_quiz()
{
	jQuery("#quiz-loader").fadeIn();
	jQuery.getJSON(home_url + "/?r=ajax/randomquiz",
		{
			d: dictionary_slug,
			c: trivia
		},
		function(data){
			jQuery("#quiz-loader").fadeOut();
			jQuery("#quiz-qe").html(data.quiz.q);
			jQuery("#quiz-q").html(data.quiz.sentence);
			shuffle(data.quiz.choice);
			jQuery("#quiz-a-1").html('<span class="q-sn">(<span class="semi-bold">A</span>)</span> ' + data.quiz.choice[0]);
			jQuery("#quiz-a-2").html('<span class="q-sn">(<span class="semi-bold">B</span>)</span> ' + data.quiz.choice[1]);
			jQuery("#quiz-a-3").html('<span class="q-sn">(<span class="semi-bold">C</span>)</span> ' + data.quiz.choice[2]);
			if(trivia == 1){
				jQuery("#quiz-level").text(data.level);
				jQuery("#quiz-lesson").text(data.lesson);
			}
			trivia_a = data.quiz.ca;
		}
	);
}