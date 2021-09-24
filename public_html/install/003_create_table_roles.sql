  
CREATE TABLE IF NOT EXISTS Roles (
    id            SERIAL          NOT NULL PRIMARY KEY,
    name          VARCHAR(100)    NOT NULL UNIQUE,
    description   VARCHAR(100)    NOT NULL DEFAULT '',
    is_active     BOOLEAN         NOT NULL DEFAULT TRUE,
    created       timestamp       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified      timestamp       NOT NULL DEFAULT CURRENT_TIMESTAMP
);
--!!
DROP TRIGGER IF EXISTS update_roles_modified ON Roles;
--!!
CREATE TRIGGER update_roles_modified BEFORE UPDATE
    ON Roles FOR EACH ROW EXECUTE PROCEDURE 
    update_modified_column();
