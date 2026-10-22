<?php

declare(strict_types=1);

namespace Drupal\test_access_policy\Access;

use Drupal\Core\Session\AccessPolicyBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\CalculatedPermissionsItem;
use Drupal\Core\Session\RefinableCalculatedPermissionsInterface;

/**
 * Access policy per timezone.
 */
class UserTimeZoneAccessPolicy extends AccessPolicyBase {

  /**
   * {@inheritdoc}
   */
  public function calculatePermissions(AccountInterface $account, string $scope): RefinableCalculatedPermissionsInterface {
    $calculated_permissions = parent::calculatePermissions($account, $scope);

    $user_timezone = $account->getTimeZone();
    // Grant create and edit permissions only if the user's timezone is
    // 'Asia/Kolkata'.
    if ($user_timezone === "Asia/Kolkata") {
      $req_permissions = [
        'create article content',
        'create page content',
        'create recipe content',
        'edit any article content',
        'edit any page content',
        'edit any recipe content',
      ];
      $calculated_permissions->addItem(
        item: new CalculatedPermissionsItem(
          permissions: $req_permissions,
        ),
        overwrite: FALSE
      );
    }

    return $calculated_permissions;
  }

  /**
   * {@inheritdoc}
   */
  public function getPersistentCacheContexts(): array {
    return ['timezone'];
  }

}
