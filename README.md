# Basin
The ultimate PocketMine-MP load balancing solution

Features:
* Convenient installer in first run, both for plugin configuration and database setup
* Asynchronously save data and recalculate the least loaded server almost multiple times per second (depending on your MySQL connection speed) with almost zero lag
* Calls a `BalancePlayerEvent` before transferring a player to another server

