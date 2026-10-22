<?php

declare(strict_types=1);

namespace Drupal\test_access_policy\Cache\Context;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextInterface;
use Drupal\Core\Cache\Context\UserCacheContextBase;
use Drupal\Core\Session\AccountInterface;

/**
 * @todo Add a description for the cache context.
 *
 * Cache context ID: 'is_user_mail_valid'.
 *
 * @DCG
 * Check out the core/lib/Drupal/Core/Cache/Context directory for examples of
 * cache contexts provided by Drupal core.
 */
final class IsUserMailValidCacheContext extends UserCacheContextBase implements CacheContextInterface {

  /**
   * {@inheritdoc}
   */
  public static function getLabel(): string {
    return (string) t('Is user mail valid');
  }

  /**
   * {@inheritdoc}
   */
  public function getContext(): string {
    return 'is_user_mail_valid_' . static::isUserMailValid($this->user);
  }

  /**
   * Returns whether the user mail is valid or not.
   */
  public static function isUserMailValid(AccountInterface $user) {
    $user_mail = $user->getEmail();
    return (!$user_mail || substr(strrchr($user_mail, "@"), 1) === "example.com") ? 'No' : 'Yes';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata(): CacheableMetadata {
    return new CacheableMetadata();
  }

}
