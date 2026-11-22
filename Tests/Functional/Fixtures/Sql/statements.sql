-- Fixture for ExecuteSqlTrait (SQLite): comments, strings and several statements per line
-- dash comment line;
/* block comment
	with a semicolon; */
DROP TABLE IF EXISTS tx_t3extensiontools_fixture; -- comment after a statement; with semicolon
CREATE TABLE tx_t3extensiontools_fixture (
	uid INT PRIMARY KEY, /* comment in the middle of a line; */
	title VARCHAR(255)
);
INSERT INTO tx_t3extensiontools_fixture VALUES (1, 'semicolon at end of line;
inside a string'); -- comment after a statement
INSERT INTO tx_t3extensiontools_fixture VALUES (2, 'it''s -- no comment'); INSERT INTO tx_t3extensiontools_fixture VALUES (3, '/* no comment */')
