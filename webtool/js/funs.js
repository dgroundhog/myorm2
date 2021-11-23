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

function MyFun() {
    this.uuid = "";//唯一码
    this.name = "";//名称
    this.title = "";//标题
    this.position = 255;//排序
    this.ctime = "";//创建时间
    this.utime = "";//更新时间
}

MyFun.prototype.parseBasic = function (json_one) {
    //解析一个单体
    this.uuid = json_one.uuid;
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
    MyFun.call(this);
    this.curr_version = "";//当前版本号
    this.version_list = {};//版本号列表  k-v
    //版本号不排序
}

//把子类的原型指向通过Object.create创建的中间对象
MyProject.prototype = Object.create(MyFun.prototype);
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

    for(var ii in json_one.version_list){
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
    if (!App.su.validate.isLetterAndNumber(_name)) {
        return false;
    }

    if (_name.length < 2 || _name.length > 32) {
        return false;
    }

    return true;
}

/**
 * 主APP的结构
 * @constructor
 */
function MyApp() {
    MyFun.call(this);
    this._model_order_ = {};//模型位置  k-v
    this._models_ = [];//模型列表
    this.project_id = "";
    this.img_icon_id = "";
    this.img_logo_id = "";

    this.conf_list = [];//配置列表

}

//把子类的原型指向通过Object.create创建的中间对象
MyApp.prototype = Object.create(MyFun.prototype);
MyApp.prototype.constructor = MyApp;

MyApp.prototype.parse = function (json_one) {
    //解析一个单体
    this.parseBasic(json_one);

    this.project_id = json_one.project_id;
    this.img_icon_id = json_one.img_icon_id;
    this.img_logo_id = json_one.img_logo_id;

    //配置
    this.conf_list = new Object();
    for(var ii in json_one.conf_list){
        var _conf = new MyAppConf();
        _conf.parse(json_one.conf_list[ii]);
        var _uuid = _conf.uuid;
        this.conf_list[_uuid] = _conf;
    }

    // this.version_list = new Object();//版本号列表  k-v
    //
    // $.each(json_one.version_list,function (i, item){
    //     var _app = new MyApp();
    //     _app.parse(item);
    //     var _uuid = _app.uuid;
    //     this.version_list[_uuid] = _app;
    // });
};

/**
 * 追加一个模型
 * @param  _model MyModel
 */
MyApp.prototype.appendModel = function (_model) {
    if (!_model instanceof MyModel) {
        return;
    }
    this._models_.push(_model);
};

/**
 * 主模型
 * @constructor
 */
function MyModel() {
    this.uuid = "";//唯一码
    this.name = "";//名称
    this._field_order_ = {};//模型位置  k-v
    this._fields_ = [];//字段列表
    this._fun_order_ = {};//模型位置  k-v
    this._funs_ = [];//函数列表
}


/**
 * 主APP的结构
 * @constructor
 */
function MyAppConf() {
    MyFun.call(this);
    this.mvc = "";
    this.ui = "";
    this.has_restful = "0";
    this.has_test = "0";
    this.has_doc = "0";
    //版本号不排序
}

//把子类的原型指向通过Object.create创建的中间对象
MyAppConf.prototype = Object.create(MyFun.prototype);
MyAppConf.prototype.constructor = MyAppConf;

/**
 *
 * @param json_one
 */
MyAppConf.prototype.parse = function (json_one) {
    //解析一个单体
    this.parseBasic(json_one);

    this.mvc = json_one.mvc;
    this.ui =json_one.ui;
    this.has_restful = json_one.has_restful;
    this.has_test =json_one.has_test;
    this.has_doc = json_one.has_doc;

};

/**
 * 主字段
 * @constructor
 */
function MyField() {
}

/**
 * CURD 操作
 * @constructor
 */
function MyFun() {
}

/**
 * 查询条件
 * @constructor
 */
function MyWhere() {
}

/**
 * 数据库配置
 * @constructor
 */
function MyDb() {
}
