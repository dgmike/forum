<?php

return array(
    '(/\d+)?/?'                         => 'App\Controller\Home',
    '/new-thread/?'                     => 'App\Controller\Newthread',
    '/thread(/\d+)?(/\d+)?/?'           => 'App\Controller\Thread',
    '/thread/(\d+)/(\d+)/reply/(\d+)/?' => 'App\Controller\ThreadReply',
    '/blacklist/?'                      => 'App\Controller\Blacklist',
);
