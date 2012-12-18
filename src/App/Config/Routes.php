<?php

return array(
    '(/\d+)?/?'                         => 'App\Controller\Home',
    '/new-thread/?'                     => 'App\Controller\Newthread',
    '/thread(/\d+)?(/\d+)?/?'           => 'App\Controller\Thread',
    '/thread/(\d+)/(\d+)/reply/(\d+)/?' => 'App\Controller\ThreadReply',
    '/blacklist(/regenerated)?/?$'      => 'App\Controller\Blacklist',
    '/blacklist/remove/?'               => 'App\Controller\BlacklistRemove',
    '/blacklist/regenerate/?'           => 'App\Controller\BlacklistRegenerate',
);
