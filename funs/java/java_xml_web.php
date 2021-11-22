<?php
if (!defined("JAVA_BASE")) {
    define('JAVA_BASE', realpath(dirname(__FILE__)));
}
include_once(JAVA_BASE . "/java_html.inc.php");

/**
 * 详情页
 * @param $model
 */
function java_xml_web($package, $a_models, $a_urlx = array())
{

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    ?>

    <web-app xmlns="http://java.sun.com/xml/ns/javaee"
             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
             xsi:schemaLocation="http://java.sun.com/xml/ns/javaee
		  http://java.sun.com/xml/ns/javaee/web-app_2_5.xsd"
             version="2.5">
        <welcome-file-list>
            <welcome-file>index.html</welcome-file>
            <welcome-file>index.jsp</welcome-file>
        </welcome-file-list>

        <servlet>
            <servlet-name>my-service</servlet-name>
            <servlet-class>org.glassfish.jersey.servlet.ServletContainer</servlet-class>
            <init-param>
                <param-name>jersey.config.server.provider.packages</param-name>
                <param-value><?= $package ?>.service</param-value>
            </init-param>
            <load-on-startup>1</load-on-startup>
        </servlet>
        <servlet-mapping>
            <servlet-name>my-service</servlet-name>
            <url-pattern>/rest/*</url-pattern>
        </servlet-mapping>

        <listener>
            <listener-class><?= $package ?>.MyListener</listener-class>
        </listener>

        <?php
        foreach ($a_urlx as $servlet => $url) {
            ?>
            <servlet>
                <servlet-name><?= $servlet ?></servlet-name>
                <servlet-class><?= $package ?>.servlet.<?= $servlet ?></servlet-class>
            </servlet>
            <servlet-mapping>
                <servlet-name><?= $servlet ?></servlet-name>
                <url-pattern><?= $url ?></url-pattern>
            </servlet-mapping>
        <?php }

        foreach ($a_models as $table => $model) {
            $table_name = $model["table_name"];
            $uc_table = ucfirst($table_name);
            ?>
            <servlet>
                <servlet-name><?= $uc_table ?>Servlet</servlet-name>
                <servlet-class><?= $package ?>.servlet.<?= $uc_table ?>Servlet</servlet-class>
            </servlet>
            <servlet-mapping>
                <servlet-name><?= $uc_table ?>Servlet</servlet-name>
                <url-pattern>/<?= $table_name ?>/*</url-pattern>
            </servlet-mapping>

        <?php }
        ?>
    </web-app>
    <?php
}