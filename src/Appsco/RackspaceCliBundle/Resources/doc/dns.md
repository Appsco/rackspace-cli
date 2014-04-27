DNS
=====

Dig
----

Prints domain info and records. Domain may not match exactly to existing domain name, but can also be part of the domain
name.

```
appsco:rackspace:dns:dig domain recordType --limit LIMIT --offset OFFSET
```


Create domain
-------------

Creates a new domain

```
appsco:rackspace:dns:domain:add domain email ttl
```

Update domain
-------------

Updates existing domain

```
appsco:rackspace:dns:domain:update domainId email ttl
```

Add record
----------

Adds new record to existing domain

```
appsco:rackspace:dns:record:add domainId name type value priority
```

Update record
-------------

Updates existing record

```
appsco:rackspace:dns:record:update recordId name value priority
```

Delete record
--------------

Deletes existing record

```
appsco:rackspace:dns:record:update recordId
```

