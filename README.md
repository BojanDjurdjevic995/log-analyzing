# Instalation

**Follow this steps for setup project:**

* Clone project to your local machine / server
* Run command
```
composer update
```
* Then enter credentials for database (into .env)
* Then run command
```
php artisan migrate
```

### Everything is ready for use 
***

#### List of API link

Route | Method | Description
------------ | ------------- | -------------
/ | GET | List of available uploaded logs
/log | POST | Upload log file (plain txt or gzipped)
/log/{name} | DELETE | Delete uploaded log
/log/{name} | GET | Delete uploaded log
/aggregate/ip | GET | Aggregated by IP
/aggregate/method | GET | Aggregated by HTTP method
/aggregate/url | GET | Aggregate by URL (without GET arguments)

*Aggregate routes support optional **“dt_start”** and **“dt_end”** query parameters that contain the
start and end time on which aggregations will referee, the app will consider only log lines
between that data range. Datetime will be in the format: **“YYYY-MM-DD HH:MM:SS”.***

*Third optional query parameter is **“name”** who represent the name of log file.*
## NOTE

** Required header parameter **

```
api_token : token
```
