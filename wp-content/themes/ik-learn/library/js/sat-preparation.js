(function ($) {
    $(function () {
        $(".class-detail-btn").click(function (e) {
            e.preventDefault();
            var modal = $("#class-detail-modal");
            modal.find(".modal-body").html($(this).next().html());
            modal.modal();
        });
    });
})(jQuery);