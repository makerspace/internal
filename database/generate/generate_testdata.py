#!/usr/bin/python

import MySQLdb
import members
import member_rfid

db = MySQLdb.connect(user='internal', passwd='g34Api5C9L', host='127.0.0.1', db='internal', charset='utf8')

try:
    report = []
    new_members = members.generate(db, report, 1000)
    member_rfid.generate(db, report, new_members)
    db.commit()

    print "Done!\n"
    print "Special test cases generated:"
    for report_line in report:
        print "%s: %s" % (report_line[1].civicregno, report_line[0])
except:
    db.rollback()
    raise 

db.close()

