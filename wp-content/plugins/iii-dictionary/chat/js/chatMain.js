var socket = io.connect('http://107.180.78.211:8000');
var message = {
    _content: '',
    _time: '',
    _from: '',
    _to: ''
};
var current_connect = '';
var last_connect = '';
var queu_connect = []; //open muilti connect at this time, so just accept one connect at this time.
var accept_connect = {'_from': '', '_to': ''};
var open_notepad = 0;
//const 
var HAVE_CONNECT_CLASS = 'have-connect';
var money = 0;
(function ($) {
    $(document).ready(function () {
        socket.emit('set_config', {'id': __US, 'name': __NAME});
    });

    $('#open-notepad-btn').click(function () {
        open_notepad = (open_notepad) ? 0 : 1;
    });
    jQuery("body").on("keyup", "#txt-chat", function (event) {
        if (event.which == 13 || event.keyCode == 13) {
            $('#btn-send').click();
        }
    });

    jQuery("body").on("click", "#wp-live-chat-close", function () {
        if ($('#wp-live-chat-2').css('display') == 'block')
        {
            $('#display-money').text('the amount you paid for the lesson is $'+money);
                $('#modal-notice-close').slideDown('slow');
        } else {
            $('#modal-notice-close').hide();
        }
    });
    $(document).on('click', '.btn-close-md', function () {
        $('#modal-notice-close').slideUp('slow');
    });
    $(document).on('click', '.close', function () {
        $('#modal-notice').hide();
    });
    $(document).on('click', '.btn-quit-site', function () {
        location.reload();
    });
    $(document).on('click', '#open-chat-btn,#btn-chat-quit,#btnClose', function () {
        if (jQuery('#sl-reciplent').attr('data-email') == "") {
            $('#block-chat').slideToggle('slow').toggleClass('opened');
            $('#modal-notice-close').hide();
            document.getElementById('wp-live-chat-2').style.display = "none";
            var isVisible = $('#block-chat').is('.opened');
            if (!isVisible) {
                socket.emit('remove_user', {'id': __US});
            } else {
                if (!isNaN(parseInt(__US))) {
                    socket.emit('push_user', {
                        'id': __US,
                        'name': __NAME,
                        'email': __EMAIL,
                        'is': __IS,
                        'can': __IS
                    });
                }
            }
        } else {
            $.ajax('http://math.ikstudy.com/?r=ajax/chat/notice', {
                method: 'post',
                data: {id: 8},
            }).done(function (data) {
                $('.notice-content').html('').append(data);
                $('#modal-notice-close').slideDown('slow');
            });

        }
    });
    $(document).on('click', '.btn-quit', function () {

        if (typeof idroom === "undefined") {
            window.location.href = "http://math.ikstudy.com/wp-content/plugins/iii-dictionary/chat/pad/Draw.html?client=&sid=&__HA=&__US=&__URL=&__IS=&__NAME=&__EMAIL=&__PRICE=&CURRENTNAME=&IDSTUDENT=&IDROOM=&EMAILTEACHER=";
        } else {
            if (jQuery('#level-name').val() == "") {
                alert('please fill in session name')
            } else if (jQuery('#input-level-grade').val() == "") {
                alert('please chose Grade your tutor')
            } else {
                if (idroom != null && idroom != '') {
                    if (jQuery('#wp-live-chat-2:visible').length == 1) {
                                $('#modal-notice-close').hide();
                                 clearInterval(set_time);
                        $.ajax('http://math.ikstudy.com/?r=ajax/chat/notice', {
                            method: 'post',
                            data: {id: 7, _id: idroom}
                        }).done(function (data) {
                            // update status quit tutor of student or teacher
                            $.ajax('http://math.ikstudy.com/?r=ajax/chat/update_quit_status', {
                                method: 'post',
                                data: {id: 1, _id: idroom}
                            }).done(function (data) {
                            });
                            // update price for lesson tutor
                            $.ajax('http://math.ikstudy.com/?r=ajax/chat/update_name_grade_chat', {
                                method: 'post',
                                data: {idroom: idroom, price: money, level_name: $('#level-name').val(), level_grade: $('#input-level-grade').val()},
                            }).done(function (data) {
                            });
                            socket.emit('quit_message', {'idroomquit': idroom, 'idstudent': __US, 'idteacher': idteacher, 'email': EMAILTEACHER, 'idstop': 0});
                            document.getElementById('wp-live-chat-2').style.display = "none";
                            $('#parent').css('margin-left', '');
                        });

                    }
                }
            }
        }
    });

    socket.on('update_quit_message', function (data) {
        if (data.idteacher == idteacher && data.idroomquit == idroom && data.idstudent == __US && data.idstop == 1) {
            var html1 = '<em>' + EMAILTEACHER + ' has closed and ended the chat</em><div class="wplc-clear-float-message"></div>';
            jQuery("#wplc_chatbox").prepend(html1);
            var html2 = '<em>[System Message} Your tutoring connection is closed</em><div class="wplc-clear-float-message"></div>';
            jQuery("#wplc_chatbox").prepend(html2);
            alert('Tutoring stopped by the Tutor.');
            clearInterval(set_time);
        }
    });

    socket.on('reload_tab_update', function (data) {
        if (data.uid == __US && data.idroom == idroom && data.idteacher == idteacher) {
            alert('You has closed and ended the chat');
            $.ajax('http://math.ikstudy.com/?r=ajax/chat/update_quit_status', {
                method: 'post',
                data: {id: 1, _id: idroom}
            }).done(function (data) {
            });
            // update price for lesson tutor
            $.ajax('http://math.ikstudy.com/?r=ajax/chat/update_price_chat', {
                method: 'post',
                data: {idroom: idroom, price: data.money},
            }).done(function (data) {
            });
            socket.emit('quit_message', {'idroomquit': data.idroom, 'idstudent': __US, 'idteacher': data.idteacher, 'email': data.email, 'idstop': 0});
        }
    });

    // quit tutoring
    $(document).on('click', '.btn-cancel-session', function () {
        $.ajax('http://math.ikstudy.com/?r=ajax/chat/cancel_session', {
            method: 'post',
            data: {'sid': __SID, 'uid': __US}
        }).done(function () {
            $('.block-popup').slideToggle('slow');
        });
    });

    $('body').on('click', '.btn-close-bp', function () {
        $('.block-popup').slideToggle('slow');
    });
    $('#btn-chat-accept').click(function () {
        $.ajax('http://math.ikstudy.com/?r=ajax/chat/request', {
            method: 'post',
            data: {'id': __US, 'return': __URL, 'sid': __SID, 'check': false},
            beforeSend: function () {
                $('#btn-chat-accept').button('loading')
            }
        }).done(function (data) {
//            var datareturn = jQuery.parseJSON(data, true);
            $('#btn-chat-accept').button('reset');
//            $('.block-popup-content').html('').append(datareturn['html']);
            $('.block-popup-content').html('').append(data);
            $('.block-popup').slideDown('slow');
        });
    });
    // listen accept requets from teacher
    socket.on('get_accept', function (data) {
        if (data.__US == __US && data.__SID == __SID) {
            idroom = data.__IDROOM;
            $('#sl-reciplent').attr('IDROOM', data.__IDROOM);
            $('#sl-reciplent').attr('data-log', data.name);
            $('#sl-reciplent').attr('data-email', data.email);
            $('#sl-reciplent').attr('class', data.class);
            $('#sl-reciplent').attr('id-teacher', data.idteacher);
            idteacher = data.idteacher;
            $.ajax('http://math.ikstudy.com/?r=ajax/chat/request', {
                method: 'post',
                data: {'id': __US, 'return': __URL, 'sid': __SID, 'check': false},
                beforeSend: function () {
                    $('#btn-chat-accept').button('loading')
                }
            }).done(function (data) {
                $('#btn-chat-accept').button('reset')
                $('.block-popup-content').html('').append(data);
            });

        }
    });

//    $('#slitter').css({height: '260px', position: 'relative'}).split({orientation: 'horizontal', limit: 20});

    // click start lesson
    $(document).on('click', '#start-session', function () {
        connect = $(this).attr('data-teacher');
        $.ajax('http://math.ikstudy.com/?r=ajax/chat/start_session', {
            method: 'post',
            data: {'sid': __SID, 'uid': __US},
            beforeSend: function () {
                $('#start-session').button('loading')
            }
        }).done(function (data) {
            if (data > 0) {
                var $set_session = $('#sl-reciplent').attr('data-email');
                $('#start-session').button('reset');
                $('.section-register').hide();
                $('#block-chat').hide();
                if ($set_session != "") {
                    $('.block-apend-message').html('');
                    connect_change();
                }
                if (!open_notepad) {
                    $('#open-notepad-btn').attr('data-idteacher', $('#sl-reciplent').val());
                    $('#open-notepad-btn').click();
                }
                socket.emit('start_time', {'std': __US, 'email': __EMAIL, 'name': __NAME, 'tch': connect, 'idteacher': idteacher, 'idroom': idroom, 'btn_first': true});
                socket.emit('teacher_room', {'idroom': idroom, 'email': $('#sl-reciplent').attr('data-email')});

            }
        });
    });
    $(document).on('click', '#auto-open-chat', function () {
        jQuery("#modal-notice").hide();
    });

    socket.on('update_list_rmv', function (data) {
        $('#sl-reciplent').val('');

        $.ajax('http://math.ikstudy.com/?r=ajax/chat/clear_session', {
            method: 'post',
            data: {'uid': data.id, 'sid': __SID}
        });
    });

    function connect_change() {
        current_connect = jQuery('#sl-reciplent').val();
        switch (current_connect) {
            case 'new-friend' :
                var $tthis = $('#block-new-friend');
                $tthis.fadeIn('slow');
                $('#nf-close').click(function () {
                    $tthis.fadeOut('slow');
                });
                break;
            case '0':
                var selector = $('.block-apend-message');
                var html = '<div class="receive-message col-md-12">';
                html += '<div class="col-md-8 cm-text">';
                html += 'Hello <b class="b-name">' + __NAME + '</b>';
                html += '</div>';
                html += '</div>';
                selector.html('');
                append_notice(selector, $("#block-cc-chat"), html);
                break;
            default :
                socket.emit('make_room', {uid: __US, cid: current_connect, sid: __SID, is: __IS, from: __US});
                var selector = $('.block-apend-message');
                selector.html('');
                $.ajax('http://math.ikstudy.com/?r=ajax/chat/get_history', {
                    'method': 'post',
                    'data': {'room': room($('#sl-reciplent').val(), __US), 'id': __US, 'idteacher': $('#sl-reciplent').val(), 'idstudent': ''},
                    beforeSend: function () {
                        $(this).prop('readonly', true);
                        $('#btn-send').button('loading');
                    }
                }).done(function (data) {
                    $(this).prop('readonly', false);
                    $('#btn-send').button('reset');
                    if (data.length > 0) {
                        jQuery("#wplc_chatbox").append(data);

                        jQuery('#wplc_chatbox').scrollTop(0);
                    }
                });
                break;
        }
    }

    socket.on('send_connect', function (data) {
        if (data.id == __US) {
            $('#txt-chat').prop({'readonly': true}).val(data.who_name + ' is requesting to connect');
            $('#btn-send').addClass(HAVE_CONNECT_CLASS).text('Accept ?');
            $("#btn-send").focus();
            accept_connect._from = data.who;
            accept_connect._to = data.id;
            setTimeout(function () {
                $("#btn-send").removeClass(HAVE_CONNECT_CLASS).text('Send');
                $("#txt-chat").prop('readonly', false).text('');
            }, 1000 * 60 * 10);
        }
    });
    
    //handle event button send message
    $(document).on('click', '#btn-send', function () {
        if ($(this).hasClass(HAVE_CONNECT_CLASS)) {
            //accept_connect;
            $("#btn-send").removeClass(HAVE_CONNECT_CLASS).text('Send');
            $("#txt-chat").prop('readonly', false).val('');
            socket.emit('accept_connect', accept_connect);
        } else {
            var message = {
                '_from': __US,
                '_to': idteacher,
                '_content': $('#txt-chat').val(),
                '_id_db': '',
                'name': CURRENTNAME
            }
            $('#txt-chat').val('');
            var date = new Date();
            var html = '<span class="wplc-user-message ">Student : ' + message._content + '</span><div class="wplc-clear-float-message"></div>';

            // insert message to DB then show it in dialog chat
            $.ajax('http://math.ikstudy.com/?r=ajax/chat/insert_history', {
                'method': 'post',
                'data': {'from_id': message._from,
                    'to_id': message._to,
                    'from_time': date.toLocaleTimeString(),
                    'content': message._content,
                    'room': room(message._from, message._to),
                    'name': CURRENTNAME,
                    'idroom': idroom
                },
                beforeSend: function () {
                    $('#btn-send').button('loading')
                }
            }).done(function (data) {
                if (data != '1') {
                    $('#btn-send').button('reset');
                    append_notice($('.block-apend-message'), $("#block-cc-chat"), html);
                    jQuery("#wplc_chatbox").prepend(html);
                    jQuery('#wplc_chatbox').scrollTop(0);
                    message._id_db = data;
                    socket.emit('send_message', message);
                    if ($('#sl-reciplent').val() == '') {
                        socket.emit('send_message_html', message);
                    } else {
                        socket.emit('send_message_php', message);
                    }
                } else {
                    alert('you can not send a message when the conversation has ended');

                }
            });
        }
    });
    
    //receive message
    socket.on('receive_message', function (data) {
        if (data._to == __US) {
            var date = new Date();
            $.ajax('http://math.ikstudy.com/?r=ajax/chat/update_history', {
                'method': 'post',
                'data': {'to_time': date.toLocaleTimeString(),
                    'id': data._id_db
                }
            }).done(function () {
//                var str = window.location.href;
//                var n = str.indexOf("Draw.html");
//                if (n != -1) {
//                    new Audio('http://ikstudy.com/wp-content/plugins/wp-live-chat-support/ding.mp3').play()
//                }
                var html = '<span class="wplc-admin-message"><strong></strong>Tutor : ' + data._content + '</span><br /><div class="wplc-clear-float-message"></div>';
                jQuery("#wplc_chatbox").prepend(html);
                jQuery('#wplc_chatbox').scrollTop(0);
            });
        }
    });
    socket.on('update_message_html', function (data) {
        if (data._from == __US) {
            if ($('#sl-reciplent').val() != '') {
                var html = '<span class="wplc-user-message">Tutor : ' + data._content + '</span><div class="wplc-clear-float-message"></div>';
                jQuery("#wplc_chatbox").prepend(html);
                jQuery('#wplc_chatbox').scrollTop(0);

            }
        }
    });
    socket.on('user_not_online', function (data) {
        if (data.id == __US) {
            var html = '<div class="receive-message col-md-12">';
            html += '<div class="col-md-8 cm-text">';
            html += '<p class="system-notice">Missing user : ' + data.find + '</p>';
            html += '</div>';
            html += '</div>';
            append_notice($('.block-apend-message'), $("#block-cc-chat"), html);
        }

    });

    $(document).on('click', '.not_enough', function () {
        $.ajax('http://math.ikstudy.com/?r=ajax/chat/notice', {
            method: 'post',
            data: {id: 2, return: __URL}
        }).done(function (data) {
            $('.notice-content').html('').append(data);
            $('#modal-notice-close').slideDown('slow');
        });
    });
})(jQuery);

function in_array(value, array) {
    for (var i = 0; i < array.length; i++) {
        if (value == array[i]) {
            return true;
        }
    }
    return false;
}
function append_notice(selector, scrollbar, html) {
    selector.append(html);
    scrollbar.mCustomScrollbar("scrollTo", "bottom");
}
function room(_from, _to) {
    if (_from > _to) {
        return _to + '.' + _from;
    }
    return _from + '.' + _to;
}

