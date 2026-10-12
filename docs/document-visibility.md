# Document visibility and private storage

AtlasHub keeps the system catalog, endpoint details, filters, and reports behind login. Account creation belongs to administrators in **Backoffice → Users**; there is no public registration. Only PDFs marked **Visible sin iniciar sesion** may be listed or downloaded anonymously. New and existing documents default to private.

## Deploy an existing installation

1. Back up the database and `storage/app/public/documents` before upgrading. Put the site in maintenance mode so no uploads or downloads race the migration.
2. Deploy the code and run `php artisan migrate --force`.
3. Run `php artisan documents:migrate-to-private`. It copies each referenced legacy PDF to `storage/app/private`, verifies size and checksum, then removes the public copy. It exits unsuccessfully and retains the public original when verification or deletion fails. Fix the reported files and rerun it; it is safe to rerun.
4. Confirm `storage/app/public/documents` contains no remaining PDFs, including unreferenced files. Investigate any unreferenced files before removing them.
5. **Required before ending maintenance:** run `php artisan atlashub:audit-default-passwords`. The command exits unsuccessfully and lists accounts still using the demonstration password. Rotate every listed account with `php artisan atlashub:rotate-password user@example.com`; it asks for a hidden strong password and revokes personal access tokens. Repeat the audit until it succeeds. The known password is rejected by both portal and Backoffice login in production, so affected users cannot sign in until rotation. Invalidate remaining active sessions in non-database session stores as part of the upgrade.
6. Review each document in Backoffice and turn on public visibility only for approved, non-sensitive PDFs. Public document URLs are served through the application, not `public/storage`. Lift maintenance mode only after the PDF audit and password audit both pass.

On a fresh installation, run migrations before uploading PDFs. The `local` disk is rooted at `storage/app/private` and must not be exposed by the web server. Keep the `public/storage` symlink restricted to assets intended for everyone; do not place document PDFs there.

Do **not** run `db:seed` or `migrate --seed` in production. `DatabaseSeeder` contains example infrastructure and demonstration accounts and intentionally refuses to run when `APP_ENV=production`. After `php artisan migrate --force`, run `php artisan atlashub:create-admin owner@example.com "Site Owner"` in an interactive terminal. The command asks for a hidden password and confirmation (minimum 12 characters with mixed case, a number, and a symbol), creates the access-control roles, and provisions the first admin. It never accepts a password on the command line. Repeating it for the same admin does not change credentials; using an existing non-admin email fails. Then sign in and create other users in **Backoffice → Users**. New and changed account passwords use the same strength rule.

## Access rules

| Resource | Anonymous | Signed-in user | Required permission |
| --- | --- | --- | --- |
| System catalog and reports | No access | Access | Login |
| Public PDF | List, inspect, view, download | Same | None |
| Private PDF | Not listed; detail/file return 404 | View when permitted | `document.view` |
| Upload, delete | No access | Manage when permitted | `document.manage` |
| Change visibility | No access | Admin only | `admin` role |

Anonymous document JSON contains only selected document fields and application-controlled file URLs. It does not expose storage paths, uploader email, system infrastructure, or endpoint URLs. Anonymous catalog-ID filters are rejected to avoid exposing a public PDF's private parent associations. Making a PDF public **does not** make its parent system public.

Token clients may send a Sanctum bearer token to the public document API. When authorized, private PDFs appear in the listing, and file URLs point to `/api/v1/documents/{id}/file` so the client can send the same bearer token on download. Anonymous requests still see only public PDFs.
