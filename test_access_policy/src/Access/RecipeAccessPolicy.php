<?php

declare(strict_types=1);

namespace Drupal\test_access_policy\Access;

use Drupal\Core\Session\AccessPolicyBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\CalculatedPermissionsItem;
use Drupal\Core\Session\RefinableCalculatedPermissionsInterface;

/**
 * RecipeAccessPolicy class.
 */
class RecipeAccessPolicy extends AccessPolicyBase {

  public function applies(string $scope): bool {
    return $scope === 'recipe';
  }

  /**
   * {@inheritdoc}
   */
  public function calculatePermissions(AccountInterface $account, string $scope): RefinableCalculatedPermissionsInterface {
    $calculated_permissions = parent::calculatePermissions($account, $scope);

    $req_permissions = [
      'create recipe content',
      'edit any recipe content',
    ];
    $calculated_permissions->addItem(
      item: new CalculatedPermissionsItem(
        permissions: $req_permissions,
        scope: 'recipe',
        identifier: 'en'
      ),
      overwrite: FALSE
    );

    return $calculated_permissions;
  }

  /**
   * {@inheritdoc}
   */
  public function getPersistentCacheContexts(): array {
    return ['languages'];
  }

}
