/**
 * 依赖jquery
 * 依赖app.js
 * 依赖toastr
 * 一旦提交，全部提交
 */
if (typeof App == "undefined") {
    App = {};
}
App.dt = {};
/**
 * 使用toastr做提示
 * @param _msg
 */
App.dt.fail = function (_msg) {
    console.log(_msg)
    toastr.error(_msg);
};

App.dt.succ = function (_msg) {
    console.log(_msg)
    toastr.success(_msg);
};

//加密、解密算法封装：
App.dt.base64 = {
    // private property
    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
    // public method for encoding
    encode: function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;
        input = this._utf8_encode(input);
        while (i < input.length) {
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);
            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;
            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }
            output = output +
                this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
        }
        return output;
    },

    // public method for decoding
    decode: function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
        while (i < input.length) {
            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));
            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;
            output = output + String.fromCharCode(chr1);
            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }
        }
        output = this._utf8_decode(output);
        return output;
    },

    // private method for UTF-8 encoding
    _utf8_encode: function (string) {
        string = string.replace(/\r\n/g, "\n");
        var utftext = "";
        for (var n = 0; n < string.length; n++) {
            var c = string.charCodeAt(n);
            if (c < 128) {
                utftext += String.fromCharCode(c);
            } else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            } else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }
        return utftext;
    },

    // private method for UTF-8 decoding
    _utf8_decode: function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;
        while (i < utftext.length) {
            c = utftext.charCodeAt(i);
            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            } else if ((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            } else {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }
        return string;
    }
};


/**
 * 普通ajax post参数
 * @param _data
 * @param _callback
 */
App.dt.aPost = function (_data, _callback) {
    var self = App.dt;
    var _uuid = App.su.maths.uuid.create();
    $.ajax({
            url: "./tool.php?_r=" + _uuid,
            type: 'POST',
            data: _data,
            dataType: 'json',
            // processData: false,
            // 告诉jQuery不要去处理发送的数据
            // contentType: false,
            // 告诉jQuery不要去设置Content-Type请求头
            success: function (responseStr) {
                console.log(responseStr);
                if (responseStr.code == "ok") {
                    _callback(responseStr.data);
                } else {
                    if (typeof responseStr == "undefined") {
                        self.fail("501");
                    } else if (typeof responseStr.msg == "undefined") {
                        self.fail(responseStr);
                    } else {
                        self.fail(responseStr.msg);
                    }
                }
            },
            error: function (responseStr) {
                console.log(responseStr);
                self.fail("服务器异常，请稍后再试--" + responseStr);
            }
        }
    );
}

/**
 * 渲染全部
 * @param _project_info
 */
App.dt.renderAll = function (_project_info) {

}

/**
 * 选
 * @param _project_info
 */
App.dt.getTpl = function (_tpl_id) {
    return document.getElementById(_tpl_id).innerHTML
}

App.dt.data = {};

/**
 * 当前项目ID
 * @type {string}
 * @private
 */
App.dt.data.curr_project = "";
App.dt.data.curr_project_version = "";

/**
 * 用于保存全局数据结构
 * @type {*[]}
 */
App.dt.data.projects = [];

/**
 * 操作项目的若干方法
 * @type {{}}
 */
App.dt.project = {};

/**
 * 菜单管理相关
 * @type {{}}
 */
App.dt.menu = {};
App.dt.menu.render = function () {
    var self = App.dt;
    var _json_copy = [];
    //hashmap to array
    _json_copy['projects'] = self.data.projects;
    var tpl = new jSmart(self.getTpl('tpl_project_menu_list'));
    var res = tpl.fetch(_json_copy);
    $("#project_menu_list").html(res);
}

/**
 * 渲染菜单
 * @param _menu_list
 */
App.dt.project.loadAll = function () {
    var self = App.dt;
    self.aPost("act=index", self.project.onLoadAll);
}

/**
 * 加载全部数据
 * @param json_projects
 */
App.dt.project.onLoadAll = function (_server_return) {
    var self = App.dt;
    var json_projects = _server_return['projects'];
    self.data.projects = [];
    var firstOne = true;
    var firstProject = "";
    $.each(json_projects, function (i, item) {

        var _project = new MyProject();
        _project.parse(item);
        var _name = _project.name;
        if (MyProject.isGoodName(_name)) {
            if (firstOne) {
                firstOne = false;
                firstProject = _name;
            }
            self.data.projects[_name] = _project;
        }
    });
    self.menu.render();

    if (self.data.curr_project != "") {
        self.data.curr_project = firstProject;
    }


    if (self.data.curr_project != "") {
        //加载第一个项目的第一个应用
        self.data.curr_project_version = "";
        var firstVersion = "";
        var project_name = self.data.curr_project;
        var _version_list = self.data.projects[project_name].version_list;

        for (var ii in _version_list) {
            //var _version = ii
            var _version = _version_list[ii];
            firstVersion = _version.uuid;
            break;
        }
        var last_app_version = self.data.curr_project_version;
        if (last_app_version == "" || undefined == _version_list[last_app_version]) {
            self.data.curr_project_version = firstVersion;
        }
        self.project.loadProject(project_name, self.data.curr_project_version);
    }
};

/**
 * 加载1个项目
 * @param project_name
 */
App.dt.project.loadProject = function (project_name, app_version) {

    var self = App.dt;
    self.data.curr_project = project_name;
    self.data.curr_project_version = app_version
    var _project = self.data.projects[self.data.curr_project];

    $(".menu_row_project_a").removeClass("active");
    $(".menu_row_app_a").removeClass("active");
    var _me = $("#menu_row_project_" + project_name);
    var _a = _me.find(".menu_row_project_a");
    _a.addClass("active");
    //_a.trigger("click");
    // self.succ()
    $("#txt_project_name").val(_project.name);
    $("#txt_project_title").val(_project.title);
    $("#txt_project_memo").val(_project.memo);
    $("#txt_project_ctime").val(_project.ctime);
    $("#txt_project_utime").val(_project.utime);
    //
    if (app_version != "") {
        var _app = _project.version_list[self.data.curr_project_version];
        //最后一个app无法删除
        var _meapp = $("#menu_row_app_" + app_version);
        var _aapp = _meapp.find(".menu_row_app_a");
        _aapp.addClass("active");

        $("#txt_curr_version_name").html(_app.name);
        $("#txt_curr_version_title").html(_app.title);

        $(".txt_app_name").val(_app.name);
        $(".txt_app_title").val(_app.title);
        $(".txt_app_memo").val(_app.memo);
        $(".txt_app_ctime").val(_app.ctime);
        $(".txt_app_utime").val(_app.utime);
        console.log(_app.img_icon_id);
        console.log(_app.img_logo_id);

        $(".img_icon_saved").attr("src", "tool.php?act=app_img&project=" + project_name + "&version=" + app_version + "&img_id=" + _app.img_icon_id);
        $(".img_logo_saved").attr("src", "tool.php?act=app_img&project=" + project_name + "&version=" + app_version + "&img_id=" + _app.img_logo_id);

        $("#img_icon_id").val(_app.img_icon_id);
        $("#img_logo_id").val(_app.img_logo_id);

        //其他更新
        self.editor.updateTitle();
    }

}

App.dt.editor = {};
App.dt.editor.updateTitle = function () {
    var self = App.dt;
    var _project = self.data.projects[self.data.curr_project];


    var tt = new StringBuffer();
    tt.append("当前项目：");
    tt.append(_project.name);
    tt.append(" / ");
    tt.append(_project.title);
    var _title = tt.toString();
    $("#txt_curr_project").html(_title);
    $(document).attr('title', _title);
}

App.dt.editor.getUploadParam = function () {
    return {
        showCancel: false,
        showPreview: false,
        theme: 'fas',
        language: 'zh',
        uploadUrl: function () {
            return 'img_upload.php?_rnd=' + App.su.maths.uuid.create();
        },
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        uploadExtraData: function () {
            return {
                project: App.dt.data.curr_project,
                version: App.dt.data.curr_project_version
            };
        }
    }
}

/**
 * 添加一个
 * @param project_name
 */
App.dt.project.addProject = function (project_name) {
    var self = App.dt;
    var sbf = new StringBuffer();
    sbf.append("act=init");
    sbf.append("&project=");
    sbf.append(encodeURIComponent(project_name));
    var _data = sbf.toString();
    self.aPost(_data, self.project.onAddProject);
}

App.dt.project.onAddProject = function (_server_return) {
    var self = App.dt;
    var project_info = _server_return['project_info'];
    if (App.su.isEmpty(project_info)) {
        self.fail("保存失败，请稍后再试试");
        return;
    }
    var _project = new MyProject();
    _project.parse(project_info);
    var _name = _project.name;
    if (MyProject.isGoodName(_name)) {
        self.data.projects[_name] = _project;
        self.data.curr_project = _name;
        self.menu.render();

        var firstVersion = "";
        var _version_list = _project.version_list;
        for (var ii in _version_list) {
            //var _version = ii
            var _version = _version_list[ii];
            firstVersion = _version.uuid;
            break;
        }
        self.project.loadProject(_name, firstVersion);

        $("#menu_row_project_" + _name).find(".menu_row_project_a").trigger("click");


        self.succ("添加成功，请在右边进一步编辑");
    } else {
        self.fail("服务器返回错误信息，请稍后再试试");
    }
}

/**
 * 修改当前项目的标题和备注
 * @param project_name
 */
App.dt.project.update = function () {
    var self = App.dt;
    var sbf = new StringBuffer();
    var _curr_project = self.data.curr_project;
    /**
     * 只能保存标题和备注
     */
    var _title = $("#txt_project_title").val();
    var _memo = $("#txt_project_memo").val();
    sbf.append("act=project_update");
    sbf.append("&project=");
    sbf.append(encodeURIComponent(_curr_project));
    sbf.append("&title=");
    sbf.append(encodeURIComponent(_title));
    sbf.append("&memo=");
    sbf.append(encodeURIComponent(_memo));
    var _data = sbf.toString();
    self.aPost(_data, self.project.onUpdate);
}


App.dt.project.onUpdate = function (_server_return) {
    var self = App.dt;
    var project_info = _server_return['project_info'];
    if (App.su.isEmpty(project_info)) {
        self.fail("保存失败，请稍后再试试2");
        return;
    }
    var _project = new MyProject();
    _project.parse(project_info);
    var _name = _project.name;
    //self.data.projects[_name] = _project;
    self.data.projects[_name].title = _project.title;
    self.data.projects[_name].memo = _project.memo;
    self.data.projects[_name].utime = _project.utime;

    $("#txt_project_utime").val(_project.utime);
    self.succ("更新成功")
}


/**
 * 修改当前项目的标题和备注
 * @param project_name
 */
App.dt.project.updateApp = function () {
    var self = App.dt;
    var _curr_project = self.data.curr_project;
    if(App.su.isEmpty(_curr_project) || undefined == self.data.projects[_curr_project]){
        self.fail("项目不存在2")
        return;
    }

    var sbf = new StringBuffer();

    var _curr_version = self.data.curr_project_version;
    var _name = $("#txt_app_name").val();
    var _version_list = self.data.projects[_curr_project].version_list;

    for (var ii in _version_list) {
        //var _version = ii
        var _version = _version_list[ii];
        var __uuid = _version.uuid;
        var __name = _version.name;
        if (_name == __name && __uuid != _curr_version) {
            self.fail("存在同名的其他版本--" + __name)
            return;
        }
    }

    /**
     * 只能保存标题和备注
     */
    var _title = $("#txt_app_title").val();
    var _memo = $("#txt_app_memo").val();

    var _img_icon_id = $("#img_icon_id").val();
    var _img_logo_id = $("#img_logo_id").val();

    var _project = self.data.projects[_curr_project];
    _project.version_list[_curr_version].name = _name;
    _project.version_list[_curr_version].memo = _memo;
    _project.version_list[_curr_version].title = _title;
    _project.version_list[_curr_version].img_icon_id = _img_icon_id;
    _project.version_list[_curr_version].img_logo_id = _img_logo_id;

    var dataStr = JSON.stringify(_project);

    sbf.append("act=save");
    sbf.append("&project=");
    sbf.append(encodeURIComponent(_curr_project));
    sbf.append("&version=");
    sbf.append(encodeURIComponent(_curr_version));
    sbf.append("&data=");
    sbf.append(encodeURIComponent(dataStr));
    var _data = sbf.toString();
    self.aPost(_data, self.project.onUpdateApp);
}

/**
 * 全更新
 * @param _server_return
 */
App.dt.project.onUpdateApp = function (_server_return) {
    var self = App.dt;
    var project_info = _server_return['project_info'];
    if (App.su.isEmpty(project_info)) {
        self.fail("保存失败，请稍后再试试3");
        return;
    }
    var _project = new MyProject();
    _project.parse(project_info);
    var _name = _project.name;
    //self.data.projects[_name] = _project;
    self.data.projects[_name] = _project;

    var _img_icon_id = $("#img_icon_id").val();
    var _img_logo_id = $("#img_logo_id").val();

    var project_name = self.data.curr_project;
    var app_version = self.data.curr_project_version;

    $(".img_icon_saved").attr("src", "tool.php?act=app_img&project=" + project_name + "&version=" + app_version + "&img_id=" + _img_icon_id);
    $(".img_logo_saved").attr("src", "tool.php?act=app_img&project=" + project_name + "&version=" + app_version + "&img_id=" + _img_logo_id);
    $(".img_icon_free").attr("src", "");
    $(".img_logo_free").attr("src", "");

    var _app= _project[app_version];

    $(".txt_app_name").val(_app.name);
    $(".txt_app_title").val(_app.title);
    $(".txt_app_memo").val(_app.memo);
    $(".txt_app_ctime").val(_app.ctime);
    $(".txt_app_utime").val(_app.utime);

    $("#modal_edit_app_info").modal('hide');
    self.succ("更新成功");
}


/**
 * 修改当前项目的标题和备注
 * @param project_name
 */
App.dt.project.addApp = function (new_version) {
    var self = App.dt;
    var _curr_project = self.data.curr_project;
    if(App.su.isEmpty(_curr_project) || undefined == self.data.projects[_curr_project]){
        self.fail("项目不存在2")
        return;
    }

    var _project = self.data.projects[_curr_project];
    for(var ii in _project.version_list){
        var _app = _project.version_list[ii];
        if(new_version == _app.name) {
            self.fail("本项目已经存在同名的版本--"+new_version);
            return;
        }
    }
    //var _curr_version = self.data.curr_project_version;
    var sbf = new StringBuffer();

    sbf.append("act=add");
    sbf.append("&project=");
    sbf.append(encodeURIComponent(_curr_project));
    sbf.append("&version=");
    sbf.append(encodeURIComponent(new_version));
    var _data = sbf.toString();
    self.aPost(_data, self.project.onCloneApp);
}

/**
 * 全更新
 * @param _server_return
 */
App.dt.project.onAddApp = function (_server_return) {
    var self = App.dt;
    var project_info = _server_return['project_info'];
    var _app_version = _server_return['new_app_version'];
    if (App.su.isEmpty(project_info)) {
        self.fail("保存失败，请稍后再试试3");
        return;
    }
    var _project = new MyProject();
    _project.parse(project_info);
    var _name = _project.name;
    //self.data.projects[_name] = _project;
    self.data.projects[_name] = _project;

    self.menu.render();

    self.project.loadProject(_name,_app_version);

    self.succ("添加成功");
}

/**
 * 修改当前项目的标题和备注
 * @param project_name
 */
App.dt.project.cloneApp = function (new_version) {
    var self = App.dt;
    var _curr_project = self.data.curr_project;
    if(App.su.isEmpty(_curr_project) || undefined == self.data.projects[_curr_project]){
        self.fail("项目不存在2")
        return;
    }

    var _project = self.data.projects[_curr_project];
    for(var ii in _project.version_list){
        var _app = _project.version_list[ii];
        if(new_version == _app.name) {
            self.fail("本项目已经存在同名的版本--"+new_version);
            return;
        }
    }



    var _curr_version = self.data.curr_project_version;
    var sbf = new StringBuffer();

    sbf.append("act=copy");
    sbf.append("&project=");
    sbf.append(encodeURIComponent(_curr_project));
    sbf.append("&version=");
    sbf.append(encodeURIComponent(_curr_version));
    sbf.append("&version2=");
    sbf.append(encodeURIComponent(new_version));
    var _data = sbf.toString();
    self.aPost(_data, self.project.onCloneApp);
}

/**
 * 全更新
 * @param _server_return
 */
App.dt.project.onCloneApp = function (_server_return) {
    var self = App.dt;
    var project_info = _server_return['project_info'];
    var _app_version = _server_return['new_app_version'];
    if (App.su.isEmpty(project_info)) {
        self.fail("保存失败，请稍后再试试3");
        return;
    }
    var _project = new MyProject();
    _project.parse(project_info);
    var _name = _project.name;
    //self.data.projects[_name] = _project;
    self.data.projects[_name] = _project;

    self.menu.render();

    self.project.loadProject(_name,_app_version);

    self.succ("复制成功");
}

/**
 * 删除app和删除app后的处理
 */
App.dt.project.deleteApp = function () {
    var self = App.dt;
    var _curr_project = self.data.curr_project;
    if(App.su.isEmpty(_curr_project) || undefined == self.data.projects[_curr_project]){
        self.fail("项目不存在3")
        return;
    }
    var _project = self.data.projects[_curr_project];

    if ( Object.keys(_project.version_list).length < 2) {
        self.fail("最后一个版本不能删除");
        return;
    }
    var _curr_version = self.data.curr_project_version;

    bootbox.confirm("确认删除当前版本", function (result) {
        console.log(result);
        if (result) {
            var sbf = new StringBuffer();
            sbf.append("act=drop");
            sbf.append("&project=");
            sbf.append(encodeURIComponent(_curr_project));
            sbf.append("&version=");
            sbf.append(encodeURIComponent(_curr_version));
            var _data = sbf.toString();
            self.aPost(_data, self.project.onDeleteApp);
        }
    });


}

App.dt.project.onDeleteApp = function (_server_return) {

    var project_info = _server_return['project_info'];

    if (App.su.isEmpty(project_info)) {
        self.fail("删除失败，请稍后再试试3");
        return;
    }
    var _project = new MyProject();
    _project.parse(project_info);
    var _name = _project.name;
    //self.data.projects[_name] = _project;
    self.data.projects[_name] = _project;
    self.data.curr_project_version = "";
    self.menu.render();

    var _version_list = _project.version_list;

    for (var ii in _version_list) {
        //var _version = ii
        var _version = _version_list[ii];
        firstVersion = _version.uuid;
        break;
    }
    self.data.curr_project_version = firstVersion;
    self.project.loadProject(_name,firstVersion);

    self.succ("删除成功");
}

/**
 * 主程序入口
 */
App.dt.init = function () {
    var self = App.dt;
    App.dt.project.loadAll();

    /**
     * 1.1 增加一个项目
     */
    $("#btn_new_project").click(function () {
        var _name = $("#ipt_new_project").val();
        if (!MyProject.isGoodName(_name)) {
            self.fail("项目名字需要字母开头，字母+数字组合，长度2-32个字符");
            return;
        }
        if (undefined !== self.data.projects[_name]) {
            self.fail("项目已经存在，请用别的名称--" + _name);
            return;
        }
        self.project.addProject(_name);
    });

    /**
     * 1.2 保存一个项目
     */
    $("#btn_save_project").click(function () {
        self.project.update();
    });

    /**
     * 1.3 app的icon和logo的上传
     */
    $("#btn_edit_app").click(function () {
        $("#modal_edit_app_info").modal('show');
    });
    $('#app_file_logo').fileinput(self.editor.getUploadParam())
        .on('fileuploaded', function (event, data, index, fileId) {

            if (data.response.code == "ok") {
                var _tmpUrl = data.response.img_url;
                var _tmpId = data.response.img_id;
                $("#img_logo_free").attr("src", _tmpUrl);
                $("#img_logo_id").val(_tmpId);
            }
        });

    $('#app_file_icon').fileinput(self.editor.getUploadParam())
        .on('fileuploaded', function (event, data, index, fileId) {

            if (data.response.code == "ok") {
                var _tmpUrl = data.response.img_url;
                var _tmpId = data.response.img_id;
                $("#img_icon_free").attr("src", _tmpUrl);
                $("#img_icon_id").val(_tmpId);
            }
        });

    /**
     * 1.4 保存app
     */
    $("#btn_save_app").click(function () {
        self.project.updateApp();
    });

    /**
     * 1.5 复制app
     */
    $("#btn_clone_app").click(function () {

        bootbox.prompt("请输入复制后的新版本号", function (result) {
            console.log(result);
            if (!App.su.isEmpty(result)) {
                self.project.cloneApp(result);
            }
        });
    });

    /**
     * 1.5 删除app
     */
    $("#btn_delete_app").click(function () {
        self.project.deleteApp();
    });

    /**
     * 1.7 添加app
     */
    $("#btn_add_app").click(function () {
        bootbox.prompt("请输入新版本号", function (result) {
            console.log(result);
            if (!App.su.isEmpty(result)) {
                self.project.addApp(result);
            }
        });
    });

    /**
     *   $(".btn-warning").on('click', function () {
        var $el = $("#file-4");
        if ($el.attr('disabled')) {
            $el.fileinput('enable');
        } else {
            $el.fileinput('disable');
        }
    });
     */

}