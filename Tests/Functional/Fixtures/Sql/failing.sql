DROP TABLE IF EXISTS tx_t3extensiontools_fixture;
CREATE TABLE tx_t3extensiontools_fixture (uid INT PRIMARY KEY, title VARCHAR(255));
INSERT INTO tx_t3extensiontools_fixture VALUES (1, 'first');
THIS IS NOT VALID SQL;
INSERT INTO tx_t3extensiontools_fixture VALUES (2, 'second');
