CREATE TABLE IF NOT EXISTS Testcases (
    id          SERIAL          NOT NULL PRIMARY KEY,
    for_problem INTEGER         NOT NULL,
    case_order  INTEGER         NOT NULL,
    title       VARCHAR(256)    NOT NULL,
    input       VARCHAR(1024)   NOT NULL,
    output      VARCHAR(1024)   NOT NULL,
    weight      INTEGER         NOT NULL,
    created     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (for_problem) REFERENCES Problems(id),
    UNIQUE (for_problem, case_order)
);
--!!
DROP TRIGGER IF EXISTS update_testcases_modified ON Testcases;
--!!
CREATE TRIGGER update_testcases_modified BEFORE UPDATE
    ON Testcases FOR EACH ROW EXECUTE PROCEDURE 
    update_modified_column();
