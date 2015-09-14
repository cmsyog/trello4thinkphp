<?php
return array(
    //URL模式
    'URL_MODEL'             =>  2,
    //开启路由
    'URL_ROUTER_ON'   => true,
    'URL_ROUTE_RULES'=>array(
       //'news/:year/:month/:day' => array('News/archive', 'status=1'),
    ),
    'URL_MAP_RULES'=>array(
       // '/api/b/view_model' => '/admin.php/Board/getBoard',//路由不生效原因：原路径不存在，解析时报错，没走到路由那一步
        //'Index/index' => 'Index/show',
    ),
    'TMPL_PARSE_STRING'  =>array(
        '__UPLOAD__' => '/Uploads', // 增加新的上传路径替换规
        '__MODULE__' => '/admin.php/', // 默认替换为空，从新定义
    )
);