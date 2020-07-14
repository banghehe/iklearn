(function($){
	$(function(){
            $("#sel-self-study-months").change(function(){
                month=parseInt($( "#sel-self-study-months option:selected" ).val());
                if($('#check-sub-tutoring').val()==1){
                    $('#ss-total-amount').text(month*MATH_INTENSIVE_TUTORING);
                }else{
                    $('#ss-total-amount').text(month*MATH_TUTORING);
                }
		});
//            $('#btn-sub-basic').click(function (){
//                
//            });
//            $('#btn-sub-intensive').click(function (){
//                
//            });
            $('#start-tutoring-basic').click(function (){
                if(BASIC!=true){
                    if(confirm('you need subscription "Math Tutoring Basic Plan" before start lession')){
                        $('#no-of-points').val(MATH_TUTORING);
                        $('#math-tutoring-sub-type').val(26);
                        $('#p-tutoring').text(NAME_MATH_TUTORING);
                        $('#sat-class').val(54);
                        $('#check-sub-tutoring').val(0);
                        month=parseInt($( "#sel-self-study-months option:selected" ).val());
                        $('#ss-total-amount').text(month*MATH_TUTORING);
                        $('#self-study-subscription-dialog').modal();
                    }
                }else{
                    
                }
            });
            $('#start-tutoring-intensive').click(function (){
                if(INTENSUVE!=true){
                    if(confirm('you need subscription "Math Tutoring Intensive Plan" before start lession')){
                        $('#no-of-points').val(MATH_INTENSIVE_TUTORING);
                        $('#math-tutoring-sub-type').val(27);
                        $('#sat-class').val(55);
                        $('#p-tutoring').text(NAME_MATH_INTENSIVE_TUTORING);
                        $('#check-sub-tutoring').val(1);
                        month=parseInt($( "#sel-self-study-months option:selected" ).val());
                        $('#ss-total-amount').text(month*MATH_INTENSIVE_TUTORING);
                        $('#self-study-subscription-dialog').modal();
                    }
                }else{
                    
                }
            });
	});
})(jQuery);