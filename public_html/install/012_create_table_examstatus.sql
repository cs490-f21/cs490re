CREATE TABLE IF NOT EXISTS ExamStatus (
    id                  SERIAL        NOT NULL PRIMARY KEY,
    for_exam            INTEGER       NOT NULL, 
    from_student        INTEGER       NOT NULL,
    status              INTEGER       DEFAULT 0,

    FOREIGN KEY (for_exam) REFERENCES Exam(id),
    FOREIGN KEY (from_student) REFERENCES User(id)
);