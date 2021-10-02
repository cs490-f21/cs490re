CREATE TABLE IF NOT EXISTS Comments (
    id             SERIAL          NOT NULL PRIMARY KEY,
    for_submission INTEGER         NOT NULL,
    from_user      INTEGER         NOT NULL,
    content        VARCHAR(1024)   NOT NULL,
    created        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (for_submission) REFERENCES Submissions(id),
    FOREIGN KEY (from_user) REFERENCES Users(id)
);
--!!
DROP TRIGGER IF EXISTS update_comments_modified ON Comments;
--!!
CREATE TRIGGER update_comments_modified BEFORE UPDATE
    ON Comments FOR EACH ROW EXECUTE PROCEDURE 
    update_modified_column();
