<?php

/**
 * @file
 * Contains \Drupal\service_container\LegacyMessenger.
 */

namespace Drupal\service_container\Messenger;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Class that manage the messages in Drupal.
 *
 * @codeCoverageIgnore
 */
class LegacyMessenger implements MessengerInterface {
  public function addMessage($message, $type = self::STATUS, $repeat = FALSE) {
    drupal_set_message($message, $type, $repeat);
  }

  public function getMessages() {
    return drupal_get_messages();
  }

  public function getMessagesByType($type) {
    $messages = $this->getMessages();
    return isset($messages[$type]) ? $messages[$type] : $messages;
  }

  public function deleteMessages() {
    throw new \BadMethodCallException('LegacyMessenger::deleteMessages is not implemented.');
  }

  public function deleteMessagesByType($type) {
    throw new \BadMethodCallException('LegacyMessenger::deleteMessagesByType is not implemented.');
  }
}
