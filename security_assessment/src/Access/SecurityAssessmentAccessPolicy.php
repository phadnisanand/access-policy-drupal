<?php

namespace Drupal\security_assessment\Access;

use Drupal\Core\Session\AccessPolicyBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\CalculatedPermissionsItem;
use Drupal\Core\Session\RefinableCalculatedPermissionsInterface;
use Drupal\user\Entity\User;

/**
 * Revokes node creation/editing permissions until the assessment is completed.
 */
final class SecurityAssessmentAccessPolicy extends AccessPolicyBase {

  /**
   * Permissions this policy restricts.
   *
   * Update these names to match the content types used on your site.
   *
   * @var string[]
   */
  private const RESTRICTED_PERMISSIONS = [
    'create article content',
    'delete own article content',
	'edit own article content'
  ];

  /**
   * {@inheritdoc}
   */
  public function calculatePermissions(AccountInterface $account, string $scope): RefinableCalculatedPermissionsInterface {
    $calculated_permissions = parent::calculatePermissions($account, $scope);

    // The policy varies by account, and user cache tags invalidate the result
    // when the assessment field on the user entity is updated.
    $calculated_permissions->addCacheContexts(['user']);
    if ($account->id() !== NULL && ($user = User::load($account->id()))) {
      $calculated_permissions->addCacheTags($user->getCacheTags());
    }

    return $calculated_permissions;
  }

  /**
   * {@inheritdoc}
   */
  public function alterPermissions(AccountInterface $account, string $scope, RefinableCalculatedPermissionsInterface $calculated_permissions): void {
    parent::alterPermissions($account, $scope, $calculated_permissions);

    // Keep UID 1 (Drupal's superuser) exempt from this policy.
    if ((int) $account->id() === 1) {
      return;
    }


    $user = $account->id() !== NULL ? User::load($account->id()) : NULL;
    $completed = $user
      && $user->hasField('field_field_security_assessment')
      && (string) $user->get('field_field_security_assessment')->value === '1';

    if ($completed) {
      return;
    }

    // Replace each permission item after removing the restricted permissions.
    // Reconstruct items with the same scope and identifier. Preserve admin
    // items by not changing items that already grant all permissions.
    foreach ($calculated_permissions->getItemsByScope($scope) as $item) {
      $permissions = $item->getPermissions();
      $remaining_permissions = array_values(array_diff($permissions, self::RESTRICTED_PERMISSIONS));
      if ($remaining_permissions === $permissions) {
        continue;
      }
      $replacement = new CalculatedPermissionsItem(
        $remaining_permissions,
        FALSE,
        $item->getScope(),
        $item->getIdentifier(),
      );
      $calculated_permissions->addItem($replacement, TRUE);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getPersistentCacheContexts(): array {
    return ['user'];
  }

}
