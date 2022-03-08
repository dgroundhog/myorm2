/**
 * 依赖jquery
 * 依赖app.js
 * 依赖toastr
 * 一旦提交，全部提交
 */

/**
 * @@ 外部输入
 * ## 所建立的内部聚合函数，如果存在
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
    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", // public method for encoding
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
            output = output + this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) + this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
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
        url: "./tool.php?_r=" + _uuid, type: 'POST', data: _data, dataType: 'json', // processData: false,
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
        }, error: function (responseStr) {
            console.log(responseStr);
            self.fail("服务器异常，请稍后再试--" + responseStr);
        }
    });
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
 * 基本字段id
 */
App.dt.data.ccField_ID = function () {
    var _field = new MyField();
    var _now = App.su.datetime.getCurrentDateTime();
    _field.uuid = App.su.maths.uuid.create();
    _field.ctime = _now;
    _field.utime = _now;
    _field.is_global = 1;//
    _field.name = "id";
    _field.title = "自增ID";
    _field.memo = "sys";
    _field.type = "LONGINT";
    _field.size = 10;
    _field.auto_increment = 1;
    _field.default_value = 0;
    _field.required = 1;
    _field.filter = "INT";
    _field.regexp = "";
    _field.input_by = "DEFAULT";
    _field.input_hash = "";
    _field.position = 1;

    return _field;
}

/**
 * 基本日期时间
 */
App.dt.data.ccField_Date = function (date_key, title) {
    var _field = new MyField();
    var _now = App.su.datetime.getCurrentDateTime();
    _field.uuid = App.su.maths.uuid.create();
    _field.ctime = _now;
    _field.utime = _now;
    _field.is_global = 1;//
    _field.name = date_key;
    _field.title = title;
    _field.memo = "sys";
    _field.type = "DATE";
    _field.size = 10;
    _field.auto_increment = 0;
    _field.default_value = "0000-00-00";
    _field.required = 1;
    _field.filter = "DATE";
    _field.regexp = "";
    _field.input_by = "DATE";
    _field.input_hash = "";
    _field.position = 1;
    return _field;
}


/**
 * 基本日期时间
 */
App.dt.data.ccField_Time = function (time_key, title) {
    var _field = new MyField();
    var _now = App.su.datetime.getCurrentDateTime();
    _field.uuid = App.su.maths.uuid.create();
    _field.ctime = _now;
    _field.utime = _now;
    _field.is_global = 1;
    _field.name = time_key;
    _field.title = title;
    _field.memo = "sys";
    _field.type = "DATETIME";
    _field.size = 18;
    _field.auto_increment = 0;
    _field.default_value = "0000-00-00 00:00:00";
    _field.required = 1;
    _field.filter = "DATETIME";
    _field.regexp = "";
    _field.input_by = "DATETIME";
    _field.input_hash = "";
    _field.position = 1;
    return _field;
}

/**
 * 模型自定义主键
 * cadmin
 * uadmin
 */
App.dt.data.ccField_Uuid = function (key, title) {
    var _field = new MyField();
    var _now = App.su.datetime.getCurrentDateTime();
    _field.uuid = App.su.maths.uuid.create();
    _field.ctime = _now;
    _field.utime = _now;
    _field.is_global = 1;
    _field.name = key;
    _field.title = title;
    _field.memo = "sys";
    _field.type = "STRING";
    _field.size = 36;
    _field.auto_increment = 0;
    _field.default_value = "";
    _field.required = 1;
    _field.filter = "NO_FILTER";
    _field.regexp = "";
    _field.input_by = "DEFAULT";
    _field.input_hash = "";
    _field.position = 1;
    return _field;
}


/**
 * 模型名称
 * cadmin
 * uadmin
 */
App.dt.data.ccField_Name = function (key, title) {
    var _field = new MyField();
    var _now = App.su.datetime.getCurrentDateTime();
    _field.uuid = App.su.maths.uuid.create();
    _field.ctime = _now;
    _field.utime = _now;
    _field.is_global = 1;
    _field.name = key;
    _field.title = title;
    _field.memo = "sys";
    _field.type = "STRING";
    _field.size = 255;
    _field.auto_increment = 0;
    _field.default_value = "";
    _field.required = 1;
    _field.filter = "NO_FILTER";
    _field.regexp = "";
    _field.input_by = "DEFAULT";
    _field.input_hash = "";
    _field.position = 1;

    return _field;
}


/**
 * 模型名称
 * cadmin
 * uadmin
 */
App.dt.data.ccField_Text = function (key, title) {
    var _field = new MyField();
    var _now = App.su.datetime.getCurrentDateTime();
    _field.uuid = App.su.maths.uuid.create();
    _field.ctime = _now;
    _field.utime = _now;
    _field.is_global = 1;
    _field.name = key;
    _field.title = title;
    _field.memo = "sys";
    _field.type = "TEXT";
    _field.size = 1024;
    _field.auto_increment = 0;
    _field.default_value = "";
    _field.required = 1;
    _field.filter = "NO_FILTER";
    _field.regexp = "";
    _field.input_by = "DEFAULT";
    _field.input_hash = "";
    _field.position = 1;

    return _field;
}


/**
 * 一般图片字段
 * cadmin
 * uadmin
 */
App.dt.data.ccField_Img = function (key, title) {
    var _field = new MyField();
    var _now = App.su.datetime.getCurrentDateTime();
    _field.uuid = App.su.maths.uuid.create();
    _field.ctime = _now;
    _field.utime = _now;
    _field.is_global = 0;
    _field.name = key;
    _field.title = title;
    _field.memo = "sys";
    _field.type = "LONGBLOB";
    _field.size = 4096;
    _field.auto_increment = 0;
    _field.default_value = "";
    _field.required = 0;
    _field.filter = "NO_FILTER";
    _field.regexp = "";
    _field.input_by = "UPLOAD_IMAGE";
    _field.input_hash = "";
    _field.position = 1;

    return _field;
}


/**
 *  单一字段的配置
 */
App.dt.data.ccField_State = function (key, title) {
    var _field = new MyField();
    var _now = App.su.datetime.getCurrentDateTime();
    _field.uuid = App.su.maths.uuid.create();
    _field.ctime = _now;
    _field.utime = _now;
    _field.is_global = 1;
    _field.name = key;
    _field.title = title;
    _field.memo = "sys";
    _field.type = "CHAR";
    _field.size = 1;
    _field.auto_increment = 0;
    _field.default_value = "N";
    _field.required = 1;
    _field.filter = "NO_FILTER";
    _field.regexp = "";
    _field.input_by = "SELECT";
    _field.input_hash = "N,可用;D,禁用";
    _field.position = 1;

    return _field;
}


/**
 *  单一字段的配置
 */
App.dt.data.ccField_Int = function (key, title) {
    var _field = new MyField();
    var _now = App.su.datetime.getCurrentDateTime();
    _field.uuid = App.su.maths.uuid.create();
    _field.ctime = _now;
    _field.utime = _now;
    _field.is_global = 1;
    _field.name = key;
    _field.title = title;
    _field.memo = "sys";
    _field.type = "INT";
    _field.size = 11;
    _field.auto_increment = 0;
    _field.default_value = "0";
    _field.required = 1;
    _field.filter = "INT";
    _field.regexp = "";
    _field.input_by = "DEFAULT";
    _field.input_hash = "";
    _field.position = 1;

    return _field;
}


App.dt.data.ccGlobalFields = function (key) {
    var self = App.dt;
    var _currApp = self.project.getCurrApp();
    for (var ii in _currApp.field_list) {
        if (_currApp.field_list[ii].name == key) {
            return _currApp.field_list[ii];
        }
    }
    var _obj = undefined;
    switch (key) {

        case "id":
            _obj = self.data.ccField_ID();
            break;
        case "flag":
            _obj = self.data.ccField_State("flag", "数据状态");
            break;
        case "ctime":
            _obj = self.data.ccField_Time("ctime", "创建时间");
            break;
        case "utime":
            _obj = self.data.ccField_Time("utime", "更新时间");
            break;
        case "cadmin":
            _obj = self.data.ccField_Uuid("cadmin", "创建人");
            break;
        case "uadmin":
            _obj = self.data.ccField_Uuid("uadmin", "更新人");
            break;
        default:
            break;
    }
    if (undefined != _obj) {
        _currApp.field_list[_obj.uuid] = _obj;
        self.project.setCurrApp(_currApp);
        return _obj;
    }
    return new MyField();
}

/**
 * 导入基本管理员模型
 */
App.dt.data.ccModel_Group = function () {

    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选中应用")
        return;
    }

    var _modelGroup = new MyModel();
    var _uuidGroup = App.su.maths.uuid.create();
    var _now = App.su.datetime.getCurrentDateTime();
    _modelGroup.uuid = _uuidGroup;
    _modelGroup.ctime = _now;
    _modelGroup.utime = _now;
    _modelGroup.name = "group"
    _modelGroup.title = "人员分组";
    _modelGroup.memo = "auto";
    _modelGroup.primary_key = "group_id";
    _modelGroup.table_name = "group";
    _modelGroup.fa_icon = "users";
    _modelGroup.field_list = {};
    _modelGroup.idx_list = {};
    _modelGroup.fun_list = {};

    var _f_id = self.data.ccGlobalFields("id");
    var _f_group_id = self.data.ccField_Name("group_id", "分组ID");
    var _f_name = self.data.ccField_Name("name", "组名");
    var _f_memo = self.data.ccField_Text("memo", "备注");
    var _f_state = self.data.ccField_State("state", "状态");
    var _f_flag = self.data.ccGlobalFields("flag");
    var _f_ctime = self.data.ccGlobalFields("ctime");
    var _f_utime = self.data.ccGlobalFields("utime");
    var _f_cadmin = self.data.ccGlobalFields("cadmin");
    var _f_uadmin = self.data.ccGlobalFields("uadmin");


    _modelGroup.field_list[_f_id.uuid] = _f_id;
    _modelGroup.field_list[_f_group_id.uuid] = _f_group_id;
    _modelGroup.field_list[_f_name.uuid] = _f_name;
    _modelGroup.field_list[_f_memo.uuid] = _f_memo;
    _modelGroup.field_list[_f_state.uuid] = _f_state;
    _modelGroup.field_list[_f_flag.uuid] = _f_flag;
    _modelGroup.field_list[_f_ctime.uuid] = _f_ctime;
    _modelGroup.field_list[_f_utime.uuid] = _f_utime;
    _modelGroup.field_list[_f_cadmin.uuid] = _f_cadmin;
    _modelGroup.field_list[_f_uadmin.uuid] = _f_uadmin;

    var _idx_u = new MyIndex();
    _idx_u.uuid = App.su.maths.uuid.create();
    _idx_u.ctime = _now;
    _idx_u.utime = _now;
    _idx_u.name = "udx_group_id";
    _idx_u.memo = "";
    _idx_u.type = "UNIQUE";
    _idx_u.field_list = {};
    _idx_u.field_list[_f_group_id.uuid] = deepCopy(_f_group_id);

    var _idx_i = new MyIndex();
    _idx_i.uuid = App.su.maths.uuid.create();
    _idx_i.ctime = _now;
    _idx_i.utime = _now;
    _idx_i.name = "idx_group_1";
    _idx_i.memo = "";
    _idx_i.type = "KEY";
    _idx_i.field_list = {};
    _idx_i.field_list[_f_state.uuid] = deepCopy(_f_state);
    _idx_i.field_list[_f_flag.uuid] = deepCopy(_f_flag);

    _modelGroup.idx_list[_idx_u.uuid] = _idx_u;
    _modelGroup.idx_list[_idx_i.uuid] = _idx_i;

    var _funAdd = new MyFun();
    _funAdd.uuid = App.su.maths.uuid.create();
    _funAdd.ctime = _now;
    _funAdd.utime = _now;
    _funAdd.name = "default";
    _funAdd.title = "默认插入方法";
    _funAdd.memo = "";
    _funAdd.type = "ADD";
    _funAdd.all_field = 1;
    _funAdd.group_by = "";
    _funAdd.group_field = "";
    _funAdd.order_enable = 0;
    _funAdd.order_by = "";
    _funAdd.order_dir = "";
    _funAdd.pager_enable = 0;
    _funAdd.pager_size = 0;
    _funAdd.field_list = {};
    _funAdd.where = null;

    _modelGroup.fun_list[_funAdd.uuid] = _funAdd;

    var _pkWhere = new MyWhere();
    _pkWhere.uuid = App.su.maths.uuid.create();
    _pkWhere.ctime = _now;
    _pkWhere.utime = _now;

    var _pkCond = new MyCond();
    _pkCond.uuid = App.su.maths.uuid.create();
    _pkCond.ctime = _now;
    _pkCond.utime = _now;
    _pkCond.type = "EQ";
    _pkCond.field = _f_group_id.uuid;
    _pkCond.v1 = "";
    _pkCond.v2 = "";
    _pkCond.v1_type = "INPUT";
    _pkCond.v2_type = "";

    _pkWhere.cond_list = {};
    _pkWhere.cond_list[_pkCond.uuid] = _pkCond;

    //fun del
    var _funDelete = new MyFun();
    _funDelete.uuid = App.su.maths.uuid.create();
    _funDelete.ctime = _now;
    _funDelete.utime = _now;
    _funDelete.name = "default";
    _funDelete.title = "默认删除方法";
    _funDelete.memo = "";
    _funDelete.type = "DELETE";
    _funDelete.all_field = 0;
    _funDelete.group_by = "";
    _funDelete.group_field = "";
    _funDelete.order_enable = 0;
    _funDelete.order_by = "";
    _funDelete.order_dir = "";
    _funDelete.pager_enable = 0;
    _funDelete.pager_size = 0;
    _funDelete.field_list = [];
    _funDelete.where = deepCopy(_pkWhere);
    _modelGroup.fun_list[_funDelete.uuid] = _funDelete;

    var _funUpdate = new MyFun();
    _funUpdate.uuid = App.su.maths.uuid.create();
    _funUpdate.ctime = _now;
    _funUpdate.utime = _now;
    _funUpdate.name = "default";
    _funUpdate.title = "默认更新方法";
    _funUpdate.memo = "";
    _funUpdate.type = "UPDATE";
    _funUpdate.all_field = 0;//不存在全部更新
    _funUpdate.group_by = "";
    _funUpdate.group_field = "";
    _funUpdate.order_enable = 0;
    _funUpdate.order_by = "";
    _funUpdate.order_dir = "";
    _funUpdate.pager_enable = 0;
    _funUpdate.pager_size = 0;
    _funUpdate.field_list = {};

    _funUpdate.field_list[_f_name.uuid] = deepCopy(_f_name);
    _funUpdate.field_list[_f_memo.uuid] = deepCopy(_f_memo);

    _funUpdate.where = deepCopy(_pkWhere);
    _modelGroup.fun_list[_funUpdate.uuid] = _funUpdate;


    var _funUpdate2 = new MyFun();
    _funUpdate2.uuid = App.su.maths.uuid.create();
    _funUpdate2.ctime = _now;
    _funUpdate2.utime = _now;
    _funUpdate2.name = "ForDrop";
    _funUpdate2.title = "逻辑删除";
    _funUpdate2.memo = "";
    _funUpdate2.type = "UPDATE";
    _funUpdate2.all_field = 0;//不存在全部更新
    _funUpdate2.group_by = "";
    _funUpdate2.group_field = "";
    _funUpdate2.order_enable = 0;
    _funUpdate2.order_by = "";
    _funUpdate2.order_dir = "";
    _funUpdate2.pager_enable = 0;
    _funUpdate2.pager_size = 0;
    _funUpdate2.field_list = {};
    _funUpdate2.field_list[_f_flag.uuid] = deepCopy(_f_flag);
    _funUpdate2.where = deepCopy(_pkWhere);
    _modelGroup.fun_list[_funUpdate2.uuid] = _funUpdate2;

    var _funUpdate4 = new MyFun();
    _funUpdate4.uuid = App.su.maths.uuid.create();
    _funUpdate4.ctime = _now;
    _funUpdate4.utime = _now;
    _funUpdate4.name = "State";
    _funUpdate4.title = "启用或者停用";
    _funUpdate4.memo = "";
    _funUpdate4.type = "UPDATE";
    _funUpdate4.all_field = 0;//不存在全部更新
    _funUpdate4.group_by = "";
    _funUpdate4.group_field = "";
    _funUpdate4.order_enable = 0;
    _funUpdate4.order_by = "";
    _funUpdate4.order_dir = "";
    _funUpdate4.pager_enable = 0;
    _funUpdate4.pager_size = 0;
    _funUpdate4.field_list = {};
    _funUpdate4.field_list[_f_state.uuid] = deepCopy(_f_state);
    _funUpdate4.where = deepCopy(_pkWhere);
    _modelGroup.fun_list[_funUpdate4.uuid] = _funUpdate4;

    //2个查询个体方法
    var _funFetch = new MyFun();
    _funFetch.uuid = App.su.maths.uuid.create();
    _funFetch.ctime = _now;
    _funFetch.utime = _now;
    _funFetch.name = "default";
    _funFetch.title = "默认查询个体方法";
    _funFetch.memo = "";
    _funFetch.type = "FETCH";
    _funFetch.all_field = 1;//不存在全部更新
    _funFetch.group_by = "";
    _funFetch.group_field = "";
    _funFetch.order_enable = 0;
    _funFetch.order_by = "";
    _funFetch.order_dir = "";
    _funFetch.pager_enable = 0;
    _funFetch.pager_size = 0;
    _funFetch.field_list = "";
    _funFetch.where = deepCopy(_pkWhere);
    _modelGroup.fun_list[_funFetch.uuid] = _funFetch;


    //通用列表查询
    var _funList = new MyFun();
    _funList.uuid = App.su.maths.uuid.create();
    _funList.ctime = _now;
    _funList.utime = _now;
    _funList.name = "default";
    _funList.title = "通用列表查询";
    _funList.memo = "";
    _funList.type = "LIST";

    _funList.group_by = "";
    _funList.group_field = "";
    _funList.order_enable = 1;
    _funList.order_by = _f_group_id.uuid;
    _funList.order_dir = "ASC";
    _funList.pager_enable = 0;
    _funList.pager_size = 0;
    _funList.all_field = 1;//不存在全部更新
    _funList.field_list = [];

    var _listWhere = new MyWhere();
    _listWhere.uuid = App.su.maths.uuid.create();
    _listWhere.ctime = _now;
    _listWhere.utime = _now;
    _listWhere.cond_list = {};


    var _stateCond = new MyCond();
    _stateCond.uuid = App.su.maths.uuid.create();
    _stateCond.ctime = _now;
    _stateCond.utime = _now;
    _stateCond.type = "EQ";
    _stateCond.field = _f_state.uuid;
    _stateCond.v1 = "";
    _stateCond.v2 = "";
    _stateCond.v1_type = "INPUT";
    _stateCond.v2_type = "";
    //_listWhere.cond_list[_stateCond.uuid] = _stateCond;

    _listWhere.cond_list = {};
    _listWhere.cond_list[_stateCond.uuid] = _stateCond;

    _funList.where = _listWhere;
    _modelGroup.fun_list[_funList.uuid] = _funList;

    _curr_app.model_list[_uuidGroup] = _modelGroup;
    //_curr_app.model_list[_uuidtype] = _modeltype;

    if (self.project.setCurrApp(_curr_app)) {
        self.succ("暂存成功");
    } else {
        self.fail("暂存失败");
    }
    //加载全局字段
    self.project.fieldLoad();
    //加载模型
    self.project.modelLoad();

}


/**
 * 导入基本人员
 */
App.dt.data.ccModel_User = function () {

    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选中应用")
        return;
    }

    var _modelUser = new MyModel();
    var _uuidUser = App.su.maths.uuid.create();
    var _now = App.su.datetime.getCurrentDateTime();
    _modelUser.uuid = _uuidUser;
    _modelUser.ctime = _now;
    _modelUser.utime = _now;
    _modelUser.name = "user"
    _modelUser.title = "人员";
    _modelUser.memo = "auto";
    _modelUser.primary_key = "user_id";
    _modelUser.table_name = "user";
    _modelUser.fa_icon = "user";
    _modelUser.field_list = {};
    _modelUser.idx_list = {};
    _modelUser.fun_list = {};
    var _f_id = self.data.ccGlobalFields("id");
    var _f_user_id = self.data.ccField_Uuid("user_id", "人员ID");
    var _f_group_id = self.data.ccField_Name("group_id", "分组ID");
    var _f_name = self.data.ccField_Name("name", "姓名");
    var _f_gender = self.data.ccField_Name("gender", "性别");
    var _f_type = self.data.ccField_Name("type", "类型");
    var _f_memo = self.data.ccField_Text("memo", "备注");
    var _f_state = self.data.ccField_State("state", "状态");
    var _f_flag = self.data.ccGlobalFields("flag");
    var _f_ctime = self.data.ccGlobalFields("ctime");
    var _f_utime = self.data.ccGlobalFields("utime");
    var _f_cadmin = self.data.ccGlobalFields("cadmin");
    var _f_uadmin = self.data.ccGlobalFields("uadmin");

    var _f_photo = self.data.ccField_Img("photo", "头像");

    _f_type.input_hash = "C00,A类;C01,B类";

    _modelUser.field_list[_f_id.uuid] = _f_id;
    _modelUser.field_list[_f_user_id.uuid] = _f_user_id;
    _modelUser.field_list[_f_group_id.uuid] = _f_group_id;
    _modelUser.field_list[_f_type.uuid] = _f_type;
    _modelUser.field_list[_f_name.uuid] = _f_name;
    _modelUser.field_list[_f_gender.uuid] = _f_gender;
    _modelUser.field_list[_f_memo.uuid] = _f_memo;
    _modelUser.field_list[_f_photo.uuid] = _f_photo;
    _modelUser.field_list[_f_state.uuid] = _f_state;
    _modelUser.field_list[_f_flag.uuid] = _f_flag;
    _modelUser.field_list[_f_ctime.uuid] = _f_ctime;
    _modelUser.field_list[_f_utime.uuid] = _f_utime;
    _modelUser.field_list[_f_cadmin.uuid] = _f_cadmin;
    _modelUser.field_list[_f_uadmin.uuid] = _f_uadmin;

    var _idx_u = new MyIndex();
    _idx_u.uuid = App.su.maths.uuid.create();
    _idx_u.ctime = _now;
    _idx_u.utime = _now;
    _idx_u.name = "udx_user_id";
    _idx_u.memo = "";
    _idx_u.type = "UNIQUE";
    _idx_u.field_list = {};
    _idx_u.field_list[_f_user_id.uuid] = deepCopy(_f_user_id);

    var _idx_i = new MyIndex();
    _idx_i.uuid = App.su.maths.uuid.create();
    _idx_i.ctime = _now;
    _idx_i.utime = _now;
    _idx_i.name = "idx_user_1";
    _idx_i.memo = "";
    _idx_i.type = "KEY";
    _idx_i.field_list = {};
    _idx_i.field_list[_f_group_id.uuid] = deepCopy(_f_group_id);
    _idx_i.field_list[_f_type.uuid] = deepCopy(_f_type);
    _idx_i.field_list[_f_gender.uuid] = deepCopy(_f_gender);
    _idx_i.field_list[_f_state.uuid] = deepCopy(_f_state);
    _idx_i.field_list[_f_flag.uuid] = deepCopy(_f_flag);

    _modelUser.idx_list[_idx_u.uuid] = _idx_u;
    _modelUser.idx_list[_idx_i.uuid] = _idx_i;

    var _funAdd = new MyFun();
    _funAdd.uuid = App.su.maths.uuid.create();
    _funAdd.ctime = _now;
    _funAdd.utime = _now;
    _funAdd.name = "default";
    _funAdd.title = "默认插入方法";
    _funAdd.memo = "";
    _funAdd.type = "ADD";
    _funAdd.all_field = 1;
    _funAdd.group_by = "";
    _funAdd.group_field = "";
    _funAdd.order_enable = 0;
    _funAdd.order_by = "";
    _funAdd.order_dir = "";
    _funAdd.pager_enable = 0;
    _funAdd.pager_size = 0;
    _funAdd.field_list = {};
    _funAdd.where = null;

    _modelUser.fun_list[_funAdd.uuid] = _funAdd;

    var _pkWhere = new MyWhere();
    _pkWhere.uuid = App.su.maths.uuid.create();
    _pkWhere.ctime = _now;
    _pkWhere.utime = _now;

    var _pkCond = new MyCond();
    _pkCond.uuid = App.su.maths.uuid.create();
    _pkCond.ctime = _now;
    _pkCond.utime = _now;
    _pkCond.type = "EQ";
    _pkCond.field = _f_user_id.uuid;
    _pkCond.v1 = "";
    _pkCond.v2 = "";
    _pkCond.v1_type = "INPUT";
    _pkCond.v2_type = "";

    _pkWhere.cond_list = {};
    _pkWhere.cond_list[_pkCond.uuid] = _pkCond;

    //fun del
    var _funDelete = new MyFun();
    _funDelete.uuid = App.su.maths.uuid.create();
    _funDelete.ctime = _now;
    _funDelete.utime = _now;
    _funDelete.name = "default";
    _funDelete.title = "默认删除方法";
    _funDelete.memo = "";
    _funDelete.type = "DELETE";
    _funDelete.all_field = 0;
    _funDelete.group_by = "";
    _funDelete.group_field = "";
    _funDelete.order_enable = 0;
    _funDelete.order_by = "";
    _funDelete.order_dir = "";
    _funDelete.pager_enable = 0;
    _funDelete.pager_size = 0;
    _funDelete.limit = 1;
    _funDelete.field_list = [];
    _funDelete.where = deepCopy(_pkWhere);
    _modelUser.fun_list[_funDelete.uuid] = _funDelete;

    var _funUpdate = new MyFun();
    _funUpdate.uuid = App.su.maths.uuid.create();
    _funUpdate.ctime = _now;
    _funUpdate.utime = _now;
    _funUpdate.name = "default";
    _funUpdate.title = "默认更新方法";
    _funUpdate.memo = "";
    _funUpdate.type = "UPDATE";
    _funUpdate.all_field = 0;//不存在全部更新
    _funUpdate.group_by = "";
    _funUpdate.group_field = "";
    _funUpdate.order_enable = 0;
    _funUpdate.order_by = "";
    _funUpdate.order_dir = "";
    _funUpdate.pager_enable = 0;
    _funUpdate.pager_size = 0;
    _funDelete.limit = 1;
    _funUpdate.field_list = {};


    _funUpdate.field_list[_f_group_id.uuid] = deepCopy(_f_group_id);
    _funUpdate.field_list[_f_type.uuid] = deepCopy(_f_type);
    _funUpdate.field_list[_f_name.uuid] = deepCopy(_f_name);
    _funUpdate.field_list[_f_gender.uuid] = deepCopy(_f_gender);
    _funUpdate.field_list[_f_memo.uuid] = deepCopy(_f_memo);

    _funUpdate.where = deepCopy(_pkWhere);
    _modelUser.fun_list[_funUpdate.uuid] = _funUpdate;

    var _funUpdate1 = new MyFun();
    _funUpdate1.uuid = App.su.maths.uuid.create();
    _funUpdate1.ctime = _now;
    _funUpdate1.utime = _now;
    _funUpdate1.name = "Photo";
    _funUpdate1.title = "修改头像";
    _funUpdate1.memo = "";
    _funUpdate1.type = "UPDATE";
    _funUpdate1.all_field = 0;//不存在全部更新
    _funUpdate1.group_by = "";
    _funUpdate1.group_field = "";
    _funUpdate1.order_enable = 0;
    _funUpdate1.order_by = "";
    _funUpdate1.order_dir = "";
    _funUpdate1.pager_enable = 0;
    _funUpdate1.pager_size = 0;
    _funUpdate1.field_list = {};
    _funUpdate1.field_list[_f_photo.uuid] = deepCopy(_f_photo);
    _funUpdate1.where = deepCopy(_pkWhere);
    _modelUser.fun_list[_funUpdate1.uuid] = _funUpdate1;

    var _funUpdate2 = new MyFun();
    _funUpdate2.uuid = App.su.maths.uuid.create();
    _funUpdate2.ctime = _now;
    _funUpdate2.utime = _now;
    _funUpdate2.name = "ForDrop";
    _funUpdate2.title = "逻辑删除";
    _funUpdate2.memo = "";
    _funUpdate2.type = "UPDATE";
    _funUpdate2.all_field = 0;//不存在全部更新
    _funUpdate2.group_by = "";
    _funUpdate2.group_field = "";
    _funUpdate2.order_enable = 0;
    _funUpdate2.order_by = "";
    _funUpdate2.order_dir = "";
    _funUpdate2.pager_enable = 0;
    _funUpdate2.pager_size = 0;
    _funUpdate2.field_list = {};
    _funUpdate2.field_list[_f_flag.uuid] = deepCopy(_f_flag);
    _funUpdate2.where = deepCopy(_pkWhere);
    _modelUser.fun_list[_funUpdate2.uuid] = _funUpdate2;

    var _funUpdate4 = new MyFun();
    _funUpdate4.uuid = App.su.maths.uuid.create();
    _funUpdate4.ctime = _now;
    _funUpdate4.utime = _now;
    _funUpdate4.name = "State";
    _funUpdate4.title = "启用或者停用";
    _funUpdate4.memo = "";
    _funUpdate4.type = "UPDATE";
    _funUpdate4.all_field = 0;//不存在全部更新
    _funUpdate4.group_by = "";
    _funUpdate4.group_field = "";
    _funUpdate4.order_enable = 0;
    _funUpdate4.order_by = "";
    _funUpdate4.order_dir = "";
    _funUpdate4.pager_enable = 0;
    _funUpdate4.pager_size = 0;
    _funUpdate4.field_list = {};
    _funUpdate4.field_list[_f_state.uuid] = deepCopy(_f_state);
    _funUpdate4.where = deepCopy(_pkWhere);
    _modelUser.fun_list[_funUpdate4.uuid] = _funUpdate4;

    //2个查询个体方法
    var _funFetch = new MyFun();
    _funFetch.uuid = App.su.maths.uuid.create();
    _funFetch.ctime = _now;
    _funFetch.utime = _now;
    _funFetch.name = "default";
    _funFetch.title = "默认查询个体方法";
    _funFetch.memo = "";
    _funFetch.type = "FETCH";
    _funFetch.all_field = 1;//不存在全部更新
    _funFetch.group_by = "";
    _funFetch.group_field = "";
    _funFetch.order_enable = 0;
    _funFetch.order_by = "";
    _funFetch.order_dir = "";
    _funFetch.pager_enable = 0;
    _funFetch.pager_size = 0;
    _funFetch.field_list = "";
    _funFetch.where = deepCopy(_pkWhere);
    _modelUser.fun_list[_funFetch.uuid] = _funFetch;


    //通用列表查询
    var _funList = new MyFun();
    _funList.uuid = App.su.maths.uuid.create();
    _funList.ctime = _now;
    _funList.utime = _now;
    _funList.name = "default";
    _funList.title = "通用列表查询";
    _funList.memo = "";
    _funList.type = "LIST";

    _funList.group_by = "";
    _funList.group_field = "";
    _funList.order_enable = 1;
    _funList.order_by = _f_group_id.uuid;
    _funList.order_dir = "ASC";
    _funList.pager_enable = 1;
    _funList.pager_size = 20;
    _funList.all_field = 1;//不存在全部更新
    _funList.field_list = [];

    var _listWhere = new MyWhere();
    _listWhere.uuid = App.su.maths.uuid.create();
    _listWhere.ctime = _now;
    _listWhere.utime = _now;
    _listWhere.cond_list = {};


    var _stateCond = new MyCond();
    _stateCond.uuid = App.su.maths.uuid.create();
    _stateCond.ctime = _now;
    _stateCond.utime = _now;
    _stateCond.type = "EQ";
    _stateCond.field = _f_state.uuid;
    _stateCond.v1 = "";
    _stateCond.v2 = "";
    _stateCond.v1_type = "INPUT";
    _stateCond.v2_type = "";
    //_listWhere.cond_list[_stateCond.uuid] = _stateCond;


    var _kwCond = new MyCond();
    _kwCond.uuid = App.su.maths.uuid.create();
    _kwCond.ctime = _now;
    _kwCond.utime = _now;
    _kwCond.type = "KW";
    _kwCond.title = "关键字1";
    _kwCond.field = _f_name.uuid;
    _kwCond.v1 = "";
    _kwCond.v2 = "";
    _kwCond.v1_type = "INPUT";
    _kwCond.v2_type = "";

    var _gidCond = new MyCond();
    _gidCond.uuid = App.su.maths.uuid.create();
    _gidCond.ctime = _now;
    _gidCond.utime = _now;
    _gidCond.title = "分组";
    _gidCond.type = "EQ";
    _gidCond.field = _f_group_id.uuid;
    _gidCond.v1 = "";
    _gidCond.v2 = "";
    _gidCond.v1_type = "INPUT";
    _gidCond.v2_type = "";

    var _genderCond = new MyCond();
    _genderCond.uuid = App.su.maths.uuid.create();
    _genderCond.ctime = _now;
    _genderCond.utime = _now;
    _genderCond.title = "性别";
    _genderCond.type = "EQ";
    _genderCond.field = _f_gender.uuid;
    _genderCond.v1 = "";
    _genderCond.v2 = "";
    _genderCond.v1_type = "INPUT";
    _genderCond.v2_type = "";

    var _typeCond = new MyCond();
    _typeCond.uuid = App.su.maths.uuid.create();
    _typeCond.ctime = _now;
    _typeCond.utime = _now;
    _typeCond.title = "类别";
    _typeCond.type = "EQ";
    _typeCond.field = _f_type.uuid;
    _typeCond.v1 = "";
    _typeCond.v2 = "";
    _typeCond.v1_type = "INPUT";
    _typeCond.v2_type = "";


    var _timeCond = new MyCond();
    _timeCond.uuid = App.su.maths.uuid.create();
    _timeCond.ctime = _now;
    _timeCond.utime = _now;
    _timeCond.title = "时间范围";
    _timeCond.type = "TIME";//== between
    _timeCond.field = _f_ctime.uuid;
    _timeCond.v1 = "";
    _timeCond.v2 = "";
    _timeCond.v1_type = "INPUT";
    _timeCond.v2_type = "INPUT";

    _listWhere.cond_list = {};
    _listWhere.cond_list[_gidCond.uuid] = _gidCond;
    _listWhere.cond_list[_typeCond.uuid] = _typeCond;
    _listWhere.cond_list[_genderCond.uuid] = _genderCond;
    _listWhere.cond_list[_stateCond.uuid] = _stateCond;
    _listWhere.cond_list[_kwCond.uuid] = _kwCond;
    _listWhere.cond_list[_timeCond.uuid] = _timeCond;

    _funList.where = _listWhere;
    _modelUser.fun_list[_funList.uuid] = _funList;


    _curr_app.model_list[_uuidUser] = _modelUser;
    //_curr_app.model_list[_uuidtype] = _modeltype;

    if (self.project.setCurrApp(_curr_app)) {
        self.succ("暂存成功");
    } else {
        self.fail("暂存失败");
    }
    //加载全局字段
    self.project.fieldLoad();
    //加载模型
    self.project.modelLoad();

}

/**
 * 导入基本管理员模型
 */
App.dt.data.ccModel_Admin = function () {

    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选中应用")
        return;
    }

    var _modelAdmin = new MyModel();
    var _modelRole = new MyModel();
    var _uuidAdmin = App.su.maths.uuid.create();
    var _uuidRole = App.su.maths.uuid.create();
    var _now = App.su.datetime.getCurrentDateTime();
    _modelAdmin.uuid = _uuidAdmin;
    _modelAdmin.ctime = _now;
    _modelAdmin.utime = _now;
    _modelAdmin.name = "admin"
    _modelAdmin.title = "管理员";
    _modelAdmin.memo = "auto";
    _modelAdmin.primary_key = "admin_id";
    _modelAdmin.table_name = "admin";
    _modelAdmin.fa_icon = "address-card";
    _modelAdmin.field_list = {};
    _modelAdmin.idx_list = {};
    _modelAdmin.fun_list = {};
    var _f_id = self.data.ccGlobalFields("id");
    var _f_admin_id = self.data.ccField_Uuid("admin_id", "登录名");
    var _f_account = self.data.ccField_Name("account", "登录名");
    var _f_passwd = self.data.ccField_Name("passwd", "加密密码");
    var _f_role = self.data.ccField_Name("role", "权限");
    var _f_name = self.data.ccField_Name("name", "姓名");
    var _f_memo = self.data.ccField_Text("memo", "备注");
    var _f_state = self.data.ccField_State("state", "业务状态");
    var _f_flag = self.data.ccGlobalFields("flag");
    var _f_ctime = self.data.ccGlobalFields("ctime");
    var _f_utime = self.data.ccGlobalFields("utime");
    var _f_cadmin = self.data.ccGlobalFields("cadmin");
    var _f_uadmin = self.data.ccGlobalFields("uadmin");


    _f_role.input_hash = "R00,超级管理员;R01,一般操作员";

    _modelAdmin.field_list[_f_id.uuid] = _f_id;
    _modelAdmin.field_list[_f_admin_id.uuid] = _f_admin_id;
    _modelAdmin.field_list[_f_account.uuid] = _f_account;
    _modelAdmin.field_list[_f_passwd.uuid] = _f_passwd;
    _modelAdmin.field_list[_f_role.uuid] = _f_role;
    _modelAdmin.field_list[_f_name.uuid] = _f_name;
    _modelAdmin.field_list[_f_memo.uuid] = _f_memo;
    _modelAdmin.field_list[_f_state.uuid] = _f_state;
    _modelAdmin.field_list[_f_flag.uuid] = _f_flag;
    _modelAdmin.field_list[_f_ctime.uuid] = _f_ctime;
    _modelAdmin.field_list[_f_utime.uuid] = _f_utime;
    _modelAdmin.field_list[_f_cadmin.uuid] = _f_cadmin;
    _modelAdmin.field_list[_f_uadmin.uuid] = _f_uadmin;

    var _idx_u = new MyIndex();
    _idx_u.uuid = App.su.maths.uuid.create();
    _idx_u.ctime = _now;
    _idx_u.utime = _now;
    _idx_u.name = "udx_admin_id";
    _idx_u.memo = "";
    _idx_u.type = "UNIQUE";
    _idx_u.field_list = {};
    _idx_u.field_list[_f_admin_id.uuid] = deepCopy(_f_admin_id);

    var _idx_i = new MyIndex();
    _idx_i.uuid = App.su.maths.uuid.create();
    _idx_i.ctime = _now;
    _idx_i.utime = _now;
    _idx_i.name = "idx_account_name";
    _idx_i.memo = "";
    _idx_i.type = "KEY";
    _idx_i.field_list = {};
    _idx_i.field_list[_f_account.uuid] = deepCopy(_f_account);
    _idx_i.field_list[_f_name.uuid] = deepCopy(_f_name);

    _modelAdmin.idx_list[_idx_u.uuid] = _idx_u;
    _modelAdmin.idx_list[_idx_i.uuid] = _idx_i;

    var _funAdd = new MyFun();
    _funAdd.uuid = App.su.maths.uuid.create();
    _funAdd.ctime = _now;
    _funAdd.utime = _now;
    _funAdd.name = "default";
    _funAdd.title = "默认插入方法";
    _funAdd.memo = "";
    _funAdd.type = "ADD";
    _funAdd.all_field = 1;
    _funAdd.group_by = "";
    _funAdd.group_field = "";
    _funAdd.order_enable = 0;
    _funAdd.order_by = "";
    _funAdd.order_dir = "";
    _funAdd.pager_enable = 0;
    _funAdd.pager_size = 0;
    _funAdd.field_list = {};
    _funAdd.where = null;

    _modelAdmin.fun_list[_funAdd.uuid] = _funAdd;

    var _pkWhere = new MyWhere();
    _pkWhere.uuid = App.su.maths.uuid.create();
    _pkWhere.ctime = _now;
    _pkWhere.utime = _now;

    var _pkCond = new MyCond();
    _pkCond.uuid = App.su.maths.uuid.create();
    _pkCond.ctime = _now;
    _pkCond.utime = _now;
    _pkCond.type = "EQ";
    _pkCond.field = _f_admin_id.uuid;
    _pkCond.v1 = "";
    _pkCond.v2 = "";
    _pkCond.v1_type = "INPUT";
    _pkCond.v2_type = "";

    _pkWhere.cond_list = {};
    _pkWhere.cond_list[_pkCond.uuid] = _pkCond;

    //fun del
    var _funDelete = new MyFun();
    _funDelete.uuid = App.su.maths.uuid.create();
    _funDelete.ctime = _now;
    _funDelete.utime = _now;
    _funDelete.name = "default";
    _funDelete.title = "默认删除方法";
    _funDelete.memo = "";
    _funDelete.type = "DELETE";
    _funDelete.all_field = 0;
    _funDelete.group_by = "";
    _funDelete.group_field = "";
    _funDelete.order_enable = 0;
    _funDelete.order_by = "";
    _funDelete.order_dir = "";
    _funDelete.pager_enable = 0;
    _funDelete.pager_size = 0;
    _funDelete.field_list = [];
    _funDelete.where = deepCopy(_pkWhere);
    _modelAdmin.fun_list[_funDelete.uuid] = _funDelete;

    var _funUpdate = new MyFun();
    _funUpdate.uuid = App.su.maths.uuid.create();
    _funUpdate.ctime = _now;
    _funUpdate.utime = _now;
    _funUpdate.name = "default";
    _funUpdate.title = "默认更新方法";
    _funUpdate.memo = "";
    _funUpdate.type = "UPDATE";
    _funUpdate.all_field = 0;//不存在全部更新
    _funUpdate.group_by = "";
    _funUpdate.group_field = "";
    _funUpdate.order_enable = 0;
    _funUpdate.order_by = "";
    _funUpdate.order_dir = "";
    _funUpdate.pager_enable = 0;
    _funUpdate.pager_size = 0;
    _funUpdate.field_list = {};
    _funUpdate.field_list[_f_name.uuid] = deepCopy(_f_name);
    _funUpdate.field_list[_f_memo.uuid] = deepCopy(_f_memo);

    _funUpdate.where = deepCopy(_pkWhere);
    _modelAdmin.fun_list[_funUpdate.uuid] = _funUpdate;


    var _funUpdate1 = new MyFun();
    _funUpdate1.uuid = App.su.maths.uuid.create();
    _funUpdate1.ctime = _now;
    _funUpdate1.utime = _now;
    _funUpdate1.name = "Password";
    _funUpdate1.title = "修改密码";
    _funUpdate1.memo = "";
    _funUpdate1.type = "UPDATE";
    _funUpdate1.all_field = 0;//不存在全部更新
    _funUpdate1.group_by = "";
    _funUpdate1.group_field = "";
    _funUpdate1.order_enable = 0;
    _funUpdate1.order_by = "";
    _funUpdate1.order_dir = "";
    _funUpdate1.pager_enable = 0;
    _funUpdate1.pager_size = 0;
    _funUpdate1.field_list = {};
    _funUpdate1.field_list[_f_name.uuid] = deepCopy(_f_passwd);
    _funUpdate1.where = deepCopy(_pkWhere);
    _modelAdmin.fun_list[_funUpdate1.uuid] = _funUpdate1;

    var _funUpdate2 = new MyFun();
    _funUpdate2.uuid = App.su.maths.uuid.create();
    _funUpdate2.ctime = _now;
    _funUpdate2.utime = _now;
    _funUpdate2.name = "ForDrop";
    _funUpdate2.title = "逻辑删除";
    _funUpdate2.memo = "";
    _funUpdate2.type = "UPDATE";
    _funUpdate2.all_field = 0;//不存在全部更新
    _funUpdate2.group_by = "";
    _funUpdate2.group_field = "";
    _funUpdate2.order_enable = 0;
    _funUpdate2.order_by = "";
    _funUpdate2.order_dir = "";
    _funUpdate2.pager_enable = 0;
    _funUpdate2.pager_size = 0;
    _funUpdate2.field_list = {};
    _funUpdate2.field_list[_f_flag.uuid] = deepCopy(_f_flag);
    _funUpdate2.where = deepCopy(_pkWhere);
    _modelAdmin.fun_list[_funUpdate2.uuid] = _funUpdate2;

    var _funUpdate4 = new MyFun();
    _funUpdate4.uuid = App.su.maths.uuid.create();
    _funUpdate4.ctime = _now;
    _funUpdate4.utime = _now;
    _funUpdate4.name = "State";
    _funUpdate4.title = "启用或者停用";
    _funUpdate4.memo = "";
    _funUpdate4.type = "UPDATE";
    _funUpdate4.all_field = 0;//不存在全部更新
    _funUpdate4.group_by = "";
    _funUpdate4.group_field = "";
    _funUpdate4.order_enable = 0;
    _funUpdate4.order_by = "";
    _funUpdate4.order_dir = "";
    _funUpdate4.pager_enable = 0;
    _funUpdate4.pager_size = 0;
    _funUpdate4.field_list = {};
    _funUpdate4.field_list[_f_state.uuid] = deepCopy(_f_state);
    _funUpdate4.where = deepCopy(_pkWhere);
    _modelAdmin.fun_list[_funUpdate4.uuid] = _funUpdate4;

    //2个查询个体方法
    var _funFetch = new MyFun();
    _funFetch.uuid = App.su.maths.uuid.create();
    _funFetch.ctime = _now;
    _funFetch.utime = _now;
    _funFetch.name = "default";
    _funFetch.title = "默认查询个体方法";
    _funFetch.memo = "";
    _funFetch.type = "FETCH";
    _funFetch.all_field = 1;//不存在全部更新
    _funFetch.group_by = "";
    _funFetch.group_field = "";
    _funFetch.order_enable = 0;
    _funFetch.order_by = "";
    _funFetch.order_dir = "";
    _funFetch.pager_enable = 0;
    _funFetch.pager_size = 0;
    _funFetch.field_list = "";
    _funFetch.where = deepCopy(_pkWhere);
    _modelAdmin.fun_list[_funFetch.uuid] = _funFetch;

    var _funFetch1 = new MyFun();
    _funFetch1.uuid = App.su.maths.uuid.create();
    _funFetch1.ctime = _now;
    _funFetch1.utime = _now;
    _funFetch1.name = "ForLogin";
    _funFetch1.title = "登录查询";
    _funFetch1.memo = "";
    _funFetch1.type = "FETCH";
    _funFetch1.all_field = 1;//不存在全部更新
    _funFetch1.group_by = "";
    _funFetch1.group_field = "";
    _funFetch1.order_enable = 0;
    _funFetch1.order_by = "";
    _funFetch1.order_dir = "";
    _funFetch1.pager_enable = 0;
    _funFetch1.pager_size = 0;
    _funFetch1.field_list = [];

    var _pwWhere = new MyWhere();
    _pwWhere.uuid = App.su.maths.uuid.create();
    _pwWhere.ctime = _now;
    _pwWhere.utime = _now;

    var _pwCondAcc = new MyCond();
    _pwCondAcc.uuid = App.su.maths.uuid.create();
    _pwCondAcc.ctime = _now;
    _pwCondAcc.utime = _now;
    _pwCondAcc.type = "EQ";
    _pwCondAcc.field = _f_account.uuid;
    _pwCondAcc.v1 = "";
    _pwCondAcc.v2 = "";
    _pwCondAcc.v1_type = "INPUT";
    _pwCondAcc.v2_type = "";

    var _pwCondPwd = new MyCond();
    _pwCondPwd.uuid = App.su.maths.uuid.create();
    _pwCondPwd.ctime = _now;
    _pwCondPwd.utime = _now;
    _pwCondPwd.type = "EQ";
    _pwCondPwd.field = _f_passwd.uuid;
    _pwCondPwd.v1 = "";
    _pwCondPwd.v2 = "";
    _pwCondPwd.v1_type = "INPUT";
    _pwCondPwd.v2_type = "";

    _pwWhere.cond_list = {};
    _pwWhere.cond_list[_pwCondAcc.uuid] = _pwCondAcc;
    _pwWhere.cond_list[_pwCondPwd.uuid] = _pwCondPwd;

    _funFetch1.where = _pwWhere;
    _modelAdmin.fun_list[_funFetch1.uuid] = _funFetch1;

    //通用列表查询
    var _funList = new MyFun();
    _funList.uuid = App.su.maths.uuid.create();
    _funList.ctime = _now;
    _funList.utime = _now;
    _funList.name = "default";
    _funList.title = "通用列表查询";
    _funList.memo = "";
    _funList.type = "LIST";

    _funList.group_by = "";
    _funList.group_field = "";
    _funList.order_enable = 1;
    _funList.order_by = _f_account.uuid;
    _funList.order_dir = "ASC";
    _funList.pager_enable = 1;
    _funList.pager_size = 20;
    _funList.all_field = 1;//不存在全部更新
    _funList.field_list = [];

    var _listWhere = new MyWhere();
    _listWhere.uuid = App.su.maths.uuid.create();
    _listWhere.ctime = _now;
    _listWhere.utime = _now;
    _listWhere.cond_list = {};


    var _stateCond = new MyCond();
    _stateCond.uuid = App.su.maths.uuid.create();
    _stateCond.ctime = _now;
    _stateCond.utime = _now;
    _stateCond.type = "EQ";
    _stateCond.field = _f_state.uuid;
    _stateCond.v1 = "";
    _stateCond.v2 = "";
    _stateCond.v1_type = "INPUT";
    _stateCond.v2_type = "";
    //_listWhere.cond_list[_stateCond.uuid] = _stateCond;


    var _kwCond = new MyCond();
    _kwCond.uuid = App.su.maths.uuid.create();
    _kwCond.ctime = _now;
    _kwCond.utime = _now;
    _kwCond.type = "KW";
    _kwCond.title = "关键字1";
    _kwCond.field = _f_account.uuid;
    _kwCond.v1 = "";
    _kwCond.v2 = "";
    _kwCond.v1_type = "INPUT";
    _kwCond.v2_type = "";

    var _kwCond2 = new MyCond();
    _kwCond2.uuid = App.su.maths.uuid.create();
    _kwCond2.ctime = _now;
    _kwCond2.utime = _now;
    _kwCond2.title = "关键字2";
    _kwCond2.type = "KW";
    _kwCond2.field = _f_name.uuid;
    _kwCond2.v1 = "";
    _kwCond2.v2 = "";
    _kwCond2.v1_type = "INPUT";
    _kwCond2.v2_type = "";

    var _timeCond = new MyCond();
    _timeCond.uuid = App.su.maths.uuid.create();
    _timeCond.ctime = _now;
    _timeCond.utime = _now;
    _timeCond.title = "时间范围";
    _timeCond.type = "TIME";//== between
    _timeCond.field = _f_ctime.uuid;
    _timeCond.v1 = "";
    _timeCond.v2 = "";
    _timeCond.v1_type = "INPUT";
    _timeCond.v2_type = "INPUT";

    _listWhere.cond_list = {};
    _listWhere.cond_list[_stateCond.uuid] = _stateCond;
    _listWhere.cond_list[_kwCond.uuid] = _kwCond;
    _listWhere.cond_list[_kwCond2.uuid] = _kwCond2;
    _listWhere.cond_list[_timeCond.uuid] = _timeCond;

    _funList.where = _listWhere;
    _modelAdmin.fun_list[_funList.uuid] = _funList;


    _curr_app.model_list[_uuidAdmin] = _modelAdmin;
    //_curr_app.model_list[_uuidRole] = _modelRole;

    if (self.project.setCurrApp(_curr_app)) {
        self.succ("暂存成功");
    } else {
        self.fail("暂存失败");
    }
    //加载全局字段
    self.project.fieldLoad();
    //加载模型
    self.project.modelLoad();

}

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

App.dt.project.buildCC = function () {
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

    var _sel_mvc = $("#sel_build_mvc").val();
    var _sel_db = $("#sel_build_db").val();
    if ("" == _sel_mvc || "" ==  _sel_db) {
        self.fail("未选中应用框架配置和数据库配置");
        return;
    }


    var sbf = new StringBuffer();
    sbf.append("act=build");
    sbf.append("&project=");
    sbf.append(encodeURIComponent(_curr_app.project_id));
    sbf.append("&version=");
    sbf.append(encodeURIComponent(_curr_app.uuid));
    sbf.append("&arch=");
    sbf.append(encodeURIComponent(_sel_mvc));
    sbf.append("&db=");
    sbf.append(encodeURIComponent(_sel_db));

    var _data = sbf.toString();
    var _cb = function () {
        self.succ("构建成功");
    }
    self.succ("请稍后---");
    self.aPost(_data, _cb);

}

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
        self.project.archLoad();
        //加载数据库
        self.project.dbLoad();
        //加载全局字段
        self.project.fieldLoad();
        //加载模型
        self.project.modelLoad();
        //加载可构建选项
        self.project.reloadBuild(_app);
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
        showCancel: false, showPreview: false, theme: 'fas', language: 'zh', uploadUrl: function () {
            return 'img_upload.php?_rnd=' + App.su.maths.uuid.create();
        }, allowedFileExtensions: ['jpg', 'png', 'gif'], uploadExtraData: function () {
            return {
                project: App.dt.data.curr_project, version: App.dt.data.curr_project_version
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

    self.project.reloadBuild(_app);

    self.succ("和服务器同步成功");
}

/**
 * 更新可构建的清单
 * @param project_name
 */
App.dt.project.reloadBuild = function (_app) {
    $("#sel_build_mvc").empty();
    $("#sel_build_db").empty();

    for(var ii in _app.arch_list ){
        var _obj = _app.arch_list[ii];
        var _name = _obj.name;
        var _uuid = _obj.uuid;
        var _sel = '<option value="'+_uuid+'" >'+_name+'</option>';
        $("#sel_build_mvc").append(_sel);
    }

    for(var ii in _app.db_list ){
        var _obj = _app.db_list[ii];
        var _name = _obj.name;
        var _uuid = _obj.uuid;
        var _sel = '<option value="'+_uuid+'" >'+_name+'</option>';
        $("#sel_build_db").append(_sel);
    }

    $('.select2').change();
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
App.dt.project.archLoad = function () {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本,无法打开配置");
        return;
    }

    var tpl = new jSmart(self.getTpl('tpl_arch_list'));
    var res = tpl.fetch(_curr_app);
    $("#table_arch_list").html(res);
}


/**
 * 保存配置
 */
App.dt.project.archSave = function () {
    var self = App.dt;
    var _uuid = $("#txt_conf_uuid").val();
    var _conf = new MyArch();
    var now = App.su.datetime.getCurrentDateTime();
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("保存失败");
        return;
    }

    if (App.su.isEmpty(_uuid)) {
        //新建的
        var new_uuid = App.su.maths.uuid.create();
        _conf.uuid = new_uuid;
        _conf.name = "new_arch";
        _conf.ctime = now;
        _conf.utime = now;
        _uuid = new_uuid;
    } else {
        _conf = _curr_app.arch_list[_uuid];
        _conf.utime = now;
    }

    _conf.name = $("#txt_conf_name").val();
    _conf.mvc = $("#sel_app_mvc").val();
    _conf.ui = $("#sel_app_ui").val();


    _conf.has_restful = self.editor.getBootSwitchVal("#txt_conf_has_restful");
    _conf.has_doc = self.editor.getBootSwitchVal("#txt_conf_has_doc");
    _conf.has_test = self.editor.getBootSwitchVal("#txt_conf_has_test");


    console.log(_conf);

    _curr_app.arch_list[_uuid] = _conf;
    if (self.project.setCurrApp(_curr_app)) {
        self.succ("暂存成功");
    } else {
        self.fail("暂存失败");
    }
    $("#modal_edit_app_conf").modal('hide');
    self.project.archLoad();
}

/**
 * 编辑配置
 */
App.dt.project.archEdit = function (_uuid) {
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
        var _conf = _curr_app.arch_list[_uuid];

        $("#txt_conf_name").val(_conf.name);
        $("#sel_app_mvc").val(_conf.mvc);
        $("#sel_app_ui").val(_conf.ui);

        self.editor.setBootSwitchVal("#txt_conf_has_restful", _conf.has_restful);
        self.editor.setBootSwitchVal("#txt_conf_has_doc", _conf.has_doc);
        self.editor.setBootSwitchVal("#txt_conf_has_test", _conf.has_test);
    }
    $("#modal_edit_app_conf").modal('show');
    $('.select2').change();
}

/**
 * 编辑配置
 */
App.dt.project.archDrop = function (_uuid) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本");
        return;
    }
    //
    if (App.su.isEmpty(_uuid)) {
        self.fail("未选择配置");

    } else {
        bootbox.confirm("确认删除这个配置", function (ret) {
            if (ret) {
                if (undefined != _curr_app.arch_list[_uuid]) {
                    delete _curr_app.arch_list[_uuid];
                    self.succ("移除成功");
                } else {
                    self.fail("移除失败1");
                }
                self.project.archLoad();
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

        _db.ctime = now;
        _db.utime = now;
        _uuid = new_uuid;
    } else {
        _db = _curr_app.db_list[_uuid];
        _db.utime = now;
    }
    _db.name = $("#txt_db_name").val();
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

    $("#txt_db_name").val(_db.name);
    $("#txt_db_host").val(_db.host);
    $("#txt_db_port").val(_db.port);
    $("#txt_db_database").val(_db.database);
    $("#txt_db_user").val(_db.user);
    $("#txt_db_passwd").val(_db.password);
    $("#txt_db_uri").val(_db.uri);

    $("#sel_db_driver").val(_db.driver);
    $("#sel_db_source").val(_db.source);
    $("#sel_db_charset").val(_db.charset);

    $("#modal_edit_app_db").modal('show');
    $('.select2').change();
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

    } else {
        bootbox.confirm("确认删除这个数据库配置", function (ret) {
            if (ret) {
                if (undefined != _curr_app.db_list[_uuid]) {
                    delete _curr_app.db_list[_uuid];
                    self.succ("移除成功");
                } else {
                    self.fail("移除失败2");
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
            var _new_list = {};
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

    var now = App.su.datetime.getCurrentDateTime();
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("保存失败,未打开app");
        return;
    }
    var _new_name = $("#txt_field_name").val();
    if (!MyProject.isGoodName(_new_name)) {
        self.fail("请输入合法的字段名，字母开头，可包含字母、数字和下划线");
        return;
    }
    var _field = new MyField();
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
        if (App.su.isEmpty(_model_id)) {
            _field = _curr_app.field_list[_uuid];
        } else {
            var _model = self.project.getCurrModel(_model_id);
            if (null == _model) {
                self.fail("保存失败1,未打开model");
                return;
            }
            _field = _model.field_list[_uuid];
        }
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
    self.project.modelLoad();
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


    $("#sel_field_type").val(_field.type);

    $("#txt_field_size").val(_field.size);
    self.editor.setBootSwitchVal("#txt_field_auto_inc", _field.auto_increment);
    $("#txt_field_default_val").val(_field.default_value);
    self.editor.setBootSwitchVal("#txt_field_required", _field.required);


    $("#sel_field_filter").val(_field.filter);

    $("#txt_field_regexp").val(_field.regexp);


    $("#sel_field_input_by").val(_field.input_by);

    $("#txt_field_hash").val(_field.input_hash);
    $("#txt_field_position").val(_field.position);


    $("#modal_edit_field").modal('show');
    $('.select2').change();
}

/**
 * 删除字段
 */
App.dt.project.fieldDrop = function (_uuid, _model_id) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    if (null == _curr_app) {
        self.fail("未选择应用版本");
        return;
    }
    var _curr_model = null;
    //需要区分全局还是局部
    if (!App.su.isEmpty(_model_id)) {
        _curr_model = self.project.getCurrModel(_model_id);
        if (null == _curr_model) {
            self.fail("未选择有效模型");
            return;
        }
    }

    if (App.su.isEmpty(_uuid)) {
        self.fail("未选择字段");

    } else {
        bootbox.confirm("确认删除这个字段配置", function (ret) {
            if (ret) {
                if (!App.su.isEmpty(_model_id)) {
                    //_curr_model = self.project.getCurrModel(_model_id);
                    if (undefined != _curr_model.field_list[_uuid]) {
                        delete _curr_model.field_list[_uuid];
                        self.succ("移除成功1-fieldDrop");
                    } else {
                        self.fail("移除失败1-fieldDrop");
                    }
                    _curr_app.model_list[_model_id] = _curr_model;
                    self.project.setCurrApp(_curr_app);
                } else if (undefined != _curr_app.field_list[_uuid]) {
                    delete _curr_app.field_list[_uuid];
                    self.project.setCurrApp(_curr_app);
                    self.succ("移除成功2-fieldDrop");
                } else {
                    self.fail("移除失败2-fieldDrop");
                }
                self.project.fieldLoad();
                self.project.modelLoad();
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

    console.log(_curr_app);
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
                var _new_list = {};
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
                //console.log(_new_list);
                _model.field_list = _new_list;
                _curr_app.model_list[_model_id] = _model;
                if (self.project.setCurrApp(_curr_app)) {
                    //console.log(self.project.getCurrApp());
                    self.succ("更新成功");
                    self.project.modelLoad();
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
    $('.select2').change();
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

    } else {
        bootbox.confirm("确认删除这个模型", function (ret) {
            if (ret) {
                if (undefined != _curr_app.model_list[_uuid]) {
                    delete _curr_app.model_list[_uuid];
                    if (self.project.setCurrApp(_curr_app)) {
                        self.succ("移除成功");
                        self.project.modelLoad();
                    } else {
                        self.fail("移除失败4");
                    }
                } else {
                    self.fail("移除失败5");
                }
                self.project.modelLoad();
            }
        });
    }
}

function deepCopy(obj) {
    if (obj == null) {
        return null;
    }
    const keys = Object.keys(obj);
    const values = Object.values(obj);
    const newObj = {};

    for (let i = 0; i < keys.length; i++) {
        if (typeof values[i] == 'object') {
            values[i] = deepCopy(values[i]);
        }
        newObj[keys[i]] = values[i];
    }
    return newObj;
}

function clone(item) {
    if (!item) {
        return item;
    } // null, undefined values check

    var types = [Number, String, Boolean], result;

    // normalizing primitives if someone did new String('aaa'), or new Number('444');
    types.forEach(function (type) {
        if (item instanceof type) {
            result = type(item);
        }
    });

    if (typeof result == "undefined") {
        if (Object.prototype.toString.call(item) === "[object Array]") {
            result = [];
            item.forEach(function (child, index, array) {
                result[index] = clone(child);
            });
        } else if (typeof item == "object") {
            // testing that this is DOM
            if (item.nodeType && typeof item.cloneNode == "function") {
                result = item.cloneNode(true);
            } else if (!item.prototype) { // check that this is a literal
                if (item instanceof Date) {
                    result = new Date(item);
                } else {
                    // it is an object literal
                    result = {};
                    for (var i in item) {
                        result[i] = clone(item[i]);
                    }
                }
            } else {
                // depending what you would like here,
                // just keep the reference, or create new object
                if (false && item.constructor) {
                    // would not advice to do that, reason? Read below
                    result = new item.constructor();
                } else {
                    result = item;
                }
            }
        } else {
            result = item;
        }
    }
    return result;
}

function copy2(aObject) {
    if (!aObject) {
        return aObject;
    }
    let v;
    let bObject = Array.isArray(aObject) ? [] : {};
    for (const k in aObject) {
        v = aObject[k];
        bObject[k] = (typeof v === "object") ? copy2(v) : v;
    }
    return bObject;
}


/**
 * 复制模型
 */
App.dt.project.modelCopy = function (_uuid) {
    var self = App.dt;
    var _model = self.project.getCurrModel(_uuid)
    if (null == _model) {
        self.fail("未选择模型版本");
        return;
    }
    var _curr_app = self.project.getCurrApp();
    //var _model = _curr_app.model_list[_uuid];
    var _new_uuid = App.su.maths.uuid.create();
    var _model2 = deepCopy(_model);
    _model2.uuid = _new_uuid
    _model2.name = _model.name + "2";
    _model2.title = _model.title + "2";
    _curr_app.model_list[_new_uuid] = _model2;

    if (self.project.setCurrApp(_curr_app)) {
        self.succ("复制成功");
        self.project.modelLoad();
    } else {
        self.fail("复制失败");
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
    //$("#sel_index_field").empty();
    $("#sel_global_field option:selected").each(function () {
        var _fid = $(this).val();
        if (undefined != _field_g[_fid]) {
            _model.field_list[_fid] = deepCopy(_field_g[_fid]);
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
    var _curr_model = self.project.getCurrModel(model_id);
    if (null == _curr_model) {
        self.fail("未选择目标模型");
        return;
    }

    var _indexOld = {};

    $("#txt_model_index_mid").val(model_id);
    if (App.su.isEmpty(index_id)) {
        console.log("新的索引");
        $("#txt_model_index_iid").val("");

    } else {
        console.log("编辑旧索引");
        $("#txt_model_index_iid").val(index_id);
        var _idx = _curr_model.idx_list[index_id];
        $("#txt_index_name").val(_idx.name);
        $("#txt_index_memo").val(_idx.memo);
        _indexOld = _idx.field_list;


        $("#sel_index_type").val(_idx.type);

    }
    console.log("过滤可用于索引的字段")
    var _index2b = {};
    for (var ii in _curr_model.field_list) {
        var ff = _curr_model.field_list[ii];
        console.log(ff)
        var typeU = ff.type.toUpperCase();
        if (typeU == 'INT' || typeU == 'CHAR' || typeU == 'STRING' || typeU == 'LONGINT' || typeU == 'DATE' || typeU == 'DATETIME') {
            _index2b[ii] = ff;
        }
    }

    var sel_index = $("#sel_index_field");
    sel_index.empty();
    //
    for (var ii in _index2b) {
        var ff = _index2b[ii];
        var o = document.createElement("option");
        var _uuid = ff.uuid;
        o.value = _uuid;
        o.text = ff.name + " | " + ff.title;
        if (undefined != _indexOld[_uuid]) {
            o.selected = "selected";
        }
        sel_index[0].options.add(o);
    }
    //
    sel_index.bootstrapDualListbox('refresh');

    $("#modal_edit_index").modal('show');
    $('.select2').change();
}

/**
 * 完成全局字段导入
 */
App.dt.project.modelIndexSave = function () {
    var self = App.dt;
    //
    var _model_id = $("#txt_model_index_mid").val();
    //console.log("_model_id--" + _model_id)
    var _curr_model = self.project.getCurrModel(_model_id);
    if (null == _curr_model) {
        self.fail("未选择目标模型");
        return;
    }
    var _curr_app = self.project.getCurrApp();
    var _name = $("#txt_index_name").val();
    if (!MyProject.isGoodName(_name)) {
        self.fail("请输入合法的索引名，字母开头，可包含字母、数字和下划线");
        return;
    }
    var _idx = new MyIndex();
    var now = App.su.datetime.getCurrentDateTime();
    var _uuid = $("#txt_model_index_iid").val();
    if (App.su.isEmpty(_uuid)) {
        var new_uuid = App.su.maths.uuid.create();
        _idx.uuid = new_uuid;
        _idx.ctime = now;
    } else {
        _idx = _curr_model.idx_list[_uuid];
    }
    _idx.utime = now;
    _idx.name = _name;
    _idx.memo = $("#txt_index_memo").val();
    _idx.type = $("#sel_index_type").val();

    console.log("过滤可用于索引的字段")
    var _index2b = {};
    for (var ii in _curr_model.field_list) {
        var ff = _curr_model.field_list[ii];
        var typeU = ff.type.toUpperCase();
        if (typeU == 'INT' || typeU == 'CHAR' || typeU == 'STRING' || typeU == 'LONGINT' || typeU == 'DATE' || typeU == 'DATETIME') {
            _index2b[ii] = ff;
        }
    }
    _idx.field_list = {};
    //遍历选中的值
    $("#sel_index_field option:selected").each(function () {
        var _fid = $(this).val();
        if (undefined != _index2b[_fid]) {
            _idx.field_list[_fid] = _index2b[_fid];
        }
    });
    _curr_model.idx_list[_idx.uuid] = _idx;

    $("#modal_edit_index").modal("hide");
    //重新加载
    self.project.modelLoad();
};

/**
 * 删除索引
 */
App.dt.project.modelIndexDrop = function (model_id, index_id) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    var _curr_model = self.project.getCurrModel(model_id);
    console.log(_curr_model);
    if (null == _curr_model) {
        self.fail("未选择目标模型");
        return;
    }
    if (App.su.isEmpty(index_id)) {
        self.fail("未选择索引");

    } else {
        bootbox.confirm("确认删除这个索引？", function (ret) {
            if (ret) {
                if (undefined != _curr_model.idx_list[index_id]) {
                    delete _curr_model.idx_list[index_id];
                    _curr_app.model_list[model_id] = _curr_model;
                    if (self.project.setCurrApp(_curr_app)) {
                        self.succ("移除成功--modelIndexDrop");
                        self.project.modelLoad();
                    } else {
                        self.fail("移除失败1-modelIndexDrop");
                    }
                } else {
                    self.fail("移除失败2-modelIndexDrop");
                }
                self.project.modelLoad();
            }
        });
    }
}

App.dt.editor.ccOption = function (val, txt, isSel) {
    var o = document.createElement("option");
    o.value = val;
    o.text = txt;
    if (undefined != isSel && isSel == true) {
        o.selected = "selected";
    }
    return o;
}

/**
 * 创建功能函数
 * @param model_id
 * @param fun_id
 */
App.dt.project.modelFunEdit = function (model_id, fun_id) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    var _curr_model = self.project.getCurrModel(model_id);
    if (null == _curr_model) {
        self.fail("未选择目标模型for fun");
        return;
    }
    $("#txt_model_fun_mid").val(model_id);
    console.log("过滤可用于索引的字段2")
    self.project.curr_fieldCanIndex = {};
    self.project.curr_where = null;
    for (var ii in _curr_model.field_list) {
        var ff = _curr_model.field_list[ii];
        var typeU = ff.type.toUpperCase();
        if (typeU == 'INT' || typeU == 'CHAR' || typeU == 'STRING' || typeU == 'LONGINT' || typeU == 'DATE' || typeU == 'DATETIME') {
            self.project.curr_fieldCanIndex[ii] = ff;
        }
    }
    //其他单选字段
    var sel_fun_group_by = $("#sel_fun_group_by");
    var sel_fun_group_field = $("#sel_fun_group_field");
    var sel_fun_order_by = $("#sel_fun_order_by");
    var sel_fun_cond_field = $("#sel_fun_cond_field");
    //
    sel_fun_group_by.empty();
    sel_fun_group_field.empty();
    sel_fun_order_by.empty();
    sel_fun_cond_field.empty();
    var o1 = self.editor.ccOption("@@", "不可用或者外部输入");
    var o2 = self.editor.ccOption("@@", "不可用或者外部输入");
    var o3 = self.editor.ccOption("@@", "不可用或者外部输入");

    sel_fun_group_by[0].options.add(o1);
    sel_fun_group_field[0].options.add(o2);
    sel_fun_order_by[0].options.add(o3);

    for (var ii in self.project.curr_fieldCanIndex) {
        var ff = self.project.curr_fieldCanIndex[ii];

        var _uuid = ff.uuid;
        var _txt = ff.name + " | " + ff.title;
        var _sel = (undefined != self.project.curr_fieldCanIndex[_uuid]) ? true : false;
        var o1 = self.editor.ccOption(_uuid, _txt, _sel);
        var o2 = self.editor.ccOption(_uuid, _txt, _sel);
        var o3 = self.editor.ccOption(_uuid, _txt, _sel);
        var o4 = self.editor.ccOption(_uuid, _txt, _sel);

        sel_fun_group_by[0].options.add(o1);
        sel_fun_group_field[0].options.add(o2);
        sel_fun_order_by[0].options.add(o3);
        sel_fun_cond_field[0].options.add(o4);

    }

    var o1 = self.editor.ccOption("##", "聚合新健");
    var o2 = self.editor.ccOption("##", "聚合新健");
    sel_fun_order_by[0].options.add(o1);
    sel_fun_cond_field[0].options.add(o2);

    var _funFieldSelected = {};
    if (App.su.isEmpty(fun_id)) {
        console.log("新的函数");
        $("#txt_model_fun_fid").val("");

        $("#txt_fun_name").val("");
        $("#txt_fun_title").val("");
        $("#txt_fun_memo").val("");

        sel_fun_group_by.val("@@");
        sel_fun_group_field.val("@@");
        sel_fun_order_by.val("@@");
        $("#sel_fun_order_dir").val("@@");

        $("#txt_fun_limit").val("");
        self.editor.setBootSwitchVal("#txt_fun_query_optimize", "0");

        self.editor.setBootSwitchVal("#txt_fun_all_field", "1");
        self.editor.setBootSwitchVal("#txt_fun_order_enable", "0");
        self.editor.setBootSwitchVal("#txt_fun_pager_enable", "0");
        $("#txt_fun_pager_size").val(0);
        $("#block_where").html("");
    } else {
        console.log("编辑旧函数22");
        $("#txt_model_fun_fid").val(fun_id);
        var o_fun = _curr_model.fun_list[fun_id];
        console.log(o_fun);
        $("#txt_fun_name").val(o_fun.name);
        $("#sel_fun_type").val(o_fun.type);
        $("#sel_fun_type").change();
        $("#txt_fun_title").val(o_fun.title);
        $("#txt_fun_memo").val(o_fun.memo);
        //TODO 其他辅助字段
        _funFieldSelected = o_fun.field_list;
        sel_fun_group_by.val(o_fun.group_by);
        sel_fun_group_field.val(o_fun.group_field);
        sel_fun_order_by.val(o_fun.order_by);
        $("#sel_fun_order_dir").val(o_fun.order_dir);

        $("#txt_fun_limit").val(o_fun.limit);
        self.editor.setBootSwitchVal("#txt_fun_query_optimize", o_fun.query_optimize);


        self.editor.setBootSwitchVal("#txt_fun_all_field", o_fun.all_field);
        self.editor.setBootSwitchVal("#txt_fun_order_enable", o_fun.order_enable);
        self.editor.setBootSwitchVal("#txt_fun_pager_enable", o_fun.pager_enable);
        $("#txt_fun_pager_size").val(o_fun.pager_size);

        self.project.curr_where = o_fun.where;
        self.project.modelFunWhereInit();

        self.project.curr_having = o_fun.group_having;
        self.project.modelFunHavingInit();
    }
    //需要操作的字段
    var sel_fun_field = $("#sel_fun_field");
    sel_fun_field.empty();
    for (var ii in _curr_model.field_list) {
        var ff = _curr_model.field_list[ii];
        var _uuid = ff.uuid;
        var _txt = ff.name + " | " + ff.title;
        var _sel = (undefined != _funFieldSelected[_uuid]) ? true : false;
        var o = self.editor.ccOption(_uuid, _txt, _sel);
        sel_fun_field[0].options.add(o);
    }
    sel_fun_field.bootstrapDualListbox('refresh');

    $("#block_edit_mode_conf").hide();
    $("#modal_edit_fun").modal('show');
    $('.select2').change();
}

/**
 * 穿件基本的增删改查，注意
 * 需要主键
 */
App.dt.project.modelFunInitBasic = function () {

}

/**
 * 完成全局字段导入
 */
App.dt.project.modelFunSave = function () {
    var self = App.dt;
    var _model_id = $("#txt_model_fun_mid").val();
    var _curr_app = self.project.getCurrApp();
    var _curr_model = self.project.getCurrModel(_model_id);
    if (null == _curr_model) {
        self.fail("未选择目标模型");
        return;
    }
    var _name = $("#txt_fun_name").val();
    if (!MyProject.isGoodName(_name)) {
        self.fail("请输入合法的索引名，字母开头，可包含字母、数字和下划线");
        return;
    }
    var o_fun = new MyFun();
    var now = App.su.datetime.getCurrentDateTime();
    var _uuid = $("#txt_model_fun_fid").val();
    if (App.su.isEmpty(_uuid)) {
        var new_uuid = App.su.maths.uuid.create();
        o_fun.uuid = new_uuid;
        o_fun.ctime = now;
    } else {
        o_fun = _curr_model.fun_list[_uuid];
        //一般情况不再判断
    }

    o_fun.utime = now;
    o_fun.name = _name;
    o_fun.title = $("#txt_fun_title").val();
    o_fun.memo = $("#txt_fun_memo").val();
    o_fun.type = $("#sel_fun_type").val();

    o_fun.all_field = self.editor.getBootSwitchVal("#txt_fun_all_field");
    o_fun.group_by = $("#sel_fun_group_by").val();
    o_fun.group_field = $("#sel_fun_group_field").val();

    o_fun.order_enable = self.editor.getBootSwitchVal("#txt_fun_order_enable");
    o_fun.order_by = $("#sel_fun_order_by").val();
    o_fun.order_dir = $("#sel_fun_order_dir").val();

    o_fun.pager_enable = self.editor.getBootSwitchVal("#txt_fun_pager_enable");
    o_fun.pager_size = $("#txt_fun_pager_size").val();

    o_fun.query_optimize = self.editor.getBootSwitchVal("#txt_fun_query_optimize");
    o_fun.limit = $("#txt_fun_limit").val();

    o_fun.where = self.project.curr_where;

    o_fun.group_having = self.project.curr_having;



    console.log("过滤可用于操作的的字段")

    o_fun.field_list = {};
    //遍历选中的值
    $("#sel_fun_field option:selected").each(function () {
        var _fid = $(this).val();
        if (undefined != _curr_model.field_list[_fid]) {
            o_fun.field_list[_fid] = _curr_model.field_list[_fid];
        }
    });
    console.log(o_fun);
    _curr_model.fun_list[o_fun.uuid] = o_fun;
    _curr_app.model_list[_model_id] = _curr_model;
    if (self.project.setCurrApp(_curr_app)) {
        self.succ("保存成功--modelFunSave");
        self.project.modelLoad();
    } else {
        self.fail("保存失败1-modelFunSave");
    }
    $("#modal_edit_fun").modal("hide");
    //重新加载
    //self.project.modelLoad();
};

/**
 * 删除索引
 */
App.dt.project.modelFunDrop = function (model_id, fun_id) {
    var self = App.dt;
    var _curr_app = self.project.getCurrApp();
    var _curr_model = self.project.getCurrModel(model_id);
    console.log(_curr_model);
    if (null == _curr_model) {
        self.fail("未选择目标模型");
        return;
    }
    if (App.su.isEmpty(fun_id)) {
        self.fail("未选择函数");

    } else {
        bootbox.confirm("确认删除这个函数？", function (ret) {
            if (ret) {
                if (undefined != _curr_model.fun_list[fun_id]) {
                    delete _curr_model.fun_list[fun_id];
                    _curr_app.model_list[model_id] = _curr_model;
                    if (self.project.setCurrApp(_curr_app)) {
                        self.succ("移除成功--modelFunDrop");
                        self.project.modelLoad();
                    } else {
                        self.fail("移除失败1-modelFunDrop");
                    }
                } else {
                    self.fail("移除失败2-modelFunDrop");
                }
                self.project.modelLoad();
            }
        });
    }
}

/**
 * 全局临时条件
 * @type {MyWhere}
 */
App.dt.project.curr_where = new MyWhere();
/**
 * havidng 仅是一个独立的查询条件
 * @type {MyCond}
 */
App.dt.project.curr_having = new MyCond();

/**
 * 当前可用来索引的字段
 * @type {{}}
 */
App.dt.project.curr_fieldCanIndex = {};

/**
 * 初始化渲染一个查询条件
 * cond_list
 * where_list
 */
App.dt.project.modelFunWhereInit = function () {
    var self = App.dt;
    var a_data = {};
    //处理一下这个
    a_data['where0'] = self.project.curr_where;
    a_data['model_field_list'] = self.project.curr_fieldCanIndex;

    var tpl = new jSmart(self.getTpl('tpl_model_where'));
    var res = tpl.fetch(a_data);
    $("#block_where").html(res);


}

/**
 * 初始化渲染一个having条件
 * cond_list
 * where_list
 */
App.dt.project.modelFunHavingInit = function () {
    var self = App.dt;
    console.log(self.project.curr_having);
    if (undefined != self.project.curr_having && self.project.curr_having != null) {
        $("#txt_having_type").html(self.project.curr_having.type);
        $("#txt_having_v1").html(self.project.curr_having.v1);
        $("#txt_having_v1_type").html(self.project.curr_having.v1_type);
        $("#txt_having_v2").html(self.project.curr_having.v2);
        $("#txt_having_v2_type").html(self.project.curr_having.v2_type);
        $("#btn_drop_having").show();
    } else {
        $("#txt_having_type").html("");
        $("#txt_having_v1").html("");
        $("#txt_having_v1_type").html("");
        $("#txt_having_v2").html("");
        $("#txt_having_v2_type").html("");
        $("#btn_drop_having").hide();
    }
}

/**
 * 添加一个查询条件组合
 *
 * @param par 父亲节点
 * @param type
 */
App.dt.project.modelFunWhereAdd = function (par, type) {
    var self = App.dt;
    if (App.su.isEmpty(par) && self.project.curr_where != null && self.project.curr_where.uuid != null) {
        self.fail("当前已经添加了一个根条件,无需重复添加");
        return;
    }
    var o_where = new MyWhere();
    o_where.uuid = App.su.maths.uuid.create();
    o_where.type = type;

    if (App.su.isEmpty(par)) {
        console.log("添加[根]查询条件--" + type);
        o_where.parent_where = "";
        self.project.curr_where = o_where;
    } else {
        console.log("添加[嵌套]查询条件--" + type);
        o_where.parent_where = par;
        self.project.curr_where.where_list[o_where.uuid] = o_where;
    }
    self.project.modelFunWhereInit();
}

/**
 * 删除一个查询条件组合
 */
App.dt.project.modelFunWhereDrop = function (where_id) {
    var self = App.dt;
    if (App.su.isEmpty(where_id) && self.project.curr_where != null) {
        self.fail("非法业务流程");
        return;
    }
    if (where_id == self.project.curr_where.uuid) {
        self.project.curr_where = null;
        $("#block_where").html("");
    } else {
        if (undefined != self.project.curr_where.where_list[where_id]) {
            var _where_list = self.project.curr_where.where_list;
            delete _where_list[where_id];
            self.project.curr_where.where_list = _where_list;
            console.log(444);
        }
    }
    self.project.modelFunWhereInit();
}


/**
 * 编辑一个查询条件
 */
App.dt.project.modelFunCondEdit = function (where_id, cond_id) {
    var self = App.dt;
    if (undefined == self.project.curr_where || null == self.project.curr_where) {
        self.fail("非法任务流程--modelFunCondEdit");
        return;
    }
    $("#txt_where_uuid").val(where_id);
    $("#block_fun_cond_field").show();
    $("#btn_save_having").hide();
    $("#btn_save_cond").show();

    var sel_fun_cond_field = $("#sel_fun_cond_field");
    var sel_fun_cond_type = $("#sel_fun_cond_type");
    var sel_fun_cond_v1_type = $("#sel_fun_cond_v1_type");
    var sel_fun_cond_v2_type = $("#sel_fun_cond_v2_type");

    if (App.su.isEmpty(cond_id)) {
        console.log("编辑新条件");
        $("#btn_save_cond").text("新添加条件");
        $("#txt_fun_cond_v1").val("");
        $("#txt_fun_cond_v2").val("");
        $("#txt_cond_uuid").val("");


    } else {
        console.log("编辑旧条件");
        //console.log(where_id);
        //console.log(cond_id);
        $("#btn_save_cond").text("保存旧条件");
        //var _currCond =  new MyCond();
        //console.log(self.project.curr_where.where_list)
        var _currCond = null;
        if (undefined != self.project.curr_where.cond_list[cond_id]) {
            console.log(111);
            _currCond = self.project.curr_where.cond_list[cond_id];
        } else {
            console.log(222);
            if (undefined != self.project.curr_where.where_list[where_id]) {
                var _where2 = self.project.curr_where.where_list[where_id];
                _currCond = _where2.cond_list[cond_id];
                console.log(333);
            }
        }
        if (null == _currCond) {
            self.fail("非法任务流程2--modelFunCondEdit");
            return;
        }
        sel_fun_cond_field.val(_currCond.field);
        sel_fun_cond_type.val(_currCond.type);
        sel_fun_cond_v1_type.val(_currCond.v1_type);
        sel_fun_cond_v2_type.val(_currCond.v2_type);

        $("#txt_fun_cond_v1").val(_currCond.v1);
        $("#txt_fun_cond_v2").val(_currCond.v2);
        //cond_id
        $("#txt_cond_uuid").val(_currCond.uuid);
    }

    sel_fun_cond_field.change();
    sel_fun_cond_type.change();
    sel_fun_cond_v1_type.change();
    sel_fun_cond_v2_type.change();

    $("#block_edit_mode_conf").show();
}


/**
 * 保存一个查询条件
 */
App.dt.project.modelFunCondSave = function () {
    var self = App.dt;
    if (null == self.project.curr_where) {
        self.fail("请先建立根条件--modelFunCondSave");
        return;
    }

    var _cond_id = $("#txt_cond_uuid").val();
    var _where_id = $("#txt_where_uuid").val();

    var _currCond = null;
    if (!App.su.isEmpty(_cond_id)) {
        if (undefined != self.project.curr_where.cond_list[_cond_id]) {
            _currCond = self.project.curr_where.cond_list[_cond_id];
        } else {
            if (undefined != self.project.curr_where.where_list[_where_id]) {
                var _where2 = self.project.curr_where.where_list[_where_id];
                _currCond = _where2.cond_list[_cond_id];
            }
        }
    }
    var _now = App.su.datetime.getCurrentDateTime();
    if (null == _currCond) {
        console.log("保存新配置");
        _currCond = new MyCond();
        _cond_id = App.su.maths.uuid.create();
        _currCond.uuid = _cond_id;
        _currCond.ctime = _now;
    } else {
        console.log("保存旧配置");
    }
    _currCond.utime = _now;
    _currCond.field = $("#sel_fun_cond_field").val();
    _currCond.type = $("#sel_fun_cond_type").val();
    _currCond.v1_type = $("#sel_fun_cond_v1_type").val();
    _currCond.v2_type = $("#sel_fun_cond_v2_type").val();
    _currCond.v1 = $("#txt_fun_cond_v1").val();
    _currCond.v2 = $("#txt_fun_cond_v2").val();

    if (undefined != self.project.curr_where.where_list[_where_id]) {
        self.project.curr_where.where_list[_where_id].cond_list[_cond_id] = _currCond;
    } else {
        self.project.curr_where.cond_list[_cond_id] = _currCond;
    }
    $("#block_edit_mode_conf").hide();
    self.succ("临时添加");
    self.project.modelFunWhereInit();
}

/**
 * 删除一个查询条件
 */
App.dt.project.modelFunCondDrop = function (_where_id, _cond_id) {
    var self = App.dt;
    if (null == self.project.curr_where) {
        self.fail("没有条件--modelFunCondDrop");
        return;
    }
    if (!App.su.isEmpty(_cond_id)) {
        var _currWhere = self.project.curr_where;
        if (undefined != _currWhere.cond_list[_cond_id]) {
            console.log("删除的在主条件中11");
            delete _currWhere.cond_list[_cond_id];
        } else {
            if (undefined != _currWhere.where_list[_where_id]) {
                console.log("删除的在主条件中22");
                if (undefined != _currWhere.where_list[_where_id].cond_list[_cond_id]) {
                    delete _currWhere.where_list[_where_id].cond_list[_cond_id];
                    console.log("删除的在主条件中33");
                }
            }
        }
        self.project.curr_where = _currWhere;
        self.project.modelFunWhereInit();
        return;
    }
    self.fail("没有条件--空的--modelFunCondDrop");
    return;
}

/**
 * 编辑一个having条件
 */
App.dt.project.modelFunHavingEdit = function () {
    var self = App.dt;
    $("#txt_where_uuid").val("");

    var sel_fun_cond_field = $("#sel_fun_cond_field");
    var sel_fun_cond_type = $("#sel_fun_cond_type");
    var sel_fun_cond_v1_type = $("#sel_fun_cond_v1_type");
    var sel_fun_cond_v2_type = $("#sel_fun_cond_v2_type");

    $("#block_fun_cond_field").hide();
    $("#btn_save_cond").hide();
    $("#btn_save_having").show();

    var _currCond = self.project.curr_having;
    $("#btn_save_having").text("保存聚合过滤条件");
    if (undefined == _currCond || App.su.isEmpty(_currCond.uuid)) {
        console.log("编辑新Having 条件");

        $("#txt_fun_cond_v1").val("");
        $("#txt_fun_cond_v2").val("");
        $("#txt_cond_uuid").val("");


    } else {
        console.log("编辑旧Having 条件");
        //sel_fun_cond_field.val(_currCond.field);
        sel_fun_cond_type.val(_currCond.type);
        sel_fun_cond_v1_type.val(_currCond.v1_type);
        sel_fun_cond_v2_type.val(_currCond.v2_type);

        $("#txt_fun_cond_v1").val(_currCond.v1);
        $("#txt_fun_cond_v2").val(_currCond.v2);
        //cond_id
        $("#txt_cond_uuid").val(_currCond.uuid);
    }

    //sel_fun_cond_field.change();
    sel_fun_cond_type.change();
    sel_fun_cond_v1_type.change();
    sel_fun_cond_v2_type.change();

    $("#block_edit_mode_conf").show();
}


/**
 * 保存一个having条件
 */
App.dt.project.modelFunHavingSave = function () {
    var self = App.dt;
    var _currCond = self.project.curr_having;

    var _now = App.su.datetime.getCurrentDateTime();
    if (null == _currCond) {
        console.log("保存新配置");
        _currCond = new MyCond();
        _currCond.uuid = App.su.maths.uuid.create();
        ;
        _currCond.ctime = _now;
    } else {
        console.log("保存旧配置");
    }
    _currCond.utime = _now;
    //_currCond.field = $("#sel_fun_cond_field").val();
    _currCond.type = $("#sel_fun_cond_type").val();
    _currCond.v1_type = $("#sel_fun_cond_v1_type").val();
    _currCond.v2_type = $("#sel_fun_cond_v2_type").val();
    _currCond.v1 = $("#txt_fun_cond_v1").val();
    _currCond.v2 = $("#txt_fun_cond_v2").val();

    self.project.curr_having = _currCond;
    $("#block_edit_mode_conf").hide();
    self.project.modelFunHavingInit();
}

/**
 * 删除having条件
 */
App.dt.project.modelFunHavingDrop = function () {
    var self = App.dt;
    self.project.curr_having = null;
    self.project.modelFunHavingInit();
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
            $('.select2').change();
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
        self.project.archEdit("");
    });

    $("#btn_save_conf").click(function () {
        self.project.archSave();
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

    $("#btn_save_cond").click(function () {
        self.project.modelFunCondSave();
    });

    $("#btn_save_having").click(function () {
        self.project.modelFunHavingSave();
    });

    $("#btn_build").click(function () {
        self.project.buildCC();
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