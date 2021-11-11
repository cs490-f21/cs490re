CREATE TABLE IF NOT EXISTS Submissions (
    id           SERIAL          NOT NULL PRIMARY KEY,
    for_part     INTEGER         NOT NULL,
    from_student INTEGER         NOT NULL,
    answer       VARCHAR(4096)   NOT NULL,
    created      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (for_part) REFERENCES ExamParts(id),
    FOREIGN KEY (from_student) REFERENCES Users(id),
    UNIQUE (for_part, from_student)
);
--!!
DROP TRIGGER IF EXISTS update_submissions_modified ON Submissions;
--!!
CREATE TRIGGER update_submissions_modified BEFORE UPDATE
    ON Submissions FOR EACH ROW EXECUTE PROCEDURE 
    update_modified_column();
