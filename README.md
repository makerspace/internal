Internal
========
This is a membership administration platform for Makerspace's.

We are upgrading to version 2. All the old stuff are in the v1 directory. The new stuff will be in v2. Under the migration phase both version will be used at the same time. When the migration is done the v1 version will be removed.


Key features
============
Well, the v2 system does nothing so far. We are working activley on it and will release a beta version this summer. v1 does only membership management.

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

Setting up an development environment
=====================================
All development is, preferably, done in a virtual machine. You run this virtual machine on your own computer. Everything that is needed to start development and/or testing is installed just with a single command. With another command you can destroy the virtual box and remove all files. In this way it's easy for new people to start developing. If you mess something up, just click a button and you have a new fresh install!

First of all you need to download and install Vagrant and Virtualbox.

In Linux you can just open the terminal and type:

`sudo apt-get update`

`sudo apt-get install vagrant virtualbox`

If you're on any other operating system you should go to:
https://www.vagrantup.com/downloads.html
https://www.virtualbox.org/wiki/Downloads

Next step is to clone this repository. Go to a terminal, navigate to the directory where you want to put the code, and type:

`git clone https://github.com/makerspace/internal`

Now you have everything you need to create a new virtual machine. In the terminal you should type:

`vagrant up`

The installation process will take a few minutes on a fast computer.

Your machine will be up and running and have the IP 192.168.32.10. It is recomended that you modify your /etc/hosts to add a hostname that points to this IP.

`sudo echo "192.168.32.10 internal.dev " >> /etc/hosts`

To log in to the virtual machine type:

`vagrant ssh`

You will find a directory /vagrant which is actually shared with the host computer. Everything that is valuable should be kept in this directory, which means it will also be in the GIT repository. Be aware that if you `rm -rf /*` all your files will be gone, because /vagrant is removed.


Vagrant cheat sheet
------------------
Create a new virtual machine

`vagrant up`

Shut down a virtual machine

`vagrant halt`

Remove a virtual machine (Warning: all files in the virtual machine, except those in /vargant, will be removed)

`vagrant destroy`

SSH in to the virtual machine

`vagrant ssh`
