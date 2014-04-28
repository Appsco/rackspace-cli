rackspace api php cli client
============================

Rackspace login info
--------------------

All commands either take Rackspace credentials as command line arguments or if omitted look for them in environment
variables. Common parameters are

    **Username**
        Value: string
        --username, -u
        RAX_USERNAME
    **API Key**
        Value: string
        --api-key, -a
        RAX_API_KEY
    **Region**
        Value: string (LON, IAD, ORD, DFW, HKG, SYD)
        --region, -r
        RAX_REGION
    **Url type**
        Value: bool
        Default: 0
        --public, -p
        RAX_PUBLIC
        If specified public url of the service will be used rather then the internal url.

Its implied they are required for each of the specified commands and won't be individually documented in each of them.


[DNS](src/Appsco/RackspaceCliBundle/Resources/doc/dns.md)
[Cloud Files](src/Appsco/RackspaceCliBundle/Resources/doc/cloudFiles.md)
