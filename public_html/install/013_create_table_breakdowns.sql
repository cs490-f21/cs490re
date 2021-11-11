CREATE TABLE IF NOT EXISTS Breakdowns (
    id             SERIAL          NOT NULL PRIMARY KEY,
    for_submission INTEGER         NOT NULL,
    subject        VARCHAR(1024)   NOT NULL,
    expected       VARCHAR(4096)   NOT NULL,
    result         VARCHAR(4096)   NOT NULL,
    autoscore      INTEGER         NOT NULL,
    finalscore     INTEGER         NOT NULL,
    created        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (for_submission) REFERENCES Submissions(id)
);
--!!
DROP TRIGGER IF EXISTS update_breakdowns_modified ON Breakdowns;
--!!
CREATE TRIGGER update_breakdowns_modified BEFORE UPDATE
    ON Breakdowns FOR EACH ROW EXECUTE PROCEDURE 
    update_modified_column();
