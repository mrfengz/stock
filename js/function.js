/**
 * 封装ajax提交表单。 post提交，serialize序列化表单数据提交
 * @param $form
 */
function submitForm($form){
    var index = loading();
    console.log($form.serialize());
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: $form.prop('action'),
        data: $form.serialize(),
        success: function(res){
            if(res.code == 0) {
                successMsg(res.msg);
                setTimeout(function(){
                    location.href = $form.data('jump-to');
                }, 2000);
            } else {
                errorMsg(res.msg);
            }
        },
        error: function(obj, msg){
            errorMsg('错误[' + msg + ']' + obj.statusText);
        },
        complete: function() {
            layer.close(index);
        }
    })
}

/**
 * 加载并上传图片
 * @param dom object self
 */
function loadAndUploadFile(self) {
    uploadImg(self, $(self).data('upload-url'));
    $("#" + $(self).data('id') + 'name').html(self.files[0].name);
}

/**
 * 使用FormData属性上传图片
 * @param elem 上传按钮元素
 * @param uploadUrl 提交的url
 */
function uploadImg(elem, uploadUrl)
{
    var formData = new FormData();
    file = $(elem)[0].files[0];
    // formData.append('image', $(elem).data('id'));
    formData.append('image', file, file.name);
    var index = loading();
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: uploadUrl,
        data: formData,
        contentType: false, // 注意这里应设为false
        processData: false,
        success: function(res){
            if(res.code == 0) {
                var previewElemId =$(elem).data('id') + '-preview';
                console.log(previewElemId, res);
                if(!$( '#' + previewElemId).length) {
                    $('#'+$(elem).data('id')).val(res.data.url);
                    $('#'+$(elem).data('id')).after("<img src='" +res.data.url+ "' class='img-preview' id='"+previewElemId+"'/>")
                } else {
                    $( '#' + previewElemId).attr('src',res.data.url);
                }
            } else {
                errorMsg(res.msg);
            }
        },
        error: function(obj, msg){
            errorMsg('错误[' + msg + ']' + obj.statusText);
        },
        complete: function() {
            layer.close(index);
        }
    })
}

/**
 * 合并配置对象
 *  如果传入config未设置，则按照defaultConfig中的值进行设置
 * @param object    config        传入的config
 * @param object    defaultConfig 默认的config
 * @returns object 合并后的配置
 */
function mergeConfig(config, defaultConfig) {
    if (config != undefined && typeof config == 'object') {
        for(var key in config) {
            if (config.hasOwnProperty(key)) {
                if (config[key] !== undefined) {
                    defaultConfig[key] = config[key];
                }
            }
        }
    }
    return defaultConfig;
}


// ========== 基于layer插件的二次封装 ===========
/**
 * 警告弹框 !消息提示
 * @param  string msg       提示的消息
 * @param  Object paramsObj 成员对象包括 {title:'',bnt:[],success:function(){},error:function(){},cancel:function(){}}
 */
function warnAlert(msg, paramsObj) {
    _re_alert(msg, 0, paramsObj);
}
/**
 * 成功提示框 对号+提示消息
 */
function successAlert(msg, paramsObj) {
    _re_alert(msg, 1, paramsObj);
}

/**
 * 错误提示框 叉号+提示消息
 */
function errorAlert(msg, paramsObj) {
    _re_alert(msg, 2, paramsObj);
}
/**
 * 错误提示基础方法
 * @param  string msg       提示的消息
 * @param  Object paramsObj 成员对象包括 {title:'',bnt:[],success:function(){},error:function(){},cancel:function(){}}
 * @param  number|stringNumber type   0：警告； 1：成功；2：错误
 */
function _re_alert(msg,type,paramsObj) {
    var defaultConfig = {
        title: '提示',
        btn: ['确定'],
        error: function(){},    //点击确定按钮回调函数
        success: function(){},  //点击取消按钮回调函数
        cancel: function(){}    //点击右上角关闭按钮回调函数
    };
    defaultConfig = mergeConfig(paramsObj, defaultConfig);
    var _layer = layer.open({
        title: defaultConfig.title,
        icon: type,
        content: msg,
        btn: defaultConfig.btn,
        yes: function(){
            if(typeof defaultConfig.success === 'function'){
                defaultConfig.success();
                layer.close(_layer);
            }
        },
        btn2: function(){
            if(typeof defaultConfig.error === 'function')
                defaultConfig.error();
        },
        cancel: function(){
            if(typeof defaultConfig.cancel === 'function')
                defaultConfig.cancel();
        }
    });
}

// -------- msg提示 不带按钮 ---------
/*
 * 默认提示，不带按钮。灰色背景
 */
function defaultMsg(msg,config){
    _re_msg(msg, -1, config);
}
/*
 * 警告提示，不带按钮 ！+ 消息
 */
function warnMsg(msg,config){
    _re_msg(msg, 0, config);
}
/*
 * 错误提示，不带按钮 怒脸+消息
 */
function errorMsg(msg,config){
    _re_msg(msg, 5, config);
}
/*
 * 成功提示，不带按钮 笑脸+消息
 */
function successMsg(msg,config){
    _re_msg(msg, 6, config);
}


// 公共msg方法
function _re_msg(msg, icon, config){
    var defaultConfig = {
        time: 2000,
        callFunc: function(){}
    };
    var defaultConfig = mergeConfig(config, defaultConfig);

    layer.msg(msg, {
        icon: icon,
        time: defaultConfig.time //2秒关闭（如果不配置，默认是3秒）
    }, defaultConfig.callFunc); //defaultConfig.callFunc 关闭时的回调
}

// ------ loading层 -----------
/**
 * 加载层，返回层的索引，可以用于关闭
 * @return {[type]} [description]
 */
function loading() {
    return layer.load(1, {
        shade: [0.1,'#fff'] //0.1透明度的白色背景
    });
}

// ------ iframe ---------
function load_iframe(url,area,title) {
    title = title || '页面';
    area = area || ['800px', '600px'];
    return layer.open({
        type: 2,
        title: title,
        shadeClose: true,
        offset: '180px',
        // shade: true,
        maxmin: true, //开启最大化最小化按钮
        area: area,
        content: url,
    });
}

/**
 * 显示一个tips
 * @param content string 显示的内容
 * @param type 吸附在元素的哪边 1:上 2:右 3:下 4:左
 * @param ele class/id selector  example: .product|#desc
 */
function showTips(content, ele, type) {
    type = undefined || 4;
    layer.tips(content, $(ele), {
        tips: [type, '#666'],
        time: 6000,
        shade: '#fff',
        shadeClose: true,
        area: ['auto', 'auto'],
    });
}

// ========== 基于layer插件的二次封装部分结束 ===========
/**
 * 获取数组的最大值
 * @param  Array arr 数组
 * @return 最大值
 */
function max(arr) {
    return Math.max.apply(Math, arr);
}

/**
 * 获取数组的最小值
 * @param  Array arr 数组
 * @return 最小值
 */
function min(arr) {
    return Math.min.apply(Math, arr);
}

function sum(arr) {
    return arr.reduce(function(p,n){
        return p+n;
    })
}