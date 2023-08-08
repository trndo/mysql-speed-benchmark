# mysql-speed-benchmark

### Machine for testing
11th Gen Intel® Core™ i5-1135G7 @ 2.40GHz × 8 - `CPU`

32GB - `RAM`

SSD KXG6AZNV1T02 TOSHIBA , read/write up to (MB/s): 3180 / 2960 - `Storage`

MySQL v8.0.34

### Table schema
Engine - InnoDB

```sql
`id`         int          NOT NULL AUTO_INCREMENT PRIMARY KEY,
`username`   varchar(50)  NOT NULL,
`email`      varchar(100) NOT NULL,
`birth_date` date DEFAULT NULL,
```

### Read benchmark tests

Load data with `load-users.php {number_of_rows}` script.
In our case `40000000`.

Run and analyze queries without indexes and with them.

**Result:**
|  Query | Without indexes | With BTREE index | Rows |
|--------------------------------|----------------------------------------------|---------------------------------------------|--------------------------------------------|
| SELECT * from users where birth_date = "2009-01-01"  | 9.05 sec | 0.01 sec | 836 | 
| SELECT * from users where birth_date >= "2009-01-01" and birth_date < "2010-01-01" | 9.54 sec | 2.21 sec | 322879 |
| SELECT * from users where birth_date >= "2005-01-01" and birth_date < "2010-01-01" | 14.53 sec | 9.71 sec | 1612032 |
| SELECT * from users where birth_date <= "2005-01-01" | 47.37 sec | 46.11 sec (not using index) | 33874374 | 
| SELECT * from users where birth_date >= "1990-01-01" and birth_date < "2010-01-01" | 35.00 sec | 34.13 sec (not using index) | 6450407 |

Time for creation INDEX - `2 min 15.40 sec`

**Conclusion**: 
BTREE index for column `birth_date` is ineffective for big-range data selection. This is due to its high selectivity.
BTREE index is effective for queries with single comparing values and not big data range values.
> HASH index is not supported by the MySql Innodb storage engine since September 2021, it uses Adaptive HASH index. The results are in many cases the same and almost 
the same cause it uses BTREE index under the hood, so we don't include it in comparison table

### Write benchmark tests

We used `sysbench` tool for testing MySQL benchmarks

**Requirements:**
Number of threads - `5`,
Number of Inserts queries - `100000`

Use `mysql-test.lua` script to perform inset operation with `sysbench` too

**Result:**
| innodb_flush_log_at_trx_commit | Total time, s | Transactions per second | Latency min, ms | Latency max, ms |
|--------------------------------|-------------------------|-------------------|------|-------|
| 0                              | 57.9345                 | 1726.05           | 1.36 | 19.61 |
| 1                              | 100.2748                | 997.25            | 2.08 | 24.43 |
| 2                              | 57.3026                 | 1745.08           | 1.59 | 23.96 |

**Conclusion**:
`innodb_flush_log_at_trx_commit` - this setting alone can change the speed of writing to the database.
Levels: 

`0` - is the fastest level. Logs are written and flushed to disk once per second. But transactions for which logs have not been flushed can be lost in a crash. 

`1` - is a default level, and the slowest one. Logs are written and flushed to disk at each transaction commit.

`2` - is on second place by speed after `0`. Logs are written after each transaction commit and flushed to disk once per second. Transactions for which logs have not been flushed can be lost in a crash.

