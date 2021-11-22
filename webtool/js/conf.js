if (typeof MyWhereCond == "undefined") {
    //搜索的条件
    var MyWhereCond = {
        eq: {
            vv: "eq",
            desc: "= 等于",
            params: 1
        },
        neq: {
            vv: "neq",
            desc: "!= 不等于",
            params: 1
        },
        gt: {
            vv: "gt",
            desc: "&gt; 大于",
            params: 1
        },
        gte: {
            vv: "gte",
            desc: "&gt;= 大于等于",
            params: 1
        },
        lt: {
            vv: "lt",
            desc: "&lt; 少于",
            params: 1
        },
        lte: {
            vv: "lte",
            desc: "&lt;= 少于等于",
            params: 1
        },
        kw: {
            vv: "kw",
            desc: "关键字模糊匹配",
            params: 1
        },
        date: {
            vv: "date",
            desc: "日期范围内",
            params: 2
        },
        time: {
            vv: "time",
            desc: "时间范围内",
            params: 2
        },
        in: {
            vv: "in",
            desc: "离散量范围内",
            params: 2
        },
        notin: {
            vv: "notin",
            desc: "离散量范围外",
            params: 2
        },
        between: {
            vv: "between",
            desc: "标量范围内",
            params: 2
        },
        notbetween: {
            vv: "notbetween",
            desc: "标量范围外",
            params: 2
        }
    }
}