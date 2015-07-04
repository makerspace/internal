#!/usr/bin/python

import datetime
import time
import random
import uuid

# Takes a table name and an object, where the object attributes are mapped directly to database columns
def insert_into_table(db, table_name, obj):
    excluded = ['__doc__', '__module__']
    cursor = db.cursor()
    fields = []
    placeholders = []
    values = []
    for member, value in vars(obj).iteritems():
        if member not in excluded:
            fields.append(member)
            placeholders.append("%s")
            values.append(value)

    query = "INSERT INTO %s (%s) VALUES (%s)" % (table_name, ', '.join(fields), ', '.join(placeholders))
    cursor.execute(query, tuple(values))
    return cursor.lastrowid

def get_random_datetime(from_date, to_date):
    first_timestamp = time.mktime(from_date.timetuple())
    last_timestamp = time.mktime(to_date.timetuple())
    random_timestamp = first_timestamp + (last_timestamp - first_timestamp) * random.random()
    return datetime.datetime.fromtimestamp(random_timestamp)

def get_short_unique_string():
    return str(uuid.uuid4())[:8]

def get_random_item(my_list):
    return my_list[int(random.random()*len(my_list))]
