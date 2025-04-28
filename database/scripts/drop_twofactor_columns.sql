ALTER TABLE users
  DROP COLUMN IF EXISTS two_factor_enabled,
  DROP COLUMN IF EXISTS two_factor_code,
  DROP COLUMN IF EXISTS two_factor_expires_at;
