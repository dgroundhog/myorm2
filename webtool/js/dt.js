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

    for (var ii in json_projects) {
        var _project = new MyProject();
        var item = json_projects[ii];
        _project.parse(item);
        var _name = _project.name;
        if (MyProject.isGoodName(_name)) {
            if (firstOne) {
                firstOne = false;
                firstProject = _name;
            }
            self.data.projects[_name] = _project;
        }
    }


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
        //console.log(_app.img_icon_id);
        //console.log(_app.img_logo_id);

        $(".img_icon_saved").attr("src", "tool.php?act=app_img&project=" + project_name + "&version=" + app_version + "&img_id=" + _app.img_icon_id);
        $(".img_logo_saved").attr("src", "tool.php?act=app_img&project=" + project_name + "&version=" + app_version + "&img_id=" + _app.img_logo_id);

        $("#img_icon_id").val(_app.img_icon_id);
        $("#img_logo_id").val(_app.img_logo_id);

        //其他更新
        self.editor.updateTitle();
        //加载配置
        self.project.confLoad();
        //加载数据库
        self.project.dbLoad();
        //加载全局字段
        self.project.fieldLoad();
        //加载模型
        self.project.modelLoad();
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

App.dt.editor.getBootSwitchVal = function (who) {
    var _val_bool = $(who).bootstrapSwitch('state');
    return _val_bool ? "1" : "0";
}

App.dt.editor.setBootSwitchVal = function (who, val) {

    $(who).bootstrapSwitch('state', (val == "1") ? true : false);

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
 * 修改当前项目应用
 * @param project_name
 */
App.dt.project.syncApp = function () {
    var self = App.dt;
    var _curr_project = self.project.getCurrProject();
    if (null == _curr_project) {
        self.fail("未选中项目")
        return;
    }

    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选中应用")
        return;
    }


    var dataStr = JSON.stringify(_curr_app);

    var sbf = new StringBuffer();
    sbf.append("act=save");
    sbf.append("&project=");
    sbf.append(encodeURIComponent(_curr_app.project_id));
    sbf.append("&version=");
    sbf.append(encodeURIComponent(_curr_app.uuid));
    sbf.append("&data=");
    sbf.append(encodeURIComponent(dataStr));
    var _data = sbf.toString();
    self.aPost(_data, self.project.onSyncApp);
}

/**
 * 全更新整个项目
 * @param _server_return
 */
App.dt.project.onSyncApp = function (_server_return) {
    var self = App.dt;
    var project_info = _server_return['project_info'];
    if (App.su.isEmpty(project_info)) {
        self.fail("同步失败，请稍后再试试3");
        return;
    }
    var _project = new MyProject();
    _project.parse(project_info);
    var _name = _project.name;
    //self.data.projects[_name] = _project;
    self.data.projects[_name] = _project;
    //UI 上更新2个时间
    $(".txt_project_utime").val(_project.utime);
    var app_version = self.data.curr_project_version;
    var _app = _project.version_list[app_version];

    $(".txt_app_utime").val(_app.utime);

    self.succ("和服务器同步成功");
}


/**
 * 修改当前项目的标题和备注
 * @param project_name
 */
App.dt.project.updateApp = function () {
    var self = App.dt;
    var _curr_project = self.project.getCurrProject();
    if (null == _curr_project) {
        self.fail("未选中项目")
        return;
    }

    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选中应用")
        return;
    }

    var _name = $("#txt_app_name").val();
    var _version_list = _curr_project.version_list;
    var _myuuid = _curr_app.uuid;

    for (var ii in _version_list) {
        //var _version = ii
        var _version = _version_list[ii];
        var __uuid = _version.uuid;
        var __name = _version.name;
        if (_name == __name && __uuid != _myuuid) {
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

    _curr_app.name = _name;
    _curr_app.memo = _memo;
    _curr_app.title = _title;
    _curr_app.img_icon_id = _img_icon_id;
    _curr_app.img_logo_id = _img_logo_id;
    self.project.setCurrApp(_curr_app);

    $(".img_icon_saved").attr("src", "tool.php?act=app_img&project=" + _curr_app.project_id + "&version=" + _myuuid + "&img_id=" + _img_icon_id);
    $(".img_logo_saved").attr("src", "tool.php?act=app_img&project=" + _curr_app.project_id + "&version=" + _myuuid + "&img_id=" + _img_logo_id);
    $(".img_icon_free").attr("src", "");
    $(".img_logo_free").attr("src", "");
    $(".txt_app_name").val(_curr_app.name);
    $(".txt_app_title").val(_curr_app.title);
    $(".txt_app_memo").val(_curr_app.memo);
    $(".txt_app_ctime").val(_curr_app.ctime);
    $(".txt_app_utime").val(_curr_app.utime);

    $("#modal_edit_app_info").modal('hide');
    self.succ("暂存成功");

}


/**
 * 修改当前项目的标题和备注
 * @param project_name
 */
App.dt.project.addApp = function (new_version) {
    var self = App.dt;
    var _curr_project = self.data.curr_project;
    if (App.su.isEmpty(_curr_project) || undefined == self.data.projects[_curr_project]) {
        self.fail("项目不存在2")
        return;
    }

    var _project = self.data.projects[_curr_project];
    for (var ii in _project.version_list) {
        var _app = _project.version_list[ii];
        if (new_version == _app.name) {
            self.fail("本项目已经存在同名的版本--" + new_version);
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

    self.project.loadProject(_name, _app_version);

    self.succ("添加成功");
}

/**
 * 修改当前项目的标题和备注
 * @param project_name
 */
App.dt.project.cloneApp = function (new_version) {
    var self = App.dt;
    var _curr_project = self.data.curr_project;
    if (App.su.isEmpty(_curr_project) || undefined == self.data.projects[_curr_project]) {
        self.fail("项目不存在2")
        return;
    }

    var _project = self.data.projects[_curr_project];
    for (var ii in _project.version_list) {
        var _app = _project.version_list[ii];
        if (new_version == _app.name) {
            self.fail("本项目已经存在同名的版本--" + new_version);
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

    self.project.loadProject(_name, _app_version);

    self.succ("复制成功");
}

/**
 * 删除app和删除app后的处理
 */
App.dt.project.deleteApp = function () {
    var self = App.dt;
    var _curr_project = self.data.curr_project;
    if (App.su.isEmpty(_curr_project) || undefined == self.data.projects[_curr_project]) {
        self.fail("项目不存在3")
        return;
    }
    var _project = self.data.projects[_curr_project];

    if (Object.keys(_project.version_list).length < 2) {
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
    var self = App.dt;
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
    self.project.loadProject(_name, firstVersion);

    self.succ("删除成功");
}


/**
 * 获取当前应用
 * null or MyApp
 */
App.dt.project.getCurrProject = function () {
    var self = App.dt;
    var _curr_project = self.data.curr_project;

    if (App.su.isEmpty(_curr_project) || undefined == self.data.projects[_curr_project]) {
        console.log("项目不存在--" + _curr_project);
        return null;
    }
    return self.data.projects[_curr_project];


}

/**
 * 获取当前应用
 * null or MyApp
 */
App.dt.project.getCurrModel = function (model_id) {

    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        //self.fail("未选择应用版本");
        console.log("目标应用--空");
        return null;
    }
    if (App.su.isEmpty(model_id) || undefined == _curr_app.model_list[model_id]) {
        //self.fail("未选择目标模型");
        console.log("目标模型--空");
        return null;
    }
    return _curr_app.model_list[model_id];
}

/**
 * 获取当前应用
 * null or MyApp
 */
App.dt.project.getCurrApp = function () {
    var self = App.dt;
    var _curr_project = self.data.curr_project;

    if (App.su.isEmpty(_curr_project) || undefined == self.data.projects[_curr_project]) {
        console.log("项目不存在--" + _curr_project);
        return null;
    }
    var _project = self.data.projects[_curr_project];
    var _curr_version = self.data.curr_project_version;
    if (App.su.isEmpty(_curr_version) || undefined == _project.version_list[_curr_version]) {
        console.log("应用不存在11--" + _curr_version);
        return null;
    }
    return _project.version_list[_curr_version];
}

/**
 * 设置当前应用
 * true or false
 * @param app
 */
App.dt.project.setCurrApp = function (app) {
    var self = App.dt;
    var _curr_project = self.data.curr_project;
    if (App.su.isEmpty(_curr_project) || undefined == self.data.projects[_curr_project]) {
        console.log("项目不存在22--" + _curr_project);
        return false;
    }
    var _project = self.data.projects[_curr_project];
    var _curr_version = self.data.curr_project_version;
    if (App.su.isEmpty(_curr_version) || undefined == _project.version_list[_curr_version]) {
        console.log("应用不存在22--" + _curr_version);
        return null;
    }
    _project.version_list[_curr_version] = app;
    return true;
}

/**
 * 加载配置
 */
App.dt.project.confLoad = function () {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本,无法打开配置");
        return;
    }

    var tpl = new jSmart(self.getTpl('tpl_conf_list'));
    var res = tpl.fetch(_curr_app);
    $("#table_conf_list").html(res);
}


/**
 * 保存配置
 */
App.dt.project.confSave = function () {
    var self = App.dt;
    var _uuid = $("#txt_conf_uuid").val();
    var _conf = new MyAppConf();
    var now = App.su.datetime.getCurrentDateTime();
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("保存失败");
        return;
    }

    if (App.su.isEmpty(_uuid)) {
        var new_uuid = App.su.maths.uuid.create();
        _conf.uuid = new_uuid;
        _conf.name = now;
        _conf.ctime = now;
        _conf.utime = now;
        _uuid = new_uuid;
    } else {
        _conf = _curr_app.conf_list[_uuid];
        _conf.utime = now;
    }

    _conf.mvc = $("#sel_app_mvc").val();
    _conf.ui = $("#sel_app_ui").val();


    _conf.has_restful = self.editor.getBootSwitchVal("#txt_conf_has_restful");
    _conf.has_doc = self.editor.getBootSwitchVal("#txt_conf_has_doc");
    _conf.has_test = self.editor.getBootSwitchVal("#txt_conf_has_test");


    console.log(_conf);

    _curr_app.conf_list[_uuid] = _conf;
    if (self.project.setCurrApp(_curr_app)) {
        self.succ("暂存成功");
    } else {
        self.fail("暂存失败");
    }
    $("#modal_edit_app_conf").modal('hide');
    self.project.confLoad();
}

/**
 * 编辑配置
 */
App.dt.project.confEdit = function (_uuid) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本");
        return;
    }
    if (App.su.isEmpty(_uuid)) {
        console.log("新的配置");

        self.editor.setBootSwitchVal("#txt_conf_has_restful", "1");
        self.editor.setBootSwitchVal("#txt_conf_has_doc", "1");
        self.editor.setBootSwitchVal("#txt_conf_has_test", "1");

        $("#txt_conf_uuid").val("");

    } else {
        console.log("编辑旧配置");
        $("#txt_conf_uuid").val(_uuid);
        var _conf = _curr_app.conf_list[_uuid];

        App.su.select.selectByValue($("#sel_app_mvc"), _conf.mvc);
        App.su.select.selectByValue($("#sel_app_ui"), _conf.ui);

        self.editor.setBootSwitchVal("#txt_conf_has_restful", _conf.has_restful);
        self.editor.setBootSwitchVal("#txt_conf_has_doc", _conf.has_doc);
        self.editor.setBootSwitchVal("#txt_conf_has_test", _conf.has_test);

    }

    $("#modal_edit_app_conf").modal('show');

}

/**
 * 编辑配置
 */
App.dt.project.confDrop = function (_uuid) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本");
        return;
    }
    //
    if (App.su.isEmpty(_uuid)) {
        self.fail("未选择配置");
        return;
    } else {
        bootbox.confirm("确认删除这个配置", function (ret) {
            if (ret) {
                if (undefined != _curr_app.conf_list[_uuid]) {
                    delete _curr_app.conf_list[_uuid];
                    self.succ("移除成功");
                } else {
                    self.fail("移除失败");
                }
                self.project.confLoad();
            }
        });
    }
}

/**
 * 加载配置
 */
App.dt.project.dbLoad = function () {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本,无法打开配置");
        return;
    }

    var tpl = new jSmart(self.getTpl('tpl_db_list'));
    var res = tpl.fetch(_curr_app);
    $("#table_db_list").html(res);
}


/**
 * 保存配置
 */
App.dt.project.dbSave = function () {
    var self = App.dt;
    var _uuid = $("#txt_db_uuid").val();
    var _db = new MyDb();
    var now = App.su.datetime.getCurrentDateTime();
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("保存失败");
        return;
    }

    if (App.su.isEmpty(_uuid)) {
        var new_uuid = App.su.maths.uuid.create();
        _db.uuid = new_uuid;
        _db.name = now;
        _db.ctime = now;
        _db.utime = now;
        _uuid = new_uuid;
    } else {
        _db = _curr_app.db_list[_uuid];
        _db.utime = now;
    }

    _db.driver = $("#sel_db_driver").val();
    _db.source = $("#sel_db_source").val();
    _db.host = $("#txt_db_host").val();
    _db.port = $("#txt_db_port").val();
    _db.database = $("#txt_db_database").val();
    _db.user = $("#txt_db_user").val();
    _db.password = $("#txt_db_passwd").val();
    _db.charset = $("#sel_db_charset").val();
    _db.uri = $("#txt_db_uri").val();


    console.log(_db);

    _curr_app.db_list[_uuid] = _db;
    if (self.project.setCurrApp(_curr_app)) {
        self.succ("暂存成功");
    } else {
        self.fail("暂存失败");
    }
    $("#modal_edit_app_db").modal('hide');
    self.project.dbLoad();
}

/**
 * 编辑配置
 */
App.dt.project.dbEdit = function (_uuid) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本2");
        return;
    }
    var _db = new MyDb();
    if (App.su.isEmpty(_uuid)) {
        console.log("新的配置2");
        $("#txt_db_uuid").val("");

    } else {
        console.log("编辑旧配置2");
        $("#txt_db_uuid").val(_uuid);
        _db = _curr_app.db_list[_uuid];
    }

    $("#txt_db_host").val(_db.host);
    $("#txt_db_port").val(_db.port);
    $("#txt_db_database").val(_db.database);
    $("#txt_db_user").val(_db.user);
    $("#txt_db_passwd").val(_db.password);
    $("#txt_db_uri").val(_db.uri);

    App.su.select.selectByValue($("#sel_db_driver"), _db.driver);
    App.su.select.selectByValue($("#sel_db_source"), _db.source);
    App.su.select.selectByValue($("#sel_db_charset"), _db.charset);

    $("#modal_edit_app_db").modal('show');

}

/**
 * 编辑配置
 */
App.dt.project.dbDrop = function (_uuid) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本");
        return;
    }
    if (App.su.isEmpty(_uuid)) {
        self.fail("未选择配置");
        return;
    } else {
        bootbox.confirm("确认删除这个数据库配置", function (ret) {
            if (ret) {
                if (undefined != _curr_app.db_list[_uuid]) {
                    delete _curr_app.db_list[_uuid];
                    self.succ("移除成功");
                } else {
                    self.fail("移除失败");
                }
                self.project.dbLoad();
            }
        });
    }
}


/**
 * 加载字段，全局
 */
App.dt.project.fieldLoad = function () {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本,无法打开配置");
        return;
    }
    var _old_list = _curr_app.field_list;
    var tpl = new jSmart(self.getTpl('tpl_field_list'));
    var res = tpl.fetch(_curr_app);
    //排序 arr.sort((a,b) => a.sortNo -  b.sortNo)
    var g_field_list = $("#table_field_list");
    g_field_list.html(res);
    g_field_list.sortable({
        stop: function () {
            var _new_list = new Object();
            var iii = 0
            g_field_list.find(".field_row").each(function () {
                var _me = $(this);
                var _uuid = _me.attr("title");
                if (undefined != _old_list[_uuid]) {
                    iii++;
                    var _field = _old_list[_uuid];
                    _field.position = iii;
                    _new_list[_uuid] = _field;
                }
            });
            console.log(_new_list);
            _curr_app.field_list = _new_list;
            if (self.project.setCurrApp(_curr_app)) {
                console.log(self.project.getCurrApp());
                self.succ("更新成功");
                self.project.fieldLoad();
            } else {
                self.fail("更新失败");
            }
        }
    });
}


/**
 * 保存字段
 */
App.dt.project.fieldSave = function () {
    var self = App.dt;
    var _uuid = $("#txt_field_uuid").val();
    var _model_id = $("#txt_field_model_id").val();
    //如果modelID 非空，则保存到model节点   TODO
    var _field = new MyField();
    var now = App.su.datetime.getCurrentDateTime();
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("保存失败,未打开app");
        return;
    }
    //TODO 判断model _model_id
    var _new_name = $("#txt_field_name").val();
    if (!MyProject.isGoodName(_new_name)) {
        self.fail("请输入合法的字段名，字母开头，可包含字母、数字和下划线");
        return;
    }

    if (App.su.isEmpty(_uuid)) {
        var new_uuid = App.su.maths.uuid.create();
        _field.uuid = new_uuid;
        _field.ctime = now;
        _field.utime = now;
        if (App.su.isEmpty(_model_id)) {
            _field.is_global = 1;
        } else {
            _field.is_global = 0;
        }
        _uuid = new_uuid;
    } else {
        _field = _curr_app.field_list[_uuid];

        _field.utime = now;
    }


    _field.name = _new_name;


    _field.title = $("#txt_field_title").val();
    _field.memo = $("#txt_field_memo").val();
    _field.type = $("#sel_field_type").val();
    _field.size = $("#txt_field_size").val();
    _field.auto_increment = self.editor.getBootSwitchVal("#txt_field_auto_inc");
    _field.default_value = $("#txt_field_default_val").val();
    _field.required = self.editor.getBootSwitchVal("#txt_field_required");
    _field.filter = $("#sel_field_filter").val();
    _field.regexp = $("#txt_field_regexp").val();
    _field.input_by = $("#sel_field_input_by").val();
    _field.input_hash = $("#txt_field_hash").val();
    _field.position = $("#txt_field_position").val();
    //TODO 去重复
    console.log(_field);
    if (App.su.isEmpty(_model_id)) {
        console.log("全局字段");
        _curr_app.field_list[_uuid] = _field;
    } else {
        console.log("私有字段");
        var _model = self.project.getCurrModel(_model_id);
        if (null == _model) {
            self.fail("保存失败,未打开model");
            return;
        }
        _model.field_list[_uuid] = _field;
        _curr_app.model_list[_model_id] = _model;
    }
    console.log(_curr_app);
    if (self.project.setCurrApp(_curr_app)) {
        console.log(self.project.getCurrApp());
        self.succ("暂存成功");
    } else {
        self.fail("暂存失败");
    }
    $("#modal_edit_field").modal('hide');
    self.project.fieldLoad();
}

/**
 * 编辑字段
 */
App.dt.project.fieldEdit = function (_uuid, _model_id) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本2");
        return;
    }

    //TODO 在这里填入model
    var _field = new MyField();


    if (App.su.isEmpty(_model_id)) {
        console.log("全局字段");
        $("#txt_field_model_id").val("");
        if (App.su.isEmpty(_uuid)) {
            console.log("新的字段1");
            $("#txt_field_uuid").val("");
        } else {
            console.log("编辑旧字段1");
            $("#txt_field_uuid").val(_uuid);
            _field = _curr_app.field_list[_uuid];
        }
    } else {
        console.log("私有字段");
        $("#txt_field_model_id").val(_model_id);

        if (App.su.isEmpty(_uuid)) {
            console.log("新的字段2");
            $("#txt_field_uuid").val("");
        } else {
            console.log("编辑旧字段2");
            $("#txt_field_uuid").val(_uuid);
            var _model = self.project.getCurrModel(_model_id);
            if (null == _model) {
                self.fail("打开失败,未打开model");
                return;
            }
            _field = _model.field_list[_uuid];
        }
    }

    $("#txt_field_name").val(_field.name);
    $("#txt_field_title").val(_field.title);
    $("#txt_field_memo").val(_field.memo);
    App.su.select.selectByValue($("#sel_field_type"), _field.type);
    $("#txt_field_size").val(_field.size);
    self.editor.setBootSwitchVal("#txt_field_auto_inc", _field.auto_increment);
    $("#txt_field_default_val").val(_field.default_value);
    self.editor.setBootSwitchVal("#txt_field_required", _field.required);

    App.su.select.selectByValue($("#sel_field_filter"), _field.filter);

    $("#txt_field_regexp").val(_field.regexp);

    App.su.select.selectByValue($("#sel_field_input_by"), _field.input_by);

    $("#txt_field_hash").val(_field.input_hash);
    $("#txt_field_position").val(_field.position);

    $("#modal_edit_field").modal('show');

}

/**
 * 删除字段
 */
App.dt.project.fieldDrop = function (_uuid) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本");
        return;
    }
    if (App.su.isEmpty(_uuid)) {
        self.fail("未选择字段");
        return;
    } else {
        //TODO 需要区分全局还是局部
        bootbox.confirm("确认删除这个字段配置", function (ret) {
            if (ret) {
                if (undefined != _curr_app.field_list[_uuid]) {
                    delete _curr_app.field_list[_uuid];
                    self.succ("移除成功");
                } else {
                    self.fail("移除失败");
                }
                self.project.fieldLoad();
            }
        });
    }
}


/**
 * 加载基本模型
 */
App.dt.project.modelLoad = function () {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本,无法打开配置");
        return;
    }
    var tpl = new jSmart(self.getTpl('tpl_model_list'));
    var res = tpl.fetch(_curr_app);
    $("#table_model_list").html(res);

    var tpl2 = new jSmart(self.getTpl('tpl_model_design'));
    var res2 = tpl2.fetch(_curr_app);
    $("#model_design").html(res2);

    var tpl3 = new jSmart(self.getTpl('tpl_model_menu'));
    var res3 = tpl3.fetch(_curr_app);
    $("#block_model_menu").html(res3);

    $(".m_sort_field_list").each(function () {
        var _mm = $(this);
        var _model_id = _mm.attr("title");
        var _model = self.project.getCurrModel(_model_id);
        if (_model == null) {
            return;
        }
        var _old_list = _model.field_list;
        _mm.sortable({
            stop: function () {
                var _new_list = new Object();
                var iii = 0
                _mm.find(".field_row").each(function () {
                    var _me = $(this);
                    var _uuid = _me.attr("title");
                    if (undefined != _old_list[_uuid]) {
                        iii++;
                        var _field = _old_list[_uuid];
                        _field.position = iii;
                        _new_list[_uuid] = _field;
                    }
                });
                console.log(_new_list);
                _model.field_list = _new_list;
                _curr_app.model_list[_model_id] = _model;
                if (self.project.setCurrApp(_curr_app)) {
                    console.log(self.project.getCurrApp());
                    self.succ("更新成功");
                    self.project.fieldLoad();
                } else {
                    self.fail("更新失败");
                }
            }
        }).disableSelection();
    });
}

/**
 * 保存模型
 */
App.dt.project.modelSave = function () {
    var self = App.dt;
    var _uuid = $("#txt_model_uuid").val();
    var _model = new MyModel();
    var now = App.su.datetime.getCurrentDateTime();
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("保存失败");
        return;
    }

    if (App.su.isEmpty(_uuid)) {
        var new_uuid = App.su.maths.uuid.create();
        _model.uuid = new_uuid;
        _model.ctime = now;
        _model.utime = now;
        _uuid = new_uuid;
    } else {
        _model = _curr_app.model_list[_uuid];
        _model.utime = now;
    }

    _model.name = $("#txt_model_name").val();
    _model.title = $("#txt_model_title").val();
    _model.memo = $("#txt_model_memo").val();
    _model.table_name = $("#txt_table_name").val();
    _model.primary_key = $("#txt_primary_key").val();
    _model.fa_icon = $("#txt_model_icon").val();

    console.log(_model);

    _curr_app.model_list[_uuid] = _model;
    if (self.project.setCurrApp(_curr_app)) {
        self.succ("暂存成功");
    } else {
        self.fail("暂存失败");
    }
    $("#modal_edit_model").modal('hide');
    self.project.modelLoad();
}

/**
 * 编辑模型
 */
App.dt.project.modelEdit = function (_uuid) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本");
        return;
    }
    if (App.su.isEmpty(_uuid)) {
        console.log("新的模型");
        $("#txt_model_uuid").val("");

    } else {
        console.log("编辑旧模型");
        $("#txt_model_uuid").val(_uuid);
        var _model = _curr_app.model_list[_uuid];

        $("#txt_model_name").val(_model.name);
        $("#txt_model_title").val(_model.title);
        $("#txt_model_memo").val(_model.memo);
        $("#txt_table_name").val(_model.table_name);
        $("#txt_primary_key").val(_model.primary_key);
        $("#txt_model_icon").val(_model.fa_icon);
    }

    $("#modal_edit_model").modal('show');

}

/**
 * 删除模型
 */
App.dt.project.modelDrop = function (_uuid) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本");
        return;
    }
    if (App.su.isEmpty(_uuid)) {
        self.fail("未选择模型");
        return;
    } else {
        bootbox.confirm("确认删除这个模型", function (ret) {
            if (ret) {
                if (undefined != _curr_app.model_list[_uuid]) {
                    delete _curr_app.model_list[_uuid];
                    self.succ("移除成功");
                } else {
                    self.fail("移除失败");
                }
                self.project.modelLoad();
            }
        });
    }
}

/**
 * 导入全局字段
 * @param model_id
 */
App.dt.project.modelImportGlobalField = function (model_id) {
    var self = App.dt;
    //
    $("#sel_index_field").empty();
    var _model = self.project.getCurrModel(model_id);
    if (null == _model) {
        self.fail("未选择目标模型");
        return;
    }
    //
    var _curr_app = self.project.getCurrApp();
    $("#txt_model_global_field_mid").val(model_id);

    var _field_g = _curr_app.field_list;
    var _field_m = _model.field_list;
    var sel_import = $("#sel_global_field");
    sel_import.empty();
    //
    for (var ii in _field_g) {
        var ff = _field_g[ii];
        var o = document.createElement("option");
        var _uuid = ff.uuid;
        o.value = _uuid;
        o.text = ff.name + " | " + ff.title;
        if (undefined != _field_m[_uuid]) {
            o.selected = "selected";
        }
        sel_import[0].options.add(o);
    }
    //
    sel_import.bootstrapDualListbox('refresh');
    $("#modal_import_global_field").modal("show");
};

/**
 * 完成全局字段导入
 */
App.dt.project.modelImportGlobalFieldDone = function () {
    var self = App.dt;
    //
    var _model_id = $("#txt_model_global_field_mid").val();
    console.log("_model_id--" + _model_id)

    var _model = self.project.getCurrModel(_model_id);
    if (null == _model) {
        self.fail("未选择目标模型");
        return;
    }
    //
    var _curr_app = self.project.getCurrApp();
    var _field_g = _curr_app.field_list;
    //遍历选中的值
    $("#sel_index_field").empty();
    $("#sel_global_field option:selected").each(function () {
        var _fid = $(this).val();
        if (undefined != _field_g[_fid]) {
            _model.field_list[_fid] = _field_g[_fid];
        }
    });
    $("#modal_import_global_field").modal("hide");
    //重新加载
    self.project.modelLoad();
};

/**
 * 编辑模型
 */
App.dt.project.modelIndexEdit = function (model_id, index_id) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本");
        return;
    }
    if (App.su.isEmpty(model_id) || undefined == _curr_app.model_list[model_id]) {
        self.fail("未选择目标模型");
        return;
    }
    var _model = _curr_app.model_list[model_id];
    $("#txt_model_index_mid").val(model_id);

    if (App.su.isEmpty(index_id)) {
        console.log("新的索引");
        $("#txt_model_index_iid").val("");

    } else {
        console.log("编辑旧索引");
        $("#txt_model_index_iid").val(index_id);
        var _idx = _model.idx_list[index_id];

        $("#txt_index_name").val(_idx.name);
        $("#txt_index_memo").val(_idx.memo);

        // $("#sel_index_type").val(_model.table_name);
        // $("#sel_index_field").val(_model.primary_key);

    }

    $("#modal_edit_index").modal('show');

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
     * 1.1 暂存保存一个项目
     */
    $("#btn_save_project").click(function () {
        self.project.update();
    });


    /**
     * 1.1 同步到服务器
     */
    $(".btn_sync_project").click(function () {
        self.project.syncApp();
    });


    /**
     * 1.2 app的icon和logo的上传
     */
    $("#btn_edit_app").click(function () {
        if (null != self.project.getCurrApp()) {
            $("#modal_edit_app_info").modal('show');
        } else {
            self.fail("当前未打开应用")
        }
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
     * 1.2 保存app
     */
    $("#btn_save_app").click(function () {
        self.project.updateApp();
    });

    /**
     * 1.2 复制app
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
     * 1.2 删除app
     */
    $("#btn_delete_app").click(function () {
        self.project.deleteApp();
    });

    /**
     * 1.2 添加app
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
     * 1.5 添加app
     */
    $("#btn_edit_conf").click(function () {
        self.project.confEdit("");
    });

    $("#btn_save_conf").click(function () {
        self.project.confSave();
    });

    /**
     * 1.6 数据库
     */
    $("#btn_edit_db").click(function () {
        self.project.dbEdit("");
    });

    $("#btn_save_db").click(function () {
        self.project.dbSave();
    });

    /**
     * 1.7 全局字段
     */

    $("#btn_edit_field").click(function () {
        self.project.fieldEdit("");
    });

    $("#btn_save_field").click(function () {
        self.project.fieldSave();
    });

    /**
     * 1.8 实体模型
     */
    $("#btn_edit_model").click(function () {
        self.project.modelEdit("");
    });

    $("#btn_save_model").click(function () {
        self.project.modelSave();
    });

    $("#btn_confirm_import").click(function () {
        self.project.modelImportGlobalFieldDone();
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