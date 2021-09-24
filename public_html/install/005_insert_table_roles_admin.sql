INSERT INTO Roles (id, name, description) VALUES
    (1, 'admin', 'Administrator')
    ON CONFLICT (id) DO NOTHING;