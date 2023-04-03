# mycoco

- 一个跨语言应用的代码和项目自动生成小工具
- 对数据库对所有操作均基于存储过程，默认单表操作
- 这是放弃了orm的便利性，换用sql语句的一次编译
- 目前仅支持基于phalcon的php项目和基本servlet的java项目，
- 着手支持基于qt-webapp的cpp项目。

## 应用支支持

| 语言   | 技术       | 网址                               |
|------|----------|----------------------------------|
| php  | phalcon  | https://phalcon.io/              |
| java | servlet  | https://www.oracle.com/java/     |
| cpp  | qtwebapp | http://stefanfrings.de/qtwebapp/ |
| c#   | --       | --                               |

## 数据库建模

| 产品         | 支持计划 |
|------------| -------- |
| mysql      | 支持     |
| sqlite     | 部分支持 |
| postgresql | 计划支持 |
| oracle     | 计划支持 |
| sqlserver  | 计划支持 |

## UI建模

| 产品           | 网站                      |
| -------------- | ------------------------- |
| purecss        | https://purecss.io/       |
| bootstrap      | https://getbootstrap.com/ |
| AdminLTE-3.1.0 | https://adminlte.io/      |
| swing          | 单纯的java swing UI       |

# code to code，code for milk

## TODO 
controller 的count 变成了list
其中model是对的

cp -rf D:/build/20230403185213/src/main/*  D:/build/demoee/src/main 
