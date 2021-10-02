CREATE TABLE IF NOT EXISTS Problems (
    id             SERIAL          NOT NULL PRIMARY KEY,
    title          VARCHAR(256)    NOT NULL,
    description    VARCHAR(1024)   NOT NULL DEFAULT '',
    created        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    from_professor INTEGER         DEFAULT NULL,
    level          INTEGER         NOT NULL,
    type           INTEGER         NOT NULL,

    FOREIGN KEY (from_professor) REFERENCES Users(id)
);
--!!
DROP TRIGGER IF EXISTS update_problems_modified ON Problems;
--!!
CREATE TRIGGER update_problems_modified BEFORE UPDATE
    ON Problems FOR EACH ROW EXECUTE PROCEDURE 
    update_modified_column();
