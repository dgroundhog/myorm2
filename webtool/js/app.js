/**
 * 兼容性工具,基于jquery su= small tools
 */
var App = {};
App._time = new Date();
App.su = {
    isEmpty: function (v) {
        if (!v) {
            return true;
        }
        if (undefined == v || '' == v || '0' == v || 0 == v) {
            return true;
        }
        return false;
    }, isEmpty2: function (v) {
        if (undefined == v || '' == v) {
            return true;
        }
        return false;
    },

    /**
     * 兼容性工具,基于jquery
     */
    string: {

        /**
         * 拼合url
         * @param url
         * @param param
         * @returns {string}
         */

        okurl: function (url, param) {
            var _joiner = "?";
            if (url.indexOf("?") > 0) {
                _joiner = "&";
            }
            var type = typeof param;
            if ((type == 'string') || (type == 'number')) {
                url += _joiner + param;
            } else if (type == 'object') {
                url += _joiner;
                for (var i in param) {
                    url += ["&", i, "=", param[i]].join('');
                }
            }
            return url;
        }, /**
         * 计算中文长度，把中文当作一个2字节
         *
         * @param str_maybe_cn
         * @returns {string}
         */
        cnLength: function (str_maybe_cn) {
            if (!str_maybe_cn) {
                return 0;
            }
            var arr = str_maybe_cn.match(/[^\x00-\xff]/ig);
            return str_maybe_cn.length + (arr == null ? 0 : arr.length);
        }, /**
         * 截断中字
         *
         * @param str
         * @param n
         * @returns {string}
         */
        cnTruncate: function (str, n) {
            if (!str) {
                return '';
            }
            var r = /[^\x00-\xff]/g;
            if (str.replace(r, "mm").length > n) {
                var m = Math.floor(n / 2);
                for (var i = m; i < str.length; i++) {
                    if (str.substr(0, i).replace(r, "mm").length >= n) {
                        return str.substr(0, i) + "…";
                    }
                }
            }
            return str;
        }, /**
         * 删除2边空格，包括全角的
         *
         * @param str_maybe_empty
         * @returns {string}
         */
        trim: function (str_maybe_empty) {
            if (!str_maybe_empty) {
                return '';
            }
            return str_maybe_empty.replace(/^\s+/, "").replace(/\s+$/, "");
        }, /**
         * 删除所有空格，包括全角的
         *
         * @param str_maybe_empty
         * @returns {string}
         */
        trimAll: function (str_maybe_empty) {
            if (!str_maybe_empty) {
                return '';
            }
            return str_maybe_empty.replace(/[\u3000]/g, "").replace(/\s+/g, "");
        }, /**
         * text
         *
         * @param str_maybe_html
         * @returns {string}
         */
        text: function (str_maybe_html) {
            return str_maybe_html.replace(/<[^>].*?>/g, "");
        },

        /**
         * 监测密码强度
         *
         * @param sPW
         * @returns {number}
         */
        checkStrong: function (sPW) {
            if (sPW.length < 6) return 0; // 密码太短
            var i;
            // 判断输入密码的类型
            var _charMode = function (iN) {
                if (iN >= 48 && iN <= 57) // 数字
                    return 1;
                if (iN >= 65 && iN <= 90) // 大写
                    return 2;
                if (iN >= 97 && iN <= 122) // 小写
                    return 4; else return 8;
            };
            // bitTotal函数
            // 计算密码模式
            var _bitTotal = function (num) {

            };
            // 返回强度级别
            var Modes = 0;
            for (i = 0; i < sPW.length; i++) {
                // 密码模式
                Modes |= _charMode(sPW.charCodeAt(i));
            }
            var _modes = 0;
            for (i = 0; i < 4; i++) {
                if (Modes & 1) _modes++;
                Modes >>>= 1;
            }
            return _modes;
        }, jsonToString: function (arr) {
            var s = "";
            if (arr instanceof Array || arr instanceof Object) {
                var isObj = 0;
                // check value type
                for (key in arr) {
                    if (isNaN(parseInt(key))) { // key is string
                        isObj = 1;
                    } else {
                        // key is index , check sort
                        var na = arr.length;
                        var tmp = arr;
                        // hack for ie
                        arr = Array();
                        for (var j = 0; j < na; j++) {
                            if (typeof (tmp[j]) == "undefined") {
                                arr[j] = "";
                            } else {
                                arr[j] = tmp[j];
                            }
                        }
                    }
                    break;
                }
                for (key in arr) {
                    var value = arr[key];
                    if (isObj) {
                        if (s) {
                            s += ',';
                        }
                        s += '"' + key + '":' + App.su.string.jsonToString(value);
                    } else {
                        if (s) {
                            s += ',';
                        }
                        s += App.su.string.jsonToString(value);
                    }
                }
                if (isObj) s = '{' + s + '}'; else s = '[' + s + ']';
            } else {
                s = '"' + arr + '"';
                /**
                 * if (!isNaN(parseInt(arr))) { s += arr; } else { s = '"' + arr +
                 * '"'; }
                 */
            }
            return s;
        }, stringToJson: function (json) {
            eval('var s=' + json + '');
            return s;
        }, /**
         * base64 编码
         */
        base64: {
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
                    output = output + _keyStr.charAt(enc1) + _keyStr.charAt(enc2) + _keyStr.charAt(enc3) + _keyStr.charAt(enc4);
                }
                return output;
            }, decode: function (input) {
                var output = "";
                var chr1, chr2, chr3;
                var enc1, enc2, enc3, enc4;
                var i = 0;
                input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
                while (i < input.length) {
                    enc1 = _keyStr.indexOf(input.charAt(i++));
                    enc2 = _keyStr.indexOf(input.charAt(i++));
                    enc3 = _keyStr.indexOf(input.charAt(i++));
                    enc4 = _keyStr.indexOf(input.charAt(i++));
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
            }, // private method for UTF-8 encoding
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
            }, // private method for UTF-8 decoding
            _utf8_decode: function (utftext) {
                var string = "";
                var i = 0;
                var c = 0;
                // var c1 = 0;
                var c2 = 0;
                var c3 = 0;
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
        }
    },

    array: {
        in_array: function (needle, haystack) {
            var type = typeof needle;
            if ((type == 'string') || (type == 'number')) {
                for (var i in haystack) {
                    if (haystack[i] == needle) {
                        return true;
                    }
                }
            }
            return false;
        }, getValue: function (haystack, key) {
            for (var i in haystack) {
                if (i == key) {
                    return haystack[i];
                }
            }
            return false;
        }, setValue: function (haystack, key, val) {
            for (var i in haystack) {
                if (i == key) {
                    haystack[i] = val;
                    break;
                }
            }
            return haystack;
        }
    },

    select: {
        /**
         * 通过id设置选定
         *
         * @param where_select
         * @param id_selected
         */
        setSelectedById: function (selector_id, selected_id) {
            var fkie6 = function () {
                $("select#" + selector_id).each(function () {
                    for (var i = 0; i < this.options.length; i++) {
                        if (this.options[i].id == selected_id) {
                            this.options[i].selected = true;
                            break;
                        }
                    }
                });
            };
            setTimeout(fkie6, 10);
        }, /**
         * 通过value设置选定
         *
         * @param where_select
         * @param id_selected
         */
        setSelectedByValue: function (selector_id, selected_value) {
            var fkie6 = function () {
                $("select#" + selector_id).each(function () {
                    for (var i = 0; i < this.options.length; i++) {
                        if (this.options[i].value == selected_value) {
                            this.options[i].selected = true;
                            break;
                        }
                    }
                });
            };
            setTimeout(fkie6, 10);
        },

        /**
         * 通过value设置选定
         *
         * @param where_select
         * @param id_selected
         */
        selectByValue: function (who, selected_value) {
            var fkie6 = function () {
                who.each(function () {
                    for (var i = 0; i < this.options.length; i++) {
                        if (this.options[i].value == selected_value) {
                            this.options[i].selected = true;
                            break;
                        }
                    }
                });
            };
            setTimeout(fkie6, 10);
        }, noOption: function () {
            return '<option value="">请选择</option>';
        }

    },

    validate: {

        check: function (form_id) {

            var _form = $("#" + form_id);

            var finalRet = true;
            var firstErr = null;
            _form.find("input").each(function () {
                var _me = $(this);
                if (undefined == _me.attr("valid_rule") || null == _me.attr("valid_rule")) {
                    //return;

                } else {

                    _me.removeClass("is-invalid");
                    _me.removeClass("is-valid");
                    var _name = _me.attr("name");
                    var helpText = $("#" + _name + "Help");

                    helpText.removeClass("text-muted");
                    helpText.removeClass("invalid-feedback");
                    helpText.removeClass("valid-feedback");

                    var required = false;
                    if (undefined != _me.attr("required") && null != _me.attr("required")) {
                        required = true;
                    }

                    var _min = 0;
                    var _max = 0;

                    if (undefined != _me.attr("valid_min") && null != _me.attr("valid_min")) {
                        _min = _me.attr("valid_min");
                    }
                    if (undefined != _me.attr("valid_max") && null != _me.attr("valid_max")) {
                        _max = _me.attr("valid_max");
                    }


                    var valid_rule = _me.attr("valid_rule");
                    var valid_rules = valid_rule.split(",");
                    var len = valid_rules.length;
                    for (var ii = 0; ii < len; ii++) {
                        var _rule = valid_rules[ii];
                        var ret = App.su.validate._checkOne(_me.val(), _rule, required, _max, _min);
                        if (!ret) {
                            finalRet = finalRet && false;
                            if (null == firstErr) {
                                firstErr = _me;
                            }
                            _me.addClass("is-invalid");
                            helpText.addClass("invalid-feedback");
                            break;
                        } else {
                            _me.addClass("is-valid");
                            helpText.addClass("valid-feedback");
                        }
                    }
                }

            });

            if (finalRet) {
                return true;
            } else {
                if (firstErr != null) {
                    firstErr.focus();
                }
                App.su.notice.err("提交失败", "请按照提示检查错误");
            }
        },


        _checkOne: function (val, type, required, max, min) {
            //TODO
            if (App.su.isEmpty2(val)) {
                if (required) {
                    return false;
                }
                return true;
            } else {
                switch (type) {
                    case "email":
                        return App.su.validate.isEmail(val);
                    case "mobile":
                        return App.su.validate.isMobilePhone(val);
                    case "idcard":
                        return App.su.validate.isCardNoCn(val);
                    case "size_range":
                        var _val = val.length;
                        var _max = max * 1;
                        var _min = min * 1;

                        return (_val <= _max) && (_val >= _min);
                    case "size_limit":
                        var _val = val.length;
                        var _max = max * 1;
                        //var _min = min * 1;
                        return (_val <= _max);
                    case "val_range":
                        var _val = val * 1;
                        var _max = max * 1;
                        var _min = min * 1;
                        return (_val <= _max) && (_val >= _min);
                    case "val_limit":
                        var _val = val * 1;
                        var _max = max * 1;
                        //var _min = min * 1;
                        return (_val <= _max);
                    default:
                        return true;

                }

            }
        },

        /**
         * 是否email
         *
         * @param obj
         * @returns {Boolean}
         */
        isEmail: function (obj) {
            var pattern = /^([a-zA-Z0-9]+[_|-|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|-|.]?)*[a-zA-Z0-9]+.[a-zA-Z]{2,3}$/gi;
            if (pattern.test(obj) === false) {
                return false;

            }
            return true;
        },


        /**
         * 是否身份证号码
         * 身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X
         *
         * @param card
         * @returns {boolean}
         */
        isCardNoCn: function (card) {
            var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
            if (reg.test(card) === false) {
                //alert("身份证输入不合法");
                return false;
            }
            return true;

        },


        /**
         * 检查手机号码
         * @param phone
         * @returns {boolean}
         */
        isMobilePhone: function (phone) {
            var reg = /^1[34578]\d{9}$/;
            if (!(reg.test(phone))) {

                return false;
            }
            return true;
        },

        email: function (a) {
            return /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(a)
        }, phone: function (a) {
            return /^1\d{10}$/.test(a)
        }, cardId: function (a) {
            return /(^\d{15}$)|(^\d{17}(x|X|\d)$)/.test(a)
        }, url: function (a) {
            return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(a)
        }, date: function (a) {
            return !/Invalid|NaN/.test(new Date(a).toString())
        }, dateISO: function (a) {
            return /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test(a)
        }, number: function (a) {
            return /^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(a)
        }, isNumberAndLetter: function (a) {//数字与字母
            return /^[0-9a-zA-Z]*$/.test(a)
        }, isLetterAndNumber: function (a) {//字母与数字
            return /^[a-zA-Z][0-9a-zA-Z]*$/.test(a)
        }, letter: function (a) {//英文
            return /^[A-Za-z]+$/.test(a)
        }, character: function (a) {//中文
            return /^[\u4E00-\u9FA5]+$/.test(a)
        }, characterAndLetter: function (a) {//中文+英文，不包含数字以及特殊符号
            return /^[\u4E00-\u9FA5A-Za-z]+$/.test(a)
        }, digits: function (a) {
            return /^\d+$/.test(a)
        }

    }, maths: {
        uuid: {
            incc: 0, /* 将时间戳格式化为yyyy-MM-dd hh:mm:ss格式，其它格式可自行更改 */
            create: function () {
                //TODO 1妙1万个操作
                App.su.maths.uuid.incc++;
                var iiccc = App.su.maths.uuid.incc % 10000;
                var siccc = "";
                if (iiccc < 10) {
                    siccc = "000" + iiccc;
                } else if (iiccc < 100) {
                    siccc = "00" + iiccc;
                } else if (iiccc < 1000) {
                    siccc = "0" + iiccc;
                } else {
                    siccc = "" + iiccc;
                }
                var date = new Date();
                var timeStr = date.getFullYear();
                if (date.getMonth() < 9) { // 月份从0开始的
                    timeStr += '0';
                }
                timeStr += date.getMonth() + 1;
                timeStr += date.getDate() < 10 ? ('0' + date.getDate()) : date.getDate();
                timeStr += date.getHours() < 10 ? ('0' + date.getHours()) : date.getHours();
                timeStr += date.getMinutes() < 10 ? ('0' + date.getMinutes()) : date.getMinutes();
                timeStr += date.getSeconds() < 10 ? ('0' + date.getSeconds()) : date.getSeconds();
                return "U" + timeStr + "_" + siccc;
            }, create2: function () {
                var dg = new Date(1982, 2, 12, 0, 0, 0, 0).getTime();
                var dc = new Date().getTime();
                var t = (dg < 0) ? Math.abs(dg) + dc : dc - dg;
                var h = '_';
                var tl = this._getIntegerBits(t, 0, 31);
                var tm = this._getIntegerBits(t, 32, 47);
                var thv = this._getIntegerBits(t, 48, 59) + '1';
                var csar = this._getIntegerBits(this._randRange(0, 4095), 0, 7);
                var csl = this._getIntegerBits(this._randRange(0, 4095), 0, 7);
                var n = this._getIntegerBits(this._randRange(0, 8191), 0, 7) + this._getIntegerBits(this._randRange(0, 8191), 8, 15) + this._getIntegerBits(this._randRange(0, 8191), 0, 7) + this._getIntegerBits(this._randRange(0, 8191), 8, 15) + this._getIntegerBits(this._randRange(0, 8191), 0, 15);
                return tl + h + tm + h + thv + h + csar + csl + n;
            }, _getIntegerBits: function (val, start, end) {
                var base = 16;
                var base16 = val.toString(base).toUpperCase();
                var quadArray = base16.split('');
                var quadString = '';
                var i = 0;
                for (i = Math.floor(start / 4); i <= Math.floor(end / 4); i++) {
                    if (!quadArray[i] || (quadArray[i] == '')) {
                        quadString += '0';
                    } else {
                        quadString += quadArray[i];
                    }
                }
                return quadString;
            }, _randRange: function (min, max) {
                return Math.max(Math.min(Math.round(Math.random() * max), max), min);
            }
        }, rand: function (min, max) {
            return Math
                .max(Math.min(Math.round(Math.random() * max), max), min);
        }
    }, file: {
        /**
         * 获取扩展名
         * @param file_name
         * @returns {Array|{index: number, input: string}}
         */
        getExt: function (file_name) {

            var _reg = /^.*\.([a-z]{1,5})$/i;
            var _rex_info = file_name.match(_reg);
            return _rex_info ? _rex_info[1] : false;

        }, /**
         * 获取大小描述
         * @param file_size
         * @returns {number}
         */
        getSizeDesc: function (file_size) {
            var mark = "";
            var new_file_size = 0;
            if (file_size > 0 && file_size < 1024) {
                new_file_size = file_size;
                mark = "K";
            } else if (file_size >= 1024 && file_size < 1024 * 1024) {
                new_file_size = file_size / 1024;
                mark = "KB";
            } else if (file_size >= 1024 * 1024) {
                new_file_size = file_size / 1024 / 1024;
                mark = "M";
            }
            new_file_size = Number(new_file_size).toFixed(2) + mark;
            return new_file_size;
        }

    }, datetime: {
        /**
         * 星座
         */
        horoscopes: {
            data: "魔羯水瓶双鱼牡羊金牛双子巨蟹狮子处女天秤天蝎射手",
            seps: [20, 19, 21, 21, 21, 22, 23, 23, 23, 23, 22, 22],
            get:

                function (month, day) {
                    month--;
                    return this.data.substr(((day >= this.seps[month] ? (month + 1) : month) % 12) * 2, 2);
                }
        }, // 判断是否是闰年
        isLeapYear: function (year) {
            return (year % 400 == 0) || ((year % 4 == 0) && (year % 100 != 0));
        }, // 计算指定月的总天数
        getDaysByMonth: function (year, month) {
            if (month == 2) {
                if (this.isLeapYear(year)) {
                    return 29;
                } else {
                    return 28;
                }
            } else if ((month == 4) || (month == 6) || (month == 9) || (month == 11)) {
                return 30;
            } else {
                return 31;
            }
        }, /**
         * 格式化最近的时间
         */
        formatRecent: function (seconds) {
            var d = new Date();
            var c = Math.ceil(d.getTime() / 1000);
            var _d = c - seconds;
            if (60 >= _d) {
                return "刚刚";
            } else if ((60 < _d) && (3600 >= _d)) {
                return (Math.ceil(_d / 60) - 1) + "分钟前";
            } else if ((3600 < _d) && (86400 >= _d)) {
                return (Math.ceil(_d / 3600) - 1) + "小时前";
            } else if ((86400 < _d) && (2592000 >= _d)) {
                return (Math.ceil(_d / 86400) - 1) + "天前";
            } else if ((2592000 < _d) && (31536000 >= _d)) {
                return (Math.ceil(_d / 2592000) - 1) + "月前";
            } else {
                return (Math.ceil(_d / 31536000) - 1) + "年前";
            }
        }, /**
         * 获取当前日期
         */
        getCurrentDate: function () {
            var myDate = new Date();
            // myDate.getYear(); //获取当前年份(2位)
            var Y = myDate.getFullYear(); // 获取完整的年份(4位,1970-????)
            var m = myDate.getMonth(); // 获取当前月份(0-11,0代表1月)
            if (m < 10) {
                m = '0' + m;
            }
            var d = myDate.getDate(); // 获取当前日(1-31)
            if (d < 10) {
                d = '0' + d;
            }
            /**
             // myDate.getDay(); //获取当前星期X(0-6,0代表星期天)
             // myDate.getTime(); //获取当前时间(从1970.1.1开始的毫秒数)
             // myDate.getHours(); //获取当前小时数(0-23)
             // myDate.getMinutes(); //获取当前分钟数(0-59)
             // myDate.getSeconds(); //获取当前秒数(0-59)
             // myDate.getMilliseconds(); //获取当前毫秒数(0-999)
             */
            return Y + '-' + m + '-' + d;
        }, /**
         * 获取当前日期时间
         */
        getCurrentDateTime: function () {
            var myDate = new Date();
            // myDate.getYear(); //获取当前年份(2位)
            var Y = myDate.getFullYear(); // 获取完整的年份(4位,1970-????)
            var m = myDate.getMonth(); // 获取当前月份(0-11,0代表1月)
            if (m < 10) {
                m = '0' + m;
            }
            var d = myDate.getDate(); // 获取当前日(1-31)
            if (d < 10) {
                d = '0' + d;
            }
            // myDate.getDay(); //获取当前星期X(0-6,0代表星期天)
            // myDate.getTime(); //获取当前时间(从1970.1.1开始的毫秒数)
            var H = myDate.getHours(); // 获取当前小时数(0-23)
            if (H < 10) {
                H = '0' + H;
            }
            var i = myDate.getMinutes(); // 获取当前分钟数(0-59)
            if (i < 10) {
                i = '0' + i;
            }
            var s = myDate.getSeconds(); // 获取当前秒数(0-59)
            if (s < 10) {
                s = '0' + s;
            }
            // myDate.getMilliseconds(); //获取当前毫秒数(0-999)
            return Y + '-' + m + '-' + d + ' ' + H + ':' + i + ':' + s;
        }, getWeekdayByDate: function (date) {
            var _date = new Date(date);
            return _date.getDay();
        }, pickdate: function (input, handle) {
            var _handle = handle || input;
            $('#' + _handle).datepicker({
                language: 'zh-CN',
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd',
                targetInput: $("#" + input)
            }).on('changeDate', function (e) {
                $('#' + _handle).datepicker('hide');
            });
        }, pickdatetime: function (where) {
            var _w = $('#' + where);
            _w.datetimepicker({
                format: 'yyyy-MM-dd hh:mm', pickSeconds: false, language: 'zh-CN'
            }).on('show', function (e) {
                $('.confirm_datetime').click(function () {
                    _w.datetimepicker('hide');
                });
            });
        }

    }, color: {
        random: function () {
            // 16进制方式表示颜色0-F
            var arrHex = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F"];
            var strHex = "#";
            var index;
            for (var i = 0; i < 6; i++) {
                // 取得0-15之间的随机整数
                index = Math.round(Math.random() * 15);
                strHex += arrHex[index];
            }
            return strHex;
        }
    },

    notice: {
        succ: function (t, msg) {
            var _title = t || "标题";
            var _msg = msg || "";
            $.notify({title: _title, message: _msg}, {
                type: "success", placement: {
                    from: "bottom", align: "left"
                }, delay: 2000, timer: 2000
            });
        },

        info: function (t, msg) {
            var _title = t || "标题";
            var _msg = msg || "";
            $.notify({title: _title, message: _msg}, {
                type: "info", placement: {
                    from: "bottom", align: "left"
                }, delay: 2000, timer: 2000
            });
        },

        err: function (t, msg) {
            var _title = t || "标题";
            var _msg = msg || "";
            $.notify({title: _title, message: _msg}, {
                type: "danger", placement: {
                    from: "bottom", align: "left"
                }, delay: 2000, timer: 2000
            });
        }
    }
};