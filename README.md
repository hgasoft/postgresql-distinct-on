# postgresql-distinct-on

## What it does:
* Provides support for the postgresql specific DISTINCT ON feature for Laravel 5-9.

From the [Postgresql documentation][1]:
> SELECT DISTINCT ON ( expression [, ...] ) keeps only the first row of each set of rows where the given expressions 
evaluate to equal. The DISTINCT ON expressions are interpreted using the same rules as for ORDER BY (see above). Note 
that the "first row" of each set is unpredictable unless ORDER BY is used to ensure that the desired row appears first.

> For example:
> ```sql
> SELECT DISTINCT ON (location) location, time, report
>    FROM weather_reports
>    ORDER BY location, time DESC;
>```

> retrieves the most recent weather report for each location. But if we had not used ORDER BY to force descending order 
of time values for each location, we'd have gotten a report from an unpredictable time for each location.

> The DISTINCT ON expression(s) must match the leftmost ORDER BY expression(s). The ORDER BY clause will normally 
contain additional expression(s) that determine the desired precedence of rows within each DISTINCT ON group.

## Why do we need it?

I built this specifically because I wanted the ability to have a descriptive metadata table that allowed me to retain 
all versions of data for a given key. I needed to be able to easily retrieve only the most recent version of the stored
value. Postgres makes this very easy by supporting SELECT DISTINCT ON, however Laravel/Eloquent have no base support
for this database specific functionality. 

## How to use it:

1. In your project directory, run: `composer require datajoe/postgresql-distinct-on`

2. In your app.php, replace `Illuminate\Database\DatabaseServiceProvider::class,` with 
`DataJoe\Extensions\Illuminate\Database\DatabaseServiceProvider::class,`

3. In your code, you can access the DISTINCT ON functionality using the `->distinctOn('field_name')` method. Be sure to 
also include `->orderBy('field_name')` in your query!

## Example:

This would return a single RecordMeta row, with only the most recent update_at timestamp, for each distinct meta_name 
where the record_id is 1
```php
$fields = RecordMeta::select(['record_id', 'value', 'updated_at'])
                 ->distinctOn('meta_name')
                 ->where('record_id', 1)
                 ->orderBy('meta_name')
                 ->orderBy('updated_at', 'desc');
```

That command would be equivalent to the following sql:
```sql 
SELECT DISTINCT ON ('meta_name') meta_name, record_id, value, updated_at
    FROM record_meta
    WHERE record_id = 1
    ORDER BY meta_name, updated_at DESC;
```

## Other cool things you can do:

To be able to access the meta data through a model relationship (i.e. Record -> RecordMeta), you can add the following 
to your record model to establish a custom relationship:

```php
public function recordMeta()
{
    $relation = $this->hasMany(RecordMeta::class);

    $relation->getQuery()
             ->select(['record_id', 'value', 'updated_at'])
             ->distinctOn('meta_name')
             ->orderBy('updated_at', 'desc');

    return $relation;
}
```

Then, in your code, you can access the metadata like this:

```php
$record = Record::with('recordMeta')->get();
```

And you will get back all record with only the most recently set value for each meta_name for each record.


[1]: https://www.postgresql.org/docs/9.5/sql-select.html#SQL-DISTINCT "Postgresql Distinct On"
