<?php

declare(strict_types=1);

namespace Drupal\test_access_policy\Access;

use Drupal\Core\Session\AccessPolicyBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\CalculatedPermissionsItem;
use Drupal\Core\Session\RefinableCalculatedPermissionsInterface;
use Drupal\test_access_policy\Cache\Context\IsUserMailValidCacheContext;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;

/**
 * Access policy based on user email domain.
 */
class UserEmailDomainAccessPolicy extends AccessPolicyBase {

  /**
   * {@inheritdoc}
   */
  public function alterPermissions( AccountInterface $account, string $scope, RefinableCalculatedPermissionsInterface $calculated_permissions): void {
    // Give only 'Authenticated user' permissions to the user if the email
    // domain is 'example.com', regardless of the user's roles.
    if (IsUserMailValidCacheContext::isUserMailValid($account) == 'No') {
      $new_permissions = Role::load(RoleInterface::AUTHENTICATED_ID)->getPermissions();
      $calculated_permissions->addItem(
        item: new CalculatedPermissionsItem(
          permissions: $new_permissions,
          isAdmin: FALSE
        ),
        // Set this to 'TRUE' to override the permissions.
        overwrite: TRUE
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getPersistentCacheContexts(): array {
    return ['is_user_mail_valid'];
  }

}
