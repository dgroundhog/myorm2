# 保存的目录结构

/project1/ 不同的项目目录
/project1/version1/ 项目版本1的私有数据，图片，icon等
/project1/version2/ 项目版本2的私有数据
/project1.js 项目主数据

/project2..


cd D:/build/20220501142042
cp -rf ./src/*  D:/xampp/htdocs/phal/
echo done!

cd D:/build/20220410203610

echo done!
run on mingw64 bash shell

\
export src=D:/build/20220730071006
export tar=D:/face/arc_java_4.0
cd $src
cp -rf ./doc/sql/init_db.sql  ./init_db.sql
cat ./doc/sql/*cc_table.sql > ./init_t.sql
cat ./doc/sql/*reset_table.sql > ./reset_t.sql
cat ./doc/sql/*_proc.sql > ./init_p.sql
cp -rf ./doc/sql/*.sql  $tar/doc/sql/
cp -rf ./*.sql  $tar/doc/
cp -rf ./src/*  $tar/src/
echo done!