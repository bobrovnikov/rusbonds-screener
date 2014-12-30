Rusbonds Screener
================
This is a set of scripts designed to search, parse and display bonds which:

 - are issued by Russian corporations
 - can be purchased in the open market
 - have maturity within a year
 
Scripts should be run in the following order:

 1. `bonds-search.php` (with `?pages=NN` it records links to retrieve information from)
 2. `bonds-download.php` (saves external pages to local database to speed up further work)
 3. `bonds-parse.php` (parses downloaded pages for ISIN code, coupon, yield to maturity, etc.)
 4. `bonds-view.php` (displays collected data in a table format, spreadsheets are useful for further analysis)

> Note that some scripts may need several minutes to finish loading external data.
