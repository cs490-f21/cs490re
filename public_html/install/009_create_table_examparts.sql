CREATE TABLE IF NOT EXISTS ExamParts (
    id             SERIAL          NOT NULL PRIMARY KEY,
    for_exam       INTEGER         NOT NULL,
    part_order     INTEGER         NOT NULL,
    with_problem   INTEGER         NOT NULL,
    point          INTEGER         NOT NULL,
    created        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (for_exam) REFERENCES Exams(id),
    FOREIGN KEY (with_problem) REFERENCES Problems(id),
    UNIQUE (for_exam, part_order)
);
--!!
DROP TRIGGER IF EXISTS update_examparts_modified ON ExamParts;
--!!
CREATE TRIGGER update_examparts_modified BEFORE UPDATE
    ON ExamParts FOR EACH ROW EXECUTE PROCEDURE 
    update_modified_column();
