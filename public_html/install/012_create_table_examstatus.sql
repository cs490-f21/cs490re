CREATE TABLE IF NOT EXISTS ExamStatus (
    id                  SERIAL        NOT NULL PRIMARY KEY,
    for_exam            INTEGER       NOT NULL, 
    from_student        INTEGER       NOT NULL,
    status              INTEGER       DEFAULT 0,
    created             TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified            TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (for_exam) REFERENCES Exams(id),
    FOREIGN KEY (from_student) REFERENCES Users(id),
    UNIQUE (for_exam, from_student)
);
--!!
DROP TRIGGER IF EXISTS update_examstatus_modified ON ExamStatus;
--!!
CREATE TRIGGER update_examstatus_modified BEFORE UPDATE
    ON ExamStatus FOR EACH ROW EXECUTE PROCEDURE 
    update_modified_column();
