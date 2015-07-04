#!/usr/bin/python

import MySQLdb
import members

db = MySQLdb.connect(user='internal', passwd='g34Api5C9L', host='127.0.0.1', db='internal', charset='utf8')

try:
    new_member_ids = members.generate(db, 100)
    db.commit()
except:
    db.rollback()
    raise 

db.close()

