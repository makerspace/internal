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

def ensure_unique_value(db, table_name, column_name, value):
    query = "SELECT COUNT(*) FROM %s WHERE %s = %%s" % (table_name, column_name)
    cursor = db.cursor()
    cursor.execute(query, (value,)) # The extra comma is deliberate
    rows = cursor.fetchall()
    return rows[0][0] == 0

def get_random_datetime(from_date, to_date):
    first_timestamp = time.mktime(from_date.timetuple())
    last_timestamp = time.mktime(to_date.timetuple())
    random_timestamp = first_timestamp + (last_timestamp - first_timestamp) * random.random()
    return datetime.datetime.fromtimestamp(random_timestamp)

def get_short_unique_string():
    return str(uuid.uuid4())[:8]

def get_random_digit_string(length):
    number = ""
    for i in range(0, length):
        number = number + str(int(random.random() * 10))
    return number

def chance(chance):
    return random.random() < chance

def random_index_with_exclude(maximum, excludes):
    index = random.randint(0, maximum-1)
    while index in excludes:
        index = random.randint(0, maximum-1)
    return index
