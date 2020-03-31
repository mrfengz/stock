$(function(){
    /**
     * ajax提交表单
     */
    $('.submit-form').on('click', function(e){
       // e.preventDefault();
       submitForm($(this).parents('form'));
    });

    /**
     * reset表单
     */
    $("#reset-form").on('click', function(e){
        $(this).parents('form')[0].reset()
    });

    /**
     * 有.date的元素绑定laydate日期插件
     */
    $('.date').each(function(){
        laydate.render({
            elem: '#'+$(this).prop('id'),
            trigger: 'click',
            eventElem: '#'+$(this).prop('id')+'-addon'
        });
    });

    /**
     * 有.datetime的元素绑定laydate时间插件
     */
    $('.datetime').each(function(){
        laydate.render({
            elem: '#'+$(this).prop('id'),
            type: 'datetime',
            trigger: 'click',
            eventElem:  '#'+$(this).prop('id')+'-addon'
        });
    });

    /**
     * 删除列表记录
     */
    $('.del-btn').on('click', function(){
        var that = $(this);
        layer.confirm('确定要删除吗？' , function(){
            var index = loading();
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: that.parents('table').data('del-url'),
                data: {'id': that.data('id')},
                success: function(res){
                    if(res.code == 0) {
                        successMsg(res.msg);
                        setTimeout(function(){
                            location.reload();
                        }, 2000);
                    } else {
                        errorAlert(res.message);
                    }
                },
                error: function(obj, msg){
                    errorMsg('错误[' + msg + ']' + obj.statusText);
                },
                complete: function() {
                    layer.close(index);
                }
            });
        })
    });

    /**
     * 初始化wangEditor编辑器。编辑器对应的必须有.editor类，如果想定制，可去掉.editor属性，然后再定义
     */
    var E = window.wangEditor;
    var inputElemId = $('.editor').eq(0).data('id');
    var editorId = '#' + inputElemId+'-editor';
    var editor = new E(editorId);  // 或者 var editor = new E( document.getElementById('editor') )
    editor.customConfig = {
        // 下面两个配置，使用其中一个即可显示“上传图片”的tab。但是两者不要同时使用！！！
        //uploadImgShowBase64: true,  //使用base64保存图片
        //uploadImgServer: '/upload',  // 上传图片到服务器，应该填写url
        uploadImgServer: $(editorId).data('upload-url'),
        showLinkImg: false,             // 隐藏“网络图片”tab
        uploadImgMaxSize: 2 * 1024 * 1024,  //大小限制3M
        uploadImgMaxLength: 5, // 限制一次最多上传 5 张图片
        //其它参数
        // 如果版本 <=v3.1.0 ，属性值会自动进行 encode ，此处无需 encode
        // 如果版本 >=v3.1.1 ，属性值不会自动 encode ，如有需要自己手动 encode
        uploadImgParams: {
            token: 'abcdef12345'
        },
        onchange: function(html) {
            $('#'+inputElemId).val(html);
        },
        //自定义header
        uploadImgHeaders: {
            'Accept': 'text/x-json'
        },
        uploadImgParamsWithUrl: true,  //如果还需要将参数拼接到 url 中
        uploadFileName: 'image[]',    //自定义 fileName
        withCredentials: true,     //跨域
        uploadImgTimeout: 3000,    // 将 timeout 时间改为 3s
        //自定义错误提示，info 是需要提示的内容
        customAlert: function (info) {
            alert('自定义提示：' + info)
        }
    };
    editor.create();
    editor.txt.html($('#'+inputElemId).val());


});