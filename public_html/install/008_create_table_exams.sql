CREATE TABLE IF NOT EXISTS Exams (
    id             SERIAL          NOT NULL PRIMARY KEY,
    title          VARCHAR(256)    NOT NULL,
    description    VARCHAR(1024)   NOT NULL DEFAULT '',
    point          INTEGER         NOT NULL,
    created        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    from_professor INTEGER         DEFAULT NULL,

    FOREIGN KEY (from_professor) REFERENCES Users(id)
);
--!!
DROP TRIGGER IF EXISTS update_exams_modified ON Exams;
--!!
CREATE TRIGGER update_exams_modified BEFORE UPDATE
    ON Exams FOR EACH ROW EXECUTE PROCEDURE 
    update_modified_column();
