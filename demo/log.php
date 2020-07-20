<?php

$logger = new \Psr\Log\NullLogger();
//class X implements \Psr\Log\LoggerInterface {
//    use \Psr\Log\LoggerTrait;
//
//    public function log($level, $message, array $context = array())
//    {
//        // TODO: Implement log() method.
//    }
//};
//$logger = new X;
$logger->info('User {username} created', ['username' => 'John']); // no error
$logger->info('User {username} created', ['usernane' => 'John']);