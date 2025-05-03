/*
 this script is for removing unneed tables
 */

ALTER TABLE users
  DROP COLUMN IF EXISTS two_factor_enabled,
  DROP COLUMN IF EXISTS two_factor_code,
  DROP COLUMN IF EXISTS two_factor_expires_at;
/*
       this is for changing the constraint to include campus_member instead of student
 */

ALTER TABLE users DROP CONSTRAINT IF EXISTS users_user_role_check;
ALTER TABLE users ADD CONSTRAINT users_user_role_check
    CHECK (user_role IN ('admin', 'technician', 'campus_member'));

-- Step 4: Verify the changes
SELECT DISTINCT user_role FROM users;
