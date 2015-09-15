<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\MysqlMq\Model\MessageFactory $messageFactory */
$messageFactory = $objectManager->create('Magento\MysqlMq\Model\MessageFactory');
$message1 = $messageFactory->create();

$message1->setTopicName('topic.updated.use.just.in.tests')
    ->setBody('{test:test}')
    ->save();

$messageId1 = $message1->getId();

/** @var \Magento\MysqlMq\Model\MessageStatusFactory $messageStatusFactory */
$queueFactory = $objectManager->create('Magento\MysqlMq\Model\QueueFactory');
$queueId1 = $queueFactory->create()
    ->load('queue1', Magento\MysqlMq\Model\Queue::KEY_NAME)
    ->getId();
$queueId2 = $queueFactory->create()
    ->load('queue2', Magento\MysqlMq\Model\Queue::KEY_NAME)
    ->getId();
$queueId3 = $queueFactory->create()
    ->load('queue3', Magento\MysqlMq\Model\Queue::KEY_NAME)
    ->getId();
$queueId4 = $queueFactory->create()
    ->load('queue4', Magento\MysqlMq\Model\Queue::KEY_NAME)
    ->getId();


$plan = [
    [$messageId1, $queueId1, time() - 24 * 7 * 60 * 60, Magento\MysqlMq\Model\QueueManagement::MESSAGE_STATUS_COMPLETE],
    [$messageId1, $queueId2, time() - 24 * 7 * 60 * 60, Magento\MysqlMq\Model\QueueManagement::MESSAGE_STATUS_ERROR],
    [$messageId1, $queueId3, time() - 24 * 7 * 60 * 60, Magento\MysqlMq\Model\QueueManagement::MESSAGE_STATUS_COMPLETE],
    [$messageId1, $queueId4, time(), Magento\MysqlMq\Model\QueueManagement::MESSAGE_STATUS_COMPLETE],
];


/** @var \Magento\MysqlMq\Model\MessageStatusFactory $messageStatusFactory */
$messageStatusFactory = $objectManager->create('Magento\MysqlMq\Model\MessageStatusFactory');
foreach ($plan as $instruction) {
    $messageStatus = $messageStatusFactory->create();

    $messageStatus->setQueueId($instruction[1])
        ->setMessageId($instruction[0])
        ->setUpdatedAt($instruction[2])
        ->setStatus($instruction[3])
        ->save();
}