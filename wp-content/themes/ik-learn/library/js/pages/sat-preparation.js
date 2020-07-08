(function ($) {
    $(function () {
        $(".class-detail-btn").click(function (e) {
            e.preventDefault();
            var modal = $("#class-detail-modal");
            modal.find(".modal-body").html($(this).next().html());
            modal.modal();
        });
        $(".btn-subscrible").click(function(){
            var type = $(this).attr("data-type"); //type để kiểm tra xem nó là loại nào 1-7 40-50,...
            
            if(check_login == "0")  // chưa login
            {
                $("#modal-show-login").modal("show");
            }else{
                $.get(home_url+"/?r=ajax/check_sub_by_type",{type:type},function (data){  // if(data==1 thì đã subcrible)
                    var array_english =[1,2,3,4,5,6,7] ;
                    var ck = 0; // 
                    var array_sat1 = [9,10,11,12,13,14];
                    var ck1 = 0; // 
                    var array_sat2 = [15,16,17,18,19,20];
                    var ck2 = 0; // 
                    var array_ikmath = [38,39,40,41,42,43,44,45,46,47,48,49,50];
                    var ck3 = 0; // 
                    for(var i=0 ; i< array_english.length;i++) {
                        if(array_english[i]==type) {
                            ck=1;
                        }
                    }
                    for(var i=0 ; i< array_sat1.length;i++) {
                        if(array_sat1[i]==type) {
                            ck1=1;
                        }
                    }
                    for(var i=0 ; i< array_sat2.length;i++) {
                        if(array_sat2[i]==type) {
                            ck2=1;
                        }
                    }
                    for(var i=0 ; i< array_ikmath.length;i++) {
                        if(array_ikmath[i]==type) {
                            ck3=1;
                        }
                    }
                    if(ck==1) {
                        if(type ==1) {
                            $('#grammar-subscrible-modal').modal('show');
                        }else if(type ==2){
                            $('#writing-subscrible-modal').modal('show');
                        }else {
                            $("#select-sat-test-class").selectBoxIt('selectOption', type.toString()).data("selectBox-selectBoxIt");
                            $("#select-sat-test-class").data("selectBox-selectBoxIt").refresh();
                            $('#sat-test-subscrible-modal').modal('show');
                        }
                    }else if(ck1==1) {
                        if(type ==9) {
                            $('#sat1-preparation-subscrible-modal').modal("show");
                        }else {
                            $("#select-sat1-class").selectBoxIt('selectOption', type.toString()).data("selectBox-selectBoxIt");
                            $("#select-sat1-class").data("selectBox-selectBoxIt").refresh();
                            $('#sat1-subscrible-modal').modal("show");
                        }
                    }else if(ck2==1) {
                        if(type ==15) {
                            $('#sat2-preparation-subscrible-modal').modal("show");
                        }else {
                            $("#select-sat2-class").selectBoxIt('selectOption', type.toString()).data("selectBox-selectBoxIt");
                            $("#select-sat2-class").data("selectBox-selectBoxIt").refresh();
                            $('#sat2-subscrible-modal').modal("show");
                        }
                    }else if(ck3==1) {
                        $("#select-class-ikmath-course").selectBoxIt('selectOption', type.toString()).data("selectBox-selectBoxIt");
                        $("#select-class-ikmath-course").data("selectBox-selectBoxIt").refresh();
                        $('#ikmath-course-subscrible-modal').modal('show');
                    }
                });
            } 
        });
        function calc_grammar_price() {
            var months = parseInt($("#grammar-month").val());
            $("#total-amount-grammar").text(months * satGp);
        }
        function calc_writing_price() {
            var months = parseInt($("#writing-month").val());
            $("#total-amount-writing").text(months * satWp);
        }
        function calc_sat_test_price() {
            var months = parseInt($("#select-sat-test-month").val());
            $("#total-sat-test").text(months * satStp);
        }
        function calc_sat1_price() {
            var months = parseInt($("#select-sat1-months").val());
            $("#total-amount-sat1-class").text(parseInt(months * satMIP))  
        }
        function calc_sat1_preparation_price() {
            var months = parseInt($("#select-sat1-preparation-months").val());
            $("#total-amount-sat1-preparation").text(parseInt(months * sat1Pre));  
        }
        function calc_sat2_preparation_price() {
            var months = parseInt($("#select-sat2-preparation-months").val());
            $("#total-amount-sat2-preparation").text(parseInt(months * sat2Pre));  
        }
        $('#sat1-preparation-subscrible-modal').on('show.bs.modal',function (){
            calc_sat1_preparation_price();
        });
        $('#sat-test-subscrible-modal').on('show.bs.modal',function (){
            $("#select-sat-test-month").selectBoxIt('selectOption', "1".toString()).data("selectBox-selectBoxIt");
            $("#select-sat-test-month").data("selectBox-selectBoxIt").refresh();
            calc_sat_test_price();
        });
        $('#grammar-subscrible-modal').on('show.bs.modal',function (){
            calc_grammar_price();
        });
        $('#grammar-month').change (function (){
            calc_grammar_price();
        });
        $('#sel-month-ikmath-course,#select-class-ikmath-course').change (function (){
            calc_ikmath_course_price();
        });
        $('#ikmath-course-subscrible-modal').on('show.bs.modal',function (){
            calc_ikmath_course_price();
        });
        $('#writing-month').change (function (){
            calc_writing_price();
        });
        $('#select-sat-test-class').change (function (){
            var class_name = $('#select-sat-test-class').val();
            $('#sat-test-class').val(class_name);
        });
        $('#select-sat-test-month,#select-sat-test-class').change (function (){
            calc_sat_test_price();       
        });
        $('#select-sat1-months').change(function (){
            calc_sat1_price();
        });
        $('#select-sat1-class').change(function (){
            var sat1_class = $('#select-sat1-class').val();
            $('#sat1-class').val(sat1_class);
            calc_sat1_price();
        });
        $('#select-sat1-preparation-months').change (function(){
            calc_sat1_preparation_price();
        });
        $('#writing-subscrible-modal').on('show.bs.modal',function (){
            calc_writing_price();
        });
        $('#select-sat2-preparation-months').change (function(){
            calc_sat2_preparation_price();
        });
        $('#sat1-subscrible-modal').on('show.bs.modal',function (){
            calc_sat1_price();
            var sat1_class = $('#select-sat1-class').val();
            $('#sat1-class').val(sat1_class);
        });
        $('#sat2-preparation-subscrible-modal').on('show.bs.modal',function (){
            calc_sat2_preparation_price();
        });
        function calc_sat2_price() {
            var months = parseInt($("#select-sat2-months").val());
            $("#total-amount-sat2-class").text(parseInt(months * satMIIP));  
        }
        function calc_ikmath_course_price() {
            var months = parseInt($("#sel-month-ikmath-course").val());
            var d = $("#select-class-ikmath-course").val();
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
                default :
                    p = satMIKP12;
                    break;
            }
            $("#total-ikmath-course").text(months * p);
        }
        $('#select-class-ikmath-course').change (function (){
            var class_ik = $('#select-class-ikmath-course').val();
            $('#ikmath-course-sat-class').val(class_ik);
        });
        $('#sat2-subscrible-modal').on('show.bs.modal',function (){
            calc_sat2_price();
            var sat2_class = $('#select-sat2-class').val();
            $('#sat1-class').val(sat2_class);
        });
        $('#select-sat2-months').change(function (){
            calc_sat2_price();
        });
        $('#select-sat2-class').change(function (){
            var sat2_class = $('#select-sat2-class').val();
            $('#sat2-class').val(sat2_class);
            calc_sat2_price();
        });
        $("#btn-login").click(function(){
            $("#modal-show-login").modal("hide");
            jQuery('#show_login').click();
        });
    });
})(jQuery);