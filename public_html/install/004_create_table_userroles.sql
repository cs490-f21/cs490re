CREATE TABLE IF NOT EXISTS UserRoles (
    id        SERIAL      NOT NULL PRIMARY KEY,
    user_id   INT         NOT NULL,
    role_id   INT         NOT NULL,
    is_active BOOLEAN     NOT NULL DEFAULT TRUE,
    created   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (role_id) REFERENCES Roles(id),
    UNIQUE (user_id, role_id)
);
--!!
DROP TRIGGER IF EXISTS update_user_roles_modified ON UserRoles;
--!!
CREATE TRIGGER update_user_roles_modified BEFORE UPDATE
    ON UserRoles FOR EACH ROW EXECUTE PROCEDURE 
    update_modified_column();
