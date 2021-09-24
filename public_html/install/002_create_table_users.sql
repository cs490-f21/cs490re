CREATE TABLE IF NOT EXISTS Users (
    id         SERIAL          NOT NULL PRIMARY KEY,
    email      VARCHAR(100)    NOT NULL UNIQUE,
    username   VARCHAR(60)     NOT NULL UNIQUE,
    password   VARCHAR(60)     NOT NULL,
    created    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    extra      VARCHAR(65536)  NOT NULL DEFAULT '',
    visibility INT             NOT NULL DEFAULT 0
);
--!!
DROP TRIGGER IF EXISTS update_users_modified ON Users;
--!!
CREATE TRIGGER update_users_modified BEFORE UPDATE
    ON Users FOR EACH ROW EXECUTE PROCEDURE 
    update_modified_column();
