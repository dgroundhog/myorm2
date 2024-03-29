/**
 * 0-1 字符缓存
 * @constructor
 */
function StringBuffer() {
    this._strings_ = [];
}

StringBuffer.prototype.append = function (str) {
    this._strings_.push(str);
};

StringBuffer.prototype.toString = function () {
    return this._strings_.join("");
};

/**
 * 深度copy
 * @param obj
 * @returns {{}|null}
 */
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

//copy
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

//copy2
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

function MyStruct() {
    this.uuid = "";//唯一码
    this.scope = "";//范畴
    this.name = "";//名称
    this.type = "";//类型
    this.title = "";//标题
    this.memo = "";//备注或者帮助
    this.position = 255;//排序
    this.ctime = "";//创建时间
    this.utime = "";//更新时间
}

MyStruct.prototype.parseBasic = function (json_one) {
    //解析一个单体
    //console.log(json_one)
    this.uuid = json_one.uuid;
    //this.scope = json_one.scope;
    this.type = json_one.type;
    this.name = json_one.name;
    this.title = json_one.title;
    this.memo = json_one.memo;
    this.position = json_one.position;
    this.ctime = json_one.ctime;
    this.utime = json_one.utime;
};


/**
 * 主APP的结构
 * @constructor
 */
function MyProject() {
    MyStruct.call(this);
    this.scope = "PROJECT";
    this.curr_version = "";//当前版本号
    this.version_list = {};//版本号列表  k-v
    //版本号不排序
}

//把子类的原型指向通过Object.create创建的中间对象
MyProject.prototype = Object.create(MyStruct.prototype);
MyProject.prototype.constructor = MyProject;

/**
 *
 * @param json_one
 */
MyProject.prototype.parse = function (json_one) {
    //解析一个单体
    this.parseBasic(json_one);
    this.curr_version = "";//当前版本号
    this.version_list = {};//版本号列表  k-v

    for (var ii in json_one.version_list) {
        var _app = new MyApp();
        _app.parse(json_one.version_list[ii]);
        var _uuid = _app.uuid;
        this.version_list[_uuid] = _app;
    }
};


/**
 * 静态方法,是否有效的UID
 */
MyProject.isGoodName = function (_name) {
    //console.log(_name);
    var reg_text = /^[a-zA-Z][_0-9a-zA-Z]*$/.test(_name);
    if (!reg_text) {
        console.log("~isGoodName--111");
        return false;
    }
    if (_name.length < 2 || _name.length > 32) {
        console.log("~isGoodName--111");
        return false;
    }

    return true;
}

/**
 * 主APP的结构
 * @constructor
 */
function MyApp() {
    MyStruct.call(this);
    this.scope = "APP";

    this.project_id = "";
    this.img_icon_id = "";
    this.img_logo_id = "";
    this.package = "";//包名称

    this.arch_list = [];//应用配置列表
    this.db_list = [];//数据库配置列表
    this.field_list = [];//全局字段
    this.model_list = [];//模型配置列表

    this.ecode_list = [];//错误码列表

}

//把子类的原型指向通过Object.create创建的中间对象
MyApp.prototype = Object.create(MyStruct.prototype);
MyApp.prototype.constructor = MyApp;

MyApp.prototype.parse = function (json_one) {
    //解析一个单体
    this.parseBasic(json_one);

    //
    this.project_id = json_one.project_id;
    this.img_icon_id = json_one.img_icon_id;
    this.img_logo_id = json_one.img_logo_id;
    this.package = json_one.package;

    //架构配置
    this.arch_list = {};
    for (var ii in json_one.arch_list) {
        var _conf = new MyArch();
        _conf.parse(json_one.arch_list[ii]);
        var _uuid = _conf.uuid;
        this.arch_list[_uuid] = _conf;
    }

    //数据库
    this.db_list = {};
    for (var ii in json_one.db_list) {
        var _db = new MyDb();
        _db.parse(json_one.db_list[ii]);
        var _uuid = _db.uuid;
        this.db_list[_uuid] = _db;
    }

    //全局字段
    this.field_list = {};
    for (var ii in json_one.field_list) {
        var _field = new MyField();
        _field.parse(json_one.field_list[ii]);
        var _uuid = _field.uuid;
        this.field_list[_uuid] = _field;
    }

    //实体模型
    this.model_list = {};
    for (var ii in json_one.model_list) {
        var _model = new MyModel();
        _model.parse(json_one.model_list[ii]);
        var _uuid = _model.uuid;
        this.model_list[_uuid] = _model;
    }

    //错误码，简单的kv结构，按码排序
    this.ecode_list = {};
    for (var ii in json_one.ecode_list) {
        this.ecode_list[ii] = json_one.ecode_list[ii];
    }

};

/**
 * 静态方法,是否有效的UID
 */
MyApp.isGoodPackageName = function (_name) {
    //console.log(_name);
    var reg_text = /^[a-zA-Z]+[0-9a-zA-Z_]*(\.[a-zA-Z]+[0-9a-zA-Z_]*)*(\\[a-zA-Z]+[0-9a-zA-Z_]*)*$/.test(_name);
    if (!reg_text) {
        console.log("~isGoodPackageName--111");
        return false;
    }
    if (_name.length > 32) {
        console.log("~PackageName limit to 32 chars");
        return false;
    }
    return true;
}


/**
 * 主模型
 * @constructor
 */
function MyModel() {
    MyStruct.call(this);
    this.scope = "MODEL";
    this.primary_key = "id";
    this.has_ui = "1";
    this.fa_icon = "apple";
    this.table_name = "newtabel";

    this.field_list = [];//全局字段
    this.idx_list = [];//应用配置列表
    this.fun_list = [];//操作方法

}

//把子类的原型指向通过Object.create创建的中间对象
MyModel.prototype = Object.create(MyStruct.prototype);
MyModel.prototype.constructor = MyModel;

/**
 *
 * @param json_one
 */
MyModel.prototype.parse = function (json_one) {
    //解析一个单体
    this.parseBasic(json_one);
    this.primary_key = json_one.primary_key;
    this.has_ui = json_one.has_ui;
    this.fa_icon = json_one.fa_icon;
    this.table_name = json_one.table_name;

    //字段列表
    this.field_list = {};
    for (var ii in json_one.field_list) {
        var _obj = new MyField();
        _obj.parse(json_one.field_list[ii]);
        var _uuid = _obj.uuid;
        this.field_list[_uuid] = _obj;
        //全局字段仅用uuid和排序位置
    }

    //索引列表
    this.idx_list = {};
    for (var ii in json_one.idx_list) {
        var _obj = new MyIndex();
        _obj.parse(json_one.idx_list[ii]);
        var _uuid = _obj.uuid;
        this.idx_list[_uuid] = _obj;
    }

    //函数列表
    this.fun_list = {};
    for (var ii in json_one.fun_list) {
        var _obj = new MyFun();
        _obj.parse(json_one.fun_list[ii]);
        var _uuid = _obj.uuid;
        this.fun_list[_uuid] = _obj;
    }

};


/**
 * 主APP的结构
 * @constructor
 */
function MyArch() {
    MyStruct.call(this);
    this.name = "tech1";
    this.scope = "ARCH";
    this.mvc = "";
    this.ui = "";
    this.has_restful = "0";
    this.has_test = "0";
    this.has_doc = "0";
    //版本号不排序
}

//把子类的原型指向通过Object.create创建的中间对象
MyArch.prototype = Object.create(MyStruct.prototype);
MyArch.prototype.constructor = MyArch;

/**
 *
 * @param json_one
 */
MyArch.prototype.parse = function (json_one) {
    //解析一个单体
    this.parseBasic(json_one);

    this.mvc = json_one.mvc;
    this.ui = json_one.ui;
    this.has_restful = json_one.has_restful;
    this.has_test = json_one.has_test;
    this.has_doc = json_one.has_doc;

};


/**
 * 数据库配置
 * @constructor
 */
function MyDb() {
    MyStruct.call(this);
    this.scope = "DB";
    this.driver = "";
    this.source = "";
    this.host = "localhost";
    this.port = "3306";
    this.database = "db1";
    this.user = "root";
    this.password = "";
    this.charset = "";
    this.uri = "";

}

//把子类的原型指向通过Object.create创建的中间对象
MyDb.prototype = Object.create(MyStruct.prototype);
MyDb.prototype.constructor = MyDb;

/**
 *
 * @param json_one
 */
MyDb.prototype.parse = function (json_one) {
    //解析一个单体
    this.parseBasic(json_one);
    this.driver = json_one.driver;
    this.source = json_one.source;
    this.host = json_one.host;
    this.port = json_one.port;
    this.database = json_one.database;
    this.user = json_one.user;
    this.password = json_one.password;
    this.charset = json_one.charset;
    this.uri = json_one.uri;
    this.password = json_one.password;

};


/**
 * 主字段
 * @constructor
 */
function MyField() {
    MyStruct.call(this);
    this.scope = "FIELD";
    this.type = "STRING";
    this.size = "255";
    this.auto_increment = "0";
    this.default_value = "";
    this.required = "0";
    this.filter = "";
    this.regexp = "";
    this.input_by = "";
    this.input_hash = "";
    this.is_global = "0";//


}

//把子类的原型指向通过Object.create创建的中间对象
MyField.prototype = Object.create(MyStruct.prototype);
MyField.prototype.constructor = MyField;

/**
 *
 * @param json_one
 */
MyField.prototype.parse = function (json_one) {
    //解析一个单体
    this.parseBasic(json_one);
    this.size = json_one.size;
    this.auto_increment = json_one.auto_increment;
    this.default_value = json_one.default_value;
    this.required = json_one.required;
    this.filter = json_one.filter;
    this.regexp = json_one.regexp;
    this.input_by = json_one.input_by;
    this.input_hash = json_one.input_hash;
    this.is_global = json_one.is_global;
};

/**
 * 索引结构
 * @constructor
 */
function MyIndex() {
    MyStruct.call(this);
    this.scope = "INDEX";
    this.type = "KEY";
    this.field_list = [];//索引字段
}

//把子类的原型指向通过Object.create创建的中间对象
MyIndex.prototype = Object.create(MyStruct.prototype);
MyIndex.prototype.constructor = MyIndex;

/**
 *
 * @param json_one
 */
MyIndex.prototype.parse = function (json_one) {
    //解析一个单体
    this.parseBasic(json_one);

    this.field_list = {};
    for (var ii in json_one.field_list) {
        var _field = new MyField();
        _field.parse(json_one.field_list[ii]);
        var _uuid = _field.uuid;
        this.field_list[_uuid] = _field;
    }
};

/**
 * CURD 操作
 * @constructor
 */
function MyFun() {
    MyStruct.call(this);
    this.scope = "FUN";
    this.type = "";
    this.all_field = "1";
    this.has_kw = "0";//主要是页面上，模糊查询
    this.where = null;
    this.field_list = [];//操作字段
    this.group_field = "";
    this.group_by = "";
    this.group_having = null;//MyCond
    this.order_enable = "0";
    this.order_by = "";
    this.order_dir = "";
    this.pager_enable = "";//是否需要排序
    this.pager_size = "";//排序字段，为空时外部输入
    this.query_optimize = "0";//查询优化，百万表内不用考虑
    this.limit = 0;//更新或者删除限制

}

//把子类的原型指向通过Object.create创建的中间对象
MyFun.prototype = Object.create(MyStruct.prototype);
MyFun.prototype.constructor = MyFun;

/**
 *
 * @param json_one
 */
MyFun.prototype.parse = function (json_one) {
    //解析一个单体
    this.parseBasic(json_one);

    this.type = json_one.type;
    this.all_field = json_one.all_field;
    //this.where = json_one.where;
    this.group_field = json_one.group_field;
    this.group_by = json_one.group_by;
    this.group_having = null;
    if (undefined != json_one.group_having && null != json_one.group_having && null != json_one.group_having.uuid) {
        console.log(json_one.group_having);
        var o_having = new MyCond();
        o_having.parse(json_one.group_having);
        this.group_having = o_having;
    }
    console.log(this.group_having);

    this.order_enable = json_one.order_enable;
    this.order_by = json_one.order_by;
    this.order_dir = json_one.order_dir;

    this.pager_enable = json_one.pager_enable;
    this.pager_size = json_one.pager_size;

    this.query_optimize = json_one.query_optimize;
    this.limit = json_one.limit;

    this.where = null;
    if (undefined != json_one.where && null != json_one.where) {
        var o_where = new MyWhere();
        o_where.parse(json_one.where);
        this.where = o_where;
    }

    this.field_list = {};
    for (var ii in json_one.field_list) {
        var _field = new MyField();
        _field.parse(json_one.field_list[ii]);
        var _uuid = _field.uuid;
        this.field_list[_uuid] = _field;
    }
}

/**
 * 查询条件组合
 * 允许嵌套
 * @constructor
 */
function MyWhere() {
    MyStruct.call(this);
    this.scope = "WHERE";
    this.type = "AND";
    this.cond_list = [];//操作字段

}

//把子类的原型指向通过Object.create创建的中间对象
MyWhere.prototype = Object.create(MyStruct.prototype);
MyWhere.prototype.constructor = MyWhere;

/**
 *
 * @param json_one
 */
MyWhere.prototype.parse = function (json_one) {
    //解析一个单体
    this.parseBasic(json_one);

    this.type = json_one.type;
    this.cond_list = {};
    for (var ii in json_one.cond_list) {
        var _cond = new MyCond();
        _cond.parse(json_one.cond_list[ii]);
        var _uuid = _cond.uuid;
        this.cond_list[_uuid] = _cond;
    }
}

/**
 * 最小查询条件
 * @constructor
 */
function MyCond() {
    MyStruct.call(this);
    this.scope = "COND";
    this.type = "EQ";
    this.field = "";
    this.v1 = "@@";
    this.v2 = "@@";
    this.v1_type = "@@";
    this.v2_type = "@@";
    this.is_sub_where = "0";//1表示子嵌套
    this.type_sub_where = "OR";//子嵌套类型
    this.cond_list = [];//操作字段
}

//把子类的原型指向通过Object.create创建的中间对象
MyCond.prototype = Object.create(MyStruct.prototype);
MyCond.prototype.constructor = MyCond;

/**
 * @param json_one
 */
MyCond.prototype.parse = function (json_one) {
    //解析一个单体
    this.parseBasic(json_one);
    this.type = json_one.type;
    this.field = json_one.field;
    this.v1 = json_one.v1;
    this.v2 = json_one.v2;
    this.v1_type = json_one.v1_type;
    this.v2_type = json_one.v2_type;
    this.is_sub_where = json_one.is_sub_where;
    this.type_sub_where = json_one.type_sub_where;

    this.cond_list = {};
    for (var ii in json_one.cond_list) {
        var _cond = new MyCond();
        _cond.parse(json_one.cond_list[ii]);
        var _uuid = _cond.uuid;
        this.cond_list[_uuid] = _cond;
    }
}