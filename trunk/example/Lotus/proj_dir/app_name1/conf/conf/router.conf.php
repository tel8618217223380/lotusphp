<?php
/**
 * url����ģʽ
 * standard, rewrite, path_info
 */
$config['my_url_option'] = array(
    'url_mode' => 'path_info'
);

/**
 * ·��ƥ�����
 */
$config['my_routers'] = array(
    'book' => array(
        'module' => 'book',
        'action' => 'list',
        'pattern' => 'book/:category/:id',
        'suffix' => 'html'
    ),
    'passport' => array(
        'module' => 'User',
        'action' => 'Signin',
        'pattern' => 'UserSignin'
    )
);
