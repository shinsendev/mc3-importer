# MC3-Importer

This PHP Symfony 5 application converts MySQL data from MC2 into PostgreSQL for MC3.

## Installation

It requires a MySQL server with mc2 project database.

## Start

```
make start
```

## Objectives of the importer

The importer convert old MySQL MC2 model into new PostgreSQL MC3 model and import all the usefull data.

To be used, the importer need to be connected to MySQL and a postgreSQL server.

## Homepage

You can use the homepage to launch the imports processes : [http://127.0.0.1:8000](http://127.0.0.1:8000)

## Requirements

You need 
- a copy of MC2 DB in SQL format = the MySQL data you want to import in the new PgSQL model
- to create the mc3 DB on your local machine = all the tables and the structure of the new table, you can generate it easily with mc3 symfony project and doctrine command

## All processes

All processes option triggers all processes in the correct order.

There are two steps, first the importer creates a new MySQL table with old data.

Then, the application imports the data of this MySQL table in the new PostgreSQL one.

## Initiate the project

Recreate MySQL MC2 database

## Create the model and import data

Create the database model and import all the data from a SQL file copy of MC2 previous database.

## Clean data

Delete all useless tables from old model like Stagenumber or Stageshow and useless columns for number, films, etc.

## Export data



