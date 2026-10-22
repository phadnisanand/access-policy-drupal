# Security Assessment Access Policy (Drupal 10.3+ / Drupal 11)

This custom module uses Drupal core's Access Policy API to remove selected node create/edit permissions for users who have not completed a security assessment.

## Important setup

1. Add a Boolean field to the User entity with machine name `field_security_assessment_completed`.
2. Use `0` / No until the assessment is passed; set it to `1` / Yes when the assessment is completed.
3. Review `RESTRICTED_PERMISSIONS` in `src/Access/SecurityAssessmentAccessPolicy.php` and add the create/edit permission names for every content type that should be restricted. The sample includes Article and Basic page (`article` and `page`).
4. Copy the `security_assessment` directory to `web/modules/custom/` (or your site's custom modules directory).
5. Enable and rebuild caches:

   ```bash
   drush en security_assessment -y
   drush cr
   ```

## Behavior

- Assessment incomplete or field missing: configured node create/edit permissions are removed.
- Assessment completed: this policy does not remove those permissions; the user's normal roles and other policies still determine access.
- UID 1 (Drupal superuser): exempt.

## Notes and limitations

- Requires Drupal core 10.3+ or Drupal 11, where the core Access Policy API is available.
- This policy only affects the permission names listed in the constant. Add all relevant content types and any other permissions your workflow needs to restrict.
- It does not itself create the assessment field or mark users complete; your assessment workflow must update the user field.
- Test on a development/staging site first. If your site has custom permission scopes or additional access policies, validate interactions with those policies.
