<?php
/**
 *
 * ������Ϣ
 * �����վ�õ��ĸ���������Ϣ������ͨ������xx������
 * �������url������ͷ����
 *
 */
$config['my_hosts'] = array(
    'home' => array(
        'host'      => 'www.example.com.cn',
        'port'      => '80', //�����Ĭ��80�˿ڿ��Բ���д
        'protocol'  => 'http',
        'base'      => '/', //�����վ��·�������λ�ڸ�·�����Բ���д
        'name'          => '��վ��'
    ),
    'passport' => array(
        'host'      => 'passport.example.com.cn',
        'port'      => '443',
        'protocol'  => 'https',
        'base'      => '/',
        'name'      => 'ͼƬ������һ'
    )
);