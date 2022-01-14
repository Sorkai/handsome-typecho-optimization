$.fn.extend({
    insertAtCursor: function(iValue) {
        var $t = $(this)[0];
        if (document.selection) {
            this.focus();
            sel = document.selection.createRange();
            sel.text = iValue;
            this.focus();
        } else if ($t.selectionStart || $t.selectionStart == '0') {
            var startPos = $t.selectionStart;
            var endPos = $t.selectionEnd;
            var scrollTop = $t.scrollTop;
            $t.value = $t.value.substring(0, startPos) + iValue + $t.value.substring(endPos, $t.value.length);
            this.focus();
            $t.selectionStart = startPos + iValue.length;
            $t.selectionEnd = startPos + iValue.length;
            $t.scrollTop = scrollTop;
        } else {
            this.value += iValue;
            this.focus();
        }
    },
    selection: function() {
        var s, e, range, stored_range;
        if (this[0].selectionStart == undefined) {
            var selection = document.selection;
            if (this[0].tagName.toLowerCase() != "textarea") {
                var val = this.val();
                range = selection.createRange().duplicate();
                range.moveEnd("character", val.length);
                s = (range.text == "" ? val.length : val.lastIndexOf(range.text));
                range = selection.createRange().duplicate();
                range.moveStart("character", -val.length);
                e = range.text.length;
            } else {
                range = selection.createRange(),
                    stored_range = range.duplicate();
                stored_range.moveToElementText(this[0]);
                stored_range.setEndPoint('EndToEnd', range);
                s = stored_range.text.length - range.text.length;
                e = s + range.text.length;
            }
        } else {
            s = this[0].selectionStart,
                e = this[0].selectionEnd;
        }
        var te = this[0].value.substring(s, e);
        return { start: s, end: e, text: te };
    }
});

function getEditor() {
    if (typeof postEditormd != "undefined") {
        return postEditormd;
    } else if (typeof accEditor != "undefined") {
        return accEditor;
    }
};

accEditor = {
    getSelection: function() {
        return $('#text').selection().text;
    },
    replaceSelection: function(text) {
        editor = getEditor();
        sel = editor.getSelection();
        $('#text').insertAtCursor(text);
    },
};

function insertTextAtCursor(insertValue) {
    if (typeof postEditormd != "undefined") {
        // 兼容 EditorMD 编辑器
        postEditormd.insertValue(insertValue);
        return (false);
    }
    insertField = $('#text')[0]; // Typecho 原版编辑器 Textarea
    //IE 浏览器
    if (document.selection) {
        insertField.focus();
        sel = document.selection.createRange();
        sel.text = insertValue;
        sel.select();
    }
    //FireFox、Chrome等
    else if (insertField.selectionStart || insertField.selectionStart == '0') {
        var startPos = insertField.selectionStart;
        var endPos = insertField.selectionEnd;
        // 保存滚动条
        var restoreTop = insertField.scrollTop;
        insertField.value = insertField.value.substring(0, startPos) + insertValue + insertField.value.substring(endPos, insertField.value.length);
        if (restoreTop > 0) {
            insertField.scrollTop = restoreTop;
        }
        insertField.selectionStart = startPos + insertValue.length;
        insertField.selectionEnd = startPos + insertValue.length;
        insertField.focus();
    } else {
        insertField.value += insertValue;
        insertField.focus();
    }
}
/** 消息提醒 来自 Typecho common-js.php */
function notice(noticeText, noticeType, noticeClass = '.typecho-head-nav') {
    var head = $(noticeClass),
        p = $('<div class="message popup ' + noticeType + '">' +
            '<ul><li>' + noticeText + '</li></ul></div>'),
        offset = 0;

    if (head.length > 0) {
        p.insertAfter(head);
        offset = head.outerHeight();
    } else {
        p.prependTo(document.body);
    }

    function checkScroll() {
        if ($(window).scrollTop() >= offset) {
            p.css({
                'position': 'fixed',
                'top': 0
            });
        } else {
            p.css({
                'position': 'absolute',
                'top': offset
            });
        }
    }

    $(window).scroll(function() {
        checkScroll();
    });

    checkScroll();

    p.slideDown(function() {
        var t = $(this),
            color = '#C6D880';

        if (t.hasClass('error')) {
            color = '#FBC2C4';
        } else if (t.hasClass('notice')) {
            color = '#FFD324';
        }

        t.effect('highlight', {
                color: color
            })
            .delay(5000).fadeOut(function() {
                $(this).remove();
            });
    });
}
/** 更新附件计数 */
function updateAttacmentNumber() {
    var btn = $('#tab-files-btn'),
        balloon = $('.balloon', btn),
        count = $('#file-list li .insert').length;

    if (count > 0) {
        if (!balloon.length) {
            btn.html($.trim(btn.html()) + ' ');
            balloon = $('<span class="balloon"></span>').appendTo(btn);
        }

        balloon.html(count);
    } else if (0 == count && balloon.length > 0) {
        balloon.remove();
    }
}
/** 自定义删除 */
function attachCustomDeleteEvent(el) {
    var file = $('a.insert', el).text();
    console.log(file);
    $('.delete', el).click(function() {
        if (confirm('确认要删除文件 %s 吗 ?'.replace('%s', file))) {
            var cid = $(this).parents('li').data('cid');
            console.log(cid);
            $.ajax({
                url: window.del_attach_action,
                type: "POST",
                data: { 'cid': cid },
                dataType: "json",
                success: function(result) {
                    $(el).fadeOut(function() {
                        $(this).remove();
                        updateAttacmentNumber();
                    });
                    notice(result.msg, result.notice);
                },
            });
        }
        return false;
    });
}

function attachDialogCommonEvents() {
    $('.accessories-dialog-content').bind('clickoutside', function() {
        $('.accessories-dialog').fadeOut().remove();
    });
    $('#accessories-cancel').on('click', function() {
        notice('啥也没干', 'success');
        $('.accessories-dialog').fadeOut().remove();
        return false;
    });
}

function accessories_edit_payment(el) {
    var t = $(el),
        pp = t.parents('li'),
        a = $('.insert', pp);
    $.post(window.get_payment_action, {
        'cid': pp.data('cid')
    }, function(result) {
        if (result.post_id != undefined) {
            accessories_payment_dialog(result);
        } else {
            notice(result.msg, result.notice);
        }
    }, "json");
}

function accessories_payment_dialog(config) {
    $('body').append($('<div class="accessories-dialog">' +
        '<div class="accessories-dialog-background">' +
        '<div class="accessories-dialog-content">' +
        '<p><b>' + '付款设置: %s'.replace('%s', config.post_title) + '</b></p>' +
        '<form id="accessories-form-payment" action="' + window.set_payment_action + '" type="post">' +
        '<input type="hidden" name="cid" value="' + config.post_id + '" />' +
        '<ul class="dialog-items">' +
        '<li class="dialog-item">' +
        '<label for="title">付款方式</label>' +
        '<select class="post-see-type" name="post_see_type" class="typecho-post-option"><option value="-1" selected="true">未进行付费设置</option><option value="0">0:限时免费</option><option value="1">1:登录可见</option><option value="2">2:仅VIP会员可见</option><option value="3">3:要单独付费</option></select>' +
        '</li>' +
        '<li class="dialog-item">' +
        '<label for="post_price">正常付费价格（元）</label>' +
        '<input type="text" name="post_price" class="accessories-post-price" autocomplete="off" value="' + config.post_price + '"/>' +
        '</li>' +
        '<li class="dialog-item">' +
        '<label for="post_price_for_vip">VIP会员付费价格（元）</label>' +
        '<input type="text" name="post_price_for_vip" class="accessories-post-price-vip" autocomplete="off" value="' + config.post_price_for_vip + '"/>' +
        '</li>' +
        '<li class="dialog-item">' +
        '<label for="post_price_for_eternal">终身会员付费价格（元）</label>' +
        '<input type="text" name="post_price_for_eternal" class="accessories-password" autocomplete="off" value="' + config.post_price_for_eternal + '" />' +
        '</li>' +
        '<button type="submit" class="btn btn-s primary">确定</button><button type="button" class="btn btn-s" id="accessories-cancel">取消</button>' +
        '</form>' +
        '</div>' +
        '</div>' +
        '</div>'));

    // 添加事件
    attachDialogCommonEvents();

    $('.post-see-type', $('#accessories-form-payment')).val(config.post_see_type);

    $("#accessories-form-payment").submit(function() {
        var form = $(this),
            params = form.serialize();
        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: params,
            dataType: "json",
            beforeSend: function() {
                form.find("button").attr("disabled", "disabled");
            },
            complete: function() {
                form.find("button").removeAttr("disabled");
                $('.accessories-dialog').fadeOut().remove();
            },
            success: function(result) {
                notice(result.msg, result.notice);
            },
        });
        return false;
    });
    // 显示对话框
    $('.accessories-dialog').fadeIn();
}

/** 附件参数对话框 */
function accessories_detail_dialog_normal(config) {
    $('body').append($('<div class="accessories-dialog">' +
        '<div class="accessories-dialog-background">' +
        '<div class="accessories-dialog-content">' +
        '<p><b>普通附件</b></p>' +
        '<form id="accessories-form" action="' + window.set_attach_action + '" type="post">' +
        '<input type="hidden" name="cid" value="' + config.cid + '" />' +
        '<input type="hidden" name="type" value="normal" />' +
        '<ul class="dialog-items">' +
        '<li class="dialog-item">' +
        '<label for="title">名称</label>' +
        '<input type="text" name="title" class="accessories-title" value="' + config.title + '" required />' +
        '</li>' +
        '<li class="dialog-item">' +
        '<label for="slug">短链</label>' +
        '<input type="text" name="slug" class="accessories-slug" value="' + config.slug + '"required />' +
        '</li>' +
        '<li class="dialog-item flex-column">' +
        '<label for="path">链接</label>' +
        '<textarea type="text" name="path" class="accessories-path" required >' + config.path + '</textarea>' +
        '</li>' +
        '<li class="dialog-item">' +
        '<label for="password">密码</label>' +
        '<input type="text" name="password" class="accessories-password" value="' + config.password + '" />' +
        '</li>' +
        '<button type="submit" class="btn btn-s primary" id="accessories-ok">确定</button><button type="button" class="btn btn-s" id="accessories-cancel">取消</button>' +
        '</form>' +
        '</div>' +
        '</div>' +
        '</div>'));

    // 添加事件
    attachDialogCommonEvents();
    $("#accessories-form").submit(function() {
        var form = $(this),
            params = form.serialize();
        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: params,
            dataType: "json",
            beforeSend: function() {
                form.find("button").attr("disabled", "disabled");
            },
            complete: function() {
                form.find("button").removeAttr("disabled");
                $('.accessories-dialog').fadeOut().remove();
            },
            success: function(result) {
                if (result.update == 1) {
                    // 刷新附件信息
                    aItem = $('li[data-cid=' + result.cid + ']', $('#file-list'));
                    aItem.attr('data-url', result.path);
                    aItem.attr('data-image', result.isImage);
                    $('.insert', aItem).html(result.title);
                }
            },
        });
        return false;
    });
    // 显示对话框
    $('.accessories-dialog').fadeIn();
}

function accessories_dialog_thirdpardy(operate) {
    parent = $('input[name="cid"]').val();
    parentInput = (parent == "") ? "" : '<input type="hidden" name="parent" value="' + parent + '" />';
    action = (operate == 'set') ? window.set_attach_action : window.add_attach_action;

    $('body').append($('<div class="accessories-dialog">' +
        '<div class="accessories-dialog-background">' +
        '<div class="accessories-dialog-content">' +
        '<p><b>外链附件</b></p>' +
        '<form id="accessories-form-add" action="' + action + '" type="post">' +
        '<input type="hidden" name="type" value="thirdparty" />' +
        parentInput +
        '<ul class="dialog-items">' +
        '<li class="dialog-item">' +
        '<label for="title">名称</label>' +
        '<input type="text" name="title" class="accessories-title"/>' +
        '</li>' +
        '<li class="dialog-item">' +
        '<label for="slug">短链</label>' +
        '<input type="text" name="slug" class="accessories-slug"/>' +
        '</li>' +
        '<li class="dialog-item flex-column">' +
        '<label for="path">外链</label>' +
        '<textarea type="text" name="path" class="accessories-path" required></textarea>' +
        '</li>' +
        '<li class="dialog-item">' +
        '<label for="password">密码</label>' +
        '<input type="text" name="password" class="accessories-password" />' +
        '</li>' +
        '<button type="submit" class="btn btn-s primary" id="accessories-ok">确定</button><button type="button" class="btn btn-s" id="accessories-cancel">取消</button>' +
        '</form>' +
        '</div>' +
        '</div>' +
        '</div>'));
    // 添加事件
    attachDialogCommonEvents();

    $("#accessories-form-add").submit(function() {
        var form = $(this),
            params = form.serialize();
        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: params,
            dataType: "json",
            beforeSend: function() {
                form.find("button").attr("disabled", "disabled");
            },
            complete: function() {
                form.find("button").removeAttr("disabled");
                $('.accessories-dialog').fadeOut().remove();
            },
            success: function(result) {
                $('<li id="accessories-' + result.cid + '"></li>').appendTo('#file-list');
                var el = $('#accessories-' + result.cid);
                el.attr('data-cid', result.cid)
                    .attr('data-url', result.url)
                    .attr('data-image', result.isImage + 0)
                    .html('<input type="hidden" name="attachment[]" value="' + result.cid + '" />' +
                        '<a class="insert" target="_blank" href="###" title="点击插入文件 ">' + result.title +
                        '</a><div class="info ">' + result.bytes +
                        '<a class="file" target="_blank" href="' + window.adminUrl + 'media.php?cid=' + result.cid + '" title="编辑"><i class="i-edit"></i></a>' +
                        '<a class="delete" href="###" title="删除"><i class="i-delete"></i></a></div>')
                    .effect('highlight', 1000);
                el.attr('id', '');
                $('.insert', el).click(function() {
                    var t = $(this),
                        p = t.parents('li');
                    Typecho.insertFileToEditor(t.text(), p.data('url'), p.data('image'));
                    return false;
                });
                attachCustomDeleteEvent(el);
                Typecho.uploadComplete();
            },
        });
        return false;
    });
    // 显示对话框
    $('.accessories-dialog').fadeIn();
}

function accessories_dialog_netdisk(operate, config) {
    parent = $('input[name="cid"]').val();
    parentInput = (parent == "") ? "" : '<input type="hidden" name="parent" value="' + parent + '" />';
    action = (operate == 'set') ? window.set_attach_action : window.add_attach_action;

    if (config != undefined) {
        title = (config.title == undefined) ? '' : config.title;
        slug = (config.slug == undefined) ? '' : config.slug;
        path = (config.path == undefined) ? '' : config.path;
        password = (config.password == undefined) ? '' : config.password;
    } else {
        title = '';
        slug = '';
        path = '';
        password = '';
    }

    $('body').append($('<div class="accessories-dialog">' +
        '<div class="accessories-dialog-background">' +
        '<div class="accessories-dialog-content">' +
        '<p><b>网盘附件</b></p>' +
        '<form id="accessories-form-add" action="' + action + '" type="post">' +
        '<input type="hidden" name="type" value="netdisk" />' +
        parentInput +
        '<ul class="dialog-items">' +
        '<li class="dialog-item">' +
        '<label for="title">名称</label>' +
        '<input type="text" name="title" class="accessories-title" value="' + title + '" />' +
        '</li>' +
        '<li class="dialog-item">' +
        '<label for="slug">短链</label>' +
        '<input type="text" name="slug" class="accessories-slug" value="' + slug + '" />' +
        '</li>' +
        '<li class="dialog-item flex-column">' +
        '<label for="path">外链(只能一个，格式：链接 或者 链接|提取码)</label>' +
        '<textarea type="text" name="path" class="accessories-path" required >' + path + '</textarea>' +
        '</li>' +
        '<li class="dialog-item">' +
        '<label for="password">密码</label>' +
        '<input type="text" name="password" class="accessories-password" value="' + password + '" />' +
        '</li>' +
        '<button type="submit" class="btn btn-s primary" id="accessories-ok">确定</button><button type="button" class="btn btn-s" id="accessories-cancel">取消</button>' +
        '</form>' +
        '</div>' +
        '</div>' +
        '</div>'));
    if (operate == 'set') {
        // 添加隐藏参数说明修改的是哪个附件
        $('input[name="type"]').before('<input type="hidden" name="cid" value="' + config.cid + '" />');
    }
    // 添加事件
    attachDialogCommonEvents();
    $("#accessories-form-add").submit(function() {
        var form = $(this),
            params = form.serialize();
        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: params,
            dataType: "json",
            beforeSend: function() {
                form.find("button").attr("disabled", "disabled");
            },
            complete: function() {
                form.find("button").removeAttr("disabled");
                $('.accessories-dialog').fadeOut().remove();
            },
            success: function(result) {
                notice(result.msg, result.notice);
                if (operate == 'add') {
                    $('<li id="accessories-' + result.cid + '"></li>').appendTo('#file-list');
                    var el = $('#accessories-' + result.cid);
                    el.attr('data-cid', result.cid)
                        .attr('data-url', result.url)
                        .attr('data-image', result.isImage + 0)
                        .html('<input type="hidden" name="attachment[]" value="' + result.cid + '" />' +
                            '<a class="insert" target="_blank" href="###" title="点击插入文件 ">' + result.title +
                            '</a><div class="info ">' + result.bytes +
                            '<a class="file" target="_blank" href="' + window.adminUrl + 'media.php?cid=' + result.cid + '" title="编辑"><i class="i-edit"></i></a>' +
                            '<a class="delete" href="###" title="删除"><i class="i-delete"></i></a></div>')
                        .effect('highlight', 1000);
                    el.attr('id', '');
                    $('.insert', el).click(function() {
                        var t = $(this),
                            p = t.parents('li');
                        Typecho.insertFileToEditor(t.text(), p.data('url'), p.data('image'));
                        return false;
                    });
                    attachCustomDeleteEvent(el);
                    Typecho.uploadComplete();
                } else if (result.update == 1) {
                    // 刷新附件信息
                    aItem = $('li[data-cid=' + result.cid + ']', $('#file-list'));
                    aItem.attr('data-url', result.path);
                    aItem.attr('data-image', result.isImage);
                    $('.insert', aItem).html(result.title);
                }
            },
        });
        return false;
    });
    $('.accessories-dialog').fadeIn();
}
/** 获取附件参数 */
function accessories_get_detail(cid, callback) {
    $.post(window.get_attach_action, {
        'cid': cid
    }, function(result) {
        if (result.cid != undefined) {
            if (result.password == null)
                result.password = "";
            callback(result);
        } else {
            alert(result.msg);
        }
    }, "json");
}
/** 修改附件参数回调 */
function accessories_edit_detail_callback(config) {
    if (config.type == 'netdisk') {
        accessories_dialog_netdisk('set', config);
    } else {
        accessories_detail_dialog_normal(config);
    }
}
/** 修改附件参数 */
function accessories_edit_detail(el) {
    var t = $(el),
        pp = t.parents('li'),
        a = $('.insert', pp);
    accessories_get_detail(pp.data('cid'), accessories_edit_detail_callback);
    return false;
}
/** 插入附件 */
function accessories_insert(el) {
    var t = $(el),
        pp = t.parents('li'),
        a = $('.insert', pp);
    getEditor().replaceSelection('[attach]' + pp.data('cid') + '[/attach]');
    // if (pp.data('image') == 0) {
    //     getEditor().replaceSelection('[attach]' + pp.data('cid') + '[/attach]');
    // } else {
    //     getEditor().replaceSelection('[image]' + pp.data('cid') + '[/image]');
    // }
}
/** 复制链接回调 */
function copy_permalink_callback(config) {
    $('body').append($('<div class="accessories-dialog">' +
        '<div class="accessories-dialog-background">' +
        '<div class="accessories-dialog-content">' +
        '<p><b>复制链接</b></p>' +
        '<ul class="dialog-items">' +
        '<li class="dialog-item">' +
        '<label>友好链接</label>' +
        '<input id="#accessories-permalink" type="text" value="' + config.permalink + '" />' +
        '<button class="btn accessories-dialog-copy-btn" data-clipboard-target="#accessories-permalink"><span class="accessories-copy">复制</span></button>' +
        '</li>' +
        '<li class="dialog-item">' +
        '<label>实际链接</label>' +
        '<input id="#accessories-realUrl" type="text" value="' + config.realUrl + '" />' +
        '<button class="btn accessories-dialog-copy-btn" data-clipboard-target="#accessories-realUrl"><span class="accessories-copy">复制</span></button>' +
        '</li>' +
        '<li class="dialog-item">' +
        '<label for="path">下载链接</label>' +
        '<input id="#accessories-url" type="text" value="' + config.url + '" />' +
        '<button class="btn accessories-dialog-copy-btn" data-clipboard-target="#accessories-url"><span class="accessories-copy">复制</span></button>' +
        '</li>' +
        '<button type="button" class="btn btn-s" id="accessories-cancel">关闭</button>' +
        '</div>' +
        '</div>' +
        '</div>'));
    // 添加事件
    attachDialogCommonEvents();
    $('.accessories-dialog input').attr("readonly", "readonly")
    $('.accessories-dialog-copy-btn').click(function() {
        var input = $(this).parent().find('input').eq(0);
        input.attr("readonly", false);
        input.select();
        if (document.execCommand('copy')) {
            notice("复制成功", "success", ".accessories-dialog-content");
        } else {
            notice("复制失败，请手动复制", "error", ".accessories-dialog-content");
        }
        input.blur();
        input.attr("readonly", true);
    });
    $('.accessories-dialog').fadeIn();
}
/** 复制链接 */
function accessories_copy_permalink(el) {
    var t = $(el),
        pp = t.parents('li'),
        a = $('.insert', pp);
    accessories_get_detail(pp.data('cid'), copy_permalink_callback);
}
/** 添加附件标志 */
function addAttachmentTypeTitle(result) {
    aItem = $('li[data-cid=' + result.cid + '] .info', $('#file-list'));
    if (result.type == "netdisk") {
        aItem.after('<span class="accessories-type">网盘</span>');
    } else if (result.type == "thirdparty") {
        aItem.after('<span class="accessories-type">外链</span>');
    }
}
/** 添加按钮函数 */
function addInsertLink(el) {
    $('.insert', el).unbind('click').click(function() {
        var t = $(this),
            pp = t.parents('li');
        var html = pp.data('image') ? '![' + t.text() + '](' + pp.data('url') + ')' :
            '[' + t.val() + '](' + pp.data('url') + ')';
        getEditor().replaceSelection(html);
        return false;
    });
    name = $('.insert', el).html();
    cid = $('input[name="attachment[]"]', el).val();

    copy_button = '<a title="复制链接[' + name + ']" class="accessories copy-link" href="javascript:void(0);" onclick="accessories_copy_permalink(this);"></a>';

    if (window.is_tepass_enabled == 1) {
        payment_button = '<a title="设置付费[' + name + ']" class="accessories payment" href="javascript:void(0);" onclick="accessories_edit_payment(this);"></a>';
    } else {
        payment_button = '';
    }
    modify_button = '<a title="修改附件[' + name + ']" class="accessories setting" href="javascript:void(0);" onclick="accessories_edit_detail(this);"></a>';
    image_button = '<a title="插入附件[' + name + ']" class="accessories not-image" href="javascript:void(0);" onclick="accessories_insert(this);"></a>';
    non_image_button = '<a title="插入图片[' + name + ']" class="accessories image" href="javascript:void(0);" onclick="accessories_insert(this);"></a>';
    html = '<div class="accessories-group">' + copy_button + payment_button + modify_button;
    if ($(el).data('image') == 0) {
        html = html + image_button + '</div>';
        if (!($('.accessories', el).length > 0)) {
            $(el).append(html);
        }
    } else {
        html = html + non_image_button + '</div>';
        if (!($('.accessories', el).length > 0)) {
            $(el).append(html);
        }
    }
    // 添加附件标志
    accessories_get_detail(cid, addAttachmentTypeTitle);
}
$(document).ready(function() {
    // 遍历添加按钮
    $('#file-list li').each(function() {
        addInsertLink(this);
    });
    // 增加外链附件
    $('.upload-file').after('<br> 或者 <a id="accessories-add-thirdparty" href="#" >添加外链附件</a><br> 或者 <a id="accessories-add-netdisk" href="#">添加网盘附件</a>');
    $('#accessories-add-thirdparty').click(function() {
        accessories_dialog_thirdpardy('add');
        return false;
    });
    $('#accessories-add-netdisk').click(function() {
        accessories_dialog_netdisk('add');
        return false;
    });

    // 单个附件插入优化
    Typecho.uploadComplete = function(file) {
        $('#file-list li').each(function() {
            addInsertLink(this);
        });
    };

    // 附件一次性插入
    $('#upload-panel').append('<span id="ph-insert-images" class="ph-btn">插入所有图片附件</span>');
    $('#ph-insert-images').on('click', function() {
        var editor = getEditor();
        var fileList = $('li', $('#file-list'));
        var text = "";
        for (times = 0; times < fileList.length; times++) {
            var link = fileList.eq(times).data('url');
            var isImage = fileList.eq(times).data('image');
            var cid = fileList.eq(times).data('cid');
            var name = $('a', fileList.eq(times)).eq(0).text();
            if (isImage == 1)
                text = text + '\n' + '![' + name + '](' + link + ')';
        }
        editor.replaceSelection(text);
    });
    $('#upload-panel').append('<span id="ph-insert-non-images" class="ph-btn">插入所有非图片附件</span>');
    $('#ph-insert-non-images').on('click', function() {
        var editor = getEditor();
        var fileList = $('li', $('#file-list'));
        var text = "";
        for (times = 0; times < fileList.length; times++) {
            var link = fileList.eq(times).data('url');
            var isImage = fileList.eq(times).data('image');
            var cid = fileList.eq(times).data('cid');
            var name = $('a', fileList.eq(times)).eq(0).text();
            if (isImage == 0)
                text = text + '\n' + '[attach]' + cid + '[/attach]';
        }
        editor.replaceSelection(text);
    });
});