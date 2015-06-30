Internal
========
This is a membership administration platform for Makerspace's.

We are upgrading to version 2. All the old stuff are in the v1 directory. The new stuff will be in v2. Under the migration phase both version will be used at the same time. When the migration is done the v1 version will be removed.


This system does the following
==============================
Well, the v2 system does nothing so far. v1 does only membership management.

Membership management
---------------------
A member database with personal information and a unique member id.

A member could be added to one or more groups. This is useful for creating mailing lists, etc.

You can list economy transactions connected to a member.

You can add RFID tags to a member.

You can use the economy transactions as a decision basis for the expiry date on the key.


Economy management
------------------
Aggregates transaction data from Stripe, PayPal, SEB (A swedish bank) and Bankgirot (A swedish payment system)

You can do all your work in this system. No need to log in into the other systems.

Transactions could be connected to a specific user.

Transactions could be connected to a specific accounting instruction in an external accounting system. If you are using a web based accounting systems you can set up a link schema, so all the referenses to accounting instructions are clickable.

Transaction without any connection could be listed. Very useful when doing either the accounting or membership managerment.


Key management
--------------
In some makerspaces the members have their own keys to the makerspace. When this is the case there are a lot of active keys at the same time. A good system is needed to track the keys and relevant data.
