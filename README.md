PHPDAL
======

Just another PHP Database Abstraction Library.

NOTE: This library is not finished, and will be very full of bugs. At the
moment, this is not recommended for use in production code.

PURPOSE
-------

PHPDAL is meant to be different from existing database abstraction layers in a
very key detail. The main goal of this library is **NOT to provide cross-vendor
abstraction**, but rather it is meant to provide **a high level object-oriented
interface for database interaction**. While cross-vendor abstraction is
provided, many more expensive cross-vendor abstraction features have been
sacrificed in favor of improving the performance.

Thus, if you're looking for a
library because you want something to translate your SQL queries, this is not
the library for you. However, if you think that

```php
$results = array();
$stmt = $db->prepare('
    INSERT INTO table
        (col_1, col_2, col_3, col_4, col_5)
    VALUES
        (:col_1, :col_2, :col_3, :col_4, :col_5)
', [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
foreach ($rows as $row) {
    $stmt->execute([
        ':col_1' => 1,
        ':col_2' => 2,
        ':col_3' => 3,
        ':col_4' => 4,
        ':col_5' => 5
    ]);
    $results[] = $stmt->fetchAll();
}
return $results;
```

should look like

```php
return $db->insert([
    'col_1' => 1,
    'col_2' => 2,
    'col_3' => 3,
    'col_4' => 4,
    'col_5' => 5
], 'table');
```

then this is the library for you. You know... once it's finished.

INSTRUCTIONS
------------

include/require /src/PHPDAL.php in your project and you're ready
to roll.
