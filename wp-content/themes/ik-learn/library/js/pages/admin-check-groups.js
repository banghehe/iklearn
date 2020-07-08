/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($) {
            $("#update-btn").click(function (e){
                e.preventDefault();
                var id= $('#homework-id').val();
                if($('#hw-for-test').prop('checked')){
                    var testcheck=0;
                 }
                if($('#hw-for-practice').prop('checked')){
                    var testcheck=1;
                 }
                if($('#is-retryable-no').prop('checked')){
                     var retryablecheck=0;
                 }
                if($('#is-retryable-yes').prop('checked')){
                    var retryablecheck=1;
                 }
                if($('#rdo-no').prop('checked')){
                    var displaycheck=1;
                 }else{
                     var displaycheck=0;
                    }
                var deadline=$('#deadline').datepicker({ dateFormat: 'dd-mm-yy' }).val();
                if(deadline==''||deadline==null){
                    deadline='0000-00-00';
                }
                var time= deadline.split("/");
                deadline=time[2]+'-'+time[1]+'-'+time[0];
                alert(deadline);
                    $.post(home_url + "/?r=ajax/update/updatehomework", {id: id,is_retryable :retryablecheck,for_practice: testcheck, deadline:deadline, teacherlastpage:displaycheck}, function (data) {
                        $("#assign-homework-modal").modal("hide")
                    });
            });
            $(".delete-homework").click(function (e){
                e.preventDefault();
                var removehomework = $(this).parents('.grid-table-row');
                if (confirm('Are you sure you want to DELETE this?')) {
                    $.post(home_url + "/?r=ajax/delete/deletehomework", {id: $(this).attr('data-id') }, function (data) {
                         removehomework.remove();
                    });
                } else {
                }
            });
            $(".edit-homework").click(function (){
                var id=$(this).attr('data-id');
                var name=$(this).attr('data-name');
                var practice=$(this).attr('data-practice');
                var retryable=$(this).attr('data-retryable');
                var deadline=$(this).attr('data-deadline');
                var displaylastpage=$(this).attr('data-displaylastpage');
                if(practice==0){
                    $("#hw-for-test").attr('checked', 'checked');
                }else{
                    $("#hw-for-practice").attr('checked', 'checked');
                }
                if(retryable==0){
                    $("#is-retryable-no").attr('checked', 'checked');
                }else{
                    $("#is-retryable-yes").attr('checked', 'checked');
                    
                }
                 $('#homework-name').text(name);
                 if(deadline=="0000-00-00"){
                     var timeformat=null;
                 }else{
                    var time= deadline.split("-");
                    var timeformat=time[2]+'/'+time[1]+'/'+time[0];
                 }
                if(displaylastpage==1){
                    $("#rdo-no").attr('checked', 'checked');
                }
                $('#deadline').val(timeformat);
                $('#homework-id').val(id);
                $("#assign-homework-modal").modal()
            });
        $(function () {
                get_sel_groups_options($("#sel-group-types").val());

		$("#sel-group-types").change(function(){
			get_sel_groups_options($(this).val());
		});

		function get_sel_groups_options(id){
			var ops = '<option value="">Select group</option>' + $("#class-group" + id).html();
			if(typeof $("#sel-group").html(ops).data("selectBox-selectBoxIt") !== "undefined")
				$("#sel-group").html(ops).data("selectBox-selectBoxIt").refresh();
		}
                $("#deadline").datepicker({minDate: 0, maxDate: "+1M",dateFormat: 'dd/mm/yy'});

		$("#show-datepicker").click(function(e){
			e.preventDefault();
			$("#deadline").datepicker("show");
		});
            $("#toggle-active").click(function () {
                var check_count = $('[name="cid[]"]:checked').length;
                if (check_count == 0) {
                    $("#confirm-modal .modal-body").html("You must select a Group first.");
                    $("#confirm-modal .modal-footer > .row").html('<div class="col-md-12"><a href="#" data-dismiss="modal" class="btn btn-block grey"><span class="icon-cancel"></span>Back</a></div>');
                } else {
                    $("#task").val("toggle-active");
                    $("#confirm-modal .modal-body").html("You are about to Active/Deactive " + check_count + " Groups.<br>Do you want to process?");
                    $("#confirm-modal .modal-footer > .row").html('<div class="col-md-6"><a href="#" data-dismiss="modal" id="btnConfirm" class="btn btn-block orange confirm"><span class="icon-accept"></span>Yes</a></div><div class="col-md-6"><a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-block grey secondary"><span class="icon-cancel"></span>No</a></div>');
                }
                $("#confirm-modal").modal();
            });

            $("body").on("click", "#btnConfirm", function () {
                $("#main-form").submit();
            });
        });
    })(jQuery);

